<?php

namespace App\Http\Controllers\ECOM;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Transaction;
use App\Models\OrderTrackingStatus;
use App\Cart;
use App\CartItem;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\Log;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OrderDownloadLog;

class OrdersController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $contactUtil;

    protected $businessUtil;

    protected $transactionUtil;

    protected $productUtil;
    protected $moduleUtil;

    private $receipt_details;

    protected $dummyPaymentLine;
    protected $shipping_status_colors;

    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;

        $this->dummyPaymentLine = [
            'method' => '',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'bank_account_number' => '',
            'is_return' => 0,
            'transaction_no' => '',
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }
    public function mySaleOrders(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            
            // Get query parameters
            $filter = $request->query('filter', 'all'); // all, not_yet_shipped, cancelled, buy_again
            $search = $request->query('search', ''); // Search by item, order number, PO number
            $orderNumber = $request->query('order_number', ''); // Search specifically by sales order number
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $months = $request->query('months'); // Filter by last N months (3, 6, 12, etc.)
            $year = $request->query('year'); // Filter by year (e.g. 2026)
            $status = $request->query('status'); // Specific status filter
            // Track effective date range used for the query (start & end)
            $effectiveStartDate = $dateFrom ?? null;
            $effectiveEndDate = $dateTo ?? null;
            // Build base query
            $query = Transaction::with([
                'sell_lines' => function($query) {
                    $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'unit_price', 'unit_price_inc_tax', 'received_quantity');
                },
                'sell_lines.product' => function ($query) {
                    $query->select('id', 'name', 'slug', 'image');
                },
                'sell_lines.variations' => function ($query) {
                    $query->select('id', 'product_id', 'name', 'sub_sku');
                },
                'sell_lines.variations.media' => function($query) {
                    $query->select('id', 'file_name', 'model_id');
                }
            ])
            ->where('contact_id', $contact->id)
            ->where('business_id', $business_id)
            ->where('type', 'sales_order')
            ->where('status', '!=', 'void'); // Hide void orders
            
            // Apply filter based on tab selection
            if ($filter === 'cancelled') {
                $query->where('status', 'cancelled');
            } elseif ($filter === 'completed') {
                // Completed orders only
                $query->where('status', 'completed');
            } elseif ($filter === 'not_yet_shipped') {
                // Orders that exist (sales order created) but are NOT yet shipped
                // Exclude: invoiced, shipped, delivered, completed
                $query->where(function($q) {
                    $q->whereNull('picking_status')
                      ->orWhere('picking_status', '!=', 'INVOICED');
                })
                ->where(function($q) {
                    $q->whereNotIn('status', ['delivered', 'completed', 'shipped'])
                      ->where(function($q2) {
                          $q2->whereNull('shipping_status')
                             ->orWhereNotIn('shipping_status', ['delivered', 'shipped']);
                      });
                });
            } elseif ($filter === 'buy_again') {
                // Orders that can be bought again (completed/delivered orders)
                $query->whereIn('status', ['completed', 'delivered']);
            } else {
                // 'all' - show all non-void orders (default)
            }
            
            // Apply status filter if provided
            if ($status) {
                $query->where('status', $status);
            }
            
            // Apply year filter (takes precedence over months/date range when provided)
            if ($year && is_numeric($year) && (int)$year >= 2000 && (int)$year <= 2100) {
                $yearStart = (int) $year . '-01-01';
                $yearEnd = (int) $year . '-12-31';
                $effectiveStartDate = $yearStart;
                $effectiveEndDate = $yearEnd;
                $query->whereDate('transaction_date', '>=', $yearStart)
                    ->whereDate('transaction_date', '<=', $yearEnd);
            } elseif ($months && is_numeric($months) && $months > 0) {
                // Apply months filter (takes precedence over date_from/date_to if provided)
                $monthsAgo = now()->subMonths((int)$months)->startOfDay();
                $effectiveStartDate = $monthsAgo->toDateString();
                $effectiveEndDate = now()->endOfDay()->toDateString();

                $query->where('transaction_date', '>=', $monthsAgo);
            } else {
                // Apply date range filter (only if year/months not provided)
                if ($dateFrom) {
                    $query->whereDate('transaction_date', '>=', $dateFrom);
                    $effectiveStartDate = $dateFrom;
                    $effectiveEndDate = $dateTo;
                }
                if ($dateTo) {
                    $query->whereDate('transaction_date', '<=', $dateTo);
                }
            }
            
            // Apply order number search (specific search for sales order number)
            if ($orderNumber) {
                $orderNumberTerm = '%' . $orderNumber . '%';
                $query->where(function($q) use ($orderNumberTerm, $orderNumber) {
                    // Search by sales order number (invoice_no)
                    $q->where('invoice_no', 'LIKE', $orderNumberTerm);
                    
                    // Also search by exact transaction ID if it's numeric
                    if (is_numeric($orderNumber)) {
                        $q->orWhere('id', '=', $orderNumber);
                    }
                });
            }
            
            // Apply general search filter (works alongside order_number or independently)
            if ($search) {
                $searchTerm = '%' . $search . '%';
                $query->where(function($q) use ($searchTerm, $search, $orderNumber) {
                    // Search by order number (invoice_no) - only if order_number param not used
                    if (!$orderNumber) {
                        $q->where('invoice_no', 'LIKE', $searchTerm);
                    }
                    
                    // Search by transaction ID
                    if (is_numeric($search)) {
                        $q->orWhere('id', '=', $search);
                    }
                    
                    // Search by PO number (if exists in custom fields or notes)
                    $q->orWhere('additional_notes', 'LIKE', $searchTerm);
                    
                    // Search by product name in sell lines
                    $q->orWhereHas('sell_lines.product', function($productQuery) use ($searchTerm) {
                        $productQuery->where('name', 'LIKE', $searchTerm);
                    });
                    
                    // Search by variation SKU
                    $q->orWhereHas('sell_lines.variations', function($variationQuery) use ($searchTerm) {
                        $variationQuery->where('sub_sku', 'LIKE', $searchTerm)
                                       ->orWhere('var_barcode_no', 'LIKE', $searchTerm);
                    });
                });
            }
            
            $orders = $query->orderBy('created_at', 'desc')->paginate(15);
            
            // Transform the data to include sale lines with product info and receiving status
            $transformedCollection = $orders->getCollection()->map(function ($order) use ($filter) {
                $totalItems = 0;
                $receivedItems = 0;
                
                $order->sale_lines = $order->sell_lines->map(function($line) use (&$totalItems, &$receivedItems) {
                    $productImage = null;
                    
                    // Try to get image from variation media first, then product image
                    if ($line->variations && $line->variations->media && $line->variations->media->isNotEmpty()) {
                        $media = $line->variations->media->first();
                        $productImage = $media->display_url ?? asset('/uploads/media/' . rawurlencode($media->file_name));
                    } elseif ($line->product && $line->product->image) {
                        $productImage = $line->product->image_url ?? asset('/uploads/img/' . rawurlencode($line->product->image));
                    } else {
                        $productImage = asset('/img/default.png');
                    }
                    
                    // Calculate receiving status
                    $quantity = (float) $line->quantity;
                    $receivedQty = (float) ($line->received_quantity ?? 0);
                    $isReceived = $receivedQty >= $quantity;
                    
                    $totalItems++;
                    if ($isReceived) {
                        $receivedItems++;
                    }
                    
                    return [
                        'id' => $line->id,
                        'product_id' => $line->product_id,
                        'product_name' => $line->product ? $line->product->name : null,
                        'product_image' => $productImage,
                        'variation_id' => $line->variation_id,
                        'variation_name' => $line->variations ? $line->variations->name : null,
                        'quantity' => $quantity,
                        'received_quantity' => $receivedQty,
                        'is_received' => $isReceived,
                        'unit_price' => $line->unit_price,
                        'unit_price_inc_tax' => $line->unit_price_inc_tax,
                    ];
                });
                
                // Calculate pending receiving status
                $pendingReceiving = $totalItems > 0 ? ($totalItems - $receivedItems) . '/' . $totalItems : '0/0';
                $order->pending_receiving = $pendingReceiving;
                $order->all_items_received = ($receivedItems === $totalItems && $totalItems > 0);
                $order->has_pending_receiving = ($receivedItems < $totalItems && $totalItems > 0);
                
                // Remove the sell_lines relationship to avoid duplication
                unset($order->sell_lines);
                
                return $order;
            });
            
            // No additional collection filter for not_yet_shipped (already filtered in query)
            
            $orders->setCollection($transformedCollection);
            
            return response()->json([
                'status' => true,
                'data' => $orders,
                'filters_applied' => [
                    'filter' => $filter,
                    'search' => $search,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'months' => $months,
                    'year' => $year ? (int) $year : null,
                    'status' => $status,
                ],
                'server_time' => now()->toDateTimeString()
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error fetching orders', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }

    /**
     * Get standardized order response API
     * Returns orders in a clean, consistent format
     */
    public function getOrderResponse(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            
            // Get query parameters
            $filter = $request->query('filter', 'all');
            $search = $request->query('search', '');
            $orderNumber = $request->query('order_number', '');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            $months = $request->query('months');
            $year = $request->query('year');
            $status = $request->query('status');
            $page = $request->query('page', 1);
            $perPage = $request->query('per_page', 15);
            
            // Track effective date range
            $effectiveStartDate = null;
            $effectiveEndDate = null;
            
            // Build base query
            $query = Transaction::with([
                'sell_lines' => function($query) {
                    $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'unit_price', 'unit_price_inc_tax', 'received_quantity');
                },
                'sell_lines.product' => function ($query) {
                    $query->select('id', 'name', 'slug', 'image');
                },
                'sell_lines.variations' => function ($query) {
                    $query->select('id', 'product_id', 'name', 'sub_sku');
                },
                'sell_lines.variations.media' => function($query) {
                    $query->select('id', 'file_name', 'model_id');
                },
                'payment_lines' => function($query) {
                    $query->select('id', 'transaction_id', 'amount', 'method', 'paid_on');
                },
                'location' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->where('contact_id', $contact->id)
            ->where('business_id', $business_id)
            ->where('type', 'sales_order')
            ->where('status', '!=', 'void');
            
            // Apply filters (same logic as mySaleOrders)
            if ($filter === 'cancelled') {
                $query->where('status', 'cancelled');
            } elseif ($filter === 'not_yet_shipped') {
                // Orders that exist (sales order created) but are NOT yet shipped
                $query->where(function($q) {
                    $q->whereNull('picking_status')
                      ->orWhere('picking_status', '!=', 'INVOICED');
                })
                ->where(function($q) {
                    $q->whereNotIn('status', ['delivered', 'completed', 'shipped'])
                      ->where(function($q2) {
                          $q2->whereNull('shipping_status')
                             ->orWhereNotIn('shipping_status', ['delivered', 'shipped']);
                      });
                });
            } elseif ($filter === 'buy_again') {
                $query->whereIn('status', ['completed', 'delivered']);
            }
            
            if ($status) {
                $query->where('status', $status);
            }
            
            // Apply year filter (takes precedence over months/date range when provided)
            if ($year && is_numeric($year) && (int)$year >= 2000 && (int)$year <= 2100) {
                $yearStart = (int) $year . '-01-01';
                $yearEnd = (int) $year . '-12-31';
                $effectiveStartDate = $yearStart;
                $effectiveEndDate = $yearEnd;
                $query->whereDate('transaction_date', '>=', $yearStart)
                    ->whereDate('transaction_date', '<=', $yearEnd);
            } elseif ($months && is_numeric($months) && $months > 0) {
                $monthsAgo = now()->subMonths((int)$months)->startOfDay();
                $effectiveStartDate = $monthsAgo->toDateString();
                $effectiveEndDate = now()->endOfDay()->toDateString();
                $query->where('transaction_date', '>=', $monthsAgo);
            } else {
                if ($dateFrom) {
                    $query->whereDate('transaction_date', '>=', $dateFrom);
                    $effectiveStartDate = $dateFrom;
                }
                if ($dateTo) {
                    $query->whereDate('transaction_date', '<=', $dateTo);
                    $effectiveEndDate = $dateTo;
                }
            }
            
            // Apply order number search
            if ($orderNumber) {
                $orderNumberTerm = '%' . $orderNumber . '%';
                $query->where(function($q) use ($orderNumberTerm, $orderNumber) {
                    $q->where('invoice_no', 'LIKE', $orderNumberTerm);
                    if (is_numeric($orderNumber)) {
                        $q->orWhere('id', '=', $orderNumber);
                    }
                });
            }
            
            // Apply general search
            if ($search) {
                $searchTerm = '%' . $search . '%';
                $query->where(function($q) use ($searchTerm, $search, $orderNumber) {
                    if (!$orderNumber) {
                        $q->where('invoice_no', 'LIKE', $searchTerm);
                    }
                    if (is_numeric($search)) {
                        $q->orWhere('id', '=', $search);
                    }
                    $q->orWhere('additional_notes', 'LIKE', $searchTerm);
                    $q->orWhereHas('sell_lines.product', function($productQuery) use ($searchTerm) {
                        $productQuery->where('name', 'LIKE', $searchTerm);
                    });
                    $q->orWhereHas('sell_lines.variations', function($variationQuery) use ($searchTerm) {
                        $variationQuery->where('sub_sku', 'LIKE', $searchTerm)
                                       ->orWhere('var_barcode_no', 'LIKE', $searchTerm);
                    });
                });
            }
            
            // Get paginated orders
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Format orders for response
            $formattedOrders = $orders->getCollection()->map(function ($order) {
                $totalItems = 0;
                $receivedItems = 0;
                
                $items = $order->sell_lines->map(function($line) use (&$totalItems, &$receivedItems) {
                    $productImage = null;
                    
                    if ($line->variations && $line->variations->media && $line->variations->media->isNotEmpty()) {
                        $media = $line->variations->media->first();
                        $productImage = $media->display_url ?? asset('/uploads/media/' . rawurlencode($media->file_name));
                    } elseif ($line->product && $line->product->image) {
                        $productImage = $line->product->image_url ?? asset('/uploads/img/' . rawurlencode($line->product->image));
                    } else {
                        $productImage = asset('/img/default.png');
                    }
                    
                    $quantity = (float) $line->quantity;
                    $receivedQty = (float) ($line->received_quantity ?? 0);
                    $isReceived = $receivedQty >= $quantity;
                    
                    $totalItems++;
                    if ($isReceived) {
                        $receivedItems++;
                    }
                    
                    return [
                        'id' => $line->id,
                        'product_id' => $line->product_id,
                        'product_name' => $line->product ? $line->product->name : null,
                        'product_slug' => $line->product ? $line->product->slug : null,
                        'product_image' => $productImage,
                        'variation_id' => $line->variation_id,
                        'variation_name' => $line->variations ? $line->variations->name : null,
                        'variation_sku' => $line->variations ? $line->variations->sub_sku : null,
                        'quantity' => $quantity,
                        'received_quantity' => $receivedQty,
                        'is_received' => $isReceived,
                        'unit_price' => (float) $line->unit_price,
                        'unit_price_inc_tax' => (float) $line->unit_price_inc_tax,
                        'line_total' => (float) ($line->unit_price_inc_tax * $quantity),
                    ];
                });
                
                // Helper function to format dates safely
                $formatDate = function($date, $format = 'Y-m-d') {
                    if (!$date) return null;
                    if (is_string($date)) {
                        try {
                            return \Carbon\Carbon::parse($date)->format($format);
                        } catch (\Exception $e) {
                            return $date; // Return as-is if parsing fails
                        }
                    }
                    if (method_exists($date, 'format')) {
                        return $date->format($format);
                    }
                    return $date;
                };
                
                // Calculate payment summary
                $totalPaid = $order->payment_lines ? $order->payment_lines->sum('amount') : 0;
                $amountDue = $order->final_total - $totalPaid;
                
                return [
                    'order_id' => $order->id,
                    'order_number' => $order->invoice_no,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status ?? 'due',
                    'shipping_status' => $order->shipping_status ?? null,
                    'picking_status' => $order->picking_status ?? null,
                    'transaction_date' => $formatDate($order->transaction_date, 'Y-m-d'),
                    'transaction_datetime' => $formatDate($order->transaction_date, 'Y-m-d H:i:s'),
                    'location' => $order->location ? [
                        'id' => $order->location->id,
                        'name' => $order->location->name,
                    ] : null,
                    'items' => $items,
                    'items_summary' => [
                        'total_items' => $totalItems,
                        'received_items' => $receivedItems,
                        'pending_items' => $totalItems - $receivedItems,
                        'all_received' => ($receivedItems === $totalItems && $totalItems > 0),
                        'has_pending' => ($receivedItems < $totalItems && $totalItems > 0),
                    ],
                    'pricing' => [
                        'subtotal' => (float) ($order->total_before_tax ?? 0),
                        'tax_amount' => (float) ($order->tax_amount ?? 0),
                        'discount_amount' => (float) ($order->discount_amount ?? 0),
                        'discount_type' => $order->discount_type ?? null,
                        'shipping_charges' => (float) ($order->shipping_charges ?? 0),
                        'final_total' => (float) $order->final_total,
                    ],
                    'payment' => [
                        'total_paid' => (float) $totalPaid,
                        'amount_due' => (float) $amountDue,
                        'payment_methods' => $order->payment_lines ? $order->payment_lines->map(function($payment) use ($formatDate) {
                            return [
                                'method' => $payment->method,
                                'amount' => (float) $payment->amount,
                                'paid_on' => $formatDate($payment->paid_on, 'Y-m-d H:i:s'),
                            ];
                        })->values() : [],
                    ],
                    'created_at' => $formatDate($order->created_at, 'Y-m-d H:i:s'),
                    'updated_at' => $formatDate($order->updated_at, 'Y-m-d H:i:s'),
                ];
            });
            
            // Calculate report totals
            $reportQuery = (clone $query);
            $reportTotals = $reportQuery->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(final_total), 0) as total_amount')
            )->first();
            
            $statusBreakdownRaw = (clone $query)
                ->select(
                    'status',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('COALESCE(SUM(final_total), 0) as total_amount')
                )
                ->groupBy('status')
                ->get();
            
            $statusBreakdown = $statusBreakdownRaw->map(function ($row) {
                return [
                    'status' => $row->status,
                    'count' => (int) $row->count,
                    'total_amount' => (float) $row->total_amount,
                ];
            })->values();
            
            // No additional filter for not_yet_shipped (already filtered in query)
            
            // Check if download is requested
            $download = $request->query('download');
            if ($download && in_array(strtolower($download), ['pdf', 'csv', 'excel', 'xlsx'])) {
                // Get all orders without pagination for export
                $allOrdersQuery = (clone $query);
                $allOrders = $allOrdersQuery->orderBy('created_at', 'desc')->get();
                
                // Format all orders for export
                $allFormattedOrders = $allOrders->map(function ($order) use (&$totalItems, &$receivedItems) {
                    $totalItems = 0;
                    $receivedItems = 0;
                    
                    $items = $order->sell_lines->map(function($line) use (&$totalItems, &$receivedItems) {
                        $quantity = (float) $line->quantity;
                        $receivedQty = (float) ($line->received_quantity ?? 0);
                        $isReceived = $receivedQty >= $quantity;
                        
                        $totalItems++;
                        if ($isReceived) {
                            $receivedItems++;
                        }
                        
                        return [
                            'id' => $line->id,
                            'product_id' => $line->product_id,
                            'product_name' => $line->product ? $line->product->name : null,
                            'product_slug' => $line->product ? $line->product->slug : null,
                            'variation_id' => $line->variation_id,
                            'variation_name' => $line->variations ? $line->variations->name : null,
                            'variation_sku' => $line->variations ? $line->variations->sub_sku : null,
                            'quantity' => $quantity,
                            'received_quantity' => $receivedQty,
                            'is_received' => $isReceived,
                            'unit_price' => (float) $line->unit_price,
                            'unit_price_inc_tax' => (float) $line->unit_price_inc_tax,
                            'line_total' => (float) ($line->unit_price_inc_tax * $quantity),
                        ];
                    });
                    
                    $totalPaid = $order->payment_lines ? $order->payment_lines->sum('amount') : 0;
                    $amountDue = $order->final_total - $totalPaid;
                    
                    // Helper function to format dates safely
                    $formatDate = function($date, $format = 'Y-m-d') {
                        if (!$date) return null;
                        if (is_string($date)) {
                            try {
                                return \Carbon\Carbon::parse($date)->format($format);
                            } catch (\Exception $e) {
                                return $date; // Return as-is if parsing fails
                            }
                        }
                        if (method_exists($date, 'format')) {
                            return $date->format($format);
                        }
                        return $date;
                    };
                    
                    return [
                        'order_id' => $order->id,
                        'order_number' => $order->invoice_no,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status ?? 'due',
                        'shipping_status' => $order->shipping_status ?? null,
                        'picking_status' => $order->picking_status ?? null,
                        'transaction_date' => $formatDate($order->transaction_date, 'Y-m-d'),
                        'transaction_datetime' => $formatDate($order->transaction_date, 'Y-m-d H:i:s'),
                        'location' => $order->location ? [
                            'id' => $order->location->id,
                            'name' => $order->location->name,
                        ] : null,
                        'items' => $items,
                        'items_summary' => [
                            'total_items' => $totalItems,
                            'received_items' => $receivedItems,
                            'pending_items' => $totalItems - $receivedItems,
                            'all_received' => ($receivedItems === $totalItems && $totalItems > 0),
                            'has_pending' => ($receivedItems < $totalItems && $totalItems > 0),
                        ],
                        'pricing' => [
                            'subtotal' => (float) ($order->total_before_tax ?? 0),
                            'tax_amount' => (float) ($order->tax_amount ?? 0),
                            'discount_amount' => (float) ($order->discount_amount ?? 0),
                            'discount_type' => $order->discount_type ?? null,
                            'shipping_charges' => (float) ($order->shipping_charges ?? 0),
                            'final_total' => (float) $order->final_total,
                        ],
                        'payment' => [
                            'total_paid' => (float) $totalPaid,
                            'amount_due' => (float) $amountDue,
                            'payment_methods' => $order->payment_lines ? $order->payment_lines->map(function($payment) use ($formatDate) {
                                return [
                                    'method' => $payment->method,
                                    'amount' => (float) $payment->amount,
                                    'paid_on' => $formatDate($payment->paid_on, 'Y-m-d H:i:s'),
                                ];
                            })->values() : [],
                        ],
                        'created_at' => $formatDate($order->created_at, 'Y-m-d H:i:s'),
                        'updated_at' => $formatDate($order->updated_at, 'Y-m-d H:i:s'),
                    ];
                })->toArray();
                
                // No additional filter for not_yet_shipped (already filtered in query)
                
                // Generate filename
                $filename = 'orders_' . now()->format('Y-m-d_His');
                if ($effectiveStartDate && $effectiveEndDate) {
                    $filename .= '_' . $effectiveStartDate . '_to_' . $effectiveEndDate;
                }
                
                // Export based on format
                if (strtolower($download) === 'pdf') {
                    // Get business information and logo
                    $business = Business::find($business_id);
                    $businessLogo = null;
                    if ($business && !empty($business->logo)) {
                        $logoPath = public_path('uploads/business_logos/' . $business->logo);
                        if (file_exists($logoPath)) {
                            $businessLogo = $logoPath;
                        }
                    }
                    
                    // Generate PDF
                    $body = view('ecom.orders_pdf')
                        ->with([
                            'orders' => array_values($allFormattedOrders), // Ensure array is indexed
                            'business' => $business,
                            'business_logo' => $businessLogo,
                            'filters' => [
                                'filter' => $filter,
                                'search' => $search,
                                'order_number' => $orderNumber,
                                'date_from' => $dateFrom,
                                'date_to' => $dateTo,
                                'months' => $months ?? null,
                                'year' => $year ? (int) $year : null,
                                'status' => $status,
                            ],
                            'date_range' => [
                                'start_date' => $effectiveStartDate,
                                'end_date' => $effectiveEndDate,
                            ],
                            'generated_at' => now()->format('Y-m-d H:i:s'),
                        ])
                        ->render();
                    
                    $mpdf = new \Mpdf\Mpdf([
                        'tempDir' => public_path('uploads/temp'),
                        'mode' => 'utf-8',
                        'autoScriptToLang' => true,
                        'autoLangToFont' => true,
                        'autoVietnamese' => true,
                        'autoArabic' => true,
                        'margin_top' => 10,
                        'margin_bottom' => 10,
                        'margin_left' => 10,
                        'margin_right' => 10,
                        'format' => 'A4',
                        'orientation' => 'P', // Portrait orientation for A4 size
                    ]);
                    
                    $mpdf->SetTitle('Orders Report - ' . $filename);
                    $mpdf->WriteHTML($body);
                    
                    // Save PDF download to database for tracking
                    $orderNumbers = collect($allFormattedOrders)->pluck('order_number')->toArray();
                    $orderIds = collect($allFormattedOrders)->pluck('order_id')->toArray();
                    
                    OrderDownloadLog::create([
                        'contact_id' => $contact->id,
                        'business_id' => $business_id,
                        'download_type' => 'pdf',
                        'filename' => $filename . '.pdf',
                        'total_orders' => count($allFormattedOrders),
                        'order_numbers' => $orderNumbers,
                        'order_ids' => $orderIds,
                        'filters' => [
                            'filter' => $filter,
                            'search' => $search,
                            'order_number' => $orderNumber,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'months' => $months ?? null,
                            'status' => $status,
                        ],
                        'date_range' => [
                            'start_date' => $effectiveStartDate,
                            'end_date' => $effectiveEndDate,
                        ],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    
                    // Output PDF as string and return as proper response with CORS headers
                    $pdfContent = $mpdf->Output('', 'S'); // 'S' returns as string
                    
                    return response($pdfContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"')
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, Origin, X-Requested-With')
                        ->header('Access-Control-Allow-Credentials', 'true');
                } elseif (strtolower($download) === 'csv') {
                    // Save CSV download to database for tracking
                    $orderNumbers = collect($allFormattedOrders)->pluck('order_number')->toArray();
                    $orderIds = collect($allFormattedOrders)->pluck('order_id')->toArray();
                    
                    OrderDownloadLog::create([
                        'contact_id' => $contact->id,
                        'business_id' => $business_id,
                        'download_type' => 'csv',
                        'filename' => $filename . '.csv',
                        'total_orders' => count($allFormattedOrders),
                        'order_numbers' => $orderNumbers,
                        'order_ids' => $orderIds,
                        'filters' => [
                            'filter' => $filter,
                            'search' => $search,
                            'order_number' => $orderNumber,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'months' => $months ?? null,
                            'status' => $status,
                        ],
                        'date_range' => [
                            'start_date' => $effectiveStartDate,
                            'end_date' => $effectiveEndDate,
                        ],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    
                    return Excel::download(new OrdersExport($allFormattedOrders), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
                } else {
                    // Save Excel download to database for tracking
                    $orderNumbers = collect($allFormattedOrders)->pluck('order_number')->toArray();
                    $orderIds = collect($allFormattedOrders)->pluck('order_id')->toArray();
                    
                    OrderDownloadLog::create([
                        'contact_id' => $contact->id,
                        'business_id' => $business_id,
                        'download_type' => 'excel',
                        'filename' => $filename . '.xlsx',
                        'total_orders' => count($allFormattedOrders),
                        'order_numbers' => $orderNumbers,
                        'order_ids' => $orderIds,
                        'filters' => [
                            'filter' => $filter,
                            'search' => $search,
                            'order_number' => $orderNumber,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'months' => $months ?? null,
                            'status' => $status,
                        ],
                        'date_range' => [
                            'start_date' => $effectiveStartDate,
                            'end_date' => $effectiveEndDate,
                        ],
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    
                    return Excel::download(new OrdersExport($allFormattedOrders), $filename . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                }
            }
            
            // Update pagination collection
            $orders->setCollection($formattedOrders);
            
            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => [
                    'orders' => $orders->items(),
                    'pagination' => [
                        'current_page' => $orders->currentPage(),
                        'per_page' => $orders->perPage(),
                        'total' => $orders->total(),
                        'last_page' => $orders->lastPage(),
                        'from' => $orders->firstItem(),
                        'to' => $orders->lastItem(),
                    ],
                ],
                'order_report' => [
                    'total_orders' => (int) ($reportTotals->total_orders ?? 0),
                    'total_amount' => (float) ($reportTotals->total_amount ?? 0),
                    'status_breakdown' => $statusBreakdown,
                    'start_date' => $effectiveStartDate,
                    'end_date' => $effectiveEndDate,
                ],
                'filters_applied' => [
                    'filter' => $filter,
                    'search' => $search,
                    'order_number' => $orderNumber,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'months' => $months ?? null,
                    'year' => $year ? (int) $year : null,
                    'status' => $status,
                ],
                'meta' => [
                    'server_time' => now()->toDateTimeString(),
                    'timezone' => config('app.timezone'),
                ]
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching orders',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function mySaleInvoices(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $sale_type = 'sell';
            $orders = Transaction::where('contact_id', $contact->id)->where('business_id', $business_id)->where('type', 'sell')->orderBy('created_at', 'desc')->paginate(15);
            return response()->json([
                'status' => true,
                'data' => $orders
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }

    public function mySaleReturns(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $sale_type = 'sales_order';
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'ordered');
            $orders = $sells->with('payment_lines')->paginate(15);
            return response()->json([
                'status' => true,
                'data' => $orders,
                 // Effective date range used for the report (start & end)
                 'start_date' => $effectiveStartDate,
                 'end_date' => $effectiveEndDate,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }

    public function getOrderDetails($orderId)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            $orders = Transaction::with([
                'payment_lines',
                'sell_lines',
                'sell_lines.product' => function ($query) {
                    $query->select('id', 'name', 'slug','image'); 
                },
                'sell_lines.variations' => function ($query) {
                    $query->select('id','product_id','name','sub_sku', 'var_barcode_no'); 
                },
                'sell_lines.variations.media' => function($query) {
                    $query->select('id', 'file_name', 'model_id');
                }
            ])
            ->where('contact_id', $contact->id)
            ->where('business_id', $business_id)
            ->orderBy('created_at', 'desc')
            ->where('id', $orderId)
            ->first();

            try {
                if ($orders->payment_status == '') {
                    $orders->payment_status = 'due';
                }
                // On-account orders with no payments should show as "due", not "partial"
                $totalPaid = $orders->payment_lines ? $orders->payment_lines->sum(function ($p) {
                    return ($p->is_return ?? 0) ? -1 * (float) $p->amount : (float) $p->amount;
                }) : 0;
                if ($orders->payment_status === 'partial' && $totalPaid <= 0) {
                    $orders->payment_status = 'due';
                }
            } catch (\Throwable $th) {}

            $statusMap = [
                'intransit' => null,
                'packed' => null,
                'shipped' => null,
            ];

            $relatedInvoice = null;
            if ($orders->type === 'sales_order') {
                $relatedInvoice = Transaction::with([
                    'sell_lines',
                    'sell_lines.product' => function ($query) {
                        $query->select('id', 'name', 'slug','image'); 
                    },
                    'sell_lines.variations' => function ($query) {
                        $query->select('id','product_id','name','sub_sku', 'var_barcode_no'); 
                    },
                    'sell_lines.variations.media' => function($query) {
                        $query->select('id', 'file_name', 'model_id');
                    }
                ])
                ->where('type', 'sell')
                ->where('business_id', $orders->business_id)    
                ->where('contact_id', $orders->contact_id)
                ->where(function($query) use ($orderId) {
                    $query->whereRaw('JSON_CONTAINS(sales_order_ids, ?)', [json_encode((string)$orderId)]);
                })
                ->first();
            }

            $intransitTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                ->where('status', 'intransit')
                ->first();

            $invoiceIntransitTracking = null;
            if ($relatedInvoice) {
                $invoiceIntransitTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                    ->where('status', 'intransit')
                    ->first();
            }

            if ($intransitTracking) {
                $intransitDate = $orders->created_at;
                $trackingDate = $intransitTracking->status_date ?? $intransitTracking->created_at ?? $intransitTracking->updated_at;
                $finalIntransitDate = $trackingDate ?? $intransitDate;
                $statusMap['intransit'] = [
                    'status' => 'intransit',
                    'date' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d') : null,
                    'datetime' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d H:i:s') : null,
                    'message' => 'your order is in transit',
                ];
            } elseif ($invoiceIntransitTracking) {
                $intransitDate = $orders->created_at;
                $trackingDate = $invoiceIntransitTracking->status_date ?? $invoiceIntransitTracking->created_at ?? $invoiceIntransitTracking->updated_at;
                $finalIntransitDate = $trackingDate ?? $intransitDate;
                $statusMap['intransit'] = [
                    'status' => 'intransit',
                    'date' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d') : null,
                    'datetime' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d H:i:s') : null,
                    'message' => 'your order is in transit',
                ];
            } else {
                $intransitDate = $orders->created_at;
                $statusMap['intransit'] = [
                    'status' => 'intransit',
                    'date' => $intransitDate ? $intransitDate->format('Y-m-d') : null,
                    'datetime' => $intransitDate ? $intransitDate->format('Y-m-d H:i:s') : null,
                    'message' => 'your order is in transit',
                ];
            }

            if ($orders->status === 'ordered') {
                $intransitTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                    ->where('status', 'intransit')
                    ->first();
                $invoiceIntransitTracking = null;
                if ($relatedInvoice) {
                    $invoiceIntransitTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                        ->where('status', 'intransit')
                        ->first();
                }
                if ($intransitTracking) {
                    $intransitDate = $orders->created_at;
                    $trackingDate = $intransitTracking->status_date ?? $intransitTracking->created_at ?? $intransitTracking->updated_at;
                    $finalIntransitDate = $trackingDate ?? $intransitDate;
                    $statusMap['intransit'] = [
                        'status' => 'intransit',
                        'date' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d') : null,
                        'datetime' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is in transit',
                    ];
                } elseif ($invoiceIntransitTracking) {
                    $intransitDate = $orders->created_at;
                    $trackingDate = $invoiceIntransitTracking->status_date ?? $invoiceIntransitTracking->created_at ?? $invoiceIntransitTracking->updated_at;
                    $finalIntransitDate = $trackingDate ?? $intransitDate;
                    $statusMap['intransit'] = [
                        'status' => 'intransit',
                        'date' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d') : null,
                        'datetime' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is in transit',
                    ];
                } else {
                    $intransitDate = $orders->created_at;
                    $statusMap['intransit'] = [
                        'status' => 'intransit',
                        'date' => $intransitDate ? $intransitDate->format('Y-m-d') : null,
                        'datetime' => $intransitDate ? $intransitDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is in transit',
                    ];
                }
            }

            $packedTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                ->where('status', 'packed')
                ->first();
            $invoicePackedTracking = null;
            if ($relatedInvoice) {
                $invoicePackedTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                    ->where('status', 'packed')
                    ->first();
            }
            $hasPackedTracking = $packedTracking !== null || $invoicePackedTracking !== null;
            $pickingStatus = $orders->picking_status ? strtoupper(trim($orders->picking_status)) : null;
            $isPackedByStatus = in_array($pickingStatus, ['PICKED', 'PACKED', 'INVOICED'], true);
            $invoicePickingStatus = null;
            $isInvoicePackedByStatus = false;
            if ($relatedInvoice && $relatedInvoice->picking_status) {
                $invoicePickingStatus = strtoupper(trim($relatedInvoice->picking_status));
                $isInvoicePackedByStatus = in_array($invoicePickingStatus, ['PICKED', 'PACKED', 'INVOICED'], true);
            }
            $isPacked = $isPackedByStatus || $isInvoicePackedByStatus || $hasPackedTracking;
            if ($isPacked) {
                if ($packedTracking) {
                    $packedDate = $orders->updated_at ?? $orders->created_at;
                    $trackingDate = $packedTracking->status_date ?? $packedTracking->created_at ?? $packedTracking->updated_at;
                    $finalPackedDate = $trackingDate ?? $packedDate;
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $finalPackedDate ? $finalPackedDate->format('Y-m-d') : null,
                        'datetime' => $finalPackedDate ? $finalPackedDate->format('Y-m-d H:i:s') : null,
                    ];
                } elseif ($invoicePackedTracking) {
                    $packedDate = $relatedInvoice->updated_at ?? $relatedInvoice->created_at;
                    $trackingDate = $invoicePackedTracking->status_date ?? $invoicePackedTracking->created_at ?? $invoicePackedTracking->updated_at;
                    $finalPackedDate = $trackingDate ?? $packedDate;
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $finalPackedDate ? $finalPackedDate->format('Y-m-d') : null,
                        'datetime' => $finalPackedDate ? $finalPackedDate->format('Y-m-d H:i:s') : null,
                    ];
                } elseif ($isPackedByStatus) {
                    $packedDate = $orders->updated_at ?? $orders->created_at;
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $packedDate ? $packedDate->format('Y-m-d') : null,
                        'datetime' => $packedDate ? $packedDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is packed',
                    ];
                } elseif ($isInvoicePackedByStatus && $relatedInvoice) {
                    $packedDate = $relatedInvoice->updated_at ?? $relatedInvoice->created_at;
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $packedDate ? $packedDate->format('Y-m-d') : null,
                        'datetime' => $packedDate ? $packedDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is packed',
                    ];
                }
            }

            $shippedTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                ->where('status', 'shipped')
                ->first();
            $invoiceShippedTracking = null;
            if ($relatedInvoice) {
                $invoiceShippedTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                    ->where('status', 'shipped')
                    ->first();
            }
            $hasShippedTracking = $shippedTracking !== null || $invoiceShippedTracking !== null;
            $isShipped = false;

            if ($orders->shipping_status === 'shipped') {
                $isShipped = true;
            } elseif ($orders->status === 'completed') {
                if ($orders->type === 'sales_order' && $relatedInvoice) {
                    if ($relatedInvoice->shipping_status === 'shipped' || !empty($relatedInvoice->shipment)) {
                        $isShipped = true;
                    } else {
                        $isShipped = true;
                    }
                } else {
                    $isShipped = true;
                }
            } elseif ($relatedInvoice && ($relatedInvoice->shipping_status === 'shipped' || !empty($relatedInvoice->shipment))) {
                $isShipped = true;
            }
            if ($hasShippedTracking) {
                $isShipped = true;
            }
            if ($isShipped) {
                if ($orders->type === 'sales_order' && isset($relatedInvoice)) {
                    $shippedDate = $relatedInvoice->updated_at ?? $relatedInvoice->created_at;
                } else {
                    $shippedDate = $orders->updated_at ?? $orders->created_at;
                }
                if ($shippedTracking) {
                    $trackingDate = $shippedTracking->status_date ?? $shippedTracking->created_at ?? $shippedTracking->updated_at;
                    $finalShippedDate = $trackingDate ?? $shippedDate;
                    $statusMap['shipped'] = [
                        'status' => 'shipped',
                        'date' => $finalShippedDate ? $finalShippedDate->format('Y-m-d') : null,
                        'datetime' => $finalShippedDate ? $finalShippedDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is shipped',
                    ];
                } elseif ($invoiceShippedTracking) {
                    $trackingDate = $invoiceShippedTracking->status_date ?? $invoiceShippedTracking->created_at ?? $invoiceShippedTracking->updated_at;
                    $finalShippedDate = $trackingDate ?? $shippedDate;
                    $statusMap['shipped'] = [
                        'status' => 'shipped',
                        'date' => $finalShippedDate ? $finalShippedDate->format('Y-m-d') : null,
                        'datetime' => $finalShippedDate ? $finalShippedDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is shipped',
                    ];
                } else {
                    $statusMap['shipped'] = [
                        'status' => 'shipped',
                        'date' => $shippedDate ? $shippedDate->format('Y-m-d') : null,
                        'datetime' => $shippedDate ? $shippedDate->format('Y-m-d H:i:s') : null,
                        'message' => 'your order is shipped',
                    ];
                }
            }

            $pickingStatusDisplay = $orders->picking_status;
            if ($pickingStatusDisplay === null && $orders->status === 'ordered') {
                $pickingStatusDisplay = 'ordered';
            }

            $invoiceNo = $orders->invoice_no;
            if (isset($relatedInvoice) && !empty($relatedInvoice->invoice_no)) {
                $invoiceNo = $relatedInvoice->invoice_no;
            }

            $sellInvoiceData = null;
            if ($relatedInvoice) {
                $sellInvoiceData = [
                    'invoice_id' => $relatedInvoice->id,
                    'invoice_no' => $relatedInvoice->invoice_no,
                    'transaction_date' => $relatedInvoice->transaction_date,
                    'final_total' => $relatedInvoice->final_total,
                    'total_before_tax' => $relatedInvoice->total_before_tax,
                    'tax_amount' => $relatedInvoice->tax_amount,
                    'discount_amount' => $relatedInvoice->discount_amount,
                    'discount_type' => $relatedInvoice->discount_type,
                    'payment_status' => $relatedInvoice->payment_status ?? 'due',
                    'shipping_status' => $relatedInvoice->shipping_status,
                    'status' => $relatedInvoice->status,
                    'picking_status' => $relatedInvoice->picking_status,
                    'shipment' => $relatedInvoice->shipment,
                    'created_at' => $relatedInvoice->created_at,
                    'updated_at' => $relatedInvoice->updated_at,
                    'reward_points' => [
                        'points_used' => (int) ($relatedInvoice->rp_redeemed ?? 0),
                        'dollars_used' => (float) ($relatedInvoice->rp_redeemed_amount ?? 0),
                    ],
                    'sell_lines' => $relatedInvoice->sell_lines->map(function($line) {
                        return [
                            'id' => $line->id,
                            'product_id' => $line->product_id,
                            'variation_id' => $line->variation_id,
                            'quantity' => $line->quantity,
                            'unit_price' => $line->unit_price,
                            'unit_price_inc_tax' => $line->unit_price_inc_tax,
                            'item_tax' => $line->item_tax,
                            'tax_id' => $line->tax_id,
                            'line_discount_type' => $line->line_discount_type,
                            'line_discount_amount' => $line->line_discount_amount,
                            'unit_price_before_discount' => $line->unit_price_before_discount,
                            'product' => $line->product,
                            'variations' => $line->variations ? [
                                'id' => $line->variations->id,
                                'product_id' => $line->variations->product_id,
                                'name' => $line->variations->name,
                                'sub_sku' => $line->variations->sub_sku,
                                'var_barcode_no' => $line->variations->var_barcode_no,
                                'media' => $line->variations->media,
                            ] : null,
                        ];
                    }),
                    'payment_lines' => $relatedInvoice->payment_lines,
                    'items_total_tax' => $relatedInvoice->sell_lines->sum(function($line) {
                        return $line->item_tax * $line->quantity;
                    }),
                ];
            }

            // Use billing address from transaction (snapshot at checkout) when present; else fall back to contact
            $hasTransactionBilling = ! empty(trim((string) ($orders->billing_address1 ?? '')));
            if ($hasTransactionBilling) {
                $billingAddressString = trim(implode(' ', array_filter([
                    $orders->billing_address1 ?? '',
                    $orders->billing_address2 ?? '',
                    $orders->billing_city ?? '',
                    $orders->billing_state ?? '',
                    $orders->billing_country ?? '',
                    $orders->billing_zip ?? '',
                ])));
            } else {
                $billingAddressString = trim(($contact->address_line_1 ?? '') . ' ' . ($contact->address_line_2 ?? '') . ' ' . ($contact->city ?? '') . ' ' . ($contact->state ?? '') . ' ' . ($contact->country ?? '') . ' ' . ($contact->zip_code ?? ''));
            }

            // Reward points: points used and dollars used on this order
            // In many cases the actual redemption is stored on the related SELL invoice,
            // not on the original SALES ORDER. To make the API consistent for the
            // customer app, we prefer the invoice values when present and fall back
            // to the order values if the invoice doesn't have them.
            $orderPointsUsed = (int) ($orders->rp_redeemed ?? 0);
            $orderDollarsUsed = (float) ($orders->rp_redeemed_amount ?? 0);

            $invoicePointsUsed = isset($relatedInvoice) ? (int) ($relatedInvoice->rp_redeemed ?? 0) : 0;
            $invoiceDollarsUsed = isset($relatedInvoice) ? (float) ($relatedInvoice->rp_redeemed_amount ?? 0) : 0;

            // Prefer invoice values if any points were redeemed there; otherwise use order values.
            $rewardPointsUsed = $invoicePointsUsed > 0 ? $invoicePointsUsed : $orderPointsUsed;
            $rewardPointsDollarsUsed = $invoiceDollarsUsed > 0 ? $invoiceDollarsUsed : $orderDollarsUsed;
            $rewardPoints = [
                'points_used' => $rewardPointsUsed,
                'dollars_used' => $rewardPointsDollarsUsed,
            ];

            // Gift card information: prefer invoice values if present, otherwise use order values
            $orderGiftCardAmount = (float) ($orders->gift_card_amount ?? 0);
            $invoiceGiftCardAmount = isset($relatedInvoice) ? (float) ($relatedInvoice->gift_card_amount ?? 0) : 0;
            $giftCardAmount = $invoiceGiftCardAmount > 0 ? $invoiceGiftCardAmount : $orderGiftCardAmount;
            
            // Find gift card line items (where product_id is null) - these are purchased gift cards
            $giftCardLines = $orders->sell_lines->filter(function($line) {
                return is_null($line->product_id) && !empty($line->sell_line_note) && 
                       (stripos($line->sell_line_note, 'gift card') !== false);
            })->map(function($line) {
                return [
                    'id' => $line->id,
                    'product_id' => null,
                    'variation_id' => null,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'unit_price_inc_tax' => $line->unit_price_inc_tax,
                    'item_tax' => $line->item_tax,
                    'tax_id' => $line->tax_id,
                    'line_discount_type' => $line->line_discount_type,
                    'line_discount_amount' => $line->line_discount_amount,
                    'unit_price_before_discount' => $line->unit_price_before_discount,
                    'sell_line_note' => $line->sell_line_note,
                    'product' => null,
                    'variations' => null,
                    'is_gift_card' => true,
                ];
            });
            
            // If gift card was applied (used as payment) but not saved as sell_line, create virtual line item
            if ($giftCardAmount > 0 && $giftCardLines->isEmpty()) {
                $giftCardLines = collect([[
                    'id' => null,
                    'product_id' => null,
                    'variation_id' => null,
                    'quantity' => 1,
                    'unit_price' => $giftCardAmount,
                    'unit_price_inc_tax' => $giftCardAmount,
                    'item_tax' => 0,
                    'tax_id' => null,
                    'line_discount_type' => null,
                    'line_discount_amount' => 0,
                    'unit_price_before_discount' => $giftCardAmount,
                    'sell_line_note' => 'Gift Card Applied',
                    'product' => null,
                    'variations' => null,
                    'is_gift_card' => true,
                    'is_applied_gift_card' => true, // Flag to indicate this is an applied gift card, not purchased
                ]]);
            }

            return response()->json([
                'status' => true,
                'data' => $orders,
                'items_total_tax' => $orders->sell_lines->sum(function($line) {
                    return $line->item_tax * $line->quantity;
                }),
                'billing_address' => $billingAddressString,
                'order_id' => (string)$orderId,
                'invoice_no' => $invoiceNo,
                'order_status' => $orders->status,
                'picking_status' => $pickingStatusDisplay,
                'intransit_status' => $statusMap['intransit'],
                'packed_status' => $statusMap['packed'],
                'shipped_status' => $statusMap['shipped'],
                'reward_points' => $rewardPoints,
                'gift_card_amount' => $giftCardAmount,
                'gift_card_lines' => $giftCardLines->values()->all(),
                'is_gift' => (bool) ($orders->is_gift ?? false),
                'hide_prices_for_recipient' => (bool) ($orders->hide_prices_for_recipient ?? false),
                'SellInvoice' => $sellInvoiceData
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Error', 'error' => $th->getMessage() . ' at ' . $th->getFile()]);
        }
    }
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null,
        $is_package_slip = false,
        $from_pos_screen = true,
        $invoice_layout_id = null,
        $is_delivery_note = false,
        $use_shipping_packing_slip_view = false
    ) {
        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        if ($from_pos_screen && $location_details->print_receipt_on_invoice != 1) {
            return $output;
        }
        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $invoice_layout_id);

        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;
        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];


        $receipt_details->currency = $currency_details; // later fix $receipt_details['currency']

        if ($is_package_slip) {
            $view_name = $use_shipping_packing_slip_view ? 'sale_pos.receipts.shipping_packing_slip' : 'sale_pos.receipts.packing_slip';
            $output['html_content'] = view($view_name, compact('receipt_details'))->render();

            return $output;
        }

        if ($is_delivery_note) {
            $output['html_content'] = view('sale_pos.receipts.delivery_note', compact('receipt_details'))->render();

            return $output;
        }

        $output['print_title'] = $receipt_details->invoice_no;
        //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
            $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';

            $output['html_content'] = view($layout, compact('receipt_details'))->render();
        }

        return $output;
    }
    public function printInvoice(Request $request, $orderId)
    {
        try {
            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];

            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;

            $transaction = Transaction::
                // where('business_id', $business_id)
                // ->
                where('id', $orderId)
                ->where('contact_id', $contact->id)
                ->with(['location'])
                ->first();

            if (empty($transaction)) {
                return $output;
            }

            $printer_type = 'browser';
            if (!empty(request()->input('check_location')) && request()->input('check_location') == true) {
                $printer_type = $transaction->location->receipt_printer_type;
            }

            $is_package_slip = !empty($request->input('package_slip')) ? true : false;
            $is_delivery_note = !empty($request->input('delivery_note')) ? true : false;

            $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;
            $receipt = $this->receiptContent($business_id, $transaction->location_id, $orderId, $printer_type, $is_package_slip, false, $invoice_layout_id, $is_delivery_note);

            if (!empty($receipt)) {
                $output = ['success' => 1, 'receipt' => $receipt];
            }
        } catch (\Exception $e) {
            Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Download packing slip / shipping PDF for an order (Amazon-style; prices hidden when gift)
     * GET /api/customer/my-order/{orderId}/packing-slip-pdf
     */
    public function packingSlipPdf(Request $request, $orderId)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
            }

            $transaction = Transaction::where('id', $orderId)
                ->where('contact_id', $contact->id)
                ->with(['location'])
                ->first();

            if (empty($transaction)) {
                return response()->json(['status' => false, 'message' => 'Order not found'], 404);
            }

            $business_id = $contact->business_id;
            $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;
            $receipt = $this->receiptContent(
                $business_id,
                $transaction->location_id,
                (int) $orderId,
                'browser',
                true,  // is_package_slip
                false, // from_pos_screen (so receipt is always generated)
                $invoice_layout_id,
                false, // is_delivery_note
                true   // use_shipping_packing_slip_view
            );

            if (empty($receipt['html_content'])) {
                return response()->json(['status' => false, 'message' => 'Could not generate packing slip'], 422);
            }

            $body = $receipt['html_content'];
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => config('constants.mpdf_temp_path', storage_path('app/pdf')),
                'mode' => 'utf-8',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
                'format' => 'A4',
            ]);
            $mpdf->useSubstitutions = true;
            $mpdf->SetTitle('Packing-Slip-' . $transaction->invoice_no . '.pdf');
            $mpdf->WriteHTML($body);
            $pdfContent = $mpdf->Output('', 'S');
            $filename = 'Packing-Slip-' . $transaction->invoice_no . '.pdf';

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Throwable $e) {
            Log::error('Packing slip PDF failed: ' . $e->getMessage(), ['order_id' => $orderId, 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => false, 'message' => 'Failed to generate packing slip PDF', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get order tracking status
     * Returns tracking statuses (packed, shipped) with dates
     */
    public function getOrderTracking($orderId)
    {
        try {
            $contact = Auth::guard('api')->user();
            
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }
            
            $business_id = $contact->business_id;

            // Verify order belongs to the customer or same business (check both sales_order and sell types)
            // Allow contacts from the same business to view each other's orders for tracking
            $order = Transaction::where('id', $orderId)
                ->where('business_id', $business_id)
                ->whereIn('type', ['sales_order', 'sell'])
                ->first();

            if (!$order) {
                // Debug: Check if order exists but doesn't match criteria
                $orderExists = Transaction::where('id', $orderId)->first();
                if ($orderExists) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Order not found or does not belong to you',
                        'debug' => [
                            'order_contact_id' => $orderExists->contact_id,
                            'auth_contact_id' => $contact->id,
                            'order_business_id' => $orderExists->business_id,
                            'auth_business_id' => $business_id,
                            'order_type' => $orderExists->type,
                        ]
                    ], 404);
                }
                
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Build response based on order's current state - automatically from picking_status and shipping_status
            // This ensures the response always reflects the actual order state
            $statusMap = [
                'intransit' => null,
                'packed' => null,
                'shipped' => null,
            ];

            // First, always check for related invoice for sales orders (regardless of order status)
            // This is important because orders can be voided/ordered but invoices may have been created
            $relatedInvoice = null;
            if ($order->type === 'sales_order') {
                $relatedInvoice = Transaction::where('type', 'sell')
                    ->where('business_id', $order->business_id)
                    ->where('contact_id', $order->contact_id)
                    ->where(function($query) use ($orderId) {
                        // Check if sales_order_ids JSON array contains this order ID
                        $query->whereRaw('JSON_CONTAINS(sales_order_ids, ?)', [json_encode((string)$orderId)]);
                    })
                    ->first();
            }

            // Check if order status is "ordered" - show as intransit
            // If the status = ordered then the order is in transit
            if ($order->status === 'ordered') {
                // Check tracking records for both order and related invoice
                $intransitTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                    ->where('status', 'intransit')
                    ->first();
                
                // Also check related invoice for intransit tracking
                $invoiceIntransitTracking = null;
                if ($relatedInvoice) {
                    $invoiceIntransitTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                        ->where('status', 'intransit')
                        ->first();
                }
                
                // Priority: Use order tracking record first, then invoice tracking record, then infer from order creation
                if ($intransitTracking) {
                    // Use order tracking record
                    $intransitDate = $order->created_at;
                    $trackingDate = $intransitTracking->status_date ?? $intransitTracking->created_at ?? $intransitTracking->updated_at;
                    $finalIntransitDate = $trackingDate ?? $intransitDate;
                    
                    $statusMap['intransit'] = [
                        'status' => 'intransit',
                        'date' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d') : null,
                        'datetime' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d H:i:s') : null,
                        'notes' => $intransitTracking->notes,
                        'message' => 'your order is in transit',
                    ];
                } elseif ($invoiceIntransitTracking) {
                    // Use invoice tracking record
                    $intransitDate = $order->created_at;
                    $trackingDate = $invoiceIntransitTracking->status_date ?? $invoiceIntransitTracking->created_at ?? $invoiceIntransitTracking->updated_at;
                    $finalIntransitDate = $trackingDate ?? $intransitDate;
                    
                    $statusMap['intransit'] = [
                        'status' => 'intransit',
                        'date' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d') : null,
                        'datetime' => $finalIntransitDate ? $finalIntransitDate->format('Y-m-d H:i:s') : null,
                        'notes' => $invoiceIntransitTracking->notes,
                        'message' => 'your order is in transit',
                    ];
                } else {
                    // If no tracking record but order status is "ordered", use order creation date
                    $intransitDate = $order->created_at;
                    $statusMap['intransit'] = [
                        'status' => 'intransit',
                        'date' => $intransitDate ? $intransitDate->format('Y-m-d') : null,
                        'datetime' => $intransitDate ? $intransitDate->format('Y-m-d H:i:s') : null,
                        'notes' => null,
                        'message' => 'your order is in transit',
                    ];
                }
            }

            // Automatically determine packed status from order's picking_status
            // Order flow: PICKING -> PICKED -> PACKED -> INVOICED (completed)
            // If order is PACKED (but not completed), show packed status
            // If order is INVOICED/COMPLETED, it means it was invoiced - check for shipment
            // Also check if there's a tracking record with 'packed' status (handles edge cases)
            
            // Check tracking records for both order and related invoice
            $packedTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                ->where('status', 'packed')
                ->first();
            
            // Also check related invoice for packed tracking
            $invoicePackedTracking = null;
            if ($relatedInvoice) {
                $invoicePackedTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                    ->where('status', 'packed')
                    ->first();
            }
            
            $hasPackedTracking = $packedTracking !== null || $invoicePackedTracking !== null;
            
            // Check if order has been packed (either by picking_status or tracking record)
            // Handle case-insensitive comparison and null values
            // Note: PICKED status means order has been picked/packed
            // PACKED and INVOICED also mean order has been packed
            $pickingStatus = $order->picking_status ? strtoupper(trim($order->picking_status)) : null;
            $isPackedByStatus = in_array($pickingStatus, ['PICKED', 'PACKED', 'INVOICED'], true);
            
            // Also check related invoice picking status
            $invoicePickingStatus = null;
            $isInvoicePackedByStatus = false;
            if ($relatedInvoice && $relatedInvoice->picking_status) {
                $invoicePickingStatus = strtoupper(trim($relatedInvoice->picking_status));
                $isInvoicePackedByStatus = in_array($invoicePickingStatus, ['PICKED', 'PACKED', 'INVOICED'], true);
            }
            
            $isPacked = $isPackedByStatus || $isInvoicePackedByStatus || $hasPackedTracking;
            
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Order Tracking Debug', [
                'order_id' => $orderId,
                'picking_status' => $order->picking_status,
                'picking_status_upper' => $pickingStatus,
                'status' => $order->status,
                'isPackedByStatus' => $isPackedByStatus,
                'hasPackedTracking' => $hasPackedTracking,
                'isPacked' => $isPacked,
                'order_status' => $order->status,
                'will_show_packed' => $isPacked,
            ]);
            
            // Show packed status if:
            // 1. There's a tracking record with 'packed' status (order or invoice), OR
            // 2. Order picking_status indicates it was packed (PICKED, PACKED, INVOICED), OR
            // 3. Related invoice picking_status indicates it was packed
            // Note: Show packed status even if order is completed, as packing happens before completion
            if ($isPacked) {
                // Priority: Use order tracking record first, then invoice tracking record, then infer from status
                if ($packedTracking) {
                    // Use order tracking record
                    $packedDate = $order->updated_at ?? $order->created_at;
                    $trackingDate = $packedTracking->status_date ?? $packedTracking->created_at ?? $packedTracking->updated_at;
                    $finalPackedDate = $trackingDate ?? $packedDate;
                    
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $finalPackedDate ? $finalPackedDate->format('Y-m-d') : null,
                        'datetime' => $finalPackedDate ? $finalPackedDate->format('Y-m-d H:i:s') : null,
                        'notes' => $packedTracking->notes,
                    ];
                } elseif ($invoicePackedTracking) {
                    // Use invoice tracking record
                    $packedDate = $relatedInvoice->updated_at ?? $relatedInvoice->created_at;
                    $trackingDate = $invoicePackedTracking->status_date ?? $invoicePackedTracking->created_at ?? $invoicePackedTracking->updated_at;
                    $finalPackedDate = $trackingDate ?? $packedDate;
                    
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $finalPackedDate ? $finalPackedDate->format('Y-m-d') : null,
                        'datetime' => $finalPackedDate ? $finalPackedDate->format('Y-m-d H:i:s') : null,
                        'notes' => $invoicePackedTracking->notes,
                    ];
                } elseif ($isPackedByStatus) {
                    // Use order dates
                    $packedDate = $order->updated_at ?? $order->created_at;
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $packedDate ? $packedDate->format('Y-m-d') : null,
                        'datetime' => $packedDate ? $packedDate->format('Y-m-d H:i:s') : null,
                        'notes' => null,
                        'message' => 'your order is packed',
                    ];
                } elseif ($isInvoicePackedByStatus && $relatedInvoice) {
                    // Use invoice dates
                    $packedDate = $relatedInvoice->updated_at ?? $relatedInvoice->created_at;
                    $statusMap['packed'] = [
                        'status' => 'packed',
                        'date' => $packedDate ? $packedDate->format('Y-m-d') : null,
                        'datetime' => $packedDate ? $packedDate->format('Y-m-d H:i:s') : null,
                        'notes' => null,
                        'message' => 'your order is packed',
                    ];
                }
            }

            // Automatically determine shipped status from order's shipping_status
            // Show shipped if:
            // 1. Order status is 'completed' (invoiced) AND has shipment, OR
            // 2. Order has shipping_status = 'shipped', OR  
            // 3. For sales orders, related invoice has shipping_status = 'shipped', OR
            // 4. Tracking record exists with 'shipped' status
            
            // Check tracking records for both order and related invoice
            $shippedTracking = OrderTrackingStatus::where('transaction_id', $orderId)
                ->where('status', 'shipped')
                ->first();
            
            // Also check related invoice for shipped tracking
            $invoiceShippedTracking = null;
            if ($relatedInvoice) {
                $invoiceShippedTracking = OrderTrackingStatus::where('transaction_id', $relatedInvoice->id)
                    ->where('status', 'shipped')
                    ->first();
            }
            
            $hasShippedTracking = $shippedTracking !== null || $invoiceShippedTracking !== null;
            
            $isShipped = false;
            
            // Check if order is shipped - multiple ways to determine:
            // 1. Order has shipping_status = 'shipped' (most direct check)
            // 2. Order is completed/invoiced (completed orders are typically shipped)
            // 3. For sales orders, check related invoice has shipping_status = 'shipped'
            // 4. Tracking record exists with 'shipped' status (checked separately below)
            
            // First, check direct shipping_status on the order
            if ($order->shipping_status === 'shipped') {
                $isShipped = true;
            } elseif ($order->status === 'completed') {
                // Completed orders are typically shipped (invoice completion = shipment)
                // For sales orders, verify by checking related invoice
                if ($order->type === 'sales_order' && $relatedInvoice) {
                    // If related invoice has shipping_status = 'shipped', confirm shipped
                    if ($relatedInvoice->shipping_status === 'shipped' || !empty($relatedInvoice->shipment)) {
                        $isShipped = true;
                    } else {
                        // Completed invoice without explicit shipping_status - still consider shipped
                        $isShipped = true;
                    }
                } else {
                    // Direct sell order that's completed - consider it shipped
                    $isShipped = true;
                }
            } elseif ($relatedInvoice && ($relatedInvoice->shipping_status === 'shipped' || !empty($relatedInvoice->shipment))) {
                // For sales orders, check if related invoice has shipping_status = 'shipped'
                $isShipped = true;
            }
            
            // If tracking record exists, mark as shipped (handles cases where order was shipped)
            if ($hasShippedTracking) {
                $isShipped = true;
            }
            
            // Show shipped status if order is shipped
            // Priority: Use order tracking record first, then invoice tracking record, then infer from status
            if ($isShipped) {
                // Use updated_at for when order was shipped (status changed), fallback to created_at
                // For sales orders, prefer related invoice's dates
                if ($order->type === 'sales_order' && isset($relatedInvoice)) {
                    $shippedDate = $relatedInvoice->updated_at ?? $relatedInvoice->created_at;
                } else {
                    $shippedDate = $order->updated_at ?? $order->created_at;
                }
                
                if ($shippedTracking) {
                    // Use order tracking record
                    $trackingDate = $shippedTracking->status_date ?? $shippedTracking->created_at ?? $shippedTracking->updated_at;
                    $finalShippedDate = $trackingDate ?? $shippedDate;
                    
                    $statusMap['shipped'] = [
                        'status' => 'shipped',
                        'date' => $finalShippedDate ? $finalShippedDate->format('Y-m-d') : null,
                        'datetime' => $finalShippedDate ? $finalShippedDate->format('Y-m-d H:i:s') : null,
                        'notes' => $shippedTracking->notes,
                        'message' => 'your order is shipped',
                    ];
                } elseif ($invoiceShippedTracking) {
                    // Use invoice tracking record
                    $trackingDate = $invoiceShippedTracking->status_date ?? $invoiceShippedTracking->created_at ?? $invoiceShippedTracking->updated_at;
                    $finalShippedDate = $trackingDate ?? $shippedDate;
                    
                    $statusMap['shipped'] = [
                        'status' => 'shipped',
                        'date' => $finalShippedDate ? $finalShippedDate->format('Y-m-d') : null,
                        'datetime' => $finalShippedDate ? $finalShippedDate->format('Y-m-d H:i:s') : null,
                        'notes' => $invoiceShippedTracking->notes,
                        'message' => 'your order is shipped',
                    ];
                } else {
                    // If no tracking record but order is shipped, use order/invoice dates
                    $statusMap['shipped'] = [
                        'status' => 'shipped',
                        'date' => $shippedDate ? $shippedDate->format('Y-m-d') : null,
                        'datetime' => $shippedDate ? $shippedDate->format('Y-m-d H:i:s') : null,
                        'notes' => null,
                        'message' => 'your order is shipped',
                    ];
                }
            }

            // For sales orders, check if there's a related invoice (type='sell') and use its invoice_no
            $invoiceNo = $order->invoice_no;
            // Use the relatedInvoice we already found above (if it exists)
            if (isset($relatedInvoice) && !empty($relatedInvoice->invoice_no)) {
                $invoiceNo = $relatedInvoice->invoice_no;
            }
            
            // Handle picking_status: if null, infer from order status
            // If order status is 'ordered' and picking_status is null, it means order is waiting to be picked
            $pickingStatusDisplay = $order->picking_status;
            if ($pickingStatusDisplay === null && $order->status === 'ordered') {
                $pickingStatusDisplay = 'ordered'; // Show as 'ordered' when order hasn't entered picking workflow yet
            }
            
            return response()->json([
                'status' => true,
                'data' => [
                    'order_id' => $orderId,
                    'invoice_no' => $invoiceNo,
                    'order_status' => $order->status,
                    'picking_status' => $pickingStatusDisplay,
                    'intransit_status' => $statusMap['intransit'],
                    'packed_status' => $statusMap['packed'],
                    'shipped_status' => $statusMap['shipped'], 
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching tracking status',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update order tracking status
     * This endpoint is typically used by admin/backend to update order status
     */
    public function updateOrderTracking(Request $request, $orderId)
    {
        try {
            $request->validate([
                'status' => 'required|in:packed,shipped',
                'status_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Get current user (admin/staff)
            $user = Auth::guard('api')->user();
            if (!$user) {
                // Try staff auth
                $user = Auth::guard('api')->user();
            }

            // Verify order exists
            $order = Transaction::find($orderId);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $status = $request->input('status');
            $statusDate = $request->input('status_date') ? date('Y-m-d H:i:s', strtotime($request->input('status_date'))) : now();

            // Update or create tracking status
            $trackingStatus = OrderTrackingStatus::updateOrCreate(
                [
                    'transaction_id' => $orderId,
                    'status' => $status,
                ],
                [
                    'status_date' => $statusDate,
                    'notes' => $request->input('notes'),
                    'updated_by' => $user ? $user->id : null,
                ]
            );

            // Also update the shipping_status on transaction if shipped
            if ($status === 'shipped') {
                $order->shipping_status = 'shipped';
                $order->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Order tracking status updated successfully',
                'data' => [
                    'order_id' => $orderId,
                    'status' => $trackingStatus->status,
                    'date' => $trackingStatus->status_date ? $trackingStatus->status_date->format('Y-m-d') : null,
                    'datetime' => $trackingStatus->status_date ? $trackingStatus->status_date->format('Y-m-d H:i:s') : null,
                    'notes' => $trackingStatus->notes,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating tracking status',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Mark order items as received
     * 
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReceived(Request $request, $orderId)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Validate request
            $request->validate([
                'items' => 'required|array',
                'items.*.sell_line_id' => 'required|integer|exists:transaction_sell_lines,id',
                'items.*.received_quantity' => 'required|numeric|min:0',
            ]);

            // Verify order belongs to the customer
            $order = Transaction::where('id', $orderId)
                ->where('contact_id', $contact->id)
                ->where('business_id', $contact->business_id)
                ->whereIn('type', ['sales_order', 'sell'])
                ->first();

            if (!$order) {
                // Check if order exists but belongs to different customer
                $orderExists = Transaction::where('id', $orderId)->first();
                if ($orderExists) {
                    Log::info('Order access denied', [
                        'order_id' => $orderId,
                        'authenticated_contact_id' => $contact->id,
                        'order_contact_id' => $orderExists->contact_id,
                        'authenticated_business_id' => $contact->business_id,
                        'order_business_id' => $orderExists->business_id
                    ]);
                    return response()->json([
                        'status' => false,
                        'message' => 'Order not found or does not belong to you. Please use an order ID from GET /api/customer/my-orders',
                        'hint' => 'Call GET /api/customer/my-orders first to get your valid order IDs'
                    ], 404);
                }
                
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found. Please use an order ID from GET /api/customer/my-orders',
                    'hint' => 'Call GET /api/customer/my-orders first to get your valid order IDs'
                ], 404);
            }

            // Verify all sell lines belong to this order
            $sellLineIds = collect($request->items)->pluck('sell_line_id')->toArray();
            $sellLines = \App\TransactionSellLine::where('transaction_id', $orderId)
                ->whereIn('id', $sellLineIds)
                ->get();

            if ($sellLines->count() !== count($sellLineIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some items do not belong to this order'
                ], 422);
            }

            DB::beginTransaction();

            $updatedItems = [];
            foreach ($request->items as $item) {
                $sellLine = $sellLines->firstWhere('id', $item['sell_line_id']);
                
                if (!$sellLine) {
                    continue;
                }

                $receivedQty = (float) $item['received_quantity'];
                $maxQty = (float) $sellLine->quantity;

                // Ensure received quantity doesn't exceed ordered quantity
                if ($receivedQty > $maxQty) {
                    $receivedQty = $maxQty;
                }

                // Update received quantity
                // Note: If received_quantity column doesn't exist, you may need to add it via migration
                // For now, we'll use a JSON field or add it to the sell line
                $sellLine->received_quantity = $receivedQty;
                $sellLine->save();

                $updatedItems[] = [
                    'sell_line_id' => $sellLine->id,
                    'product_id' => $sellLine->product_id,
                    'product_name' => $sellLine->product ? $sellLine->product->name : null,
                    'ordered_quantity' => (float) $sellLine->quantity,
                    'received_quantity' => $receivedQty,
                ];
            }

            // Check if all items are fully received
            $allReceived = $sellLines->every(function ($line) {
                return isset($line->received_quantity) && 
                       (float) $line->received_quantity >= (float) $line->quantity;
            });

            // Optionally update order status if all items received
            // Uncomment if you want to auto-update order status
            // if ($allReceived && $order->status !== 'completed') {
            //     $order->status = 'completed';
            //     $order->save();
            // }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Items marked as received successfully',
                'data' => [
                    'order_id' => $orderId,
                    'items' => $updatedItems,
                    'all_received' => $allReceived,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error marking items as received',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get buy again information for all eligible orders - No order ID required
     * Returns all completed/delivered orders that can be bought again
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBuyAgain(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Get optional product_id from query parameter
            $requestedProductId = $request->query('product_id');
            $filterProductId = null;
            if ($requestedProductId !== null && $requestedProductId !== '') {
                $filterProductId = (int) $requestedProductId;
            }

            // Get all orders for this customer - only completed/delivered (buy again is based on order history only)
            // Deleting a buy-again product from cart does NOT remove the order from buy again
            $orders = Transaction::with([
                'sell_lines' => function($query) {
                    $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'unit_price');
                },
                'sell_lines.product' => function($query) {
                    $query->select('id', 'name', 'enable_selling', 'is_inactive', 'image');
                },
                'sell_lines.variations' => function($query) {
                    $query->select('id', 'product_id', 'name', 'sub_sku');
                },
                'sell_lines.variations.media' => function($query) {
                    $query->select('id', 'file_name', 'model_id');
                },
            ])
            ->where('contact_id', $contact->id)
            ->where('business_id', $contact->business_id)
            ->whereIn('type', ['sales_order', 'sell'])
            ->whereIn('status', ['completed', 'delivered'])
            ->orderBy('created_at', 'desc')
            ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'orders' => [],
                        'total_orders' => 0,
                    ]
                ]);
            }

            // Index current cart items so we can mark which "buy again" items are already selected in cart
            $cartItems = CartItem::where('user_id', $contact->id)->get();
            $cartIndex = $cartItems->groupBy(function ($item) {
                return $item->product_id . ':' . $item->variation_id;
            });

            $ordersData = [];

            foreach ($orders as $order) {
                if (!$order->sell_lines || $order->sell_lines->isEmpty()) {
                    continue;
                }

                $sellLinesToProcess = collect($order->sell_lines);
                
                // Filter by product_id if provided
                if ($filterProductId !== null) {
                    $sellLinesToProcess = $sellLinesToProcess->filter(function($sellLine) use ($filterProductId) {
                        return (int) $sellLine->product_id === $filterProductId;
                    })->values();
                    
                    // Skip this order if no items match the product filter
                    if ($sellLinesToProcess->isEmpty()) {
                        continue;
                    }
                }

                $availableItems = [];
                $unavailableItems = [];

                foreach ($sellLinesToProcess as $sellLine) {
                    $product = $sellLine->product;
                    $variation = $sellLine->variations;
                    
                    // Build product image URL (variation media > product image > default)
                    $productImage = null;
                    if ($variation && $variation->media && $variation->media->isNotEmpty()) {
                        $media = $variation->media->first();
                        $productImage = $media->display_url ?? asset('/uploads/media/' . rawurlencode($media->file_name));
                    } elseif ($product && $product->image) {
                        $productImage = $product->image_url ?? asset('/uploads/img/' . rawurlencode($product->image));
                    } else {
                        $productImage = asset('/img/default.png');
                    }

                    // Cart selection info
                    $cartKey = $sellLine->product_id . ':' . $sellLine->variation_id;
                    $isInCart = isset($cartIndex[$cartKey]);
                    $cartQty = $isInCart ? $cartIndex[$cartKey]->sum('qty') : 0;

                    $itemData = [
                        'sell_line_id' => $sellLine->id,
                        'product_id' => $sellLine->product_id,
                        'product_name' => $product ? $product->name : 'Unknown',
                        'product_image' => $productImage,
                        'variation_id' => $sellLine->variation_id,
                        'variation_name' => $variation ? $variation->name : null,
                        'quantity' => (float) $sellLine->quantity,
                        'unit_price' => (float) ($sellLine->unit_price ?? 0),
                        'is_in_cart' => $isInCart,
                        'cart_quantity' => (float) $cartQty,
                    ];

                    // Check if product is available and active
                    if (!$product || $product->enable_selling != 1 || $product->is_inactive == 1) {
                        $itemData['reason'] = 'Product is not available for purchase';
                        $unavailableItems[] = $itemData;
                    } elseif (!$variation) {
                        $itemData['reason'] = 'Variation not found';
                        $unavailableItems[] = $itemData;
                    } else {
                        $availableItems[] = $itemData;
                    }
                }

                // Only include orders that have at least some items
                if (count($availableItems) > 0 || count($unavailableItems) > 0) {
                    // Handle transaction_date - could be Carbon instance or string
                    $orderDate = null;
                    if ($order->transaction_date) {
                        if (is_object($order->transaction_date) && method_exists($order->transaction_date, 'format')) {
                            // If it's a Carbon/DateTime instance
                            $orderDate = $order->transaction_date->format('Y-m-d');
                        } else {
                            // If it's a string, extract date part
                            $orderDate = is_string($order->transaction_date) 
                                ? substr($order->transaction_date, 0, 10) 
                                : $order->transaction_date;
                        }
                    }
                    
                    $ordersData[] = [
                        'order_id' => (string) $order->id,
                        'invoice_no' => $order->invoice_no,
                        'order_date' => $orderDate,
                        'status' => $order->status,
                        'available_items' => $availableItems,
                        'unavailable_items' => $unavailableItems,
                        'total_available' => count($availableItems),
                        'total_unavailable' => count($unavailableItems),
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'orders' => $ordersData,
                    'total_orders' => count($ordersData),
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching buy again information',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get buy again information - Preview items that can be bought again from an order
     * 
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBuyAgain(Request $request, $orderId)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Verify order belongs to the customer
            $order = Transaction::with(['sell_lines' => function($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'unit_price');
            }, 'sell_lines.product' => function($query) {
                $query->select('id', 'name', 'enable_selling', 'is_inactive', 'image');
            }, 'sell_lines.variations' => function($query) {
                $query->select('id', 'product_id', 'name', 'sub_sku');
            }])
            ->where('id', $orderId)
            ->where('contact_id', $contact->id)
            ->where('business_id', $contact->business_id)
            ->whereIn('type', ['sales_order', 'sell'])
            ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found or does not belong to you'
                ], 404);
            }

            if (!$order->sell_lines || $order->sell_lines->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No items found in this order'
                ], 404);
            }

            // Get optional product_id from query parameter
            $requestedProductId = $request->query('product_id');
            $filterProductId = null;
            
            // Filter sell lines if product_id is provided
            $sellLinesToProcess = collect($order->sell_lines);
            
            if ($requestedProductId !== null && $requestedProductId !== '') {
                $filterProductId = (int) $requestedProductId;
                $sellLinesToProcess = $sellLinesToProcess->filter(function($sellLine) use ($filterProductId) {
                    return (int) $sellLine->product_id === $filterProductId;
                })->values();
                
                if ($sellLinesToProcess->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Product not found in this order'
                    ], 404);
                }
            }

            $availableItems = [];
            $unavailableItems = [];

            foreach ($sellLinesToProcess as $sellLine) {
                $product = $sellLine->product;
                $variation = $sellLine->variations;
                
                $itemData = [
                    'sell_line_id' => $sellLine->id,
                    'product_id' => $sellLine->product_id,
                    'product_name' => $product ? $product->name : 'Unknown',
                    'variation_id' => $sellLine->variation_id,
                    'variation_name' => $variation ? $variation->name : null,
                    'quantity' => (float) $sellLine->quantity,
                    'unit_price' => (float) ($sellLine->unit_price ?? 0),
                ];

                // Check if product is available and active
                if (!$product || $product->enable_selling != 1 || $product->is_inactive == 1) {
                    $itemData['reason'] = 'Product is not available for purchase';
                    $unavailableItems[] = $itemData;
                } elseif (!$variation) {
                    $itemData['reason'] = 'Variation not found';
                    $unavailableItems[] = $itemData;
                } else {
                    $availableItems[] = $itemData;
                }
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'order_id' => (string) $orderId,
                    'available_items' => $availableItems,
                    'unavailable_items' => $unavailableItems,
                    'total_available' => count($availableItems),
                    'total_unavailable' => count($unavailableItems),
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching buy again information',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Buy again - Add items from a previous order to cart (order_id in body)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyAgainFromBody(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Get order_id from request body
            $orderId = $request->input('order_id');
            if (!$orderId) {
                return response()->json([
                    'status' => false,
                    'message' => 'order_id is required in request body'
                ], 422);
            }

            // Get optional product_id from request body
            $requestedProductId = $request->input('product_id');
            $filterProductId = null;

            // Verify order belongs to the customer
            $order = Transaction::with(['sell_lines' => function($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'unit_price');
            }, 'sell_lines.product' => function($query) {
                $query->select('id', 'name', 'enable_selling', 'is_inactive');
            }])
            ->where('id', $orderId)
            ->where('contact_id', $contact->id)
            ->where('business_id', $contact->business_id)
            ->whereIn('type', ['sales_order', 'sell'])
            ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found or does not belong to you'
                ], 404);
            }

            if (!$order->sell_lines || $order->sell_lines->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No items found in this order'
                ], 404);
            }

            // Filter sell lines if product_id is provided - only process that specific product
            $sellLinesToProcess = collect($order->sell_lines);
            
            if ($requestedProductId !== null && $requestedProductId !== '') {
                // Convert to integer for strict comparison
                $filterProductId = (int) $requestedProductId;
                $sellLinesToProcess = $sellLinesToProcess->filter(function($sellLine) use ($filterProductId) {
                    return (int) $sellLine->product_id === $filterProductId;
                })->values(); // Reset keys after filtering
                
                if ($sellLinesToProcess->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Product not found in this order'
                    ], 404);
                }
            }

            $userId = $contact->id;
            $priceTier = $contact->price_tier;
            $priceGroupId = $priceTier ? key($priceTier) : null;
            $currentTime = now();
            $addedItems = [];
            $skippedItems = [];
            $errors = [];

            DB::beginTransaction();

            try {
                // Get or create cart
                $cart = Cart::where('user_id', $userId)->first();
                if (!$cart) {
                    $cart = Cart::create([
                        'user_id' => $userId,
                        'isFreeze' => false,
                    ]);
                }

                foreach ($sellLinesToProcess as $sellLine) {
                    // Double-check: if product_id was requested, only process that product
                    if ($filterProductId !== null && (int) $sellLine->product_id !== $filterProductId) {
                        continue;
                    }
                    
                    // Check if product is still available and active
                    $product = $sellLine->product;
                    if (!$product || $product->enable_selling != 1 || $product->is_inactive == 1) {
                        $skippedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'product_name' => $product ? $product->name : 'Unknown',
                            'reason' => 'Product is not available for purchase'
                        ];
                        continue;
                    }

                    // Check if variation exists
                    $variation = \App\Variation::where('id', $sellLine->variation_id)->first();
                    if (!$variation) {
                        $skippedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'reason' => 'Variation not found'
                        ];
                        continue;
                    }

                    // Check if item already exists in cart
                    $existingCartItem = CartItem::where([
                        'user_id' => $userId,
                        'product_id' => $sellLine->product_id,
                        'variation_id' => $sellLine->variation_id,
                    ])->first();

                    $quantity = (float) $sellLine->quantity;
                    
                    if ($existingCartItem) {
                        // Add to existing quantity if item already in cart
                        $existingCartItem->qty += $quantity;
                        $existingCartItem->save();
                        $addedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'quantity' => $quantity,
                            'action' => 'updated'
                        ];
                    } else {
                        // Create new cart item
                        CartItem::create([
                            'user_id' => $userId,
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'qty' => $quantity,
                            'price' => $sellLine->unit_price ?? 0,
                        ]);
                        $addedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'quantity' => $quantity,
                            'action' => 'added'
                        ];
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => count($addedItems) > 0 
                        ? 'Items added to cart successfully' 
                        : 'No items could be added to cart',
                    'data' => [
                        'order_id' => $orderId,
                        'added_items' => $addedItems,
                        'skipped_items' => $skippedItems,
                        'total_added' => count($addedItems),
                        'total_skipped' => count($skippedItems),
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error adding items to cart',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Buy again - Add all items from a previous order to cart
     * 
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyAgain(Request $request, $orderId)
    {
        try {
            $contact = Auth::guard('api')->user();
            if (!$contact) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Verify order belongs to the customer
            $order = Transaction::with(['sell_lines' => function($query) {
                $query->select('id', 'transaction_id', 'product_id', 'variation_id', 'quantity', 'unit_price');
            }, 'sell_lines.product' => function($query) {
                $query->select('id', 'name', 'enable_selling', 'is_inactive');
            }])
            ->where('id', $orderId)
            ->where('contact_id', $contact->id)
            ->where('business_id', $contact->business_id)
            ->whereIn('type', ['sales_order', 'sell'])
            ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found or does not belong to you'
                ], 404);
            }

            if (!$order->sell_lines || $order->sell_lines->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No items found in this order'
                ], 404);
            }

            // Get optional product_id from request body
            $requestedProductId = $request->input('product_id');
            $filterProductId = null;
            
            // Filter sell lines if product_id is provided - only process that specific product
            $sellLinesToProcess = collect($order->sell_lines);
            
            if ($requestedProductId !== null && $requestedProductId !== '') {
                // Convert to integer for strict comparison
                $filterProductId = (int) $requestedProductId;
                
                // Filter to only include items with the requested product_id
                $sellLinesToProcess = $sellLinesToProcess->filter(function($sellLine) use ($filterProductId) {
                    return (int) $sellLine->product_id === $filterProductId;
                })->values(); // Reset keys after filtering
                
                if ($sellLinesToProcess->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Product not found in this order'
                    ], 404);
                }
            }

            $userId = $contact->id;
            $priceTier = $contact->price_tier;
            $priceGroupId = $priceTier ? key($priceTier) : null;
            $currentTime = now();
            $addedItems = [];
            $skippedItems = [];
            $errors = [];

            DB::beginTransaction();

            try {
                // Get or create cart
                $cart = Cart::where('user_id', $userId)->first();
                if (!$cart) {
                    $cart = Cart::create([
                        'user_id' => $userId,
                        'isFreeze' => false,
                    ]);
                }

                foreach ($sellLinesToProcess as $sellLine) {
                    // Double-check: if product_id was requested, only process that product
                    if ($filterProductId !== null && (int) $sellLine->product_id !== $filterProductId) {
                        continue;
                    }
                    
                    // Check if product is still available and active
                    $product = $sellLine->product;
                    if (!$product || $product->enable_selling != 1 || $product->is_inactive == 1) {
                        $skippedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'product_name' => $product ? $product->name : 'Unknown',
                            'reason' => 'Product is not available for purchase'
                        ];
                        continue;
                    }

                    // Check if variation exists
                    $variation = \App\Variation::where('id', $sellLine->variation_id)->first();
                    if (!$variation) {
                        $skippedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'reason' => 'Variation not found'
                        ];
                        continue;
                    }

                    // Check if item already exists in cart
                    $existingCartItem = CartItem::where([
                        'user_id' => $userId,
                        'product_id' => $sellLine->product_id,
                        'variation_id' => $sellLine->variation_id,
                    ])->first();

                    $quantity = (float) $sellLine->quantity;
                    
                    if ($existingCartItem) {
                        // Add to existing quantity if item already in cart
                        $existingCartItem->qty += $quantity;
                        $existingCartItem->save();
                        $addedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'quantity' => $quantity,
                            'action' => 'updated'
                        ];
                    } else {
                        // Create new cart item
                        CartItem::create([
                            'user_id' => $userId,
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'qty' => $quantity,
                            'price' => $sellLine->unit_price ?? 0,
                        ]);
                        $addedItems[] = [
                            'product_id' => $sellLine->product_id,
                            'variation_id' => $sellLine->variation_id,
                            'quantity' => $quantity,
                            'action' => 'added'
                        ];
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => count($addedItems) > 0 
                        ? 'Items added to cart successfully' 
                        : 'No items could be added to cart',
                    'data' => [
                        'order_id' => $orderId,
                        'added_items' => $addedItems,
                        'skipped_items' => $skippedItems,
                        'total_added' => count($addedItems),
                        'total_skipped' => count($skippedItems),
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error adding items to cart',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get download logs for orders
     * Returns all download history for the authenticated user
     */
    public function getDownloadLogs(Request $request)
    {
        try {
            $contact = Auth::guard('api')->user();
            $business_id = $contact->business_id;
            
            // Get query parameters
            $downloadType = $request->query('download_type'); // 'pdf', 'csv', 'excel', or null for all
            $page = $request->query('page', 1);
            $perPage = $request->query('per_page', 15);
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');
            
            // Build query
            $query = OrderDownloadLog::with(['contact:id,name,email'])
                ->where('contact_id', $contact->id)
                ->where('business_id', $business_id)
                ->orderBy('created_at', 'desc');
            
            // Filter by download type
            if ($downloadType && in_array(strtolower($downloadType), ['pdf', 'csv', 'excel'])) {
                $query->where('download_type', strtolower($downloadType));
            }
            
            // Filter by date range
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            
            // Paginate results
            $logs = $query->paginate($perPage);
            
            // Format response
            $formattedLogs = $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'download_type' => $log->download_type,
                    'filename' => $log->filename,
                    'total_orders' => $log->total_orders,
                    'order_numbers' => $log->order_numbers,
                    'order_ids' => $log->order_ids,
                    'filters' => $log->filters,
                    'date_range' => $log->date_range,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'downloaded_at' => $log->created_at->toDateTimeString(),
                    'user' => [
                        'id' => $log->contact->id ?? null,
                        'name' => $log->contact->name ?? 'N/A',
                        'email' => $log->contact->email ?? 'N/A',
                    ],
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Download logs retrieved successfully',
                'data' => [
                    'download_logs' => $formattedLogs,
                    'pagination' => [
                        'current_page' => $logs->currentPage(),
                        'per_page' => $logs->perPage(),
                        'total' => $logs->total(),
                        'last_page' => $logs->lastPage(),
                        'from' => $logs->firstItem(),
                        'to' => $logs->lastItem(),
                    ],
                ],
                'filters_applied' => [
                    'download_type' => $downloadType,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching download logs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching download logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Business;
use App\Contact;
use App\LocationTaxCharge;
use App\Models\ModalAccess;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use App\BusinessLocation;
use App\Jobs\SendNotificationJob;

use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\Utils\NotificationUtil;
use App\Variation;
use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Notification;
use Tymon\JWTAuth\Facades\JWTAuth;
use Yajra\DataTables\Facades\DataTables;
use App\Services\FireBaseServices;
use App\PickersActivity;
use App\VerifierActivity;

class OrderfulfillmentController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $contactUtil;

    protected $businessUtil;

    protected $transactionUtil;

    protected $productUtil;
    protected $moduleUtil;
    protected $dummyPaymentLine;
    protected $shipping_status_colors;
    protected $picking_status_colors;
    protected $notificationUtil;




    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, NotificationUtil $notificationUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->notificationUtil = $notificationUtil;
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
            'ordered' => 'bg-info',
            'packed' => 'bg-navy',
            'shipped' => 'bg-yellow',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
            'completed' => 'bg-green'
        ];
        $this->picking_status_colors = [
            'PICKING' => 'bg-info',
            'PICKED' => 'bg-green',
            'PACKED' => 'bg-red',
            'INVOICED' => 'bg-navy',
        ];
    }
    
    /**
     * Generate order hierarchy HTML for DataTables
     * Shows parent-child relationship and vendor info for dropship orders
     *
     * @param Transaction $row
     * @return string HTML
     */
    protected function getOrderHierarchyHtml($row)
    {
        $html = '';
        $url = action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
        
        // Base invoice link
        $invoiceLink = '<a href="#" data-href="' . $url . '" class="invoice-link">' . $row->invoice_no . '</a>';
        
        // Check order type and hierarchy
        $orderType = $row->type ?? 'sales_order';
        $hasChildren = !empty($row->sales_order_ids);
        $hasParent = !empty($row->transfer_parent_id);
        
        // Type badge colors
        $typeBadges = [
            'sales_order' => '<span class="label bg-blue" title="Sales Order">SO</span>',
            'erp_sales_order' => '<span class="label bg-gray" title="In-House Order">ERP</span>',
            'wp_sales_order' => '<span class="label bg-purple" title="WooCommerce Dropship">WC-DS</span>',
            'erp_dropship_order' => '<span class="label bg-info" title="ERP Portal Dropship">ERP-DS</span>',
        ];
        
        $typeBadge = $typeBadges[$orderType] ?? '';
        
        // Build hierarchy info
        if ($hasParent) {
            // This is a child order - show parent info
            $parentOrder = Transaction::find($row->transfer_parent_id);
            $parentNo = $parentOrder ? $parentOrder->invoice_no : 'N/A';
            
            $html = '<div class="order-hierarchy">';
            $html .= $invoiceLink . ' ' . $typeBadge;
            $html .= '<br><small class="text-muted"><i class="fas fa-level-up-alt fa-rotate-90"></i> Parent: ' . $parentNo . '</small>';
            
            // Show vendor info for dropship orders (both WooCommerce and ERP Portal)
            if (in_array($orderType, ['wp_sales_order', 'erp_dropship_order'])) {
                $tracking = \App\Models\DropshipOrderTracking::where('transaction_id', $row->id)->with('vendor')->first();
                if ($tracking && $tracking->vendor) {
                    $vendorTypeLabel = $tracking->vendor->vendor_type === 'woocommerce' ? 'WC' : 'Portal';
                    $html .= '<br><small class="text-purple"><i class="fas fa-store"></i> ' . $tracking->vendor->display_name . ' (' . $vendorTypeLabel . ')</small>';
                    $statusBadge = $tracking->status_badge ?? '';
                    $html .= ' ' . $statusBadge;
                }
            }
            
            $html .= '</div>';
        } elseif ($hasChildren) {
            // This is a parent order - show child count and frozen status
            $childIds = is_array($row->sales_order_ids) ? $row->sales_order_ids : json_decode($row->sales_order_ids, true);
            $childCount = is_array($childIds) ? count($childIds) : 0;
            
            // Check if order is frozen (any child in packing stage)
            $transaction = Transaction::find($row->id);
            $isFrozen = $transaction && method_exists($transaction, 'hasChildInPackingStage') && $transaction->hasChildInPackingStage();
            $frozenBadge = $isFrozen 
                ? '<span class="label bg-red" title="Editing disabled - child orders in packing"><i class="fas fa-lock"></i> Frozen</span>' 
                : '';
            
            $html = '<div class="order-hierarchy">';
            $html .= $invoiceLink . ' ' . $typeBadge . ' ' . $frozenBadge;
            $html .= '<br><small class="text-muted"><i class="fas fa-sitemap"></i> Split into ' . $childCount . ' child order(s)</small>';
            $html .= '</div>';
        } else {
            // Standalone order
            $html = $invoiceLink . ' ' . $typeBadge;
        }
        
        return $html;
    }

    /**
     * Preprocessing Orders endpoint - shows orders before they move to pending
     * Part of Dropshipping preprocessing workflow
     */
    public function preprocessingOrder(Request $request)
    {
        return $this->pendingOrdersData($request, false);
    }

    /**
     * Shared DataTable builder for preprocessing / pending buckets.
     * Filters orders based on is_preprocessed flag for dropshipping workflow
     */
    private function pendingOrdersData(Request $request, bool $isPreprocessed)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sale_type = 'sales_order';
            $typeArray = ['sales_order'];
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true, $typeArray);
            // Show only Sales Orders (SO), hide ERP orders in Pending tab.
            $sells->where('transactions.type', 'sales_order');

            $sells = $sells->where('transactions.status', 'ordered');
            $sells = $sells->where('transactions.shipping_status', null);
            
            $date_range = $request->input('sell_list_filter_date_range');
            if (! empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) use ($request) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', $request->session()->get('user.id'));
                    }
                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', $request->session()->get('user.id'));
                    }
                });
            }

            if ($request->has('location_id')) {
                $location_id = $request->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', $request->session()->get('user.id'));
                }
            }

            $sells->whereNull('transactions.picking_status');

            // Exclude WooCommerce vendor orders (handled in Woo tab via wp_sales_order)
            $sells->where('transactions.type', '!=', 'wp_sales_order');
            // Exclude WooCommerce-origin orders entirely (they belong in Woo tab)
            $sells->where(function ($q) {
                $q->whereNull('transactions.woocommerce_order_id')
                  ->orWhere('transactions.woocommerce_order_id', 0)
                  ->orWhere('transactions.woocommerce_order_id', '')
                  ->orWhere('transactions.woocommerce_order_id', null);
            });
            $sells->where(function ($q) {
                $q->whereNull('transactions.source')
                  ->orWhere('transactions.source', '=', '')
                  ->orWhere('transactions.source', 'not like', 'woo%');
            });
            // Also exclude API-created Woo imports if flagged
            $sells->where(function ($q) {
                $q->whereNull('transactions.is_created_from_api')
                  ->orWhere('transactions.is_created_from_api', 0);
            });

            if ($isPreprocessed) {
                // Pending bucket: already preprocessed OR ERP child orders OR orders that already have split children.
                $sells->where(function ($q) {
                    $q->where('transactions.is_preprocessed', true)
                        ->orWhere('transactions.type', 'erp_sales_order')
                        ->orWhere(DB::raw('JSON_LENGTH(transactions.sales_order_ids)'), '>', 0);
                });
            } else {
                // Preprocessing bucket: only base ERP sales orders that have NOT been preprocessed and have no split children yet.
                $sells->where('transactions.type', 'sales_order');
                $sells->where(function ($q) {
                    $q->whereNull('transactions.is_preprocessed')
                        ->orWhere('transactions.is_preprocessed', false);
                });
                $sells->where(function ($q) {
                    $q->whereNull('transactions.sales_order_ids')
                        ->orWhere(DB::raw('JSON_LENGTH(transactions.sales_order_ids)'), '=', 0);
                });
                // exclude any child records (only parents should stay here)
                $sells->whereNull('transactions.transfer_parent_id');
            }

            $sells->groupBy('transactions.id');

            return DataTables::of($sells)
                ->addColumn('merged_column', function ($data) {
                    $name = '<b>' . $data->contact_id . '</b> ' . $data->supplier_business_name ?? $data->name;
                    $id = $data->cid ?? "";
                    return '<a href="/contacts/' . $id . '?type=customer" target="_blank" > ' . $name . '</a>';
                })
                ->addColumn('invoice_plain', function ($row) {
                    return $row->invoice_no;
                })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.contact_id', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('final_total', function ($row) {
                    return '$ ' . number_format($row->final_total, 2);
                })
                ->editColumn('total_paid', function ($row) {
                    return '$ ' . number_format($row->total_paid, 2);
                })
                ->addColumn('total_ordered_qty', function ($row) {
                    $total_ordered = $row->sell_lines->sum('ordered_quantity');
                    return '<span>' . $total_ordered . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '';
                    if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                        $html .= '<a href="' . action([\App\Http\Controllers\SellController::class, 'cancelSO'], [$row->id]) . '" style="margin-left:10px;color: red;" id="cancel-so"><i class="fas fa-ban" style="color: red;" aria-hidden="true"></i> ' . __('messages.cancel') . '</a>';
                    }
                    return $html;
                })
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->editColumn('payment_status', function ($row) {
                    if ($row->total_paid) {
                        $diff = $row->final_total - $row->total_paid;
                        if ($diff == 0) {
                            return '<a href="#" class="label bg-green" value="Paid">Paid</a>';
                        } elseif ($diff == $row->final_total) {
                            return '<a href="#" class="label bg-yellow" value="Due">Due</a>';
                        } elseif ($diff <= $row->final_total) {
                            return '<a href="#" class="label bg-info" value="Partial">Partial</a>';
                        }
                        return '<a href="#" class="label bg-yellow" value="Due">Due</a>';
                    } else {
                        return '<a href="#" class="label bg-yellow" value="due">Due</a>';
                    }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                    $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                    $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                    return $status;
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                        return $this->getOrderHierarchyHtml($row);
                    } else {
                        return $row->invoice_no;
                    }
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->where('transactions.invoice_no', 'like', "%{$keyword}%");
                })
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                ->rawColumns(['status', 'action', 'invoice_no', 'merged_column', 'total_ordered_qty', 'bulk_select', 'payment_status'])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }

    // order fulfillment tab tables
    /**
     * Summary of Pending Order
     * Pending Order table - shows preprocessed orders (already split)
     */
    public function processingOrder()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sale_type = 'sales_order';
            $typeArray = ['sales_order'];
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true, $typeArray);
            // Show only Sales Orders (SO), hide ERP orders in Pending tab.
            $sells->where('transactions.type', 'sales_order');
            
            // Include both 'ordered' and 'pending' statuses to show in Pending tab
            $sells = $sells->whereIn('transactions.status', ['ordered', 'pending']);
            $sells = $sells->where(function($q) {
                $q->whereNull('transactions.shipping_status')
                  ->orWhere('transactions.shipping_status', '');
            });

            $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $sells->whereNull('transactions.picking_status');
            $sells->groupBy('transactions.id');
            
            // Send push notifications for orders that reached processing stage
            try {
                // Get orders that match the processing criteria before DataTables processes them
                $processingOrders = clone $sells;
                $orders = $processingOrders->get();
                
                foreach ($orders as $order) {
                    // Check if notification has already been sent for this order in processing stage
                    $trackingStatus = \App\Models\OrderTrackingStatus::where('transaction_id', $order->id)
                        ->where('status', 'processing')
                        ->first();
                    
                    // Only send notification if not already sent
                    if (!$trackingStatus) {
                        try {
                            $contact = Contact::find($order->contact_id);
                            
                            if ($contact) {
                                // Create tracking status to mark notification as sent
                                \App\Models\OrderTrackingStatus::updateOrCreate(
                                    [
                                        'transaction_id' => $order->id,
                                        'status' => 'processing',
                                    ],
                                    [
                                        'status_date' => now(),
                                    ]
                                );
                                
                                // Send push notification to customer
                                $this->notificationUtil->sendPushNotification(
                                    'Order Processing',
                                    'Your order #' . $order->invoice_no . ' is now being processed and will be ready soon.',
                                    $order->contact_id,
                                    [
                                        'order_id' => $order->id,
                                        'invoice_no' => $order->invoice_no,
                                        'status' => 'processing',
                                        'type' => 'order_status_update'
                                    ],
                                    'non_urgent'
                                );
                                
                                Log::info('Processing order notification sent', [
                                    'transaction_id' => $order->id,
                                    'contact_id' => $order->contact_id,
                                    'invoice_no' => $order->invoice_no
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to send processing order notification', [
                                'transaction_id' => $order->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error in processing order notification logic', [
                    'error' => $e->getMessage()
                ]);
            }
            
            return DataTables::of($sells)
                ->addColumn('merged_column', function ($data) {
                     $name='<b>'. $data->contact_id . '</b> ' . $data->supplier_business_name??$data->name;
                    $id = $data->cid??"";
                    return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
                })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.contact_id', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('final_total', function ($row) {
                    return '$ ' . number_format($row->final_total, 2);
                })
                ->editColumn('total_paid', function ($row) {
                    return '$ ' . number_format($row->total_paid, 2);
                })
                ->addColumn('total_ordered_qty', function ($row) {
                    $total_ordered = $row->sell_lines->sum('ordered_quantity');
                    return '<span >' . $total_ordered. '</span>';
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<a href="' . action([\App\Http\Controllers\SellController::class, 'cancelSO'], [$row->id]) . '" style="margin-left:10px;color: red;" id="cancel-so"><i class="fas fa-ban" style="color: red;" aria-hidden="true"></i> ' . __('messages.cancel') . '</a>';
                        }
                        return $html;
                    }
                )        
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                  $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                  $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                  return $status;
              })
              
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                        return $this->getOrderHierarchyHtml($row);
                    } else {
                        return $row->invoice_no;
                    }
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->where('transactions.invoice_no', 'like', "%{$keyword}%");
                })
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                ->rawColumns(['status', 'action','invoice_no','merged_column','total_ordered_qty', 'bulk_select','payment_status'])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }
    public function pickingOrder()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');


        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = 'sales_order';

            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'ordered');
            $sells = $sells->where('transactions.shipping_status', null); $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $sells = $sells->where('transactions.picking_status', 'PICKING');

            $orderParams = request()->input('order', []);
            $columns = request()->input('columns', []);
            $blockFrontendSorting = false;
            if (!empty($orderParams)) {
                foreach ($orderParams as $order) {
                    $columnIndex = $order['column'];
                    $columnName = $columns[$columnIndex]['data'] ?? null;
                    if ($columnIndex == 0 || $columnName === 'bulk_select' || is_null($columnName)) {
                        $blockFrontendSorting = true;
                        break;
                    }
                }
            }
            if ($blockFrontendSorting || empty($orderParams)) {
                $sells = $sells->orderByRaw("
                    CASE 
                        WHEN transactions.priority > 0 THEN 0 
                        ELSE 1 
                    END ASC,
                    transactions.priority DESC,
                    transactions.id ASC
                ");
            }
            
            

            $sells->groupBy('transactions.id');
            // $sells->orderBy('transactions.id', 'DESC');
            // $sells->with('picker');
            $sells->with(['picker','verifier','sell_lines' => function ($q) {
                $q->select('transaction_id', 'picked_quantity','ordered_quantity' ,'verified_qty','unit_price_inc_tax');
            }]);
            
            return DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<a href="' . url('/sells-picking/'.$row->id) . '" ><i class="fas fa-dolly" aria-hidden="true"></i> ' . 'Pick Qty' . '</a>';
                        }
                        return $html;
                    }
                )
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                        $url = action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        return '<a href="#" data-href="' . $url . '" class="invoice-link">' . $row->invoice_no . '</a>';
                    } else {
                        return $row->invoice_no;
                    }
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->where('transactions.invoice_no', 'like', "%{$keyword}%");
                })
                ->addColumn('merged_column', function ($data) {
                    $name='<b>'. $data->contact_id . '</b> ' . $data->supplier_business_name??$data->name;
                   $id = $data->cid??"";
                   return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
               })
               ->filterColumn('merged_column', function ($query, $keyword) {
                   $query->where(function ($q) use ($keyword) {
                       $q->where('contacts.name', 'like', "%{$keyword}%")
                           ->orWhere('contacts.contact_id', 'like', "%{$keyword}%")
                         ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                   });
               })
            ->editColumn('final_total', function ($row) {
                return '$ ' . number_format($row->final_total, 2);
            })
            ->editColumn('total_paid', function ($row) {
                return '$ ' . number_format($row->total_paid, 2);
            })
                ->addColumn('total_picked_qty', function ($row) {
                    if($row->isPicked == true){
                        $total_verified = $row->sell_lines->sum('verified_qty');
                        $total_picked = $row->sell_lines->sum('picked_quantity');
                        $percentage = $total_picked > 0 ? ($total_verified / $total_picked) * 100 : 0;
                    }else{
                        $total_picked = $row->sell_lines->sum('picked_quantity');
                        $total_ordered = $row->sell_lines->sum('ordered_quantity');
                        $percentage = $total_ordered > 0 ? ($total_picked / $total_ordered) * 100 : 0;
                    }
                    return '<span data-total-percentage="' . $percentage . '">' . $total_picked. ' (' . number_format($percentage, 2) . '%)</span>';
                })
                ->addColumn('picked_qty_amount', function ($row) {
                    $amount = $row->sell_lines->sum(function ($line) {
                        return $line->picked_quantity * $line->unit_price_inc_tax;
                    });
                    return '<span>' . number_format($amount, 2) . '</span>';
                })
                
                // ->editColumn('value', '{{@num_format($value)}}')
                ->addColumn('bulk_select', function ($row) {
                    $value = DB::table('transaction_sell_lines')->select()->where('transaction_id', $row->id);
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->addColumn('picker_details', function ($row) {
                    $picker = $row->picker;
                    if ($picker) {
                        $id = $picker->id;
                        $maleImage = asset('img/man.png');  // Path to the man image
                        $femaleImage = asset('img/woman.png');  // Path to the woman

                        $genderIcon = $picker->gender == 'female' ?'<img src="' . $femaleImage . '" alt="Female" style="width: 25px; height: 25px; border-radius: 50%;">':'<img src="' . $maleImage . '" alt="Male" style="width: 25px; height: 25px; border-radius: 50%;">'; 
                    
                        // $genderIcon = $picker->gender == 'female' ? '<i class="fas fa-user-circle"></i>' :'<i class="fa fa-user-tie"></i>' ;
                        $userDetails = 
                        "Username: {$picker->username} |  Email: {$picker->email} | Full Name: {$picker->first_name} {$picker->last_name}";
                        return '<a href="/users/'.$id.' target="_blank">  <span  class="picker-gender tw-flex" data-toggle="tooltip" data-html="true" title="' . $userDetails . '">' . $genderIcon .' &nbsp;'. $picker->first_name . '</span></a>';
                    } else {
                        return '<i class="fas fa-question"></i>';
                    }
                })
                ->addColumn('verifier_details', function ($row) {
                    $verifier = $row->verifier;
                    if ($verifier) {
                        $id = $verifier->id;
                        $maleImage = asset('img/man.png');  // Path to the man image
                        $femaleImage = asset('img/woman.png');  // Path to the woman

                        $genderIcon = $verifier->gender == 'female' ?'<img src="' . $femaleImage . '" alt="Female" style="width: 25px; height: 25px; border-radius: 50%;">':'<img src="' . $maleImage . '" alt="Male" style="width: 25px; height: 25px; border-radius: 50%;">'; 
                    
                        // $genderIcon = $verifier->gender == 'female' ? '<i class="fas fa-user-circle"></i>' :'<i class="fa fa-user-tie"></i>' ;
                        $userDetails = 
                        "Username: {$verifier->username} |  Email: {$verifier->email} | Full Name: {$verifier->first_name} {$verifier->last_name}";
                        return '<a href="/users/'.$id.' target="_blank"><span  class="verifier-gender tw-flex" data-toggle="tooltip" data-html="true" title="' . $userDetails . '">' . $genderIcon .' &nbsp;'. $verifier->first_name . '</span></a>';
                    } else {
                        return '<i class="fas fa-ban text-center"></i>';
                    }
                })
                
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                  $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                  $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                  return $status;
              })
              ->editColumn('picking_status', function ($row) {
                $color="orange";
                if ($row->isPicked == 0) {
                    $status = 'PICKING';
                } elseif ($row->isPicked == 1 && $row->isVerified == 0 && $row->verifierID !== null) {
                    $status = 'VERIFYING';
                    $color='#00c0ef !important';
                } elseif ($row->isPicked == 1 && $row->isVerified == 0 && $row->verifierID === null) {
                    $status = 'PICKED';
                    $color= '#f60 !important';
                } elseif ($row->isPicked == 1 && $row->isVerified == 1 && $row->verifierID !== null) {
                    $status = 'VERIFIED';
                    $color = '#2dce89 !important';
                } else {
                    $status = 'UNKNOWN';
                    $color = 'black';
                }
                return '<a href="#" data-picking-time="'.$row->picking_started_at.'" data-status="'.$status.'" data-color="'.$color.'"  class="btn-modal edit-picking-status" data-href="' . action([\App\Http\Controllers\OrderfulfillmentController::class, 'changePickingStatus'], ['id' => $row->id]) . '"><span class="label " style="background-color:'.$color.';">' . $status . '</span></a>';
            })
              ->filterColumn('picker_details', function ($query, $keyword) {
                $query->whereHas('picker', function ($q) use ($keyword) {
                    $q->where('username', 'like', "%{$keyword}%")
                      ->orWhere('first_name', 'like', "%{$keyword}%")
                      ->orWhere('last_name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
              ->filterColumn('verifier_details', function ($query, $keyword) {
                $query->whereHas('verifier', function ($q) use ($keyword) {
                    $q->where('username', 'like', "%{$keyword}%")
                      ->orWhere('first_name', 'like', "%{$keyword}%")
                      ->orWhere('last_name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            
            
                ->rawColumns(['status', 'action','merged_column', 'bulk_select','picking_status','payment_status','picker_details','verifier_details','picked_qty_amount','total_picked_qty','invoice_no'])
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                // ->rawColumns([0, 4])
                
                ->make(true);
        }
    }
    public function pickedOrder()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = 'sales_order';

            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'ordered');
            $sells = $sells->where('transactions.shipping_status', null); $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $sells->where('transactions.picking_status', 'PICKED');

            $orderParams = request()->input('order', []);
            $columns = request()->input('columns', []);
            $blockFrontendSorting = false;
            if (!empty($orderParams)) {
                foreach ($orderParams as $order) {
                    $columnIndex = $order['column'];
                    $columnName = $columns[$columnIndex]['data'] ?? null;
                    if ($columnIndex == 1 || $columnName === 'bulk_select' || is_null($columnName)) {
                        $blockFrontendSorting = true;
                        break;
                    }
                }
            }
            if ($blockFrontendSorting || empty($orderParams)) {
                $sells = $sells->orderByRaw("
                    CASE 
                        WHEN transactions.priority > 0 THEN 0 
                        ELSE 1 
                    END ASC,
                    transactions.priority DESC,
                    transactions.id ASC
                ");
            }

            $sells->groupBy('transactions.id');
            $sells->with(['sell_lines' => function ($q) {
                $q->select('transaction_id', 'picked_quantity', 'unit_price_inc_tax');
            }]);

            return DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'manualPack'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-shipping-fast" aria-hidden="true"></i> ' . 'Make Shipment' . '</a>';
                        }
                        return $html;
                    }
                )
                ->addColumn('picking_time', function ($row) {
                    $start_time = $row->picking_started_at;
                    $end_time = $row->picking_ended_at;
                
                    if (!$start_time || !$end_time) {
                        return 'N/A';
                    }
                
                    $start = \Carbon\Carbon::parse($start_time);
                    $end = \Carbon\Carbon::parse($end_time);
                
                    $durationInSeconds = $start->diffInSeconds($end);
                
                    $hours = floor($durationInSeconds / 3600);
                    $minutes = floor(($durationInSeconds % 3600) / 60);
                    $seconds = $durationInSeconds % 60;
                
                    $parts = [];
                    if ($hours > 0) $parts[] = "$hours hr";
                    if ($minutes > 0) $parts[] = "$minutes min";
                    if ($seconds > 0 || empty($parts)) $parts[] = "$seconds sec";
                
                    return implode(' ', $parts);
                })
                
                
                ->addColumn('merged_column', function ($data) {
                    $name='<b>'. $data->contact_id . '</b> ' . $data->supplier_business_name??$data->name;
                   $id = $data->cid??"";
                   return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
               })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                        $url = action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        return '<a href="#" data-href="' . $url . '" class="invoice-link">' . $row->invoice_no . '</a>';
                    } else {
                        return $row->invoice_no;
                    }
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->where('transactions.invoice_no', 'like', "%{$keyword}%");
                })
                ->editColumn('final_total', function ($row) {
                    return '$ ' . number_format($row->final_total, 2);
                })
                ->editColumn('total_paid', function ($row) {
                    return '$ ' . number_format($row->total_paid, 2);
                })
                ->addColumn('total_picked_qty', function ($row) {
                    $total_qty = $row->sell_lines->sum('picked_quantity');
                    return '<span>' . $total_qty . '</span>';
                })
                ->addColumn('picked_qty_amount', function ($row) {
                    $amount = $row->sell_lines->sum(function ($line) {
                        return $line->picked_quantity * $line->unit_price_inc_tax;
                    });
                    return '<span>' . number_format($amount, 2) . '</span>';
                })
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                  $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                  $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                  return $status;
              })
              ->editColumn('picking_status', function ($row)  {
                 $status =  '<a href="#" class="btn-modal_sell_pick_verify_data" data-href="' . action([\App\Http\Controllers\SellController::class, 'sellPickVerifyData'], [$row->id]) . '"><span class="label ' . 'bg-green' . '">' . $row->picking_status . '</span></a>';
                return $status;
            })
        
            ->rawColumns(['status', 'action','invoice_no','merged_column','bulk_select','picking_status','payment_status','picked_qty_amount','total_picked_qty'])
            ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }
    public function cancelOrder()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
       


        if (request()->ajax()) {
         
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = 'sales_order';

            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'cancelled');
            $sells = $sells->where('transactions.shipping_status', null);
            $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $sells->whereNull('transactions.picking_status');
            $sells->groupBy('transactions.id');
            // $sells->orderBy('transactions.id', 'DESC');

            return DataTables::of($sells)
                ->addColumn('merged_column', function ($data) {
                    return $data->name . ' ' . $data->supplier_business_name;
                })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . __('messages.view') . '</a>';
                        }
                        
                        return $html;
                    }
                )
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                        $url = action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        return '<a href="#" data-href="' . $url . '" class="invoice-link">' . $row->invoice_no . '</a>';
                    } else {
                        return $row->invoice_no;
                    }
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->where('transactions.invoice_no', 'like', "%{$keyword}%");
                })
                
                // ->editColumn('value', '{{@num_format($value)}}')
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                // Ensure Final Total column always shows the monetary value, even if transactions.final_total is 0.
                ->editColumn('final_total', function ($row) {
                    $final = (float) $row->final_total;

                    // If final_total is zero but we have line-level amounts, reconstruct it.
                    if ($final == 0 && (! is_null($row->total_before_tax) || ! is_null($row->tax_amount) || ! is_null($row->discount_amount))) {
                        $before_tax = (float) $row->total_before_tax;
                        $tax        = (float) $row->tax_amount;
                        $discount   = (float) $row->discount_amount;

                        $final = $before_tax + $tax - $discount;
                    }

                    return $this->transactionUtil->num_f($final, true);
                })
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                  $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                  $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                  return $status;
              })
              ->editColumn('picking_status', function ($row)  {
                // $status_color = ! empty($this->picking_status_colors[$row->picking_status]) ? $this->picking_status_colors[$row->picking_status] : 'bg-gray';
                $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-navy' . '">' . 'Stock Released' . '</span></a>';
                return $status;
            })
             
              ->rawColumns(['status', 'action','invoice_no','merged_column', 'bulk_select','picking_status','payment_status'])
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                // ->rawColumns([0, 4])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }
    public function completeOrder(){
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sale_type = 'sales_order';
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'completed');
            $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }
                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }
            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }
            $sells->where('transactions.picking_status', 'INVOICED');
            $sells->groupBy('transactions.id');
            $sells->with(['sell_lines' => function ($q) {
                $q->select('transaction_id', 'picked_quantity', 'unit_price_inc_tax');
            }]);
            // return $sells->get();
                return DataTables::of($sells)
                ->addColumn('merged_column', function ($data) {
                    $name='<b>'. $data->contact_id . '</b> ' . $data->supplier_business_name??$data->name;
                   $id = $data->cid??"";
                   return '<a href="/contacts/'.$id.'?type=customer" target="_blank" > '.$name.'</a>';
               })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                        $url = action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        return '<a href="#" data-href="' . $url . '" class="invoice-link">' . $row->invoice_no . '</a>';
                    } else {
                        return $row->invoice_no;
                    }
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->where('transactions.invoice_no', 'like', "%{$keyword}%");
                })
                ->editColumn('final_total', function ($row) {
                    return '$ ' . number_format($row->final_total, 2);
                })
                ->editColumn('total_paid', function ($row) {
                    return '$ ' . number_format($row->total_paid, 2);
                })
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                                $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                                $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                                return $status;
                            })
                            ->editColumn('picking_status', function ($row)  {
                 $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-green' . '">' . $row->picking_status . '</span></a>';
                return $status;
            })
            ->rawColumns(['status','invoice_no','merged_column','payment_status','picking_status'])
            ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }
  
    // picker assign to order
    public function applyOrderOperation(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $orderIds = $request->input('ids'); // array of sells 
        $pickerId = null;
        if(empty($request->input('activePicker'))||$request->input('activePicker')==null){
            $pickerId = (int) $request->input('operation');
            $activity = PickersActivity::where('user_id', $pickerId)->first();
            if($activity){
            $activity->current_status = 'picking';
                $activity->last_assigned = now();
                $activity->save();
            }else{
                $activity = new PickersActivity();
                $activity->user_id = $pickerId;
                $activity->current_status = 'picking';
                $activity->last_assigned = now();
                $activity->save();
            }
        }else{
            $pickerId = (int) $request->input('activePicker');
            $activity = PickersActivity::where('user_id', $pickerId)->first();
            if($activity){
                $activity->current_status = 'picking';
                $activity->last_assigned = now();
                $activity->save();
            }else{
                $activity = new PickersActivity();
                $activity->user_id = $pickerId;
                $activity->current_status = 'picking';
                $activity->last_assigned = now();
                $activity->save();
            }
        }
        $transaction = Transaction::where('business_id', $business_id)->whereIn('id', $orderIds)
            ->update([
                'picking_status' => 'PICKING',
                'pickerID' => $pickerId
            ]);

        // Send "Order Processing" push notification to customer (contact_id) for each order moved to Processing
        foreach ($orderIds as $txnId) {
            try {
                $order = Transaction::find($txnId);
                if (!$order) {
                    continue;
                }
                $alreadySent = \App\Models\OrderTrackingStatus::where('transaction_id', $order->id)
                    ->where('status', 'processing')
                    ->exists();
                if ($alreadySent) {
                    continue;
                }
                \App\Models\OrderTrackingStatus::updateOrCreate(
                    ['transaction_id' => $order->id, 'status' => 'processing'],
                    ['status_date' => now()]
                );
                $contact = Contact::find($order->contact_id);
                if ($contact) {
                    $this->notificationUtil->sendPushNotification(
                        'Order Processing',
                        'Your order #' . $order->invoice_no . ' is now being processed and will be ready soon.',
                        $order->contact_id,
                        [
                            'order_id' => $order->id,
                            'invoice_no' => $order->invoice_no,
                            'status' => 'processing',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                    Log::info('Processing order notification sent (on move to Processing)', [
                        'transaction_id' => $order->id,
                        'contact_id' => $order->contact_id,
                        'invoice_no' => $order->invoice_no
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send processing order notification in applyOrderOperation', [
                    'transaction_id' => $txnId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Send notification to picker
        $notificationResult = false;
        try {
            $fireBaseServices = new FireBaseServices();
            $notificationResult = $fireBaseServices->sendOrderAssignmentNotification($pickerId, $orderIds, $business_id);
        } catch (\Exception $e) {
            Log::error('Failed to send picker notification in applyOrderOperation', [
                'picker_id' => $pickerId,
                'order_ids' => $orderIds,
                'error' => $e->getMessage()
            ]);
            // Don't fail the whole operation if notification fails
        }
        
        return response()->json(
            [
                'status' => true,
                'message' => 'Operation completed successfully.',
                'notification_sent' => $notificationResult ? true : false
            ]
        );
    }
    public function changePickingStatus($id){
       return view('order_fulfillment.partials.change_picking_status',compact('id'));
    }
    public function changePickingStatusStore(Request $request){
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $orderIds = $request->input('id');
        $status = $request->input('status');
        $transaction = Transaction::where('business_id', $business_id)->where('id', $orderIds)->first();
        if($status=='picking'){
            $transaction->isPicked = 0;
        }else if($status=='picked'){
            $transaction->isPicked = 1;
        } else if($status=='verifying'){
            $transaction->isPicked = 1;
            $transaction->isVerified = 0;
            $transaction->verifierID= request()->session()->get('user.id');
        } else if($status=='verified'){
            $transaction->isPicked = 1;
            $transaction->isVerified = 1;
            $transaction->verifierID= request()->session()->get('user.id');
        } 
        $transaction->save();
        return response()->json([
            'status' => true,
            'message' => 'Picking status updated successfully.'
        ]);
    }
    // reassign at processing 
    public function held1(Request $request){
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (
            ! $is_admin &&
            ! auth()->user()->hasAnyPermission([
                'sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view',
                'view_own_sell_only', 'view_commission_agent_sell',
                'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping',
                'so.view_all', 'so.view_own'
            ])
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $business_id = $request->session()->get('user.business_id');
        
        $orderIds = $request->input('orders',[]);
        $staffId = $request->input('staff');
        $type = $request->input('type');
        if (empty($orderIds) || !$staffId) {
            return response()->json([
                'status' => false,
                'message' => 'Missing order(s) or staff selection.'
            ]);
        }

        // Fetch the first transaction to decide whether to assign picker or verifier
        $transaction = Transaction::where('business_id', $business_id)
            ->where('id', $orderIds)
            ->get();
            $vcount=0;
            $pcount=0;
            $pickerOrderIds = []; // Track orders assigned to pickers for notifications
        foreach($orderIds as $orderIds){
            $transaction = Transaction::with('sell_lines')->where('business_id', $business_id)
            ->where('id', $orderIds)
            ->first();
            if($type==='picker' && !$transaction->isPicked){
                $vcount++;
                $transaction->pickerID= $staffId;
                $pickerOrderIds[] = $orderIds; // Add to notification list
                // picker cart abandoned, do fresh picking
                foreach ($transaction->sell_lines as $sellLine) {
                    $variation = Variation::with('variation_location_details')
                        ->where('id', $sellLine->variation_id)
                        ->first();

                    if ($variation) {
                        $product = $variation->product;
                        // Only manage stock if enable_stock is true
                        if ($product && $product->enable_stock == 1) {
                            $location = $variation->variation_location_details->first(); // Fixed line
                            if ($location) {
                                $location->qty_available += $sellLine->picked_quantity;
                                $location->save();
                            }
                        }
                    }

                    $sellLine->picked_quantity = null;
                    $sellLine->is_picked = 0;
                    $sellLine->save();
                }
                
            } 
            if($type==='verifier' && !$transaction->isVerified) {
                $transaction->verifierID= $staffId;
                $pcount++;

            }
            $transaction->save();
            
        }
        $msg1 = '';
        $msg2 = '';
        $msg3 = '';

        if($vcount>0){
            $msg1= $vcount.' order assinged to Verify';
        }
        else  if($pcount>0){
            $msg2= $pcount.' order assinged to Picker';
        }
        else{
            $msg3 = 'order is not assigneed to Picker or Verifier';
        }

        return response()->json([
            'status' => true,
            'message' => $msg1. ' ' .$msg2.' '.$msg3
        ]);
    }
    public function held(Request $request){
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $orderIds = $request->input('ids');
        $users = User::where('business_id',$business_id)
        ->select('id', 'first_name', 'last_name','username')
        ->get()
        ->pluck('username', 'id');
        return view('order_fulfillment.partials.assign')->with(compact('users','orderIds'));
    }

    // change order stage 
    public function processTopending(Request $request)
    {
        // Authorization check - you can adjust it as per your requirements
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);
        $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
        $allow_overselling = !empty($pos_settings['allow_overselling']) ? true : false;
        $orderIds = $request->input('ids'); // array of sells 
        $pickerId = null; // 2 =>picker id
        foreach($orderIds as $orderIds){
            $transaction = Transaction::with('sell_lines')->where('business_id', $business_id)->where('id', $orderIds)->first();
            $pickerId=$transaction->pickerID;
            $transaction->picking_status = null;
            $transaction->pickerID = null;
            $transaction->verifierID = null;
            $transaction->isPicked = 0;
            $transaction->isVerified = 0;
            $transaction->save();
            foreach ($transaction->sell_lines as $sellLine) {
                    $variation = Variation::with('variation_location_details')
                        ->where('id', $sellLine->variation_id)
                        ->first();

                    if ($variation) {
                        $product = $variation->product;
                        // Only manage stock if enable_stock is true
                        if ($product && $product->enable_stock == 1) {
                            if ($allow_overselling) {
                                $location = $variation->variation_location_details->first();
                            } else {
                                $location = $variation->variation_location_details->firstWhere('qty_available', '>=', 0);
                                if(!$location){
                                    Log::warning('Case failure at OrderfulfillmentController.php Log 1169' . $sellLine->id);
                                    $location = $variation->variation_location_details->first();
                                }
                            }
                            if ($location) {
                                // $location->in_stock_qty += $sellLine->ordered_quantity;
                                $location->qty_available += $sellLine->picked_quantity;
                                $location->save();
                            }
                        }
                    }
                    $sellLine->is_picked = 0;
                    $sellLine->isVerified = 0;
                    $sellLine->picked_quantity = 0;
                    $sellLine->verified_qty = 0;
                    $sellLine->manual_picked_qty = 0;
                    $sellLine->barcode_picked_qty = 0;
                    $sellLine->shorted_picked_qty = 0;
                    $sellLine->save();
            }

            $transactions = Transaction::where('pickerID', $pickerId)->
            where('isPicked', false)->get();
            if ($transactions->count() == 0) {
                $pickerActivity = PickersActivity::where('user_id', $pickerId)->first();
                $pickerActivity->current_status = null;
                $pickerActivity->save();
            }
        }

        
        return response()->json(
            [
                'status' => true,
                'message' => 'Operation completed successfully.'
            ]
      
        );
    }
    public function orderTopacking(Request $request)
    {
      $is_admin = $this->businessUtil->is_admin(auth()->user());

      if (! $is_admin && ! auth()->user()->hasAnyPermission([
          'sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 
          'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 
          'access_own_shipping', 'access_commission_agent_shipping', 
          'so.view_all', 'so.view_own'
      ])) {
          return response()->json(['status' => false, 'message' => 'Unauthorized action'], 403);
      }
  
      $business_id = request()->session()->get('user.business_id');
      $orderIds = $request->input('ids');
  
      if (!$orderIds || !is_array($orderIds)) {
          return response()->json(['status' => false, 'message' => 'No valid order IDs provided']);
      }
  
      DB::beginTransaction();
      try {
          $transactions = Transaction::with('payment_lines')->where('business_id', $business_id)
              ->where('type', 'sales_order')
              ->whereIn('id', $orderIds)
              ->where('status', 'cancelled')
              ->get();
  
          if ($transactions->isEmpty()) {
              return response()->json(['status' => false, 'message' => 'No valid canceled orders found']);
          }
  
          foreach ($transactions as $transaction) {
              foreach ($transaction->sell_lines as $sellLine) {
                  $variation = Variation::with('variation_location_details')
                      ->where('id', $sellLine->variation_id)
                      ->first();
  
                  if ($variation) {
                      $product = $variation->product;
                      // Only manage stock if enable_stock is true
                      if ($product && $product->enable_stock == 1) {
                          $location = $variation->variation_location_details->firstWhere('qty_available', '>=', $sellLine->picked_quantity);
                          if ($location) {
                            // handel signed value 
                            $new_stock_qty = $location->in_stock_qty - $sellLine->ordered_quantity;
                            if ($new_stock_qty < 0) {
                                $new_stock_qty = 0;
                            }
                            $location->in_stock_qty = $new_stock_qty;
                              $location->save();
                          }
                      }
                  }
              }
              $amount =0;
              foreach ($transaction->payment_lines as $paymentLine) {
                $amount += $paymentLine->amount;
              }
              $contact = Contact::find($transaction->contact_id);
              $contact->balance -= $amount;
              $contact->save();
              $transaction->status = 'ordered';
              $transaction->save();
          }
        
          DB::commit();
          return response()->json(['status' => true, 'message' => count($transactions) . ' orders restored successfully']);
      } catch (\Exception $e) {
          DB::rollBack();
          return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
      }
    }
    public function packingToprocess(Request $request)
    {
        // Authorization check - you can adjust it as per your requirements
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $orderIds = $request->input('ids'); // array of sells 
        $pickerId = (int) $request->input('operation'); // 2 =>picker id
        $transaction = Transaction::where('business_id', $business_id)->whereIn('id', $orderIds)
            ->update([
                'picking_status' => 'PICKING',
                // 'pickerID' => $pickerId
            ]);

        // Send "Order Processing" push notification to customer for each order moved to Processing
        foreach ($orderIds as $txnId) {
            try {
                $order = Transaction::find($txnId);
                if (!$order) {
                    continue;
                }
                $alreadySent = \App\Models\OrderTrackingStatus::where('transaction_id', $order->id)
                    ->where('status', 'processing')
                    ->exists();
                if ($alreadySent) {
                    continue;
                }
                \App\Models\OrderTrackingStatus::updateOrCreate(
                    ['transaction_id' => $order->id, 'status' => 'processing'],
                    ['status_date' => now()]
                );
                $contact = Contact::find($order->contact_id);
                if ($contact) {
                    $this->notificationUtil->sendPushNotification(
                        'Order Processing',
                        'Your order #' . $order->invoice_no . ' is now being processed and will be ready soon.',
                        $order->contact_id,
                        [
                            'order_id' => $order->id,
                            'invoice_no' => $order->invoice_no,
                            'status' => 'processing',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                    Log::info('Processing order notification sent (packingToprocess)', [
                        'transaction_id' => $order->id,
                        'contact_id' => $order->contact_id,
                        'invoice_no' => $order->invoice_no
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send processing order notification in packingToprocess', [
                    'transaction_id' => $txnId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json(
            [
                'status' => true,
                'message' => 'Operation completed successfully.'
            ]
        );
    }
    public function processtopacking(Request $request){
        $manage_order_module = session()->get('business.manage_order_module');
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission([
            'sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view',
            'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping',
            'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'
        ])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $orderIds = $request->input('ids'); // array of order IDs
        $pickerId = (int) $request->input('operation'); // Picker ID

        if($manage_order_module == 'manual'){
            $transactions = Transaction::where('business_id', $business_id)
                ->whereIn('id', $orderIds)
                ->get();
                
            Transaction::where('business_id', $business_id)
                ->whereIn('id', $orderIds)
                ->update([
                    'picking_status' => 'PICKED',
                    'pickerID' => request()->session()->get('user.id')
                ]);

            $variationQuantities = TransactionSellLine::whereIn('transaction_id', $orderIds)
                ->with('variation.product')
                ->get()
                ->groupBy('variation_id')
                ->map(function($lines) {
                    return $lines->sum('quantity');
                });
            foreach ($variationQuantities as $variationId => $quantity) {
                if ($variationId) {
                    $variation = Variation::with('product')->find($variationId);
                    // Only manage stock if enable_stock is true
                    if ($variation && $variation->product && $variation->product->enable_stock == 1) {
                        VariationLocationDetails::where('variation_id', $variationId)
                            ->decrement('qty_available', $quantity);
                    }
                }
            }
            
            // Send notifications for each transaction that became PICKED
            foreach ($transactions as $transaction) {
                try {
                    // Create tracking status
                    \App\Models\OrderTrackingStatus::updateOrCreate(
                        [
                            'transaction_id' => $transaction->id,
                            'status' => 'packed',
                        ],
                        [
                            'status_date' => now(),
                        ]
                    );
                    
                    // Send email notification
                    $contact = Contact::find($transaction->contact_id);
                    if ($contact && !empty($contact->email)) {
                        $notificationUtil = new NotificationUtil();
                        $notificationUtil->autoSendNotification(
                            $business_id,
                            'order_packed',
                            $transaction,
                            $contact
                        );
                    }
                    
                    // Send Firebase push notification for PICKED status
                    if ($contact) {
                        $this->notificationUtil->sendPushNotification(
                            'Order Picked',
                            'Your order #' . $transaction->invoice_no . ' has been picked and is being prepared for packing.',
                            $transaction->contact_id,
                            [
                                'order_id' => $transaction->id,
                                'invoice_no' => $transaction->invoice_no,
                                'status' => 'picked',
                                'type' => 'order_status_update'
                            ],
                            'non_urgent'
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send order packed notification in processtopacking (manual)', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            return response()->json(['status' => true, 'message' => 'Order moved to packing (reduced stock).']);
        }


        $pickedOrderIds = TransactionSellLine::whereIn('transaction_id', $orderIds)
            ->where('picked_quantity', '>', 0)
            ->pluck('transaction_id')
            ->unique()
            ->values();

        if ($pickedOrderIds->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Pick some quantity and verify before proceeding.'
            ]);
        }
        if (count($orderIds) === 1) {
            $orderId = $orderIds[0];

            $transaction = Transaction::where('business_id', $business_id)
                ->where('id', $orderId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => false,
                    'message' => "Order not found."
                ]);
            }

            if (!$transaction->isPicked) {
                return response()->json([
                    'status' => false,
                    'message' => "Picker is not assigned."
                ]);
            }

            if (!$transaction->isVerified) {
                return response()->json([
                    'status' => false,
                    'message' => "Verifier is not assigned."
                ]);
            }

            $transaction->picking_status = 'PICKED';
            $transaction->save();

            // Create tracking status and send notification
            try {
                \App\Models\OrderTrackingStatus::updateOrCreate(
                    [
                        'transaction_id' => $transaction->id,
                        'status' => 'packed',
                    ],
                    [
                        'status_date' => now(),
                    ]
                );
                
                $contact = Contact::find($transaction->contact_id);

                $isB2C = false;
                if($transaction->location_id){
                    $location = BusinessLocation::find($transaction->location_id);
                    if($location->is_b2c){
                        $isB2C = true;
                    }
                }
                if($isB2C){
                    $custom_data = (object) [
                        'contact_id' => $contact->id,
                        'transaction' => $transaction,
                        'brand_id' => $contact->brand_id,
                        'is_b2c' => true,
                        'email' => $contact->email,
                    ];
                    SendNotificationJob::dispatch(true, $business_id, 'order_packed', $contact, $custom_data, $transaction);
                }else{
                    if ($contact && !empty($contact->email)) {
                        $notificationUtil = new NotificationUtil();
                        $notificationUtil->autoSendNotification(
                            $business_id,
                            'order_packed',
                            $transaction,
                            $contact
                        );
                    }
                }
                
                // Send Firebase push notification for PICKED status
                if ($contact) {
                    $this->notificationUtil->sendPushNotification(
                        'Order Picked',
                        'Your order #' . $transaction->invoice_no . ' has been picked and is being prepared for packing.',
                        $transaction->contact_id,
                        [
                            'order_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'status' => 'picked',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to send order packed notification in processtopacking (single)', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => "Order moved to packing."
            ]);
        }

        $movedToPacking = 0;
        $stillProcessing = 0;
        $errors = [];

        foreach ($orderIds as $orderId) {
            $transaction = Transaction::where('business_id', $business_id)
                ->where('id', $orderId)
                ->first();               
            if (!$transaction) {
                $errors[] = "Order ID $orderId not found.";
                continue;
            }

            if (!$transaction->isPicked) {
                $errors[] = "Picker is not assigned for Order ID $orderId.";
                $stillProcessing++;
                continue;
            }

            if (!$transaction->isVerified) {
                $errors[] = "Verifier is not assigned for Order ID $orderId.";
                $stillProcessing++;
                continue;
            }

            // Passed all checks, mark as picked
            $transaction->picking_status = 'PICKED';
            $transaction->save();

            // Create tracking status and send notification
            try {
                \App\Models\OrderTrackingStatus::updateOrCreate(
                    [
                        'transaction_id' => $transaction->id,
                        'status' => 'packed',
                    ],
                    [
                        'status_date' => now(),
                    ]
                );
                
                $contact = Contact::find($transaction->contact_id);
                if ($contact && !empty($contact->email)) {
                    $notificationUtil = new NotificationUtil();
                    $notificationUtil->autoSendNotification(
                        $business_id,
                        'order_packed',
                        $transaction,
                        $contact
                    );
                }
                
                // Send Firebase push notification for PICKED status
                if ($contact) {
                    $this->notificationUtil->sendPushNotification(
                        'Order Picked',
                        'Your order #' . $transaction->invoice_no . ' has been picked and is being prepared for packing.',
                        $transaction->contact_id,
                        [
                            'order_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'status' => 'picked',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send order packed notification in processtopacking (multiple)', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }

            $movedToPacking++;
            }

        $message = "$movedToPacking orders moved to packing.";
        if ($stillProcessing > 0) {
            $message .= " $stillProcessing orders are still processing.";
        }
        return response()->json([
            'status' => $movedToPacking > 0,
            'message' => $message,
            'errors' => $errors
        ]);
    
    }

    public function markAsPicked(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $orderIds = $request->input('ids'); //
        $hasPickedQty = TransactionSellLine::whereIn('transaction_id', $orderIds)
        ->where('picked_quantity', '>', 0)
        ->exists();

    if (! $hasPickedQty) {
        return response()->json([
            'status' => false,
            'message' => 'Pick at least one product before marking as picked.'
        ],);
    }

        $transactions = Transaction::where('business_id', $business_id)->whereIn('id', $orderIds)->get();
        
        Transaction::where('business_id', $business_id)->whereIn('id', $orderIds)
            ->update([
                'picking_status' => 'PICKED',
                'pickerID' =>  request()->session()->get('user.id')
            ]);
            
        // Send notifications for each transaction that became PICKED
        foreach ($transactions as $transaction) {
            try {
                // Create tracking status
                \App\Models\OrderTrackingStatus::updateOrCreate(
                    [
                        'transaction_id' => $transaction->id,
                        'status' => 'packed',
                    ],
                    [
                        'status_date' => now(),
                    ]
                );
                
                // Send email notification
                $contact = Contact::find($transaction->contact_id);
                if ($contact && !empty($contact->email)) {
                    $notificationUtil = new NotificationUtil();
                    $notificationUtil->autoSendNotification(
                        $business_id,
                        'order_packed',
                        $transaction,
                        $contact
                    );
                }
                
                // Send Firebase push notification for PICKED status
                if ($contact) {
                    $this->notificationUtil->sendPushNotification(
                        'Order Picked',
                        'Your order #' . $transaction->invoice_no . ' has been picked and is being prepared for packing.',
                        $transaction->contact_id,
                        [
                            'order_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'status' => 'picked',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send order packed notification in markAsPicked', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return response()->json(
            [
                'status' => true,
                'message' => 'Operation completed successfully.'
            ]
        );
    }
    public function packedOrderToInoice(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_crm = $this->moduleUtil->isModuleInstalled('Crm');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');


        if (request()->ajax()) {
            // Log::info('Access');
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sale_type = 'sales_order';
            $typeArray = ['sales_order', 'erp_sales_order'];
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true, $typeArray);
            $sells = $sells->where('transactions.status', 'ordered');
            $sells = $sells->where('transactions.shipping_status', null);
            $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $sells->where('transactions.picking_status', 'PACKED');
            $sells->groupBy('transactions.id');
            $sells->orderBy('transactions.id', 'DESC');

            return DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'saleInvoiceCreate'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . 'View SO' . '</a>';
                        }
                        // return '<div class="btn-group">
                        //             <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" 
                        //                 data-toggle="dropdown" aria-expanded="false">' .
                        //     __('messages.actions') .
                        //     '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        //                 </span>
                        //             </button>
                        //             <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        //             ' . $html . '
                        //             </ul>
                        //             </div>';
                        return $html;
                    }
                )
                // ->editColumn('value', '{{@num_format($value)}}')
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                // ->addColumn('picking_status',function ($row) {
                //     return $row->picking_status;
                // })
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                // ->rawColumns([0, 4])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }


    // old lock logic 
    public function lockSale(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $cid = request()->session()->get('user.id');
        $transaction = Transaction::findOrFail($id);

        if ($transaction->isEditable == false && $transaction->editingSalesRep != $cid) {
            $user =  User::find($transaction->editingSalesRep);
            return response()->json([
                'status' => false,
                'message' => 'This Order locked by ' . $user->first_name . ' ' . $user->last_name,
            ],);
        }
        $transaction->isEditable = false;
        $transaction->editingSalesRep =  $cid;
        $transaction->save();

        return response()->json([
            'status' => true,
            'message' => 'Edit Mode Locked for You',
        ]);
    }
    public function unLock(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $cid = request()->session()->get('user.id');
        $transaction = Transaction::findOrFail($id);

        if ($transaction->isEditable == false && $transaction->editingSalesRep != $cid) {
            $user =  User::find($transaction->editingSalesRep);
            return response()->json([
                'status' => false,
                'message' => 'This Order locked by ' . $user->first_name . ' ' . $user->last_name,
            ],);
        }

        $transaction->isEditable = true;
        $transaction->editingSalesRep =  null;
        $transaction->save();

        return response()->json([
            'status' => true,
            'message' => 'Edit Mode Unlocked form You',
        ]);
    }
    public function takeOver(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $cid = request()->session()->get('user.id');
        $password = $request->input('password');
        $correctPassword = 'passme';

        if ($password != $correctPassword) {
            // if (request()->ajax()) {
            //     $output = [
            //         'success' => 0,
            //         'msg' => "Incorrect password.",
            //     ];
            //     return $output;
            // }
            throw response()->json([
                'status' => false,
                'error' => 'Incorrect password.',
            ]);
        }

        $transaction = Transaction::findOrFail($id);
        $transaction->isEditable = false;
        $transaction->editingSalesRep = $cid;
        $transaction->save();
        if (request()->ajax()) {
            $output = [
                'success' => 1,
                'msg' => "Transaction has been unlocked.",
            ];
            return $output;
        }
        return response()->json([
            'status' => true,
            'message' => 'Transaction has been unlocked.',
        ]);
    }
    public function unLockModel(Request $request,$modelType, $modelId)
    {
        ModalAccess::where('last_ping_at', '<=', now()->subMinutes(1))->delete();

        $activeAccess = ModalAccess::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('session_id', '!=', session()->getId())
            ->first();

        if ($activeAccess && $activeAccess->user_id != auth()->id()) {
            $user = User::find($activeAccess->user_id);
                return view('sell.partials.edit_lock_modal')
            ->with(compact('modelId','user' ,'modelType'));            
        }      

        // return view('sell.partials.edit_lock_modal')
        //     ->with(compact('id'));
    }

    // new lock logic 
    public function checkModalAccess($modelType, $modelId, $isUtil=false)
    {
        // $is_invoice=request()->query('isRedirect');
        // user id 
        $userID = 0;
        $isAPIREQ = false;
        try {
            $userID = JWTAuth::parseToken()->authenticate()->id;
            $isAPIREQ = true;
        } catch (\Exception $e) {
            $userID = auth()->id();
        }

        // Delete old records first
        ModalAccess::where('last_ping_at', '<=', now()->subMinutes(1))->delete();

        $activeAccess = ModalAccess::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('user_id', '!=', $userID)
            ->first();

        if ($activeAccess && $activeAccess->user_id != $userID) {
            $user = User::find($activeAccess->user_id);
            if($isUtil){
                return [
                    'status' => false,
                    'message' => $user->first_name . ' ' . $user->last_name . ' is already editing this modal',
                ];
            }
            if (request()->ajax() ||$isAPIREQ) {
                return response()->json([
                    'status' => false,
                    'message' => $user->first_name . ' ' . $user->last_name . ' is already editing this modal',
                ]);
            } else {
                return view('sell.partials.edit_lock')
                ->with(compact('modelId','user','modelType'));
            }
            
        }

        ModalAccess::updateOrCreate(
            ['model_type' => $modelType, 'model_id' => $modelId, 'session_id' => session()->getId()??'StateLessSeessionFound'],
            ['user_id' => $userID, 'last_ping_at' => now()]
        );
        if($isUtil){
            return [
                'status'=>true,
                'message'=>'You can edit this modal'
            ];
        }
        return response()->json(['status' => true, 'message' => 'You can edit this modal']);
    }
    public function pingModal($modelType, $modelId){
        // user id 
        $userID = 0;
        $isAPIREQ = false;
        $user = null;
        try {
            $userID = JWTAuth::parseToken()->authenticate()->id;
            $user = User::find($userID);
            $isAPIREQ = true;
        } catch (\Exception $e) {
            $userID = auth()->id();
            $user = User::find($userID);
        }

        // Delete any expired locks (older than 3 minutes)
        ModalAccess::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('last_ping_at', '<=', now()->subMinutes(3))
            ->delete();
        
        // Try to update existing access for current user
        $updated = ModalAccess::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('user_id', $userID)
            ->update(['last_ping_at' => now()]);

        if(!$updated){
            // Check if anyone else has access
            $modal = ModalAccess::where('model_type', $modelType)
                ->where('model_id', $modelId)
                ->first();

            if($modal && $modal->user_id != $userID){
                // Someone else has active access
                $otherUser = User::find($modal->user_id);
                return response()->json([
                    'status' => false,
                    'message' => $otherUser->first_name . ' ' . $otherUser->last_name . ' is already editing this modal',
                ]);
            } else if($modal && $modal->user_id == $userID){
                // Current user had access but it expired, reassign
                ModalAccess::updateOrCreate(
                    ['model_type' => $modelType, 'model_id' => $modelId],
                    ['user_id' => $userID, 'last_ping_at' => now()]
                );
                return response()->json([
                    'status' => true,
                    'message' => 'Access reassigned to you',
                ]);
            } else {
                // No one has access, assign to current user
                ModalAccess::create([
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'user_id' => $userID,
                    'last_ping_at' => now()
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Access granted to you',
                ]);
            }
        }
        return response()->json(['status' => true]);
    }

    public function releaseModal($modelType, $modelId){
        // user id 
        $userID = 0;
        $isAPIREQ = false;
        try {
            $userID = JWTAuth::parseToken()->authenticate()->id;
            $isAPIREQ = true;
        } catch (\Exception $e) {
            $userID = auth()->id();
        }
        // remove for others
        ModalAccess::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->delete();
        // set for u
        ModalAccess::updateOrCreate(
            ['model_type' => $modelType, 'model_id' => $modelId, 'session_id' => session()->getId()??'StateLessSessionFound'],
            ['user_id' => $userID, 'last_ping_at' => now()]
        );
        return response()->json(['released' => true]);
    }

     public function getBypassModal($id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'access_shipping', 'so.view_all', 'so.view_own'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $business_id = request()->session()->get('user.business_id');

        $order = Transaction::with([
            'sell_lines.product',
            'sell_lines.variations.variation_location_details' => function($q) use ($business_id) {
                $q->where('location_id', function($subquery) use ($business_id) {
                    $subquery->select('id')
                        ->from('business_locations')
                        ->where('business_id', $business_id)
                        ->limit(1);
                });
            },
            'sell_lines.variations.media',
            'contact'
        ])
        ->where('business_id', $business_id)
        ->where('id', $id)
        ->where('status', 'ordered')
        ->whereNull('picking_status')
        ->first();

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found or not in Pending status.']);
        }

        return view('order_fulfillment.partials.bypass_modal', compact('order'));
    }

        /**
     * Process bypass with partial fulfillment
     * Allows admin to specify fulfillable quantities per product line
     */
    public function bypassOrderPartial(Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'access_shipping', 'so.view_all', 'so.view_own'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $business_id = request()->session()->get('user.business_id');
        $orderId = $request->input('order_id');
        $quantities = $request->input('quantities', []);

        if (!$orderId) {
            return response()->json(['status' => false, 'message' => 'No order ID provided']);
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::with('sell_lines')
                ->where('business_id', $business_id)
                ->where('id', $orderId)
                ->where('status', 'ordered')
                ->whereNull('picking_status')
                ->first();

            if (!$transaction) {
                return response()->json(['status' => false, 'message' => 'Order not found or not in Pending status.']);
            }

            $totalOrdered = 0;
            $totalFulfilled = 0;
            $totalShorted = 0;

            // Process each sell line with specified quantities
            foreach ($transaction->sell_lines as $sellLine) {
                $lineId = $sellLine->id;
                $fulfillableQty = isset($quantities[$lineId]) ? max(0, intval($quantities[$lineId])) : 0;
                $orderedQty = $sellLine->quantity;
                
                // Cap fulfillable qty at ordered qty
                $fulfillableQty = min($fulfillableQty, $orderedQty);
                
                $totalOrdered += $orderedQty;
                $totalFulfilled += $fulfillableQty;
                $totalShorted += ($orderedQty - $fulfillableQty);

                // Deduct stock only for fulfilled quantity
                if ($fulfillableQty > 0) {
                    $variation = Variation::with('variation_location_details')
                        ->where('id', $sellLine->variation_id)
                        ->first();

                    if ($variation) {
                        $location = $variation->variation_location_details->first();
                        if ($location) {
                            // Deduct only the fulfilled quantity from stock
                            $location->qty_available -= $fulfillableQty;
                            $location->save();
                        }
                    }
                }

                // Update sell line - use fulfilled quantity as picked/verified
                $sellLine->picked_quantity = $fulfillableQty;
                $sellLine->verified_qty = $fulfillableQty;
                $sellLine->ordered_quantity = $orderedQty;
                
                // Mark as picked/shorted based on fulfillment
                if ($fulfillableQty < $orderedQty) {
                    $sellLine->is_picked = 1; // Mark as short (partially picked)
                } else {
                    $sellLine->is_picked = 1; // Fully picked
                }
                $sellLine->isVerified = 1;
                $sellLine->save();
            }

            // Update transaction status - bypass to PICKED (Packing tab)
            $transaction->picking_status = 'PICKED';
            $transaction->isPicked = 1;
            $transaction->isVerified = 1;
            $transaction->pickerID = request()->session()->get('user.id');
            $transaction->verifierID = request()->session()->get('user.id');
            $transaction->picking_started_at = now();
            $transaction->picking_ended_at = now();
            $transaction->save();

            // Create tracking status
            try {
                \App\Models\OrderTrackingStatus::updateOrCreate(
                    [
                        'transaction_id' => $transaction->id,
                        'status' => 'packed',
                    ],
                    [
                        'status_date' => now(),
                    ]
                );
                
                // Send email notification
                $contact = Contact::find($transaction->contact_id);
                if ($contact && !empty($contact->email)) {
                    $notificationUtil = new NotificationUtil();
                    $notificationUtil->autoSendNotification(
                        $business_id,
                        'order_packed',
                        $transaction,
                        $contact
                    );
                }
                
                // Send Firebase push notification for PICKED status
                if ($contact) {
                    $this->notificationUtil->sendPushNotification(
                        'Order Picked',
                        'Your order #' . $transaction->invoice_no . ' has been picked and is being prepared for packing.',
                        $transaction->contact_id,
                        [
                            'order_id' => $transaction->id,
                            'invoice_no' => $transaction->invoice_no,
                            'status' => 'picked',
                            'type' => 'order_status_update'
                        ],
                        'non_urgent'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send notification in bypassOrderPartial', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();

            $message = "Order #{$transaction->invoice_no} bypassed successfully. ";
            if ($totalShorted > 0) {
                $message .= "Fulfilled: {$totalFulfilled}/{$totalOrdered} items. Shorted: {$totalShorted} items.";
            } else {
                $message .= "All {$totalFulfilled} items fulfilled.";
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'stats' => [
                    'total_ordered' => $totalOrdered,
                    'total_fulfilled' => $totalFulfilled,
                    'total_shorted' => $totalShorted
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bypassOrderPartial', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }




    public function index(){

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $picker = User::where('business_id', $business_id)
            // ->whereHas('roles', function ($query) {
            //     $query->where('name', 'Picker#1'); 
            // })
            ->pluck('username', 'id')->toArray();
        
        // Get order counts for badges
        $order_counts = $this->getOrderCounts($business_id);
        
        return view('order_fulfillment.index', [
            'picker' => $picker, 
            'is_woocommerce' => $is_woocommerce,
            'order_counts' => $order_counts
        ]);
    }

    /**
     * WooCommerce Orders view
     * Shows WooCommerce-specific orders in the order fulfillment interface
     */
    public function woocommerceOrders()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        
        if (!$is_woocommerce) {
            abort(404, 'WooCommerce module is not installed.');
        }
        
        $picker = User::where('business_id', $business_id)
            ->pluck('username', 'id')->toArray();
        
        // Get order counts for badges
        $order_counts = $this->getOrderCounts($business_id);
        
        return view('order_fulfillment.index', [
            'picker' => $picker, 
            'is_woocommerce' => $is_woocommerce,
            'order_counts' => $order_counts,
            'woocommerce_view' => true
        ]);
    }

    /**
     * Apply common location/permission filters to a sells query (for order counts).
     */
    private function applyOrderCountFilters($query, $sale_type = 'sales_order')
    {
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
        if (! auth()->user()->can('direct_sell.view')) {
            $query->where(function ($q) {
                if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                    $q->where('transactions.created_by', request()->session()->get('user.id'));
                }
                if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                    $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                }
            });
        }
        if ($sale_type == 'sales_order') {
            if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                $query->where('transactions.created_by', request()->session()->get('user.id'));
            }
        }
        return $query;
    }

    /**
     * Get order counts for each status tab
     */
    private function getOrderCounts($business_id)
    {
        $sale_type = 'sales_order';
        $base_query = $this->transactionUtil->getListSells($business_id, $sale_type, true);
        $base_query = $base_query->where('transactions.status', 'ordered');
        $base_query = $base_query->whereNull('transactions.shipping_status');
        $this->applyOrderCountFilters($base_query, $sale_type);

        // Exclude WooCommerce vendor / dropship orders (match pendingOrdersData)
        $base_query->where('transactions.type', '!=', 'wp_sales_order');
        $base_query->where(function ($q) {
            $q->whereNull('transactions.woocommerce_order_id')
                ->orWhere('transactions.woocommerce_order_id', 0)
                ->orWhere('transactions.woocommerce_order_id', '')
                ->orWhere('transactions.woocommerce_order_id', null);
        });
        $base_query->where(function ($q) {
            $q->whereNull('transactions.source')
                ->orWhere('transactions.source', '=', '')
                ->orWhere('transactions.source', 'not like', 'woo%');
        });
        $base_query->where(function ($q) {
            $q->whereNull('transactions.is_created_from_api')
                ->orWhere('transactions.is_created_from_api', 0);
        });

        // Preprocessing: not yet preprocessed, no children, parent only
        $preprocessing_query = (clone $base_query)
            ->whereNull('transactions.picking_status')
            ->where('transactions.type', 'sales_order')
            ->where(function ($q) {
                $q->whereNull('transactions.is_preprocessed')->orWhere('transactions.is_preprocessed', false);
            })
            ->where(function ($q) {
                $q->whereNull('transactions.sales_order_ids')
                    ->orWhere(DB::raw('JSON_LENGTH(COALESCE(transactions.sales_order_ids, "[]"))'), '=', 0);
            })
            ->whereNull('transactions.transfer_parent_id')
            ->groupBy('transactions.id');
        $preprocessing_count = $preprocessing_query->get()->count();

        // Pending: preprocessed or has children (same as Pending tab)
        $pending_query = (clone $base_query)
            ->whereNull('transactions.picking_status')
            ->where(function ($q) {
                $q->where('transactions.is_preprocessed', true)
                    ->orWhere('transactions.type', 'erp_sales_order')
                    ->orWhere(DB::raw('JSON_LENGTH(COALESCE(transactions.sales_order_ids, "[]"))'), '>', 0);
            })
            ->groupBy('transactions.id');
        $pending_count = $pending_query->get()->count();

        // Processing: picking_status is 'PICKING'
        $processing_query = (clone $base_query)
            ->where('transactions.picking_status', 'PICKING')
            ->groupBy('transactions.id');
        $processing_count = $processing_query->get()->count();

        // Packing: picking_status is 'PICKED'
        $packing_query = (clone $base_query)
            ->where('transactions.picking_status', 'PICKED')
            ->groupBy('transactions.id');
        $packing_count = $packing_query->get()->count();

        // Cancelled: status cancelled, shipping_status null, picking_status null
        $cancel_query = $this->transactionUtil->getListSells($business_id, $sale_type, true);
        $cancel_query->where('transactions.status', 'cancelled');
        $cancel_query->whereNull('transactions.shipping_status');
        $cancel_query->whereNull('transactions.picking_status');
        $this->applyOrderCountFilters($cancel_query, $sale_type);
        $cancel_query->groupBy('transactions.id');
        $cancelled_count = $cancel_query->get()->count();

        // Completed: status completed, picking_status INVOICED
        $complete_query = $this->transactionUtil->getListSells($business_id, $sale_type, true);
        $complete_query->where('transactions.status', 'completed');
        $complete_query->where('transactions.picking_status', 'INVOICED');
        $this->applyOrderCountFilters($complete_query, $sale_type);
        $complete_query->groupBy('transactions.id');
        $completed_count = $complete_query->get()->count();

        return [
            'preprocessing' => $preprocessing_count,
            'pending' => $pending_count,
            'processing' => $processing_count,
            'packing' => $packing_count,
            'cancelled' => $cancelled_count,
            'completed' => $completed_count,
        ];
    }

    /**
     * Get order counts via AJAX for real-time updates
     */
    public function getOrderCountsAjax()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        
        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $business_id = request()->session()->get('user.business_id');
        $order_counts = $this->getOrderCounts($business_id);
        
        return response()->json($order_counts);
    }
    public function sendShipmentNotification(Request $request, $id)
    {
        $transaction = Transaction::with('contact')->where('id', $id)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        } 
        $business_id = $request->session()->get('user.business_id');   
        
        // Check if shipping_status needs to be updated to 'shipped'
        $old_shipping_status = $transaction->shipping_status;
        if ($transaction->shipping_status !== 'shipped') {
            $transaction->shipping_status = 'shipped';
            $transaction->save();
            
            Log::info('Shipping status updated to shipped in sendShipmentNotification', [
                'transaction_id' => $transaction->id,
                'previous_status' => $old_shipping_status,
                'business_id' => $business_id
            ]);
        }
        
        // Create tracking status for shipped
        try {
            \App\Models\OrderTrackingStatus::updateOrCreate(
                [
                    'transaction_id' => $transaction->id,
                    'status' => 'shipped',
                ],
                [
                    'status_date' => now(),
                    'updated_by' => auth()->user()->id ?? null,
                ]
            );
            Log::info('Tracking status created: shipped (from sendShipmentNotification)', [
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $trackingError) {
            Log::error('Failed to create shipped tracking status (from sendShipmentNotification)', [
                'transaction_id' => $transaction->id,
                'error' => $trackingError->getMessage()
            ]);
            // Don't fail the whole operation if tracking fails
        }

        // Send notification to customer when order is shipped
        try {
            Log::info('Attempting to send order shipped notification', [
                'transaction_id' => $transaction->id,
                'contact_id' => $transaction->contact_id,
                'business_id' => $business_id
            ]);
            
            $contact = $transaction->contact;
            if ($contact && !empty($contact->email)) {
                Log::info('Contact found for order shipped notification', [
                    'transaction_id' => $transaction->id,
                    'contact_id' => $contact->id,
                    'contact_email' => $contact->email,
                    'contact_mobile' => $contact->mobile ?? 'no mobile'
                ]);
                
                // Use NotificationUtil directly (synchronous) like packed status
                $notificationUtil = new NotificationUtil();
                $notificationUtil->autoSendNotification(
                    $business_id,
                    'order_shipped',
                    $transaction,
                    $contact
                );
                
                Log::info('Order shipped notification sent successfully', [
                    'transaction_id' => $transaction->id,
                    'contact_id' => $contact->id,
                    'business_id' => $business_id
                ]);
            } else {
                Log::warning('Contact not found or no email for order shipped notification', [
                    'transaction_id' => $transaction->id,
                    'contact_id' => $transaction->contact_id,
                    'has_email' => !empty($contact->email ?? null)
                ]);
            }
        } catch (\Exception $notificationError) {
            Log::error('Failed to send order shipped notification', [
                'transaction_id' => $transaction->id,
                'contact_id' => $transaction->contact_id ?? null,
                'error' => $notificationError->getMessage(),
                'file' => $notificationError->getFile(),
                'line' => $notificationError->getLine(),
                'trace' => $notificationError->getTraceAsString()
            ]);
            // Don't fail the whole operation if notification fails
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification sent successfully.'
        ]);
    }
    public function historyData(Request $request){
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = 'sales_order';

            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'ordered');
            $sells = $sells->whereNotIn('transactions.picking_status', ['PICKING', 'PICKED','PACKED']);
            $sells = $sells->where('transactions.shipping_status', null); 
            $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            // $sells->where('transactions.picking_status', 'PICKED');
            $sells->groupBy('transactions.id');
            // $sells->orderBy('transactions.id', 'DESC');
            $sells->with(['picker','verifier','sell_lines' => function ($q) {
                $q->select('transaction_id', 'picked_quantity', 'unit_price_inc_tax');
            }]);
            

            return DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only')) {
                            $html .= '<a href="#" data-href="' . action([\App\Http\Controllers\SellController::class, 'manualPack'], [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-shipping-fast" aria-hidden="true"></i> ' . 'Make Shipment' . '</a>';
                        }
                        return $html;
                    }
                )
                ->editColumn('invoice_no', function ($row) {
                    return '<a href="javascript:void(0)" class="view-invoice" data-id="' . $row->id . '">' . $row->invoice_no . '</a>';
                })
                ->addColumn('merged_column', function ($data) {
                    return '<a href="'.action([\App\Http\Controllers\ContactController::class, 'show'], [$data->id]).'?type=customer" target="_blank" class="view-customer">'.$data->name . ' ' . $data->supplier_business_name.'</a>';
                })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('final_total', function ($row) {
                    return '$ ' . number_format($row->final_total, 2);
                })
                ->editColumn('total_paid', function ($row) {
                    return '$ ' . number_format($row->total_paid, 2);
                })
                ->addColumn('total_picked_qty', function ($row) {
                    $total_qty = $row->sell_lines->sum('picked_quantity');
                    return '<span>' . $total_qty . '</span>';
                })
                ->addColumn('picker_details', function ($row) {
                    $picker = $row->picker;
                    $maleImage = asset('img/man.png');  // Path to the man image
                    $femaleImage = asset('img/woman.png');  // Path to the woman
                    if ($picker) {

                        $genderIcon = $picker->gender == 'female' ?'<img src="' . $femaleImage . '" alt="Female" style="width: 25px; height: 25px; border-radius: 50%;">':'<img src="' . $maleImage . '" alt="Male" style="width: 25px; height: 25px; border-radius: 50%;">'; 
                    
                        // $genderIcon = $picker->gender == 'female' ? '<i class="fas fa-user-circle"></i>' :'<i class="fa fa-user-tie"></i>' ;
                        $userDetails = 
                        "Username: {$picker->username} |  Email: {$picker->email} | Full Name: {$picker->first_name} {$picker->last_name}";
                        return '<span  class="picker-gender tw-flex" data-toggle="tooltip" data-html="true" title="' . $userDetails . '">' . $genderIcon .' &nbsp;'. $picker->first_name . '</span>';
                    } else {
                        return '<i class="fas fa-question"></i>';
                    }
                })
                ->addColumn('verifier_details', function ($row) {
                    $verifier = $row->verifier;
                    if ($verifier) {
                        $id = $verifier->id;
                        $maleImage = asset('img/man.png');  // Path to the man image
                        $femaleImage = asset('img/woman.png');  // Path to the woman

                        $genderIcon = $verifier->gender == 'female' ?'<img src="' . $femaleImage . '" alt="Female" style="width: 25px; height: 25px; border-radius: 50%;">':'<img src="' . $maleImage . '" alt="Male" style="width: 25px; height: 25px; border-radius: 50%;">'; 
                    
                        // $genderIcon = $verifier->gender == 'female' ? '<i class="fas fa-user-circle"></i>' :'<i class="fa fa-user-tie"></i>' ;
                        $userDetails = 
                        "Username: {$verifier->username} |  Email: {$verifier->email} | Full Name: {$verifier->first_name} {$verifier->last_name}";
                        return '<a href="/users/'.$id.' target="_blank"><span  class="verifier-gender tw-flex" data-toggle="tooltip" data-html="true" title="' . $userDetails . '">' . $genderIcon .' &nbsp;'. $verifier->first_name . '</span></a>';
                    } else {
                        return '<i class="fas fa-ban text-center"></i>';
                    }
                })
                ->filterColumn('picker_details', function ($query, $keyword) {
                    $query->whereHas('picker', function ($q) use ($keyword) {
                        $q->where('username', 'like', "%{$keyword}%")
                          ->orWhere('first_name', 'like', "%{$keyword}%")
                          ->orWhere('last_name', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                  ->filterColumn('verifier_details', function ($query, $keyword) {
                    $query->whereHas('verifier', function ($q) use ($keyword) {
                        $q->where('username', 'like', "%{$keyword}%")
                          ->orWhere('first_name', 'like', "%{$keyword}%")
                          ->orWhere('last_name', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('picked_qty_amount', function ($row) {
                    $amount = $row->sell_lines->sum(function ($line) {
                        return $line->picked_quantity * $line->unit_price_inc_tax;
                    });
                    return '<span>' . number_format($amount, 2) . '</span>';
                })
                
                ->addColumn('bulk_select', function ($row) {
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                  $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                  $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                  return $status;
              })
              ->editColumn('picking_status', function ($row)  {
                if($row->picking_status == 'INVOICED'){
                 $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-green' . '">' . $row->picking_status . '</span></a>';
                } else if($row->picking_status == 'PICKING'){
                  $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-yellow' . '">' . $row->picking_status . '</span></a>';
                } else if($row->picking_status == 'PICKED'){
                  $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-blue' . '">' . $row->picking_status . '</span></a>';
                } else{
                  $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-red' . '">' . $row->picking_status . '</span></a>';
                }
                return $status;
            })
            ->addColumn('picking_time', function ($row) {
                $start_time = $row->picking_started_at;
                $end_time = $row->picking_ended_at;
            
                if (!$start_time || !$end_time) {
                    return 'N/A';
                }
            
                $start = \Carbon\Carbon::parse($start_time);
                $end = \Carbon\Carbon::parse($end_time);
            
                $durationInSeconds = $start->diffInSeconds($end);
            
                $hours = floor($durationInSeconds / 3600);
                $minutes = floor(($durationInSeconds % 3600) / 60);
                $seconds = $durationInSeconds % 60;
            
                $parts = [];
                if ($hours > 0) $parts[] = "$hours hr";
                if ($minutes > 0) $parts[] = "$minutes min";
                if ($seconds > 0 || empty($parts)) $parts[] = "$seconds sec";
            
                    return implode(' ', $parts);
                })
        
              ->rawColumns(['status','invoice_no','merged_column', 'picking_time', 'action', 'bulk_select','picking_status','payment_status','picker_details','verifier_details','picked_qty_amount','total_picked_qty'])
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                // ->rawColumns([0, 4])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
    }
    public function history(Request $request){
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (! $is_admin && ! auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('order_fulfillment.partials.history');
    }
    public function picker(Request $request){
        $activity = PickersActivity::where('user_id', auth()->user()->id)->first();
        return view('order_fulfillment.picker', compact('activity'));
    }
    public function loggingActive(Request $request, $status){
        $transaction = Transaction::where('pickerID', auth()->user()->id)->where('isPicked', false)->get();
        if($transaction->count() > 0){
            $activity = PickersActivity::updateOrCreate(
                ['user_id' => auth()->user()->id],
                [
                    'is_active' => $status=='true' ? true : false,
                ]
            );
        }else{
            $activity = PickersActivity::updateOrCreate(
                ['user_id' => auth()->user()->id],
                [
                    'is_active' => $status=='true' ? true : false,
                    'current_status' => null,
                ]
            );
        }
        
        $verifierActivity = VerifierActivity::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'is_active' => $status=='true' ? true : false,
            ]
        );
        $message = $activity->wasRecentlyCreated ? 'Logging activity created' : 'Logging active status updated';
        return response()->json(['status' => true, 'message' => $message, 'data' => [
            'is_active' => $activity->is_active,
            'is_active_verifier' => $verifierActivity->is_active
        ]]);
    }
    // picker man api
    public function pickerManOrder(Request $request){
        $isVerifier = request()->query('type') == 'verifier' ? true : false;
        $is_api =false;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api =true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $is_admin = $this->businessUtil->is_admin($staff);
        if (! $is_admin && ! $staff->hasAnyPermission(['pickerman'])) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $staff->business_id;
        if (request()->ajax() || $is_api) {
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $sale_type = 'sales_order';
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type, true);
            $sells = $sells->where('transactions.status', 'ordered');
            $sells = $sells->where('transactions.shipping_status', null); 
            
            $date_range = request()->input('sell_list_filter_date_range');
            if (!empty($date_range)) {
                $dates = explode(' ~ ', $date_range);
                if (count($dates) == 2) {
                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $sells->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
                }
            }
            $permitted_locations = $staff->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (! $staff->can('direct_sell.view')) {
                $sells->where(function ($q) use ($staff) {
                    if ($staff->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', $staff->id);
                    }
                    if ($staff->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', $staff->id);
                    }
                });
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if ($sale_type == 'sales_order') {
                if (! $staff->can('so.view_all') && $staff->can('so.view_own')) {
                    $sells->where('transactions.created_by', $staff->id);
                }
            }

            $sells->where('transactions.picking_status', 'PICKING');
            if($isVerifier){
                $sells->where(function($query) use ($staff) {
                    $query->where('transactions.verifierID', $staff->id)
                          ->orWhereNull('transactions.verifierID');
                })
                ->where('transactions.isVerified', false)
                ->where('transactions.isPicked', true);
            } else {
                $sells->where('transactions.pickerID', $staff->id);
                $sells->where('transactions.isPicked', false);
            }
            $sells->groupBy('transactions.id');
            // $sells->orderBy('transactions.id', 'DESC');
            // $sells->with('picker');
            

            if($is_api){
                $sells = $sells->with(['picker', 'sell_lines' => function ($q) {
                    $q->select('transaction_id', 'picked_quantity', 'ordered_quantity', 'unit_price_inc_tax');
                }])->get();
                // changed by hyder for coming status true and getting empty array
                if($sells->isEmpty()){
                    return response()->json([
                        'status' => true,
                        'message' => 'No picking order found',
                        'data' => []
                    ]);
                }

                $sells = $sells->map(function ($sell) {
                    $total_picked = $sell->sell_lines->sum('picked_quantity');
                    $total_ordered = $sell->sell_lines->sum('ordered_quantity');
                    $percentage = $total_ordered > 0 ? ($total_picked / $total_ordered) * 100 : 0;
                    $sell->fulfilledPercentage = round($percentage, 2);
                    $sell->total_picked_qty = $total_picked;
                    $sell->total_ordered_qty = $total_ordered;
                    return $sell;
                });

                return response()->json([
                    'status' => true,
                    'message' => 'Picking orders found',
                    'data' => $sells
                ]);
            }
            $sells->with(['picker','sell_lines' => function ($q) {
                $q->select('transaction_id', 'picked_quantity', 'unit_price_inc_tax');
            }]);
            return DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '';
                        if (auth()->user()->can('sell.view')  || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only') || auth()->user()->can('pickerman')) {
                            $html .= '<a href="' . url('sells-picking/' . $row->id) . '" ><i class="fas fa-dolly" aria-hidden="true"></i> ' . 'Pick Qty' . '</a>';
                        }
                        // return '<div class="btn-group">
                        //             <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max dropdown-toggle" 
                        //                 data-toggle="dropdown" aria-expanded="false">' .
                        //     __('messages.actions') .
                        //     '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        //                 </span>
                        //             </button>
                        //             <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        //             ' . $html . '
                        //             </ul>
                        //             </div>';
                        return $html;
                    }
                )
                ->addColumn('merged_column', function ($data) {
                    return $data->name . ' ' . $data->supplier_business_name;
                })
                ->filterColumn('merged_column', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                          ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('total_picked_qty', function ($row) {
                    $total_picked = $row->sell_lines->sum('picked_quantity');
                    $total_ordered = $row->sell_lines->sum('ordered_quantity'); // Assuming 'quantity' is ordered amount
                
                    // Avoid division by zero
                    $percentage = $total_ordered > 0 ? ($total_picked / $total_ordered) * 100 : 0;
                
                    return '<span>' . $total_picked . ' (' . number_format($percentage, 2) . '%)</span>';
                })
                ->addColumn('picked_qty_amount', function ($row) {
                    $amount = $row->sell_lines->sum(function ($line) {
                        return $line->picked_quantity * $line->unit_price_inc_tax;
                    });
                    return '<span>' . number_format($amount, 2) . '</span>';
                })
                
                // ->editColumn('value', '{{@num_format($value)}}')
                ->addColumn('bulk_select', function ($row) {
                    $value = DB::table('transaction_sell_lines')->select()->where('transaction_id', $row->id);
                    return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                })
                ->addColumn('picker_details', function ($row) {
                    $picker = $row->picker;
                    $maleImage = asset('img/man.png');  // Path to the man image
                    $femaleImage = asset('img/woman.png');  // Path to the woman
                    if ($picker) {

                        $genderIcon = $picker->gender == 'female' ?'<img src="' . $femaleImage . '" alt="Female" style="width: 25px; height: 25px; border-radius: 50%;">':'<img src="' . $maleImage . '" alt="Male" style="width: 25px; height: 25px; border-radius: 50%;">'; 
                    
                        // $genderIcon = $picker->gender == 'female' ? '<i class="fas fa-user-circle"></i>' :'<i class="fa fa-user-tie"></i>' ;
                        $userDetails = 
                        "Username: {$picker->username} |  Email: {$picker->email} | Full Name: {$picker->first_name} {$picker->last_name}";
                        return '<span  class="picker-gender tw-flex" data-toggle="tooltip" data-html="true" title="' . $userDetails . '">' . $genderIcon .' &nbsp;'. $picker->first_name . '</span>';
                    } else {
                        return '<i class="fas fa-question"></i>';
                    }
                })
                
                ->editColumn('payment_status', function ($row) {
                  if($row->total_paid){
                    $diff = $row->final_total-$row->total_paid;
                    if($diff==0){
                      return '<a href="#" class="label bg-green" value="' ."Paid". '">Paid </a>';
                    } elseif($diff==$row->final_total){
                      return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                    } else if($diff<=$row->final_total){
                      return '<a href="#" class="label bg-info" value="' ."Partial". '">Partial</a>';
                    }
                    return '<a href="#" class="label bg-yellow" value="' ."Due". '">Due</a>';
                  } else{
                    return '<a href="#" class="label bg-yellow" value="' ."due". '">Due</a>';
                  }
                })
                ->editColumn('status', function ($row) use ($shipping_statuses) {
                  $status_color = ! empty($this->shipping_status_colors[$row->status]) ? $this->shipping_status_colors[$row->status] : 'bg-gray';
                  $status = ! empty($row->status) ? '<a href="#" class="btn-modal" data-href=""><span class="label ' . $status_color . '">' . $shipping_statuses[$row->status] . '</span></a>' : '';
                  return $status;
              })
                ->editColumn('picking_status', function ($row)  {
                  // $status_color = ! empty($this->picking_status_colors[$row->picking_status]) ? $this->picking_status_colors[$row->picking_status] : 'bg-gray';
                  $status =  '<a href="#" class="btn-modal" data-href=""><span class="label ' . 'bg-info' . '">' . $row->picking_status . '</span></a>';
                  return $status;
              })
              ->filterColumn('picker_details', function ($query, $keyword) {
                $query->whereHas('picker', function ($q) use ($keyword) {
                    $q->where('username', 'like', "%{$keyword}%")
                      ->orWhere('first_name', 'like', "%{$keyword}%")
                      ->orWhere('last_name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            
                // ->addColumn('completion_status', function ($row) {
                //     return '<input type="checkbox" class="order-checkbox" value="' . $row->id . '">';
                // })
                // ->addColumn('picking_status',function ($row) {
                //     return $row->picking_status;
                // })
                ->rawColumns(['status', 'action', 'bulk_select','picking_status','payment_status','picker_details','picked_qty_amount','total_picked_qty'])
                ->removeColumn(['custom_field_4', 'custom_field_3', 'custom_field_2', 'custom_field_1', 'shipping_custom_field_5', 'shipping_custom_field_4', 'shipping_custom_field_3', 'shipping_custom_field_2', 'service_custom_field_1', 'waiter', 'table_name', 'so_qty_remaining'])
                // ->rawColumns([0, 4])
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('sell.view') || auth()->user()->can('view_own_sell_only')) {
                            return  action([\App\Http\Controllers\SellController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    },
                ])
                ->make(true);
        }
        return response()->json([
            'status' => true,
            'message' => 'Picking no found',
            'data' => []
        ]);
    }
    // deprecated-> update picking status not in used we using store end time 
    public function updatePickingStatus(Request $request){
        $is_api =false;
        $isVerifier = request()->query('type') == 'verifier' ? true : false;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api =true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $transaction = Transaction::with(['sell_lines' => function ($query) {
                $query->where('isVerified', false);
            }])
            ->where('id', $request->transaction_id)
            ->when($isVerifier, function ($query) use ($staff) {
                $query->where('verifierID', $staff->id);
            }, function ($query) use ($staff) {
                $query->where('pickerID', $staff->id);
            })
            ->first();

        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found or you do not have permission'
            ]);
        }

        if ($isVerifier) {
            $isMakeVerified = true;
            $failed_sell_lines = [];
            foreach ($transaction->sell_lines as $sellLine) {
                // shorted direct verified
                if($sellLine->is_picked == 1 && $sellLine->shorted_picked_qty > 0){
                    $sellLine->isVerified = true;
                    $sellLine->save();
                // unshorted verified
                } else if ($sellLine->verified_qty > $sellLine->ordered_quantity) {
                    $failed_sell_lines[] = $sellLine;
                } else {
                    $sellLine->isVerified = true;
                    $sellLine->save();
                }
            }
            if(count($failed_sell_lines) > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'Picking order is not verified',
                    'data' => $failed_sell_lines
                ]);
            }
            $transaction->isVerified = $isMakeVerified;
        } else {
            $transaction->isPicked = true;
        }
        
        $transaction->save();
        return response()->json([
            'status' => true,
            'message' => 'Picking status updated successfully'
        ]);
    }
    // verify picking order by verifier that not assigned to any verifier
    public function verifyPicking(Request $request){
        $is_api =false;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api =true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('isPicked', true)
            ->where('isVerified', false)
            ->where('picking_status', 'PICKING')
            ->whereNull('verifierID')
            ->first();
        // changed by hyder for coming status true and getting empty array
        if(!$transaction){
            return response()->json([
                'status' => true,
                'message' => 'No picking order found',
                'data' => []
            ]);
        }
        // session lock 
        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $transaction->id,true);
        if($lockModal['status'] == false){
            return response()->json(['status' => false, 'message' => $lockModal['message']]);
        }
        $transaction->update(['verifierID' => $staff->id]);
        return response()->json([
            'status' => true,
            'message' => 'Now you can verify the picking order'
        ]);
    }
    public function storeStartTime(Request $request)
    {
        $is_api =false;
        try {
            try {
                $staff = JWTAuth::parseToken()->authenticate();
                $is_api =true;
            } catch (\Throwable $th) {
                $staff = auth()->user();
            }
            $transaction = Transaction::where('id', $request->transaction_id)->first();

            if (!$transaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found'
                ]);
            }
            
            if ($transaction->isPicked) {
                $transaction = Transaction::where('id', $request->transaction_id)
                    ->where('verifierID', $staff->id)
                    ->first();
                    if(!$transaction){
                        return response()->json([
                            'status' => false,
                            'message' => 'You do not have permission to start verification'
                        ]);
                    }
            } else {
                $transaction = Transaction::where('id', $request->transaction_id)
                    ->where('pickerID', $staff->id)
                    ->first();
                    if(!$transaction){
                        return response()->json([
                            'status' => false,
                            'message' => 'You do not have permission to start picking'
                        ]);
                }
            }
            
            // session lock 
            $isLockModal = false;
            $orderFulfillmentController = app(OrderfulfillmentController::class);
            $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $transaction->id,true);
            if($lockModal['status'] == false){
                return response()->json(['status' => false, 'message' => $lockModal['message']]);
            }
            if($transaction->picking_started_at == null || $transaction->pickerID != $staff->id){ 
                $transaction->update(['picking_started_at' => now()]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Picking timer already started'
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Picking start time recorded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error recording picking start time: ' . $e->getMessage()
            ], 500);
        }
    }
    public function storeEndTime(Request $request)
    {
        $is_api =false;
        $isVerifier = request()->query('type') == 'verifier' ? true : false;
        $type = $request->type;
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api =true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }   
        $transaction = Transaction::with('sell_lines')->where('id', $request->transaction_id)
            ->when($isVerifier, function ($query) use ($staff) {
                $query->where('verifierID', $staff->id);
            }, function ($query) use ($staff) {
                $query->where('pickerID', $staff->id);
            })
            ->first();
        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found or you do not have permission' 
            ]);
        }
        // session lock 
        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $transaction->id,true);
        if($lockModal['status'] == false){
            return response()->json(['status' => false, 'message' => $lockModal['message']]);
        }
        if($type == 'finish'){
            if($isVerifier){
                if($transaction->isVerified == false){
                    $isCompleteVerified = true;
                    $transaction->sell_lines()->each(function ($sellLine) use (&$isCompleteVerified) {
                        if($sellLine->picked_quantity != $sellLine->verified_qty){
                            if($sellLine->isVerified == false){
                                $isCompleteVerified = false;
                            }
                        }
                    });
                    if($isCompleteVerified){
                        $transaction->update(['isVerified' => true]);
                        return response()->json([
                            'status' => true,
                            'message' => 'Order verified successfully'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Please verify the quantity'
                        ]);
                    }
                }
            }

            if($transaction->isPicked == false){
                foreach($transaction->sell_lines as $sellLine){
                    if($sellLine->picked_quantity != $sellLine->ordered_quantity){
                        if($sellLine->is_picked == false){
                            return response()->json([
                                'status' => false,
                                'message' => 'Please pick completely or short the quantity'
                            ]);
                        }
                    }
                }
            }
            $transaction->update(['picking_ended_at' => now(),'isPicked' => true]);
            $transaction_pickerid = $transaction->pickerID;
            // update verifier activity start
            $activeVerifier = VerifierActivity::where('is_active', true)->whereNot('user_id', $transaction_pickerid)
            ->orderByRaw('COALESCE(last_assigned, updated_at) ASC')
            ->first();

            if($activeVerifier){
                $activeVerifier->last_assigned = now();
                $activeVerifier->save();

                $transaction->update(['verifierID' => $activeVerifier->user_id]);
            }else{
                $verifierActivity = VerifierActivity::where('user_id', $transaction_pickerid)->first();
                $verifierActivity->last_assigned = now();
                $verifierActivity->save();
                $transaction->update(['verifierID' => $transaction_pickerid]);
            }

            try{
                $moduleStatus = request()->session()->get('business.manage_order_module');
            } catch (\Throwable $th) {
                $bid = $staff->business_id;
                $moduleStatus = Business::where('id',$bid)->first()->manage_order_module;
            }
            if($moduleStatus == 'manual'){
                $transaction->update(['picking_status' => 'PICKED']);
                $transaction->sell_lines()->each(function ($sellLine) {
                    $sellLine->update(['is_picked' => true]);
                    $sellLine->update(['isVerified' => true]);
                    $sellLine->update(['verified_qty' => $sellLine->picked_quantity]);
                });
                
                // Create tracking status and send notification
                try {
                    \App\Models\OrderTrackingStatus::updateOrCreate(
                        [
                            'transaction_id' => $transaction->id,
                            'status' => 'packed',
                        ],
                        [
                            'status_date' => now(),
                        ]
                    );
                    
                    $contact = Contact::find($transaction->contact_id);
                    if ($contact && !empty($contact->email)) {
                        $notificationUtil = new NotificationUtil();
                        $notificationUtil->autoSendNotification(
                            $staff->business_id,
                            'order_packed',
                            $transaction,
                            $contact
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send order packed notification in storeEndTime', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {

                $transaction->sell_lines()->update(['is_picked' => true]);
            }

            // update picker activity start
            $transactions = Transaction::where('pickerID', $staff->id)
            ->where('isPicked', false)->get();

            // Check if there are any remaining transactions for this picker
            if ($transactions->count() == 0) {
                // No more transactions available for this picker
                $pickerActivity = PickersActivity::where('user_id', $staff->id)->first();
                $pickerActivity->current_status = null;
                $pickerActivity->save();
            }
            // update picker activity end

            return response()->json([
                'status' => true,
                'message' => 'Order completely picked'
            ]);
        }
        $transaction->update(['picking_ended_at' => now()]);
        if($is_api){
            return response()->json([
                'status' => true,
                'message' => 'Picking end time recorded successfully'
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Picking end time recorded successfully'
        ]);
    }
    public function updatePriorities(Request $request){
        $is_admin = $this->businessUtil->is_admin(auth()->user());
        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.create','sell.update'])) {
            abort(403, 'Unauthorized action.');
        }

        if (!$request->has('ids') || !is_array($request->ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No transactions selected'
            ]);
        }

        $transactions = Transaction::whereIn('id', $request->ids);

        if ($request->priority === 'increase') {
            $transactions->increment('priority');
        } else if ($request->priority === 'decrease') {
            $transactions->decrement('priority'); 
        }

        return response()->json([
            'status' => true,
            'message' => 'Priority updated successfully'
        ]);
    } 
    public function revert(Request $request) {
        $is_api =false;
         $isVerifier = request()->query('type') == 'verifier' ? true : false;
         $colName='pickerID';
         if($isVerifier){
            $colName='verifierID';
         }
        $sellLineID = $request->input('line_id');
        $transactionID = $request->input('transaction_id');
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api =true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $transaction = Transaction::where('id',$transactionID)->where($colName,$staff->id)->first();
        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found or you do not have permission'
            ]);
        }
        // session lock 
        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $transaction->id,true);
        if($lockModal['status'] == false){
            return response()->json(['status' => false, 'message' => $lockModal['message']]);
        }
        if($isVerifier){
            $sellLine = TransactionSellLine::where('id',$sellLineID)->where('transaction_id',$transactionID)->first();
            $sellLine->isVerified = false;
            $sellLine->save();
        } else {
            $sellLine = TransactionSellLine::where('id',$sellLineID)->where('transaction_id',$transactionID)->first();
            $sellLine->shorted_picked_qty = null;
            $sellLine->is_picked = false;
            $sellLine->save();
        }
       

        return response()->json([
            'status' => true,
            'message' => 'Reverted Shortage',
            'updated_line' => [
                [
                    "line_id" => $sellLine->id,
                    // "qty_available" => $sellLine->qty_available,
                    "ordered_qty" => $sellLine->ordered_quantity,
                    "picked_qty" => $sellLine->picked_quantity,
                    "manual_picked_qty" => $sellLine->manual_picked_qty,
                    "barcode_picked_qty" => $sellLine->barcode_picked_qty,
                    "shorted_picked_qty" => $sellLine->shorted_picked_qty,
                    "is_picked" => $sellLine->is_picked,
                    "isVerified" => $sellLine->isVerified,
                    "verified_qty" => $sellLine->verified_qty
                ],
            ]
        ]);
    } 
    public function reset(Request $request){
        $is_api =false;
         $isVerifier = request()->query('type') == 'verifier' ? true : false;
         $colName='pickerID';
         if($isVerifier){
            $colName='verifierID';
         }
        $sellLineID = $request->input('line_id');
        $transactionID = $request->input('transaction_id');
        try {
            $staff = JWTAuth::parseToken()->authenticate();
            $is_api =true;
        } catch (\Throwable $th) {
            $staff = auth()->user();
        }
        $transaction = Transaction::where('id',$transactionID)->where($colName,$staff->id)->first();
        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found or you do not have permission'
            ]);
        }
        // session lock
        $isLockModal = false;
        $orderFulfillmentController = app(OrderfulfillmentController::class);
        $lockModal = $orderFulfillmentController->checkModalAccess('Transaction', $transaction->id,true);
        if($lockModal['status'] == false){
            return response()->json(['status' => false, 'message' => $lockModal['message']]);
        }
        $sellLine = TransactionSellLine::where('id',$sellLineID)->where('transaction_id',$transactionID)->first();
        if($sellLine){
            $variation = Variation::with('variation_location_details')->where('id',$sellLine->variation_id)->first();
            if($variation){
                $product = $variation->product;
                // Only manage stock if enable_stock is true
                if ($product && $product->enable_stock == 1) {
                    $loc = $variation->variation_location_details->first();
                    if($loc){
                        $loc->qty_available += $sellLine->picked_quantity;
                        $loc->save();
                    }
                }
            }
        }
        $sellLine->shorted_picked_qty = null;
        $sellLine->is_picked = false;
        $sellLine->picked_quantity=0;
        $sellLine->manual_picked_qty=0;
        $sellLine->barcode_picked_qty=0;
        $sellLine->save();
        return response()->json([
            'status' => true,
            'message' => 'Reset item successfully',
            'updated_line' => [
                [
                    "line_id" => $sellLine->id,
                    // "qty_available" => $sellLine->qty_available,
                    "ordered_qty" => $sellLine->ordered_quantity,
                    "picked_qty" => $sellLine->picked_quantity,
                    "manual_picked_qty" => $sellLine->manual_picked_qty,
                    "barcode_picked_qty" => $sellLine->barcode_picked_qty,
                    "shorted_picked_qty" => $sellLine->shorted_picked_qty,
                    "is_picked" => $sellLine->is_picked,
                    "isVerified" => $sellLine->isVerified,
                    "verified_qty" => $sellLine->verified_qty
                ],
            ]
        ]);
    }


    public function someXyzNotification(){
        // let's assume we have to send one notification 
        $business_id = 1;
        $notification_type = 'test_notification';
        $custom_data = null;
        $contact = Contact::where('id', 2)->first();
        $transaction = Transaction::where('id', 431)->first();
        $user = User::where('id', 2)->first();
        $notificationUtil = new NotificationUtil();
       

        // step 2 : send notification with available channels & services
        $notificationUtil->autoSendNotificationCombineService($business_id, $notification_type, $custom_data, $user);
        Log::info('Notification sent successfully');

        return response()->json([
            'status' => true,
            'message' => 'Notification sent successfully'
        ]);
    }
    public function markAsReaded($id){
        $notification = DB::table('notifications')->where('id', $id)->first();
        $notification->read_at = now();
        $notification->save();
        return response()->json([
            'status' => true,
            'message' => 'Notification marked as readed'
        ]);
    }
    public function getActivePicker(){
        // Find picker with null current_status and oldest last_assigned (or updated_at if last_assigned is null)
        $activity = PickersActivity::where('is_active', true)
            ->whereNull('current_status')
            ->orderByRaw('COALESCE(last_assigned, updated_at) ASC')
            ->first();
            
        if ($activity) {
            return response()->json([
                'status' => true, 
                'message' => 'Picker found with null status and oldest assignment',
                'picker_id' => $activity->user_id
            ]);
        } else {
            return response()->json([
                'status' => false, 
                'message' => 'No available picker found with null status'
            ]);
        }
    }
}

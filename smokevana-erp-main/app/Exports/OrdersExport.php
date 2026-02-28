<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromArray, WithHeadings, WithTitle, WithColumnWidths, WithStyles
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function array(): array
    {
        $data = [];
        
        foreach ($this->orders as $order) {
            // Main order row
            $orderRow = [
                $order['order_number'] ?? '',
                $order['transaction_date'] ?? '',
                $order['status'] ?? '',
                $order['payment_status'] ?? '',
                $order['shipping_status'] ?? 'N/A',
                $order['picking_status'] ?? 'N/A',
                isset($order['location']['name']) ? $order['location']['name'] : 'N/A',
                $order['items_summary']['total_items'] ?? 0,
                $order['items_summary']['received_items'] ?? 0,
                $order['items_summary']['pending_items'] ?? 0,
                isset($order['pricing']['subtotal']) ? number_format($order['pricing']['subtotal'], 2) : '0.00',
                isset($order['pricing']['tax_amount']) ? number_format($order['pricing']['tax_amount'], 2) : '0.00',
                isset($order['pricing']['discount_amount']) ? number_format($order['pricing']['discount_amount'], 2) : '0.00',
                isset($order['pricing']['shipping_charges']) ? number_format($order['pricing']['shipping_charges'], 2) : '0.00',
                isset($order['pricing']['final_total']) ? number_format($order['pricing']['final_total'], 2) : '0.00',
                isset($order['payment']['total_paid']) ? number_format($order['payment']['total_paid'], 2) : '0.00',
                isset($order['payment']['amount_due']) ? number_format($order['payment']['amount_due'], 2) : '0.00',
                $order['created_at'] ?? '',
                '', // Product Name (blank for order row)
                '', // Variation Name (blank for order row)
                '', // Variation SKU (blank for order row)
                '', // Quantity (blank for order row)
                '', // Received Quantity (blank for order row)
                '', // Unit Price (blank for order row)
                '', // Line Total (blank for order row)
            ];
            $data[] = $orderRow;
            
            // Add items for this order
            if (!empty($order['items']) && is_array($order['items'])) {
                foreach ($order['items'] as $item) {
                    $itemRow = [
                        '', // Order Number (blank for items)
                        '', // Date (blank for items)
                        '', // Status (blank for items)
                        '', // Payment Status (blank for items)
                        '', // Shipping Status (blank for items)
                        '', // Picking Status (blank for items)
                        '', // Location (blank for items)
                        '', // Total Items (blank for items)
                        '', // Received Items (blank for items)
                        '', // Pending Items (blank for items)
                        '', // Subtotal (blank for items)
                        '', // Tax (blank for items)
                        '', // Discount (blank for items)
                        '', // Shipping (blank for items)
                        '', // Final Total (blank for items)
                        '', // Paid (blank for items)
                        '', // Due (blank for items)
                        '', // Created At (blank for items)
                        $item['product_name'] ?? 'N/A',
                        $item['variation_name'] ?? 'N/A',
                        $item['variation_sku'] ?? 'N/A',
                        $item['quantity'] ?? 0,
                        $item['received_quantity'] ?? 0,
                        isset($item['unit_price_inc_tax']) ? number_format($item['unit_price_inc_tax'], 2) : '0.00',
                        isset($item['line_total']) ? number_format($item['line_total'], 2) : '0.00',
                    ];
                    $data[] = $itemRow;
                }
            }
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Date',
            'Status',
            'Payment Status',
            'Shipping Status',
            'Picking Status',
            'Location',
            'Total Items',
            'Received Items',
            'Pending Items',
            'Subtotal',
            'Tax Amount',
            'Discount Amount',
            'Shipping Charges',
            'Final Total',
            'Total Paid',
            'Amount Due',
            'Created At',
            'Product Name',
            'Variation Name',
            'Variation SKU',
            'Quantity',
            'Received Quantity',
            'Unit Price',
            'Line Total',
        ];
    }

    public function title(): string
    {
        return 'Orders';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Order Number
            'B' => 12,  // Date
            'C' => 12,  // Status
            'D' => 15,  // Payment Status
            'E' => 15,  // Shipping Status
            'F' => 15,  // Picking Status
            'G' => 20,  // Location
            'H' => 12,  // Total Items
            'I' => 15,  // Received Items
            'J' => 15,  // Pending Items
            'K' => 12,  // Subtotal
            'L' => 12,  // Tax Amount
            'M' => 15,  // Discount Amount
            'N' => 15,  // Shipping Charges
            'O' => 12,  // Final Total
            'P' => 12,  // Total Paid
            'Q' => 12,  // Amount Due
            'R' => 20,  // Created At
            'S' => 30,  // Product Name
            'T' => 25,  // Variation Name
            'U' => 20,  // Variation SKU
            'V' => 10,  // Quantity
            'W' => 15,  // Received Quantity
            'X' => 12,  // Unit Price
            'Y' => 12,  // Line Total
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

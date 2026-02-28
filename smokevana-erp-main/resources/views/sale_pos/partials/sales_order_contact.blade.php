@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<table class="table table-bordered table-striped ajax_view" id="sales_order_table" style="width: 100%;">
    <thead>
        <tr>
            <th>@lang('messages.date')</th>
            <th>@lang('sale.invoice_no')</th>
            <th>@lang('sale.customer_name')</th>
            <th>@lang('lang_v1.contact_no')</th>
            <th>@lang('sale.location')</th>
            <th>Status</th>
            <th>Order Status</th>
            <th>Method</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Due</th>
            <th>@lang('lang_v1.sell_return_due')</th>
            <th>@lang('lang_v1.shipping_status')</th>
            <th>Items</th>
            <th>@lang('lang_v1.types_of_service')</th>
            <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}</th>
            <th>@lang('lang_v1.added_by')</th>
            <th>@lang('sale.sell_note')</th>
            <th>@lang('sale.staff_note')</th>
            <th>@lang('sale.shipping_details')</th>
            <th>@lang('restaurant.table')</th>
            <th>@lang('restaurant.service_staff')</th>
            <th>@lang('messages.action')</th>
        </tr>
    </thead>
    {{-- <tfoot>
        <tr class="bg-gray font-17 footer-total text-left">
            <td><strong>@lang('sale.total'):</strong></td>
            <td colspan="4"></td>
            <td class="footer_payment_status_count"></td>
            <td class="payment_method_count"></td>
            <td class="footer_sale_total"></td>
            <td class="footer_total_paid"></td>
            <td class="footer_total_remaining"></td>
            <td class="footer_total_sell_return_due"></td>
            <td ></td>
            <td class="service_type_count"></td>
            <td colspan="9"></td>
        </tr>
    </tfoot> --}}
</table>
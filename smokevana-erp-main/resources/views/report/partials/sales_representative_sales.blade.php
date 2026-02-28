<div>
<table class="table   table-bordered table-striped ajax_view hide-footer" id="sr_sales_report" style="min-width: max-content;">
    <thead>
        <tr>
            <th style="min-width: 150px">@lang('messages.date')</th>
            <th style="min-width: 100px">@lang('sale.invoice_no')</th>
            <th style="min-width: 100px">@lang('sale.customer_name')</th>
            <th style="min-width: 100px">@lang('sale.location')</th>
            <th style="min-width: 100px">@lang('sale.payment_status')</th>
            <th style="min-width: 100px">@lang('sale.total_amount')</th>
            <th style="min-width: 100px">@lang('sale.total_paid')</th>
            <th style="min-width: 100px">@lang('sale.total_remaining')</th>
        </tr>
    </thead>
    <tfoot>
        <tr class="bg-gray font-17 footer-total text-center">
            <td colspan="4"><strong>@lang('sale.total'):</strong></td>
            <td id="sr_footer_payment_status_count"></td>
            <td><span class="display_currency" id="sr_footer_sale_total" data-currency_symbol ="true"></span></td>
            <td><span class="display_currency" id="sr_footer_total_paid" data-currency_symbol ="true"></span></td>
            <td class="text-left"><small>@lang('lang_v1.sell_due') - <span class="display_currency" id="sr_footer_total_remaining" data-currency_symbol ="true"></span><br>@lang('lang_v1.sell_return_due') - <span class="display_currency" id="sr_footer_total_sell_return_due" data-currency_symbol ="true"></span></small></td>
        </tr>
    </tfoot>
</table>
</div>
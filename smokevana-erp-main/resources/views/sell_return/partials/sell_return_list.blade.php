<div class="table-responsive">
    <table class="table nowrap table-bordered table-striped ajax_view" id="sell_return_table" style="min-width: max-content;">
        <thead>
            <tr>
                <th>@lang('messages.date')</th>
                <th>@lang('sale.invoice_no')</th>
                <th>@lang('lang_v1.parent_sale')</th>
                <th>@lang('sale.customer_name')</th>
                <th>@lang('sale.location')</th>
                <th>@lang('purchase.payment_status')</th>
                <th>@lang('sale.total_amount')</th>
                <th>@lang('purchase.payment_due')</th>
                <th>@lang('messages.action')</th>
            </tr>
        </thead>
        <tfoot>
            <tr class="bg-gray font-17 text-center footer-total">
                <td colspan="2"></td>
                <td><strong>@lang('sale.total'):</strong></td>
                <td colspan="2"></td>
                <td class="footer_payment_status_count_sr text-right"></td>
                <td><span class="footer_sell_return_total text-right"></span></td>
                <td><span class="footer_total_due_sr text-right"></span></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
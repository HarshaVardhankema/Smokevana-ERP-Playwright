<div class="table-responsive">
<table class="table  table-bordered table-striped ajax_view" id="purchase_return_table" style="border-collapse: collapse; width: 100%; min-width: max-content;">
    <thead style="white-space: nowrap; ">
        <tr>
            <th>@lang('messages.action')</th>
            <th>@lang('messages.date')</th>
            <th>@lang('purchase.ref_no')</th>
            <th>@lang('purchase.location')</th>
            <th>@lang('purchase.supplier')</th>
            <th>@lang('purchase.purchase_status')</th>
            {{-- <th>@lang('purchase.payment_status')</th> --}}
            <th>@lang('purchase.grand_total')</th>
            <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
            <th>@lang('lang_v1.added_by')</th>
        </tr>
    </thead>    
</table>
</div>
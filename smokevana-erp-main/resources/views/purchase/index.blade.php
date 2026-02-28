@extends('layouts.app')
@section('title', __('purchase.purchases'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .purchase-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .purchase-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }
    .purchase-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .purchase-header-content { display: flex; flex-direction: column; gap: 4px; }
    .purchase-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .purchase-header-banner .banner-title i { color: #fff !important; }
    .purchase-header-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
    .purchase-header-banner .amazon-orange-add { border: 2px solid #C7511F !important; }
    .purchase-list-page #dynamic_button,
    .purchase-list-page .amazon-orange-add,
    .purchase-list-page .box-tools .tw-dw-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
    .purchase-header-banner .amazon-orange-add:hover { border-color: #E47911 !important; opacity: 0.95; }
    .purchase-list-page #dynamic_button:hover,
    .purchase-list-page .amazon-orange-add:hover,
    .purchase-list-page .box-tools .tw-dw-btn:hover { color: #fff !important; opacity: 0.95; border-color: #E47911 !important; }
    .purchase-list-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .purchase-list-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .purchase-list-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .purchase-list-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
    .purchase-list-page .box-primary .tw-flow-root,
    .purchase-list-page .box-primary .table-responsive { background: #f7f8f8 !important; padding: 1rem 1.25rem !important; }
    .purchase-list-page .dt-buttons .btn,
    .purchase-list-page .dt-buttons button,
    .purchase-list-page .dt-buttons .dt-button { background: #232f3e !important; border: 1px solid #37475a !important; color: #fff !important; }
    .purchase-list-page .dt-buttons .btn:hover,
    .purchase-list-page .dt-buttons button:hover,
    .purchase-list-page .dt-buttons .dt-button:hover { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; }
    .purchase-list-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff9900 !important; border-color: #ff9900 !important; color: #fff !important; }
    .purchase-list-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { border-color: #ff9900; color: #232f3e; }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header no-print">
    <div class="purchase-header-banner amazon-theme-banner">
        <div class="purchase-header-content">
            <h1 class="banner-title"><i class="fas fa-shopping-cart"></i> @lang('purchase.purchases')</h1>
            <p class="banner-subtitle">@lang('purchase.all_purchases')</p>
        </div>
        <div class="tw-flex tw-gap-2">
            <button type="button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full amazon-orange-add"
                data-toggle="modal" data-target="#import_csv_modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-upload">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 9l5 -5l5 5" />
                    <path d="M12 4l0 12" />
                </svg> @lang('lang_v1.import') CSV
            </button>
            <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'downloadCsvTemplate']) }}" 
            class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full amazon-orange-add"
            download>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                    <path d="M7 11l5 5l5 -5" />
                    <path d="M12 4l0 12" />
                </svg> @lang('lang_v1.download_template_file')
            </a>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content no-print purchase-list-page">
    <div class="row">
        <div class="col-md-12">

            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('report.filters')</h4>
                        </div>
                        <div class="modal-body" style="padding: 0px; margin-top: 10px;">

                            {{-- @component('components.filters', ['title' => __('report.filters')]) --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('purchase_list_filter_location_id', __('purchase.business_location')
                                    . ':') !!}
                                    {!! Form::select('purchase_list_filter_location_id', $business_locations, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('purchase_list_filter_supplier_id', __('purchase.supplier') . ':')
                                    !!}
                                    {!! Form::select('purchase_list_filter_supplier_id', $suppliers, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('purchase_list_filter_status', __('purchase.purchase_status') . ':')
                                    !!}
                                    {!! Form::select('purchase_list_filter_status', $orderStatuses, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('purchase_list_filter_payment_status', __('purchase.payment_status')
                                    . ':') !!}
                                    {!! Form::select(
                                    'purchase_list_filter_payment_status',
                                    [
                                    'paid' => __('lang_v1.paid'),
                                    'due' => __('lang_v1.due'),
                                    'partial' => __('lang_v1.partial'),
                                    'overdue' => __('lang_v1.overdue'),
                                    ],
                                    null,
                                    ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                    __('lang_v1.all')],
                                    ) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':')
                                    !!}
                                    {!! Form::text('purchase_list_filter_date_range', null, [
                                    'placeholder' => __('lang_v1.select_a_date_range'),
                                    'class' => 'form-control',
                                    'readonly',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-primary"
                                    id="applyFiltersBtn">@lang('messages.apply')</button>
                                --}}
                                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                    data-dismiss="modal">@lang('messages.close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- @endcomponent --}}

    @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
    @can('purchase.create')
    @slot('tool')
    <div class="box-tools">
        {{-- <a class="btn btn-block btn-primary"
            href="{{action([\App\Http\Controllers\PurchaseController::class, 'create'])}}">
            <i class="fa fa-plus"></i> @lang('messages.add')</a> --}}
        <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right amazon-orange-add"
            href="{{action([\App\Http\Controllers\PurchaseController::class, 'create'])}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg> @lang('messages.add')
        </a>
    </div>
    @endslot
    @endcan
    @include('purchase.partials.purchase_table')
    @endcomponent

    <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('purchase.partials.update_purchase_status_modal')

    <!-- Import CSV Modal -->
    <div class="modal fade" id="import_csv_modal" tabindex="-1" role="dialog" aria-labelledby="import_csv_modal_label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="import_csv_modal_label">@lang('lang_v1.import') @lang('purchase.purchases') (CSV)</h4>
                </div>
                {!! Form::open(['url' => action([\App\Http\Controllers\PurchaseController::class, 'importFromCsv']), 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'import_csv_form']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('csv_file', __('lang_v1.import') . ' CSV ' . __('lang_v1.file') . ':') !!}
                        {!! Form::file('csv_file', ['class' => 'form-control', 'required', 'accept' => '.csv']) !!}
                        <small class="help-block">CSV Format: SKU, Quantity (First row can be header)</small>
                    </div>
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'required',
                            'placeholder' => __('messages.please_select'),
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('contact_id', __('purchase.supplier') . ':') !!}
                        {!! Form::select('contact_id', $suppliers, null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'required',
                            'placeholder' => __('messages.please_select'),
                        ]) !!}
                    </div>
                    <div class="form-group">
                        <a href="{{ action([\App\Http\Controllers\PurchaseController::class, 'downloadCsvTemplate']) }}" 
                           class="btn btn-success" download>
                            <i class="fa fa-download"></i> @lang('lang_v1.download_template_file')
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('lang_v1.import')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

</section>

<section id="receipt_section" class="print_section"></section>

<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    //Date range as a button
        $('#purchase_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                    moment_date_format));
                purchase_table.ajax.reload();
            }
        );
        $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_list_filter_date_range').val('');
            purchase_table.ajax.reload();
        });

        $(document).on('click', '.update_status', function(e) {
            e.preventDefault();
            $('#update_purchase_status_form').find('#status').val($(this).data('status'));
            $('#update_purchase_status_form').find('#purchase_id').val($(this).data('purchase_id'));
            $('#update_purchase_status_modal').modal('show');
        });

        $(document).on('submit', '#update_purchase_status_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('#update_purchase_status_modal').modal('hide');
                        toastr.success(result.msg);
                        purchase_table.ajax.reload();
                        $('#update_purchase_status_form')
                            .find('button[type="submit"]')
                            .attr('disabled', false);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
</script>

@endsection
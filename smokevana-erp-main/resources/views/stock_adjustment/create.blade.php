@extends('layouts.app')
@section('title', __('stock_adjustment.add'))

@section('css')
    <style>
        .sac-header-banner { background:#37475a;border-radius:6px;padding:22px 28px;margin-bottom:16px;box-shadow:0 3px 10px rgba(15,17,17,0.4);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px; }
        .sac-header-banner .banner-content { display:flex;flex-direction:column;gap:4px; }
        .sac-header-banner .banner-title { display:flex;align-items:center;gap:10px;font-size:22px;font-weight:700;margin:0;color:#fff; }
        .sac-header-banner .banner-title i { color:#fff!important; }
        .sac-header-banner .banner-subtitle { font-size:13px;color:rgba(249,250,251,0.88);margin:4px 0 0 0; }
        .amazon-orange-btn { background:linear-gradient(to bottom,#FF9900 0%,#E47911 100%)!important;border-color:#C7511F!important;color:white!important; }
        .amazon-orange-btn:hover { color:white!important;opacity:0.95; }
        #stock_adjustment_product_table tbody tr.stock-adjustment-search-row td {
            background: #fff;
            padding: 8px;
            border-top: none;
            border-bottom: 1px solid #eee;
        }

        #stock_adjustment_product_table tbody tr.stock-adjustment-search-row td .form-group {
            margin-bottom: 0;
        }

        .stock-adjustment-search {
            max-width: 520px;
            width: 100%;
        }

        .stock-adjustment-search .input-group {
            width: 100%;
        }

        .stock-adjustment-search .form-control {
            width: 100%;
            border-radius: 4px;
        }
    </style>
@endsection

@section('content')

    <!-- Content Header (Page header) -->
    {!! Form::open([
        'url' => action([\App\Http\Controllers\StockAdjustmentController::class, 'store']),
        'method' => 'post',
        'id' => 'stock_adjustment_form',
    ]) !!}
    <section class="content-header">
        <div class="sac-header-banner amazon-theme-banner">
            <div class="banner-content">
                <h1 class="banner-title"><i class="fas fa-sliders-h"></i> @lang('stock_adjustment.add')</h1>
                <p class="banner-subtitle">Adjust inventory quantities. Select location, adjustment type, and add products.</p>
            </div>
            <div>
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white amazon-orange-btn" id="save_stock_adjustment">@lang('messages.save')</button>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content no-print">
      

        @component('components.widget', ['class' => 'box-solid','style' => 'z-index:999; position:relative;'])
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location') . ':*') !!}
                        {!! Form::select('location_id', $business_locations, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required',
                            'id' => 'location_id',
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('purchase.ref_no') . ':') !!}
                        {!! Form::text('ref_no', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('adjustment_type', __('stock_adjustment.adjustment_type') . ':*') !!} @show_tooltip(__('tooltip.adjustment_type'))
                        {!! Form::select(
                            'adjustment_type',
                            ['normal' => __('stock_adjustment.normal'), 'abnormal' => __('stock_adjustment.abnormal')],
                            null,
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'],
                        ) !!}
                    </div>
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-solid'])
            {{-- <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            {!! Form::text('search_product', null, [
                                'class' => 'form-control',
                                'id' => 'search_product_for_srock_adjustment',
                                'placeholder' => __('stock_adjustment.search_product'),
                                'disabled',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-sm-12">
                    <input type="hidden" id="product_row_index" value="0">
                    <input type="hidden" id="total_amount" name="final_total" value="0">
                    <div class="table-responsive" style="max-height: 50vh;min-height: 50vh; overflow-y: auto;">
                        <table class="table table-bordered table-striped table-condensed" id="stock_adjustment_product_table">
                            <thead>
                                <tr>
                                    <th class="col-sm-4 text-center">
                                        @lang('sale.product')
                                    </th>
                                    <th class="col-sm-2 text-center">
                                        @lang('sale.qty')
                                    </th>
                                    <th class="col-sm-2 text-center show_price_with_permission">
                                        @lang('sale.unit_price')
                                    </th>
                                    <th class="col-sm-2 text-center show_price_with_permission">
                                        @lang('sale.subtotal')
                                    </th>
                                    <th class="col-sm-2 text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="stock-adjustment-search-row">
                                    <td>
                                        <div class="form-group stock-adjustment-search">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-search"></i>
                                                </span>
                                                {!! Form::text('search_product', null, [
                                                    'class' => 'form-control',
                                                    'id' => 'search_product_for_srock_adjustment',
                                                    'placeholder' => __('stock_adjustment.search_product'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td class="show_price_with_permission"></td>
                                    <td class="show_price_with_permission"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
                                {!! Form::text('total_amount_recovered', 0, [
                                    'class' => 'form-control input_number',
                                    'placeholder' => __('stock_adjustment.total_amount_recovered'),
                                    'min' => 0,
                                    'step' => '0.01'
                                ]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
                                {!! Form::textarea('additional_notes', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'),
                                    'rows' => 1,
                                ]) !!}
                            </div>
                        </div>
                        <tfoot>
                            <tr class="text-center show_price_with_permission">
                                <td colspan="2">
                                </td>
                                <td>
                                    <div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span
                                            id="total_adjustment">0.00</span></div>
                                </td>
                            </tr>
                        </tfoot>
                    </div>
                    
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-solid hide'])
            {{-- <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
                        {!! Form::text('total_amount_recovered', 0, [
                            'class' => 'form-control input_number',
                            'placeholder' => __('stock_adjustment.total_amount_recovered'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
                        {!! Form::textarea('additional_notes', null, [
                            'class' => 'form-control',
                            'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'),
                            'rows' => 3,
                        ]) !!}
                    </div>
                </div>
            </div> --}}
            {{-- <div class="row">
                <div class="col-sm-12 text-center">
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
                </div>
            </div> --}}
        @endcomponent
        {!! Form::close() !!}
    </section>
@stop
@section('javascript')
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        __page_leave_confirmation('#stock_adjustment_form');
    </script>
@endsection


@cannot('view_purchase_price')
    <style>
        .show_price_with_permission {
            display: none !important;
        }
    </style>
@endcannot

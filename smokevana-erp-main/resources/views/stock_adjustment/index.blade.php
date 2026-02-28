@extends('layouts.app')
@section('title', __('stock_adjustment.stock_adjustments'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .stock-adjustment-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .sa-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .sa-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .sa-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .sa-header-banner .banner-title i { color: #fff !important; }
    .sa-header-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
    .stock-adjustment-list-page #dynamic_button,
    .stock-adjustment-list-page .amazon-orange-add,
    .stock-adjustment-list-page .box-tools .tw-dw-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
    .stock-adjustment-list-page #dynamic_button:hover,
    .stock-adjustment-list-page .amazon-orange-add:hover,
    .stock-adjustment-list-page .box-tools .tw-dw-btn:hover { color: #fff !important; opacity: 0.95; border-color: #E47911 !important; }
    /* Section card: dark header + orange line + light body */
    .stock-adjustment-list-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .stock-adjustment-list-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .stock-adjustment-list-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .stock-adjustment-list-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
    .stock-adjustment-list-page .box-primary .tw-flow-root,
    .stock-adjustment-list-page .box-primary .table-responsive { background: #f7f8f8 !important; padding: 1rem 1.25rem !important; }
    .stock-adjustment-list-page .dt-buttons .btn,
    .stock-adjustment-list-page .dt-buttons button,
    .stock-adjustment-list-page .dt-buttons .dt-button { background: #232f3e !important; border: 1px solid #37475a !important; color: #fff !important; }
    .stock-adjustment-list-page .dt-buttons .btn:hover,
    .stock-adjustment-list-page .dt-buttons button:hover,
    .stock-adjustment-list-page .dt-buttons .dt-button:hover { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; }
    .stock-adjustment-list-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff9900 !important; border-color: #ff9900 !important; color: #fff !important; }
    .stock-adjustment-list-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { border-color: #ff9900; color: #232f3e; }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div class="sa-header-banner amazon-theme-banner">
        <h1 class="banner-title"><i class="fas fa-sliders-h"></i> @lang('stock_adjustment.stock_adjustments')</h1>
        <p class="banner-subtitle">@lang('stock_adjustment.all_stock_adjustments')</p>
    </div>
</section>

<!-- Main content -->
<section class="content stock-adjustment-list-page">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('stock_adjustment.all_stock_adjustments')])
        @slot('tool')
            <div class="box-tools">
                @if(auth()->user()->can('purchase.create'))
                    <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right amazon-orange-add"
                        href="{{action([\App\Http\Controllers\StockAdjustmentController::class, 'create'])}}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                @endif
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table  table-bordered table-striped ajax_view" id="stock_adjustment_table"  style="min-width: max-content;">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('stock_adjustment.adjustment_type')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('stock_adjustment.total_amount_recovered')</th>
                        <th>@lang('stock_adjustment.reason_for_stock_adjustment')</th>
                        <th>@lang('lang_v1.added_by')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

</section>
<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
@endsection

@cannot('view_purchase_price')
    <style>
        .show_price_with_permission {
            display: none !important;
        }
    </style>
@endcannot
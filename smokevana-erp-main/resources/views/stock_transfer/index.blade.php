@extends('layouts.app')
@section('title', __('lang_v1.stock_transfers'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .stock-transfer-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .stl-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .stl-header-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .stl-header-banner .banner-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #fff !important;
    }
    .stl-header-banner .banner-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.9) !important;
        margin: 4px 0 0 0;
    }
    .stock-transfer-list-page .box-primary {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .stock-transfer-list-page .box-primary .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .stock-transfer-list-page .box-primary .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .stock-transfer-list-page .box-primary .box-title {
        color: #fff !important;
        font-weight: 600;
    }
    .stock-transfer-list-page .box-primary .table-responsive {
        background: #f7f8f8 !important;
        padding: 1rem 1.25rem !important;
    }
    .stock-transfer-list-page .amazon-orange-add {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 2px solid #C7511F !important;
        color: #fff !important;
    }
    .stock-transfer-list-page .amazon-orange-add:hover {
        color: #fff !important;
        opacity: 0.95;
        border-color: #E47911 !important;
    }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="stl-header-banner">
        <h1 class="banner-title">
            <i class="fas fa-exchange-alt"></i>
            @lang('lang_v1.stock_transfers')
        </h1>
        <p class="banner-subtitle">@lang('lang_v1.list_stock_transfers')</p>
    </div>
</section>

<section class="content stock-transfer-list-page">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.list_stock_transfers')])
        @slot('tool')
            <div class="box-tools">
                @can('purchase.create')
                    <a class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right amazon-orange-add"
                        href="{{ action([\App\Http\Controllers\StockTransferController::class, 'create']) }}">
                        <i class="fa fa-plus"></i> @lang('lang_v1.add_stock_transfer')
                    </a>
                @endcan
            </div>
        @endslot

        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="stock_transfer_table" style="min-width: max-content;">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('lang_v1.location_from')</th>
                        <th>@lang('lang_v1.location_to')</th>
                        <th>@lang('sale.status')</th>
                        <th class="show_price_with_permission">@lang('lang_v1.shipping_charges')</th>
                        <th class="show_price_with_permission">@lang('purchase.purchase_total')</th>
                        <th>@lang('purchase.additional_notes')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    @include('stock_transfer.partials.update_status_modal')
</section>
@stop

@section('javascript')
    <script src="{{ asset('js/stock_transfer.js?v=' . $asset_v) }}"></script>
@endsection

@cannot('view_purchase_price')
    <style>
        .show_price_with_permission {
            display: none !important;
        }
    </style>
@endcannot


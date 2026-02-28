@extends('layouts.app')
@section('title', __('lang_v1.subscriptions'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* Subscriptions Page - Amazon Theme */
    .subscriptions-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    
    /* Header Banner */
    .subscriptions-page .content-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .subscriptions-page .content-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .subscriptions-page .content-header h1 {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #fff !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Box/Card Styling */
    .subscriptions-page .box-primary,
    .subscriptions-page .box-solid {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        background: #fff;
    }
    .subscriptions-page .box-primary .box-header,
    .subscriptions-page .box-solid .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .subscriptions-page .box-primary .box-header::before,
    .subscriptions-page .box-solid .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .subscriptions-page .box-primary .box-title,
    .subscriptions-page .box-solid .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
    }
    .subscriptions-page .box-primary .box-body,
    .subscriptions-page .box-solid .box-body {
        background: #f7f8f8 !important;
        padding: 1.25rem 1.5rem !important;
    }
    
    /* Form Controls */
    .subscriptions-page .form-group label {
        color: #0F1111 !important;
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .subscriptions-page .form-control {
        background: #fff;
        border: 1px solid #D5D9D9;
        color: #0F1111;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-height: 2rem;
        box-sizing: border-box;
        border-radius: 4px;
    }
    .subscriptions-page .form-control:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    
    /* Table Styling */
    .subscriptions-page #subscriptions_table thead tr {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        position: relative;
    }
    .subscriptions-page #subscriptions_table thead tr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .subscriptions-page #subscriptions_table thead th {
        background: transparent !important;
        color: #fff !important;
        font-weight: 600;
        border-color: rgba(255,255,255,0.2) !important;
        padding: 12px 8px;
        position: relative;
        z-index: 2;
    }
    .subscriptions-page #subscriptions_table thead th.sorting::after,
    .subscriptions-page #subscriptions_table thead th.sorting_asc::after,
    .subscriptions-page #subscriptions_table thead th.sorting_desc::after {
        color: #ff9900 !important;
    }
    .subscriptions-page #subscriptions_table tbody tr {
        background: #fff;
    }
    .subscriptions-page #subscriptions_table tbody td {
        border-color: #D5D9D9;
        padding: 10px 8px;
    }
    
    /* DataTables Controls */
    .subscriptions-page .dataTables_wrapper .dataTables_filter input,
    .subscriptions-page .dataTables_wrapper .dataTables_length select {
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 4px 8px;
    }
    .subscriptions-page .dataTables_wrapper .dataTables_filter input:focus,
    .subscriptions-page .dataTables_wrapper .dataTables_length select:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .subscriptions-page .dt-buttons .btn,
    .subscriptions-page .dt-buttons button,
    .subscriptions-page .dt-buttons .dt-button {
        background: #232f3e !important;
        border: 1px solid #37475a !important;
        color: #fff !important;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.8125rem;
        margin-right: 4px;
    }
    .subscriptions-page .dt-buttons .btn:hover,
    .subscriptions-page .dt-buttons button:hover,
    .subscriptions-page .dt-buttons .dt-button:hover {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
    }
    .subscriptions-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #ff9900 !important;
        border-color: #ff9900 !important;
        color: #fff !important;
    }
    .subscriptions-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: #ff9900;
        color: #232f3e;
    }
    .subscriptions-page .dataTables_wrapper .dataTables_info {
        color: #565959;
        font-size: 0.8125rem;
    }
</style>
@endsection

@section('content')
<div class="subscriptions-page">
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1><i class="fa fa-repeat"></i> @lang('lang_v1.subscriptions') @show_tooltip(__('lang_v1.recurring_invoice_help'))</h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.subscriptions')])
            @can('sell.view')
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('subscriptions_filter_date_range', __('report.date_range') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('subscriptions_filter_date_range', null, [
                                    'placeholder' => __('lang_v1.select_a_date_range'),
                                    'class' => 'form-control',
                                    'readonly',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                @include('sale_pos.partials.subscriptions_table')
            @endcan
        @endcomponent
    </section>
</div>
@stop

@section('javascript')
    @include('sale_pos.partials.subscriptions_table_javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection

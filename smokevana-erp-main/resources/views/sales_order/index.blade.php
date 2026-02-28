@extends('layouts.app')
@section('title', __( 'lang_v1.sales_order'))

@section('css')
<style>
/* Amazon Theme - Sales Order Page */
.amazon-sales-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 16px 20px;
}

/* Top banner – same style as Add new product / Manage Order / Roles */
.amazon-sales-header-banner {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 28px;
    margin-bottom: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}

.amazon-sales-header-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.amazon-sales-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

.amazon-sales-header-title i {
    font-size: 22px;
    color: #ffffff !important;
}

.amazon-sales-header-subtitle {
    font-size: 13px;
    color: rgba(249, 250, 251, 0.88);
    margin: 0;
}

/* Page Header - Action Buttons Row (below banner) */
.amazon-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-page-title {
    font-size: 24px;
    font-weight: 700;
    color: #0F1111;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.amazon-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* Main Card */
.amazon-card {
    background: #FFFFFF;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #D5D9D9;
    overflow: hidden;
}

/* Controls Bar */
.amazon-controls-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #F7F8F8;
    border-bottom: 1px solid #E7E7E7;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-controls-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.amazon-controls-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Show Entries */
.amazon-entries-select {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #0F1111;
}

.amazon-entries-select select {
    padding: 6px 28px 6px 10px;
    border: 1px solid #888C8C;
    border-radius: 4px;
    background: #FFF url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 8px center/12px;
    font-size: 13px;
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    color: #0F1111;
}

.amazon-entries-select select:focus {
    outline: none;
    border-color: #FF9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
}

/* Search Box */
.amazon-search-wrapper {
    position: relative;
    flex: 1;
    max-width: 400px;
    min-width: 200px;
}

.amazon-search-input {
    width: 100%;
    padding: 8px 12px 8px 36px;
    border: 1px solid #888C8C;
    border-radius: 4px;
    font-size: 13px;
    background: #FFF;
    transition: all 0.15s ease;
}

.amazon-search-input:focus {
    outline: none;
    border-color: #FF9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
}

.amazon-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #888C8C;
}

/* Amazon Buttons - Interactive with Hover Effects */
.amazon-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    white-space: nowrap;
    border: 1px solid;
    position: relative;
    overflow: visible;
    transform: translateY(0) scale(1);
    z-index: 1;
}

.amazon-btn:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    z-index: 10;
    text-decoration: none;
}

.amazon-btn:active {
    transform: translateY(-1px) scale(1.01);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.amazon-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.4);
}

.amazon-btn svg {
    transition: transform 0.2s ease;
}

.amazon-btn:hover svg {
    transform: scale(1.1);
}

.amazon-btn-secondary {
    background: linear-gradient(180deg, #FFFFFF 0%, #F5F6F6 100%);
    border-color: #D5D9D9;
    color: #0F1111;
    box-shadow: 0 1px 2px rgba(15, 17, 17, 0.1);
}

.amazon-btn-secondary:hover {
    background: linear-gradient(180deg, #F7F8F8 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    color: #0F1111;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.amazon-btn-primary {
    background: linear-gradient(135deg, #FFB84D 0%, #FF9900 50%, #E47911 100%);
    border-color: #C7511F;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-primary:hover {
    background: linear-gradient(135deg, #FFCC80 0%, #FFB84D 50%, #FF9900 100%);
    border-color: #C7511F;
    color: white;
    box-shadow: 0 6px 20px rgba(255, 153, 0, 0.4);
}

.amazon-btn-info {
    background: linear-gradient(135deg, #008296 0%, #007185 50%, #006073 100%);
    border-color: #00545E;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-info:hover {
    background: linear-gradient(135deg, #00A8BD 0%, #008296 50%, #007185 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(0, 113, 133, 0.4);
}

/* Button Separator */
.amazon-btn-separator {
    width: 1px;
    height: 24px;
    background: #D5D9D9;
    margin: 0 4px;
}

/* Table Styles */
.amazon-table-wrapper {
    overflow-x: auto;
    padding: 0;
    background: #FFFFFF;
    border-radius: 8px;
    border: 1px solid #D5D9D9;
    overflow: hidden;
}

.amazon-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.amazon-table thead {
    background: #232F3E !important;
    position: relative;
    z-index: 10;
}

/* Orange stripe below header */
.amazon-table-wrapper::before {
    content: '';
    display: block;
    height: 4px;
    background: #ff9900;
    width: 100%;
    position: relative;
    z-index: 11;
}

/* Alternative: Orange stripe using thead tr */
.amazon-table thead tr {
    position: relative;
}

.amazon-table thead tr::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #ff9900;
    z-index: 12;
}

.amazon-table thead th {
    padding: 12px 12px;
    font-weight: 600;
    color: #FFFFFF !important;
    text-align: left;
    border: none;
    white-space: nowrap;
    vertical-align: middle;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    background: #232F3E !important;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.3px;
}

.amazon-table thead th:last-child {
    border-right: none;
}

.amazon-table thead th.sorting,
.amazon-table thead th.sorting_asc,
.amazon-table thead th.sorting_desc {
    position: relative;
    padding-right: 24px;
    cursor: pointer;
}

.amazon-table thead th.sorting::after,
.amazon-table thead th.sorting_asc::after,
.amazon-table thead th.sorting_desc::after {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    opacity: 1;
    color: #ff9900 !important;
}

.amazon-table thead th.sorting::after {
    content: "⇅";
    color: #ff9900 !important;
}

.amazon-table thead th.sorting_asc::after {
    content: "↑";
    color: #ff9900 !important;
}

.amazon-table thead th.sorting_desc::after {
    content: "↓";
    color: #ff9900 !important;
}

.amazon-table tbody tr {
    border-bottom: 1px solid #E7E7E7;
    transition: background 0.1s ease;
}

.amazon-table tbody tr:hover {
    background: #F7FAFA;
}

.amazon-table tbody tr:nth-child(even) {
    background: #FAFAFA;
}

.amazon-table tbody tr:nth-child(even):hover {
    background: #F0F2F2;
}

.amazon-table tbody td {
    padding: 10px 12px;
    color: #0F1111;
    vertical-align: middle;
}

/* Override DataTables default header styles */
#sell_table.dataTable thead th {
    background: #232F3E !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

#sell_table.dataTable thead th.sorting::after,
#sell_table.dataTable thead th.sorting_asc::after,
#sell_table.dataTable thead th.sorting_desc::after {
    color: #ff9900 !important;
}

/* Ensure orange stripe is visible */
#sell_table.dataTable thead tr::after {
    content: '' !important;
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 4px !important;
    background: #ff9900 !important;
    z-index: 12 !important;
}

/* Hide original DataTable controls */
.dataTables_length,
.dataTables_filter,
.dt-buttons {
    display: none !important;
}

/* Custom Column Visibility Dropdown */
.colvis-dropdown-wrapper {
    position: relative;
    display: inline-block;
}

.colvis-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 8px;
    background: #FFFFFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    padding: 0;
    min-width: 220px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 9999;
    display: none;
}

.colvis-dropdown.show {
    display: block;
    animation: colvisSlideDown 0.2s ease;
}

@keyframes colvisSlideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.colvis-dropdown-header {
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #232F3E;
    background: #F7F8F8;
    border-bottom: 1px solid #E7E7E7;
    border-radius: 8px 8px 0 0;
}

.colvis-dropdown-body {
    padding: 8px 0;
}

.colvis-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 13px;
    color: #0F1111;
}

.colvis-item:hover {
    background: #F7F8F8;
}

.colvis-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.colvis-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid #888C8C;
    border-radius: 4px;
    background: #FFF;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.colvis-item.active .colvis-checkbox {
    background: linear-gradient(135deg, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
}

.colvis-item.active .colvis-checkbox::after {
    content: '';
    display: block;
    width: 10px;
    height: 10px;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='white' d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
    background-size: contain;
    background-repeat: no-repeat;
}

.colvis-label {
    flex: 1;
}

/* DataTables Info */
.dataTables_info {
    font-size: 13px;
    color: #565959;
    padding: 12px 16px;
}

/* Modern Pagination */
.dataTables_paginate {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

/* Style the <a> inside pagination <li> (Bootstrap DataTables renders <ul><li><a>) */
.dataTables_paginate .paginate_button > a,
.dataTables_paginate .paginate_button > span {
    padding: 8px 14px !important;
    border-radius: 6px !important;
    border: 1px solid #D5D9D9 !important;
    background: linear-gradient(180deg, #FFFFFF 0%, #F5F6F6 100%) !important;
    color: #0F1111 !important;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-block;
    text-decoration: none;
    float: none !important;
    margin-left: 0 !important;
}

/* Reset the <li> wrapper so it doesn't create its own visible box */
.dataTables_paginate .paginate_button {
    border: none !important;
    background: none !important;
    padding: 0 !important;
    margin: 0 2px !important;
    box-shadow: none !important;
}

.dataTables_paginate .paginate_button:hover > a {
    background: linear-gradient(180deg, #F7F8F8 0%, #E3E6E6 100%) !important;
    border-color: #BBBFBF !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.dataTables_paginate .paginate_button.active > a,
.dataTables_paginate .paginate_button.active > span {
    background: linear-gradient(135deg, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: white !important;
    box-shadow: 0 2px 8px rgba(255, 153, 0, 0.3);
}

.dataTables_paginate .paginate_button.active:hover > a {
    background: linear-gradient(135deg, #FFB84D 0%, #FF9900 100%) !important;
}

.dataTables_paginate .paginate_button.disabled > a {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* Custom bottom row layout (info + pagination) */
#sell_table_wrapper .bottom-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
}

/* Ensure pagination UL has no extra margins or list styling */
#sell_table_wrapper .dataTables_paginate .pagination {
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    gap: 2px;
}

/* Hide dt-buttons that might leak into DOM */
#sell_table_wrapper .dt-buttons {
    display: none !important;
}

/* Filter Modal Amazon Style */
#filterModal .modal-header {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    color: white;
    border-bottom: none;
    padding: 12px 16px;
}

#filterModal .modal-title {
    color: white;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

#filterModal .close {
    color: white;
    opacity: 1;
    font-size: 24px;
}

#filterModal .modal-body {
    padding: 20px;
}

#filterModal .modal-footer {
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
    padding: 12px 16px;
}

/* Widget override */
.box.box-primary {
    border: none;
    box-shadow: none;
    background: transparent;
}

.box-primary > .box-header {
    display: none;
}

.box-body {
    padding: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .amazon-page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .amazon-header-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .amazon-controls-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .amazon-controls-left,
    .amazon-controls-right {
        width: 100%;
        justify-content: flex-start;
    }
    
    .amazon-search-wrapper {
        max-width: none;
        width: 100%;
    }
}
</style>
@endsection

@section('content')
<div class="amazon-sales-container">
    <!-- Top banner – same style as Add new product / Manage Order -->
    <div class="amazon-sales-header-banner">
        <div class="amazon-sales-header-content">
            <h1 class="amazon-sales-header-title">
                <i class="fas fa-file-invoice"></i>
                @lang('lang_v1.sales_order')
            </h1>
            <p class="amazon-sales-header-subtitle">
                View and manage sales orders. Track status, payment, and shipping. Create new orders or go to Manage Orders for fulfillment.
            </p>
        </div>
    </div>

    <!-- Action Buttons Row (below banner) -->
    <div class="amazon-page-header">
        <div class="amazon-header-actions" style="margin-left: auto;">
            @can('so.create')
            <a class="amazon-btn amazon-btn-primary" href="{{action([\App\Http\Controllers\SalesOrderController::class, 'create'])}}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Sales Order
            </a>
            <a class="amazon-btn amazon-btn-info" href="{{action([\App\Http\Controllers\OrderfulfillmentController::class, 'index'])}}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1.51-1 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1-1.51 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                Manage Orders
            </a>
            @endcan
        </div>
    </div>

    <!-- Filter Modal - Amazon Styled -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        @lang('report.filters')
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sell_list_filter_location_id', __('purchase.business_location') . ':') !!}
                                {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sell_list_filter_customer_id', __('contact.customer') . ':') !!}
                                {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('so_list_filter_status', __('sale.status') . ':') !!}
                                {!! Form::select('so_list_filter_status', $sales_order_statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sell_list_filter_payment_status', __('purchase.payment_status') . ':') !!}
                                {!! Form::select('sell_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                            </div>
                        </div>
                        @if(!empty($shipping_statuses))
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('so_list_shipping_status', __('lang_v1.shipping_status') . ':') !!}
                                {!! Form::select('so_list_shipping_status', $shipping_statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                                {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'style' => 'border-radius: 4px;']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="amazon-card">
        <!-- Controls Bar -->
        <div class="amazon-controls-bar">
            <div class="amazon-controls-left">
                <div class="amazon-entries-select">
                    <span>Show</span>
                    <select id="amazonEntriesSelect">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="amazon-search-wrapper">
                    <svg class="amazon-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="amazon-search-input" id="amazonSearchInput" placeholder="Search orders...">
                </div>
            </div>
            <div class="amazon-controls-right">
                <button class="amazon-btn amazon-btn-secondary" id="amazonFilterBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Filters
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonCsvBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Export CSV
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonExcelBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="M8 13h2"></path>
                        <path d="M8 17h2"></path>
                        <path d="M14 13h2"></path>
                        <path d="M14 17h2"></path>
                    </svg>
                    Export Excel
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonPrintBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print
                </button>
                <div class="colvis-dropdown-wrapper">
                    <button class="amazon-btn amazon-btn-secondary" id="amazonColVisBtn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Column Visibility
                    </button>
                    <div class="colvis-dropdown" id="colvisDropdown">
                        <div class="colvis-dropdown-header">Toggle Columns</div>
                        <div class="colvis-dropdown-body" id="colvisDropdownBody">
                            <!-- Columns will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        @if(auth()->user()->can('so.view_own') || auth()->user()->can('so.view_all'))
        <div class="amazon-table-wrapper">
            <table class="amazon-table table table-bordered table-striped ajax_view hide-footer" id="sell_table" style="min-width: max-content;">
                <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('restaurant.order_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.contact_no')</th>
                        <th>Payment Status</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('sale.status')</th>
                        <th>@lang('lang_v1.shipping_status')</th>
                        <th>@lang('lang_v1.quantity_remaining')</th>
                        <th>@lang('lang_v1.added_by')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
        @endif
    </div>

    <div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
</div>
@stop

@section('javascript')
@includeIf('sales_order.common_js')
<script type="text/javascript">
    $(document).ready(function(){
        // Date Range Picker
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_table.ajax.reload();
        });

        // Initialize DataTable
        sell_table = $('#sell_table').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            scrollY: "60vh",
            scrollX: true,
            fixedHeader: false,
            aaSorting: [[0, 'desc']],
            pageLength: 25,
            pagingType: 'simple_numbers',
            dom: 'rt<"bottom-row"ip>',
            "ajax": {
                "url": '/sells?sale_type=sales_order',
                "data": function (d) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();
                    if ($('#so_list_filter_status').length) {
                        d.status = $('#so_list_filter_status').val();
                    }
                    if ($('#so_list_shipping_status').length) {
                        d.shipping_status = $('#so_list_shipping_status').val();
                    }
                    if($('#sell_list_filter_payment_status').length) {
                        d.payment_status = $('#sell_list_filter_payment_status').val();
                    }
                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [{
                "targets": 7,
                "orderable": false,
                "searchable": false
            }],
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'conatct_name', name: 'conatct_name' },
                { data: 'mobile', name: 'contacts.mobile', visible: false },
                { data: 'payment_status', name: 'payment_status' },
                { data: 'business_location', name: 'bl.name' },
                { data: 'status', name: 'status' },
                { data: 'shipping_status', name: 'shipping_status' },
                { data: 'so_qty_remaining', name: 'so_qty_remaining', "searchable": false },
                { data: 'added_by', name: 'u.first_name' },
                { data: 'action', name: 'action', orderable: false, "searchable": false },
            ],
            drawCallback: function() {
                $('#sell_table_wrapper .dt-buttons').hide();
            },
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file-csv"></i> CSV',
                    className: 'hidden-btn',
                    exportOptions: { columns: ':visible' },
                    footer: true,
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'hidden-btn',
                    exportOptions: { columns: ':visible' },
                    footer: true,
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'hidden-btn',
                    exportOptions: { columns: ':visible', stripHtml: true },
                    footer: true,
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i> Columns',
                    className: 'hidden-btn',
                },
            ],
        });

        // === Custom Amazon Button Handlers ===
        
        // Search Input
        $('#amazonSearchInput').on('keyup', function() {
            sell_table.search($(this).val()).draw();
        });

        // Entries Select
        $('#amazonEntriesSelect').on('change', function() {
            sell_table.page.len($(this).val()).draw();
        });

        // Filter Button
        $('#amazonFilterBtn').on('click', function() {
            $('#filterModal').modal('show');
        });

        // Export CSV
        $('#amazonCsvBtn').on('click', function() {
            sell_table.button('.buttons-csv').trigger();
        });

        // Export Excel
        $('#amazonExcelBtn').on('click', function() {
            sell_table.button('.buttons-excel').trigger();
        });

        // Print
        $('#amazonPrintBtn').on('click', function() {
            sell_table.button('.buttons-print').trigger();
        });

        // Column Visibility - Custom Dropdown
        function buildColVisDropdown() {
            var $body = $('#colvisDropdownBody');
            $body.empty();
            
            sell_table.columns().every(function(index) {
                var column = this;
                var header = $(column.header()).text().trim();
                if (header) {
                    var isVisible = column.visible();
                    var $item = $('<div class="colvis-item' + (isVisible ? ' active' : '') + '" data-column="' + index + '">' +
                        '<div class="colvis-checkbox"></div>' +
                        '<span class="colvis-label">' + header + '</span>' +
                    '</div>');
                    $body.append($item);
                }
            });
        }
        
        // Toggle dropdown
        $('#amazonColVisBtn').on('click', function(e) {
            e.stopPropagation();
            var $dropdown = $('#colvisDropdown');
            
            if ($dropdown.hasClass('show')) {
                $dropdown.removeClass('show');
            } else {
                buildColVisDropdown();
                $dropdown.addClass('show');
            }
        });
        
        // Handle column toggle
        $(document).on('click', '.colvis-item', function(e) {
            e.stopPropagation();
            var $item = $(this);
            var colIndex = $item.data('column');
            var column = sell_table.column(colIndex);
            var isVisible = column.visible();
            
            // Toggle visibility
            column.visible(!isVisible);
            
            // Update UI
            $item.toggleClass('active');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.colvis-dropdown-wrapper').length) {
                $('#colvisDropdown').removeClass('show');
            }
        });

        // Filter change handlers
        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by, #so_list_filter_status, #so_list_shipping_status, #sell_list_filter_payment_status', function() {
            sell_table.ajax.reload();
        });

        // Keyboard shortcuts
        var lastFocusedRow = null;
        
        $(document).on("focus", "tr.sell-line-row input", function() {
            lastFocusedRow = $(this).closest('tr.sell-line-row');
        });

        $(document).on("keydown", function(e) {
            if (e.key === 'H' || e.key === 'h') {
                var focusedElement = document.activeElement;
                if(focusedElement.tagName != 'INPUT' && focusedElement.tagName != 'SELECT' || focusedElement.type == 'checkbox'){
                    e.preventDefault();
                    $('.product_history').trigger('click');
                }
            }
        });
        
        $(document).on("keydown", function (e) {
            if (e.shiftKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
                e.preventDefault();
                
                var table = $('#sellsModalTable');
                if (!table.length) return false;
                
                var focusedElement = document.activeElement;
                var currentRow = $(focusedElement).closest('tr.sell-line-row');
                
                if (!currentRow || currentRow.length === 0) {
                    currentRow = lastFocusedRow;
                }
                
                if (!currentRow || currentRow.length === 0) {
                    var checkedCheckbox = table.find('tr.sell-line-row input[type="checkbox"]:checked');
                    if (!checkedCheckbox || checkedCheckbox.length === 0) {
                        var firstRow = table.find('tr.sell-line-row').first();
                        if (firstRow && firstRow.length > 0) {
                            table.find('tr.sell-line-row').each(function(){
                                var checkbox = $(this).find('input[type="checkbox"]');
                                if (checkbox.length > 0) checkbox.prop('checked', false);
                            });
                            var firstCheckbox = firstRow.find('input[type="checkbox"]');
                            if (firstCheckbox.length > 0) firstCheckbox.prop('checked', true);
                            lastFocusedRow = firstRow;
                            var firstInput = firstRow.find('input').first();
                            if (firstInput.length > 0) firstInput.focus();
                        }
                    } else {
                        currentRow = checkedCheckbox.closest('tr.sell-line-row');
                        if (currentRow && currentRow.length > 0) lastFocusedRow = currentRow;
                    }
                    return false;
                }
                
                if (currentRow && currentRow.length > 0) {
                    var targetRow;
                    
                    if (e.key === 'ArrowUp') {
                        targetRow = currentRow.prev('tr.sell-line-row');
                        if (!targetRow || targetRow.length === 0) {
                            targetRow = table.find('tr.sell-line-row').last();
                        }
                    } else {
                        targetRow = currentRow.next('tr.sell-line-row');
                        if (!targetRow || targetRow.length === 0) {
                            targetRow = table.find('tr.sell-line-row').first();
                        }
                    }
                    
                    if (targetRow && targetRow.length > 0) {
                        table.find('tr.sell-line-row').each(function(){
                            var checkbox = $(this).find('input[type="checkbox"]');
                            if (checkbox.length > 0) checkbox.prop('checked', false);
                        });
                        
                        var targetCheckbox = targetRow.find('input[type="checkbox"]');
                        if (targetCheckbox.length > 0) targetCheckbox.prop('checked', true);
                        
                        lastFocusedRow = targetRow;
                        
                        setTimeout(function() {
                            var targetInput = targetRow.find('input').first();
                            if (targetInput && targetInput.length > 0) targetInput.focus();
                        }, 10);
                    }
                }
                
                return false;
            }
        });
    });
</script>
@endsection

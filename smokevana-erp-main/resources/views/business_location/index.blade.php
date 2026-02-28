@extends('layouts.app')
@section('title', __('business.business_locations'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* ========== Business Locations – Amazon look & feel ========== */
.business-locations-page {
    background: #EAEDED;
    min-height: 100%;
    padding-bottom: 2rem;
}
/* Top banner – same style as Contact/Customer list */
.business-locations-page .bl-header-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 10px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.business-locations-page .bl-header-banner .banner-inner {
    display: flex;
    align-items: center;
    gap: 18px;
}
.business-locations-page .bl-header-banner .banner-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.business-locations-page .bl-header-banner .banner-text { display: flex; flex-direction: column; gap: 6px; }
.business-locations-page .bl-header-banner .banner-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    color: #fff;
}
.business-locations-page .bl-header-banner .banner-subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.78);
    margin: 0;
}

/* Locations card – white panel */
.business-locations-page .box-primary {
    background: #ffffff !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 5px rgba(15, 17, 17, 0.08) !important;
    overflow: hidden;
}
.business-locations-page .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important;
    border-bottom: 2px solid #ff9900 !important;
    padding: 16px 24px !important;
}
.business-locations-page .box-title {
    color: #fff !important;
    font-weight: 600;
}
.business-locations-page #dynamic_button {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important;
    color: #0f1111 !important;
    font-weight: 600;
    border-radius: 8px;
    padding: 10px 20px;
    box-shadow: 0 2px 5px rgba(15, 17, 17, 0.15);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.business-locations-page #dynamic_button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15, 17, 17, 0.2);
    color: #0f1111 !important;
}

/* DataTable wrapper & controls */
.business-locations-page #business_location_table_wrapper {
    padding: 16px 24px;
    background: #fff;
}
.business-locations-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    padding: 8px 12px;
}
.business-locations-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
}
.business-locations-page .dataTables_wrapper .dataTables_length select {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
}
.business-locations-page .dataTables_wrapper .dataTables_info,
.business-locations-page .dataTables_wrapper .dataTables_filter label {
    color: #565959;
}

/* Table – Amazon header & rows */
.business-locations-page #business_location_table thead th {
    background: #232f3e !important;
    color: #fff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
    font-weight: 600;
    font-size: 13px;
}
.business-locations-page #business_location_table tbody td {
    padding: 12px 14px;
    color: #0f1111;
    border-color: #e5e7eb;
    font-size: 13px;
}
.business-locations-page #business_location_table tbody tr:nth-child(even) td {
    background: #f9fafb !important;
}
.business-locations-page #business_location_table tbody tr:hover td {
    background: #fff8e7 !important;
}

/* Action buttons in table */
.business-locations-page #business_location_table .btn-warning,
.business-locations-page #business_location_table .btn-edit-location {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #a88734 !important;
    color: #0f1111 !important;
    border-radius: 6px;
    font-weight: 500;
}
.business-locations-page #business_location_table .btn-info,
.business-locations-page #business_location_table .btn-settings-location {
    background: #37475a !important;
    border-color: #4a5d6e !important;
    color: #fff !important;
    border-radius: 6px;
}
.business-locations-page #business_location_table .btn-danger,
.business-locations-page #business_location_table .btn-deactivate-location {
    background: #c45500 !important;
    border-color: #9a3e00 !important;
    color: #fff !important;
    border-radius: 6px;
}
.business-locations-page #business_location_table tbody .btn {
    transition: transform 0.1s ease, box-shadow 0.1s ease;
}
.business-locations-page #business_location_table tbody .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

/* Pagination */
.business-locations-page .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px;
    border: 1px solid #D5D9D9;
}
.business-locations-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important;
    color: #0f1111 !important;
}
.business-locations-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: #ff9900;
    background: #fff8e7 !important;
    color: #0f1111;
}

/* Modals */
.business-locations-page .location_add_modal .modal-content,
.business-locations-page .location_edit_modal .modal-content {
    border: 1px solid #D5D9D9;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(15, 17, 17, 0.15);
}
.business-locations-page .location_add_modal .modal-header,
.business-locations-page .location_edit_modal .modal-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    border-bottom: 2px solid #ff9900 !important;
    color: #fff !important;
}
.business-locations-page .location_add_modal .modal-title,
.business-locations-page .location_edit_modal .modal-title {
    color: #fff !important;
}
.business-locations-page .location_add_modal .modal-header .close,
.business-locations-page .location_edit_modal .modal-header .close {
    color: #fff !important;
    opacity: 0.9;
}
.business-locations-page .location_add_modal .modal-header .close:hover,
.business-locations-page .location_edit_modal .modal-header .close:hover {
    color: #ff9900 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page business-locations-page">
    <!-- Amazon-style banner -->
    <div class="bl-header-banner amazon-theme-banner">
        <div class="banner-inner">
            <div class="banner-icon"><i class="fa fa-map-marker-alt"></i></div>
            <div class="banner-text">
                <h1 class="banner-title">@lang('business.business_locations')</h1>
                <p class="banner-subtitle">@lang('business.manage_your_business_locations')</p>
            </div>
        </div>
    </div>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'business.all_your_business_locations' )])
        @slot('tool')
            <div class="box-tools">
                <button id="dynamic_button" class="tw-dw-btn pull-right tw-mb-2 btn-modal"
                    data-href="{{ action([\App\Http\Controllers\BusinessLocationController::class, 'create']) }}"
                    data-container=".location_add_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="business_location_table">
                <thead>
                    <tr>
                        <th>@lang( 'invoice.name' )</th>
                        <th>@lang( 'lang_v1.location_id' )</th>
                        <th>@lang( 'business.landmark' )</th>
                        <th>@lang( 'business.city' )</th>
                        <th>@lang( 'business.zip_code' )</th>
                        <th>@lang( 'business.state' )</th>
                        <th>@lang( 'business.country' )</th>
                        <th>@lang( 'lang_v1.price_group' )</th>
                        <th>@lang( 'invoice.invoice_scheme' )</th>
                        <th>@lang('lang_v1.invoice_layout_for_pos')</th>
                        <th>@lang('lang_v1.invoice_layout_for_sale')</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade location_add_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade location_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
</div>
@endsection

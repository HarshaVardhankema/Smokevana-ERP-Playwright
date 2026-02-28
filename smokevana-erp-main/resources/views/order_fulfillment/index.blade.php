@extends('layouts.app')
@section('title', 'Order Fulfillment')

@section('css')
<style>
/* Amazon Theme - Order Fulfillment Page */
.amazon-orders-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 16px 20px;
}

/* Top banner – same style as Add new product / Roles / Sales Commission Agents */
.amazon-orders-header-banner {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 28px;
    margin-bottom: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}

.amazon-orders-header-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.amazon-orders-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

.amazon-orders-header-title i {
    font-size: 22px;
    color: #ffffff;
}

.amazon-orders-header-subtitle {
    font-size: 13px;
    color: rgba(249, 250, 251, 0.88);
    margin: 0;
}

/* Page Header - Status Tabs Row (below banner) */
.amazon-page-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
    width: 100%;
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

/* Status Tabs - Amazon Style */
.amazon-status-tabs {
    display: flex;
    gap: 4px;
    width: 100%;
    background: #FFF;
    padding: 4px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #D5D9D9;
    flex-wrap: wrap;
}

.amazon-status-tab {
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #0F1111;
    background: transparent;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 6px;
}

.amazon-status-tab:hover {
    background: #F7FAFA;
    color: #C7511F;
    text-decoration: none;
}

.amazon-status-tab.active {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(228, 121, 17, 0.3);
}

.amazon-status-tab.active:hover {
    color: white;
}

.amazon-status-tab svg {
    width: 14px;
    height: 14px;
}

/* Status tab count badges (notification counts) */
.amazon-status-tab .order-count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
    margin-left: 4px;
}
.amazon-status-tab .order-count-badge.badge-pending { background: #d13212; color: #fff; }
.amazon-status-tab .order-count-badge.badge-processing { background: #e47911; color: #fff; }
.amazon-status-tab .order-count-badge.badge-packing { background: #232f3e; color: #fff; }
.amazon-status-tab .order-count-badge.badge-cancelled { background: #6b7280; color: #fff; }
.amazon-status-tab .order-count-badge.badge-completed { background: #067d62; color: #fff; }
.amazon-status-tab .order-count-badge.badge-preprocessing { background: #5a6268; color: #fff; }
.amazon-status-tab.active .order-count-badge { background: rgba(255,255,255,0.35) !important; color: #fff !important; }

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

/* Hover lift and glow effect */
.amazon-btn:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    z-index: 10;
}

.amazon-btn:active {
    transform: translateY(-1px) scale(1.01);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Focus state for accessibility */
.amazon-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.4);
}

/* SVG icon animation on hover */
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
    text-decoration: none;
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
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 15px rgba(255, 153, 0, 0.4);
}

.amazon-btn-info {
    background: linear-gradient(135deg, #008296 0%, #007185 50%, #006073 100%);
    border-color: #00545E;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-info:hover {
    background: linear-gradient(135deg, #00A8BD 0%, #008296 50%, #007185 100%);
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 113, 133, 0.4);
}

.amazon-btn-success {
    background: linear-gradient(135deg, #078E70 0%, #067D62 50%, #046854 100%);
    border-color: #035A48;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-success:hover {
    background: linear-gradient(135deg, #08A882 0%, #078E70 50%, #067D62 100%);
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 15px rgba(6, 125, 98, 0.4);
}

.amazon-btn-warning {
    background: linear-gradient(135deg, #FF9900 0%, #E47911 50%, #C7511F 100%);
    border-color: #A84400;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-warning:hover {
    background: linear-gradient(135deg, #FFB84D 0%, #FF9900 50%, #E47911 100%);
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 15px rgba(228, 121, 17, 0.4);
}

.amazon-btn-danger {
    background: linear-gradient(135deg, #E31351 0%, #CC0C39 50%, #A8002F 100%);
    border-color: #8C0026;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-danger:hover {
    background: linear-gradient(135deg, #F5305F 0%, #E31351 50%, #CC0C39 100%);
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 15px rgba(204, 12, 57, 0.4);
}

.amazon-btn-purple {
    background: linear-gradient(135deg, #7C3AED 0%, #6B46C1 50%, #553C9A 100%);
    border-color: #44337A;
    color: white;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.amazon-btn-purple:hover {
    background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 50%, #6B46C1 100%);
    text-decoration: none;
    color: white;
    box-shadow: 0 4px 15px rgba(107, 70, 193, 0.4);
}

/* Small button variant for action buttons */
.amazon-btn-sm {
    padding: 5px 10px;
    font-size: 11px;
    border-radius: 4px;
}

/* Button Group Styling */
.amazon-btn-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

/* Separator between button groups */
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
}

.amazon-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.amazon-table thead {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    position: sticky;
    top: 0;
    z-index: 10;
}

.amazon-table thead th {
    padding: 10px 12px;
    font-weight: 600;
    color: #FFF;
    text-align: left;
    border: none;
    white-space: nowrap;
    vertical-align: middle;
    border-right: 1px solid #37475A;
}

.amazon-table thead th:last-child {
    border-right: none;
}

/* Fix sorting icons */
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
    opacity: 0.7;
}

.amazon-table thead th.sorting::after {
    content: "⇅";
}

.amazon-table thead th.sorting_asc::after {
    content: "↑";
    opacity: 1;
    color: #FF9900;
}

.amazon-table thead th.sorting_desc::after {
    content: "↓";
    opacity: 1;
    color: #FF9900;
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

/* Hide original DataTable controls - we use custom Amazon buttons */
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
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
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

/* Progress Bar */
.progress {
    background-color: #d6d6d6;
    border-radius: 10px;
    height: 20px;
    width: 100%;
    overflow: hidden;
    box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.15);
}

.progress-bar {
    height: 100%;
    border-radius: 10px;
    text-align: center;
    font-weight: 600;
    font-size: 12px;
    color: #fff;
    background-image: linear-gradient(270deg, #ffbd92, #f60, #ffbd92);
    background-size: 200% 100%;
    background-position: 100% 0;
    animation: barFlow 3s linear infinite;
    transition: width 0.5s ease-in-out;
}

@keyframes barFlow {
    0% { background-position: 100% 0; }
    100% { background-position: 0 0; }
}

/* Qty Controls */
.qty-input-group {
    display: flex;
    align-items: center;
    justify-content: center;
}

.qty-btn {
    padding: 2px 8px;
    margin: 0 4px;
    border: 1px solid #ddd;
    background: #f8f9fa;
    cursor: pointer;
}

.qty-btn:hover {
    background: #e9ecef;
}

.inline-pick {
    width: 60px !important;
    text-align: center;
}

/* Searchable Select */
.searchable-select {
    position: relative;
    display: inline-block;
    width: 300px;
}

.searchable-select input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.searchable-select .dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    display: none;
    z-index: 1000;
}

.searchable-select .dropdown div {
    padding: 8px;
    cursor: pointer;
}

.searchable-select .dropdown div:hover {
    background-color: #f0f0f0;
}

.verify_picking_button {
    background-color: #00c0ef;
    color: #fff;
}

.start_picking_button {
    background-color: #f60;
    color: #fff;
}

/* Hide original nav-tabs - we use Amazon tabs */
.nav-tabs-custom > .nav-tabs {
    display: none !important;
}

/* Tab Content Styling */
.tab-content {
    padding: 0;
}

.tab-pane {
    padding: 0;
}

/* Filter Modal Amazon Style */
#filterModal .modal-header {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    color: white;
    border-bottom: none;
}

#filterModal .modal-title {
    color: white;
}

#filterModal .close {
    color: white;
    opacity: 1;
}

#filterModal .modal-footer {
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
}

/* DataTables Info and Pagination */
.dataTables_info {
    font-size: 13px;
    color: #565959;
    padding: 12px 16px;
}

.dataTables_paginate {
    padding: 12px 16px;
}

.dataTables_paginate .paginate_button {
    padding: 6px 12px !important;
    margin: 0 2px;
    border-radius: 4px !important;
    border: 1px solid #D5D9D9 !important;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%) !important;
    color: #0F1111 !important;
}

.dataTables_paginate .paginate_button:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%) !important;
    border-color: #BBBFBF !important;
}

.dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: white !important;
}

/* Hide empty pagination buttons and ellipsis */
.dataTables_paginate .paginate_button.ellipsis,
.dataTables_paginate .paginate_button:empty,
.dataTables_paginate .paginate_button.disabled:not(.previous):not(.next):not(.current) {
    display: none !important;
}

/* Hide pagination buttons with empty content or just whitespace */
.dataTables_paginate .paginate_button:not(.previous):not(.next):not(.current) a:empty,
.dataTables_paginate .paginate_button:not(.previous):not(.next):not(.current):not(.ellipsis) span:empty {
    display: none !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .amazon-page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .amazon-status-tabs {
        width: 100%;
        overflow-x: auto;
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
<div class="amazon-orders-container">
    <!-- Top banner – same style as Add new product / Roles / Sales Commission Agents -->
    <div class="amazon-orders-header-banner">
        <div class="amazon-orders-header-content">
            <h1 class="amazon-orders-header-title">
                <i class="fas fa-clipboard-list"></i>
                Manage Orders
            </h1>
            <p class="amazon-orders-header-subtitle">
                Track and fulfill orders through Preprocessing, Pending, Processing, Packing, Cancelled, and Completed stages.
            </p>
        </div>
    </div>

    <!-- Status Tabs (below banner) -->
    <div class="amazon-page-header">
        @php
            $order_counts = $order_counts ?? [
                'preprocessing' => 0, 'pending' => 0, 'processing' => 0,
                'packing' => 0, 'cancelled' => 0, 'completed' => 0
            ];
        @endphp
        <div class="amazon-status-tabs" id="amazonOrderTabs">
            <a href="#preprocessing_orders" class="amazon-status-tab" data-toggle="tab" id="amazon-tab-preprocessing">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                    <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                </svg>
                Preprocessing
                <span class="order-count-badge badge-preprocessing" id="order-count-preprocessing">{{ $order_counts['preprocessing'] }}</span>
            </a>
            <a href="#processing_orders" class="amazon-status-tab active" data-toggle="tab" id="amazon-tab-pending">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 6v6l4 2"></path>
                </svg>
                Pending
                <span class="order-count-badge badge-pending" id="order-count-pending">{{ $order_counts['pending'] }}</span>
            </a>
            <a href="#picking_orders" class="amazon-status-tab" data-toggle="tab" id="amazon-tab-processing">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1.51-1 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1-1.51 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                Processing
                <span class="order-count-badge badge-processing" id="order-count-processing">{{ $order_counts['processing'] }}</span>
            </a>
            <a href="#picked_orders" class="amazon-status-tab" data-toggle="tab" id="amazon-tab-packing">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                Packing
                <span class="order-count-badge badge-packing" id="order-count-packing">{{ $order_counts['packing'] }}</span>
            </a>
            <a href="#cancel_orders" class="amazon-status-tab" data-toggle="tab" id="amazon-tab-cancelled">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
                Cancelled
                <span class="order-count-badge badge-cancelled" id="order-count-cancelled">{{ $order_counts['cancelled'] }}</span>
            </a>
            <a href="#complete_orders" class="amazon-status-tab" data-toggle="tab" id="amazon-tab-completed">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Completed
                <span class="order-count-badge badge-completed" id="order-count-completed">{{ $order_counts['completed'] }}</span>
            </a>
        </div>
    </div>

    <!-- Filter Modal - Amazon Styled -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fa fa-filter" style="font-size: 18px; margin-right: 10px; vertical-align: -1px; padding: 6px; border-radius: 6px; background: rgba(255,255,255,0.10);"></i>
                        @lang('report.filters')
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                                {!! Form::text('sell_list_filter_date_range', null, [
                                    'id' => 'sell_list_filter_date_range',
                                    'placeholder' => __('lang_v1.select_a_date_range'),
                                    'class' => 'form-control',
                                    'readonly',
                                    'style' => 'border-color: #888C8C; border-radius: 4px;'
                                ]) !!}
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
            <div class="amazon-controls-right" id="amazonActionButtons">
                <!-- Standard Action Buttons - Like Customer Screen -->
                <button class="amazon-btn amazon-btn-secondary" id="amazonFilterBtn" title="Filter orders by date range">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Filters
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonCsvBtn" title="Export to CSV">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Export CSV
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonExcelBtn" title="Export to Excel">
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
                <button class="amazon-btn amazon-btn-secondary" id="amazonPrintBtn" title="Print table">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print
                </button>
                <div class="colvis-dropdown-wrapper">
                    <button class="amazon-btn amazon-btn-secondary" id="amazonColVisBtn" title="Toggle column visibility">
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
                        </div>
                    </div>
                </div>
                
                <span class="amazon-btn-separator" id="amazonSeparatorBeforeWorkflow"></span>
                
                <!-- Workflow Action Buttons -->
                <button class="amazon-btn amazon-btn-danger" id="amazonProcessingBtn" title="Move selected orders back to Processing" style="display: none;">
                    <i class="fa fa-arrow-left" style="font-size: 14px;"></i>
                    Processing
                </button>
                <button class="amazon-btn amazon-btn-danger" id="amazonPendingBtn" title="Move selected orders back to Pending" style="display: none;">
                    <i class="fa fa-spinner" style="font-size: 14px;"></i>
                    Pending
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonRefreshBtn" title="Refresh table" style="display: none;">
                    <i class="fa fa-retweet" style="font-size: 14px;"></i>
                    Refresh
                </button>
                <button class="amazon-btn amazon-btn-danger" id="amazonCancelledPendingBtn" title="Move selected orders back to Pending" style="display: none;">
                    <i class="fa fa-clock" style="font-size: 14px;"></i>
                    Pending
                </button>
                <button class="amazon-btn amazon-btn-success" id="amazonAssignPickerBtn" title="Assign picker to selected orders">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    Assign Picker
                </button>
                <button class="amazon-btn amazon-btn-info" id="amazonPriorityDownBtn" title="Decrease priority">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                    ↓Priority
                </button>
                <button class="amazon-btn amazon-btn-warning" id="amazonPriorityUpBtn" title="Increase priority">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="19" x2="12" y2="5"></line>
                        <polyline points="5 12 12 5 19 12"></polyline>
                    </svg>
                    ↑Priority
                </button>
                <button class="amazon-btn amazon-btn-info" id="amazonMakeShipmentBtn" title="Make shipment" style="display: none;">
                    <i class="fas fa-shipping-fast" style="font-size: 14px;"></i>
                    Make Shipment
                </button>
                <button class="amazon-btn amazon-btn-danger" id="amazonCancelBtn" title="Cancel selected orders">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    Cancel
                </button>
                <button class="amazon-btn amazon-btn-success" id="amazonHeldBtn" title="Held selected orders">
                    <i class="fa fa-arrow-up" style="font-size: 14px;"></i>
                    Held
                </button>
                @if (in_array(session()->get('business.manage_order_module'), ['manual', 'both']))
                <button class="amazon-btn amazon-btn-warning" id="amazonStartPickingBtn" title="Start picking for selected orders">
                    <i class="fas fa-dolly" style="font-size: 14px;"></i>
                    Start Picking
                </button>
                @endif
                @if (session()->get('business.manage_order_module') == 'both')
                <button class="amazon-btn amazon-btn-info" id="amazonVerifyPickingBtn" title="Verify picking for selected orders">
                    <i class="fa fa-search-plus" style="font-size: 14px;"></i>
                    Verify Picking
                </button>
                @endif
                <button class="amazon-btn amazon-btn-primary" id="amazonPackOrderBtn" title="Pack order and move to packing">
                    <i class="fa fa-box-open" style="font-size: 14px;"></i>
                    Pack Order
                </button>
                <button class="amazon-btn amazon-btn-purple" id="amazonBypassBtn" title="Bypass picking and move to packing">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="5 4 15 12 5 20 5 4"></polygon>
                        <line x1="19" y1="5" x2="19" y2="19"></line>
                    </svg>
                    BYPASS
                </button>
                
                <span class="amazon-btn-separator" id="amazonSeparatorBeforeHistory"></span>
                
                <!-- History and Guide buttons -->
                <button class="amazon-btn amazon-btn-info" id="so-history-button"
                    data-href="{{ action([\App\Http\Controllers\OrderfulfillmentController::class, 'history']) }}" title="View order history">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    History
                </button>
                <button class="amazon-btn amazon-btn-primary" id="guide-button" title="View keyboard shortcuts guide">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Guide
                </button>
            </div>
        </div>

        <!-- Hidden original tabs for compatibility -->
        <div class="nav-tabs-custom" style="margin: 0; border: none; box-shadow: none;">
            <ul class="nav nav-tabs" style="display: none !important;">
                <li>
                    <a href="#preprocessing_orders" data-toggle="tab" id="tab-preprocessing-orders">
                        <i class="fa fa-clipboard-list"></i> <strong>Preprocessing</strong>
                    </a>
                </li>
                <li class="active">
                    <a href="#processing_orders" data-toggle="tab" id="tab-processing-orders">
                        <i class="fa fa-spinner"></i> <strong> Pending </strong>
                    </a>
                </li>
                <li>
                    <a href="#picking_orders" data-toggle="tab" id="tab-picking-orders">
                        <i class="fa fa-cogs"></i> <strong>Processing</strong>
                    </a>
                </li>
                <li>
                    <a href="#picked_orders" data-toggle="tab" id="tab-picked-orders">
                        <i class="fa fa-box-open"></i> <strong>Packing</strong>
                    </a>
                </li>
                <li>
                    <a href="#cancel_orders" data-toggle="tab" id="tab-cancel-orders">
                        <i class="fa fa-ban"></i> <strong>Cancelled</strong>
                    </a>
                </li>
                <li>
                    <a href="#complete_orders" data-toggle="tab" id="tab-complete-orders">
                        <i class="fa fa-check-circle text-success"></i> <strong>Completed</strong>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                {{-- Preprocessing tab - Dropshipping workflow --}}
                <div class="tab-pane" style="overflow-x: auto; width: 100%;" id="preprocessing_orders">
                    <table style="border-collapse: collapse; width: 100%; min-width: max-content;"
                        class="table table-bordered table-striped" id="preprocessing-orders-table">
                        <thead style="white-space: nowrap;">
                            <tr>
                                <th><input type="checkbox" id="select-all-preprocessing"></th>
                                <th>Sales Order</th>
                                <th>Sale Date</th>
                                <th>Customer</th>
                                <th>Ordered Qty</th>
                                <th>Final Total</th>
                                <th>Payment Status</th>
                                <th>Paid Amount</th>
                                <th>Added By</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="tab-pane active" style="overflow-x: auto; width: 100%;" id="processing_orders">
                    <table style="border-collapse: collapse; width: 100%; min-width: max-content;"
                        class="table table-bordered table-striped " id="processing-orders-table">
                        <thead style="white-space: nowrap; ">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                @if (session()->get('business.manage_order_module') != 'manual')
                                    <th>Picking Status</th>
                                @endif
                                <th>Sales Order</th>
                                <th>Sale Date</th>
                                <th>Customer</th>
                                <th>Ordered Qty</th>
                                <th>Final Total</th>
                                <th>Payment Status</th>
                                <th>Paid Amount</th>
                                <th>Added By</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="tab-pane " style="overflow-x: auto; width: 100%;" id="picking_orders">
                    <table style="border-collapse: collapse; width: 100%; min-width: max-content;" id="picking-orders-table"
                        class="table table-bordered table-striped ">
                        <thead style="white-space: nowrap;">
                            <tr>
                                <th><input type="checkbox" id="select-all-2"></th>
                                @if (session()->get('business.manage_order_module') != 'manual')
                                    <th>Picking Status</th>
                                @endif
                                {{-- <th>Picked Qty</th> --}}
                                <th>Sales Order</th>
                                <th>Sale Date</th>
                                <th>Name</th>
                                <th>Final Total</th>
                                {{-- <th>Status</th> --}}
                                <th>Payment Status</th>
                                <th>Paid Amount</th>
                                <th>Picker </th>
                                @if (session()->get('business.manage_order_module') == 'both')
                                    <th>Verifier</th>
                                @endif
                                {{-- <th>Qty Amount</th> --}}
                                {{-- <th>Action</th> --}}
                                {{-- <th style="min-width: 100px">Priority</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="tab-pane " style="overflow-x: auto; width: 100%;" id="picked_orders">
                    <table style="border-collapse: collapse; width: 100%; min-width: max-content;" id="picked-orders-table"
                        class="table table-bordered table-striped ">
                        <thead style="white-space: nowrap;">
                            <tr>
                                <th><input type="checkbox" id="select-all-3"></th>
                                <th>Picking Status</th>
                                <th>Sales Order</th>
                                <th>Name</th>
                                <th>Final Total</th>
                                {{-- <th>Status</th> --}}
                                <th>Payment Status</th>
                                <th>Paid Amount</th>
                                <th>Sale Date</th>
                                <th>Picking Time</th>
                                <th>Picked Qty</th>
                                <th>Qty Amount</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>


                <div class="tab-pane " style="overflow-x: auto; width: 100%;" id="cancel_orders">
                    <table id="cancel-orders-table" class="table table-bordered table-striped nowrap"
                        style="width: 100% !important;">
                        <thead style="white-space: nowrap;">
                            <tr>
                                <th><input type="checkbox" id="select-all-4"></th>
                                <th>Sales Order</th>
                                <th>Name</th>
                                <th>Final Total</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Paid Amount</th>
                                <th>Sale Date</th>
                                <th>Picking Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane " style="overflow-x: auto; width: 100%;" id="complete_orders">
                    <table id="complete-orders-table" class="table table-bordered table-striped nowrap"
                        style="width: 100% !important;">
                        <thead style="white-space: nowrap;">
                            <tr>
                                <th><input type="checkbox" id="select-all-5"></th>
                                <th>Status</th>
                                <th>Picking Status</th>
                                <th>Sales Order</th>
                                <th>Name</th>
                                <th>Final Total</th>
                                <th>Payment Status</th>
                                <th>Paid Amount</th>
                                <th>Sale Date</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div id="AssignPickerButton" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close-modal pull-right tw-p-2" id="close_button"
                            style="border-radius: 5px">Close</button>
                        <h4 class="modal-title" id="modalTitle">Assign Picker</h4>
                    </div>
                    <div class="modal-body">
                        <div id="operation-section" class="tw-flex tw-justify-center">
                            @if (session()->get('business.manage_order_module') != 'manual')
                                <div class="searchable-select">
                                    <input type="text" id="order-action" placeholder="Select or search an option">
                                    <div class="dropdown" id="dropdown">
                                        @foreach ($picker as $v => $p)
                                            <div data-value="{{ $v }}">{{ $p }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <button id="apply-operation" class="btn btn-primary">Apply Operation</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="MarkPickedButton" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close-modal pull-right tw-p-2" id="close_button"
                            style="border-radius: 5px">Close</button>
                        <h4 class="modal-title" id="modalTitle">Assign Picker</h4>
                    </div>
                    <div class="modal-body">
                        <div id="operation-section">
                            <select id="picked-order-action" class="form-control">
                                {{-- @foreach ($picker as $v => $p) --}}
                                {{-- <option value={{ $v }}> {{ $p }}</option> --}}
                                <option value="PICKED">Mark as Picked</option>
                                {{-- @endforeach --}}
                            </select>
                            <button id="picked-apply-operation" class="btn btn-primary">Apply Operation</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="history_modal" class="modal fade" tabindex="-1" role="dialog">

        </div>
        <div id="held_modal" class="modal fade" tabindex="-1" role="dialog">

        </div>
        <div id="change-picking-status-form-modal" class="modal fade" tabindex="-1" role="dialog">

        </div>
        <div style="width: 100%; display:flex;justify-content:center">
            <div id="manual_pick_verify_modal" class="modal fade" tabindex="-1" role="dialog"></div>
        </div>
        <div id="modal_shipment_packing_modal" class="modal fade" tabindex="-1" role="dialog"></div>
        <div id="sell_pick_verify_data_modal" class="modal fade" tabindex="-1" role="dialog"></div>
        <div id="bypass_order_modal" class="modal fade" tabindex="-1" role="dialog"></div>

        <!-- Keyboard Shortcuts Guide Modal -->
        <div id="keyboard-shortcuts-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="keyboardShortcutsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding: 5px;">
                        <div class="tw-flex tw-justify-between tw-items-center tw-w-full">
                            <h4 class="modal-title" id="keyboardShortcutsModalLabel">
                                <i class="fas fa-keyboard"></i> Keyboard Shortcuts Guide
                            </h4>
                            <div class="tw-flex">
                                <button type="button" class="btn btn-primary" onclick="printShortcuts()">
                                    <i class="fas fa-print"></i> Print Guide
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times tw-text-red-500" ></i>
                                </button>
                            </div>
                        </div>
                       
                    </div>
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Tip:</strong> Use these keyboard shortcuts to navigate and operate the order fulfillment system more efficiently.
                                </div>
                            </div>
                        </div>

                        <!-- Table Navigation Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-table"></i> Table Navigation
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Shortcut</th>
                                                <th width="40%">Action</th>
                                                <th width="30%">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><kbd>↓</kbd> Arrow Down</td>
                                                <td>Move to next row</td>
                                                <td>Select and highlight the next row in the table</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>↑</kbd> Arrow Up</td>
                                                <td>Move to previous row</td>
                                                <td>Select and highlight the previous row in the table</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Space</kbd></td>
                                                <td>Toggle row selection</td>
                                                <td>Check/uncheck the current row's checkbox</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>Enter</kbd></td>
                                                <td>Toggle row selection</td>
                                                <td>Alternative way to check/uncheck current row</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>↓</kbd></td>
                                                <td>Move cursor only</td>
                                                <td>Move to next row without selecting it</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>↑</kbd></td>
                                                <td>Move cursor only</td>
                                                <td>Move to previous row without selecting it</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>↓</kbd></td>
                                                <td>Multi-select down</td>
                                                <td>Move to next row and add to selection</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>↑</kbd></td>
                                                <td>Multi-select up</td>
                                                <td>Move to previous row and add to selection</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Input Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-success">
                                    <i class="fas fa-calculator"></i> Quantity Input (Picking/Verification)
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Shortcut</th>
                                                <th width="40%">Action</th>
                                                <th width="30%">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><kbd>←</kbd> Left Arrow</td>
                                                <td>Decrease quantity</td>
                                                <td>Reduce picked/verified quantity by 1</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>→</kbd> Right Arrow</td>
                                                <td>Increase quantity</td>
                                                <td>Increase picked/verified quantity by 1</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Barcode Scanner Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-warning">
                                    <i class="fas fa-barcode"></i> Barcode Scanner
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Shortcut</th>
                                                <th width="40%">Action</th>
                                                <th width="30%">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>V</kbd></td>
                                                <td>Paste multiple barcodes</td>
                                                <td>Paste barcodes separated by newlines, tabs, commas, or spaces</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Enter</kbd></td>
                                                <td>Process barcode</td>
                                                <td>Process scanned barcode after typing</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Auto-focus</kbd></td>
                                                <td>Focus barcode input</td>
                                                <td>Barcode field automatically receives focus when modal opens</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Workflow Actions Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-danger">
                                    <i class="fas fa-tasks"></i> Workflow Actions (Shift Key Combinations)
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Shortcut</th>
                                                <th width="40%">Action</th>
                                                <th width="30%">Context</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><kbd>Shift</kbd> + <kbd>P</kbd></td>
                                                <td>Start picking selected order</td>
                                                <td>Picking Orders table (single row)</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Shift</kbd> + <kbd>V</kbd></td>
                                                <td>Start verifying selected order</td>
                                                <td>Picking Orders table (single row)</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Shift</kbd> + <kbd>C</kbd></td>
                                                <td>Cancel selected orders</td>
                                                <td>Processing Orders table (multiple rows)</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Shift</kbd> + <kbd>←</kbd></td>
                                                <td>Move orders to previous status</td>
                                                <td>Picking/Packed Orders tables</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Shift</kbd> + <kbd>→</kbd></td>
                                                <td>Move orders to next status</td>
                                                <td>Processing/Picking/Packed Orders tables</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Navigation Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-warning">
                                    <i class="fas fa-keyboard"></i> Tab Navigation
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Shortcut</th>
                                                <th width="40%">Action</th>
                                                <th width="30%">Context</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><kbd>←</kbd></td>
                                                <td>Switch to previous tab</td>
                                                <td>Main interface (when no input focused)</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>→</kbd></td>
                                                <td>Switch to next tab</td>
                                                <td>Main interface (when no input focused)</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- General System Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-info">
                                    <i class="fas fa-cogs"></i> General System
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="30%">Shortcut</th>
                                                <th width="40%">Action</th>
                                                <th width="30%">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><kbd>Esc</kbd></td>
                                                <td>Close modal</td>
                                                <td>Close any open modal dialog</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Tab</kbd></td>
                                                <td>Navigate fields</td>
                                                <td>Move between form fields and buttons</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>A</kbd></td>
                                                <td>Select all rows</td>
                                                <td>Select all rows in the current table</td>
                                            </tr>
                                            <tr>
                                                <td><kbd>Ctrl</kbd> + <kbd>F</kbd></td>
                                                <td>Search table</td>
                                                <td>Open table search/filter functionality</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Special Features Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-purple">
                                    <i class="fas fa-star"></i> Special Features
                                </h5>
                                <div class="alert alert-warning">
                                    <ul class="mb-0">
                                        <li><strong>Auto-Save:</strong> Quantity changes are automatically saved after 1 second delay</li>
                                        <li><strong>Smart Barcode Processing:</strong> Supports both barcode and SKU scanning with priority for incomplete lines</li>
                                        <li><strong>Focus Management:</strong> Automatic focus on barcode scanner input when modals open</li>
                                        <li><strong>Debounce Protection:</strong> Built-in delays prevent rapid-fire key presses</li>
                                        <li><strong>Role-Based Access:</strong> Some shortcuts are only available to users with specific permissions</li>
                                        <li><strong>Context-Aware Shortcuts:</strong> Shift key combinations are disabled when modals are open or input fields are focused</li>
                                        <li><strong>Batch Processing:</strong> Paste multiple barcodes separated by newlines, tabs, commas, or spaces</li>
                                        <li><strong>Smart Matching:</strong> Prioritizes incomplete lines when multiple barcodes match</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Important Notes
                                </h5>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <li><strong>Shift + P/V:</strong> Require exactly one row selected</li>
                                        <li><strong>Shift + C:</strong> Works with multiple selected rows</li>
                                        <li><strong>Arrow Navigation:</strong> Disabled when input fields are focused</li>
                                        <li><strong>Barcode Scanning:</strong> Automatically increments quantities within stock limits</li>
                                        <li><strong>Modal Context:</strong> Shift shortcuts are disabled when any modal is open</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Context-Specific Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary">
                                    <i class="fas fa-user"></i> Picker Mode
                                </h5>
                                <ul>
                                    <li>All quantity shortcuts work for picking operations</li>
                                    <li>Left/Right arrows adjust picked quantities</li>
                                    <li>Barcode scanning adds to picked quantities</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-success">
                                    <i class="fas fa-search"></i> Verifier Mode
                                </h5>
                                <ul>
                                    <li>All quantity shortcuts work for verification operations</li>
                                    <li>Left/Right arrows adjust verified quantities</li>
                                    <li>Additional checkbox controls for marking items as short</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
@php
    $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
@endphp
    <script type="text/javascript">
        $(document).ready(function() {

            var currentTable = processingOrdersTable;

            // Amazon Tab Click Handler - Sync with original tabs
            $('#amazonOrderTabs .amazon-status-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                
                // Update Amazon tabs active state
                $('#amazonOrderTabs .amazon-status-tab').removeClass('active');
                $(this).addClass('active');
                
                // Trigger original tab click
                $('.nav-tabs a[href="' + target + '"]').trigger('click');
                
                // Update currentTable reference based on active tab
                switch(target) {
                    case '#preprocessing_orders':
                        currentTable = preprocessingOrdersTable;
                        break;
                    case '#processing_orders':
                        currentTable = processingOrdersTable;
                        break;
                    case '#picking_orders':
                        currentTable = pickingOrdersTable;
                        break;
                    case '#picked_orders':
                        currentTable = pickedOrdersTable;
                        break;
                    case '#cancel_orders':
                        currentTable = cancelOrdersTable;
                        break;
                    case '#complete_orders':
                        currentTable = completeOrdersTable;
                        break;
                }
            });

            // Refresh order count badges from server (used after table reloads)
            function refreshOrderCounts() {
                $.get('/order-counts').done(function(counts) {
                    $('#order-count-preprocessing').text(counts.preprocessing != null ? counts.preprocessing : 0);
                    $('#order-count-pending').text(counts.pending != null ? counts.pending : 0);
                    $('#order-count-processing').text(counts.processing != null ? counts.processing : 0);
                    $('#order-count-packing').text(counts.packing != null ? counts.packing : 0);
                    $('#order-count-cancelled').text(counts.cancelled != null ? counts.cancelled : 0);
                    $('#order-count-completed').text(counts.completed != null ? counts.completed : 0);
                }).fail(function() { /* ignore */ });
            }

            // Amazon Search - Connect to DataTable search
            $('#amazonSearchInput').on('keyup', function() {
                if (currentTable) {
                    currentTable.search($(this).val()).draw();
                }
            });

            // Amazon Entries Select - Connect to DataTable page length
            $('#amazonEntriesSelect').on('change', function() {
                if (currentTable) {
                    currentTable.page.len($(this).val()).draw();
                }
            });

            // Function to clean up empty/unstyled pagination controls
            function removeEmptyPaginationButtons() {
                // First, remove entire pagination containers that have no visible text at all
                $('.dataTables_paginate').each(function() {
                    var $container = $(this);
                    if ($container.text().trim() === '') {
                        $container.remove();
                    }
                });

                // Then, remove individual empty buttons (extra blank squares)
                $('.dataTables_paginate .paginate_button').each(function() {
                    var $button = $(this);
                    var text = $button.text().trim();
                    
                    // Remove buttons that are not previous/next/current/ellipsis and have no label
                    if (
                        !$button.hasClass('previous') && 
                        !$button.hasClass('next') && 
                        !$button.hasClass('current') && 
                        !$button.hasClass('ellipsis') &&
                        text === ''
                    ) {
                        $button.remove();
                    }
                });
            }

            // Sync original tab changes with Amazon tabs
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                $('#amazonOrderTabs .amazon-status-tab').removeClass('active');
                $('#amazonOrderTabs .amazon-status-tab[href="' + target + '"]').addClass('active');
            });

            // Pending: Assign Picker, Priority, Cancel, Bypass (+ Filters, CSV, Excel, Print, ColVis, History, Guide). Hide Held, Start Picking, Verify Picking, Pack Order, Pending, Refresh, Processing, Make Shipment, Cancelled Pending.
            // Processing: Filters, CSV, Excel, Print, ColVis, Pending, Refresh, ↓↑Priority, Held, Start Picking, Verify Picking, Pack Order. Hide Assign Picker, Cancel, Bypass, History, Guide, Processing, Make Shipment, Cancelled Pending.
            // Packing: Filters, CSV, Excel, Print, ColVis, Processing (back), ↓↑Priority, Make Shipment, Cancel. Hide the rest.
            // Cancelled: Filters, CSV, Excel, Print, ColVis, Pending (back to Pending), ↓↑Priority. Hide all other workflow, History, Guide.
            // Complete: only Filters, Export CSV, Export Excel, Print, Column visibility. Hide all workflow, History, Guide.
            // Preprocessing: only Filters, Export CSV, Export Excel, Print, Column visibility. Hide all workflow, History, Guide.
            function toggleWorkflowButtonsForTab(target) {
                var isPending = (target === '#processing_orders');
                var isProcessing = (target === '#picking_orders');
                var isPacking = (target === '#picked_orders');
                var isCancelled = (target === '#cancel_orders');
                var isComplete = (target === '#complete_orders');
                var isPreprocessing = (target === '#preprocessing_orders');
                // On Complete: hide all workflow, separators, History/Guide; only Filters, CSV, Excel, Print, ColVis stay
                if (isComplete) {
                    $('#amazonSeparatorBeforeWorkflow, #amazonProcessingBtn, #amazonPendingBtn, #amazonRefreshBtn, #amazonCancelledPendingBtn, #amazonAssignPickerBtn, #amazonPriorityDownBtn, #amazonPriorityUpBtn, #amazonMakeShipmentBtn, #amazonCancelBtn, #amazonHeldBtn, #amazonStartPickingBtn, #amazonVerifyPickingBtn, #amazonPackOrderBtn, #amazonBypassBtn, #amazonSeparatorBeforeHistory, #so-history-button, #guide-button').hide();
                    return;
                }
                // On Preprocessing: only Filters, CSV, Excel, Print, ColVis (same as Complete)
                if (isPreprocessing) {
                    $('#amazonSeparatorBeforeWorkflow, #amazonProcessingBtn, #amazonPendingBtn, #amazonRefreshBtn, #amazonCancelledPendingBtn, #amazonAssignPickerBtn, #amazonPriorityDownBtn, #amazonPriorityUpBtn, #amazonMakeShipmentBtn, #amazonCancelBtn, #amazonHeldBtn, #amazonStartPickingBtn, #amazonVerifyPickingBtn, #amazonPackOrderBtn, #amazonBypassBtn, #amazonSeparatorBeforeHistory, #so-history-button, #guide-button').hide();
                    return;
                }
                // On Cancelled: only Filters, CSV, Excel, Print, ColVis, Pending (back to Pending), ↓↑Priority
                if (isCancelled) {
                    $('#amazonSeparatorBeforeWorkflow').show();
                    $('#amazonCancelledPendingBtn, #amazonPriorityDownBtn, #amazonPriorityUpBtn').show();
                    $('#amazonProcessingBtn, #amazonPendingBtn, #amazonRefreshBtn, #amazonAssignPickerBtn, #amazonMakeShipmentBtn, #amazonCancelBtn, #amazonHeldBtn, #amazonStartPickingBtn, #amazonVerifyPickingBtn, #amazonPackOrderBtn, #amazonBypassBtn, #amazonSeparatorBeforeHistory, #so-history-button, #guide-button').hide();
                    return;
                }
                $('#amazonSeparatorBeforeWorkflow').show();
                $('#amazonCancelledPendingBtn').hide();
                // Held, Start Picking, Verify Picking, Pack Order: hide on Pending and Packing
                $('#amazonHeldBtn').toggle(!isPending && !isPacking);
                $('#amazonStartPickingBtn').toggle(!isPending && !isPacking);
                $('#amazonVerifyPickingBtn').toggle(!isPending && !isPacking);
                $('#amazonPackOrderBtn').toggle(!isPending && !isPacking);
                // Pending, Refresh: show only on Processing
                $('#amazonPendingBtn').toggle(isProcessing);
                $('#amazonRefreshBtn').toggle(isProcessing);
                // Processing (back to Processing), Make Shipment: show only on Packing
                $('#amazonProcessingBtn').toggle(isPacking);
                $('#amazonMakeShipmentBtn').toggle(isPacking);
                // Assign Picker, Bypass, separator, History, Guide: hide on Processing and Packing
                $('#amazonAssignPickerBtn').toggle(!isProcessing && !isPacking);
                $('#amazonBypassBtn').toggle(!isProcessing && !isPacking);
                $('#amazonSeparatorBeforeHistory').toggle(!isProcessing && !isPacking);
                $('#so-history-button').toggle(!isProcessing && !isPacking);
                $('#guide-button').toggle(!isProcessing && !isPacking);
                // Cancel: hide only on Processing (show on Pending, Packing, and other tabs)
                $('#amazonCancelBtn').toggle(!isProcessing);
                // Priority: show on Pending, Processing, Packing (and Preprocessing, Cancelled); hidden only on Complete (handled above)
                $('#amazonPriorityDownBtn, #amazonPriorityUpBtn').show();
            }

            // === Custom Amazon Button Handlers ===
            
            // Filter Button
            $('#amazonFilterBtn').on('click', function() {
                $('#filterModal').modal('show');
            });

            // Export CSV Button
            $('#amazonCsvBtn').on('click', function() {
                if (currentTable) {
                    // Find the DataTable's CSV button and click it
                    var csvBtn = $(currentTable.table().container()).find('.buttons-csv');
                    if (csvBtn.length) {
                        csvBtn.trigger('click');
                    } else {
                        // Fallback: export manually
                        currentTable.button('.buttons-csv:eq(0)').trigger();
                    }
                }
            });

            // Export Excel Button
            $('#amazonExcelBtn').on('click', function() {
                if (currentTable) {
                    var excelBtn = $(currentTable.table().container()).find('.buttons-excel');
                    if (excelBtn.length) {
                        excelBtn.trigger('click');
                    } else {
                        currentTable.button('.buttons-excel:eq(0)').trigger();
                    }
                }
            });

            // Print Button
            $('#amazonPrintBtn').on('click', function() {
                if (currentTable) {
                    var printBtn = $(currentTable.table().container()).find('.buttons-print');
                    if (printBtn.length) {
                        printBtn.trigger('click');
                    } else {
                        currentTable.button('.buttons-print:eq(0)').trigger();
                    }
                }
            });

            // Column Visibility - Custom Dropdown
            function buildColVisDropdown() {
                var $body = $('#colvisDropdownBody');
                $body.empty();
                
                if (currentTable) {
                    currentTable.columns().every(function(index) {
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
            }
            
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
            
            $(document).on('click', '.colvis-item', function(e) {
                e.stopPropagation();
                var $item = $(this);
                var colIndex = $item.data('column');
                
                if (currentTable) {
                    var column = currentTable.column(colIndex);
                    column.visible(!column.visible());
                    $item.toggleClass('active');
                }
            });
            
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.colvis-dropdown-wrapper').length) {
                    $('#colvisDropdown').removeClass('show');
                }
            });

            // Pending Button (Processing: move selected back to Pending)
            $('#amazonPendingBtn').on('click', function() {
                var backwardBtn = $(currentTable.table().container()).find('.backward-button');
                if (backwardBtn.length) {
                    backwardBtn.trigger('click');
                }
            });
            // Processing Button (Packing: move selected back to Processing)
            $('#amazonProcessingBtn').on('click', function() {
                var backwardBtn = $(currentTable.table().container()).find('.backward-button1');
                if (backwardBtn.length) {
                    backwardBtn.trigger('click');
                }
            });
            // Cancelled Pending Button (Cancelled: move selected back to Pending)
            $('#amazonCancelledPendingBtn').on('click', function() {
                var backwardBtn = $(currentTable.table().container()).find('.backward-button2');
                if (backwardBtn.length) {
                    backwardBtn.trigger('click');
                }
            });
            // Make Shipment Button (Packing)
            $('#amazonMakeShipmentBtn').on('click', function() {
                var btn = $(currentTable.table().container()).find('.make_shipment, .complete_order');
                if (btn.length) {
                    btn.first().trigger('click');
                }
            });
            // Refresh Button (Processing)
            $('#amazonRefreshBtn').on('click', function() {
                if (currentTable) {
                    currentTable.ajax.reload();
                }
            });
            // Assign Picker Button
            $('#amazonAssignPickerBtn').on('click', function() {
                // Trigger the same action as the original assign picker button
                var assignBtn = $(currentTable.table().container()).find('.assign_picker_button');
                if (assignBtn.length) {
                    assignBtn.trigger('click');
                } else {
                    $('#AssignPickerButton').modal('show');
                }
            });

            // Priority Down Button
            $('#amazonPriorityDownBtn').on('click', function() {
                var decreaseBtn = $(currentTable.table().container()).find('.decrease_priority_button');
                if (decreaseBtn.length) {
                    decreaseBtn.trigger('click');
                }
            });

            // Priority Up Button
            $('#amazonPriorityUpBtn').on('click', function() {
                var increaseBtn = $(currentTable.table().container()).find('.increase_priority_button');
                if (increaseBtn.length) {
                    increaseBtn.trigger('click');
                }
            });

            // Cancel Button
            $('#amazonCancelBtn').on('click', function() {
                var cancelBtn = $(currentTable.table().container()).find('.pending_cancel_button');
                if (cancelBtn.length) {
                    cancelBtn.trigger('click');
                }
            });

            // Held Button
            $('#amazonHeldBtn').on('click', function() {
                var heldBtn = $(currentTable.table().container()).find('.held_button');
                if (heldBtn.length) {
                    heldBtn.trigger('click');
                }
            });

            // Bypass Button
            $('#amazonBypassBtn').on('click', function() {
                var bypassBtn = $(currentTable.table().container()).find('.bypass-to-shipping-button');
                if (bypassBtn.length) {
                    bypassBtn.trigger('click');
                }
            });

            // Pack Order Button
            $('#amazonPackOrderBtn').on('click', function() {
                var forwardBtn = $(currentTable.table().container()).find('.forward-button');
                if (forwardBtn.length) {
                    forwardBtn.trigger('click');
                }
            });

            // Start Picking Button
            $(document).on('click', '#amazonStartPickingBtn', function() {
                var btn = $(currentTable.table().container()).find('.start_picking_button');
                if (btn.length) {
                    btn.trigger('click');
                }
            });

            // Verify Picking Button
            $(document).on('click', '#amazonVerifyPickingBtn', function() {
                var btn = $(currentTable.table().container()).find('.verify_picking_button');
                if (btn.length) {
                    btn.trigger('click');
                }
            });

            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    currentTable.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                currentTable.ajax.reload();
            });

            // Preprocessing Orders DataTable - Dropshipping workflow
            var preprocessingOrdersTable = $('#preprocessing-orders-table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                scrollY: "53vh",
                scrollCollapse: true, // collapse height when fewer rows so pagination sits just below table
                scrollX: true,  
                order: [
                    [1, 'asc']
                ],
                ajax: {
                    url: '/order-fulfillment-preprocessing',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('MM/DD/YYYY');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('MM/DD/YYYY');
                            d.sell_list_filter_date_range = start + ' ~ ' + end;
                        }
                    },
                    dataSrc: function(json) {
                        var total = (json && (json.recordsFiltered != null ? json.recordsFiltered : json.recordsTotal)) || 0;
                        $('#order-count-preprocessing').text(total);
                        return (json && json.data) ? json.data : [];
                    }
                },
                columnDefs: [{
                    targets: [1],
                    createdCell: function(td) {
                        $(td).css({
                            'white-space': 'normal',
                            'word-break': 'break-word',
                            'max-width': '220px'
                        });
                    }
                }],
                columns: [{
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transactions.transaction_date'
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total_ordered_qty',
                        name: 'total_ordered_qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        searchable: false
                    },
                    {
                        data: 'added_by',
                        name: 'u.username',
                        orderable: false,
                        searchable: true
                    },
                ],
                buttons: [{
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                ],
                dom: 'Bfrtip',
                drawCallback: function() {
                    removeEmptyPaginationButtons();
                }
            });

            // Handle checkbox select all for preprocessing
            $('#select-all-preprocessing').on('change', function() {
                var isChecked = $(this).prop('checked');
                $('#preprocessing-orders-table tbody input[type="checkbox"]').prop('checked', isChecked);
            });

            var processingOrdersTable = $('#processing-orders-table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                scrollY: "53vh",
                scrollCollapse: true, // collapse height when fewer rows so pagination sits just below table
                scrollX: true,
                // Ensure classic paging with Next/Previous buttons
                paging: true,
                pageLength: 25,
                lengthChange: true,
                dom: 'Blfrtip',
                order: [
                    [2, 'asc']
                ],
                ajax: {
                    url: '/processing-order',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('MM/DD/YYYY');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('MM/DD/YYYY');
                            d.sell_list_filter_date_range=start+' ~ '+end;
                        }
                    },
                    dataSrc: function(json) {
                        var total = (json && (json.recordsFiltered != null ? json.recordsFiltered : json.recordsTotal)) || 0;
                        $('#order-count-pending').text(total);
                        return (json && json.data) ? json.data : [];
                    }
                },
                columnDefs: [{
                    targets: [2],
                    createdCell: function(td) {
                        $(td).css({
                            'white-space': 'normal',
                            'word-break': 'break-word',
                            'max-width': '220px'
                        });
                    }
                }],
                columns: [{
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false
                    }
                    @if (session()->get('business.manage_order_module') != 'manual')
                        , {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return '<a href="#" class="btn-modal" data-href=""><span class="label bg-yellow">Not Assigned</span></a>';
                            }
                        }
                    @endif ,
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transactions.transaction_date'
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total_ordered_qty',
                        name: 'total_ordered_qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        searchable: false
                    },
                    {
                        data: 'added_by',
                        name: 'u.username',
                        orderable: false,
                        searchable: true
                    },
                ],
                buttons: [{
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG
                            .export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                        customize: function(win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                    {
                        text: '{!! session()->get('business.manage_order_module') == 'manual'? "<i class=\"fa fa-cogs\"></i> Process Order" : "<i class=\"fa fa-user\"></i> Assign Picker" !!}',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 assign_picker_button bg-green',
                    },

                    {
                        text: '<i class="fa fa-arrow-down"></i>Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-blue decrease_priority_button',

                    },
                    {
                        text: '<i class="fa fa-arrow-up"></i> Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red increase_priority_button',

                    }, {
                        text: '<i class="fa fa-times" style="font-size: 15px;"></i> Cancel',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red pending_cancel_button',
                    }, {
                        extend: '',
                        text: '<i class="fa fa-forward"></i> BYPASS',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-purple bypass-to-shipping-button',
                        titleAttr: 'Skip picking/verification and move directly to packing'
                    }
                    @if (session()->get('business.manage_order_module') == 'manual')
                        , {
                            // allow manual picking
                            text: '<i class="fas fa-dolly"></i> Start Picking',
                            className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 start_picking_button',
                        }
                    @elseif (session()->get('business.manage_order_module') == 'both'), {
                            // allow manual picking
                            text: '<i class="fas fa-dolly"></i> Start Picking',
                            className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 start_picking_button',
                        }, {
                            // allow verifying lense icon
                            text: '<i class="fa fa-search-plus"></i> Verify Picking',
                            className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 verify_picking_button',
                        }
                    @endif , {
                        extend: '',
                        text: '<i class="fa fa-box-open"></i> Pack Order',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-teal forward-button'
                    }
                ],

                // erp software rows navigation by keyboard start
                initComplete: function() {
                    let currentRowIndex = -1;
                    let isMultiSelect = false;

                    function selectRow(row, shouldClear = true) {
                        const checkbox = row.find('input[type="checkbox"]');
                        if (shouldClear) {
                            $(currentTable.table().node()).find('.order-checkbox').prop('checked',
                                false);
                            $(currentTable.table().node()).find('tbody tr').css('background-color', '');
                        }
                        checkbox.prop('checked', true);
                        if (row.index() === currentRowIndex) {
                            row.css('background-color', '#add8e6');
                        } else {
                            row.css('background-color', '#ccffd6');
                        }
                        currentRowIndex = row.index();
                        const tableBody = $(currentTable.table().node()).find('tbody');
                        const rowTop = row.offset().top;
                        const tableTop = tableBody.offset().top;
                        const tableHeight = tableBody.height();
                        if (rowTop < tableTop) {
                            tableBody.scrollTop(tableBody.scrollTop() + (rowTop - tableTop));
                        } else if (rowTop + row.height() > tableTop + tableHeight) {
                            tableBody.scrollTop(tableBody.scrollTop() + (rowTop + row.height() -
                                tableTop - tableHeight));
                        }
                    }

                    function moveCursor(row) {
                        $(currentTable.table().node()).find('tbody tr').each(function() {
                            const currentRow = $(this);
                            if (currentRow.find('input[type="checkbox"]').prop('checked')) {
                                currentRow.css('background-color', '#ccffd6');
                            } else {
                                currentRow.css('background-color', '');
                            }
                        });

                        currentRowIndex = row.index();
                        row.css('background-color', '#add8e6');

                        const tableBody = $(currentTable.table().node()).find('tbody');
                        const rowTop = row.offset().top;
                        const tableTop = tableBody.offset().top;
                        const tableHeight = tableBody.height();

                        if (rowTop < tableTop) {
                            tableBody.scrollTop(tableBody.scrollTop() + (rowTop - tableTop));
                        } else if (rowTop + row.height() > tableTop + tableHeight) {
                            tableBody.scrollTop(tableBody.scrollTop() + (rowTop + row.height() -
                                tableTop - tableHeight));
                        }
                    }
                    let modalOpen = false;
                    $(document).on('keydown', function(e) {
                        if (modalOpen && !$('#manual_pick_verify_modal').hasClass('in') && !$(
                                '#manual_pick_verify_modal').hasClass('show')) {
                            modalOpen = false;

                        }
                        if ($('#manual_pick_verify_modal').hasClass('in') || $(
                                '#manual_pick_verify_modal').hasClass('show')) {
                            modalOpen = true; // Set flag when Start Picking popup is open
                        }
                        if (modalOpen && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
                            e
                        .preventDefault(); // Block Up/Down Arrows for table when popup is open

                            return;
                        }

                        if ($('#AssignPickerButton').hasClass('in') || $('#AssignPickerButton')
                            .hasClass('show')) {
                            return; // Skip table navigation if modal is open
                        }
                        if ($('.modal').hasClass('in') || $('.modal').hasClass('show')) {
                            return; // Block table navigation when any modal is open
                        }

                        if ($('#manual_pick_verify_modal').hasClass('in') || $(
                                '#manual_pick_verify_modal').hasClass('show')) {
                            return; // Explicitly block for manual pick verify modal
                        }


                        const table = $(currentTable.table().node());
                        const rows = table.find('tbody tr');
                        if (rows.length === 0) return;
                        if (currentRowIndex === -1) {
                            currentRowIndex = 0;
                            selectRow(rows.eq(0));
                            return;
                        }

                        let newIndex = currentRowIndex;

                        switch (e.key) {
                            case 'ArrowDown':
                                e.preventDefault();
                                if (currentRowIndex < rows.length - 1) {
                                    newIndex = currentRowIndex + 1;
                                    if (e.ctrlKey && e.shiftKey) {
                                        // Ctrl + Shift + Down: Move and select
                                        selectRow(rows.eq(newIndex), false);
                                    } else if (e.ctrlKey) {
                                        // Ctrl + Down: Just move cursor
                                        moveCursor(rows.eq(newIndex));
                                    } else {
                                        // Just Down: Select new row
                                        selectRow(rows.eq(newIndex), true);
                                    }
                                    currentRowIndex = newIndex;
                                }
                                break;

                            case 'ArrowUp':
                                e.preventDefault();
                                if (currentRowIndex > 0) {
                                    newIndex = currentRowIndex - 1;
                                    if (e.ctrlKey && e.shiftKey) {
                                        // Ctrl + Shift + Up: Move and select
                                        selectRow(rows.eq(newIndex), false);
                                    } else if (e.ctrlKey) {
                                        // Ctrl + Up: Just move cursor
                                        moveCursor(rows.eq(newIndex));
                                    } else {
                                        // Just Up: Select new row
                                        selectRow(rows.eq(newIndex), true);
                                    }
                                    currentRowIndex = newIndex;
                                }
                                break;

                            case 'Space':
                                e.preventDefault();
                                const currentRow = rows.eq(currentRowIndex);
                                const checkbox = currentRow.find('input[type="checkbox"]');
                                checkbox.prop('checked', !checkbox.prop('checked'));
                                if (checkbox.prop('checked')) {
                                    currentRow.css('background-color', '#add8e6');
                                } else {
                                    currentRow.css('background-color', '');
                                }
                                break;

                            case 'Enter':
                                if (e.ctrlKey) {
                                    e.preventDefault();
                                    const currentRowEnter = rows.eq(currentRowIndex);
                                    const checkboxEnter = currentRowEnter.find(
                                        'input[type="checkbox"]');
                                    checkboxEnter.prop('checked', !checkboxEnter.prop(
                                        'checked'));
                                    if (checkboxEnter.prop('checked')) {
                                        currentRowEnter.css('background-color', '#add8e6');
                                    } else {
                                        currentRowEnter.css('background-color', '');
                                    }
                                }
                                break;


                        }
                    });

                    // Handle row click for all tables
                    $('.nav-tabs-custom').on('click', 'tbody tr', function(e) {
                        if (!$(e.target).is('input[type="checkbox"]')) {
                            currentRowIndex = $(this).index();
                            selectRow($(this), !e.ctrlKey);
                        }
                    });

                    // erp software rows navigation by keyboard end
                    $('.assign_picker_button').on('click', function() {
                        let operation = '';
                        var selectedRows = [];
                        $('.order-checkbox:checked').each(function() {
                            selectedRows.push($(this).val());
                        });
                        getActivePicker(function(activePicker) {
                        if ('{{ session()->get('business.manage_order_module') }}' ==
                            'manual'||activePicker) {
                            operation = '{{ auth()->user()->id }}';
                            activePicker=activePicker??null;
                            if (selectedRows.length > 0 && operation) {
                                $.ajax({
                                    url: '/apply-order-operation',
                                    type: 'POST',
                                    data: {
                                        ids: selectedRows,
                                        operation: operation,
                                        activePicker:activePicker,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        toastr.options.positionClass =
                                            'toast-top-center';
                                        toastr.success(response.message);
                                        processingOrdersTable.ajax.reload();
                                        $(".modal").modal("hide");
                                    }
                                });
                            } else {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error('Please select rows and Picker');
                            }
                            return;
                        }
                        if (selectedRows.length > 0) {
                            $("#AssignPickerButton").modal("show");
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Please select rows and Picker');
                            $(this).closest(".modal").modal("hide");
                        }
                    })
                    });


                    $(".close-modal").click(function() {
                        $(this).closest(".modal").modal("hide");
                    });
                },
                drawCallback: function() {
                    removeEmptyPaginationButtons();
                }
            });
            var currentTable = processingOrdersTable;
            pickingOrdersTable = $('#picking-orders-table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                scrollY: "53vh",
                scrollCollapse: true,
                scrollX: true,
                fixedHeader: false,
                dom: 'Blfrtip',
                ajax: {
                    url: '/picking-order',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('MM/DD/YYYY');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('MM/DD/YYYY');
                            d.sell_list_filter_date_range=start+' ~ '+end;
                        }
                    },
                    dataSrc: function(json) {
                        var total = (json && (json.recordsFiltered != null ? json.recordsFiltered : json.recordsTotal)) || 0;
                        $('#order-count-processing').text(total);
                        return (json && json.data) ? json.data : [];
                    }
                },
                // order: [[1, 'asc']], //
                columnDefs: [{
                        targets: [1],
                        createdCell: function(td) {
                            $(td).css({
                                'white-space': 'normal',
                                'word-break': 'break-word',
                                'max-width': '100px' // Set a max-width so it has room to wrap
                            });
                        }
                    },
                    {
                        targets: [2],
                        createdCell: function(td) {
                            $(td).css({
                                'white-space': 'normal',
                                'word-break': 'break-word',
                                'max-width': '200px' // Set a max-width so it has room to wrap
                            });
                        }
                    }
                ],
                columns: [{
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const transactionTime = new Date(row.transaction_date);
                            const currentTime = new Date();
                            const timeDiff = currentTime - transactionTime;
                            const hoursDiff = timeDiff / (1000 * 60 * 60);
                            let icon = '';
                            let status = $(row.picking_status).data('status');

                            if (status == 'PICKED') {
                                icon = '✅';
                            } else if (status == 'VERIFYING') {

                                icon = '✅';
                            } else if (status == 'VERIFIED') {
                                icon = '✅☑️';
                            }
                            // row.picking_status.data('status') == 'PICKED'? icon = '✅' :'';
                            // row.picking_status.data('status') == 'VERIFIED'? icon = '✅☑️' :'';
                            if (hoursDiff > 24) {
                                return '<input type="checkbox" class="order-checkbox" value="' + row
                                    .id + '"><span >🚨 ' + icon +
                                    '</span> <span class="label bg-yellow hide">' + row.priority +
                                    '</span>';
                            } else {
                                return '<input type="checkbox" class="order-checkbox" value="' + row
                                    .id + '"><span>&nbsp;&nbsp;&nbsp; ' + icon +
                                    ' </span> <span class="label bg-yellow hide">' + row.priority +
                                    '</span>';
                            }
                        }
                    }
                    @if (session()->get('business.manage_order_module') != 'manual')
                        ,

                        {
                            data: 'picking_status',
                            name: 'transactions.picking_status',
                            searchable: false,
                            orderable:false,
                            render: function(data, type, row) {
                                var status = $(data).data('status');
                                var color = $(data).data('color');
                                var picking_time = $(data).data(
                                    'picking-time'); //data-picking-time=\"\" 
                                var url = $(data).data('href');
                                var percentage = parseFloat($(row.total_picked_qty).data(
                                    'total-percentage')) || 0;
                                percentage = percentage.toFixed(2);
                                if (status === 'PICKING') {
                                    if (picking_time == "") {
                                        return ' <div class="progress tw-h-6 btn-modal edit-picking-status" style="background-color: #666666; cursor:pointer;" data-href="' +
                                            url + '">' +
                                            '<div class="progress-bar bg-yellow tw-text-sm tw-font-medium tw-text-white text-nowrap " role="progressbar" ' +
                                            'style="width: ' + percentage + '% !important;" ' +
                                            'aria-valuenow="' + '' + '" ' +
                                            'aria-valuemin="0" ' +
                                            'aria-valuemax="100">' +
                                            'Queued' + ' Order' +
                                            '</div></div>';
                                    } else {
                                        return ' <div class="progress tw-h-6 btn-modal edit-picking-status" style="background-color: #666666; cursor:pointer;" data-href="' +
                                            url + '">' +
                                            '<div class="progress-bar bg-yellow tw-text-sm tw-font-medium tw-text-white text-nowrap " role="progressbar" ' +
                                            'style="width: ' + percentage + '% !important;" ' +
                                            'aria-valuenow="' + percentage + '" ' +
                                            'aria-valuemin="0" ' +
                                            'aria-valuemax="100">' +
                                            status + ' ' +
                                            percentage + '%' +
                                            '</div></div>';
                                    }
                                } else if (status === 'PICKED') {
                                    return ' <div class="progress btn-modal edit-picking-status tw-h-6" style="cursor:pointer;" data-href="' +
                                        url + '">' +
                                        '<div style="background-color:' + color +
                                        '; padding: 0px 10px;" class=" tw-text-sm tw-font-medium tw-text-white text-nowrap " role="progressbar" ' +
                                        'style="width: ' + percentage + '% !important;" ' +
                                        'aria-valuenow="' + percentage + '" ' +
                                        'aria-valuemin="0" ' +
                                        'aria-valuemax="100">' +
                                        'Waiting for Verify'
                                    '</div></div>';

                                } else if (status === 'VERIFIED') {
                                    return ' <div class="progress btn-modal edit-picking-status tw-h-6" style="cursor:pointer;" data-href="' +
                                        url + '">' +
                                        '<div style="background-color:' + color +
                                        '; padding: 0px 10px;" class=" tw-text-sm tw-font-medium tw-text-white text-nowrap " role="progressbar" ' +
                                        'style="width: ' + percentage + '% !important;" ' +
                                        'aria-valuenow="' + percentage + '" ' +
                                        'aria-valuemin="0" ' +
                                        'aria-valuemax="100">' +
                                        status +
                                        '</div></div>';
                                }
                                return ' <div class="progress btn-modal edit-picking-status tw-h-6" style="cursor:pointer;" data-href="' +
                                    url + '">' +
                                    '<div style="background-color:' + color +
                                    '; padding: 0px 10px;" class=" tw-text-sm tw-font-medium tw-text-white text-nowrap " role="progressbar" ' +
                                    'style="width: ' + percentage + '% !important;" ' +
                                    'aria-valuenow="' + percentage + '" ' +
                                    'aria-valuemin="0" ' +
                                    'aria-valuemax="100">' +
                                    status + ' ' +
                                    percentage + '%' +
                                    '</div></div>';

                            }
                        }
                    @endif ,

                    {
                        data: 'invoice_no',
                        name: 'transactions.invoice_no'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transactions.transaction_date' // ✅ use the real column, not alias
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column'
                    },
                    {
                        data: 'final_total',
                        name: 'transactions.final_total'
                    },
                    // {
                    //     data: 'status',
                    //     name: 'transactions.status'
                    // },
                    {
                        data: 'payment_status',
                        name: 'transactions.payment_status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'transaction_date',
                    //     name: 'transactions.transaction_date' // ✅ use the real column, not alias
                    // },

                    {
                        data: 'picker_details',
                        name: 'picker_details',
                        render: function(data) {
                            return data;
                        }
                    }
                    @if (session()->get('business.manage_order_module') == 'both')
                        , {
                            data: 'verifier_details',
                            name: 'verifier_details',
                            render: function(data) {
                                return data;
                            }
                        }
                    @endif
                ],
                buttons: [{
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG
                            .export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                        customize: function(win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                    {
                        extend: '',
                        text: '<i class="fa fa-spinner"></i> Pending',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red backward-button'
                    },

                    {
                        text: '<i class="fa fa-retweet"></i> Refresh',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {
                            pickingOrdersTable.ajax.reload();
                        }
                    },
                    {
                        text: '<i class="fa fa-arrow-down"></i>Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-blue decrease_priority_button',

                    },
                    {
                        text: '<i class="fa fa-arrow-up"></i> Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red increase_priority_button',

                    },
                    {
                        text: '<i class="fa fa-arrow-up"></i> Held',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-green held_button',

                    }
                    @if (session()->get('business.manage_order_module') == 'manual')
                        , {
                            // allow manual picking
                            text: '<i class="fas fa-dolly"></i> Start Picking',
                            className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 start_picking_button',
                        }
                    @elseif (session()->get('business.manage_order_module') == 'both'), {
                            // allow manual picking
                            text: '<i class="fas fa-dolly"></i> Start Picking',
                            className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 start_picking_button',
                        }, {
                            // allow verifying lense icon
                            text: '<i class="fa fa-search-plus"></i> Verify Picking',
                            className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 verify_picking_button',
                        }
                    @endif , {
                        extend: '',
                        text: '<i class="fa fa-box-open"></i> Pack Order',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-teal forward-button'
                    }
                ],
                initComplete: function() {
                    $('.picked-apply-operation').on('click', function() {
                        var selectedRows = [];
                        $('.order-checkbox:checked').each(function() {
                            selectedRows.push($(this).val());
                        });

                        var operation = $('#picked-order-action').val();
                        if (selectedRows.length > 0 && operation) {
                            $.ajax({
                                url: '/mark-as-picked',
                                type: 'POST',
                                data: {
                                    ids: selectedRows,
                                    operation: operation,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    toastr.options.positionClass =
                                        'toast-top-center';
                                    toastr.success(response.message);
                                    pickingOrdersTable.ajax.reload();
                                }
                            });
                            $(this).closest(".modal").modal("hide");
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Please select rows and Picker.');
                        }
                    });
                },
                drawCallback: function() {
                    removeEmptyPaginationButtons();
                }
            });

            pickedOrdersTable = $('#picked-orders-table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                scrollX: true,
                scrollY: "53vh",
                scrollCollapse: true,
                fixedHeader: false,
                dom: 'Blfrtip',
                ajax: {
                    url: '/picked-order',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('MM/DD/YYYY');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('MM/DD/YYYY');
                            d.sell_list_filter_date_range=start+' ~ '+end;
                        }
                    },
                    dataSrc: function(json) {
                        var total = (json && (json.recordsFiltered != null ? json.recordsFiltered : json.recordsTotal)) || 0;
                        $('#order-count-packing').text(total);
                        return (json && json.data) ? json.data : [];
                    }
                },
                order: [
                    [1, 'asc']
                ], // Default order by invoice_no descending
                columnDefs: [{
                    targets: [1], // Change to correct index of `sku` column
                    createdCell: function(td) {
                        $(td).css({
                            'white-space': 'normal',
                            'word-break': 'break-word',
                            'max-width': '200px' // Set a max-width so it has room to wrap
                        });
                    }
                }],
                columns: [{
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="order-checkbox" value="' + row
                                .id + '">';
                        }
                    },
                    {
                        data: 'picking_status',
                        name: 'transactions.picking_status',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'invoice_no',
                        name: 'transactions.invoice_no'
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column'
                    },
                    {
                        data: 'final_total',
                        name: 'transactions.final_total'
                    },
                    // {
                    //     data: 'status',
                    //     name: 'transactions.status'
                    // },
                    {
                        data: 'payment_status',
                        name: 'transactions.payment_status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transactions.transaction_date' // ✅ Fix alias
                    },


                    {
                        data: 'picking_time',
                        name: 'picking_time',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_picked_qty',
                        name: 'total_picked_qty',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'picked_qty_amount',
                        name: 'picked_qty_amount',
                        render: function(data) {
                            return data;
                        }
                    }
                    // ,
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function (data) {
                    //         return data;
                    //     }
                    // }
                ],
                buttons: [{
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG
                            .export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true
                        },
                        footer: true,
                        customize: function(win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2'
                    },
                    {
                        extend: '',
                        text: '<i class="fa fa-arrow-left"></i> Processing ',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red backward-button1'
                    },
                    {
                        text: '<i class="fa fa-arrow-down"></i>Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-blue decrease_priority_button',

                    },
                    {
                        text: '<i class="fa fa-arrow-up"></i> Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red increase_priority_button',

                    },
                    // {
                    //     text: '<i class="fa fa-warehouse" aria-hidden="true"></i> Shipping Stations',
                    //     className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-purple shipping_stations_button',
                    // },
@if (!empty($pos_settings['is_shipping']))
                    {
                        text: '<i class="fas fa-shipping-fast" aria-hidden="true"></i> Make Shipment',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-blue make_shipment',
                    },
@else
                    {
                        text: '<i class="fa fa-check" aria-hidden="true"></i> Complete Order',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-blue complete_order',
                    },
@endif
                    {
                        text: '<i class="fa fa-times" style="font-size: 15px;"></i> Cancel',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red pending_cancel_button',
                    }
                ],
                drawCallback: function() {
                    removeEmptyPaginationButtons();
                }
            });

            var cancelOrdersTable = $('#cancel-orders-table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                scrollX: true,
                scrollY: "53vh",
                scrollCollapse: true,
                fixedHeader: false,
                dom: 'Blfrtip',
                ajax: {
                    url: '/cancel-order',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('MM/DD/YYYY');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('MM/DD/YYYY');
                            d.sell_list_filter_date_range=start+' ~ '+end;
                        }
                    },
                    dataSrc: function(json) {
                        var total = (json && (json.recordsFiltered != null ? json.recordsFiltered : json.recordsTotal)) || 0;
                        $('#order-count-cancelled').text(total);
                        return (json && json.data) ? json.data : [];
                    }
                },
                order: [
                    [2, 'asc']
                ], // Default sorting by invoice_no
                columnDefs: [{
                    targets: [1], // Change to correct index of `sku` column
                    createdCell: function(td) {
                        $(td).css({
                            'white-space': 'normal',
                            'word-break': 'break-word',
                            'max-width': '200px' // Set a max-width so it has room to wrap
                        });
                    }
                }],
                columns: [{
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="order-checkbox" value="' + row
                                .id + '">';
                        }
                    },
                    {
                        data: 'invoice_no',
                        name: 'transactions.invoice_no'
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column'
                    },
                    {
                        data: 'final_total',
                        name: 'transactions.final_total'
                    },
                    {
                        data: 'status',
                        name: 'transactions.status'
                    },
                    {
                        data: 'payment_status',
                        name: 'transactions.payment_status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transactions.transaction_date' // ✅ real DB column
                    },
                    {
                        data: 'picking_status',
                        name: 'transactions.picking_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    }
                ],
                buttons: [{
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG
                            .export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true
                        },
                        footer: true,
                        customize: function(win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2'
                    },
                    {
                        extend: '',
                        text: '<i class="fa fa-clock"></i> Pending',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red backward-button2'
                    },
                    {
                        text: '<i class="fa fa-arrow-down"></i>Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-blue'
                    },
                    {
                        text: '<i class="fa fa-arrow-up"></i> Priority',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 bg-red'
                    }
                ]
            });
            var completeOrdersTable = $('#complete-orders-table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                scrollX: true,
                scrollY: "53vh",
                fixedHeader: false,
                dom: 'Blfrtip',
                ajax: {
                    url: '/complete-order',
                    data: function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('MM/DD/YYYY');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('MM/DD/YYYY');
                            d.sell_list_filter_date_range=start+' ~ '+end;
                        }
                    },
                    dataSrc: function(json) {
                        var total = (json && (json.recordsFiltered != null ? json.recordsFiltered : json.recordsTotal)) || 0;
                        $('#order-count-completed').text(total);
                        return (json && json.data) ? json.data : [];
                    }
                },
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    targets: [4], // Targeting `merged_column` (assumed to be `sku`)
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (td) {
                            $(td).css({
                                'white-space': 'normal',
                                'word-break': 'break-word',
                                'max-width': '200px'
                            });
                        }
                    }
                }],
                columns: [{
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="order-checkbox" value="' + row
                                .id + '">';
                        }
                    },
                    {
                        data: 'status',
                        name: 'transactions.status'
                    },
                    {
                        data: 'picking_status',
                        name: 'transactions.picking_status',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'invoice_no',
                        name: 'transactions.invoice_no'
                    },
                    {
                        data: 'merged_column',
                        name: 'merged_column'
                    },
                    {
                        data: 'final_total',
                        name: 'transactions.final_total'
                    },
                    {
                        data: 'payment_status',
                        name: 'transactions.payment_status',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_date',
                        name: 'transactions.transaction_date'
                    }
                ],
                buttons: [{
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function() {

                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG
                            .export_to_excel,
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible'
                        },
                        footer: true
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true
                        },
                        footer: true,
                        customize: function(win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore($(win.document.body)
                                    .find('table'));
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2'
                    }
                ]
            });

            $('.forward-button').on('click', function() {
                var selectedRows = [];
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select rows to move.');
                    return;
                }
                $.ajax({
                    url: '/process-to-packing',
                    type: 'POST',
                    data: {
                        ids: selectedRows,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success(response.message);
                            selectedRows.forEach(function(orderId) {
                                processingOrdersTable.rows().every(function() {
                                    var row = this.node();
                                    var rowId = $(row).find('.order-checkbox')
                                        .val();
                                    if (rowId == orderId) {
                                        this.remove();
                                    }
                                });
                            });
                            processingOrdersTable.draw();
                            var currentPage = processingOrdersTable.page();
                            var totalPages = processingOrdersTable.page.info().pages;
                            if (currentPage < totalPages - 1) {
                                processingOrdersTable.page(currentPage + 1).draw(false);
                            }

                            pickingOrdersTable.ajax.reload(null, false);
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Error occurred while moving orders.');
                    }
                });
            });

            // BYPASS button - show bypass modal for partial fulfillment
            $('.bypass-to-shipping-button').on('click', function() {
                var selectedRows = [];
                $('#processing-orders-table .order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select an order to bypass.');
                    return;
                }
                
                if (selectedRows.length > 1) {
                    // Multiple orders selected - ask if they want bulk bypass or one-by-one
                    swal({
                        icon: 'info',
                        title: 'Multiple Orders Selected',
                        text: 'You have selected ' + selectedRows.length + ' orders. Choose how to proceed:',
                        buttons: {
                            cancel: 'Cancel',
                            partial: {
                                text: 'Partial Fulfill (First Order)',
                                value: 'partial',
                                className: 'bg-purple'
                            },
                            bulk: {
                                text: 'Bulk Bypass (Full Qty)',
                                value: 'bulk',
                                className: 'bg-info'
                            }
                        }
                    }).then((value) => {
                        if (value === 'partial') {
                            // Show modal for first selected order
                            openBypassModal(selectedRows[0]);
                        } else if (value === 'bulk') {
                            // Bulk bypass with full quantities (original behavior)
                            bulkBypassOrders(selectedRows);
                        }
                    });
                } else {
                    // Single order - show bypass modal
                    openBypassModal(selectedRows[0]);
                }
            });
            
            // Function to open bypass modal for partial fulfillment
            function openBypassModal(orderId) {
                $.ajax({
                    url: '/bypass-order-modal/' + orderId,
                    type: 'GET',
                    beforeSend: function() {
                        // Show loading
                        $('#bypass_order_modal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body text-center" style="padding: 50px;"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading order details...</p></div></div></div>');
                        $('#bypass_order_modal').modal('show');
                    },
                    success: function(response) {
                        if (typeof response === 'object' && response.status === false) {
                            $('#bypass_order_modal').modal('hide');
                            toastr.error(response.message);
                        } else {
                            $('#bypass_order_modal').html(response);
                        }
                    },
                    error: function(xhr) {
                        $('#bypass_order_modal').modal('hide');
                        toastr.error(xhr.responseJSON?.message || 'Error loading order details');
                    }
                });
            }
            
            // Function for bulk bypass with full quantities (original behavior)
            function bulkBypassOrders(orderIds) {
                swal({
                    icon: 'warning',
                    title: 'Bulk Bypass to Packing',
                    text: 'This will bypass ' + orderIds.length + ' orders with FULL quantities. Stock will be deducted. Are you sure?',
                    buttons: {
                        cancel: 'Cancel',
                        confirm: {
                            text: 'Yes, Bypass All',
                            className: 'bg-purple'
                        }
                    },
                    dangerMode: true
                }).then((confirmed) => {
                    if (confirmed) {
                        $.ajax({
                            url: '/bypass-to-shipping',
                            type: 'POST',
                            data: {
                                ids: orderIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    toastr.options.positionClass = 'toast-top-center';
                                    toastr.success(response.message);
                                    processingOrdersTable.ajax.reload(null, false);
                                    pickedOrdersTable.ajax.reload(null, false);
                                } else {
                                    toastr.options.positionClass = 'toast-top-center';
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error('Error occurred while bypassing orders.');
                            }
                        });
                    }
                });
            }

            $('.backward-button').on('click', function() {
                var selectedRows = [];

                // Collect the selected orders' IDs
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });

                // If no rows are selected, show an error message
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select rows to delete.');
                    return;
                }

                // Send the delete request to the server
                $.ajax({
                    url: '/process-To-pending', // Your route to handle deletion
                    type: 'POST',
                    data: {
                        ids: selectedRows,
                        _token: '{{ csrf_token() }}' // CSRF token for security
                    },
                    success: function(response) {
                        if (response.status) {
                            // Show success message
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success(response.message);

                            // Remove the deleted rows from the DataTable
                            selectedRows.forEach(function(orderId) {
                                processingOrdersTable.rows().every(function() {
                                    var row = this.node();
                                    var rowId = $(row).find('.order-checkbox')
                                        .val();
                                    if (rowId == orderId) {
                                        this
                                            .remove(); // Remove the row from the table
                                    }
                                });
                            });

                            // Redraw the table to reflect the changes
                            processingOrdersTable.draw();

                            pickingOrdersTable.ajax.reload(null, false);
                            // Go to the previous page (if not already on the first page)
                            var currentPage = processingOrdersTable.page();
                            if (currentPage > 0) {
                                processingOrdersTable.page(currentPage - 1).draw(false);
                            }
                        } else {
                            // Show error message if deletion failed
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Error occurred while deleting orders.');
                    }
                });
            });

            $('.backward-button1').on('click', function() {
                var selectedRows = [];

                // Collect the selected orders' IDs
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });

                // If no rows are selected, show an error message
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select rows to delete.');
                    return;
                }

                // Send the delete request to the server
                $.ajax({
                    url: '/packing-to-process', // Your route to handle deletion
                    type: 'POST',
                    data: {
                        ids: selectedRows,
                        _token: '{{ csrf_token() }}' // CSRF token for security
                    },
                    success: function(response) {
                        if (response.status) {
                            // Show success message
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success(response.message);

                            // Remove the deleted rows from the DataTable
                            selectedRows.forEach(function(orderId) {
                                processingOrdersTable.rows().every(function() {
                                    var row = this.node();
                                    var rowId = $(row).find('.order-checkbox')
                                        .val();
                                    if (rowId == orderId) {
                                        this
                                            .remove(); // Remove the row from the table
                                    }
                                });
                            });

                            // Redraw the table to reflect the changes
                            processingOrdersTable.draw();

                            // Go to the previous page (if not already on the first page)
                            var currentPage = processingOrdersTable.page();
                            if (currentPage > 0) {
                                processingOrdersTable.page(currentPage - 1).draw(false);
                            }

                            pickedOrdersTable.ajax.reload(null, false);
                        } else {
                            // Show error message if deletion failed
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Error occurred while deleting orders.');
                    }
                });
            });

            $('.backward-button2').on('click', function() {
                var selectedRows = [];
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select rows to delete.');
                    return;
                }
                $.ajax({
                    url: '/cancel-to-pending',
                    type: 'POST',
                    data: {
                        ids: selectedRows,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success(response.message);
                            selectedRows.forEach(function(orderId) {
                                processingOrdersTable.rows().every(function() {
                                    var row = this.node();
                                    var rowId = $(row).find('.order-checkbox')
                                        .val();
                                    if (rowId == orderId) {
                                        this.remove();
                                    }
                                });
                            });
                            processingOrdersTable.draw();
                            var currentPage = processingOrdersTable.page();
                            if (currentPage > 0) {
                                processingOrdersTable.page(currentPage - 1).draw(false);
                            }

                            cancelOrdersTable.ajax.reload(null, false);
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Error occurred while deleting orders.');
                    }
                });
            });


            $(document).on('click', '.pending_cancel_button', function(e) {
                e.preventDefault();

                let cancelUrl = '/cancel-so';
                let selectedRows = [];
                if (currentTable == pickedOrdersTable) {
                    $('#picked-orders-table .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                } else {

                    $('#processing-orders-table .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                }
                
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row.');
                    return;
                }
                swal({
                    icon: 'warning',
                    title: 'Cancel Order',
                    text: 'Are you sure you want to cancel and release available stock!',
                    buttons: {
                        cancel: 'Cancel',
                        confirm: 'Yes, Change'
                    }
                }).then((change) => {
                    if (change) {
                        $.ajax({
                            url: cancelUrl,
                            type: 'POST',
                            data: {
                                ids: selectedRows,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status) {
                                    toastr.options.positionClass = 'toast-top-center';
                                    toastr.success(response.message);
                                    if (currentTable == pickedOrdersTable) {
                                        $('#picked-orders-table').DataTable().ajax
                                            .reload();
                                    } else {
                                        $('#processing-orders-table').DataTable().ajax
                                            .reload();
                                    }
                                } else {
                                    toastr.options.positionClass = 'toast-top-center';
                                    toastr.error(response.message);
                                    if (currentTable == pickedOrdersTable) {
                                        $('#picked-orders-table').DataTable().ajax
                                            .reload();
                                    } else {
                                        $('#processing-orders-table').DataTable().ajax
                                            .reload();
                                    }
                                }
                            },
                            error: function() {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error('something went wrong');
                                if (currentTable == pickedOrdersTable) {
                                    $('#picked-orders-table').DataTable().ajax.reload();
                                } else {
                                    $('#processing-orders-table').DataTable().ajax
                                        .reload();
                                }
                            }
                        });
                    }
                });

            });

            // start picking operation
            $('.start_picking_button').on('click', function() {
                // Check both processing and picking tables
                let pickingOrderId = $('#processing-orders-table .order-checkbox:checked').first().val() || 
                                   $('#picking-orders-table .order-checkbox:checked').first().val();
                let checkedCount = $('#processing-orders-table .order-checkbox:checked').length + 
                                 $('#picking-orders-table .order-checkbox:checked').length;
                
                if (checkedCount > 1) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select only one row to start picking');
                    return;
                }
                if (pickingOrderId == undefined) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row to start picking');
                    return;
                }
                if (pickingOrderId.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row to start picking');
                    return;
                }
                let startPickingUrl = '/sells-picking-popup/' + pickingOrderId;
                $.ajax({
                    url: startPickingUrl,
                    type: 'GET',
                    accept: 'text/html',
                    success: function(response) {
                        try {
                            const json = typeof response === 'string' ? JSON.parse(response) :
                                response;
                            if (json && typeof json === 'object' && json.status === false) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error(json.message);
                                return;
                            }
                            $('#manual_pick_verify_modal').html(response);
                            $('#manual_pick_verify_modal').modal('show');
                        } catch (e) {
                            // Not JSON, so treat response as HTML
                            $('#manual_pick_verify_modal').html(response);
                            $('#manual_pick_verify_modal').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Failed to load manual pick verify modal.');
                    }
                });
            });
            // start verifying 
            $('.verify_picking_button').on('click', function() {
                // Check both processing and picking tables
                let verifyingOrderId = $('#processing-orders-table .order-checkbox:checked').first().val() || 
                                     $('#picking-orders-table .order-checkbox:checked').first().val();
                let checkedCount = $('#processing-orders-table .order-checkbox:checked').length + 
                                 $('#picking-orders-table .order-checkbox:checked').length;
                
                if (checkedCount > 1) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select only one row to start verifying');
                    return;
                }
                if (verifyingOrderId == undefined) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row to start verifying');
                    return;
                }
                if (verifyingOrderId.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row to start verifying');
                    return;
                }
                // Get the row data for the selected order - check both tables
                let row = null;
                if ($('#processing-orders-table .order-checkbox:checked').length > 0) {
                    row = processingOrdersTable.row(function(idx, data) {
                        return data.id == verifyingOrderId;
                    }).data();
                } else {
                    row = pickingOrdersTable.row(function(idx, data) {
                        return data.id == verifyingOrderId;
                    }).data();
                }
                if (row && $(row.picking_status).data('status') === 'VERIFIED') {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('This order has already been verified');
                    return;
                }
                let startVerifyingUrl = '/sells-picking-popup/' + verifyingOrderId + '?type=verifier';
                $.ajax({
                    url: startVerifyingUrl,
                    type: 'GET',
                    success: function(response) {
                        try {
                            const json = typeof response === 'string' ? JSON.parse(response) :
                                response;
                            if (json && typeof json === 'object' && json.status === false) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error(json.message);
                                return;
                            }
                            $('#manual_pick_verify_modal').html(response);
                            $('#manual_pick_verify_modal').modal('show');
                        } catch (e) {
                            // Not JSON, so treat response as HTML
                            $('#manual_pick_verify_modal').html(response);
                            $('#manual_pick_verify_modal').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Failed to load manual pick verify modal.');
                    }
                });
            });


            $('#select-all').on('click', function() {
                var rows = processingOrdersTable.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
            $('#select-all-2').on('click', function() {
                var rows = pickingOrdersTable.rows({
                    'search': 'applied'
                }).nodes();
                $(' input[type="checkbox"]', rows).prop('checked', this.checked);
            });
            $('#select-all-3').on('click', function() {
                var rows = pickedOrdersTable.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
            $('#select-all-4').on('click', function() {
                var rows = cancelOrdersTable.rows({
                    'search': 'applied'
                }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
            // $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            //     var target = $(e.target).attr('href'); //
            //     if (target === '#processing_orders') {
            //         processingOrdersTable.columns.adjust().draw();
            //         currentTable = processingOrdersTable;
            //     } else if (target === '#picking_orders') {
            //         pickingOrdersTable.columns.adjust().draw();
            //         currentTable = pickingOrdersTable;
            //     } else if (target === '#picked_orders') {
            //         pickedOrdersTable.columns.adjust().draw();
            //         currentTable = pickedOrdersTable;
            //     } else if (target === '#cancel_orders') {
            //         cancelOrdersTable.columns.adjust().draw();
            //         currentTable = cancelOrdersTable;
            //     } else if (target === '#complete_orders') {
            //         completeOrdersTable.columns.adjust().draw();
            //         currentTable = completeOrdersTable;
            //     }
            // });

            // Function to get URL parameter
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Function to update URL with active tab
            function updateUrlWithTab(tabId) {
                const url = new URL(window.location);
                url.searchParams.set('tab', tabId.replace('#', ''));
                window.history.replaceState({}, '', url);
            }

            // Function to restore active tab from URL parameter
            function restoreActiveTab() {
                const tabParam = getUrlParameter('tab');
                if (tabParam) {
                    const tabId = '#' + tabParam;
                    // Remove active class from all tabs and panes
                    $('.nav-tabs li').removeClass('active');
                    $('.tab-pane').removeClass('active');
                    
                    // Add active class to saved tab
                    $(`a[href="${tabId}"]`).parent().addClass('active');
                    $(tabId).addClass('active');
                    
                    // Update currentTable based on the restored tab
                    if (tabId === '#processing_orders') {
                        currentTable = processingOrdersTable;
                    } else if (tabId === '#picking_orders') {
                        currentTable = pickingOrdersTable;
                    } else if (tabId === '#picked_orders') {
                        currentTable = pickedOrdersTable;
                    } else if (tabId === '#cancel_orders') {
                        currentTable = cancelOrdersTable;
                    } else if (tabId === '#complete_orders') {
                        currentTable = completeOrdersTable;
                    }
                }
            }

            // Restore active tab on page load
            restoreActiveTab();
            var initTab = $('#amazonOrderTabs .amazon-status-tab.active').attr('href') || $('.nav-tabs li.active a').attr('href') || '#processing_orders';
            toggleWorkflowButtonsForTab(initTab);

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                
                // Update URL with active tab
                updateUrlWithTab(target);
                
                if (target === '#processing_orders') {
                    processingOrdersTable.columns.adjust().draw();
                    currentTable = processingOrdersTable;
                    $('#select-all').prop('checked', false); // Uncheck Select All for Pending tab
                } else if (target === '#picking_orders') {
                    pickingOrdersTable.columns.adjust().draw();
                    currentTable = pickingOrdersTable;
                    $('#select-all-2').prop('checked', false); // Uncheck Select All for Processing tab
                } else if (target === '#picked_orders') {
                    pickedOrdersTable.columns.adjust().draw();
                    currentTable = pickedOrdersTable;
                    $('#select-all-3').prop('checked', false); // Uncheck Select All for Packing tab
                } else if (target === '#cancel_orders') {
                    cancelOrdersTable.columns.adjust().draw();
                    currentTable = cancelOrdersTable;
                    $('#select-all-4').prop('checked', false); // Uncheck Select All for Cancelled tab
                } else if (target === '#complete_orders') {
                    completeOrdersTable.columns.adjust().draw();
                    currentTable = completeOrdersTable;
                    $('#select-all-5').prop('checked', false); // Uncheck Select All for Completed tab
                }
                toggleWorkflowButtonsForTab(target);
            });
            $('#sell_list_filter_date_range').daterangepicker({
                opens: 'right',
                showRanges: true,
                locale: {
                    direction: 'ltr'
                }
            });
            $('#filterModal').on('hidden.bs.modal', function() {
                $('.daterangepicker').hide();
            });
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    if (currentTable) {
                        currentTable.ajax.reload();
                    }
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                if (currentTable) {
                    currentTable.ajax.reload();
                }
            });
            const dropdown = $('#dropdown');
            const searchInput = $('#order-action');

            searchInput.on('focus', function() {
                dropdown.show();
            });

            // searchInput.on('keyup', function () {
            //     const query = $(this).val().toLowerCase();
            //     dropdown.children('div').each(function () {
            //         const text = $(this).text().toLowerCase();
            //         $(this).toggle(text.includes(query));
            //     });
            // });


            // searchInput.on('keydown', function (e) {
            //     if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            //         e.preventDefault();
            //         const visibleOptions = dropdown.children('div:visible');
            //         if (visibleOptions.length === 0) return;

            //         let currentIndex = -1;
            //         const currentSelected = dropdown.children('div.active');
            //         if (currentSelected.length) {
            //             currentIndex = visibleOptions.index(currentSelected);
            //         }

            //         let nextIndex;
            //         if (e.key === 'ArrowDown') {
            //             nextIndex = (currentIndex + 1) % visibleOptions.length;
            //         } else if (e.key === 'ArrowUp') {
            //             nextIndex = (currentIndex - 1 + visibleOptions.length) % visibleOptions.length;
            //         }

            //         dropdown.children('div').removeClass('active').css('background-color', '');
            //         const nextOption = visibleOptions.eq(nextIndex);
            //         nextOption.addClass('active').css('background-color', '#f0f0f0');
            //         searchInput.val(nextOption.text()).attr('data-value', nextOption.data('value'));
            //         ValidateText = nextOption.text(); // Update ValidateText for validation
            //     } else if (e.key === 'Enter') {
            //         e.preventDefault();
            //         const inputText = searchInput.val();
            //         let isValid = inputText === ValidateText;

            //         if (!isValid) {
            //             toastr.error('Please select a valid option from the dropdown.');
            //             return;
            //         }

            //         var selectedRows = [];
            //         $('.order-checkbox:checked').each(function () {
            //             selectedRows.push($(this).val());
            //         });
            //         let operation;
            //         if ('{{ session()->get('business.manage_order_module') }}' == 'manual') {
            //             operation = '{{ auth()->user()->id }}';
            //         } else {
            //             operation = searchInput.attr('data-value');
            //         }

            //         if (selectedRows.length > 0 && operation) {
            //             $.ajax({
            //                 url: '/apply-order-operation',
            //                 type: 'POST',
            //                 data: {
            //                     ids: selectedRows,
            //                     operation: operation,
            //                     _token: '{{ csrf_token() }}'
            //                 },
            //                 success: function (response) {
            //                     toastr.success(response.message);
            //                     processingOrdersTable.ajax.reload();
            //                     $(".modal").modal("hide");
            //                 }
            //             });
            //         } else {
            //             toastr.error('Please select rows and Picker');
            //         }
            //     }
            // });

            // Where: Updated input handler. Difference: Changed ValidateText to '' when no options match.
            searchInput.on('input', function() {
                const searchText = $(this).val().toLowerCase();
                const options = dropdown.children('div');

                if (searchText === '') {
                    // Show all options if input is empty
                    options.show().removeClass('active').css('background-color', '');
                    ValidateText = ''; // Reset ValidateText
                    return;
                }

                let firstVisible = true;
                options.each(function() {
                    const optionText = $(this).text().toLowerCase();
                    if (optionText.includes(searchText)) {
                        $(this).show();
                        if (firstVisible) {
                            $(this).addClass('active').css('background-color', '#f0f0f0');
                            firstVisible = false;
                        } else {
                            $(this).removeClass('active').css('background-color', '');
                        }
                    } else {
                        $(this).hide().removeClass('active').css('background-color', '');
                    }
                });

                // Update ValidateText only if there’s a visible option, otherwise set to empty
                const firstVisibleOption = dropdown.children('div:visible').first();
                ValidateText = firstVisibleOption.length ? firstVisibleOption.text() : '';
            });

            //  Keydown handler (unchanged, but validation now works correctly).
            searchInput.on('keydown', function(e) {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const visibleOptions = dropdown.children('div:visible');
                    if (visibleOptions.length === 0) return;

                    let currentIndex = -1;
                    const currentSelected = dropdown.children('div.active');
                    if (currentSelected.length) {
                        currentIndex = visibleOptions.index(currentSelected);
                    }

                    let nextIndex;
                    if (e.key === 'ArrowDown') {
                        nextIndex = (currentIndex + 1) % visibleOptions.length;
                    } else if (e.key === 'ArrowUp') {
                        nextIndex = (currentIndex - 1 + visibleOptions.length) % visibleOptions.length;
                    }

                    dropdown.children('div').removeClass('active').css('background-color', '');
                    const nextOption = visibleOptions.eq(nextIndex);
                    nextOption.addClass('active').css('background-color', '#f0f0f0');
                    searchInput.val(nextOption.text()).attr('data-value', nextOption.data('value'));
                    ValidateText = nextOption.text(); // Update ValidateText for validation
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const inputText = searchInput.val().trim();

                    var selectedRows = [];
                    $('.order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    let operation;

                    if ('{{ session()->get('business.manage_order_module') }}' == 'manual') {
                        operation = '{{ auth()->user()->id }}';
                    } else {
                        // Check if inputText matches any dropdown option
                        let matchingOption = dropdown.children('div').filter(function() {
                            return $(this).text().toLowerCase() === inputText.toLowerCase();
                        });

                        if (matchingOption.length > 0) {
                            // Valid option found; use its data-value
                            operation = matchingOption.first().data('value');
                            searchInput.attr('data-value', operation); // Update input's data-value
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Please select or type a valid option from the dropdown.');
                            return;
                        }
                    }

                    if (selectedRows.length > 0 && operation) {
                        $.ajax({
                            url: '/apply-order-operation',
                            type: 'POST',
                            data: {
                                ids: selectedRows,
                                operation: operation,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.success(response.message);
                                processingOrdersTable.ajax.reload();
                                $(".modal").modal("hide");
                            },
                            error: function(xhr, status, error) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error('Failed to apply operation.');
                                console.error("AJAX error:", {
                                    status,
                                    error,
                                    responseText: xhr.responseText
                                });
                            }
                        });
                    } else {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select rows and a Picker');
                    }
                }
            });


            //   // Handle option click
            let ValidateText;
            dropdown.on('click', 'div', function() {
                ValidateText = $(this).text();
                const selectedText = $(this).text();
                const selectedValue = $(this).data('value');

                // Set the selected text and value
                searchInput.val(selectedText).attr('data-value', selectedValue);
                dropdown.hide();

            });

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.searchable-select').length) {
                    dropdown.hide();
                }
            });

            // asgin picker
            // $('#apply-operation').on('click', function() {

            //     var selectedRows = [];
            //     $('.order-checkbox:checked').each(function() {
            //         selectedRows.push($(this).val());
            //     });
            //     let operation;
            //     const inputText = $('#order-action').val();
            //     if ('{{ session()->get('business.manage_order_module') }}' == 'manual') {
            //         // fill current user id
            //         operation = '{{ auth()->user()->id }}';
            //     } else {
            //         operation = $('#order-action').attr('data-value');
            //     }

            //     let isValid = false;

            //     if (inputText === ValidateText) {
            //         isValid = true;
            //     }

            //     if (!isValid) {
            //          toastr.options.positionClass = 'toast-top-center';
            //         toastr.error('Please select a valid option from the dropdown.');
            //         return;
            //     }

            //     if (selectedRows.length > 0 && operation) {
            //         $.ajax({
            //             url: '/apply-order-operation',
            //             type: 'POST',
            //             data: {
            //                 ids: selectedRows,
            //                 operation: operation,
            //                 _token: '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                  toastr.options.positionClass = 'toast-top-center';
            //                 toastr.success(response.message);
            //                 processingOrdersTable.ajax.reload();
            //                 $(".modal").modal("hide");
            //             }
            //         });
            //     } else {
            //          toastr.options.positionClass = 'toast-top-center';
            //         toastr.error('Please select rows and Picker');
            //     }
            // });
            $('#apply-operation').on('click', function() {
                var selectedRows = [];
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                let operation;
                const inputText = $('#order-action').val().trim();

                if ('{{ session()->get('business.manage_order_module') }}' == 'manual') {
                    operation = '{{ auth()->user()->id }}';
                } else {
                    // Check if inputText matches any dropdown option
                    let matchingOption = $('#dropdown div').filter(function() {
                        return $(this).text().toLowerCase() === inputText.toLowerCase();
                    });

                    if (matchingOption.length > 0) {
                        // Valid option found; use its data-value
                        operation = matchingOption.first().data('value');
                        $('#order-action').attr('data-value', operation); // Update input's data-value
                    } else {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select or type a valid option from the dropdown.');
                        return;
                    }
                }

                if (selectedRows.length > 0 && operation) {
                    $.ajax({
                        url: '/apply-order-operation',
                        type: 'POST',
                        data: {
                            ids: selectedRows,
                            operation: operation,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success(response.message);
                            processingOrdersTable.ajax.reload();
                            $(".modal").modal("hide");
                        },
                        error: function(xhr, status, error) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Failed to apply operation.');
                            console.error("AJAX error:", {
                                status,
                                error,
                                responseText: xhr.responseText
                            });
                        }
                    });
                } else {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select rows and Picker');
                }
            });
            // end asgin picker

            // view order details
            $(document).on('click', '.invoice-link', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                if (!url) return;
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(result) {
                        $('.view_modal').html(result);
                        $('.view_modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error("Failed to load order details.");
                    }
                });
            });
            $(document).on('click', '.btn-modal_sell_pick_verify_data', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                if (!url) return;
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(result) {
                        $('#sell_pick_verify_data_modal').html(result);
                        $('#sell_pick_verify_data_modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error("Failed to load order details.");
                    }
                });
            });
            
            // end view order details

            // update priority
            $(document).on('click', '.decrease_priority_button', function(e) {
                e.preventDefault();
                var selectedRows = [];
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row.');
                    return;
                }
                if (selectedRows.length > 1) {
                    toastr.error('You can only select one row at a time.');
                    return;
                }                
                if (selectedRows.length > 0) {
                    $.ajax({
                        url: '/update-priorities',
                        type: 'POST',
                        data: {
                            ids: selectedRows,
                            priority: 'decrease'
                        },
                        success: function(response) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success('Priority updated successfully');
                            currentTable.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Failed to update priority');
                        }
                    });
                }
            });


            // Handle focus and navigation for Verify Picking and Start Picking popups

            $('#manual_pick_verify_modal').on('shown.bs.modal', function() {
                // Wait briefly to ensure modal content is fully rendered
                setTimeout(() => {
                    // Determine modal type (picker or verifier) based on URL or content
                    const isVerifier = window.location.search.includes('type=verifier') || $(this)
                        .find('[data-verifier-mode]').length > 0;


                    // Find all input.inline-pick fields in the modal
                    const qtyInputs = $(this).find(
                        'input.inline-pick:not(:disabled):visible:not([readonly])');

                    if (qtyInputs.length > 0) {
                        const firstInput = qtyInputs.first();
                        $('#barcode_scanner_input').focus();
                        // firstInput.focus();
                        // // Force focus if not successful
                        // if (document.activeElement !== firstInput[0]) {
                        //     firstInput[0].focus();
                        // }


                        // Handle Up and Down Arrow key navigation
                        qtyInputs.off('keydown.modalNavigation').on('keydown.modalNavigation',
                            function(e) {
                                if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown') {
                                    return; // Allow other keys (e.g., Enter, Tab) to function normally
                                }

                                e.preventDefault(); // Prevent default scrolling


                                const currentInput = $(this);
                                const currentIndex = qtyInputs.index(currentInput);
                                let nextIndex;

                                if (e.key === 'ArrowDown') {
                                    nextIndex = (currentIndex + 1) % qtyInputs.length;
                                } else if (e.key === 'ArrowUp') {
                                    nextIndex = (currentIndex - 1 + qtyInputs.length) %
                                        qtyInputs.length;
                                }

                                const nextInput = qtyInputs.eq(nextIndex);
                                if (nextInput.length) {
                                    nextInput.focus();
                                    // Force focus if not successful
                                    if (document.activeElement !== nextInput[0]) {
                                        nextInput[0].focus();
                                    }

                                }
                            });
                    }
                }, 100); // 100ms delay to ensure rendering
            });
            $('#manual_pick_verify_modal').on('shown.bs.modal', function() {
                const inputs = $(this).find('input.inline-pick').filter(':visible:enabled');

                // Auto-focus first input
                const firstInput = inputs.first();
                // if (firstInput.length) {
                //     firstInput.focus();

                // }
                $('#barcode_scanner_input').focus();

                // Handle Up/Down arrow navigation
                inputs.off('keydown.modalNavigation').on('keydown.modalNavigation', function(e) {
                    const current = $(this);
                    const currentIndex = inputs.index(current);
                    let nextIndex;

                    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        // Navigate between inputs
                        nextIndex = e.key === 'ArrowUp' ? currentIndex - 1 : currentIndex + 1;
                        if (nextIndex >= 0 && nextIndex < inputs.length) {
                            const nextInput = inputs.eq(nextIndex);
                            nextInput.focus();;
                        }
                    }

                    //                  else if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                    //               e.preventDefault();
                    //     // Adjust quantity
                    //                let currentValue = parseInt(current.val()) || 0;
                    //               if (e.key === 'ArrowRight') {
                    //                currentValue += 1;
                    //                } else if (e.key === 'ArrowLeft' && currentValue > 0) {
                    //             //    currentValue -= 1;
                    //            }
                    //     current.val(currentValue).trigger('change');

                    //     console.log(`Quantity updated: ${currentValue} for input`, current);
                    // }
                });
            });

            // Clean up event listeners when the modal is hidden
            $('#manual_pick_verify_modal').on('hidden.bs.modal', function() {
                $(this).find('input.inline-pick').off('keydown.modalNavigation');

            });
            $('#manual_pick_verify_modal').on('shown.bs.modal', function() {
                const firstInput = $(this).find('input.inline-pick').filter(':visible:enabled').first();
                if (firstInput.length) {
                    firstInput.focus();

                }
            });

            $(document).on('keydown.tabNav', function(e) {
                if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
                if ($('.modal').hasClass('in') || $('.modal').hasClass('show')) return;
                if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
                if (e.shiftKey) return;

                e.preventDefault();

                // Get all tab links in order
                const tabs = $('.nav-tabs li a[data-toggle="tab"]');


                // Find active tab (check li.active for Bootstrap 3)
                const activeTab = tabs.closest('li').filter('.active').find('a');
                const activeIndex = tabs.index(activeTab);


                let nextIndex;

                // Calculate next tab (Right: next, Left: previous, with wrapping)
                if (e.key === 'ArrowRight') {
                    nextIndex = (activeIndex + 1) % tabs.length;
                } else {
                    nextIndex = (activeIndex - 1 + tabs.length) % tabs.length;
                }



                // Switch to next tab
                const nextTab = tabs.eq(nextIndex);
                if (nextTab.length) {
                    nextTab.tab('show');
                    const tabId = nextTab.attr('href').substring(1); // e.g., 'processing_orders'
                    $(`#${tabId} .dataTables_wrapper`).focus();

                }
            });

            // $('#manual_pick_verify_modal').on('shown.bs.modal', function() {
            //     const modal = $(this);
            //     const inputs = modal.find('input.inline-pick').filter(':visible:enabled');
            //     console.log('Inputs found:', inputs.length, inputs.map((i, el) => ({
            //         id: el.id,
            //         value: el.value,
            //         visible: $(el).is(':visible'),
            //         enabled: !$(el).is(':disabled')
            //     })).get());

            //     // Auto-focus first input
            //     const firstInput = inputs.first();
            //     if (firstInput.length) {
            //         firstInput.focus();
            //         console.log('Auto-focused first inline-pick input in #manual_pick_verify_modal');
            //     } else {
            //         console.log('No visible/enabled inline-pick inputs found in #manual_pick_verify_modal');
            //     }

            //     // Handle Up/Down arrow navigation on modal level
            //     modal.off('keydown.modalNavigation').on('keydown.modalNavigation',
            //         'input.inline-pick:focus',
            //         function(e) {
            //             if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown') return;

            //             e.preventDefault();

            //             const current = $(this);
            //             const currentIndex = inputs.index(current);
            //             let nextIndex = e.key === 'ArrowUp' ? currentIndex - 1 : currentIndex + 1;

            //             console.log('Up/Down attempt:', {
            //                 key: e.key,
            //                 currentIndex,
            //                 nextIndex,
            //                 totalInputs: inputs.length
            //             });

            //             if (nextIndex >= 0 && nextIndex < inputs.length) {
            //                 const nextInput = inputs.eq(nextIndex);
            //                 nextInput.focus();
            //                 console.log(
            //                     `Navigated to input index ${nextIndex} in #manual_pick_verify_modal`
            //                 );
            //             } else {
            //                 console.log(`Navigation blocked: No input at index ${nextIndex}`, {
            //                     key: e.key,
            //                     currentIndex,
            //                     totalInputs: inputs.length
            //                 });
            //             }
            //         });
            // });

            $(document).on('click', '.increase_priority_button', function(e) {
                e.preventDefault();
                var selectedRows = [];
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select a row.');
                    return;
                }
                if (selectedRows.length > 1) {
                    toastr.error('You can only select one row at a time.');
                    return;
                }
                if (selectedRows.length > 0) {
                    $.ajax({
                        url: '/update-priorities',
                        type: 'POST',
                        data: {
                            ids: selectedRows,
                            priority: 'increase'
                        },
                        success: function(response) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.success('Priority updated successfully');
                            currentTable.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Failed to update priority');
                        }
                    });
                }
            });

            $(document).on('click', '.held_button', function() {
                var selectedRows = [];
                $('.order-checkbox:checked').each(function() {
                    selectedRows.push($(this).val());
                });
                if (selectedRows.length === 0) {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('Please select rows to assign.');
                    return;
                }
                // if (selectedRows.length > 1) {
                //     toastr.error('You can only select one row at a time.');
                //     return;
                // }
                $.ajax({
                    url: '/held',
                    type: 'GET',
                    data: {
                        ids: selectedRows
                    },
                    success: function(result) {
                        $('#held_modal').html(result).modal('show');
                        setTimeout(() => {
                            $('#modal_selected_order').val(selectedRows[0]);
                        }, 100);
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.status, xhr.responseText);
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Failed to load assign modal.');
                    }
                });
            });
            // get post of status change
            $(document).on('click', '.edit-picking-status', function() {
                return;
                // disabled this function
                var url = $(this).data('href');
                if (!url) return;
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(result) {
                        $('#change-picking-status-form-modal').html(result);
                        $('#change-picking-status-form-modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Failed to load change picking status modal.');
                    }
                });
            });
            // Shipping Stations button click handler
            $(document).on('click', '.shipping_stations_button', function(e) {
                e.preventDefault();
                let table = "#" + currentTable.table().node().id;
                var selectedOrderId = null;

                if (table == "#picked-orders-table") {
                    var selectedRows = [];
                    $(table + ' .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length !== 1) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.info('Please select one row');
                        return; // Add return to prevent further execution
                    }
                    selectedOrderId = selectedRows[0];
                }

                // First, get the order's shipping station ID if available
                var orderShippingStationId = null;
                if (selectedOrderId) {
                    // Try to get shipping station from table row data
                    var rowData = currentTable.table().row(function(idx, data, node) {
                        return data.id == selectedOrderId;
                    }).data();
                    if (rowData && rowData.shipping_station_id) {
                        orderShippingStationId = rowData.shipping_station_id;
                    }
                }

                let url = "{{ action([\App\Http\Controllers\ShippingStationController::class, 'index']) }}?for_selection=1";
                if (selectedOrderId) {
                    url += '&order_id=' + selectedOrderId;
                }
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(result) {
                        if (!result.success || !result.stations || result.stations.length === 0) {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.warning('@lang("No shipping stations available")');
                            return;
                        }

                        // Get the order's shipping station ID from result if available
                        var preSelectedStationId = result.order_shipping_station_id || orderShippingStationId || null;

                        // Create a modal to display shipping stations for selection
                        var stationsHtml = '<div class="table-responsive"><table class="table table-bordered table-striped">';
                        stationsHtml += '<thead><tr><th>Select</th><th>@lang("Name")</th><th>@lang("Station Code")</th><th>@lang("Assigned User")</th><th>@lang("Status")</th></tr></thead>';
                        stationsHtml += '<tbody>';
                        
                        result.stations.forEach(function(station) {
                            var statusClass = station.is_active ? 'label-success' : 'label-danger';
                            var statusText = station.is_active ? '@lang("lang_v1.active")' : '@lang("lang_v1.inactive")';
                            var userText = station.user ? station.user.first_name : '@lang("lang_v1.none")';
                            var isChecked = (preSelectedStationId && preSelectedStationId == station.id) ? 'checked' : '';
                            var rowClass = isChecked ? 'info' : '';
                            stationsHtml += '<tr style="cursor: pointer;" class="station-row ' + rowClass + '" data-station-id="' + station.id + '">';
                            stationsHtml += '<td><input type="radio" name="selected_station" value="' + station.id + '" class="station-radio" ' + isChecked + '></td>';
                            stationsHtml += '<td>' + (station.name || '') + '</td>';
                            stationsHtml += '<td>' + (station.station_code || '') + '</td>';
                            stationsHtml += '<td>' + userText + '</td>';
                            stationsHtml += '<td><span class="label ' + statusClass + '">' + statusText + '</span></td>';
                            stationsHtml += '</tr>';
                        });
                        
                        stationsHtml += '</tbody></table></div>';
                        
                        var modalHtml = `
                            <div class="modal fade" id="shipping_stations_modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title">@lang('Select Shipping Station')</h4>
                                        </div>
                                        <div class="modal-body">
                                            ${stationsHtml}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                                            <button type="button" class="btn btn-primary" id="select_station_btn">@lang('Select Station')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remove existing modal if any
                        $('#shipping_stations_modal').remove();
                        
                        // Append modal to body
                        $('body').append(modalHtml);
                        
                        // Show modal
                        $('#shipping_stations_modal').modal('show');
                        
                        // Scroll to pre-selected station if any
                        if (preSelectedStationId) {
                            var selectedRow = $('#shipping_stations_modal').find('tr[data-station-id="' + preSelectedStationId + '"]');
                            if (selectedRow.length) {
                                setTimeout(function() {
                                    var modalBody = $('#shipping_stations_modal .modal-body');
                                    var scrollTop = selectedRow.offset().top - modalBody.offset().top + modalBody.scrollTop() - 100;
                                    modalBody.scrollTop(scrollTop);
                                }, 100);
                            }
                        }
                        
                        // Make rows clickable to select radio
                        $('#shipping_stations_modal').on('click', '.station-row', function(e) {
                            if ($(e.target).is('input[type="radio"]')) {
                                return; // Don't trigger if clicking directly on radio
                            }
                            $(this).find('input[type="radio"]').prop('checked', true);
                            $(this).closest('tbody').find('tr').removeClass('info');
                            $(this).addClass('info');
                        });
                        
                        // Handle station selection
                        $('#select_station_btn').off('click').on('click', function() {
                            var selectedStationId = $('#shipping_stations_modal input[name="selected_station"]:checked').val();
                            if (!selectedStationId) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.warning('@lang("Please select a shipping station")');
                                return;
                            }
                            
                            // Find the selected station data
                            var selectedStation = result.stations.find(function(s) {
                                return s.id == selectedStationId;
                            });
                            
                            if (selectedStation) {
                                // Store selected station globally
                                window.selectedShippingStation = selectedStation;
                                
                                // Store selected order ID if available (from outer scope)
                                var currentTableId = "#" + currentTable.table().node().id;
                                var orderId = null;
                                if (currentTableId == "#picked-orders-table") {
                                    var orderSelectedRows = [];
                                    $(currentTableId + ' .order-checkbox:checked').each(function() {
                                        orderSelectedRows.push($(this).val());
                                    });
                                    if (orderSelectedRows.length > 0) {
                                        orderId = orderSelectedRows[0];
                                        window.selectedOrderForShippingStation = orderId;
                                    }
                                }
                                
                                // Save shipping station to transaction in database
                                if (orderId) {
                                    $.ajax({
                                        url: "{{ route('shipping-stations.save-to-transaction') }}",
                                        method: 'POST',
                                        data: {
                                            transaction_id: orderId,
                                            shipping_station_id: selectedStation.id,
                                            _token: '{{ csrf_token() }}'
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                toastr.options.positionClass = 'toast-top-center';
                                                toastr.success(response.msg || '@lang("Shipping station assigned successfully")');
                                            } else {
                                                toastr.options.positionClass = 'toast-top-center';
                                                toastr.error(response.msg || '@lang("Failed to save shipping station")');
                                            }
                                        },
                                        error: function(xhr) {
                                            console.error('Error saving shipping station:', xhr);
                                            var errorMsg = '@lang("Failed to save shipping station")';
                                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                                errorMsg = xhr.responseJSON.msg;
                                            }
                                            toastr.options.positionClass = 'toast-top-center';
                                            toastr.error(errorMsg);
                                        }
                                    });
                                }
                                
                                // Show success message
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.success('@lang("Shipping station selected: ") ' + selectedStation.name);
                                
                                // Close modal
                                $('#shipping_stations_modal').modal('hide');
                                
                                // Trigger custom event for other parts of the code to use
                                $(document).trigger('shipping_station_selected', [selectedStation, orderId]);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', {xhr, status, error});
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Failed to load shipping stations. Please try again.');
                    }
                });
            });
            $(document).on('click', '.make_shipment', function(e) {
                e.preventDefault();
                let table = "#" + currentTable.table().node().id;

                if (table == "#picked-orders-table") {
                    let operation = '';
                    var selectedRows = [];
                    $(table + ' .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length !== 1) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.info('Please select one row');
                        return; // Add return to prevent further execution
                    }
                    let url = '/sells-packing/' + selectedRows[0] + '?_=' + new Date().getTime();
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(result) {

                            $('#modal_shipment_packing_modal').html(result);
                            $('#modal_shipment_packing_modal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', {
                                xhr,
                                status,
                                error
                            });
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Failed to load modal. Please try again.');
                        }
                    });
                }
            });



            $('#held_modal').on('submit', function(e) {
                e.preventDefault();
                var type = $('#handoverType').val();
                var staff = $('#selected-staff').val();
                let orders = [...new Set($('.carrier-checkbox:checked').map(function() {
                    return $(this).val();
                }).get())];

                $.ajax({
                    url: '/held',
                    type: 'POST',
                    data: {
                        type: type,
                        staff: staff,
                        orders: orders
                    },
                    success: function(response) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr[response.status ? 'success' : 'error'](response.message);
                        $('#picking-orders-table').DataTable().ajax.reload();

                        $('#held_modal').closest('.modal').modal('hide');

                    },
                    error: function() {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Something went wrong');
                        $('#picking-orders-table').DataTable().ajax.reload();
                    }
                });
            });


            // end update priority
            document.addEventListener('keydown', function(event) {
                // Ignore shortcuts while typing in inputs/textareas/selects or when a modal is open
                const target = event.target;
                const tagName = target && target.tagName ? target.tagName.toLowerCase() : '';
                const isEditable = (target && (target.isContentEditable || tagName === 'input' || tagName === 'textarea' || tagName === 'select'));
                const isModalOpen = !!document.querySelector('.modal.show');
                if (isEditable || isModalOpen||event.key=="Enter") {
                    return;
                }

                // Check if Shift is pressed
                if (event.shiftKey) {
                    // switch (event.key) {
                    //     case 'ArrowLeft':
                    //         onShiftLeft();
                    //         break;
                    //     case 'ArrowRight':
                    //         onShiftRight();
                    //         break;
                    // }

                    switch (event.key) {
                        case 'ArrowLeft':
                            onShiftLeft();
                            break;
                        case 'ArrowRight':
                            onShiftRight();
                            break;
                        case 'P':
                        case 'p':
                            onShiftP();
                            break;
                        case 'V':
                        case 'v':

                            onShiftV();
                            break;
                        case 'C':
                        case 'c':

                            onShiftC();
                            break;
                    }
                }
            });

            function onShiftV() {
                let table = "#" + currentTable.table().node().id;
                if (table == "#picking-orders-table") {
                    let verifyingOrderId = $(table + ' .order-checkbox:checked').first().val();
                    if ($(table + ' .order-checkbox:checked').length > 1) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select only one row to start verifying');
                        return;
                    }
                    if (!verifyingOrderId || verifyingOrderId.length === 0) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select a row to start verifying');
                        return;
                    }
                    // Get the row data for the selected order
                    let row = pickingOrdersTable.row(function(idx, data) {
                        return data.id == verifyingOrderId;
                    }).data();

                    // Check picking_status
                    if (row && $(row.picking_status).data('status') === 'VERIFIED') {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('This order has already been verified');
                        return;
                    }
                    let startVerifyingUrl = '/sells-picking-popup/' + verifyingOrderId + '?type=verifier';
                    try {
                        $.ajax({
                            url: startVerifyingUrl,
                            type: 'GET',
                            accept: 'text/html',
                            success: function(response) {
                                try {
                                    const json = typeof response === 'string' ? JSON.parse(response) :
                                        response;
                                    if (json && typeof json === 'object' && json.status === false) {
                                        toastr.options.positionClass = 'toast-top-center';
                                        toastr.error(json.message);
                                        return;
                                    }
                                    $('#manual_pick_verify_modal').html(response);
                                    $('#manual_pick_verify_modal').modal('show');
                                } catch (e) {
                                    $('#manual_pick_verify_modal').html(response);
                                    $('#manual_pick_verify_modal').modal('show');
                                }
                                //  Focus the first input.inline-pick field in the Verify Picking popup
                                $('#manual_pick_verify_modal').one('shown.bs.modal', function() {
                                    const firstQtyInput = $('#manual_pick_verify_modal').find(
                                        'input.inline-pick').first();
                                    if (firstQtyInput.length) {
                                        firstQtyInput.focus();

                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error('Failed to load manual pick verify modal.');
                                console.error("AJAX error:", {
                                    status,
                                    error,
                                    responseText: xhr.responseText
                                });
                            }
                        });
                    } catch (e) {
                        console.error("Error in onShiftV AJAX:", e);
                    }
                }
            }

            // Why: Implements Shift + C to cancel orders in Pending tab (May 28, 2025, 4:54 PM IST), allows multi-row selection, uses SweetAlert.
            // Where: Replaced onShiftC. Difference: Updated to match .pending_cancel_button, uses pending-orders-table, sends array of IDs.
            function onShiftC() {
                let table = "#" + currentTable.table().node().id;
                if (table == "#processing-orders-table") {
                    let selectedRows = [];
                    $(table + ' .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length === 0) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select at least one row to cancel');
                        return;
                    }
                    swal({
                        icon: 'warning',
                        title: 'Cancel Order',
                        text: 'Are you sure you want to cancel and release available stock!',
                        buttons: {
                            cancel: 'Cancel',
                            confirm: 'Yes, Change'
                        }
                    }).then((change) => {
                        if (change) {
                            $.ajax({
                                url: '/cancel-so',
                                type: 'POST',
                                data: {
                                    ids: selectedRows,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status) {
                                        toastr.options.positionClass = 'toast-top-center';
                                        toastr.success(response.message);
                                        $('#pending-orders-table').DataTable().ajax.reload();
                                        currentTable.columns.adjust().draw();
                                    } else {
                                        toastr.options.positionClass = 'toast-top-center';
                                        toastr.error(response.message);
                                        $('#pending-orders-table').DataTable().ajax.reload();
                                    }
                                },
                                error: function() {
                                    toastr.options.positionClass = 'toast-top-center';
                                    toastr.error('something went wrong');
                                    $('#pending-orders-table').DataTable().ajax.reload();
                                }
                            });
                        }
                    });
                }
            }

            function onShiftP() {
                let table = "#" + currentTable.table().node().id;
                if (table == "#picking-orders-table") {
                    let pickingOrderId = $(table + ' .order-checkbox:checked').first().val();
                    if ($(table + ' .order-checkbox:checked').length > 1) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select only one row to start picking');
                        return;
                    }
                    if (!pickingOrderId) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select a row to start picking');
                        return;
                    }
                    let startPickingUrl = '/sells-picking-popup/' + pickingOrderId;
                    try {
                        $.ajax({
                            url: startPickingUrl,
                            type: 'GET',
                            accept: 'text/html',
                            success: function(response) {

                                try {
                                    const json = typeof response === 'string' ? JSON.parse(response) :
                                        response;
                                    if (json && typeof json === 'object' && json.status === false) {
                                        toastr.options.positionClass = 'toast-top-center';
                                        toastr.error(json.message);
                                        return;
                                    }
                                    $('#manual_pick_verify_modal').html(response);
                                    $('#manual_pick_verify_modal').modal('show');
                                } catch (e) {
                                    $('#manual_pick_verify_modal').html(response);
                                    $('#manual_pick_verify_modal').modal('show');
                                }
                                // Grok: Focus the first input.inline-pick field in the Start Picking popup
                                $('#manual_pick_verify_modal').one('shown.bs.modal', function() {
                                    const firstQtyInput = $('#manual_pick_verify_modal').find(
                                        'input.inline-pick').first();
                                    if (firstQtyInput.length) {
                                        firstQtyInput.focus();
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error('Failed to load Start Picking popup.');
                                console.error("AJAX error:", {
                                    status,
                                    error,
                                    responseText: xhr.responseText
                                });
                            }
                        });
                    } catch (e) {
                        console.error("Error in onShiftP AJAX:", e);
                    }
                }
            }



            function onShiftLeft() {
                let table = "#" + currentTable.table().node().id;
                if (table === "#picking-orders-table") {
                    var selectedRows = [];
                    $('#picking-orders-table .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length === 0) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select at least one row to move to Pending.');
                        return;
                    }
                    $.ajax({
                        url: '/process-To-pending',
                        type: 'POST',
                        data: {
                            ids: selectedRows,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.success(response.message);
                                selectedRows.forEach(function(orderId) {
                                    pickingOrdersTable.rows().every(function() {
                                        var row = this.node();
                                        var rowId = $(row).find('.order-checkbox')
                                            .val();
                                        if (rowId == orderId) {
                                            this.remove();
                                        }
                                    });
                                });
                                pickingOrdersTable.draw();
                                processingOrdersTable.ajax.reload(null, false);
                            } else {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Error occurred while moving orders to Pending.');
                        }
                    });
                } else if (table === "#picked-orders-table") {
                    var selectedRows = [];
                    $('#picked-orders-table .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length === 0) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select at least one row to move to Processing.');
                        return;
                    }
                    $.ajax({
                        url: '/packing-to-process',
                        type: 'POST',
                        data: {
                            ids: selectedRows,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.success(response.message);
                                selectedRows.forEach(function(orderId) {
                                    pickedOrdersTable.rows().every(function() {
                                        var row = this.node();
                                        var rowId = $(row).find('.order-checkbox')
                                            .val();
                                        if (rowId == orderId) {
                                            this.remove();
                                        }
                                    });
                                });
                                pickedOrdersTable.draw();
                                pickingOrdersTable.ajax.reload(null, false);
                            } else {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Error occurred while moving orders to Processing.');
                        }
                    });
                }
            }


            function getActivePicker(callback) {
                $.ajax({
                    url: '/get-active-picker',
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            callback(response.picker_id);
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error(response.message);
                            callback(null);
                        }
                    },
                    error: function() {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Error occurred while getting active picker.');
                        callback(null);
                    }
                });
            }

            function onShiftRight() {
                let table = "#" + currentTable.table().node().id;
                if (table == "#processing-orders-table") {
                    var selectedRows = [];
                    $(table + ' .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    getActivePicker(function(activePicker) {
                        let operation = '';
                        if ('{{ session()->get('business.manage_order_module') }}' == 'manual'|| activePicker!=null) {
                            operation ='{{ auth()->user()->id }}';
                            activePicker=activePicker??null;
                        if (selectedRows.length > 0 && operation) {
                            $.ajax({
                                url: '/apply-order-operation',
                                type: 'POST',
                                data: {
                                    ids: selectedRows,
                                    operation: operation,
                                    activePicker: activePicker,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    toastr.options.positionClass = 'toast-top-center';
                                    toastr.success(response.message);
                                    processingOrdersTable.ajax.reload();
                                    $(".modal").modal("hide");
                                }
                            });
                        } else {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Please select rows and Picker');
                        }
                        return;
                    }
                    if (selectedRows.length > 0) {
                        $("#AssignPickerButton").modal("show").one('shown.bs.modal', function() {
                            const orderActionInput = $("#order-action");
                            if (orderActionInput.length) {
                                console.log("")
                                orderActionInput.focus();
                                if (document.activeElement === orderActionInput[0]) {
                                    console.log("Successfully focused on #order-action");
                                } else {
                                    console.log("");
                                    setTimeout(function() {
                                        orderActionInput.focus();

                                    }, 100);
                                }
                            }
                        });
                    } else {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select rows and Picker');
                        $(this).closest(".modal").modal("hide");
                    }
                    });
                } else if (table == "#picking-orders-table") {
                    var selectedRows = [];
                    $('#picking-orders-table .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length === 0) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.error('Please select at least one row to move to Packing.');
                        return;
                    }
                    $.ajax({
                        url: '/process-to-packing',
                        type: 'POST',
                        data: {
                            ids: selectedRows,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.success(response.message);
                                selectedRows.forEach(function(orderId) {
                                    pickingOrdersTable.rows().every(function() {
                                        var row = this.node();
                                        var rowId = $(row).find('.order-checkbox')
                                            .val();
                                        if (rowId == orderId) {
                                            this.remove();
                                        }
                                    });
                                });
                                pickingOrdersTable.draw();
                                pickedOrdersTable.ajax.reload(null, false);
                            } else {
                                toastr.options.positionClass = 'toast-top-center';
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Error occurred while moving orders to Packing.');
                        }
                    });
                } else if (table == "#picked-orders-table") {
                    var selectedRows = [];
                    $(table + ' .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length !== 1) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.info('Please select one row');
                        return;
                    }
                    let url = '/sells-packing/' + selectedRows[0] + '?_=' + new Date().getTime();
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(result) {

                            $('#modal_shipment_packing_modal').html(result);
                            $('#modal_shipment_packing_modal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', {
                                xhr,
                                status,
                                error
                            });
                            toastr.options.positionClass = 'toast-top-center';
                            toastr.error('Failed to load modal. Please try again.');
                        }
                    });
                }
            }

            // Guide button click handler
            $('#guide-button').on('click', function() {
                $('#keyboard-shortcuts-modal').modal('show');
            });

            // Print shortcuts function
            window.printShortcuts = function() {
                var printWindow = window.open('', '_blank');
                var modalContent = $('#keyboard-shortcuts-modal .modal-body').html();
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Keyboard Shortcuts Guide - Order Fulfillment System</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            h1 { color: #333; text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
                            h5 { color: #007bff; margin-top: 20px; margin-bottom: 10px; }
                            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f8f9fa; font-weight: bold; }
                            kbd { background-color: #f8f9fa; border: 1px solid #ccc; border-radius: 3px; padding: 2px 6px; font-family: monospace; }
                            .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
                            .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
                            .alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
                            ul { margin: 10px 0; }
                            li { margin: 5px 0; }
                            .text-primary { color: #007bff; }
                            .text-success { color: #28a745; }
                            .text-warning { color: #ffc107; }
                            .text-info { color: #17a2b8; }
                            .text-purple { color: #6f42c1; }
                            @media print {
                                body { margin: 0; }
                                .no-print { display: none; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1><i class="fas fa-keyboard"></i> Keyboard Shortcuts Guide</h1>
                        <div class="alert alert-info">
                            <strong>Tip:</strong> Use these keyboard shortcuts to navigate and operate the order fulfillment system more efficiently.
                        </div>
                        ${modalContent}
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            };
            $(document).on('click', '.complete_order', function(e) {
                e.preventDefault();
                let table = "#" + currentTable.table().node().id;

                if (table == "#picked-orders-table") {
                    var selectedRows = [];
                    $(table + ' .order-checkbox:checked').each(function() {
                        selectedRows.push($(this).val());
                    });
                    if (selectedRows.length !== 1) {
                        toastr.options.positionClass = 'toast-top-center';
                        toastr.info('Please select one row');
                        return;
                    }

                    var orderId = selectedRows[0];

                    swal({
                        title: 'Complete Order',
                        text: 'Are you sure you want to complete the order?',
                        icon: 'warning',
                        buttons: ['Cancel', 'Complete'],
                        dangerMode: true,
                    }).then((willComplete) => {
                        if (willComplete) {
                            let data = {
                                sale_invoice_no: orderId
                            };
                            $('#main_loader').removeClass("hidden");
                            $.ajax({
                                url: '/sells-invoice-store',
                                method: "POST",
                                contentType: "application/json",
                                data: JSON.stringify(data),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    $('#main_loader').addClass("hidden");
                                    if (response.status) {
                                        toastr.success(response.msg);
                                        pickedOrdersTable.ajax.reload(null, false);
                                        swal({
                                            title: "Do you want to send an email?",
                                            text: "This will open the email template modal.",
                                            icon: "warning",
                                            buttons: ["No", "Yes"],
                                            dangerMode: true,
                                        }).then((willSend) => {
                                            if (willSend) {
                                                let url =
                                                    "/notification/get-template/" +
                                                    response.transaction +
                                                    "/local_pickup";

                                                $.ajax({
                                                    url: url,
                                                    type: "GET",
                                                    success: function (
                                                        modalContent
                                                    ) {
                                                        $('.view_modal')
                                                            .html(
                                                                modalContent
                                                            )
                                                            .modal(
                                                                'show'
                                                            );
                                                    },
                                                    error: function () {
                                                        toastr
                                                            .error(
                                                                "Failed to load the email template."
                                                            );
                                                    }
                                                });
                                            }
                                        });
                                    } else {
                                        // Enhanced error handling for child orders
                                        var errorMsg = response.msg || response.message || 'An error occurred';
                                        
                                        // Check if this is a child order error
                                        if (response.is_child_order || errorMsg.includes('child order') || errorMsg.includes('parent order')) {
                                            swal({
                                                title: 'Cannot Create Invoice',
                                                text: errorMsg,
                                                icon: 'warning',
                                                buttons: {
                                                    cancel: 'Close',
                                                }
                                            });
                                        } else {
                                            toastr.error(errorMsg);
                                        }
                                    }
                                },
                                error: function (xhr) {
                                    $('#main_loader').addClass("hidden");
                                    var errorMsg = 'There was an error processing your request. Please try again.';
                                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                                        errorMsg = xhr.responseJSON.msg;
                                    }
                                    toastr.error(errorMsg);
                                }
                            });
                        }
                    });
                } else {
                    toastr.options.positionClass = 'toast-top-center';
                    toastr.error('This action can only be performed on picked orders.');
                }
            });

        });
    </script>
@endsection

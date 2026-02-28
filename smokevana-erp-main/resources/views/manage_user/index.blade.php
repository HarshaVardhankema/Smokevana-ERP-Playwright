@extends('layouts.app')
@section('title', __( 'user.users' ))

@section('css')
<style>
    /* Amazon-style User Management Styles */
    .amazon-users-container {
        padding: 15px 20px 25px;
        background: #EAEDED;
        min-height: calc(100vh - 60px);
    }

    /* Hide default content-header from layout */
    .content-header {
        display: none !important;
    }

    /* Top banner (All users) – same style as product header */
    .amazon-users-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-top: 4px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
        color: #f9fafb;
    }

    .amazon-users-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .amazon-users-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }

    .amazon-users-header-title i {
        font-size: 22px;
        color: #ffffff;
    }

    .amazon-users-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }

    /* Main Card Container */
    .amazon-card {
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    /* Card Header with Controls */
    .amazon-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid #e7e7e7;
        flex-wrap: wrap;
        gap: 12px;
        background: #fafafa;
        border-radius: 8px 8px 0 0;
    }

    .amazon-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .amazon-card-title h2 {
        font-size: 18px;
        font-weight: 700;
        color: #0f1111;
        margin: 0;
        font-family: "Amazon Ember", Arial, sans-serif;
    }

    .amazon-user-count {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        background: #232f3e;
        color: #fff;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        font-family: "Amazon Ember", Arial, sans-serif;
    }

    /* Add User Button - Amazon Style */
    .amazon-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: linear-gradient(to bottom, #f7dfa5, #f0c14b);
        border: 1px solid;
        border-color: #a88734 #9c7e31 #846a29;
        border-radius: 4px;
        color: #0f1111;
        font-size: 13px;
        font-weight: 500;
        font-family: "Amazon Ember", Arial, sans-serif;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.1s ease-in-out;
    }

    .amazon-add-btn:hover {
        background: linear-gradient(to bottom, #f5d78e, #eeb933);
        text-decoration: none;
        color: #0f1111;
    }

    .amazon-add-btn:active {
        background: #f0c14b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2) inset;
    }

    .amazon-add-btn svg {
        width: 16px;
        height: 16px;
    }

    /* Controls Row */
    .amazon-controls-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid #e7e7e7;
        flex-wrap: wrap;
        gap: 12px;
        background: #fff;
    }

    .amazon-controls-left {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        flex: 1;
    }

    .amazon-controls-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Entries Selector */
    .amazon-entries-selector {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #0f1111;
        font-family: "Amazon Ember", Arial, sans-serif;
    }

    .amazon-entries-selector select {
        padding: 7px 30px 7px 12px;
        border: 1px solid #d5d9d9;
        border-radius: 4px;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") no-repeat right 8px center;
        background-size: 16px;
        font-size: 13px;
        color: #0f1111;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .amazon-entries-selector select:focus {
        outline: none;
        border-color: #e77600;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.2);
    }

    /* Search Box - Increased Width */
    .amazon-search-box {
        position: relative;
        width: 380px;
        flex: 1;
        max-width: 450px;
    }

    .amazon-search-box input {
        width: 100%;
        padding: 8px 12px 8px 40px;
        border: 1px solid #d5d9d9;
        border-radius: 4px;
        font-size: 13px;
        color: #0f1111;
        font-family: "Amazon Ember", Arial, sans-serif;
        background: #fff;
    }

    .amazon-search-box input:focus {
        outline: none;
        border-color: #e77600;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.2);
    }

    .amazon-search-box input::placeholder {
        color: #888;
    }

    .amazon-search-box .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        width: 18px;
        height: 18px;
    }

    /* Export & Control Buttons */
    .amazon-export-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 12px;
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 4px;
        color: #0f1111;
        font-size: 12px;
        font-weight: 500;
        font-family: "Amazon Ember", Arial, sans-serif;
        cursor: pointer;
        transition: all 0.1s ease-in-out;
    }

    .amazon-export-btn:hover {
        background: #f7fafa;
        border-color: #a2a6a6;
        color: #0f1111;
    }

    .amazon-export-btn:active {
        background: #edeeee;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1) inset;
    }

    .amazon-export-btn svg {
        width: 14px;
        height: 14px;
    }

    .amazon-export-btn.column-visibility {
        background: #232f3e;
        border-color: #232f3e;
        color: #fff;
    }

    .amazon-export-btn.column-visibility:hover {
        background: #37475a;
        border-color: #37475a;
        color: #fff;
    }

    /* Hide ALL DataTables default controls */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate,
    .dataTables_wrapper .dt-buttons,
    .dataTables_wrapper > .row:first-child,
    .dataTables_wrapper > .row:last-child,
    div.dt-buttons {
        display: none !important;
    }

    /* Table Styles */
    .amazon-table-container {
        overflow-x: auto;
        position: relative;
        min-height: 120px;
    }

    .amazon-table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Amazon Ember", Arial, sans-serif;
    }

    .amazon-table thead {
        background: #232F3E !important;
        position: relative;
        z-index: 10;
    }

    .amazon-table thead tr {
        position: relative;
    }

    /* Orange stripe below header - same as Sales Order/Invoice */
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
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #fff !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: none;
        white-space: nowrap;
        background: #232F3E !important;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }

    .amazon-table thead th:last-child {
        border-right: none;
    }

    .amazon-table thead th.sortable,
    .amazon-table thead th.sorting,
    .amazon-table thead th.sorting_asc,
    .amazon-table thead th.sorting_desc {
        cursor: pointer;
        user-select: none;
        padding-right: 24px;
    }

    .amazon-table thead th.sortable:hover,
    .amazon-table thead th.sorting:hover {
        background: #37475a !important;
    }

    /* Orange sort icons - same as Sales Order/Invoice */
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
    }

    .amazon-table thead th.sorting_asc::after {
        content: "↑";
    }

    .amazon-table thead th.sorting_desc::after {
        content: "↓";
    }

    /* Override DataTables default header styles - match Sales Order/Invoice */
    #users_table.dataTable thead th {
        background: #232F3E !important;
        color: #FFFFFF !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    #users_table.dataTable thead th.sorting::after,
    #users_table.dataTable thead th.sorting_asc::after,
    #users_table.dataTable thead th.sorting_desc::after {
        color: #ff9900 !important;
    }

    #users_table.dataTable thead tr::after {
        content: '' !important;
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 4px !important;
        background: #ff9900 !important;
        z-index: 12 !important;
    }

    .amazon-table tbody tr {
        border-bottom: 1px solid #e7e7e7;
        transition: background 0.15s ease;
    }

    .amazon-table tbody tr:hover {
        background: #f7fafa;
    }

    .amazon-table tbody td {
        padding: 12px 16px;
        font-size: 14px;
        color: #0f1111;
        vertical-align: middle;
    }

    .amazon-table tbody td.username-cell {
        font-weight: 500;
    }

    .amazon-table tbody td .email-cell {
        color: #007185;
    }

    .amazon-table tbody td .email-cell:hover {
        color: #c7511f;
        text-decoration: underline;
        cursor: pointer;
    }

    /* Role Badge */
    .amazon-role-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #f0f2f2;
        border: 1px solid #d5d9d9;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        color: #0f1111;
    }

    .amazon-role-badge.admin {
        background: #fef3cd;
        border-color: #ffc107;
        color: #856404;
    }

    .amazon-role-badge.store-admin {
        background: #d1ecf1;
        border-color: #17a2b8;
        color: #0c5460;
    }

    .amazon-role-badge.picker {
        background: #d4edda;
        border-color: #28a745;
        color: #155724;
    }

    /* Action Buttons */
    .amazon-action-buttons {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .amazon-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        font-family: "Amazon Ember", Arial, sans-serif;
        cursor: pointer;
        transition: all 0.1s ease-in-out;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid;
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        text-shadow: none !important;
    }

    /* Remove glow from action buttons in All users table - override any vendor/layout styles */
    #users_table .amazon-action-buttons .amazon-action-btn,
    #users_table .amazon-action-buttons .amazon-action-btn:hover,
    #users_table .amazon-action-buttons .amazon-action-btn:focus,
    #users_table .amazon-action-buttons .amazon-action-btn:focus-visible,
    #users_table .amazon-action-buttons .amazon-action-btn:active {
        box-shadow: none !important;
        outline: none !important;
        filter: none !important;
        text-shadow: none !important;
    }

    .amazon-action-btn svg {
        width: 14px;
        height: 14px;
    }

    /* Edit/Delete/View styled by amazon-theme.css */

    /* Pagination Container */
    .amazon-pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-top: 1px solid #e7e7e7;
        background: #fafafa;
        border-radius: 0 0 8px 8px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .amazon-pagination-info {
        font-size: 13px;
        color: #565959;
        font-family: "Amazon Ember", Arial, sans-serif;
    }

    .amazon-pagination-info strong {
        color: #0f1111;
        font-weight: 600;
    }

    /* Custom Pagination */
    .amazon-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .amazon-pagination .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 10px;
        border: 1px solid #d5d9d9;
        border-radius: 4px;
        background: #fff;
        color: #0f1111;
        font-size: 13px;
        font-family: "Amazon Ember", Arial, sans-serif;
        cursor: pointer;
        transition: all 0.1s ease-in-out;
    }

    .amazon-pagination .page-btn:hover:not(.disabled):not(.active) {
        background: #f7fafa;
        border-color: #a2a6a6;
    }

    .amazon-pagination .page-btn.active {
        background: #232f3e;
        border-color: #232f3e;
        color: #fff;
        font-weight: 600;
    }

    .amazon-pagination .page-btn.disabled {
        background: #f7f7f7;
        color: #999;
        cursor: not-allowed;
    }

    .amazon-pagination .page-btn.nav-btn {
        font-weight: 500;
    }

    /* Processing Overlay - confine to table area only */
    .amazon-table-container .dataTables_wrapper {
        position: relative;
    }
    .amazon-table-container .dataTables_processing {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        padding: 15px 25px !important;
        background: #fff !important;
        border: 1px solid #d5d9d9 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        color: #0f1111 !important;
        font-family: "Amazon Ember", Arial, sans-serif !important;
        z-index: 100 !important;
    }

    /* Empty State */
    .amazon-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #565959;
    }

    .amazon-empty-state svg {
        width: 64px;
        height: 64px;
        color: #d5d9d9;
        margin-bottom: 16px;
    }

    .amazon-empty-state p {
        font-size: 16px;
        margin: 0;
    }

    /* Column Visibility Dropdown - hidden by default, prevent flash on load/refresh */
    .column-visibility-dropdown {
        position: relative;
        display: inline-block;
    }

    .column-visibility-menu {
        display: none !important;
        visibility: hidden;
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 4px;
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        min-width: 180px;
        padding: 8px 0;
    }

    .column-visibility-menu.show {
        display: block !important;
        visibility: visible;
    }

    /* Hide DataTables button collections / colvis dropdowns that may leak from global scripts */
    .dt-button-collection,
    .amazon-users-container .dt-button-collection,
    ul.dt-button-collection,
    div.dt-button-collection {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        height: 0 !important;
        overflow: hidden !important;
    }

    /* Hide DataTables processing overlay - can cause white box on load/refresh */
    .amazon-users-container .dataTables_processing {
        display: none !important;
    }

    .column-visibility-menu label {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        cursor: pointer;
        font-size: 13px;
        color: #0f1111;
        transition: background 0.1s ease;
    }

    .column-visibility-menu label:hover {
        background: #f7fafa;
    }

    .column-visibility-menu input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #e77600;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .amazon-controls-row {
            flex-direction: column;
            align-items: stretch;
        }

        .amazon-controls-left {
            width: 100%;
        }

        .amazon-controls-right {
            width: 100%;
            justify-content: flex-end;
        }

        .amazon-search-box {
            width: 100%;
            max-width: none;
        }
    }

    @media (max-width: 768px) {
        .amazon-users-container {
            padding: 10px;
        }

        .amazon-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .amazon-pagination-container {
            flex-direction: column;
            align-items: center;
        }

        .amazon-action-buttons {
            flex-wrap: wrap;
        }

        .amazon-action-btn span {
            display: none;
        }

        .amazon-action-btn {
            padding: 8px;
        }
    }

    @media (max-width: 576px) {
        .amazon-export-btn span {
            display: none;
        }

        .amazon-export-btn {
            padding: 8px;
        }

        .amazon-controls-left {
            flex-direction: column;
            align-items: stretch;
        }

        .amazon-entries-selector {
            width: 100%;
        }
    }

    /* Hide unnecessary elements when printing or saving to PDF */
    @media print {
        /* Hide export buttons (CSV, Excel, PDF, Print) */
        .amazon-controls-right {
            display: none !important;
        }

        /* Hide ACTION column */
        #users_table th:nth-child(5),
        #users_table td:nth-child(5) {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="amazon-users-container">
    <div class="amazon-users-header-banner amazon-theme-banner">
        <div class="amazon-users-header-content">
            <h1 class="amazon-users-header-title">
                <i class="fas fa-users"></i>
                @lang('user.all_users')
            </h1>
            <p class="amazon-users-header-subtitle">
                View, manage, and control access for all user accounts in your organization.
            </p>
        </div>
    </div>
    @can('user.view')
    <!-- Main Card -->
    <div class="amazon-card">
        <!-- Card Header - All Users + Add User Button on Same Line -->
        <div class="amazon-card-header">
            <div class="amazon-card-title">
                <h2>@lang('user.all_users')</h2>
                <span class="amazon-user-count" id="total_users">0 users</span>
            </div>
            @can('user.create')
                <a href="{{action([\App\Http\Controllers\ManageUserController::class, 'create'])}}" class="amazon-add-btn" id="add_user_btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add User
                </a>
            @endcan
        </div>

        <!-- Controls Row -->
        <div class="amazon-controls-row">
            <div class="amazon-controls-left">
                <div class="amazon-entries-selector">
                    <span>Show</span>
                    <select id="entries_per_page">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="amazon-search-box">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="users_search" placeholder="Search users by name, username, email...">
                </div>
                <!-- Column Visibility -->
                <div class="column-visibility-dropdown">
                    <button class="amazon-export-btn column-visibility" id="column_visibility_btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Columns</span>
                    </button>
                    <div class="column-visibility-menu" id="column_visibility_menu">
                        <label><input type="checkbox" data-column="0" checked> Username</label>
                        <label><input type="checkbox" data-column="1" checked> Name</label>
                        <label><input type="checkbox" data-column="2" checked> Role</label>
                        <label><input type="checkbox" data-column="3" checked> Email</label>
                        <label><input type="checkbox" data-column="4" checked> Action</label>
                    </div>
                </div>
            </div>
            <div class="amazon-controls-right">
                <button class="amazon-export-btn" id="export_csv">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>CSV</span>
                </button>
                <button class="amazon-export-btn" id="export_excel">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    <span>Excel</span>
                </button>
                <button class="amazon-export-btn" id="export_pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>PDF</span>
                </button>
                <button class="amazon-export-btn" id="print_table">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Print</span>
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="amazon-table-container">
            <table class="amazon-table" id="users_table" style="width: 100%">
                <thead>
                    <tr>
                        <th class="sortable">@lang('business.username')</th>
                        <th class="sortable">@lang('user.name')</th>
                        <th class="sortable">@lang('user.role')</th>
                        <th class="sortable">@lang('business.email')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <!-- Pagination Container -->
        <div class="amazon-pagination-container">
            <div class="amazon-pagination-info" id="pagination_info">
                Showing <strong>0</strong> to <strong>0</strong> of <strong>0</strong> entries
            </div>
            <div class="amazon-pagination" id="pagination_controls">
                <!-- Pagination will be generated by JS -->
            </div>
        </div>
    </div>
    @endcan

    <!-- User Modal -->
    <div class="modal fade user_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var users_table;

        // Ensure column visibility menu is hidden on load/refresh (fixes white box flash)
        $('#column_visibility_menu').removeClass('show');

        // Remove any stray DataTables button/colvis dropdowns that may cause white box
        $('.dt-button-collection, ul.dt-button-collection').remove();

        // Initialize DataTable with Amazon-style configuration
        users_table = $('#users_table').DataTable({
            processing: false,
            serverSide: true,
            fixedHeader: false,
            ajax: '/users',
            pageLength: 25,
            lengthChange: false,
            searching: true,
            ordering: true,
            dom: 't', // Table only - no processing overlay (avoids white box on load)
            columnDefs: [{
                "targets": [4],
                "orderable": false,
                "searchable": false
            }],
            columns: [
                {
                    data: "username",
                    render: function(data, type, row) {
                        var html = '<span class="username-cell">' + data + '</span>';
                        if (data && data.includes('login_not_allowed')) {
                            html = data;
                        }
                        return html;
                    }
                },
                {data: "full_name"},
                {
                    data: "role",
                    render: function(data, type, row) {
                        var badgeClass = '';
                        if (data && data.toLowerCase().includes('admin') && !data.toLowerCase().includes('store')) {
                            badgeClass = 'admin';
                        } else if (data && data.toLowerCase().includes('store')) {
                            badgeClass = 'store-admin';
                        } else if (data && data.toLowerCase().includes('picker')) {
                            badgeClass = 'picker';
                        }
                        return '<span class="amazon-role-badge ' + badgeClass + '">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: "email",
                    render: function(data, type, row) {
                        return '<span class="email-cell">' + (data || '-') + '</span>';
                    }
                },
                {
                    data: "action",
                    render: function(data, type, row) {
                        // Parse the existing action buttons and convert to Amazon style
                        var $temp = $('<div>').html(data);
                        var html = '<div class="amazon-action-buttons">';
                        
                        // Find edit button
                        var $editBtn = $temp.find('a[href*="edit"]');
                        if ($editBtn.length) {
                            html += '<a href="' + $editBtn.attr('href') + '" class="amazon-action-btn edit">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>' +
                                '<span>Edit</span></a>';
                        }
                        
                        // Find view button
                        var $viewBtn = $temp.find('a[href*="show"], a.tw-dw-btn-info');
                        if ($viewBtn.length) {
                            html += '<a href="' + $viewBtn.attr('href') + '" class="amazon-action-btn view">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' +
                                '<span>View</span></a>';
                        }
                        
                        // Find delete button
                        var $deleteBtn = $temp.find('button.delete_user_button');
                        if ($deleteBtn.length) {
                            html += '<button data-href="' + $deleteBtn.data('href') + '" class="amazon-action-btn delete delete_user_button">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>' +
                                '<span>Delete</span></button>';
                        }
                        
                        html += '</div>';
                        return html;
                    }
                }
            ],
            language: {
                processing: '<div class="amazon-processing">Loading users...</div>',
                emptyTable: '<div class="amazon-empty-state"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg><p>No users found</p></div>'
            },
            drawCallback: function(settings) {
                updatePagination(settings);
                updateUserCount(settings);
                // Remove any stray overlay elements that may have been created
                $('.dt-button-collection, ul.dt-button-collection').remove();
            }
        });

        // Update user count badge
        function updateUserCount(settings) {
            var totalRecords = settings._iRecordsTotal || 0;
            $('#total_users').text(totalRecords + ' user' + (totalRecords !== 1 ? 's' : ''));
        }

        // Update pagination
        function updatePagination(settings) {
            var api = users_table;
            var pageInfo = api.page.info();
            
            // Update info text
            var start = pageInfo.start + 1;
            var end = pageInfo.end;
            var total = pageInfo.recordsTotal;
            
            if (total === 0) {
                start = 0;
                $('#pagination_info').html('No entries to show');
            } else {
                $('#pagination_info').html('Showing <strong>' + start + '</strong> to <strong>' + end + '</strong> of <strong>' + total + '</strong> entries');
            }
            
            // Generate pagination controls
            var paginationHtml = '';
            var currentPage = pageInfo.page;
            var totalPages = pageInfo.pages;
            
            if (totalPages > 0) {
                // Previous button
                paginationHtml += '<button class="page-btn nav-btn ' + (currentPage === 0 ? 'disabled' : '') + '" data-page="prev">&laquo; Prev</button>';
                
                // Page numbers
                var startPage = Math.max(0, currentPage - 2);
                var endPage = Math.min(totalPages - 1, currentPage + 2);
                
                if (startPage > 0) {
                    paginationHtml += '<button class="page-btn" data-page="0">1</button>';
                    if (startPage > 1) {
                        paginationHtml += '<span style="padding: 0 6px; color: #888;">...</span>';
                    }
                }
                
                for (var i = startPage; i <= endPage; i++) {
                    paginationHtml += '<button class="page-btn ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + (i + 1) + '</button>';
                }
                
                if (endPage < totalPages - 1) {
                    if (endPage < totalPages - 2) {
                        paginationHtml += '<span style="padding: 0 6px; color: #888;">...</span>';
                    }
                    paginationHtml += '<button class="page-btn" data-page="' + (totalPages - 1) + '">' + totalPages + '</button>';
                }
                
                // Next button
                paginationHtml += '<button class="page-btn nav-btn ' + (currentPage >= totalPages - 1 ? 'disabled' : '') + '" data-page="next">Next &raquo;</button>';
            }
            
            $('#pagination_controls').html(paginationHtml);
        }

        // Pagination click handler
        $(document).on('click', '#pagination_controls .page-btn:not(.disabled)', function() {
            var page = $(this).data('page');
            
            if (page === 'prev') {
                users_table.page('previous').draw('page');
            } else if (page === 'next') {
                users_table.page('next').draw('page');
            } else {
                users_table.page(parseInt(page)).draw('page');
            }
        });

        // Entries per page change
        $('#entries_per_page').on('change', function() {
            users_table.page.len(parseInt($(this).val())).draw();
        });

        // Search functionality
        var searchTimeout;
        $('#users_search').on('keyup', function() {
            var searchValue = $(this).val();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                users_table.search(searchValue).draw();
            }, 400);
        });

        // Column visibility toggle
        $('#column_visibility_btn').on('click', function(e) {
            e.stopPropagation();
            $('#column_visibility_menu').toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.column-visibility-dropdown').length) {
                $('#column_visibility_menu').removeClass('show');
            }
        });

        // Column visibility checkbox handler
        $('#column_visibility_menu input[type="checkbox"]').on('change', function() {
            var column = users_table.column($(this).data('column'));
            column.visible($(this).is(':checked'));
        });

        // Export functions
        $('#export_csv').on('click', function() {
            exportTableData('csv');
        });

        $('#export_excel').on('click', function() {
            exportTableData('excel');
        });

        $('#export_pdf').on('click', function() {
            exportTableData('pdf');
        });

        $('#print_table').on('click', function() {
            window.print();
        });

        // Export helper function
        function exportTableData(format) {
            var data = [];
            var headers = [];
            
            // Get visible column headers
            users_table.columns(':visible').every(function(index) {
                if (index < 4) { // Exclude action column
                    headers.push($(this.header()).text().trim());
                }
            });
            
            // Get visible column data
            users_table.rows().every(function() {
                var rowData = [];
                var row = this.data();
                users_table.columns(':visible').every(function(index) {
                    if (index < 4) { // Exclude action column
                        var cellData = row[this.dataSrc()] || '';
                        // Strip HTML tags
                        cellData = $('<div>').html(cellData).text().trim();
                        rowData.push(cellData);
                    }
                });
                data.push(rowData);
            });
            
            if (format === 'csv') {
                var csv = [headers.join(',')];
                data.forEach(function(row) {
                    csv.push(row.map(function(cell) {
                        return '"' + (cell || '').replace(/"/g, '""') + '"';
                    }).join(','));
                });
                
                var blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'users_export.csv';
                link.click();
            } else if (format === 'excel' || format === 'pdf') {
                // For Excel and PDF, redirect to server endpoint
                window.location.href = '/users?export=' + format;
            }
        }

        // Delete user handler
        $(document).on('click', 'button.delete_user_button', function() {
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_user,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                users_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection

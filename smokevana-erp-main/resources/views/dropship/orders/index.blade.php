@extends('layouts.app')
@section('title', 'Dropship Orders')

@section('css')
<style>
/* Amazon Theme - Dropship Orders Page */
.amazon-dropship-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 16px 20px;
}

/* Page Header - Amazon banner */
.amazon-dropship-banner {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 28px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}
.amazon-dropship-banner .banner-content { display: flex; flex-direction: column; gap: 4px; }
.amazon-dropship-banner .amazon-page-title { font-size: 22px; font-weight: 700; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 12px; }
.amazon-dropship-banner .amazon-page-title svg { color: #ffffff !important; }
.amazon-dropship-banner .banner-subtitle { font-size: 13px; color: rgba(249, 250, 251, 0.88); margin: 4px 0 0 0; }
.amazon-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.amazon-page-title { font-size: 24px; font-weight: 700; color: #0F1111; margin: 0; display: flex; align-items: center; gap: 12px; }
.amazon-page-title svg { color: #FF9900; }

/* Header Actions */
.amazon-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Amazon Buttons */
.amazon-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
    border: 1px solid;
}

.amazon-btn-secondary {
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border-color: #D5D9D9;
    color: #0F1111;
}

.amazon-btn-secondary:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    text-decoration: none;
    color: #0F1111;
}

.amazon-btn-secondary.active {
    background: linear-gradient(to bottom, #232F3E 0%, #37475A 100%);
    border-color: #232F3E;
    color: #FFFFFF;
}

.amazon-btn-primary {
    background: linear-gradient(to bottom, #FFD814 0%, #FF9900 100%);
    border-color: #FCD200;
    color: #0F1111;
    font-weight: 600;
}

.amazon-btn-primary:hover {
    background: linear-gradient(to bottom, #FFE342 0%, #FFAD33 100%);
    border-color: #E5B700;
    text-decoration: none;
    color: #0F1111;
}

.amazon-btn-success {
    background: linear-gradient(to bottom, #10B981 0%, #059669 100%);
    border-color: #059669;
    color: #FFFFFF;
}

.amazon-btn-success:hover {
    background: linear-gradient(to bottom, #34D399 0%, #10B981 100%);
    border-color: #047857;
    text-decoration: none;
    color: #FFFFFF;
}

.amazon-btn-warning {
    background: linear-gradient(to bottom, #F59E0B 0%, #D97706 100%);
    border-color: #D97706;
    color: #FFFFFF;
}

.amazon-btn-warning:hover {
    background: linear-gradient(to bottom, #FBBF24 0%, #F59E0B 100%);
    border-color: #B45309;
    text-decoration: none;
    color: #FFFFFF;
}

.amazon-btn-info {
    background: linear-gradient(to bottom, #3B82F6 0%, #2563EB 100%);
    border-color: #2563EB;
    color: #FFFFFF;
}

.amazon-btn-info:hover {
    background: linear-gradient(to bottom, #60A5FA 0%, #3B82F6 100%);
    border-color: #1D4ED8;
    text-decoration: none;
    color: #FFFFFF;
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

/* Filter Badge */
.filter-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    font-size: 11px;
    font-weight: 700;
    color: #FFF;
    background: #FF9900;
    border-radius: 10px;
    margin-left: 4px;
}

/* Active Filters Display */
.active-filters-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #FFF8E7;
    border-bottom: 1px solid #FFE0A0;
    flex-wrap: wrap;
}

.active-filters-bar.hidden {
    display: none;
}

.active-filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #232F3E;
    color: #FFF;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
}

.active-filter-tag .remove-filter {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.15s ease;
}

.active-filter-tag .remove-filter:hover {
    background: #FF9900;
}

.clear-all-filters {
    color: #C7511F;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    margin-left: auto;
}

.clear-all-filters:hover {
    text-decoration: underline;
}

/* Filter Modal */
.filter-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    display: none;
    animation: fadeIn 0.2s ease;
    overflow-y: auto;
}

.filter-modal-overlay.show {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px) translateX(0) scale(0.95); }
    to { opacity: 1; transform: translateY(0) translateX(0) scale(1); }
}

.filter-modal {
    background: #FFFFFF;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 520px;
    max-height: calc(100vh - 80px);
    overflow: hidden;
    animation: slideDown 0.25s ease;
    position: relative;
    margin: auto;
    z-index: 10000;
}

.filter-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    background: linear-gradient(135deg, #232F3E 0%, #37475A 100%);
    border-bottom: 3px solid #FF9900;
}

.filter-modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #FFFFFF !important;
}

.filter-modal-header h3 svg {
    color: #FF9900;
}

.filter-modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 6px;
    color: #FFFFFF;
    cursor: pointer;
    transition: all 0.15s ease;
}

.filter-modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.filter-modal-body {
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
}

.filter-group {
    margin-bottom: 20px;
}

.filter-group:last-child {
    margin-bottom: 0;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #232F3E;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-label svg {
    color: #FF9900;
    width: 16px;
    height: 16px;
}

.filter-input {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #E7E7E7;
    border-radius: 8px;
    font-size: 14px;
    color: #0F1111;
    background: #FAFAFA;
    transition: all 0.15s ease;
}

.filter-input:focus {
    outline: none;
    border-color: #FF9900;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
}

.filter-input::placeholder {
    color: #888C8C;
}

/* Date Range Section */
.date-range-container {
    display: flex;
    gap: 12px;
    align-items: center;
}

.date-input-wrapper {
    flex: 1;
    position: relative;
}

.date-input-wrapper svg {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #888C8C;
    pointer-events: none;
}

.date-input {
    width: 100%;
    padding: 12px 40px 12px 14px;
    border: 2px solid #E7E7E7;
    border-radius: 8px;
    font-size: 14px;
    color: #0F1111;
    background: #FAFAFA;
    transition: all 0.15s ease;
    cursor: pointer;
}

.date-input:focus {
    outline: none;
    border-color: #FF9900;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
}

.date-separator {
    color: #888C8C;
    font-weight: 500;
}

/* Quick Date Presets */
.date-presets {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.date-preset-btn {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    color: #565959;
    background: #F7F8F8;
    border: 1px solid #D5D9D9;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.date-preset-btn:hover {
    background: #E7E7E7;
    border-color: #BBBFBF;
}

.date-preset-btn.active {
    background: #232F3E;
    border-color: #232F3E;
    color: #FFFFFF;
}

.filter-modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
}

.filter-summary {
    font-size: 13px;
    color: #565959;
}

.filter-summary strong {
    color: #0F1111;
}

.filter-modal-actions {
    display: flex;
    gap: 10px;
}

/* Table Styles */
.amazon-table-container {
    overflow-x: auto;
}

.amazon-table {
    width: 100%;
    border-collapse: collapse;
}

.amazon-table thead {
    background: #232F3E;
}

.amazon-table thead th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #FFFFFF;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    border-bottom: 2px solid #FF9900;
}

.amazon-table thead th:first-child {
    padding-left: 20px;
}

.amazon-table tbody tr {
    transition: background 0.15s ease;
    border-bottom: 1px solid #E7E7E7;
}

.amazon-table tbody tr:hover {
    background: #F7FAFA;
}

.amazon-table tbody td {
    padding: 12px 16px;
    font-size: 13px;
    color: #0F1111;
    vertical-align: middle;
}

.amazon-table tbody td:first-child {
    padding-left: 20px;
}

/* Pagination */
.amazon-pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-pagination-info {
    font-size: 13px;
    color: #565959;
}

.amazon-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.amazon-pagination .page-item {
    list-style: none;
}

.amazon-pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    font-size: 13px;
    font-weight: 500;
    color: #0F1111;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.amazon-pagination .page-link:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    text-decoration: none;
    color: #0F1111;
}

.amazon-pagination .page-item.active .page-link {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: #FFF;
    font-weight: 600;
}

.amazon-pagination .page-item.disabled .page-link {
    background: #F7F8F8;
    color: #BBBFBF;
    cursor: not-allowed;
}

/* Status Badges */
.amazon-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.amazon-badge-success {
    background: #D1FAE5;
    color: #065F46;
}

.amazon-badge-warning {
    background: #FEF3C7;
    color: #92400E;
}

.amazon-badge-danger {
    background: #FEE2E2;
    color: #991B1B;
}

.amazon-badge-info {
    background: #DBEAFE;
    color: #1E40AF;
}

.amazon-badge-secondary {
    background: #F3F4F6;
    color: #374151;
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
        max-width: 100%;
    }
    
    .filter-modal {
        max-width: calc(100% - 32px);
        max-height: calc(100vh - 40px);
    }
    
    .date-range-container {
        flex-direction: column;
    }
    
    .date-separator {
        display: none;
    }
}

/* DataTables Override */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    display: none !important;
}

.amazon-table-container .dataTables_processing {
    background: rgba(255, 255, 255, 0.9) !important;
    border: 1px solid #E7E7E7 !important;
    border-radius: 8px !important;
    padding: 20px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

/* Select2 Override for Filter Modal */
.filter-modal .select2-container {
    width: 100% !important;
}

.filter-modal .select2-container--default .select2-selection--single {
    height: 46px;
    border: 2px solid #E7E7E7;
    border-radius: 8px;
    background: #FAFAFA;
}

.filter-modal .select2-container--default .select2-selection--single:focus,
.filter-modal .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #FF9900;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
}

.filter-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px;
    padding-left: 14px;
    color: #0F1111;
}

.filter-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px;
    right: 10px;
}

/* Daterangepicker Override */
.daterangepicker {
    border: none !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    z-index: 1060 !important;
}

.daterangepicker .drp-calendar {
    padding: 16px !important;
}

.daterangepicker .calendar-table th,
.daterangepicker .calendar-table td {
    font-size: 13px !important;
}

.daterangepicker td.active,
.daterangepicker td.active:hover {
    background: #FF9900 !important;
    border-color: #FF9900 !important;
}

.daterangepicker td.in-range {
    background: #FFF3E0 !important;
}

.daterangepicker .drp-buttons {
    border-top: 1px solid #E7E7E7 !important;
    padding: 12px 16px !important;
}

.daterangepicker .drp-buttons .btn {
    padding: 8px 16px !important;
    font-size: 13px !important;
    border-radius: 6px !important;
}

.daterangepicker .drp-buttons .applyBtn {
    background: linear-gradient(to bottom, #FFD814 0%, #FF9900 100%) !important;
    border-color: #FCD200 !important;
    color: #0F1111 !important;
}

.daterangepicker .drp-buttons .cancelBtn {
    background: #F7F8F8 !important;
    border-color: #D5D9D9 !important;
    color: #0F1111 !important;
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
</style>
@endsection

@section('content')
<div class="amazon-dropship-container">
    <!-- Amazon-style banner -->
    <div class="amazon-dropship-banner">
        <div class="banner-content">
            <h1 class="amazon-page-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                Dropship Orders
            </h1>
            <p class="banner-subtitle">Sync and manage dropship orders from WooCommerce.</p>
        </div>
        <div class="amazon-header-actions">
            <button type="button" class="amazon-btn amazon-btn-success" id="sync-products-btn" title="Sync Products to WooCommerce">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                Sync Products
            </button>
            <button type="button" class="amazon-btn amazon-btn-warning" id="sync-orders-btn" title="Sync All Orders to WooCommerce">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="16 16 12 12 8 16"></polyline>
                    <line x1="12" y1="12" x2="12" y2="21"></line>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                </svg>
                Sync Orders
            </button>
            <button type="button" class="amazon-btn amazon-btn-info" id="sync-history-btn" title="View Sync History">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Sync History
            </button>
            <button type="button" class="amazon-btn amazon-btn-secondary" id="refresh-table" title="Refresh Data">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="amazon-card">
        <!-- Controls Bar -->
        <div class="amazon-controls-bar">
            <div class="amazon-controls-left">
                <div class="amazon-entries-select">
                    <span>Show</span>
                    <select id="entries-select">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="amazon-search-wrapper">
                    <svg class="amazon-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="amazon-search-input" id="search-input" placeholder="Search orders...">
                </div>
            </div>
            <div class="amazon-controls-right">
                <button type="button" class="amazon-btn amazon-btn-secondary" id="btn-open-filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Filters
                    <span class="filter-badge" id="filter-count" style="display: none;">0</span>
                </button>
                <button type="button" class="amazon-btn amazon-btn-secondary" id="export-csv">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </button>
                <button type="button" class="amazon-btn amazon-btn-secondary" id="export-excel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    Export Excel
                </button>
                <button type="button" class="amazon-btn amazon-btn-secondary" id="print-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print
                </button>
                <div class="colvis-dropdown-wrapper">
                    <button type="button" class="amazon-btn amazon-btn-secondary" id="column-visibility">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Column visibility
                    </button>
                    <div class="colvis-dropdown" id="colvisDropdown">
                        <div class="colvis-dropdown-header">Toggle Columns</div>
                        <div class="colvis-dropdown-body" id="colvisDropdownBody">
                        </div>
                    </div>
                </div>
                <button type="button" class="amazon-btn amazon-btn-secondary" id="export-pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Active Filters Bar -->
        <div class="active-filters-bar hidden" id="active-filters-bar">
            <span style="font-size: 12px; color: #565959; margin-right: 8px;">Active filters:</span>
            <div id="active-filter-tags"></div>
            <span class="clear-all-filters" id="clear-all-filters">Clear all</span>
        </div>

        <!-- Table -->
        <div class="amazon-table-container">
            <table class="amazon-table" id="dropship-orders-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Parent Order</th>
                        <th>Vendor</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Sync</th>
                        <th>Tracking</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="amazon-pagination-wrapper">
            <div class="amazon-pagination-info" id="pagination-info">
                Showing 0 to 0 of 0 entries
            </div>
            <ul class="amazon-pagination" id="pagination-container">
            </ul>
        </div>
    </div>
</div>

<!-- Filter Modal Overlay -->
<div class="filter-modal-overlay" id="filter-modal-overlay">
    <div class="filter-modal">
        <div class="filter-modal-header">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filter Orders
            </h3>
            <button type="button" class="filter-modal-close" id="close-filter-modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="filter-modal-body">
            <!-- Vendor Filter -->
            <div class="filter-group">
                <label class="filter-label">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Vendor
                </label>
                {!! Form::select('filter_vendor', $vendors, null, ['class' => 'filter-input select2-filter', 'placeholder' => 'All Vendors', 'id' => 'filter_vendor']) !!}
            </div>

            <!-- Status Filter -->
            <div class="filter-group">
                <label class="filter-label">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Status
                </label>
                {!! Form::select('filter_status', $statuses, null, ['class' => 'filter-input', 'id' => 'filter_status']) !!}
                <option value="">All Statuses</option>
            </div>

            <!-- Date Range Filter -->
            <div class="filter-group">
                <label class="filter-label">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Date Range
                </label>
                <div class="date-range-container">
                    <div class="date-input-wrapper">
                        <input type="text" class="date-input" id="filter_start_date" placeholder="Start date" readonly>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                        </svg>
                    </div>
                    <span class="date-separator">to</span>
                    <div class="date-input-wrapper">
                        <input type="text" class="date-input" id="filter_end_date" placeholder="End date" readonly>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                        </svg>
                    </div>
                </div>
                
                <!-- Quick Date Presets -->
                <div class="date-presets">
                    <button type="button" class="date-preset-btn" data-preset="today">Today</button>
                    <button type="button" class="date-preset-btn" data-preset="yesterday">Yesterday</button>
                    <button type="button" class="date-preset-btn" data-preset="last7">Last 7 Days</button>
                    <button type="button" class="date-preset-btn" data-preset="last30">Last 30 Days</button>
                    <button type="button" class="date-preset-btn" data-preset="thisMonth">This Month</button>
                    <button type="button" class="date-preset-btn" data-preset="lastMonth">Last Month</button>
                </div>
            </div>
        </div>
        <div class="filter-modal-footer">
            <div class="filter-summary">
                <span id="filter-summary-text">No filters applied</span>
            </div>
            <div class="filter-modal-actions">
                <button type="button" class="amazon-btn amazon-btn-secondary" id="reset-filters">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                    </svg>
                    Reset
                </button>
                <button type="button" class="amazon-btn amazon-btn-primary" id="apply-filters">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Tracking Modal -->
<div class="modal fade" id="tracking-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <button type="button" class="close" data-dismiss="modal" style="color: #FFF; opacity: 0.8;"><span>&times;</span></button>
                <h4 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FF9900" stroke-width="2" style="margin-right: 8px;">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    Add Tracking Information
                </h4>
            </div>
            <form id="tracking-form">
                <div class="modal-body">
                    <input type="hidden" id="tracking-order-id">
                    <div class="form-group">
                        {!! Form::label('tracking_number', 'Tracking Number *') !!}
                        {!! Form::text('tracking_number', null, ['class' => 'form-control', 'id' => 'tracking_number', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('carrier', 'Carrier') !!}
                        {!! Form::select('carrier', [
                            'usps' => 'USPS',
                            'ups' => 'UPS',
                            'fedex' => 'FedEx',
                            'dhl' => 'DHL',
                            'other' => 'Other'
                        ], null, ['class' => 'form-control', 'id' => 'carrier', 'placeholder' => 'Select carrier']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('carrier_tracking_url', 'Custom Tracking URL') !!}
                        {!! Form::url('carrier_tracking_url', null, ['class' => 'form-control', 'id' => 'carrier_tracking_url', 'placeholder' => 'https://...']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('shipping_cost', 'Shipping Cost') !!}
                        {!! Form::number('shipping_cost', null, ['class' => 'form-control', 'id' => 'shipping_cost', 'step' => '0.01', 'min' => '0']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="amazon-btn amazon-btn-primary">Save Tracking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="status-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <button type="button" class="close" data-dismiss="modal" style="color: #FFF; opacity: 0.8;"><span>&times;</span></button>
                <h4 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FF9900" stroke-width="2" style="margin-right: 8px;">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    Update Order Status
                </h4>
            </div>
            <form id="status-form">
                <div class="modal-body">
                    <input type="hidden" id="status-order-id">
                    <div class="form-group">
                        {!! Form::label('fulfillment_status', 'Status *') !!}
                        {!! Form::select('fulfillment_status', $statuses, null, ['class' => 'form-control', 'id' => 'fulfillment_status', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status_notes', 'Notes') !!}
                        {!! Form::textarea('status_notes', null, ['class' => 'form-control', 'id' => 'status_notes', 'rows' => 3]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="amazon-btn amazon-btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sync History Modal -->
<div class="modal fade" id="sync-history-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <button type="button" class="close" data-dismiss="modal" style="color: #FFF; opacity: 0.8;"><span>&times;</span></button>
                <h4 class="modal-title">WooCommerce Sync History</h4>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="sync-history-table" style="width: 100%; margin-bottom: 0;">
                        <thead style="background: #F7F8F8;">
                            <tr>
                                <th>Started</th>
                                <th>Type</th>
                                <th>Trigger</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Duration</th>
                                <th>Triggered By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Sync Details Modal -->
<div class="modal fade" id="sync-details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Sync Details</h4>
            </div>
            <div class="modal-body" id="sync-details-content">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Loading details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Sync Progress Toast -->
<div id="sync-progress-toast" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 9999; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 20px; min-width: 320px; border: 1px solid #E7E7E7;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
        <div style="width: 40px; height: 40px; background: #232F3E; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-sync fa-spin" style="color: #FF9900;"></i>
        </div>
        <div>
            <div style="font-weight: 600; color: #0F1111;">Syncing Products</div>
            <div style="font-size: 12px; color: #565959;" id="sync-progress-text">Starting...</div>
        </div>
    </div>
    <div class="progress" style="height: 6px; border-radius: 3px; background: #E7E7E7;">
        <div class="progress-bar" id="sync-progress-bar" style="width: 0%; background: linear-gradient(to right, #FF9900, #FFBA57); border-radius: 3px; transition: width 0.3s;"></div>
    </div>
</div>

<!-- Order Sync Progress Modal -->
<div class="modal fade" id="order-sync-progress-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <h4 class="modal-title">WooCommerce Order Sync Progress</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="order-sync-summary" style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1; text-align: center; padding: 15px; background: #F7F8F8; border-radius: 8px;">
                        <div style="font-size: 28px; font-weight: 700; color: #232F3E;" id="sync-total-count">0</div>
                        <div style="font-size: 12px; color: #565959;">Total Orders</div>
                    </div>
                    <div style="flex: 1; text-align: center; padding: 15px; background: #D1FAE5; border-radius: 8px;">
                        <div style="font-size: 28px; font-weight: 700; color: #065F46;" id="sync-success-count">0</div>
                        <div style="font-size: 12px; color: #065F46;">Queued</div>
                    </div>
                    <div style="flex: 1; text-align: center; padding: 15px; background: #FEE2E2; border-radius: 8px;">
                        <div style="font-size: 28px; font-weight: 700; color: #991B1B;" id="sync-failed-count">0</div>
                        <div style="font-size: 12px; color: #991B1B;">Failed</div>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 13px; color: #565959;">Progress</span>
                        <span style="font-size: 13px; font-weight: 600; color: #FF9900;" id="sync-progress-percent">0%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background: #E7E7E7;">
                        <div class="progress-bar" id="order-sync-progress-bar" style="width: 0%; background: linear-gradient(to right, #FF9900, #FFBA57); border-radius: 6px;"></div>
                    </div>
                </div>
                <div id="sync-status-message" style="padding: 12px; background: #F7F8F8; border-radius: 8px; margin-bottom: 20px;">
                    <i class="fas fa-spinner fa-spin" style="color: #FF9900;"></i>
                    <span style="font-size: 14px; color: #565959; margin-left: 8px;">Preparing to sync orders...</span>
                </div>
                <div style="border: 1px solid #E7E7E7; border-radius: 8px; overflow: hidden;">
                    <div style="background: #F7F8F8; padding: 10px 15px; border-bottom: 1px solid #E7E7E7;">
                        <span style="font-weight: 600; font-size: 14px; color: #0F1111;">Sync Logs</span>
                    </div>
                    <div id="sync-logs-container" style="max-height: 300px; overflow-y: auto; padding: 0;">
                        <table class="table table-sm" style="margin-bottom: 0; font-size: 12px;">
                            <thead style="background: #F7F8F8; position: sticky; top: 0;">
                                <tr>
                                    <th>Time</th>
                                    <th>Order #</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody id="sync-logs-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted" style="padding: 30px;">Waiting to start...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="amazon-btn amazon-btn-secondary" id="close-order-sync-modal" disabled data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Sync Options Modal -->
<div class="modal fade" id="sync-options-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff;">
                <button type="button" class="close" data-dismiss="modal" style="color: #FFF;"><span>&times;</span></button>
                <h4 class="modal-title">Sync Orders to WooCommerce</h4>
            </div>
            <div class="modal-body">
                <p style="color: #565959; margin-bottom: 20px;">Configure sync options and start the bulk order sync process.</p>
                <div class="form-group">
                    <label style="font-weight: 600;">Filter by Vendor</label>
                    {!! Form::select('sync_vendor_filter', $vendors->prepend('All Vendors', ''), null, ['class' => 'form-control select2', 'id' => 'sync_vendor_filter']) !!}
                </div>
                <div class="form-group">
                    <label style="font-weight: 600;">Filter by Status</label>
                    {!! Form::select('sync_status_filter', array_merge(['' => 'All Statuses'], $statuses), null, ['class' => 'form-control', 'id' => 'sync_status_filter']) !!}
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="sync_only_pending" checked> Only sync pending/failed orders
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="amazon-btn amazon-btn-warning" id="start-order-sync-btn">Start Sync</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize Select2 inside filter modal
    $('.select2-filter').select2({
        dropdownParent: $('#filter-modal-overlay'),
        placeholder: 'All Vendors',
        allowClear: true
    });

    // =====================================================
    // Filter Modal Logic
    // =====================================================
    var activeFilters = {
        vendor: null,
        vendorName: null,
        status: null,
        statusName: null,
        startDate: null,
        endDate: null
    };

    // Open filter modal
    $('#btn-open-filter').on('click', function() {
        $('#filter-modal-overlay').addClass('show');
        $('body').css('overflow', 'hidden'); // Prevent body scroll when modal is open
        // Ensure modal is centered
        $('#filter-modal-overlay').scrollTop(0);
        updateFilterSummary();
    });

    // Close filter modal
    $('#close-filter-modal').on('click', function() {
        $('#filter-modal-overlay').removeClass('show');
        $('body').css('overflow', ''); // Restore body scroll
    });

    // Close on overlay click (but not on modal content)
    $('#filter-modal-overlay').on('click', function(e) {
        // Only close if clicking directly on the overlay, not on modal content
        if ($(e.target).is('.filter-modal-overlay')) {
            $(this).removeClass('show');
            $('body').css('overflow', ''); // Restore body scroll
        }
    });
    
    // Prevent modal from closing when clicking inside it
    $('.filter-modal').on('click', function(e) {
        e.stopPropagation();
    });

    // Close on escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#filter-modal-overlay').hasClass('show')) {
            $('#filter-modal-overlay').removeClass('show');
            $('body').css('overflow', ''); // Restore body scroll
        }
    });

    // Initialize date pickers
    $('#filter_start_date').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        locale: { format: 'YYYY-MM-DD' }
    });

    $('#filter_end_date').daterangepicker({
        singleDatePicker: true,
        autoUpdateInput: false,
        locale: { format: 'YYYY-MM-DD' }
    });

    $('#filter_start_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        updateFilterSummary();
    });

    $('#filter_end_date').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
        updateFilterSummary();
    });

    // Date presets
    $('.date-preset-btn').on('click', function() {
        var preset = $(this).data('preset');
        var today = moment();
        var startDate, endDate;

        $('.date-preset-btn').removeClass('active');
        $(this).addClass('active');

        switch(preset) {
            case 'today':
                startDate = endDate = today.format('YYYY-MM-DD');
                break;
            case 'yesterday':
                startDate = endDate = today.subtract(1, 'days').format('YYYY-MM-DD');
                break;
            case 'last7':
                startDate = today.subtract(6, 'days').format('YYYY-MM-DD');
                endDate = moment().format('YYYY-MM-DD');
                break;
            case 'last30':
                startDate = today.subtract(29, 'days').format('YYYY-MM-DD');
                endDate = moment().format('YYYY-MM-DD');
                break;
            case 'thisMonth':
                startDate = today.startOf('month').format('YYYY-MM-DD');
                endDate = moment().endOf('month').format('YYYY-MM-DD');
                break;
            case 'lastMonth':
                startDate = today.subtract(1, 'month').startOf('month').format('YYYY-MM-DD');
                endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
                break;
        }

        $('#filter_start_date').val(startDate);
        $('#filter_end_date').val(endDate);
        updateFilterSummary();
    });

    // Update filter summary
    function updateFilterSummary() {
        var count = 0;
        var summaryParts = [];

        if ($('#filter_vendor').val()) {
            count++;
            summaryParts.push('Vendor: ' + $('#filter_vendor option:selected').text());
        }
        if ($('#filter_status').val()) {
            count++;
            summaryParts.push('Status: ' + $('#filter_status option:selected').text());
        }
        if ($('#filter_start_date').val() || $('#filter_end_date').val()) {
            count++;
            var dateStr = '';
            if ($('#filter_start_date').val()) dateStr += $('#filter_start_date').val();
            if ($('#filter_start_date').val() && $('#filter_end_date').val()) dateStr += ' to ';
            if ($('#filter_end_date').val()) dateStr += $('#filter_end_date').val();
            summaryParts.push('Date: ' + dateStr);
        }

        if (count > 0) {
            $('#filter-summary-text').html('<strong>' + count + '</strong> filter(s) selected');
            $('#filter-count').text(count).show();
            $('#btn-open-filter').addClass('active');
        } else {
            $('#filter-summary-text').text('No filters applied');
            $('#filter-count').hide();
            $('#btn-open-filter').removeClass('active');
        }

        // Update active filters bar
        updateActiveFiltersBar();
    }

    // Update active filters bar
    function updateActiveFiltersBar() {
        var tags = [];
        
        if ($('#filter_vendor').val()) {
            tags.push({
                type: 'vendor',
                label: 'Vendor: ' + $('#filter_vendor option:selected').text()
            });
        }
        if ($('#filter_status').val()) {
            tags.push({
                type: 'status',
                label: 'Status: ' + $('#filter_status option:selected').text()
            });
        }
        if ($('#filter_start_date').val() || $('#filter_end_date').val()) {
            var dateStr = 'Date: ';
            if ($('#filter_start_date').val()) dateStr += $('#filter_start_date').val();
            if ($('#filter_start_date').val() && $('#filter_end_date').val()) dateStr += ' to ';
            if ($('#filter_end_date').val()) dateStr += $('#filter_end_date').val();
            tags.push({
                type: 'date',
                label: dateStr
            });
        }

        var $tagsContainer = $('#active-filter-tags');
        $tagsContainer.empty();

        if (tags.length > 0) {
            tags.forEach(function(tag) {
                $tagsContainer.append(
                    '<span class="active-filter-tag" data-type="' + tag.type + '">' +
                    tag.label +
                    '<span class="remove-filter" title="Remove filter">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' +
                    '</span></span>'
                );
            });
            $('#active-filters-bar').removeClass('hidden');
        } else {
            $('#active-filters-bar').addClass('hidden');
        }
    }

    // Remove individual filter tag
    $(document).on('click', '.remove-filter', function() {
        var type = $(this).closest('.active-filter-tag').data('type');
        
        switch(type) {
            case 'vendor':
                $('#filter_vendor').val('').trigger('change');
                break;
            case 'status':
                $('#filter_status').val('');
                break;
            case 'date':
                $('#filter_start_date').val('');
                $('#filter_end_date').val('');
                $('.date-preset-btn').removeClass('active');
                break;
        }
        
        updateFilterSummary();
        ordersTable.ajax.reload();
    });

    // Clear all filters
    $('#clear-all-filters, #reset-filters').on('click', function() {
        $('#filter_vendor').val('').trigger('change');
        $('#filter_status').val('');
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        $('.date-preset-btn').removeClass('active');
        updateFilterSummary();
        ordersTable.ajax.reload();
    });

    // Apply filters
    $('#apply-filters').on('click', function() {
        $('#filter-modal-overlay').removeClass('show');
        $('body').css('overflow', ''); // Restore body scroll
        ordersTable.ajax.reload();
        updateActiveFiltersBar();
    });

    // Track filter changes for summary update
    $('#filter_vendor').on('change', updateFilterSummary);
    $('#filter_status').on('change', updateFilterSummary);

    // =====================================================
    // DataTable
    // =====================================================
    var ordersTable = $('#dropship-orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("dropship.orders.data") }}',
            data: function(d) {
                d.vendor_id = $('#filter_vendor').val();
                d.status = $('#filter_status').val();
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
            }
        },
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 'order_no', name: 'transaction_id' },
            { data: 'parent_order', name: 'parent_transaction_id' },
            { data: 'vendor_name', name: 'wp_vendor_id' },
            { data: 'customer', name: 'customer', orderable: false },
            { data: 'total', name: 'total', orderable: false },
            { data: 'status_badge', name: 'fulfillment_status' },
            { data: 'sync_status', name: 'sync_status' },
            { data: 'tracking', name: 'tracking_number' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        dom: 'rtip',
        buttons: $.fn.dataTable && $.fn.dataTable.Buttons ? [
            {
                extend: 'csv',
                text: 'Export CSV',
                className: 'hidden',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'hidden',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'hidden',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'colvis',
                text: 'Column visibility',
                className: 'hidden'
            }
        ] : [],
        drawCallback: function(settings) {
            var api = this.api();
            var info = api.page.info();
            
            var start = info.start + 1;
            var end = info.start + info.length;
            if (end > info.recordsDisplay) end = info.recordsDisplay;
            if (info.recordsDisplay === 0) start = 0;
            
            $('#pagination-info').html('Showing ' + start + ' to ' + end + ' of ' + info.recordsDisplay + ' entries');
            
            var paginationHtml = '';
            
            paginationHtml += '<li class="page-item ' + (info.page === 0 ? 'disabled' : '') + '">' +
                '<a class="page-link" href="#" data-page="' + (info.page - 1) + '">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>' +
                '</a></li>';
            
            var startPage = Math.max(0, info.page - 2);
            var endPage = Math.min(info.pages - 1, info.page + 2);
            
            if (startPage > 0) {
                paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="0">1</a></li>';
                if (startPage > 1) {
                    paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                paginationHtml += '<li class="page-item ' + (i === info.page ? 'active' : '') + '">' +
                    '<a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
            }
            
            if (endPage < info.pages - 1) {
                if (endPage < info.pages - 2) {
                    paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (info.pages - 1) + '">' + info.pages + '</a></li>';
            }
            
            paginationHtml += '<li class="page-item ' + (info.page >= info.pages - 1 ? 'disabled' : '') + '">' +
                '<a class="page-link" href="#" data-page="' + (info.page + 1) + '">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>' +
                '</a></li>';
            
            $('#pagination-container').html(paginationHtml);
        }
    });

    // Pagination click handler
    $(document).on('click', '#pagination-container .page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (typeof page !== 'undefined' && !$(this).parent().hasClass('disabled')) {
            ordersTable.page(page).draw('page');
        }
    });

    // Entries select
    $('#entries-select').on('change', function() {
        ordersTable.page.len($(this).val()).draw();
    });

    // Search
    var searchTimeout;
    $('#search-input').on('keyup', function() {
        var value = $(this).val();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            ordersTable.search(value).draw();
        }, 300);
    });

    // Refresh table
    $('#refresh-table').on('click', function() {
        ordersTable.ajax.reload();
    });

    // =====================================================
    // Export Buttons
    // =====================================================

    // Export CSV
    $('#export-csv').on('click', function(e) {
        e.preventDefault();
        if ($.fn.dataTable.Buttons && ordersTable.button) {
            try {
                ordersTable.button('.buttons-csv').trigger();
            } catch(err) {
                console.error('CSV export error:', err);
                exportToCSV();
            }
        } else {
            exportToCSV();
        }
    });

    // Export Excel
    $('#export-excel').on('click', function(e) {
        e.preventDefault();
        if ($.fn.dataTable.Buttons && ordersTable.button) {
            try {
                ordersTable.button('.buttons-excel').trigger();
            } catch(err) {
                console.error('Excel export error:', err);
                toastr.error('Excel export failed. Please ensure DataTables Buttons extension is loaded.');
            }
        } else {
            toastr.warning('Excel export requires DataTables Buttons extension');
        }
    });

    // Print
    $('#print-btn').on('click', function(e) {
        e.preventDefault();
        if ($.fn.dataTable.Buttons && ordersTable.button) {
            try {
                ordersTable.button('.buttons-print').trigger();
            } catch(err) {
                console.error('Print error:', err);
                window.print();
            }
        } else {
            window.print();
        }
    });

    // Column Visibility - Custom Dropdown
    function buildColVisDropdown() {
        var $body = $('#colvisDropdownBody');
        $body.empty();
        
        if (ordersTable) {
            ordersTable.columns().every(function(index) {
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
    
    $('#column-visibility').on('click', function(e) {
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
        
        if (ordersTable) {
            var column = ordersTable.column(colIndex);
            column.visible(!column.visible());
            $item.toggleClass('active');
        }
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.colvis-dropdown-wrapper').length) {
            $('#colvisDropdown').removeClass('show');
        }
    });

    // Export PDF
    $('#export-pdf').on('click', function(e) {
        e.preventDefault();
        if ($.fn.dataTable.Buttons && ordersTable.button && $.fn.dataTable.pdfMake) {
            try {
                ordersTable.button('.buttons-pdf').trigger();
            } catch(err) {
                console.error('PDF export error:', err);
                toastr.error('PDF export failed. Please ensure PDFMake library is loaded.');
            }
        } else {
            toastr.warning('PDF export requires DataTables Buttons and PDFMake libraries');
        }
    });

    // Helper function for CSV export
    function exportToCSV() {
        var csv = [];
        // Get headers
        var headers = [];
        ordersTable.columns(':visible').every(function() {
            headers.push(this.header().textContent.trim());
        });
        csv.push(headers.join(','));

        // Get data
        ordersTable.rows({ search: 'applied' }).every(function() {
            var row = [];
            ordersTable.columns(':visible').every(function() {
                var data = this.data();
                // Clean data for CSV
                data = String(data || '').replace(/"/g, '""');
                row.push('"' + data + '"');
            });
            csv.push(row.join(','));
        });

        // Download
        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'dropship-orders-' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // =====================================================
    // Other existing functionality (Tracking, Status, Sync, etc.)
    // =====================================================
    
    // Add Tracking
    $(document).on('click', '.add-tracking', function(e) {
        e.preventDefault();
        $('#tracking-order-id').val($(this).data('id'));
        $('#tracking-form')[0].reset();
        $('#tracking-modal').modal('show');
    });

    $('#tracking-form').on('submit', function(e) {
        e.preventDefault();
        var orderId = $('#tracking-order-id').val();
        
        $.ajax({
            url: '{{ url("dropship/orders") }}/' + orderId + '/tracking',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tracking_number: $('#tracking_number').val(),
                carrier: $('#carrier').val(),
                carrier_tracking_url: $('#carrier_tracking_url').val(),
                shipping_cost: $('#shipping_cost').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#tracking-modal').modal('hide');
                    ordersTable.ajax.reload();
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('Failed to add tracking');
            }
        });
    });

    // Update Status
    $(document).on('click', '.update-status', function(e) {
        e.preventDefault();
        $('#status-order-id').val($(this).data('id'));
        $('#status-form')[0].reset();
        $('#status-modal').modal('show');
    });

    $('#status-form').on('submit', function(e) {
        e.preventDefault();
        var orderId = $('#status-order-id').val();
        
        $.ajax({
            url: '{{ url("dropship/orders") }}/' + orderId + '/status',
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                fulfillment_status: $('#fulfillment_status').val(),
                notes: $('#status_notes').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#status-modal').modal('hide');
                    ordersTable.ajax.reload();
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('Failed to update status');
            }
        });
    });

    // Retry Sync
    $(document).on('click', '.retry-sync', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        
        $.ajax({
            url: '{{ url("dropship/orders") }}/' + orderId + '/retry-sync',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    ordersTable.ajax.reload();
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('Failed to retry sync');
            }
        });
    });

    // Sync Products
    var currentSyncId = null;
    var syncPollInterval = null;

    $('#sync-products-btn').on('click', function() {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;
        
        swal({
            title: 'Sync Products to WooCommerce?',
            text: 'This will push all ERP products to WooCommerce.',
            icon: 'info',
            buttons: { cancel: 'Cancel', confirm: { text: 'Start Sync', className: 'btn-success' }}
        }).then(function(confirmed) {
            if (confirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
                $.ajax({
                    url: '{{ route("dropship.sync.products") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            currentSyncId = response.sync_id;
                            showSyncProgress();
                            startSyncPolling();
                        } else {
                            toastr.error(response.msg);
                            resetSyncButton();
                        }
                    },
                    error: function() {
                        toastr.error('Failed to start sync');
                        resetSyncButton();
                    }
                });
            }
        });
    });

    function resetSyncButton() {
        $('#sync-products-btn').prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg> Sync Products');
    }

    function showSyncProgress() {
        $('#sync-progress-toast').fadeIn();
        $('#sync-progress-bar').css('width', '0%');
        $('#sync-progress-text').text('Starting...');
    }

    function hideSyncProgress() {
        $('#sync-progress-toast').fadeOut();
    }

    function startSyncPolling() {
        syncPollInterval = setInterval(function() {
            if (!currentSyncId) { stopSyncPolling(); return; }
            $.ajax({
                url: '{{ url("dropship/sync") }}/' + currentSyncId + '/status',
                method: 'GET',
                success: function(data) {
                    $('#sync-progress-bar').css('width', data.progress_percent + '%');
                    $('#sync-progress-text').text(data.synced_items + ' synced, ' + data.failed_items + ' failed of ' + data.total_items);
                    if (data.status === 'completed' || data.status === 'failed') {
                        stopSyncPolling();
                        hideSyncProgress();
                        resetSyncButton();
                        if (data.status === 'completed') {
                            toastr.success('Sync completed!');
                        } else {
                            toastr.error('Sync failed: ' + (data.error_message || 'Unknown error'));
                        }
                        ordersTable.ajax.reload();
                    }
                }
            });
        }, 2000);
    }

    function stopSyncPolling() {
        if (syncPollInterval) { clearInterval(syncPollInterval); syncPollInterval = null; }
        currentSyncId = null;
    }

    // Sync History
    var syncHistoryTable = null;
    $('#sync-history-btn').on('click', function() {
        $('#sync-history-modal').modal('show');
        if (syncHistoryTable) {
            syncHistoryTable.ajax.reload();
        } else {
            syncHistoryTable = $('#sync-history-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("dropship.sync.history.data") }}',
                columns: [
                    { data: 'started', name: 'started_at' },
                    { data: 'sync_type_label', name: 'sync_type' },
                    { data: 'trigger_label', name: 'trigger_type' },
                    { data: 'status_html', name: 'status' },
                    { data: 'progress', name: 'synced_items', orderable: false },
                    { data: 'duration_text', name: 'completed_at', orderable: false },
                    { data: 'triggered_by_name', name: 'triggered_by', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 10
            });
        }
    });

    $(document).on('click', '.view-sync-details', function() {
        var syncId = $(this).data('id');
        $('#sync-details-content').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        $('#sync-details-modal').modal('show');
        $.ajax({
            url: '{{ url("dropship/sync") }}/' + syncId + '/details',
            method: 'GET',
            success: function(html) { $('#sync-details-content').html(html); },
            error: function() { $('#sync-details-content').html('<div class="alert alert-danger">Failed to load details</div>'); }
        });
    });

    // Sync Orders
    $('#sync-orders-btn').on('click', function() {
        $('#sync-options-modal').modal('show');
    });

    $('#start-order-sync-btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Starting...');
        $('#sync-options-modal').modal('hide');
        $('#order-sync-progress-modal').modal('show');
        
        $.ajax({
            url: '{{ route("dropship.orders.bulk-sync-woo") }}',
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                vendor_id: $('#sync_vendor_filter').val(),
                status: $('#sync_status_filter').val(),
                sync_only_pending: $('#sync_only_pending').is(':checked')
            },
            success: function(response) {
                if (response.success) {
                    $('#sync-total-count').text(response.total || 0);
                    $('#sync-success-count').text(response.synced || 0);
                    $('#sync-failed-count').text(response.failed || 0);
                    var percent = response.total > 0 ? Math.round(((response.synced + response.failed) / response.total) * 100) : 0;
                    $('#sync-progress-percent').text(percent + '%');
                    $('#order-sync-progress-bar').css('width', percent + '%');
                    $('#sync-status-message').html('<i class="fas fa-check-circle" style="color: #10b981;"></i> <span style="margin-left: 8px;">' + response.msg + '</span>');
                    toastr.success(response.msg);
                } else {
                    $('#sync-status-message').html('<i class="fas fa-exclamation-circle" style="color: #ef4444;"></i> <span style="margin-left: 8px;">' + response.msg + '</span>');
                    toastr.error(response.msg);
                }
                $('#close-order-sync-modal').prop('disabled', false);
                ordersTable.ajax.reload();
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.msg ? xhr.responseJSON.msg : 'Failed to start sync';
                $('#sync-status-message').html('<i class="fas fa-times-circle" style="color: #ef4444;"></i> <span style="margin-left: 8px;">' + msg + '</span>');
                toastr.error(msg);
                $('#close-order-sync-modal').prop('disabled', false);
            },
            complete: function() {
                $btn.prop('disabled', false).html('Start Sync');
            }
        });
    });

    $(document).on('click', '.sync-order-woo', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        var invoice = $(this).data('invoice');
        var $link = $(this);
        
        swal({
            title: 'Sync Order to WooCommerce?',
            text: 'This will sync order ' + invoice + ' to WooCommerce.',
            icon: 'info',
            buttons: { cancel: 'Cancel', confirm: { text: 'Sync Now', className: 'btn-warning' }}
        }).then(function(confirmed) {
            if (confirmed) {
                $link.closest('.btn-group').find('button').prop('disabled', true);
                $.ajax({
                    url: '{{ url("dropship/orders") }}/' + orderId + '/sync-woo',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) { toastr.success(response.msg); } 
                        else { toastr.error(response.msg); }
                        ordersTable.ajax.reload(null, false);
                    },
                    error: function() { toastr.error('Failed to sync order'); ordersTable.ajax.reload(null, false); },
                    complete: function() { $link.closest('.btn-group').find('button').prop('disabled', false); }
                });
            }
        });
    });
});
</script>
@endsection

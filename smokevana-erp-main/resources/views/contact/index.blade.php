@extends('layouts.app')
@section('title', __('lang_v1.' . $type . 's'))
@php
$api_key = env('GOOGLE_MAP_API_KEY');
@endphp
@if (!empty($api_key))
@section('css')
@include('contact.partials.google_map_styles')
@endsection
@endif

@section('css')
<style>
/* Amazon Theme - Contact List Page */
.amazon-contacts-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 16px 20px;
}

/* Top banner – Amazon style (same as Add new product / Manage Order) */
.amazon-contacts-header-banner {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 28px;
    margin-bottom: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}

.amazon-contacts-header-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.amazon-contacts-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

.amazon-contacts-header-title i {
    font-size: 22px;
    color: #ffffff !important;
}

.amazon-contacts-header-subtitle {
    font-size: 13px;
    color: rgba(249, 250, 251, 0.88);
    margin: 0;
}

/* Page Header - Tabs row (below banner) */
.amazon-page-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 8px;
}

/* Header row: status tabs + prime groups + search */
.amazon-page-subheader {
    display: flex;
    align-items: center;
    width: 100%;
    gap: 12px;
}

.amazon-page-subheader-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    flex: 0 0 auto;
}

.amazon-page-header-search {
    flex: 1 1 auto;
    display: flex;
    justify-content: flex-end;
}

.amazon-page-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
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

/* Status Tabs - Amazon Style */
.amazon-status-tabs {
    display: flex;
    gap: 4px;
    background: #FFF;
    padding: 4px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #D5D9D9;
    flex-wrap: wrap;
}

.amazon-tab {
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
}

/* Vendor type counts (supplier view) */
.amazon-vendor-type-counts {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 100px;
    background: #FFFFFF;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #D5D9D9;
    margin-top: 8px;
}

.vendor-type-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #0F1111;
}

.vendor-type-label {
    font-weight: 500;
    color: #565959;
}

.vendor-type-count {
    font-weight: 600;
    color: #C7511F;
    background: #FFF7E5;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid #F0C14B;
    min-width: 28px;
    text-align: center;
}

.amazon-tab:hover {
    background: #F7FAFA;
    color: #C7511F;
}

.amazon-tab.active {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(228, 121, 17, 0.3);
}

.amazon-tab .status-count {
    margin-left: 4px;
    font-weight: 600;
    opacity: 0.9;
}

.amazon-tab.active .status-count {
    color: white;
    opacity: 1;
}

/* Prime Customer Counts - Header Version */
.amazon-prime-counts-header {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: nowrap;
    padding: 8px 12px;
    background: #FFFFFF;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #D5D9D9;
    margin-top: 0;
    overflow-x: auto;
}

.amazon-page-header-search {
    margin-left: auto;
    flex: 1 1 auto; /* allow search bar to use all remaining space */
}

/* Prime Customer Counts - Original (for controls bar if needed) */
.amazon-prime-counts {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 8px 12px;
    background: #F7F8F8;
    border-radius: 6px;
    border: 1px solid #D5D9D9;
}

.prime-count-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #0F1111;
    text-decoration: none;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 6px;
    transition: background 0.15s ease, color 0.15s ease;
}

.prime-count-item:hover {
    background: #F0F2F2;
    color: #0F1111;
}

.prime-count-item.active {
    background: #FF9900;
    color: #FFF;
}

.prime-count-item.active .prime-count {
    color: #FFF !important;
    opacity: 1;
}

.prime-count {
    margin-left: 4px;
    font-weight: 600;
    color: #C7511F;
    opacity: 0.9;
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
    flex: 1 1 auto;
    max-width: 100%;
    min-width: 0;
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

/* Amazon Buttons */
.amazon-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
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

.amazon-btn-primary {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: white;
}

.amazon-btn-primary:hover {
    background: linear-gradient(to bottom, #FFB84D 0%, #FF9900 100%);
    border-color: #C7511F;
    text-decoration: none;
    color: white;
    box-shadow: 0 2px 5px rgba(213, 217, 217, 0.5);
}

.amazon-btn-info {
    background: linear-gradient(to bottom, #007185 0%, #006073 100%);
    border-color: #00545E;
    color: white;
}

.amazon-btn-info:hover {
    background: linear-gradient(to bottom, #008296 0%, #007185 100%);
    text-decoration: none;
    color: white;
}

/* Bulk Actions Dropdown (same as products list) */
.bulk-actions-dropdown { position: relative; }
.bulk-actions-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; font-size: 13px; font-weight: 500;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9; border-radius: 4px;
    color: #0F1111; cursor: pointer; white-space: nowrap;
}
.bulk-actions-btn:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
}
.bulk-actions-menu {
    position: absolute; top: 100%; left: 0; margin-top: 4px;
    min-width: 180px; background: #FFF; border: 1px solid #D5D9D9;
    border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    padding: 6px 0; display: none; z-index: 100;
}
.bulk-actions-menu.show { display: block; }
.bulk-actions-menu .bulk-action-item {
    display: flex; align-items: center; gap: 10px;
    width: 100%; padding: 10px 14px; border: none; background: none;
    font-size: 13px; color: #0F1111; cursor: pointer; text-align: left;
}
.bulk-actions-menu .bulk-action-item.danger:hover {
    background: #FFE6E6; color: #B12704;
}

/* Table Styles */
.amazon-table-wrapper {
    overflow-x: auto;
    padding: 0;
}

/* Table container: horizontal scroll and grab cursor for scrollable area */
#contact_table_wrapper.table-container,
.amazon-contacts-container .amazon-table-wrapper {
    overflow-x: auto;
    width: 100%;
    cursor: grab;
}
#contact_table_wrapper.table-container:active,
.amazon-contacts-container .amazon-table-wrapper:active {
    cursor: grabbing;
}

/* Pointer cursor on table cells and headers for interactivity */
#contact_table td,
#contact_table th,
#contact_table_wrapper .dataTables_scrollHead th,
#contact_table_wrapper .dataTables_scrollBody td {
    cursor: pointer;
}

/* All interactive elements: action buttons, dropdowns, links - pointer cursor */
#contact_table .btn-group.dropdown,
#contact_table .btn-group.dropdown .dropdown-toggle,
#contact_table_wrapper .dataTables_scrollBody .btn-group.dropdown .dropdown-toggle,
#contact_table button,
#contact_table_wrapper .dataTables_scrollBody button,
#contact_table .dropdown-menu a,
#contact_table .dropdown-menu button,
#contact_table a[href],
#contact_table_wrapper .dataTables_scrollBody a[href] {
    cursor: pointer !important;
}

/* Disabled elements: not-allowed cursor */
#contact_table .disabled,
#contact_table_wrapper .dataTables_scrollBody .disabled {
    cursor: not-allowed;
}

/* Action column cell: pointer so users know the Actions button is clickable */
#contact_table tbody td:nth-child(2),
#contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(2) {
    cursor: pointer;
}

/* Fixed table layout: consistent column widths and alignment under headers */
#contact_table,
#contact_table_wrapper .dataTables_scrollHead table,
#contact_table_wrapper .dataTables_scrollBody table {
    table-layout: fixed !important;
    width: 100% !important;
    border-collapse: collapse;
}
#contact_table thead th,
#contact_table_wrapper .dataTables_scrollHead thead th {
    white-space: nowrap !important;
}
/* Text wraps within cells so long content (e.g. addresses) does not overflow or misalign */
#contact_table tbody td,
#contact_table_wrapper .dataTables_scrollBody tbody td {
    overflow-wrap: break-word !important;
    word-wrap: break-word !important;
    word-break: break-word;
}
.amazon-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.amazon-table thead {
    background: #ffffff;
    position: sticky;
    top: 0;
    z-index: 10;
}

.amazon-table thead th {
    padding: 10px 12px;
    font-weight: 600;
    color: #0F1111;
    text-align: left;
    border: none;
    white-space: nowrap;
    vertical-align: middle;
    border-right: 1px solid #D5D9D9;
}

.amazon-table thead th:last-child {
    border-right: none;
}

/* Customers table head - align with global white header */
#contact_table thead {
    background: #ffffff !important;
    border-bottom: 2px solid #D5D9D9 !important;
}

#contact_table thead th {
    background: #ffffff !important;
    padding: 10px 12px !important;
    font-weight: 600 !important;
    color: #0F1111 !important;
    text-align: left;
    border: none !important;
    border-right: 1px solid #D5D9D9 !important;
}

/* Ensure Action, Customer ID and checkbox columns match header color */
#contact_table thead th:nth-child(1),
#contact_table thead th:nth-child(2),
#contact_table thead th:nth-child(3) {
    background: #ffffff !important;
    color: #0F1111 !important;
}

#contact_table thead th:last-child {
    border-right: none !important;
}

#contact_table thead th.sorting::after,
#contact_table thead th.sorting_asc::after,
#contact_table thead th.sorting_desc::after {
    color: rgba(0, 0, 0, 0.6);
}

#contact_table thead th.sorting_asc::after,
#contact_table thead th.sorting_desc::after {
    color: #FF9900 !important;
}

/* Fix sorting icons - no extra line */
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
    /* Allow columns to show full content; min-widths set per column */
}

/* Text Truncation with Interactive Tooltip */
.amazon-text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
    display: block;
    position: relative;
    cursor: pointer;
}

.amazon-text-truncate:hover {
    color: #007185;
}

/* Tooltip on hover */
.amazon-text-truncate[data-full-text]:hover::after {
    content: attr(data-full-text);
    position: absolute;
    left: 0;
    top: 100%;
    background: #0F1111;
    color: #FFF;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    white-space: normal;
    max-width: 300px;
    word-wrap: break-word;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    margin-top: 4px;
}

/* ===== Table alignment: headers and content in sync (reference layout) ===== */
/* Consistent padding and box-sizing so headers align with columns */
#contact_table thead th,
#contact_table tbody td,
#contact_table_wrapper .dataTables_scrollHead thead th,
#contact_table_wrapper .dataTables_scrollBody tbody td {
    padding: 10px 12px !important;
    vertical-align: middle !important;
    box-sizing: border-box !important;
}

/* Customer table: Customer ID, Business Name, Name - proper spacing and left align */
[data-contact-type="customer"] #contact_table thead th:nth-child(3),
[data-contact-type="customer"] #contact_table tbody td:nth-child(3),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(3),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(3) {
    min-width: 95px !important;
    width: 100px !important;
    text-align: left !important;
}
[data-contact-type="customer"] #contact_table thead th:nth-child(4),
[data-contact-type="customer"] #contact_table tbody td:nth-child(4),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(4),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(4) {
    min-width: 120px !important;
    width: 140px !important;
    text-align: left !important;
}
[data-contact-type="customer"] #contact_table thead th:nth-child(5),
[data-contact-type="customer"] #contact_table tbody td:nth-child(5),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(5),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(5) {
    min-width: 200px !important;
    width: 220px !important;
    text-align: left !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    line-height: 1.35 !important;
}

/* Column 1: Checkbox - center aligned */
#contact_table thead th:nth-child(1),
#contact_table tbody td:nth-child(1),
#contact_table_wrapper .dataTables_scrollHead thead th:nth-child(1),
#contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(1) {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    text-align: center !important;
}

/* Column 2: Action - left aligned so Actions button lines up with header */
#contact_table thead th:nth-child(2),
#contact_table tbody td:nth-child(2),
#contact_table_wrapper .dataTables_scrollHead thead th:nth-child(2),
#contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(2) {
    width: 90px !important;
    min-width: 90px !important;
    max-width: 90px !important;
    text-align: left !important;
}
#contact_table tbody td:nth-child(2) .btn-group.dropdown,
#contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(2) .btn-group.dropdown {
    margin: 0;
}

/* Action Column - Compact (legacy) */
.amazon-table td:nth-child(2) {
    width: 90px !important;
    min-width: 90px !important;
    max-width: 90px !important;
}

/* Column Width Adjustments */
/* Name column - larger (4th column for customers) */
#contact_table th:nth-child(5),
#contact_table td:nth-child(5) {
    min-width: 180px !important;
    max-width: 220px !important;
}

/* Brand column - smaller */
#contact_table th#brand_col {
    width: 80px !important;
    min-width: 70px !important;
    max-width: 90px !important;
}

/* Total Sale Due - left-aligned under header (no right indent) */
.col-total-sale-due,
#contact_table th.col-total-sale-due,
#contact_table td.col-total-sale-due,
#contact_table_wrapper .dataTables_scrollHead th.col-total-sale-due,
#contact_table_wrapper .dataTables_scrollBody td.col-total-sale-due {
    width: 110px !important;
    min-width: 100px !important;
    max-width: 120px !important;
    text-align: left !important;
    padding: 10px 12px !important;
    vertical-align: middle !important;
}
/* Override any nth-child rules so Total Sale Due stays left-aligned */
[data-contact-type="customer"] #contact_table td.col-total-sale-due,
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollBody td.col-total-sale-due {
    text-align: left !important;
}

/* Invoices - right align (numeric) */
.col-invoices,
#contact_table th.col-invoices,
#contact_table td.col-invoices,
#contact_table_wrapper .dataTables_scrollHead th.col-invoices,
#contact_table_wrapper .dataTables_scrollBody td.col-invoices {
    width: 65px !important;
    min-width: 55px !important;
    max-width: 75px !important;
    text-align: right !important;
}

/* Customer: Total Sell Return Due right, Invoices right; Total Sale Due stays left (reference) */
[data-contact-type="customer"] #contact_table thead th:nth-last-child(2),
[data-contact-type="customer"] #contact_table tbody td:nth-last-child(2),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-last-child(2),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-last-child(2) {
    text-align: right !important;
}
[data-contact-type="customer"] #contact_table thead th:nth-last-child(1),
[data-contact-type="customer"] #contact_table tbody td:nth-last-child(1),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-last-child(1),
[data-contact-type="customer"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-last-child(1) {
    text-align: right !important;
}

/* Supplier: Total Purchase Due & Total Purchase Return Due - right align header and data */
[data-contact-type="supplier"] #contact_table thead th:nth-last-child(1),
[data-contact-type="supplier"] #contact_table tbody td:nth-last-child(1),
[data-contact-type="supplier"] #contact_table thead th:nth-last-child(2),
[data-contact-type="supplier"] #contact_table tbody td:nth-last-child(2),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-last-child(1),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-last-child(1),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-last-child(2),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-last-child(2) {
    text-align: right !important;
}

/* Brand column styling */
.col-brand,
#contact_table th.col-brand,
#contact_table td.col-brand {
    width: 80px !important;
    min-width: 70px !important;
    max-width: 90px !important;
}

/* Address, Mobile, Total Sale Due: aligned under headers (reference layout) */
/* Same padding, vertical-align and box-sizing so content lines up with header text */
#contact_table th.col-address,
#contact_table td.col-address,
#contact_table_wrapper .dataTables_scrollHead th.col-address,
#contact_table_wrapper .dataTables_scrollBody td.col-address,
#contact_table td.address-processed,
#contact_table td.address-cell,
.address-cell,
.address-processed {
    min-width: 200px !important;
    max-width: 320px !important;
    width: 260px !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    overflow: visible !important;
    text-overflow: clip !important;
    cursor: default !important;
    text-align: left !important;
    padding: 10px 12px !important;
    vertical-align: middle !important;
    line-height: 1.35 !important;
    box-sizing: border-box !important;
}

/* Mobile - aligned under header, consistent width */
#contact_table th.col-mobile,
#contact_table td.col-mobile,
#contact_table_wrapper .dataTables_scrollHead th.col-mobile,
#contact_table_wrapper .dataTables_scrollBody td.col-mobile {
    min-width: 120px !important;
    width: 130px !important;
    max-width: 140px !important;
    text-align: left !important;
    padding: 10px 12px !important;
    vertical-align: middle !important;
    box-sizing: border-box !important;
}

/* Supplier: purchase columns - right align and enough width for header text */
[data-contact-type="supplier"] #contact_table thead th:nth-last-child(1),
[data-contact-type="supplier"] #contact_table thead th:nth-last-child(2),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-last-child(1),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-last-child(2) {
    min-width: 130px !important;
    width: 140px !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
}

/* Total Purchase Due - fixed width so data stays under header */
.col-purchase-due,
#contact_table th.col-purchase-due,
#contact_table td.col-purchase-due,
#contact_table_wrapper .dataTables_scrollHead th.col-purchase-due,
#contact_table_wrapper .dataTables_scrollBody td.col-purchase-due {
    min-width: 140px !important;
    width: 145px !important;
    max-width: 160px !important;
    text-align: right !important;
    box-sizing: border-box !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
}

/* Total Purchase Return Due - wide enough so full header is visible at 100% zoom */
.col-purchase-return-due,
#contact_table th.col-purchase-return-due,
#contact_table td.col-purchase-return-due,
#contact_table_wrapper .dataTables_scrollHead th.col-purchase-return-due,
#contact_table_wrapper .dataTables_scrollBody td.col-purchase-return-due {
    min-width: 250px !important;
    width: 260px !important;
    max-width: 280px !important;
    text-align: right !important;
    box-sizing: border-box !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
    white-space: nowrap !important;
    overflow: visible !important;
}

/* Supplier: Business Name & Name - prevent overlap, keep aligned at 100% zoom */
[data-contact-type="supplier"] #contact_table thead th:nth-child(4),
[data-contact-type="supplier"] #contact_table tbody td:nth-child(4),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(4),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(4) {
    min-width: 155px !important;
    width: 160px !important;
    box-sizing: border-box !important;
}
[data-contact-type="supplier"] #contact_table thead th:nth-child(5),
[data-contact-type="supplier"] #contact_table tbody td:nth-child(5),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(5),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollBody tbody td:nth-child(5) {
    min-width: 170px !important;
    width: 175px !important;
    box-sizing: border-box !important;
}

/* Class-based so body cells get same widths when columnDefs apply (supplier) */
#contact_table th.col-business-name,
#contact_table td.col-business-name,
#contact_table_wrapper .dataTables_scrollHead th.col-business-name,
#contact_table_wrapper .dataTables_scrollBody td.col-business-name {
    min-width: 155px !important;
    width: 160px !important;
    box-sizing: border-box !important;
}
#contact_table th.col-name,
#contact_table td.col-name,
#contact_table_wrapper .dataTables_scrollHead th.col-name,
#contact_table_wrapper .dataTables_scrollBody td.col-name {
    min-width: 170px !important;
    width: 175px !important;
    box-sizing: border-box !important;
}

/* Supplier: Opening Balance & Advance Balance - prevent header text cut off */
[data-contact-type="supplier"] #contact_table thead th:nth-child(9),
[data-contact-type="supplier"] #contact_table thead th:nth-child(10),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(9),
[data-contact-type="supplier"] #contact_table_wrapper .dataTables_scrollHead thead th:nth-child(10) {
    min-width: 120px !important;
    width: 125px !important;
}

/* Action Dropdown */
.amazon-action-dropdown {
    position: relative;
    display: inline-block;
}

.amazon-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 500;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    color: #0F1111;
    cursor: pointer;
    transition: all 0.15s ease;
}

.amazon-action-btn:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
}

.amazon-action-btn svg {
    width: 12px;
    height: 12px;
}

/* Bootstrap Dropdown Menu Fix for Contact Table */
#contact_table .btn-group.dropdown {
    position: relative;
    z-index: auto;
}

#contact_table tbody tr {
    position: relative;
}

/* Actions Button Styling - Dark Theme */
#contact_table .btn-group.dropdown .dropdown-toggle {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%) !important;
    border-color: #37475A !important;
    color: #FFFFFF !important;
    font-weight: 500 !important;
}

#contact_table .btn-group.dropdown .dropdown-toggle:hover,
#contact_table .btn-group.dropdown .dropdown-toggle:focus {
    background: linear-gradient(to bottom, #37475a 0%, #232F3E 100%) !important;
    border-color: #37475A !important;
    color: #FFFFFF !important;
}

#contact_table .btn-group.dropdown.open .dropdown-toggle {
    background: linear-gradient(to bottom, #37475a 0%, #232F3E 100%) !important;
    border-color: #37475A !important;
    color: #FFFFFF !important;
}

/* Dark theme for highlighted rows */
#contact_table tbody tr:hover .btn-group.dropdown .dropdown-toggle,
#contact_table tbody tr.selected .btn-group.dropdown .dropdown-toggle {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%) !important;
    border-color: #37475A !important;
    color: #FFFFFF !important;
}

#contact_table .dropdown-menu {
    display: none !important;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    z-index: 1050 !important;
    min-width: 160px !important;
    padding: 4px 0 !important;
    margin: 2px 0 0 !important;
    background-color: #232F3E !important;
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%) !important;
    border: 1px solid #37475A !important;
    border-radius: 4px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
    overflow: visible !important;
    visibility: visible !important;
    opacity: 1 !important;
}

#contact_table .btn-group.open .dropdown-menu,
#contact_table .btn-group.dropdown.open .dropdown-menu,
#contact_table .dropdown.open .dropdown-menu,
#contact_table .btn-group.dropdown-toggle[aria-expanded="true"] ~ .dropdown-menu,
#contact_table .btn-group[aria-expanded="true"] .dropdown-menu {
    display: block !important;
}

#contact_table .dropdown-menu li {
    display: block !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

#contact_table .dropdown-menu li.divider {
    height: 1px !important;
    margin: 4px 0 !important;
    overflow: hidden !important;
    background-color: #37475A !important;
    border: none !important;
}

#contact_table .dropdown-menu li a {
    display: block !important;
    padding: 8px 16px !important;
    clear: both !important;
    font-weight: normal !important;
    line-height: 1.5 !important;
    color: #FFFFFF !important;
    white-space: nowrap !important;
    text-decoration: none !important;
    background-color: transparent !important;
    border: none !important;
    font-size: 13px !important;
    transition: all 0.15s ease !important;
}

#contact_table .dropdown-menu li a:hover,
#contact_table .dropdown-menu li a:focus {
    color: #FFFFFF !important;
    text-decoration: none !important;
    background-color: #37475A !important;
}

#contact_table .dropdown-menu li a i {
    margin-right: 8px !important;
    width: 16px !important;
    text-align: center !important;
    color: #FF9900 !important;
}

/* Footer - Pagination & Info */
.amazon-table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-table-info {
    font-size: 13px;
    color: #565959;
}

.amazon-table-info strong {
    color: #0F1111;
}

/* Amazon Pagination */
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
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    color: #0F1111;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.amazon-pagination .page-btn:first-child {
    border-radius: 4px 0 0 4px;
}

.amazon-pagination .page-btn:last-child {
    border-radius: 0 4px 4px 0;
}

.amazon-pagination .page-btn:hover:not(.active):not(.disabled) {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    text-decoration: none;
    color: #0F1111;
}

.amazon-pagination .page-btn.active {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: white;
}

.amazon-pagination .page-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.amazon-pagination .page-ellipsis {
    padding: 0 8px;
    color: #565959;
}

/* Total Row (footer) - hidden to remove the black summary bar */
#contact_table tfoot,
#contact_table_wrapper .dataTables_scrollBody tfoot,
.amazon-table tfoot,
#contact_table_wrapper table tfoot {
    display: none !important;
}

.amazon-table tfoot tr {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
}

.amazon-table tfoot td {
    padding: 10px 12px;
    color: #FFF;
    font-weight: 600;
}

/* Override DataTables Default Styles */
.dataTables_wrapper {
    padding: 0 !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    display: none !important;
}

.dataTables_wrapper .dt-buttons {
    display: none !important;
}

/* DataTables Processing */
.dataTables_processing {
    background: rgba(255, 255, 255, 0.9) !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 8px !important;
    padding: 20px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
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

/* Old Column visibility dropdown - keeping for compatibility */
.dt-button-collection {
    background: #FFF !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    padding: 8px !important;
}

.dt-button-collection .dt-button {
    display: block !important;
    width: 100% !important;
    padding: 8px 12px !important;
    margin: 2px 0 !important;
    font-size: 13px !important;
    text-align: left !important;
    background: transparent !important;
    border: none !important;
    border-radius: 4px !important;
    color: #0F1111 !important;
}

.dt-button-collection .dt-button:hover {
    background: #F7FAFA !important;
}

.dt-button-collection .dt-button.active {
    background: #FFF4E5 !important;
    color: #C7511F !important;
}

/* Fix scrolling container - horizontal scroll so all columns visible (dynamic layout) */
#contact_table_wrapper.dataTables_wrapper {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    max-width: 100%;
}

.amazon-table-wrapper.table-container {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.dataTables_scrollBody {
    border-bottom: none !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

.dataTables_scrollHead {
    border-bottom: none !important;
    overflow-x: hidden;
}

/* Scroll tables: fixed layout for alignment, max-content so horizontal scroll works */
#contact_table_wrapper .dataTables_scrollHead table,
#contact_table_wrapper .dataTables_scrollBody table {
    min-width: 100%;
    width: max-content !important;
    table-layout: fixed !important;
    border-collapse: collapse;
}

/* Ensure each column can show its content; horizontal scroll shows all columns */
#contact_table_wrapper .dataTables_scrollBody {
    min-height: 0;
}

/* Scroll head: overflow hidden but scrollLeft synced via JS so header stays aligned with body */
#contact_table_wrapper .dataTables_scrollHead {
    overflow-x: hidden !important;
    overflow-y: hidden;
}
#contact_table_wrapper .dataTables_scrollHeadInner {
    overflow: visible;
}
/* Body: visible horizontal scrollbar for dynamic layout; user scrolls here */
#contact_table_wrapper .dataTables_scrollBody {
    overflow-x: auto !important;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

/* Responsive - table stays aligned and scrollable across screen sizes */
@media (max-width: 768px) {
    .amazon-contacts-container {
        padding: 10px 12px;
    }
    
    .amazon-page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .amazon-page-subheader {
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
        max-width: 100%;
    }
    
    .amazon-table-footer {
        flex-direction: column;
        align-items: center;
    }
    
    .amazon-table-wrapper.table-container,
    #contact_table_wrapper.dataTables_wrapper {
        overflow-x: auto !important;
        max-width: 100%;
        -webkit-overflow-scrolling: touch;
    }
}

@media (max-width: 992px) {
    .amazon-table-wrapper.table-container,
    #contact_table_wrapper.dataTables_wrapper {
        overflow-x: auto !important;
        max-width: 100%;
    }
}

/* Hide original nav tabs */
#contactStatusTabs {
    display: none !important;
}

/* Make table rows clickable cursor */
#contact_table tbody tr[href] {
    cursor: pointer;
}
</style>
@endsection

@section('content')
<div class="amazon-contacts-container" data-contact-type="{{ $type }}">
    <!-- Top banner – Amazon style -->
    <div class="amazon-contacts-header-banner amazon-theme-banner">
        <div class="amazon-contacts-header-content">
            <h1 class="amazon-contacts-header-title">
                <i class="fas fa-users"></i>
                @lang('lang_v1.' . $type . 's')
            </h1>
            <p class="amazon-contacts-header-subtitle">
                @if ($type == 'customer')
                View and manage customer accounts. Filter by status or Prime subscription tier.
                @else
                View and manage supplier contacts and vendor information.
                @endif
            </p>
        </div>
    </div>

    <!-- Status Tabs + Prime Groups + Search (below banner) -->
    <div class="amazon-page-header">
        @if ($type == 'customer')
        @php
            $user = auth()->user();
            $business_id = session('business.id');
            $permitted_locations = $user->permitted_locations($business_id);
            $is_admin = $user->can('access_all_locations') || $user->can('admin');
            
            $has_b2c_access = false;
            if ($permitted_locations == 'all') {
                $has_b2c_access = \App\BusinessLocation::where('business_id', $business_id)
                    ->where('is_b2c', 1)
                    ->exists();
            } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
                $has_b2c_access = \App\BusinessLocation::whereIn('id', $permitted_locations)
                    ->where('is_b2c', 1)
                    ->exists();
            }
            // Brand column is not required for customers
            $show_brand_column = false;
        @endphp

        <div class="amazon-page-subheader">
            <div class="amazon-page-subheader-left">
                <div class="amazon-status-tabs" id="amazonStatusTabs">
                <a class="amazon-tab active" data-status="active" href="#">
                    Active
                    @if(isset($customer_status_counts))
                    <span class="status-count">({{ $customer_status_counts['active'] ?? 0 }})</span>
                    @endif
                </a>
                <a class="amazon-tab" data-status="inactive" href="#">
                    Inactive
                    @if(isset($customer_status_counts))
                    <span class="status-count">({{ $customer_status_counts['inactive'] ?? 0 }})</span>
                    @endif
                </a>
                <a class="amazon-tab" data-status="pending" href="#">
                    Pending
                    @if(isset($customer_status_counts))
                    <span class="status-count">({{ $customer_status_counts['pending'] ?? 0 }})</span>
                    @endif
                </a>
                <a class="amazon-tab" data-status="rejected" href="#">
                    Rejected
                    @if(isset($customer_status_counts))
                    <span class="status-count">({{ $customer_status_counts['rejected'] ?? 0 }})</span>
                    @endif
                </a>
                @if ($has_b2c_access || $is_admin)
                <a class="amazon-tab" data-status="guest" href="#">
                    Guest
                    @if(isset($customer_status_counts))
                    <span class="status-count">({{ $customer_status_counts['guest'] ?? 0 }})</span>
                    @endif
                </a>
                @endif
                </div>
                
                @if (isset($prime_customer_counts))
                <div class="amazon-prime-counts-header" id="amazonPrimeTierTabs">
                    <a href="#" class="prime-count-item prime-tab" data-prime-tier="prime_silver" title="Filter by Prime Silver">
                        Prime Silver
                        <span class="prime-count">({{ $prime_customer_counts['prime_silver'] ?? 0 }})</span>
                    </a>
                    <a href="#" class="prime-count-item prime-tab" data-prime-tier="prime_gold" title="Filter by Prime Gold">
                        Prime Gold
                        <span class="prime-count">({{ $prime_customer_counts['prime_gold'] ?? 0 }})</span>
                    </a>
                    <a href="#" class="prime-count-item prime-tab" data-prime-tier="prime_platinum" title="Filter by Prime Platinum">
                        Prime Platinum
                        <span class="prime-count">({{ $prime_customer_counts['prime_platinum'] ?? 0 }})</span>
                    </a>
                    <a href="#" class="prime-count-item prime-tab" data-prime-tier="prime_elite" title="Filter by Prime Elite">
                        Prime Elite
                        <span class="prime-count">({{ $prime_customer_counts['prime_elite'] ?? 0 }})</span>
                    </a>
                    <a href="#" class="prime-count-item prime-tab" data-prime-tier="prime_pro" title="Filter by Prime Pro">
                        Prime Pro
                        <span class="prime-count">({{ $prime_customer_counts['prime_pro'] ?? 0 }})</span>
                    </a>
                    <a href="#" class="prime-count-item prime-tab" data-prime-tier="prime_pro_max" title="Filter by Prime Pro Max">
                        Prime Pro Max
                        <span class="prime-count">({{ $prime_customer_counts['prime_pro_max'] ?? 0 }})</span>
                    </a>
                </div>
                @endif
            </div>

            <div class="amazon-page-header-search">
                <div class="amazon-search-wrapper">
                    <svg class="amazon-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="amazon-search-input" id="amazonSearchInput" placeholder="Search...">
                </div>
            </div>
        </div>
        @endif

        @if ($type == 'supplier' && !empty($vendor_type_counts))
        <div class="amazon-vendor-type-counts">
            <div class="vendor-type-chip">
                <span class="vendor-type-label">In-house vendors:</span>
                <span class="vendor-type-count">{{ $vendor_type_counts['inhouse'] ?? 0 }}</span>
            </div>
            <div class="vendor-type-chip">
                <span class="vendor-type-label">Dropship vendors:</span>
                <span class="vendor-type-count">{{ $vendor_type_counts['dropship'] ?? 0 }}</span>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Hidden Original Tabs for Compatibility -->
    @if ($type == 'customer')
    <ul class="nav nav-tabs hide" style="display:none!important;" id="contactStatusTabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-status="active" href="#">Active</a></li>
        <li class="nav-item"><a class="nav-link" data-status="inactive" href="#">Inactive</a></li>
        <li class="nav-item"><a class="nav-link" data-status="pending" href="#">Pending</a></li>
        <li class="nav-item"><a class="nav-link" data-status="rejected" href="#">Rejected</a></li>
        @if ($has_b2c_access || $is_admin)
        <li class="nav-item"><a class="nav-link" data-status="guest" href="#">Guest</a></li>
        @endif
    </ul>
    <input type="hidden" id="contact_status_tab_filter" value="active">
    <input type="hidden" id="prime_tier_filter" value="">
    <input type="hidden" id="show_brand_column" value="{{ $show_brand_column ? '1' : '0' }}">
    @endif

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #37475A; color: #ffffff;">
                    <h4 class="modal-title" style="margin: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 8px;">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        @lang('report.filters')
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="row">
                        @if ($type == 'customer')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="has_sell_due" value="1" id="has_sell_due" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('lang_v1.sell_due')</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="has_sell_return" value="1" id="has_sell_return" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('lang_v1.sell_return')</strong>
                                </label>
                            </div>
                        </div>
                        @elseif($type == 'supplier')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="has_purchase_due" value="1" id="has_purchase_due" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('report.purchase_due')</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="has_purchase_return" value="1" id="has_purchase_return" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('lang_v1.purchase_return')</strong>
                                </label>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="has_advance_balance" value="1" id="has_advance_balance" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('lang_v1.advance_balance')</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="has_opening_balance" value="1" id="has_opening_balance" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('lang_v1.opening_balance')</strong>
                                </label>
                            </div>
                        </div>
                        @if ($type == 'customer')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="has_no_sell_from">@lang('lang_v1.has_no_sell_from'):</label>
                                {!! Form::select(
                                    'has_no_sell_from',
                                    [
                                        'one_month' => __('lang_v1.one_month'),
                                        'three_months' => __('lang_v1.three_months'),
                                        'six_months' => __('lang_v1.six_months'),
                                        'one_year' => __('lang_v1.one_year'),
                                    ],
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'id' => 'has_no_sell_from',
                                        'placeholder' => __('messages.please_select'),
                                        'style' => 'border-color: #888C8C; border-radius: 4px;'
                                    ],
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cg_filter">@lang('lang_v1.customer_group'):</label>
                                {!! Form::select('cg_filter', $customer_groups, null, [
                                    'class' => 'form-control',
                                    'id' => 'cg_filter',
                                    'style' => 'border-color: #888C8C; border-radius: 4px;'
                                ]) !!}
                            </div>
                        </div>
                        @endif
                        @if (config('constants.enable_contact_assign') === true)
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('assigned_to', __('lang_v1.assigned_to') . ':') !!}
                                {!! Form::select('assigned_to', $users, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%'
                                ]) !!}
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status_filter">@lang('sale.status'):</label>
                                {!! Form::select(
                                    'status_filter',
                                    ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')],
                                    null,
                                    ['class' => 'form-control', 'id' => 'status_filter', 'placeholder' => __('lang_v1.none'), 'style' => 'border-color: #888C8C; border-radius: 4px;'],
                                ) !!}
                            </div>
                        </div>
                        @if ($type == 'customer')
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_filter', __('business.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations ?? [], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'location_filter', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                        @if ($show_brand_column)
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('brand_filter',"Brand" . ':') !!}
                                {!! Form::select('brand_id', $brands ?? [], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'brand_filter', 'placeholder' => __('lang_v1.all')]); !!}
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                <div class="modal-footer" style="background: #37475A; color: #ffffff; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" value="{{ $type }}" id="contact_type">
    
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
                @if ($type == 'customer' && auth()->user()->can('customer.update'))
                    <div id="bulk_customer_status_actions" style="display:none; gap:6px; align-items:center; flex-wrap:wrap;">
                        <span id="bulk_customer_selected_count" style="font-size:13px; color:#565959;"></span>
                        <button type="button" class="amazon-btn amazon-btn-secondary bulk-customer-status" data-status="active">Active</button>
                        <button type="button" class="amazon-btn amazon-btn-secondary bulk-customer-status" data-status="inactive">Inactive</button>
                        <button type="button" class="amazon-btn amazon-btn-secondary bulk-customer-status" data-status="pending">Pending</button>
                        <button type="button" class="amazon-btn amazon-btn-secondary bulk-customer-status" data-status="rejected">Rejected</button>
                        @if ($has_b2c_access || $is_admin)
                            <button type="button" class="amazon-btn amazon-btn-secondary bulk-customer-status" data-status="guest">Guest</button>
                        @endif
                    </div>
                @endif
            </div>
            <div class="amazon-controls-right">
                @if(auth()->user()->can('customer.delete') || auth()->user()->can('supplier.delete'))
                <div class="bulk-actions-dropdown">
                    <button type="button" class="bulk-actions-btn" id="contactBulkActionsBtn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                        </svg>
                        Bulk Actions
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="bulk-actions-menu" id="contactBulkActionsMenu">
                        <button type="button" class="bulk-action-item danger" id="contactBulkDeleteBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Delete Selected
                        </button>
                    </div>
                </div>
                @endif
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
                        </div>
                    </div>
                </div>
                <button class="amazon-btn amazon-btn-secondary" id="amazonPdfBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                    </svg>
                    Export PDF
                </button>
                <button class="amazon-btn amazon-btn-primary" id="amazonAddBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add
                </button>
            </div>
        </div>
        
        <!-- Table -->
        @if (
            auth()->user()->can('supplier.view') ||
            auth()->user()->can('customer.view') ||
            auth()->user()->can('supplier.view_own') ||
            auth()->user()->can('customer.view_own')
        )
        <div class="amazon-table-wrapper table-container">
            <table class="amazon-table table table-bordered table-striped" id="contact_table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox" id="select-all-row" data-table-id="contact_table"></th>
                        <th style="width: 90px;">@lang('messages.action')</th>
                        @if ($type == 'supplier')
                        <th style="width: 95px; min-width: 95px;">Supplier ID</th>
                        <th class="col-business-name" style="width: 160px; min-width: 155px;">@lang('business.business_name')</th>
                        <th class="col-name" style="width: 175px; min-width: 170px;">@lang('contact.name')</th>
                        <th style="width: 180px; min-width: 160px;">@lang('business.email')</th>
                        <th style="width: 100px; min-width: 90px;">@lang('contact.tax_no')</th>
                        <th style="width: 100px; min-width: 90px;">@lang('contact.pay_term')</th>
                        <th style="width: 120px; min-width: 110px;">@lang('account.opening_balance')</th>
                        <th style="width: 120px; min-width: 110px;">@lang('lang_v1.advance_balance')</th>
                        <th style="width: 95px; min-width: 85px;">Created</th>
                        <th class="col-address" style="width: 200px; min-width: 180px;">@lang('business.address')</th>
                        <th class="col-mobile" style="width: 120px; min-width: 115px;">@lang('contact.mobile')</th>
                        <th class="col-purchase-due" style="width: 145px; min-width: 140px;">@lang('contact.total_purchase_due')</th>
                        <th class="col-purchase-return-due" style="width: 260px; min-width: 250px;">@lang('lang_v1.total_purchase_return_due')</th>
                        @elseif($type == 'customer')
                        <th style="width: 95px; min-width: 95px;">Customer ID</th>
                        <th style="width: 130px; min-width: 120px;">@lang('business.business_name')</th>
                        <th style="width: 200px; min-width: 180px;">@lang('user.name')</th>
                        <th>@lang('business.email')</th>
                        <th>@lang('contact.tax_no')</th>
                        <th>@lang('lang_v1.credit_limit')</th>
                        <th>@lang('account.opening_balance')</th>
                        <th>@lang('lang_v1.advance_balance')</th>
                        <th>Created</th>
                        {{-- @if ($reward_enabled)
                        <th id="rp_col">{{ session('business.rp_name') }}</th>
                        @endif --}}
                        <th>@lang('lang_v1.customer_group')</th>
                        @php
                            $user = auth()->user();
                            $business_id = session('business.id');
                            $permitted_locations = $user->permitted_locations($business_id);
                            $is_admin = $user->can('access_all_locations') || $user->can('admin');
                            
                            $has_b2c_access = false;
                            if ($permitted_locations == 'all') {
                                $has_b2c_access = \App\BusinessLocation::where('business_id', $business_id)
                                    ->where('is_b2c', 1)
                                    ->exists();
                            } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
                                $has_b2c_access = \App\BusinessLocation::whereIn('id', $permitted_locations)
                                    ->where('is_b2c', 1)
                                    ->exists();
                            }
                            // Brand column is not needed for customers
                            $show_brand_column = false;
                        @endphp
                        @if ($show_brand_column)
                        <th id="brand_col" style="width: 80px; min-width: 70px;">Brand</th>
                        @endif
                        <th class="col-address" style="width: 200px; min-width: 200px;">@lang('business.address')</th>
                        <th class="col-mobile" style="width: 120px; min-width: 115px;">@lang('contact.mobile')</th>
                        <th class="col-total-sale-due" style="width: 100px; min-width: 90px;">@lang('contact.total_sale_due')</th>
                        <th>@lang('lang_v1.total_sell_return_due')</th>
                        <th class="col-invoices" style="width: 65px; min-width: 55px;">Invoices</th>
                        @endif
                    </tr>
                </thead>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total">
                        @if ($type == 'supplier')
                            <td colspan="3"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td colspan="7"></td>
                            <td></td>
                            <td></td>
                            <td class="footer_contact_due"></td>
                            <td class="footer_contact_return_due"></td>
                        @elseif ($type == 'customer')
                            @php
                                $user = auth()->user();
                                $business_id = session('business.id');
                                $permitted_locations = $user->permitted_locations($business_id);
                                $is_admin = $user->can('access_all_locations') || $user->can('admin');
                                
                                $has_b2c_access = false;
                                if ($permitted_locations == 'all') {
                                    $has_b2c_access = \App\BusinessLocation::where('business_id', $business_id)
                                        ->where('is_b2c', 1)
                                        ->exists();
                                } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
                                    $has_b2c_access = \App\BusinessLocation::whereIn('id', $permitted_locations)
                                        ->where('is_b2c', 1)
                                        ->exists();
                                }
                                // Brand column is hidden for customers
                                $show_brand_column = false;
                                $brand_colspan = $show_brand_column ? 1 : 0;
                            @endphp
                            <td colspan="6"></td>
                            @if ($reward_enabled)
                                <td colspan="{{ 6 + $brand_colspan }}"></td>
                                <td><strong>@lang('sale.total'):</strong></td>
                            @else
                                <td colspan="{{ 5 + $brand_colspan }}"></td>
                                <td><strong>@lang('sale.total'):</strong></td>
                            @endif
                            <td class="footer_contact_due"></td>
                            <td class="footer_contact_return_due"></td> 
                            <td></td>
                            <td></td> 
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Custom Footer with Pagination -->
        <div class="amazon-table-footer">
            <div class="amazon-table-info" id="amazonTableInfo">
                Showing <strong>0</strong> to <strong>0</strong> of <strong>0</strong> entries
            </div>
            <div class="amazon-pagination" id="amazonPagination">
                <!-- Pagination will be generated by JS -->
            </div>
        </div>
        @endif
    </div>

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</div>

{{-- Hide empty duplicate header row in scrollBody (between header and data) --}}
<style id="contact-table-scrollbody-thead-hide">
#contact_table_wrapper .dataTables_scrollBody table thead {
    display: none !important;
}
</style>

{{-- Table header - force white for both main table and DataTables scroll head (scrollX/scrollY) --}}
<style id="contact-table-header-override">
/* Main table (no scroll) and DataTables cloned header (scrollX/scrollY) */
table#contact_table thead,
table#contact_table thead tr,
table#contact_table thead th,
#contact_table_wrapper .dataTables_scrollHead thead,
#contact_table_wrapper .dataTables_scrollHead thead tr,
#contact_table_wrapper .dataTables_scrollHead thead th {
    background: #ffffff !important;
    color: #0F1111 !important;
    font-weight: 600 !important;
    text-shadow: none !important;
    border-bottom: 2px solid #D5D9D9 !important;
    box-shadow: none !important;
}
table#contact_table thead th,
#contact_table_wrapper .dataTables_scrollHead thead th {
    padding: 10px 12px !important;
    border-right: 1px solid #D5D9D9 !important;
    font-size: 13px !important;
}
table#contact_table thead th:last-child,
#contact_table_wrapper .dataTables_scrollHead thead th:last-child {
    border-right: none !important;
}
/* Sort icon alignment - same for main table and scroll head so columns align at 100% zoom */
table#contact_table thead th.sorting,
table#contact_table thead th.sorting_asc,
table#contact_table thead th.sorting_desc,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting_asc,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting_desc {
    position: relative !important;
    padding-right: 24px !important;
}
table#contact_table thead th.sorting::after,
table#contact_table thead th.sorting_asc::after,
table#contact_table thead th.sorting_desc::after,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting::after,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting_asc::after,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting_desc::after {
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
}
/* Sort icons */
table#contact_table thead th.sorting_asc::after,
table#contact_table thead th.sorting_desc::after,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting_asc::after,
#contact_table_wrapper .dataTables_scrollHead thead th.sorting_desc::after {
    color: #FF9900 !important;
    opacity: 1 !important;
}
</style>
@stop

@section('javascript')
<script>
$(document).ready(function() {
    var contactTable = null;

    function getSelectedContactRows() {
        var selected_rows = [];
        $('#contact_table_wrapper .dataTables_scrollBody input.row-select:checked').each(function() {
            selected_rows.push($(this).val());
        });
        if (selected_rows.length === 0) {
            $('#contact_table tbody input.row-select:checked').each(function() {
                selected_rows.push($(this).val());
            });
        }
        return selected_rows;
    }

    function updateBulkCustomerActions() {
        var $actions = $('#bulk_customer_status_actions');
        if (!$actions.length) return;
        var selected = getSelectedContactRows();
        if (selected.length > 0) {
            $('#bulk_customer_selected_count').text(selected.length + ' selected');
            $actions.css('display', 'inline-flex');
        } else {
            $('#bulk_customer_selected_count').text('');
            $actions.hide();
        }
    }

    // Bulk Actions + Bulk Delete: bind immediately (do not wait for DataTable)
    $(document).on('click', '#contactBulkActionsBtn', function(e) {
        e.stopPropagation();
        $('#contactBulkActionsMenu').toggleClass('show');
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.bulk-actions-dropdown').length) {
            $('#contactBulkActionsMenu').removeClass('show');
        }
    });
    $(document).on('click', '#contactBulkActionsMenu', function(e) {
        e.stopPropagation();
    });
    $(document).on('click', '#contactBulkDeleteBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#contactBulkActionsMenu').removeClass('show');
        var dt = (typeof contactTable !== 'undefined' && contactTable !== null) ? contactTable : (typeof contact_table !== 'undefined' ? contact_table : null);
        if (!dt) {
            toastr.error('Table not ready. Please refresh the page.');
            return;
        }
        var selected_rows = getSelectedContactRows();
        if (selected_rows.length === 0) {
            swal("@lang('lang_v1.no_row_selected')");
            return;
        }
        swal({
            title: (typeof LANG !== 'undefined' && LANG.sure) ? LANG.sure : 'Are you sure?',
            text: "Delete " + selected_rows.length + " selected contact(s)? This cannot be undone.",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then(function(willDelete) {
            var confirmed = willDelete === true || (willDelete && willDelete.isConfirmed === true);
            if (!confirmed) return;
            $.ajax({
                method: 'POST',
                url: '{{ url("/contacts/bulk/delete") }}',
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                data: {
                    selected_contacts: selected_rows.join(','),
                    _token: '{{ csrf_token() }}'
                },
                success: function(result) {
                    if (result.success) {
                        toastr.success(result.msg);
                        $('#select-all-row').prop('checked', false);
                        var tbl = (typeof contactTable !== 'undefined' && contactTable !== null) ? contactTable : (typeof contact_table !== 'undefined' ? contact_table : null);
                        if (tbl && typeof reloadDataTablePreservingPage === 'function') {
                            reloadDataTablePreservingPage(tbl, function() { updateBulkCustomerActions(); });
                        } else {
                            location.reload();
                        }
                    } else {
                        toastr.warning(result.msg);
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : (typeof LANG !== 'undefined' && LANG.something_went_wrong) ? LANG.something_went_wrong : 'Something went wrong.';
                    toastr.error(msg);
                }
            });
        });
    });

    // Wait for DataTable to initialize
    var checkTable = setInterval(function() {
        if (typeof contact_table !== 'undefined' && contact_table !== null) {
            contactTable = contact_table;
            clearInterval(checkTable);
            initCustomControls();
            /* Sync scroll head and body column widths; bind horizontal scroll sync */
            setTimeout(function() {
                try {
                    if (contactTable && typeof contactTable.columns === 'function') {
                        contactTable.columns().adjust();
                    }
                    if (typeof syncScrollHeadBodyWidths === 'function') syncScrollHeadBodyWidths();
                    if (typeof bindScrollSync === 'function') bindScrollSync();
                } catch (e) {}
            }, 400);
        }
    }, 100);
    
    function initCustomControls() {
        // Apply header styles (DataTables scroll puts header in separate div)
        setTimeout(function() {
            $('#contact_table thead th, #contact_table_wrapper .dataTables_scrollHead thead th').css({
                'background': '#ffffff',
                'color': '#0F1111',
                'font-weight': '600',
                'border-bottom': '2px solid #D5D9D9',
                'border-right': '1px solid #D5D9D9'
            });
            $('#contact_table thead th:last-child, #contact_table_wrapper .dataTables_scrollHead thead th:last-child').css('border-right', 'none');
        }, 200);
        
        // Custom entries select
        $('#amazonEntriesSelect').on('change', function() {
            contactTable.page.len($(this).val()).draw();
        });

    $(document).on('change', '#select-all-row', function() {
        var checked = $(this).is(':checked');
        var $inputs = $('#contact_table_wrapper .dataTables_scrollBody input.row-select').length
            ? $('#contact_table_wrapper .dataTables_scrollBody input.row-select')
            : $('#contact_table tbody input.row-select');
        $inputs.prop('checked', checked);
        updateBulkCustomerActions();
    });

    $(document).on('change', '#contact_table_wrapper input.row-select', function() {
        var $inputs = $('#contact_table_wrapper .dataTables_scrollBody input.row-select').length
            ? $('#contact_table_wrapper .dataTables_scrollBody input.row-select')
            : $('#contact_table tbody input.row-select');
        var total = $inputs.length;
        var checkedCount = $inputs.filter(':checked').length;
        $('#select-all-row').prop('checked', total > 0 && total === checkedCount);
        updateBulkCustomerActions();
    });

    $(document).on('click', '.bulk-customer-status', function(e) {
        e.preventDefault();
        if (!contactTable) {
            return;
        }

        var selected_rows = getSelectedContactRows();
        if (selected_rows.length === 0) {
            swal("@lang('lang_v1.no_row_selected')");
            return;
        }

        var status = $(this).data('status');
        swal({ title: LANG.sure, text: 'Update status for ' + selected_rows.length + ' customers?', icon: "info", buttons: true }).then((confirmed) => {
            if (!confirmed) {
                return;
            }

            $.ajax({
                method: 'POST',
                url: '/contacts/bulk-update-status',
                dataType: 'json',
                data: {
                    selected_contacts: selected_rows.join(','),
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(result) {
                    if (result.success) {
                        toastr.success(result.msg);
                        $('#select-all-row').prop('checked', false);
                        reloadDataTablePreservingPage(contactTable, function() {
                            updateBulkCustomerActions();
                        });
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function() {
                    toastr.error(LANG.something_went_wrong);
                }
            });
        });
    });

        // Custom search
        var searchTimeout;
        $('#amazonSearchInput').on('keyup', function() {
            clearTimeout(searchTimeout);
            var searchVal = $(this).val();
            searchTimeout = setTimeout(function() {
                contactTable.search(searchVal).draw();
            }, 300);
        });
        
        // Custom buttons - trigger DataTable buttons
        $('#amazonFilterBtn').on('click', function() {
            $('#filterModal').modal('show');
        });
        
        $('#amazonCsvBtn').on('click', function() {
            contactTable.button('.buttons-csv').trigger();
        });
        
        $('#amazonExcelBtn').on('click', function() {
            contactTable.button('.buttons-excel').trigger();
        });
        
        $('#amazonPrintBtn').on('click', function() {
            contactTable.button('.buttons-print').trigger();
        });
        
        $('#amazonPdfBtn').on('click', function() {
            contactTable.button('.buttons-pdf').trigger();
        });
        
        // Column Visibility - Custom Dropdown
        function buildColVisDropdown() {
            var $body = $('#colvisDropdownBody');
            $body.empty();
            
            contactTable.columns().every(function(index) {
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
            var column = contactTable.column(colIndex);
            
            column.visible(!column.visible());
            $item.toggleClass('active');
        });
        
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.colvis-dropdown-wrapper').length) {
                $('#colvisDropdown').removeClass('show');
            }
        });
        
        $('#amazonAddBtn').on('click', function() {
            let type = $('#contact_type').val() || 'customer'; 
            let url = `/contacts/create?type=${type}`;
            $.ajax({
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('.contact_modal').html(result).modal('show');
                },
                error: function(err) {
                    console.error('Modal load failed:', err);
                }
            });
        });
        
        // Apply header styles (DataTables scroll places header in separate div)
        function applyAmazonHeaderStyles() {
            var headerStyle = {
                'background': '#ffffff',
                'color': '#0F1111',
                'font-weight': '600',
                'border-bottom': '2px solid #D5D9D9',
                'border-right': '1px solid #D5D9D9'
            };
            $('#contact_table thead th, #contact_table_wrapper .dataTables_scrollHead thead th').css(headerStyle);
            $('#contact_table thead th:last-child, #contact_table_wrapper .dataTables_scrollHead thead th:last-child').css('border-right', 'none');
            $('#contact_table thead, #contact_table_wrapper .dataTables_scrollHead thead').css({
                'background': '#ffffff',
                'color': '#0F1111'
            });
        }
        
        // Sync scroll head and body column widths (body as source of truth for alignment)
        function syncScrollHeadBodyWidths() {
            var $headTh = $('#contact_table_wrapper .dataTables_scrollHead thead th');
            var $bodyRow = $('#contact_table_wrapper .dataTables_scrollBody tbody tr:first');
            var $bodyTd = $bodyRow.find('td');
            if (!$headTh.length || !$bodyTd.length || $headTh.length !== $bodyTd.length) return;
            var widths = [];
            $bodyTd.each(function(i) {
                var w = Math.max($(this).outerWidth(), 50);
                widths.push(w);
            });
            $headTh.each(function(i) {
                var w = widths[i] || 80;
                $(this).css({ width: w + 'px', minWidth: w + 'px', maxWidth: w + 'px' });
            });
            $('#contact_table_wrapper .dataTables_scrollBody tbody tr').each(function() {
                $(this).find('td').each(function(i) {
                    var w = widths[i] || 80;
                    $(this).css({ width: w + 'px', minWidth: w + 'px', maxWidth: w + 'px' });
                });
            });
        }
        // Keep header aligned when user scrolls body horizontally (single scrollbar on body)
        function bindScrollSync() {
            var $body = $('#contact_table_wrapper .dataTables_scrollBody');
            var $head = $('#contact_table_wrapper .dataTables_scrollHead');
            $body.off('scroll.contactSync').on('scroll.contactSync', function() {
                $head.scrollLeft($(this).scrollLeft());
            });
        }

        // Update custom pagination and info on DataTable draw
        contactTable.on('draw', function() {
            updateCustomPagination();
            updateTableInfo();
            applyColumnClasses();
            addTextTruncateTooltips();
            updateBulkCustomerActions();
            applyAmazonHeaderStyles();
            // Reinitialize Bootstrap dropdowns after table redraw
            initializeDropdowns();
            // Keep scroll head and body columns aligned (scrollX)
            setTimeout(function() {
                try { contactTable.columns().adjust(); } catch (e) {}
            }, 0);
            setTimeout(function() {
                try { contactTable.columns().adjust(); } catch (e) {}
                syncScrollHeadBodyWidths();
                bindScrollSync();
            }, 80);
            setTimeout(function() {
                syncScrollHeadBodyWidths();
            }, 200);
            setTimeout(function() {
                syncScrollHeadBodyWidths();
            }, 400);
        });
        
        // Initialize Bootstrap dropdowns
        function initializeDropdowns() {
            // Remove any existing event handlers to prevent duplicates
            $('#contact_table').off('click hover', '.dropdown-toggle');
            $('#contact_table').off('mouseenter mouseleave', '.btn-group.dropdown');
            
            // Initialize dropdowns using Bootstrap's dropdown plugin
            $('#contact_table .btn-group.dropdown').each(function() {
                var $dropdown = $(this);
                var $toggle = $dropdown.find('.dropdown-toggle');
                var $menu = $dropdown.find('.dropdown-menu');
                
                // Ensure menu exists and has content
                if ($menu.length === 0) {
                    return; // Skip if no menu
                }
                
                // Check if menu has content
                var menuContent = $menu.html().trim();
                if (!menuContent || menuContent === '') {
                    console.warn('Empty dropdown menu found');
                    return;
                }
                
                // Remove existing handlers
                $toggle.off('click.dropdown');
                $dropdown.off('mouseenter.dropdown mouseleave.dropdown');
                $menu.off('mouseenter mouseleave');
                
                // Click handler for dropdown toggle
                $toggle.on('click.dropdown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close other open dropdowns
                    $('#contact_table .btn-group.dropdown').not($dropdown).removeClass('open');
                    
                    // Toggle current dropdown
                    $dropdown.toggleClass('open');
                    
                    // Update aria-expanded
                    $toggle.attr('aria-expanded', $dropdown.hasClass('open'));
                });
                
                // Hover support - show on hover
                $dropdown.on('mouseenter.dropdown', function() {
                    // Close other dropdowns
                    $('#contact_table .btn-group.dropdown').not($dropdown).removeClass('open');
                    // Open this dropdown
                    $dropdown.addClass('open');
                    $toggle.attr('aria-expanded', 'true');
                });
                
                // Keep dropdown open when hovering over menu
                $menu.on('mouseenter', function() {
                    $dropdown.addClass('open');
                });
                
                // Close dropdown when mouse leaves
                $dropdown.on('mouseleave.dropdown', function() {
                    var self = this;
                    setTimeout(function() {
                        if (!$(self).is(':hover') && !$menu.is(':hover')) {
                            $dropdown.removeClass('open');
                            $toggle.attr('aria-expanded', 'false');
                        }
                    }, 100);
                });
            });
        }
        
        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#contact_table .btn-group.dropdown').length) {
                $('#contact_table .btn-group.dropdown').removeClass('open');
                $('#contact_table .dropdown-toggle').attr('aria-expanded', 'false');
            }
        });
        
        // Initialize dropdowns on page load and after table redraw
        setTimeout(function() {
            initializeDropdowns();
        }, 500);
        
        // Initial update
        updateCustomPagination();
        updateTableInfo();
        applyColumnClasses();
        updateBulkCustomerActions();
        
        // Keep header and body columns aligned on window resize (responsive)
        var resizeTimeout;
        $(window).on('resize', function() {
            if (!contactTable) return;
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                try {
                    contactTable.columns().adjust();
                    syncScrollHeadBodyWidths();
                } catch (e) {}
            }, 150);
        });
    }

    function getSelectedContactRows() {
        var selected_rows = [];
        $('#contact_table tbody input.row-select:checked').each(function() {
            selected_rows.push($(this).val());
        });
        return selected_rows;
    }

    function updateBulkCustomerActions() {
        var $actions = $('#bulk_customer_status_actions');
        if (!$actions.length) {
            return;
        }
        var selected = getSelectedContactRows();
        if (selected.length > 0) {
            $('#bulk_customer_selected_count').text(selected.length + ' selected');
            $actions.css('display', 'inline-flex');
        } else {
            $('#bulk_customer_selected_count').text('');
            $actions.hide();
        }
    }
    
    // Apply specific classes to columns for styling (header + body in sync for scrollX)
    function applyColumnClasses() {
        // With scrollX, header lives in scrollHead and body in scrollBody – use same source for both
        var $headerRow = $('#contact_table_wrapper .dataTables_scrollHead thead th').length
            ? $('#contact_table_wrapper .dataTables_scrollHead thead th')
            : $('#contact_table thead th');
        var $bodyRows = $('#contact_table_wrapper .dataTables_scrollBody tbody tr').length
            ? $('#contact_table_wrapper .dataTables_scrollBody tbody tr')
            : $('#contact_table tbody tr');

        $headerRow.each(function(index) {
            var $th = $(this);
            var headerText = $th.text().toLowerCase().trim();

            if (headerText.includes('total sale') || headerText.includes('sale due')) {
                $th.addClass('col-total-sale-due');
            }
            if (headerText === 'invoices') {
                $th.addClass('col-invoices');
            }
            if (headerText === 'brand') {
                $th.addClass('col-brand');
            }
            if (headerText === 'address' || headerText.includes('address')) {
                $th.addClass('col-address');
            }
            if (headerText === 'mobile' || headerText.includes('mobile')) {
                $th.addClass('col-mobile');
            }
            if (headerText.includes('total purchase due') && !headerText.includes('return')) {
                $th.addClass('col-purchase-due');
            }
            if (headerText.includes('total purchase return due') || (headerText.includes('purchase return') && headerText.includes('due'))) {
                $th.addClass('col-purchase-return-due');
            }
        });

        $bodyRows.each(function() {
            $(this).find('td').each(function(index) {
                var $td = $(this);
                var headerText = $headerRow.eq(index).text().toLowerCase().trim();

                if (headerText.includes('total sale') || headerText.includes('sale due')) {
                    $td.addClass('col-total-sale-due');
                }
                if (headerText === 'invoices') {
                    $td.addClass('col-invoices');
                }
                if (headerText === 'brand') {
                    $td.addClass('col-brand');
                }
                if (headerText === 'address' || headerText.includes('address')) {
                    $td.addClass('col-address');
                }
                if (headerText === 'mobile' || headerText.includes('mobile')) {
                    $td.addClass('col-mobile');
                }
                if (headerText.includes('total purchase due') && !headerText.includes('return')) {
                    $td.addClass('col-purchase-due');
                }
                if (headerText.includes('total purchase return due') || (headerText.includes('purchase return') && headerText.includes('due'))) {
                    $td.addClass('col-purchase-return-due');
                }
            });
        });
    }
    
    function updateCustomPagination() {
        if (!contactTable) return;
        
        var info = contactTable.page.info();
        var totalPages = info.pages;
        var currentPage = info.page + 1;
        
        var html = '';
        
        // Previous button
        html += '<a class="page-btn ' + (currentPage === 1 ? 'disabled' : '') + '" data-page="prev">Previous</a>';
        
        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            html += '<a class="page-btn" data-page="1">1</a>';
            if (startPage > 2) {
                html += '<span class="page-ellipsis">...</span>';
            }
        }
        
        for (var i = startPage; i <= endPage; i++) {
            html += '<a class="page-btn ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</a>';
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += '<span class="page-ellipsis">...</span>';
            }
            html += '<a class="page-btn" data-page="' + totalPages + '">' + totalPages + '</a>';
        }
        
        // Next button
        html += '<a class="page-btn ' + (currentPage === totalPages || totalPages === 0 ? 'disabled' : '') + '" data-page="next">Next</a>';
        
        $('#amazonPagination').html(html);
    }
    
    function updateTableInfo() {
        if (!contactTable) return;
        
        var info = contactTable.page.info();
        var start = info.start + 1;
        var end = info.end;
        var total = info.recordsTotal;
        
        if (total === 0) {
            start = 0;
            end = 0;
        }
        
        $('#amazonTableInfo').html('Showing <strong>' + start.toLocaleString() + '</strong> to <strong>' + end.toLocaleString() + '</strong> of <strong>' + total.toLocaleString() + '</strong> entries');
    }
    
    function addTextTruncateTooltips() {
        // Use same header/body source as applyColumnClasses (scrollHead/scrollBody when scrollX)
        var $headerCells = $('#contact_table_wrapper .dataTables_scrollHead thead th').length
            ? $('#contact_table_wrapper .dataTables_scrollHead thead th')
            : $('#contact_table thead th');
        var addressColIndex = -1;
        var actionColIndex = -1;
        $headerCells.each(function(index) {
            var headerText = $(this).text().toLowerCase().trim();
            if (headerText === 'address' || headerText.includes('address')) {
                addressColIndex = index;
            }
            if (headerText === 'action' || headerText === 'actions') {
                actionColIndex = index;
            }
        });

        var $bodyRows = $('#contact_table_wrapper .dataTables_scrollBody tbody tr').length
            ? $('#contact_table_wrapper .dataTables_scrollBody tbody tr')
            : $('#contact_table tbody tr');
        $bodyRows.each(function() {
            var $row = $(this);
            
            $row.find('td').each(function(index) {
                var $td = $(this);
                // Skip Action column - do not add title/tooltip so the Actions dropdown works correctly on hover
                if (index === actionColIndex) {
                    $td.removeAttr('title');
                    return;
                }
                var text = $td.text().trim();
                
                // For address column - keep tooltip for full text, do not force ellipsis (let CSS wrap)
                if (index === addressColIndex && text.length > 0) {
                    if (!$td.hasClass('address-processed')) {
                        $td.addClass('address-processed');
                        $td.attr('title', text);
                        $td.css('cursor', 'help');
                    }
                } else if (index !== actionColIndex && this.offsetWidth < this.scrollWidth && text.length > 20) {
                    // For other truncated columns
                    $td.attr('title', text);
                    $td.css('cursor', 'help');
                }
            });
        });
    }
    
    
    // Pagination click handler
    $(document).on('click', '#amazonPagination .page-btn:not(.disabled)', function(e) {
        e.preventDefault();
        if (!contactTable) return;
        
        var page = $(this).data('page');
        
        if (page === 'prev') {
            contactTable.page('previous').draw('page');
        } else if (page === 'next') {
            contactTable.page('next').draw('page');
        } else {
            contactTable.page(page - 1).draw('page');
        }
    });
    
    // Status tabs click handler
    $('#amazonStatusTabs .amazon-tab').on('click', function(e) {
        e.preventDefault();
        $('#amazonStatusTabs .amazon-tab').removeClass('active');
        $(this).addClass('active');
        
        var status = $(this).data('status');
        $('#contact_status_tab_filter').val(status);
        
        // Also update the hidden original tabs for compatibility
        $('#contactStatusTabs .nav-link').removeClass('active');
        $('#contactStatusTabs .nav-link[data-status="' + status + '"]').addClass('active');
        
        if (contactTable) {
            contactTable.ajax.reload();
        }
    });
    
    // Prime tier tabs click handler (filter by customer group)
    $('#amazonPrimeTierTabs .prime-tab').on('click', function(e) {
        e.preventDefault();
        var tier = $(this).data('prime-tier');
        var currentFilter = $('#prime_tier_filter').val();
        
        // Toggle: if same tier clicked again, clear filter
        if (currentFilter === tier) {
            $('#prime_tier_filter').val('');
            $('#amazonPrimeTierTabs .prime-tab').removeClass('active');
        } else {
            $('#prime_tier_filter').val(tier);
            $('#amazonPrimeTierTabs .prime-tab').removeClass('active');
            $(this).addClass('active');
        }
        
        if (contactTable) {
            contactTable.ajax.reload();
        }
    });
    
    // Table row click handler (preserve existing functionality)
    $('#contact_table tbody').on('click', 'tr', function(e) {
        if (
            $(e.target).is('input[type="checkbox"]') ||
            $(e.target).closest('button').length ||
            $(e.target).closest('a').length ||
            $(e.target).closest('.dropdown').length
        ) {
            return;
        }
        
        var href = $(this).attr('href');
        if (href) {
            window.location.href = href;
        }
    });
    
    // Location filter change
    $(document).on('change', '#location_filter', function() {
        var locationId = $(this).val();
        var brandSelect = $('#brand_filter');
        
        brandSelect.empty().append('<option value="">@lang('lang_v1.all')</option>');
        
        if (locationId) {
            $.ajax({
                url: '/get-brands-for-location/' + locationId,
                method: 'GET',
                success: function(response) {
                    if (response && response.length > 0) {
                        $.each(response, function(index, brand) {
                            brandSelect.append('<option value="' + brand.id + '">' + brand.name + '</option>');
                        });
                        brandSelect.select2({ dropdownParent: $('#filterModal') });
                    }
                }
            });
        } else {
            brandSelect.select2({ dropdownParent: $('#filterModal') });
        }
        
        if (contactTable) {
            contactTable.ajax.reload();
        }
    });
    
    // Brand filter change
    $(document).on('change', '#brand_filter', function() {
        if (contactTable) {
            contactTable.ajax.reload();
        }
    });
    
    // Initialize select2 for filters when modal is shown
    $('#filterModal').on('shown.bs.modal', function() {
        $('#location_filter, #brand_filter').select2({
            dropdownParent: $('#filterModal')
        });
    });
});
</script>

@if (!empty($api_key))
<script>
    function initAutocomplete() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -33.8688, lng: 151.2195 },
            zoom: 10,
            mapTypeId: 'roadmap'
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                map.setCenter(initialLocation);
            });
        }

        var input = document.getElementById('shipping_address');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) { return; }

            markers.forEach(function(marker) { marker.setMap(null); });
            markers = [];

            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) { return; }
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                markers.push(new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

                var lat_long = [place.geometry.location.lat(), place.geometry.location.lng()]
                $('#position').val(lat_long);

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $api_key }}&libraries=places" async defer></script>
<script type="text/javascript">
    $(document).on('shown.bs.modal', '.contact_modal', function(e) {
        initAutocomplete();
    });
</script>
@endif
@endsection

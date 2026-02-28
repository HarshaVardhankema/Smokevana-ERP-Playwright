@extends('layouts.app')
@section('title', __('sale.products'))

@section('css')
<style>
/* Amazon Theme - Product List Page */
.amazon-products-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 16px 20px;
}

/* Top banner – Amazon style */
.product-header-banner {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 20px;
    margin-bottom: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.product-header-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.product-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

.product-header-title i {
    font-size: 22px;
    color: #ffffff !important;
}

.product-header-subtitle {
    font-size: 13px;
    color: rgba(249, 250, 251, 0.88);
    margin: 0;
}

/* Page Header – Tabs row (below banner) */
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

/* Status Tabs - Amazon Style (Top Right) */
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

/* Amazon Buttons */
.amazon-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
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

/* Main Card */
.amazon-card {
    background: #FFFFFF;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid #D5D9D9;
    overflow: hidden;
}

/* Tabs - Amazon Style */
.amazon-tabs {
    display: flex;
    gap: 0;
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    padding: 0 16px;
    border-bottom: 2px solid #FF9900;
}

.amazon-tab-btn {
    padding: 12px 20px;
    font-size: 14px;
    font-weight: 500;
    color: #D5DBDB;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}

.amazon-tab-btn:hover {
    color: #FFF;
    text-decoration: none;
}

.amazon-tab-btn.active {
    color: #FF9900;
    border-bottom-color: #FF9900;
    background: rgba(255, 153, 0, 0.1);
}

/* Controls Bar */
.amazon-controls-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
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
    max-width: 600px;
    min-width: 320px;
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

/* Table Styles */
.amazon-table-wrapper {
    /* Horizontal scroll for wide tables; let vertical scroll happen at page level
       so pagination and footer controls stay visible outside the scroll area. */
    overflow-x: auto;
    overflow-y: visible;
    padding: 0;
}

/* Override DataTables default styles */
.dataTables_wrapper {
    padding: 0 !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    display: none !important;
}

.dataTables_wrapper .dt-buttons {
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

.dataTables_wrapper .dataTables_info {
    padding: 12px 20px !important;
    background: #F7F8F8;
    font-size: 13px;
    color: #565959;
}

.dataTables_wrapper .dataTables_paginate {
    padding: 12px 20px !important;
    background: #F7F8F8;
}

/* Amazon Pagination Override */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 10px;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%) !important;
    border: 1px solid #D5D9D9 !important;
    color: #0F1111 !important;
    cursor: pointer;
    transition: all 0.15s ease;
    border-radius: 0 !important;
    margin: 0 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:first-child {
    border-radius: 4px 0 0 4px !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:last-child {
    border-radius: 0 4px 4px 0 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%) !important;
    border-color: #BBBFBF !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Table Header */
#product_table thead,
#stock_report_table thead {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
}

#product_table thead th,
#stock_report_table thead th {
    padding: 10px 12px !important;
    font-weight: 600;
    color: #FFF !important;
    text-align: left;
    border: none !important;
    white-space: nowrap;
    vertical-align: middle;
    border-right: 1px solid #37475A !important;
    font-size: 13px;
}

/* Style only the visible scrollHead thead */
#product_table_wrapper .dataTables_scrollHead thead,
#stock_report_table_wrapper .dataTables_scrollHead thead {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%) !important;
}

#product_table_wrapper .dataTables_scrollHead thead th,
#stock_report_table_wrapper .dataTables_scrollHead thead th {
    padding: 10px 12px !important;
    font-weight: 600 !important;
    color: #FFF !important;
    text-align: left !important;
    border: none !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
    border-right: 1px solid #37475A !important;
    font-size: 13px !important;
    background: transparent !important;
}

#product_table_wrapper .dataTables_scrollHead thead th:last-child,
#stock_report_table_wrapper .dataTables_scrollHead thead th:last-child {
    border-right: none !important;
}

/* Hide scrollBody thead completely - it's only for column width alignment */
#product_table_wrapper .dataTables_scrollBody thead,
#stock_report_table_wrapper .dataTables_scrollBody thead {
    display: none !important;
    height: 0 !important;
    overflow: hidden !important;
    background: transparent !important;
}

#product_table_wrapper .dataTables_scrollBody thead th,
#stock_report_table_wrapper .dataTables_scrollBody thead th {
    display: none !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    background: transparent !important;
}

#product_table thead th:last-child,
#stock_report_table thead th:last-child {
    border-right: none !important;
}

/* Fix sorting icons */
#product_table thead th.sorting,
#product_table thead th.sorting_asc,
#product_table thead th.sorting_desc,
#stock_report_table thead th.sorting,
#stock_report_table thead th.sorting_asc,
#stock_report_table thead th.sorting_desc {
    position: relative;
    padding-right: 24px !important;
    cursor: pointer;
}

#product_table thead th.sorting::after,
#stock_report_table thead th.sorting::after {
    content: "⇅" !important;
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    opacity: 0.7;
}

#product_table thead th.sorting_asc::after,
#stock_report_table thead th.sorting_asc::after {
    content: "↑" !important;
    opacity: 1;
    color: #FF9900;
}

#product_table thead th.sorting_desc::after,
#stock_report_table thead th.sorting_desc::after {
    content: "↓" !important;
    opacity: 1;
    color: #FF9900;
}

/* Table Body */
#product_table tbody tr,
#stock_report_table tbody tr {
    border-bottom: 1px solid #E7E7E7;
    transition: background 0.1s ease;
}

#product_table tbody tr:hover,
#stock_report_table tbody tr:hover {
    background: #F7FAFA !important;
}

#product_table tbody tr:nth-child(even),
#stock_report_table tbody tr:nth-child(even) {
    background: #FAFAFA;
}

#product_table tbody tr:nth-child(even):hover,
#stock_report_table tbody tr:nth-child(even):hover {
    background: #F0F2F2 !important;
}

#product_table tbody td,
#stock_report_table tbody td {
    padding: 10px 12px !important;
    color: #0F1111;
    vertical-align: middle;
    font-size: 13px;
    border: none !important;
    border-right: 1px solid #E7E7E7 !important;
}

#product_table tbody td:last-child,
#stock_report_table tbody td:last-child {
    border-right: none !important;
}

/* Table Footer */
#product_table tfoot,
#stock_report_table tfoot {
    background: #F7F8F8;
}

#product_table tfoot td,
#stock_report_table tfoot td {
    padding: 12px 16px !important;
    border: none !important;
}

/* Action buttons in footer */
#product_table tfoot .tw-dw-btn,
#stock_report_table tfoot .tw-dw-btn {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
    font-weight: 500;
}

#product_table tfoot .tw-dw-btn-error {
    background: linear-gradient(to bottom, #B12704 0%, #921B05 100%);
    border-color: #7E1905;
    color: white;
}

#product_table tfoot .tw-dw-btn-success {
    background: linear-gradient(to bottom, #067D62 0%, #05654F 100%);
    border-color: #044F3E;
    color: white;
}

#product_table tfoot .tw-dw-btn-warning {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: white;
}

#product_table tfoot .tw-dw-btn-primary {
    background: linear-gradient(to bottom, #007185 0%, #006073 100%);
    border-color: #00545E;
    color: white;
}

#product_table tfoot .tw-dw-btn-neutral {
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    color: #0F1111;
}

/* Column visibility dropdown */
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

/* Filter Modal - Amazon Theme */
.modal-content {
    border-radius: 8px;
    border: none;
}

#filterModal .modal-header {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    color: white;
    border-radius: 8px 8px 0 0;
    padding: 16px 20px;
}

#filterModal .modal-header .modal-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

#filterModal .modal-header .close {
    color: white;
    opacity: 0.8;
}

#filterModal .modal-header .close:hover {
    opacity: 1;
}

#filterModal .modal-body {
    padding: 20px;
}

#filterModal .modal-footer {
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
    padding: 12px 20px;
}

#filterModal .form-control {
    border-color: #888C8C;
    border-radius: 4px;
}

#filterModal .form-control:focus {
    border-color: #FF9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
}

/* Nav tabs override */
.nav-tabs-custom {
    border: none;
    background: transparent;
    margin: 0;
    box-shadow: none;
}

.nav-tabs-custom > .nav-tabs {
    display: none !important;
}

.nav-tabs-custom > .tab-content {
    padding: 0;
    background: transparent;
}

/* Responsive */
@media (max-width: 768px) {
    .amazon-products-container {
        padding: 10px 12px;
    }
    
    .amazon-page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .amazon-status-tabs {
        width: 100%;
        justify-content: flex-start;
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
        flex-wrap: wrap;
    }
    
    .amazon-search-wrapper {
        max-width: 100%;
    }
    
    .amazon-btn-download-excel,
    .amazon-btn-add-product {
        margin-left: 0;
    }
}

/* Hide original buttons at top */
.content-header .tw-dw-btn {
    display: none !important;
}

/* Hide old tabs - we moved them to top right */
.amazon-tabs {
    display: none !important;
}

/* Product Image Styling */
#product_table td img,
.product-image-cell img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #E7E7E7;
    background: #FFF;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

/* Placeholder image replacement */
.product-image-placeholder {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(to bottom, #F7F8F8 0%, #EAEDED 100%);
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    color: #888C8C;
}

.product-image-placeholder svg {
    width: 24px;
    height: 24px;
}

/* Remove extra header rows */
#product_table thead tr:nth-child(n+2),
#stock_report_table thead tr:nth-child(n+2) {
    display: none !important;
    height: 0 !important;
    visibility: hidden !important;
}

/* ============================================
   DATATABLES SCROLL - keep head/body aligned and allow horizontal scroll
   ============================================ */

#product_table_wrapper,
#stock_report_table_wrapper {
    width: 100%;
}

#product_table_wrapper .dataTables_scroll,
#stock_report_table_wrapper .dataTables_scroll {
    overflow: hidden !important;
    width: 100%;
}

/* Scroll head: overflow hidden; scrollLeft synced via JS so header moves with horizontal scroll */
#product_table_wrapper .dataTables_scrollHead,
#stock_report_table_wrapper .dataTables_scrollHead {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
}

#product_table_wrapper .dataTables_scrollBody,
#stock_report_table_wrapper .dataTables_scrollBody {
    overflow-x: auto !important;
    overflow-y: auto !important;
}

#product_table_wrapper .dataTables_scrollHeadInner,
#stock_report_table_wrapper .dataTables_scrollHeadInner {
    box-sizing: border-box;
    position: relative !important; /* Allow transform */
    transition: transform 0s !important; /* Smooth scroll sync */
}

#product_table_wrapper .dataTables_scrollHead table,
#product_table_wrapper .dataTables_scrollBody table {
    margin: 0 !important;
    table-layout: fixed !important;
    width: 100% !important;
}

#stock_report_table_wrapper .dataTables_scrollHead table,
#stock_report_table_wrapper .dataTables_scrollBody table {
    width: 100% !important;
    table-layout: fixed !important;
    margin: 0 !important;
}

/* Ensure stock report header and body cells have matching widths */
#stock_report_table_wrapper .dataTables_scrollHead thead th,
#stock_report_table_wrapper .dataTables_scrollBody tbody td {
    box-sizing: border-box !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Allow product column to wrap properly */
#stock_report_table_wrapper .dataTables_scrollBody tbody td:nth-child(3) {
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* Default positioning – JS will toggle to fixed when needed */
#product_table_wrapper .dataTables_scrollHead {
    position: relative;
    transition: box-shadow 0.15s ease;
}
#stock_report_table_wrapper .dataTables_scrollHead {
    position: relative !important;
}

/* DataTables scroll header fix */
#product_table_wrapper .dataTables_scrollHead table thead tr:nth-child(n+2),
#stock_report_table_wrapper .dataTables_scrollHead table thead tr:nth-child(n+2) {
    display: none !important;
    height: 0 !important;
}

/* Ensure only first header row shows */
#product_table_wrapper table.dataTable thead tr:nth-child(2),
#product_table_wrapper table.dataTable thead tr:nth-child(3),
#stock_report_table_wrapper table.dataTable thead tr:nth-child(2),
#stock_report_table_wrapper table.dataTable thead tr:nth-child(3) {
    display: none !important;
    height: 0 !important;
    max-height: 0 !important;
    overflow: hidden !important;
    visibility: hidden !important;
    position: absolute !important;
    left: -9999px !important;
}

/* Keep DataTables internal sizing header inside scrollBody fully collapsed.
   Our custom header styles use !important, so we must force this too. */
#product_table_wrapper .dataTables_scrollBody thead th,
#product_table_wrapper .dataTables_scrollBody thead td,
#stock_report_table_wrapper .dataTables_scrollBody thead th,
#stock_report_table_wrapper .dataTables_scrollBody thead td {
    height: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    border-top-width: 0 !important;
    border-bottom-width: 0 !important;
    border-left-width: 0 !important;
    border-right-width: 0 !important;
    background: transparent !important;
    font-size: 0 !important;
    line-height: 0 !important;
}

/* Header action buttons (moved to controls bar) */
.amazon-btn-download-excel,
.amazon-btn-add-product {
    margin-left: 8px;
}

/* ============================================
   ENHANCED PRODUCT TABLE STYLES - AMAZON THEME
   Clean, Professional, Properly Aligned
   ============================================ */

/* General table cell alignment - consistent padding and vertical alignment */
#product_table thead th,
#product_table tbody td,
#product_table_wrapper .dataTables_scrollHead thead th,
#product_table_wrapper .dataTables_scrollBody tbody td {
    padding: 10px 12px !important;
    vertical-align: middle !important;
    box-sizing: border-box !important;
}

/* Scroll head: same padding and alignment as body so columns line up from SKU onward */
#product_table_wrapper .dataTables_scrollHead thead th {
    text-align: left !important;
    padding: 10px 12px !important;
}
#product_table_wrapper .dataTables_scrollHead th.col-checkbox,
#product_table_wrapper .dataTables_scrollHead th.col-action,
#product_table_wrapper .dataTables_scrollHead th.col-image {
    text-align: left !important;
}
#product_table_wrapper .dataTables_scrollHead th.col-sku {
    text-align: left !important;
}
/* Numeric column headers: right-aligned to match data */
#product_table_wrapper .dataTables_scrollHead th.col-purchase-price,
#product_table_wrapper .dataTables_scrollHead th.col-selling-price,
#product_table_wrapper .dataTables_scrollHead th.col-stock {
    text-align: right !important;
}
/* Allow long header labels to wrap so "Unit Purchase Price" / "Selling Price" don't clip */
#product_table_wrapper .dataTables_scrollHead th.col-purchase-price,
#product_table_wrapper .dataTables_scrollHead th.col-selling-price {
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* Explicit column widths for scroll head AND body so they stay aligned */
#product_table_wrapper .dataTables_scrollHead th.col-checkbox,
#product_table_wrapper .dataTables_scrollBody td.col-checkbox { width: 50px !important; min-width: 50px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-action,
#product_table_wrapper .dataTables_scrollBody td.col-action { width: 95px !important; min-width: 95px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-image,
#product_table_wrapper .dataTables_scrollBody td.col-image { width: 140px !important; min-width: 140px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-product,
#product_table_wrapper .dataTables_scrollBody td.col-product { width: 200px !important; min-width: 180px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-sku,
#product_table_wrapper .dataTables_scrollBody td.col-sku { width: 130px !important; min-width: 120px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-date,
#product_table_wrapper .dataTables_scrollBody td.col-date { width: 150px !important; min-width: 140px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-purchase-price,
#product_table_wrapper .dataTables_scrollBody td.col-purchase-price { width: 150px !important; min-width: 140px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-selling-price,
#product_table_wrapper .dataTables_scrollBody td.col-selling-price { width: 120px !important; min-width: 110px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-stock,
#product_table_wrapper .dataTables_scrollBody td.col-stock { width: 110px !important; min-width: 100px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-type,
#product_table_wrapper .dataTables_scrollBody td.col-type { width: 130px !important; min-width: 100px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-fulfillment,
#product_table_wrapper .dataTables_scrollBody td.col-fulfillment { width: 130px !important; min-width: 110px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-category,
#product_table_wrapper .dataTables_scrollBody td.col-category { width: 180px !important; min-width: 140px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-brand,
#product_table_wrapper .dataTables_scrollBody td.col-brand { width: 120px !important; min-width: 100px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-tier-price,
#product_table_wrapper .dataTables_scrollBody td.col-tier-price { width: 100px !important; min-width: 90px !important; }
#product_table_wrapper .dataTables_scrollHead th.col-location,
#product_table_wrapper .dataTables_scrollBody td.col-location { width: 120px !important; min-width: 100px !important; }

/* Numeric columns: headers left-aligned, data right-aligned (Unit Purchase Price, Selling Price, Current Stock) */
#product_table th.col-purchase-price,
#product_table th.col-selling-price,
#product_table th.col-stock,
#product_table_wrapper .dataTables_scrollHead th.col-purchase-price,
#product_table_wrapper .dataTables_scrollHead th.col-selling-price,
#product_table_wrapper .dataTables_scrollHead th.col-stock {
    text-align: left !important;
    padding: 5x 12px !important;
}
#product_table td.col-purchase-price,
#product_table td.col-selling-price,
#product_table td.col-stock,
#product_table_wrapper .dataTables_scrollBody td.col-purchase-price,
#product_table_wrapper .dataTables_scrollBody td.col-selling-price,
#product_table_wrapper .dataTables_scrollBody td.col-stock {
    text-align: right !important;
    padding: 10px 12px !important;
}
/* Numeric column headers: left-aligned */
#product_table thead th.col-purchase-price,
#product_table thead th.col-selling-price,
#product_table thead th.col-stock {
    text-align: left !important;
}

/* Text columns: left-aligned (Created At, Product Type, Fulfillment, Category, Brand) */
#product_table_wrapper .dataTables_scrollBody td.col-date,
#product_table_wrapper .dataTables_scrollBody td.col-type,
#product_table_wrapper .dataTables_scrollBody td.col-fulfillment,
#product_table_wrapper .dataTables_scrollBody td.col-category,
#product_table_wrapper .dataTables_scrollBody td.col-brand {
    text-align: left !important;
    padding: 10px 12px !important;
}

/* SKU column: left-aligned (header and body, main table and scroll) */
#product_table th.col-sku,
#product_table td.col-sku,
#product_table_wrapper .dataTables_scrollHead th.col-sku,
#product_table_wrapper .dataTables_scrollBody td.col-sku {
    text-align: left !important;
    padding: 10px 12px !important;
}
/* SKU card: left-aligned in cell */
#product_table_wrapper .dataTables_scrollBody td.col-sku .sku-card,
#product_table td.col-sku .sku-card {
    display: inline-block;
    text-align: left;
}

/* Table Container - Both header and body scroll together */
#product_table_wrapper {
    width: 100%;
    overflow-x: auto;
    overflow-y: visible;
}

/* Base Table Styling - Fixed layout for consistent column widths */
#product_table {
    border-collapse: collapse !important;
    border-spacing: 0 !important;
    width: 100% !important;
    min-width: 100% !important;
    font-size: 13px;
    table-layout: fixed !important;
}
#product_table_wrapper .dataTables_scrollBody #product_table {
    table-layout: fixed !important;
}

/* Table Header Styling - Sticky header */
#product_table thead th {
    background: linear-gradient(to bottom, #F7F8F8 0%, #E7E9EC 100%) !important;
    border-bottom: 2px solid #E77600 !important;
    border-right: 1px solid #D5D9D9 !important;
    color: #0F1111 !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
    padding: 12px 10px !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    text-align: left !important;
}
/* SKU header: left-aligned */
#product_table thead th.col-sku {
    text-align: left !important;
}

#product_table_wrapper .dataTables_scrollHead.is-sticky {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    border-bottom: 2px solid #E77600;
}

#product_table thead th:last-child {
    border-right: none !important;
}

/* Table Body Styling */
#product_table tbody td {
    background: #FFF !important;
    border-bottom: 1px solid #E7E7E7 !important;
    border-right: 1px solid #F0F0F0 !important;
    padding: 10px !important;
    vertical-align: middle !important;
    color: #0F1111;
    font-size: 13px;
    box-sizing: border-box;
}
/* Product column: long names wrap (no clipping), stay within column - prevent overflow */
#product_table td.col-product,
#product_table th.col-product,
#product_table_wrapper .dataTables_scrollHead th.col-product,
#product_table_wrapper .dataTables_scrollBody td.col-product {
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    overflow: hidden !important;
    text-overflow: clip !important;
    padding: 10px 12px !important;
    vertical-align: middle !important;
    max-width: 200px !important;
    width: 200px !important;
    box-sizing: border-box !important;
    hyphens: auto;
}
/* Other text columns wrap */
#product_table td.col-sku,
#product_table td.col-category,
#product_table td.col-brand {
    overflow-wrap: break-word;
    word-wrap: break-word;
}

#product_table tbody td:last-child {
    border-right: none !important;
}

/* Hide empty rows/dataTables empty state row and first empty line */
#product_table tbody tr.dataTables_empty,
#product_table tbody tr.odd:empty,
#product_table tbody tr.even:empty,
#product_table tbody tr:first-child:empty,
#product_table tbody tr:first-child.dataTables_empty {
    display: none !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Remove padding/margin from first data row to eliminate empty line */
#product_table tbody tr:first-child:not(.dataTables_empty) td {
    padding-top: 10px !important;
}

/* Hide DataTables empty message row */
#product_table tbody .dataTables_empty {
    display: none !important;
}

/* Row Hover Effect */
#product_table tbody tr:hover td {
    background: #F7FAFA !important;
}

/* Row Selection Highlight */
#product_table tbody tr.selected td {
    background: #FFF8E7 !important;
    box-shadow: inset 3px 0 0 #FF9900;
}

/* ============================================
   COLUMN-SPECIFIC WIDTHS & ALIGNMENT
   ============================================ */

/* Column 1: Checkbox */
#product_table thead th:nth-child(1),
#product_table tbody td:nth-child(1) {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    text-align: center !important;
    padding: 8px 5px !important;
}

/* Column 2: Action */
#product_table thead th:nth-child(2) {
    width: 150px !important;
    min-width: 150px !important;
    max-width: 150px !important;
    text-align: left !important;
}
#product_table tbody td:nth-child(2) {
    width: 120px !important;
    min-width: 120px !important;
    max-width: 120px !important;
    text-align: left !important;
}

/* Column 3: Default Product Image */
#product_table thead th:nth-child(3),
#product_table tbody td:nth-child(3) {
    min-width: 180px !important;
    width: 180px !important;
    text-align: left !important;
}

#product_table thead th:nth-child(3),
#product_table_wrapper .dataTables_scrollHead th:nth-child(3) {
    white-space: nowrap !important;
    word-break: normal !important;
    writing-mode: horizontal-tb !important;
    overflow-wrap: normal !important;
}

/* Column 4: Product Name - left-aligned, wrap content */
#product_table thead th:nth-child(4),
#product_table tbody td:nth-child(4),
#product_table th.col-product,
#product_table td.col-product {
    min-width: 180px !important;
    text-align: left !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    white-space: normal !important;
}

/* Column 5: SKU - left-aligned, wrap if needed */
#product_table thead th:nth-child(5),
#product_table tbody td:nth-child(5),
#product_table th.col-sku,
#product_table td.col-sku {
    width: 120px !important;
    min-width: 100px !important;
    max-width: 160px !important;
    text-align: left !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* Column 6: Created At */
#product_table thead th:nth-child(6),
#product_table tbody td:nth-child(6),
#product_table th.col-date,
#product_table td.col-date {
    width: 140px !important;
    min-width: 120px !important;
    text-align: left !important;
}

/* Price columns - headers left-aligned, data right-aligned for numeric values */
#product_table th.col-purchase-price,
#product_table th.col-selling-price,
#product_table th.col-tier-price,
#product_table_wrapper .dataTables_scrollHead th.col-purchase-price,
#product_table_wrapper .dataTables_scrollHead th.col-selling-price {
    text-align: left !important;
    padding: 10px 12px !important;
    white-space: nowrap !important;
}
#product_table td.col-purchase-price,
#product_table td.col-selling-price,
#product_table td.col-tier-price,
#product_table_wrapper .dataTables_scrollBody td.col-purchase-price,
#product_table_wrapper .dataTables_scrollBody td.col-selling-price {
    text-align: right !important;
    padding: 10px 12px !important;
    white-space: nowrap !important;
}

/* Stock column - right-aligned for numeric values */
#product_table th.col-stock,
#product_table td.col-stock,
#product_table_wrapper .dataTables_scrollHead th.col-stock,
#product_table_wrapper .dataTables_scrollBody td.col-stock {
    text-align: right !important;
    padding: 10px 12px !important;
    white-space: nowrap !important;
}

/* Product Type column - left-aligned */
#product_table th.col-type,
#product_table td.col-type,
#product_table_wrapper .dataTables_scrollHead th.col-type,
#product_table_wrapper .dataTables_scrollBody td.col-type {
    width: 130px !important;
    min-width: 100px !important;
    text-align: left !important;
    padding: 10px 12px !important;
}

/* ============================================
   CHECKBOX STYLING
   ============================================ */

.checkbox-cell-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

#product_table input[type="checkbox"] {
    width: 16px;
    height: 16px;
    min-width: 16px;
    accent-color: #FF9900;
    cursor: pointer;
    border-radius: 3px;
    margin: 0;
    flex-shrink: 0;
}

#product_table input[type="checkbox"]:hover {
    transform: scale(1.1);
}

/* ============================================
   EXPAND/COLLAPSE ARROW
   ============================================ */

.expand-arrow {
    display: none;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    min-width: 18px;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.expand-arrow:hover {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: white;
}

.expand-arrow svg {
    width: 10px;
    height: 10px;
    transition: transform 0.15s ease;
}

.expand-arrow.expanded svg {
    transform: rotate(90deg);
}

.expand-arrow.expanded {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: white;
}

#product_table tbody tr.has-variants .expand-arrow {
    display: inline-flex !important;
}

/* ============================================
   HIDE DEFAULT DATATABLES EXPAND ICONS
   ============================================ */

#product_table tbody td.details-control::before,
#product_table tbody td.details-control::after,
#product_table tbody tr td:first-child::before,
#product_table tbody tr td:first-child::after,
.dataTable tbody td.dt-control::before,
.dataTable tbody td.dt-control::after,
.dataTable tbody td.details-control::before,
.dataTable tbody td.details-control::after,
table.dataTable td.dt-control::before,
table.dataTable td.dt-control::after,
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before,
#product_table.dtr-inline.collapsed > tbody > tr > td:first-child::before {
    content: none !important;
    display: none !important;
    background: none !important;
    background-image: none !important;
    box-shadow: none !important;
    width: 0 !important;
    height: 0 !important;
}

table.dataTable tbody td.details-control,
table.dataTable tbody td.dt-control,
#product_table td.dt-control,
#product_table td.details-control {
    background-image: none !important;
    background: transparent !important;
    cursor: default !important;
}

#product_table tbody td:first-child i.fa-plus,
#product_table tbody td:first-child i.fa-minus,
#product_table tbody td:first-child .fa-plus,
#product_table tbody td:first-child .fa-minus,
#product_table tbody td:first-child i[class*="plus"],
#product_table tbody td:first-child i[class*="minus"] {
    display: none !important;
}

/* In the first column, always show our custom wrapper (which includes checkbox + expand arrow) */
#product_table tbody tr:not(.child) > td:first-child > *:not(.checkbox-cell-wrapper) {
    display: none !important;
}

#product_table tbody tr > td:first-child .checkbox-cell-wrapper {
    display: inline-flex !important;
}

/* Make sure the expand arrow inside the wrapper is visible */
#product_table tbody tr.has-variants .checkbox-cell-wrapper .expand-arrow {
    display: inline-flex !important;
}

#product_table tr.has-variants td.details-control .expand-arrow {
    cursor: pointer !important;
}

/* ============================================
   COLUMN CLASS DEFINITIONS
   ============================================ */

/* Checkbox Column */
#product_table th.col-checkbox,
#product_table thead th:first-child {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    text-align: left !important;
    padding: 8px 5px !important;
}
#product_table td.col-checkbox,
#product_table tbody td:first-child {
    width: 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    text-align: center !important;
    padding: 8px 5px !important;
}

/* Action Column - pointer cursor on cell and all buttons/dropdowns */
#product_table th.col-action,
#product_table td.col-action,
#product_table_wrapper .dataTables_scrollBody td.col-action {
    width: 95px !important;
    min-width: 95px !important;
    max-width: 95px !important;
    text-align: center !important;
    cursor: default;
}
#product_table td.col-action a,
#product_table td.col-action button,
#product_table td.col-action .btn,
#product_table td.col-action .dropdown-toggle,
#product_table td.col-action [role="button"],
#product_table_wrapper .dataTables_scrollBody td.col-action a,
#product_table_wrapper .dataTables_scrollBody td.col-action button,
#product_table_wrapper .dataTables_scrollBody td.col-action .btn,
#product_table_wrapper .dataTables_scrollBody td.col-action .dropdown-toggle,
#product_table_wrapper .dataTables_scrollBody td.col-action [role="button"] {
    cursor: pointer !important;
}

/* Image Column */
#product_table th.col-image {
    min-width: 140px !important;
    width: 140px !important;
    text-align: left !important;
}
#product_table td.col-image {
    min-width: 140px !important;
    width: 140px !important;
    text-align: center !important;
}

/* Image column - center content, vertical align with text in other columns */
#product_table th.col-image,
#product_table td.col-image,
#product_table_wrapper .dataTables_scrollHead th.col-image {
    white-space: nowrap !important;
    word-break: normal !important;
    writing-mode: horizontal-tb !important;
    overflow-wrap: normal !important;
    text-align: center !important;
    vertical-align: middle !important;
}
#product_table td.col-image {
    vertical-align: middle !important;
}
#product_table td.col-image img,
#product_table td.col-image .product-image-placeholder {
    vertical-align: middle !important;
    display: inline-block !important;
}

/* Product Name Column - left-aligned, wrap content */
#product_table th.col-product,
#product_table td.col-product {
    min-width: 180px !important;
    text-align: left !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    white-space: normal !important;
}

/* SKU Column - left-aligned */
#product_table th.col-sku,
#product_table td.col-sku {
    width: 120px !important;
    min-width: 100px !important;
    max-width: 160px !important;
    text-align: left !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* Date Column */
#product_table th.col-date,
#product_table td.col-date {
    width: 140px !important;
    min-width: 120px !important;
    text-align: left !important;
}

/* Price Columns - left-aligned for proper header alignment */
#product_table th.col-price,
#product_table td.col-price,
#product_table th.col-purchase-price,
#product_table td.col-purchase-price,
#product_table th.col-selling-price,
#product_table td.col-selling-price {
    width: 110px !important;
    min-width: 90px !important;
    text-align: left !important;
    white-space: nowrap !important;
    padding: 10px 12px !important;
}

/* Stock Column - left-aligned for proper header alignment */
#product_table th.col-stock,
#product_table td.col-stock {
    width: 100px !important;
    min-width: 90px !important;
    text-align: left !important;
    white-space: nowrap !important;
    padding: 10px 12px !important;
}

/* Product Type Column */
#product_table th.col-type,
#product_table td.col-type,
#product_table_wrapper .dataTables_scrollHead th.col-type,
#product_table_wrapper .dataTables_scrollBody td.col-type {
    width: 120px !important;
    min-width: 100px !important;
    max-width: 180px !important;
    text-align: left !important;
    padding: 10px 12px !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    white-space: normal !important;
}

/* Fulfillment Column - ensure full text is visible */
#product_table th.col-fulfillment,
#product_table td.col-fulfillment,
#product_table_wrapper .dataTables_scrollHead th.col-fulfillment,
#product_table_wrapper .dataTables_scrollBody td.col-fulfillment {
    width: 130px !important;
    min-width: 110px !important;
    max-width: 180px !important;
    text-align: left !important;
    padding: 10px 12px !important;
    white-space: normal !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    overflow: visible !important;
}

/* Category Column - wrap long text so it doesn't overflow */
#product_table th.col-category,
#product_table td.col-category,
#product_table_wrapper .dataTables_scrollHead th.col-category,
#product_table_wrapper .dataTables_scrollBody td.col-category {
    width: 160px !important;
    min-width: 120px !important;
    max-width: 220px !important;
    text-align: left !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    white-space: normal !important;
}

/* Brand Column - wrap long text */
#product_table th.col-brand,
#product_table td.col-brand,
#product_table_wrapper .dataTables_scrollHead th.col-brand,
#product_table_wrapper .dataTables_scrollBody td.col-brand {
    width: 110px !important;
    min-width: 90px !important;
    max-width: 160px !important;
    text-align: left !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    white-space: normal !important;
}

/* Hidden Columns */
#product_table th.col-hidden,
#product_table td.col-hidden {
    display: none !important;
}

/* ============================================
   PRODUCT IMAGE STYLING
   ============================================ */

#product_table td img,
.product-thumbnail-small {
    width: 50px !important;
    height: 50px !important;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #E7E7E7;
    background: #FFF;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: block;
    margin: 0 auto;
}

#product_table td img:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transition: all 0.2s ease;
}

/* Image placeholder styling */
.product-image-placeholder {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(to bottom, #F7F8F8 0%, #EAEDED 100%);
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    color: #888C8C;
    margin: 0 auto;
}

/* ============================================
   ACTION BUTTON STYLING
   ============================================ */

#product_table .btn-group .btn,
#product_table .dropdown-toggle {
    background: var(--amazon-info, #007185) !important;
    border: 1px solid var(--amazon-info, #007185) !important;
    color: #FFFFFF !important;
    font-size: 12px;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 4px;
    transition: all 0.15s ease;
}

#product_table .btn-group .btn:hover,
#product_table .dropdown-toggle:hover,
#product_table .btn-group.open .dropdown-toggle {
    background: #005a6b !important;
    border-color: #005a6b !important;
    color: #FFFFFF !important;
}

#product_table .btn-group .btn:active,
#product_table .dropdown-toggle:active {
    background: #004352 !important;
    border-color: #004352 !important;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

/* ============================================
   AMAZON CHECKBOX WRAPPER FOR HEADER
   ============================================ */

.amazon-checkbox-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 0;
}

.amazon-checkbox-wrapper input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #FF9900;
    cursor: pointer;
}

.amazon-checkbox-mark {
    display: none;
}

/* Variant Row Styling - DataTables child row (expanded variants block) */
#product_table tbody tr.child {
    display: table-row !important;
}

#product_table tbody tr.child td {
    display: table-cell !important;
}

#product_table tbody tr.child td {
    padding: 0 !important;
    background: #fff !important;
    border-top: none !important;
    height: auto !important;
    overflow: visible !important;
}

/* Ensure child row content in first cell is visible */
#product_table tbody tr.child td:first-child > * {
    display: block !important;
}

#product_table tbody tr.child .variant-container {
    display: block;
}

/* Variant Container */
.variant-container {
    padding: 16px 20px;
    background: linear-gradient(to bottom, #FAFAFA 0%, #F5F5F5 100%);
    border-top: 1px solid #E7E7E7;
    border-bottom: 1px solid #E7E7E7;
}

.variant-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #FF9900;
}

.variant-header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #232F3E;
    display: flex;
    align-items: center;
    gap: 8px;
}

.variant-header h4 svg {
    color: #FF9900;
}

.variant-count-badge {
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    color: #FFF;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 500;
}

/* Variant Grid */
.variant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 12px;
}

/* Variant Card */
.variant-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #FFF;
    border: 1px solid #E7E7E7;
    border-radius: 8px;
    padding: 12px;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.variant-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--variant-accent, #FF9900);
}

.variant-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #C7511F;
    transform: translateY(-2px);
}

/* Variant accent colors */
.variant-card:nth-child(6n+1) { --variant-accent: #FF9900; }
.variant-card:nth-child(6n+2) { --variant-accent: #007185; }
.variant-card:nth-child(6n+3) { --variant-accent: #067D62; }
.variant-card:nth-child(6n+4) { --variant-accent: #C7511F; }
.variant-card:nth-child(6n+5) { --variant-accent: #232F3E; }
.variant-card:nth-child(6n+6) { --variant-accent: #B12704; }

.variant-image {
    width: 60px;
    height: 60px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #E7E7E7;
    background: #F7F8F8;
    flex-shrink: 0;
}

.variant-image-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 6px;
    background: linear-gradient(to bottom, #F7F8F8 0%, #EAEDED 100%);
    border: 1px solid #D5D9D9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888C8C;
    flex-shrink: 0;
}

.variant-image-placeholder svg {
    width: 24px;
    height: 24px;
}

.variant-info {
    flex: 1;
    min-width: 0;
}

.variant-name {
    font-size: 13px;
    font-weight: 600;
    color: #0F1111;
    margin-bottom: 6px;
    line-height: 1.3;
}

.variant-attributes {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
}

.variant-attribute {
    font-size: 11px;
    padding: 2px 8px;
    background: #F0F2F2;
    border-radius: 4px;
    color: #565959;
}

.variant-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

/* SKU Badge - for variant cards */
.sku-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(to bottom, #232F3E 0%, #1A252F 100%);
    color: #FFF;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 500;
    font-family: monospace;
}

/* SKU Card Style - for main table column */
.sku-card {
    display: inline-block;
    background: #F7F8F8;
    border: 1px solid #E7E7E7;
    border-radius: 6px;
    padding: 6px 12px;
    font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
    font-size: 12px;
    font-weight: 500;
    color: #0F1111;
    letter-spacing: 0.3px;
    transition: all 0.15s ease;
    cursor: default;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.sku-card:hover {
    background: #EAEDED;
    border-color: #D5D9D9;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

/* SKU card with icon */
.sku-card-wrapper {
    display: flex;
    align-items: center;
    gap: 6px;
}

.sku-card-icon {
    width: 14px;
    height: 14px;
    color: #888C8C;
}

/* Alternative SKU pill style */
.sku-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 20px;
    padding: 5px 14px;
    font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
    font-size: 12px;
    font-weight: 500;
    color: #0F1111;
    transition: all 0.15s ease;
}

.sku-pill::before {
    content: '#';
    color: #FF9900;
    font-weight: 700;
}

.sku-pill:hover {
    border-color: #FF9900;
    background: #FFFCF5;
}

/* Product Type - Clean text display */
.product-type-text {
    font-size: 13px;
    font-weight: 500;
    color: #0F1111;
    text-transform: capitalize;
}

.product-type-text.single {
    color: #565959;
}

.product-type-text.variable {
    color: #007185;
}

/* Stock Status Icons */
.stock-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
}

.stock-status .stock-value {
    color: #0F1111;
}

.stock-status .stock-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.stock-status .stock-icon svg {
    width: 16px;
    height: 16px;
}

/* Stock OK - No icon needed */
.stock-status.stock-ok .stock-value {
    color: #067D62;
}

/* Stock Warning - Yellow icon for low stock */
.stock-status.stock-low .stock-icon {
    color: #C7511F;
}

.stock-status.stock-low .stock-value {
    color: #C7511F;
}

/* Stock Critical - Red icon for zero stock */
.stock-status.stock-zero .stock-icon {
    color: #B12704;
}

.stock-status.stock-zero .stock-value {
    color: #B12704;
    font-weight: 600;
}

/* Category Badge */
.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(to bottom, #007185 0%, #006073 100%);
    color: #FFF;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 500;
}

/* Price Tag */
.price-tag {
    font-size: 14px;
    font-weight: 700;
    color: #B12704;
}

/* Stock Badge */
.stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.stock-badge.in-stock {
    background: #E7F4E4;
    color: #067D62;
}

.stock-badge.low-stock {
    background: #FFF4E5;
    color: #C7511F;
}

.stock-badge.out-of-stock {
    background: #FFE6E6;
    color: #B12704;
}

.stock-badge svg {
    width: 14px;
    height: 14px;
}

/* Zero Stock Warning in main table */
.stock-warning {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #B12704;
    font-weight: 600;
}

.stock-warning svg {
    width: 16px;
    height: 16px;
    color: #B12704;
}

/* Action Buttons Enhanced */
#product_table .btn-group .btn,
#product_table .tw-dropdown .tw-dw-btn {
    transition: all 0.2s ease;
}

#product_table .btn-group .btn:hover,
#product_table .tw-dropdown .tw-dw-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

#product_table .dropdown-menu a {
    padding: 8px 16px;
    font-size: 13px;
    transition: all 0.15s ease;
}

#product_table .dropdown-menu a:hover {
    background: #FFF8E7;
    color: #C7511F;
}

/* Bulk Actions Dropdown */
.bulk-actions-dropdown {
    position: relative;
}

.bulk-actions-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.bulk-actions-btn:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
}

.bulk-actions-btn svg {
    width: 14px;
    height: 14px;
}

.bulk-actions-menu {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 4px;
    background: #FFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    min-width: 200px;
    z-index: 1000;
    display: none;
    padding: 8px 0;
}

.bulk-actions-menu.show {
    display: block;
}

.bulk-actions-menu .bulk-action-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    font-size: 13px;
    color: #0F1111;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

.bulk-actions-menu .bulk-action-item:hover {
    background: #F7FAFA;
}

.bulk-actions-menu .bulk-action-item.danger:hover {
    background: #FFE6E6;
    color: #B12704;
}

.bulk-actions-menu .bulk-action-item.success:hover {
    background: #E7F4E4;
    color: #067D62;
}

.bulk-actions-menu .bulk-action-item.warning:hover {
    background: #FFF4E5;
    color: #C7511F;
}

.bulk-actions-menu .divider {
    height: 1px;
    background: #E7E7E7;
    margin: 8px 0;
}

/* Modern Pagination */
.amazon-pagination-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #F7F8F8;
    border-top: 1px solid #E7E7E7;
}

.amazon-pagination-info {
    font-size: 13px;
    color: #565959;
}

.amazon-pagination-info strong {
    color: #0F1111;
}

.amazon-pagination {
    display: flex;
    align-items: center;
    gap: 2px;
}

.amazon-pagination .page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    font-size: 14px;
    font-weight: 500;
    background: #FFF;
    border: 1px solid #D5D9D9;
    color: #0F1111;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.amazon-pagination .page-btn:first-child {
    border-radius: 6px 0 0 6px;
}

.amazon-pagination .page-btn:last-child {
    border-radius: 0 6px 6px 0;
}

.amazon-pagination .page-btn:hover:not(.active):not(.disabled) {
    background: #F7FAFA;
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
    pointer-events: none;
}

.amazon-pagination .page-ellipsis {
    padding: 0 8px;
    color: #565959;
}

/* Hide table footer (moved bulk actions to dropdown) and any clone in scroll body */
#product_table tfoot,
#product_table_wrapper .dataTables_scrollBody #product_table tfoot {
    display: none !important;
}

/* Expand All Button */
.expand-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.expand-all-btn:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
}

.expand-all-btn.expanded {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: white;
}

.expand-all-btn svg {
    width: 14px;
    height: 14px;
    transition: transform 0.2s ease;
}

.expand-all-btn.expanded svg {
    transform: rotate(90deg);
}

/* ============================================
   AMAZON-STYLE CHECKBOX
   ============================================ */
.amazon-checkbox-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
}

.amazon-checkbox-wrapper input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.amazon-checkbox-mark {
    width: 18px;
    height: 18px;
    background: #FFF;
    border: 2px solid #888C8C;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
}

.amazon-checkbox-wrapper:hover .amazon-checkbox-mark {
    border-color: #FF9900;
}

.amazon-checkbox-wrapper input[type="checkbox"]:checked + .amazon-checkbox-mark {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
}

.amazon-checkbox-wrapper input[type="checkbox"]:checked + .amazon-checkbox-mark::after {
    content: '';
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    margin-bottom: 2px;
}

/* ============================================
   ENHANCED TABLE ROW STYLING
   ============================================ */
#product_table tbody tr {
    transition: all 0.15s ease;
}

#product_table tbody tr:hover {
    background: #F7FAFA !important;
    box-shadow: inset 0 0 0 1px #E7E7E7;
}

#product_table tbody tr.selected {
    background: #FFF8E7 !important;
    box-shadow: inset 3px 0 0 #FF9900;
}

#product_table tbody tr.details {
    background: #FFFCF5 !important;
    box-shadow: inset 3px 0 0 #FF9900;
}

/* ============================================
   HIDE DEFAULT DATATABLES PAGINATION
   ============================================ */
#product_table_wrapper .dataTables_info,
#product_table_wrapper .dataTables_paginate {
    display: none !important;
}

/* ============================================
   SPIN ANIMATION FOR LOADING
   ============================================ */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ============================================
   ACTION DROPDOWN ENHANCED
   ============================================ */
#product_table .tw-dropdown {
    position: relative;
}

#product_table .tw-dropdown .tw-dw-btn {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: linear-gradient(to bottom, #FFF 0%, #F7F8F8 100%);
    border: 1px solid #D5D9D9;
    color: #0F1111;
    transition: all 0.15s ease;
}

#product_table .tw-dropdown .tw-dw-btn:hover {
    background: linear-gradient(to bottom, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

#product_table .tw-dropdown-content {
    background: #FFF;
    border: 1px solid #D5D9D9;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    overflow: hidden;
    min-width: 160px;
}

#product_table .tw-dropdown-content li a,
#product_table .tw-dropdown-content li button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    font-size: 13px;
    color: #0F1111;
    transition: all 0.15s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

#product_table .tw-dropdown-content li a:hover,
#product_table .tw-dropdown-content li button:hover {
    background: #F7FAFA;
    color: #C7511F;
}

#product_table .tw-dropdown-content li a.delete-product:hover {
    background: #FFE6E6;
    color: #B12704;
}

/* ============================================
   PRODUCT IMAGE IN TABLE
   ============================================ */
#product_table td img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #E7E7E7;
    background: #F7F8F8;
    transition: all 0.2s ease;
}

#product_table td img:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 10;
    position: relative;
}

/* ============================================
   PRODUCT TYPE BADGES
   ============================================ */
.product-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 12px;
    text-transform: uppercase;
}

.product-type-badge.single {
    background: #E7F4E4;
    color: #067D62;
}

.product-type-badge.variable {
    background: #FFF4E5;
    color: #C7511F;
}

.product-type-badge.combo {
    background: #E6F2FF;
    color: #007185;
}

/* ============================================
   SEARCH BAR WIDTH INCREASE
   ============================================ */
.amazon-search-wrapper {
    max-width: 550px !important;
    min-width: 300px !important;
}

/* ============================================
   PRINT: Only product data (no actions, sidebar, etc.)
   ============================================ */
@media print {
    /* Hide everything except the main content and product table */
    body * { visibility: hidden; }
    .amazon-products-container,
    .amazon-products-container * { visibility: visible; }
    .amazon-products-container { position: absolute; left: 0; top: 0; width: 100%; background: #fff; padding: 0; }

    /* CRITICAL: Re-hide Action and Checkbox columns AFTER making container visible (otherwise they show again) */
    .amazon-products-container .col-action,
    .amazon-products-container .col-checkbox,
    .amazon-products-container th.col-action, .amazon-products-container td.col-action,
    .amazon-products-container th.col-checkbox, .amazon-products-container td.col-checkbox,
    .amazon-products-container thead th:nth-child(1), .amazon-products-container thead th:nth-child(2),
    .amazon-products-container tbody td:nth-child(1), .amazon-products-container tbody td:nth-child(2) {
        visibility: hidden !important; display: none !important;
        width: 0 !important; min-width: 0 !important; max-width: 0 !important;
        padding: 0 !important; margin: 0 !important; overflow: hidden !important;
        font-size: 0 !important; line-height: 0 !important; border: none !important;
    }

    /* Hide sidebar, header, navbar, scrolltop */
    aside, .main-sidebar, [class*="sidebar"], .navbar, header, .content-header, .no-print,
    .scrolltop, .tw-dw-btn, .btn, .paginate_button, .dataTables_length, .dataTables_filter,
    .dataTables_paginate, .dataTables_info, .dataTables_processing, .dt-buttons { display: none !important; visibility: hidden !important; }

    /* Hide product page UI: banner tabs, add product button, export/filter area */
    .product-header-banner .amazon-status-tabs,
    .product-header-banner .tw-dw-btn, .product-header-banner .btn,
    .amazon-page-header .tw-dw-btn, .amazon-page-header .btn,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_paginate,
    .dataTables_wrapper .dataTables_info,
    #product_table_wrapper .dataTables_scrollHead .dataTables_scrollHeadInner table thead tr:not(:first-child),
    #product_table_wrapper .dataTables_scrollBody table tfoot { display: none !important; }

    /* Hide Action and Checkbox columns (all possible selectors) */
    #product_table thead th.col-action, #product_table thead th.col-checkbox,
    #product_table tbody td.col-action, #product_table tbody td.col-checkbox,
    #product_table_wrapper .dataTables_scrollHead th.col-action, #product_table_wrapper .dataTables_scrollHead th.col-checkbox,
    #product_table_wrapper .dataTables_scrollBody td.col-action, #product_table_wrapper .dataTables_scrollBody td.col-checkbox,
    #product_table_wrapper .dataTables_scrollHead thead th:nth-child(1), #product_table_wrapper .dataTables_scrollHead thead th:nth-child(2),
    #product_table_wrapper .dataTables_scrollBody tbody td:nth-child(1), #product_table_wrapper .dataTables_scrollBody tbody td:nth-child(2) {
        display: none !important; visibility: hidden !important;
        width: 0 !important; min-width: 0 !important; max-width: 0 !important;
        padding: 0 !important; margin: 0 !important; overflow: hidden !important;
        font-size: 0 !important; line-height: 0 !important;
    }

    /* Show only the product table area and simplify header for print */
    .product-header-banner { background: #37475a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; margin-bottom: 12px; }
    .product-header-title, .product-header-subtitle { color: #fff !important; }
    #product_table_wrapper { overflow: visible !important; }
    #product_table, #product_table_wrapper .dataTables_scrollBody table { width: 100% !important; }
    #product_table thead th, #product_table tbody td { border: 1px solid #ddd !important; padding: 6px 8px !important; }
    #product_table thead { background: #232f3e !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #product_table thead th { color: #fff !important; }
}
</style>
@endsection

@section('content')
<div class="amazon-products-container">
    <!-- Amazon-style banner -->
    <div class="product-header-banner">
        <div class="product-header-content">
            <h1 class="product-header-title">
                <i class="fas fa-cube"></i>
                @lang('sale.products')
            </h1>
            <p class="product-header-subtitle">
                Manage your product catalog. View all products or check stock levels.
            </p>
        </div>
        <!-- Status Tabs in banner -->
        <div class="amazon-status-tabs">
            <a href="#product_list_tab" class="amazon-status-tab active" data-tab="products" data-toggle="tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
                @lang('lang_v1.all_products')
            </a>
            @can('stock_report.view')
            <a href="#product_stock_report" class="amazon-status-tab product_stock_report" data-tab="stock" data-toggle="tab">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                @lang('report.stock_report')
            </a>
            @endcan
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                    <h4 class="modal-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 8px;">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('type', __('product.product_type') . ':') !!}
                                {!! Form::select('type', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                                    {!! Form::label('category_id', __('product.category') . ':') !!}
                                {!! Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('unit_id', __('product.unit') . ':') !!}
                                {!! Form::select('unit_id', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tax_id', __('product.tax') . ':') !!}
                                {!! Form::select('tax_id', $taxes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_tax_id', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('product.brand') . ':') !!}
                                {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                        <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!! Form::label('active_state', __('sale.status') . ':') !!}
                                {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]) !!}
                        </div>
                    </div>
                    @if (!empty($pos_module_data))
                        @foreach ($pos_module_data as $key => $value)
                            @if (!empty($value['view_path']))
                                @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                            @endif
                        @endforeach
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 24px;">
                                    <input type="checkbox" name="not_for_selling" value="1" id="not_for_selling" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    <strong>@lang('lang_v1.not_for_selling')</strong>
                            </label>
                        </div>
                    </div>
                    @if ($is_woocommerce)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 24px;">
                                    <input type="checkbox" name="woocommerce_enabled" value="1" id="woocommerce_enabled" style="width: 16px; height: 16px; accent-color: #FF9900;">
                                    {{ __('lang_v1.woocommerce_enabled') }}
                                </label>
                            </div>
                        </div>
                    @endif
                    </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="amazon-btn amazon-btn-secondary" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

    @can('product.view')
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
                    <input type="text" class="amazon-search-input" id="amazonSearchInput" placeholder="Search products...">
        </div>
            </div>
            <div class="amazon-controls-right">
                <!-- Bulk Actions Dropdown -->
                <div class="bulk-actions-dropdown">
                    <button class="bulk-actions-btn" id="bulkActionsBtn">
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
                    <div class="bulk-actions-menu" id="bulkActionsMenu">
                        @can('product.delete')
                        <button type="button" class="bulk-action-item danger" id="bulkDeleteBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Delete Selected
                        </button>
                        @endcan
                        @can('product.update')
                        @if(config('constants.enable_product_bulk_edit'))
                        <button type="button" class="bulk-action-item" id="bulkEditBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Bulk Edit
                        </button>
                        @endif
                        <button type="button" class="bulk-action-item" id="bulkAddLocationBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Add to Location
                        </button>
                        <button type="button" class="bulk-action-item" id="bulkRemoveLocationBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <line x1="9" y1="10" x2="15" y2="10"></line>
                            </svg>
                            Remove from Location
                        </button>
                        @endcan
                        <div class="divider"></div>
                        <button type="button" class="bulk-action-item warning" id="bulkDeactivateBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                            </svg>
                            Deactivate
                        </button>
                        <button type="button" class="bulk-action-item success" id="bulkActivateBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            Activate
                        </button>
                        @if($is_woocommerce)
                        <div class="divider"></div>
                        <button type="button" class="bulk-action-item warning" id="bulkWooSyncBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                            </svg>
                            WooCommerce Sync
                        </button>
                        @endif
                    </div>
                </div>
                
                <button class="amazon-btn amazon-btn-secondary" id="amazonFilterBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Filters
                </button>
                
                <!-- Expand All Button -->
                <button class="expand-all-btn" id="expandAllBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    Expand All
                </button>
                
                <button class="amazon-btn amazon-btn-secondary" id="amazonCsvBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    CSV
                </button>
                <button class="amazon-btn amazon-btn-secondary" id="amazonExcelBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    Excel
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
                        Columns
                    </button>
                    <div class="colvis-dropdown" id="colvisDropdown">
                        <div class="colvis-dropdown-header">Toggle Columns</div>
                        <div class="colvis-dropdown-body" id="colvisDropdownBody">
                        </div>
                    </div>
                </div>
                @if ($is_admin)
                <a class="amazon-btn amazon-btn-secondary amazon-btn-download-excel" href="{{ action([\App\Http\Controllers\ProductController::class, 'downloadExcel']) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Excel
                </a>
                @endif
                @can('product.create')
                <a class="amazon-btn amazon-btn-primary amazon-btn-add-product" href="{{ action([\App\Http\Controllers\ProductController::class, 'create']) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add
                </a>
                @endcan
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs hide" style="display:none!important;">
                <li class="active"><a href="#product_list_tab" data-toggle="tab">Products</a></li>
                            @can('stock_report.view')
                <li><a href="#product_stock_report" class="product_stock_report" data-toggle="tab">Stock</a></li>
                            @endcan
                        </ul>

                        <div class="tab-content">
                <div class="tab-pane active" id="product_list_tab">
                    <div class="amazon-table-wrapper">
                                    @include('product.partials.product_list')
                                </div>
                            </div>
                            @can('stock_report.view')
                <div class="tab-pane" id="product_stock_report">
                                    @include('report.partials.stock_report_table')
                                </div>
                            @endcan
                    </div>
                </div>
            </div>
        @endcan
    
        <input type="hidden" id="is_rack_enabled" value="{{ $rack_enabled }}">

    <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade" id="variant_modal" tabindex="-1" role="dialog" aria-labelledby="variantModalLabel">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="variantModalLabel">Product Variations</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="variant_modal_body">
                    <div style="padding: 20px; text-align: center; color: #565959;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
                        </svg>
                        Loading variants...
                    </div>
                </div>
            </div>
        </div>
    </div>

        @if ($is_woocommerce)
            @include('product.partials.toggle_woocommerce_sync_modal')
        @endif
        @include('product.partials.edit_product_location_modal')
</div>
@endsection

@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
    // Amazon status tabs click handler (moved to top-right like Customer page)
    $('.amazon-status-tab').on('click', function(e) {
        e.preventDefault();
        $('.amazon-status-tab').removeClass('active');
        $(this).addClass('active');
        
        // Trigger original tab
        var href = $(this).attr('href');
        $('.nav-tabs a[href="' + href + '"]').tab('show');
    });
    
    // Product table initialization
            product_table = $('#product_table').DataTable({
                processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                serverSide: true,
                fixedHeader: false,
        aaSorting: [[5, 'desc']],
                // Vertical scroll inside the table body so header stays fixed in the Amazon-style layout.
                scrollY: '60vh',
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                "ajax": {
                    "url": "/products",
                    "data": function(d) {
                        d.type = $('#product_list_filter_type').val();
                        d.category_id = $('#product_list_filter_category_id').val();
                        d.brand_id = $('#product_list_filter_brand_id').val();
                        d.unit_id = $('#product_list_filter_unit_id').val();
                        d.tax_id = $('#product_list_filter_tax_id').val();
                        d.active_state = $('#active_state').val();
                        d.not_for_selling = $('#not_for_selling').is(':checked');
                        d.location_id = $('#location_id').val();
                        if ($('#repair_model_id').length == 1) {
                            d.repair_model_id = $('#repair_model_id').val();
                        }
                if ($('#woocommerce_enabled').length == 1 && $('#woocommerce_enabled').is(':checked')) {
                            d.woocommerce_enabled = 1;
                        }
                        d = __datatable_ajax_callback(d);
                    }
                },
        columnDefs: [
            { "targets": [0, 10, 16], "orderable": false, "searchable": false },
            { targets: [2], className: 'col-image', createdCell: function (td) {
                $(td).css({ 'white-space': 'normal', 'word-break': 'normal', 'overflow-wrap': 'normal' });
            }}
            ],
                columns: [
            { data: 'mass_delete', className: 'col-checkbox' },
            { data: 'action', name: 'action', className: 'col-action' },
            { data: 'image', name: 'products.image', searchable: false, orderable: false, className: 'col-image' },
            { data: 'product', name: 'products.name', className: 'col-product' },
            { data: 'sku', name: 'products.sku', className: 'col-sku' },
            { data: 'created_at', name: 'products.created_at', searchable: false, className: 'col-date' },
            { data: 'product_locations', name: 'product_locations', visible: false, className: 'col-location' },
                    @can('view_purchase_price')
            { data: 'purchase_price', name: 'max_purchase_price', searchable: false, className: 'col-purchase-price' },
                    @endcan
                    @can('access_default_selling_price')
            { data: 'selling_price', name: 'max_price', searchable: false, className: 'col-selling-price' },
            @endcan
            { data: 'current_stock', searchable: false, orderable: false, className: 'col-stock' },
            { data: 'type', name: 'products.type', searchable: false, className: 'col-type' },
            { data: 'fulfillment_type', name: 'fulfillment_type', orderable: false, searchable: false, className: 'col-fulfillment' },
            { data: 'silver_price', name: 'products.silver_price', searchable: false, visible: false, className: 'col-tier-price' },
            { data: 'gold_price', name: 'products.gold_price', searchable: false, visible: false, className: 'col-tier-price' },
            { data: 'platinum_price', name: 'products.platinum_price', searchable: false, visible: false, className: 'col-tier-price' },
            { data: 'category', name: 'c1.name', className: 'col-category' },
            { data: 'brand', name: 'brands.name', className: 'col-brand' },
            { data: 'tax', name: 'tax_rates.name', searchable: false, visible: false, className: 'col-tax' },
            { data: 'alert_quantity', name: 'products.alert_quantity', searchable: false, visible: false, className: 'col-hidden' },
        ],
        createdRow: function (row, data, dataIndex) {
            // Add product type as data attribute
            $(row).attr('data-product-type', data.type);
            $(row).attr('data-product-id', data.id);
            
            var $firstCell = $(row).find('td:eq(0)');
            var $checkbox = $firstCell.find('input[type="checkbox"]');
            
            // Wrap checkbox content in alignment wrapper
            if ($checkbox.length && !$firstCell.find('.checkbox-cell-wrapper').length) {
                // Detect variable products in a robust way (handles translations / extra spaces)
                var typeText = (data.type || '').toString().toLowerCase().trim();
                var isVariable = typeText.indexOf('variable') !== -1;
                var wrapperHtml = '<div class="checkbox-cell-wrapper">' + $checkbox.prop('outerHTML');
                
                // Only add expand arrow for variable products
                if (isVariable) {
                    $(row).addClass('has-variants');
                    $firstCell.addClass('details-control');
                    wrapperHtml += '<span class="expand-arrow" title="Click to expand variants"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></span>';
                }
                
                wrapperHtml += '</div>';
                $firstCell.html(wrapperHtml);
            }
            
            // Format SKU as card-style badge (column index 4)
            var $skuCell = $(row).find('td:eq(4)');
            var skuText = $skuCell.text().trim();
            if (skuText && skuText !== '-' && skuText !== 'N/A') {
                $skuCell.html('<span class="sku-card">' + skuText + '</span>');
            }
            
            // Find columns dynamically by looking at the data
            var $cells = $(row).find('td');
            
            // Format Current Stock with status icons
            // current_stock comes from data, find the cell that displays it
            if (data.current_stock !== undefined) {
                var stockText = data.current_stock;
                var stockValue = parseFloat(String(stockText).replace(/[^0-9.-]/g, '')) || 0;
                var alertQuantity = parseFloat(data.alert_quantity) || 0;
                
                // Find the stock cell - look for cells containing the stock value
                $cells.each(function() {
                    var cellText = $(this).text().trim();
                    if (cellText === stockText || cellText === String(stockText).trim()) {
                        var $stockCell = $(this);
                        var stockHtml = '';
                        
                        if (stockText === '--' || stockText === '-') {
                            stockHtml = '<span class="stock-status"><span class="stock-value">--</span></span>';
                        } else if (stockValue <= 0) {
                            // Zero stock - Red alert icon
                            stockHtml = '<span class="stock-status stock-zero">' +
                                '<span class="stock-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></span>' +
                                '<span class="stock-value">' + stockText + '</span></span>';
                        } else if (alertQuantity > 0 && stockValue <= alertQuantity) {
                            // Low stock - Yellow warning icon
                            stockHtml = '<span class="stock-status stock-low">' +
                                '<span class="stock-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span>' +
                                '<span class="stock-value">' + stockText + '</span></span>';
                        } else {
                            stockHtml = '<span class="stock-status stock-ok"><span class="stock-value">' + stockText + '</span></span>';
                        }
                        
                        $stockCell.html(stockHtml);
                        return false; // Break loop
                    }
                });
            }
            
            // Format Product Type - clean text only, no icons
            // Look for cells with 'Single' or 'Variable' text
            $cells.each(function() {
                var cellText = $(this).text().trim();
                if (cellText === 'Single' || cellText === 'Variable' || cellText === 'single' || cellText === 'variable' || cellText === 'Combo' || cellText === 'combo') {
                    var $typeCell = $(this);
                    var typeClass = cellText.toLowerCase().replace(/\s+/g, '-');
                    $typeCell.html('<span class="product-type-text ' + typeClass + '">' + cellText + '</span>');
                    return false; // Break loop
                }
            });
                },
                initComplete: function() {
                    var api = this.api();
                    api.columns.adjust();
                    // Setup horizontal scroll sync for product table
                    setTimeout(function() {
                        syncProductTableHorizontalScroll();
                    }, 200);
                },
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#product_table'));
                    // Re-setup scroll sync in case DOM changed
                    setTimeout(function() {
                        syncProductTableHorizontalScroll();
                    }, 50);
                },
                dom: '<"top"lfB>rt<"bottom"ip>',
                buttons: [
            { text: '<i class="fa fa-filter"></i> Filters', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 buttons-filter', action: function () { $('#filterModal').modal('show'); } },
            { extend: 'csv', text: '<i class="fa fa-file-csv" aria-hidden="true"></i> CSV', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 buttons-csv', exportOptions: { columns: ':visible' }, footer: true },
            { extend: 'excel', text: '<i class="fa fa-file-excel" aria-hidden="true"></i> Excel', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 buttons-excel', exportOptions: { columns: ':visible' }, footer: true },
            {
                text: '<i class="fa fa-print" aria-hidden="true"></i> Print',
                className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 buttons-print',
                action: function (e, dt, node, config) {
                    // Build a simple HTML document from the visible DataTable data and print it
                    var exportData = dt.buttons.exportData({
                        columns: ':visible',
                        stripHtml: true
                    });

                    var win = window.open('', '', 'height=800,width=1200');
                    if (!win) {
                        alert('Please allow popups for this site to print the product list.');
                        return;
                    }

                    var doc = win.document;
                    doc.write('<html><head><title>Products - Smokevana</title>');
                    doc.write('<style>');
                    doc.write('body{font-family: Arial, sans-serif;font-size:10px;margin:16px;}');
                    doc.write('h2{margin-bottom:12px;}');
                    doc.write('table{border-collapse:collapse;width:100%;}');
                    doc.write('th,td{border:1px solid #000;padding:4px;text-align:left;white-space:nowrap;}');
                    doc.write('thead th{background:#f0f0f0;font-weight:bold;}');
                    doc.write('</style></head><body>');
                    doc.write('<h2>Products</h2>');
                    doc.write('<table><thead><tr>');

                    // Headers
                    exportData.header.forEach(function (header) {
                        doc.write('<th>' + header + '</th>');
                    });
                    doc.write('</tr></thead><tbody>');

                    // Body rows
                    exportData.body.forEach(function (row) {
                        doc.write('<tr>');
                        row.forEach(function (cell) {
                            doc.write('<td>' + cell + '</td>');
                        });
                        doc.write('</tr>');
                    });
                    doc.write('</tbody></table></body></html>');
                    doc.close();
                    win.focus();
                    win.print();
                }
            },
            { extend: 'colvis', text: '<i class="fa fa-columns" aria-hidden="true"></i> Columns', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2 buttons-colvis' },
        ],
        searchDelay: 500
    });

    // Before print: hide Action and Checkbox columns and clear their content so they never print
    window.addEventListener('beforeprint', function() {
        var $w = $('#product_table_wrapper');
        var $actionHead = $w.find('.dataTables_scrollHead th.col-action, .dataTables_scrollHead thead th:nth-child(2)');
        var $actionBody = $w.find('.dataTables_scrollBody td.col-action, .dataTables_scrollBody tbody td:nth-child(2)');
        var $checkHead = $w.find('.dataTables_scrollHead th.col-checkbox, .dataTables_scrollHead thead th:nth-child(1)');
        var $checkBody = $w.find('.dataTables_scrollBody td.col-checkbox, .dataTables_scrollBody tbody td:nth-child(1)');
        [$actionHead, $actionBody, $checkHead, $checkBody].forEach(function($el) {
            $el.addClass('no-print-col').css({ display: 'none', width: 0, overflow: 'hidden', padding: 0, margin: 0 });
            $el.attr('data-print-content', $el.html());
            $el.empty();
        });
    });
    window.addEventListener('afterprint', function() {
        $('#product_table_wrapper .no-print-col').each(function() {
            var $el = $(this);
            var content = $el.attr('data-print-content');
            if (content) $el.html(content);
            $el.removeClass('no-print-col').removeAttr('data-print-content').css({ display: '', width: '', overflow: '', padding: '', margin: '' });
        });
    });

    // Custom controls
    $('#amazonEntriesSelect').on('change', function() {
        product_table.page.len($(this).val()).draw();
    });
    
    var searchTimeout;
    $('#amazonSearchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        var searchVal = $(this).val();
        searchTimeout = setTimeout(function() {
            product_table.search(searchVal).draw();
        }, 300);
    });
    
    $('#amazonFilterBtn').on('click', function() { $('#filterModal').modal('show'); });
    $('#amazonCsvBtn').on('click', function() { product_table.button('.buttons-csv').trigger(); });
    $('#amazonExcelBtn').on('click', function() { product_table.button('.buttons-excel').trigger(); });

    // Sticky product table header
    (function setupStickyProductHeader() {
        // Disabled custom fixed-position sticky logic; DataTables scroll header
        // keeps head/body widths in sync more reliably for this layout.
        return;

        var $container = $('#scrollable-container');
        var $wrapper   = $('#product_table_wrapper');

        function init() {
            var $scrollHead = $wrapper.find('.dataTables_scrollHead').first();
            var $scrollBody = $wrapper.find('.dataTables_scrollBody').first();
            if (!$scrollHead.length || !$container.length) return;

            var $placeholder = $('<div class="sticky-head-placeholder"></div>')
                .css({ display: 'none', height: 0 })
                .insertBefore($scrollHead);

            var sticky = false;

            function update() {
                var cTop = $container[0].getBoundingClientRect().top;

                if (!sticky) {
                    var hRect = $scrollHead[0].getBoundingClientRect();
                    if (hRect.top < cTop) {
                        sticky = true;
                        var h = $scrollHead.outerHeight();
                        var parentRect = $scrollHead.parent()[0].getBoundingClientRect();
                        $placeholder.css({ display: 'block', height: h });
                        $scrollHead.addClass('is-sticky').css({
                            position: 'fixed',
                            top:   cTop + 'px',
                            left:  parentRect.left + 'px',
                            width: parentRect.width + 'px',
                            zIndex: 100
                        });
                    }
                } else {
                    var pRect = $placeholder[0].getBoundingClientRect();
                    if (pRect.top >= cTop) {
                        sticky = false;
                        $scrollHead.removeClass('is-sticky').css({
                            position: '', top: '', left: '', width: '', zIndex: ''
                        });
                        $placeholder.css('display', 'none');
                    } else {
                        var parentRect = $placeholder.parent()[0].getBoundingClientRect();
                        $scrollHead.css({
                            top:   cTop + 'px',
                            left:  parentRect.left + 'px',
                            width: parentRect.width + 'px'
                        });
                    }
                }
            }

            $container.on('scroll.stickyPH', update);
            $(window).on('resize.stickyPH', update);
        }

        if ($wrapper.find('.dataTables_scrollHead').length) {
            init();
        } else {
            product_table.on('init.dt', init);
        }
    })();

    // Sync product table header with body: copy column widths from body to header so they align
    function syncProductTableHeadBody() {
        var $headTh = $('#product_table_wrapper .dataTables_scrollHead thead th');
        var $bodyTd = $('#product_table_wrapper .dataTables_scrollBody tbody tr:first td');
        if (!$headTh.length || !$bodyTd.length || $headTh.length !== $bodyTd.length) {
            setTimeout(syncProductTableHeadBody, 100);
            return;
        }
        // One-way: set header column widths to match body (body is source of truth)
        $bodyTd.each(function(i) {
            var w = $(this).outerWidth();
            if (w && $headTh.eq(i).length) {
                $headTh.eq(i).css({ width: w + 'px', minWidth: w + 'px' });
            }
        });
    }
    function bindProductTableScrollSync() {
        var $body = $('#product_table_wrapper .dataTables_scrollBody');
        var $head = $('#product_table_wrapper .dataTables_scrollHead');
        if (!$body.length || !$head.length) return;
        $body.off('scroll.productSync').on('scroll.productSync', function() {
            var scrollLeft = $(this).scrollLeft();
            $head.scrollLeft(scrollLeft);
            // Also sync the inner scroll head table if it exists
            $head.find('.dataTables_scrollHeadInner').scrollLeft(scrollLeft);
        });
    }
    
    // Initialize sync on table init
    product_table.on('init.dt', function() {
        setTimeout(function() {
            syncProductTableHeadBody();
            bindProductTableScrollSync();
        }, 200);
    });
    
    // Re-sync on window resize
    $(window).on('resize.productTableSync', function() {
        setTimeout(function() {
            syncProductTableHeadBody();
        }, 100);
    });

    // Helper – generic DOM-based print for a DataTable-like table
    function printSimpleTable(options) {
        var tableSelector     = options.tableSelector;
        var wrapperSelector   = options.wrapperSelector;
        var title             = options.title || 'Print';
        var skipFirstColumn   = options.skipFirstColumn || false;

        // 1) Headers
        var headers = [];
        var $headerCells = $(wrapperSelector + ' .dataTables_scrollHead th:visible');
        if ($headerCells.length === 0) {
            $headerCells = $(tableSelector + ' thead th:visible');
        }
        $headerCells.each(function(index) {
            if (skipFirstColumn && index === 0) { return; }
            headers.push($(this).text().trim());
        });

        // 2) Rows
        var rows = [];
        var $rows = $(wrapperSelector + ' .dataTables_scrollBody tbody tr:visible');
        if ($rows.length === 0) {
            $rows = $(tableSelector + ' tbody tr:visible');
        }
        $rows.each(function() {
            var row = [];
            $(this).find('td:visible').each(function(index) {
                if (skipFirstColumn && index === 0) { return; }
                row.push($(this).text().trim());
            });
            if (row.length) {
                rows.push(row);
            }
        });

        var win = window.open('', '', 'height=800,width=1200');
        if (!win) {
            alert('Please allow popups for this site to print.');
            return;
        }

        var doc = win.document;
        doc.write('<html><head><title>' + title + '</title>');
        doc.write('<style>');
        doc.write('body{font-family:Arial,sans-serif;font-size:10px;margin:16px;}');
        doc.write('h2{margin-bottom:12px;}');
        doc.write('table{border-collapse:collapse;width:100%;}');
        doc.write('th,td{border:1px solid #000;padding:4px;text-align:left;white-space:nowrap;}');
        doc.write('thead th{background:#f0f0f0;font-weight:bold;}');
        doc.write('</style></head><body>');
        doc.write('<h2>' + title + '</h2>');
        doc.write('<table><thead><tr>');
        headers.forEach(function(header) {
            doc.write('<th>' + header + '</th>');
        });
        doc.write('</tr></thead><tbody>');

        rows.forEach(function(row) {
            doc.write('<tr>');
            row.forEach(function(cell) {
                doc.write('<td>' + cell + '</td>');
            });
            doc.write('</tr>');
        });

        doc.write('</tbody></table></body></html>');
        doc.close();
        win.focus();
        win.print();
    }

    $('#amazonPrintBtn').on('click', function() {
        // If the Stock tab is active, print the stock report table instead of the main product list.
        if ($('#product_stock_report').hasClass('active')) {
            printSimpleTable({
                tableSelector:    '#stock_report_table',
                wrapperSelector:  '#stock_report_table_wrapper',
                title:            'Stock Report',
                skipFirstColumn:  true   // skip Action column
            });
        } else {
            // Default: print main product list
            printSimpleTable({
                tableSelector:    '#product_table',
                wrapperSelector:  '#product_table_wrapper',
                title:            'Products',
                skipFirstColumn:  true
            });
        }
    });
    // Column Visibility - Custom Dropdown
    function buildColVisDropdown() {
        var $body = $('#colvisDropdownBody');
        $body.empty();
        
        product_table.columns().every(function(index) {
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
        var column = product_table.column(colIndex);
        
        column.visible(!column.visible());
        $item.toggleClass('active');
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.colvis-dropdown-wrapper').length) {
            $('#colvisDropdown').removeClass('show');
        }
    });

    // ==========================================
    // BULK ACTIONS DROPDOWN
    // ==========================================
    $('#bulkActionsBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#bulkActionsMenu').toggleClass('show');
    });
    
    // Close bulk actions menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.bulk-actions-dropdown').length) {
            $('#bulkActionsMenu').removeClass('show');
        }
    });
    
    // Bulk action handlers
    $('#bulkDeleteBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            swal({ title: LANG.sure, text: 'Delete ' + selected_rows.length + ' selected products?', icon: "warning", buttons: true, dangerMode: true }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: 'POST',
                        url: "{{ action([\App\Http\Controllers\ProductController::class, 'massDestroy']) }}",
                        data: { selected_rows: selected_rows.join(','), _token: '{{ csrf_token() }}' },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) { toastr.success(result.msg); product_table.ajax.reload(); }
                            else { toastr.error(result.msg); }
                        }
                    });
                }
            });
        } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    
    $('#bulkEditBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            window.location.href = "{{ action([\App\Http\Controllers\ProductController::class, 'bulkEdit']) }}?selected_products=" + selected_rows.join(',');
                                } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    
    $('#bulkAddLocationBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            $('#product_location_modal').modal('show');
            $('#product_location_modal').find('.update_product_location_confirm').data('type', 'add');
            $('input#product_ids').val(selected_rows);
        } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    
    $('#bulkRemoveLocationBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            $('#product_location_modal').modal('show');
            $('#product_location_modal').find('.update_product_location_confirm').data('type', 'remove');
            $('input#product_ids').val(selected_rows);
        } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    
    $('#bulkDeactivateBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            swal({ title: LANG.sure, text: 'Deactivate ' + selected_rows.length + ' products?', icon: "warning", buttons: true }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'POST',
                        url: "{{ action([\App\Http\Controllers\ProductController::class, 'massDeactivate']) }}",
                        data: { selected_products: selected_rows.join(','), _token: '{{ csrf_token() }}' },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) { toastr.success(result.msg); product_table.ajax.reload(); }
                            else { toastr.error(result.msg); }
                        }
                    });
                }
            });
        } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    
    $('#bulkActivateBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            swal({ title: LANG.sure, text: 'Activate ' + selected_rows.length + ' products?', icon: "info", buttons: true }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'POST',
                        url: "{{ action([\App\Http\Controllers\ProductController::class, 'massActivate']) }}",
                        data: { selected_products: selected_rows.join(','), _token: '{{ csrf_token() }}' },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) { toastr.success(result.msg); product_table.ajax.reload(); }
                            else { toastr.error(result.msg); }
                        }
                    });
                }
            });
        } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    
    @if($is_woocommerce)
    $('#bulkWooSyncBtn').on('click', function() {
        $('#bulkActionsMenu').removeClass('show');
        var selected_rows = getSelectedRows();
        if (selected_rows.length > 0) {
            $('#woocommerce_sync_modal').modal('show');
            $("input#woocommerce_products_sync").val(selected_rows);
        } else {
            swal('@lang('lang_v1.no_row_selected')');
        }
    });
    @endif

    // ==========================================
    // EXPAND ALL BUTTON
    // ==========================================
    var allExpanded = false;
    $('#expandAllBtn').on('click', function() {
        var $btn = $(this);
        allExpanded = !allExpanded;
        
        if (allExpanded) {
            $btn.addClass('expanded').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg> Collapse All');
            // Expand all variable products
            $('#product_table tbody tr.has-variants').each(function() {
                var tr = $(this);
                var row = product_table.row(tr);
                if (!row.child.isShown()) {
                    tr.addClass('details');
                    tr.find('.expand-arrow').addClass('expanded');
                    show_child_product(row, row.data());
                }
            });
        } else {
            $btn.removeClass('expanded').html('<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg> Expand All');
            // Collapse all
            $('#product_table tbody tr.has-variants').each(function() {
                var tr = $(this);
                var row = product_table.row(tr);
                if (row.child.isShown()) {
                    tr.removeClass('details');
                    tr.find('.expand-arrow').removeClass('expanded');
                    row.child.hide();
                }
            });
        }
    });

    // ==========================================
    // PRODUCT VARIATIONS EXPAND/COLLAPSE
    // ==========================================
            var product_variations = [];

    function openVariantsModal(productId) {
        if (!productId) {
            return;
        }
        $('#variant_modal_body').html('<div style="padding: 20px; text-align: center; color: #565959;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg> Loading variants...</div>');
        $('#variant_modal').modal('show');
        $.ajax({
            url: '/products/child/' + productId,
            dataType: 'html',
            success: function (data) {
                var content = (data || '').trim();
                if (!content) {
                    $('#variant_modal_body').html('<div style="padding: 20px; text-align: center; color: #B12704;">No variants found.</div>');
                    return;
                }
                var variantHtml = '<div class="variant-container">' +
                    '<div class="variant-header">' +
                    '<h4><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg> Product Variations</h4>' +
                    '</div>' +
                    '<div class="variant-content">' + content + '</div>' +
                    '</div>';
                $('#variant_modal_body').html(variantHtml);
            },
            error: function (xhr) {
                var msg = 'Failed to load variants.';
                if (xhr && xhr.responseText) {
                    msg += '<br><small>' + $('<div/>').text(xhr.responseText).html() + '</small>';
                }
                $('#variant_modal_body').html('<div style="padding: 20px; text-align: center; color: #B12704;">' + msg + '</div>');
            }
        });
    }

    // Cost eye toggle: use delegation so it works when variant modal content is loaded via AJAX (inline script in partial does not run)
    $(document).on('click', '#variant_modal .toggle-cost-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $cell = $btn.closest('td');
        var $costSpan = $cell.find('span[id^="cost-"]');
        if (!$costSpan.length) {
            $costSpan = $btn.siblings('span');
        }
        var $icon = $btn.find('i');
        if ($costSpan.length) {
            if ($costSpan.is(':visible')) {
                $costSpan.hide();
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                $costSpan.css('display', 'inline').show();
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        }
    });

    // Click on expand arrow (open modal as a reliable alternative)
    $('#product_table tbody').on('click', '.expand-arrow', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var tr = $(this).closest('tr');
        var row = product_table.row(tr);
        openVariantsModal(row.data().id);
    });
    
    // Also handle click on the cell itself for backwards compatibility
    $('#product_table tbody').on('click', 'tr.has-variants td.details-control', function(e) {
        // Only trigger if not clicking on checkbox, expand arrow, or checkbox wrapper
        if ($(e.target).is('input[type="checkbox"]') || 
            $(e.target).closest('.expand-arrow').length ||
            $(e.target).closest('.checkbox-cell-wrapper').length) {
            return;
        }
        $(this).find('.expand-arrow').trigger('click');
    });

    // Handle checkbox click - prevent propagation to avoid unwanted expand/collapse
    $('#product_table tbody').on('click', 'tr td:first-child input[type="checkbox"], tr td:first-child .checkbox-cell-wrapper input[type="checkbox"]', function(e) {
                e.stopPropagation();
        // Toggle selected state
        var tr = $(this).closest('tr');
        if ($(this).is(':checked')) {
            tr.addClass('selected');
        } else {
            tr.removeClass('selected');
        }
    });

    // View variants in modal as an alternative to inline expand
    $(document).on('click', '.view-variants', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        openVariantsModal(productId);
    });

            product_table.on('draw', function () {
        // Restore expanded state after redraw
                $.each(product_variations, function (i, id) {
            var tr = $('#' + id);
            if (tr.length && tr.hasClass('has-variants')) {
                var row = product_table.row(tr);
                if (!row.child.isShown()) {
                    tr.addClass('details');
                    tr.find('.expand-arrow').addClass('expanded');
                    show_child_product(row, row.data());
                }
            }
        });
        
        // Update custom pagination
        updateCustomPagination();
        
        // Keep header aligned: sync column widths and horizontal scroll position
        setTimeout(function() {
            try { product_table.columns().adjust(); } catch (e) {}
        }, 0);
        setTimeout(function() {
            try {
                product_table.columns().adjust();
                if (typeof syncProductTableHeadBody === 'function') syncProductTableHeadBody();
                if (typeof bindProductTableScrollSync === 'function') bindProductTableScrollSync();
            } catch (e) {}
        }, 100);
        setTimeout(function() {
            if (typeof syncProductTableHeadBody === 'function') syncProductTableHeadBody();
        }, 300);
            });

            function show_child_product(row, rowData) {
        var loadingHtml = '<div class="variant-container loading"><div style="padding: 20px; text-align: center; color: #565959;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Loading variants...</div></div>';
        row.child(loadingHtml).show();

                $.ajax({
                    url: '/products/child/' + rowData.id,
                    dataType: 'html',
                    success: function (data) {
                // Wrap the response in our styled container
                var variantHtml = '<div class="variant-container">' +
                    '<div class="variant-header">' +
                    '<h4><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg> Product Variations</h4>' +
                    '</div>' +
                    '<div class="variant-content">' + data + '</div>' +
                    '</div>';
                row.child(variantHtml).show();
                        __currency_convert_recursively($(row.child()));
                    },
                    error: function () {
                var errorHtml = '<div class="variant-container"><div style="padding: 20px; text-align: center; color: #B12704;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> No variations available</div></div>';
                row.child(errorHtml).show();
                    }
                });
            }

    // ==========================================
    // CUSTOM PAGINATION
    // ==========================================
    function updateCustomPagination() {
        var info = product_table.page.info();
        var totalPages = info.pages;
        var currentPage = info.page + 1;
        var totalRecords = info.recordsTotal;
        var start = info.start + 1;
        var end = info.end;
        
        if (totalRecords === 0) {
            start = 0;
            end = 0;
        }
        
        // Build pagination HTML
        var paginationHtml = '<div class="amazon-pagination-container">';
        paginationHtml += '<div class="amazon-pagination-info">Showing <strong>' + start.toLocaleString() + '</strong> to <strong>' + end.toLocaleString() + '</strong> of <strong>' + totalRecords.toLocaleString() + '</strong> products</div>';
        paginationHtml += '<div class="amazon-pagination">';
        
        // Previous button
        paginationHtml += '<a class="page-btn ' + (currentPage === 1 ? 'disabled' : '') + '" data-page="prev">Previous</a>';
        
        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            paginationHtml += '<a class="page-btn" data-page="1">1</a>';
            if (startPage > 2) {
                paginationHtml += '<span class="page-ellipsis">...</span>';
            }
        }
        
        for (var i = startPage; i <= endPage; i++) {
            paginationHtml += '<a class="page-btn ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</a>';
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += '<span class="page-ellipsis">...</span>';
            }
            paginationHtml += '<a class="page-btn" data-page="' + totalPages + '">' + totalPages + '</a>';
        }
        
        // Next button
        paginationHtml += '<a class="page-btn ' + (currentPage === totalPages || totalPages === 0 ? 'disabled' : '') + '" data-page="next">Next</a>';
        
        paginationHtml += '</div></div>';
        
        // Remove old custom pagination if exists
        $('#product_table_wrapper .amazon-pagination-container').remove();
        
        // Insert custom pagination after the table
        $('#product_table_wrapper').append(paginationHtml);
    }
    
    // Pagination click handler
    $(document).on('click', '#product_table_wrapper .amazon-pagination .page-btn:not(.disabled)', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        
        if (page === 'prev') {
            product_table.page('previous').draw('page');
        } else if (page === 'next') {
            product_table.page('next').draw('page');
        } else {
            product_table.page(page - 1).draw('page');
        }
    });

    // Rack details
    var detailRows = [];
            $('#product_table tbody').on('click', 'tr i.rack-details', function() {
                var i = $(this);
                var tr = $(this).closest('tr');
                var row = product_table.row(tr);
                var idx = $.inArray(tr.attr('id'), detailRows);
                if (row.child.isShown()) {
            i.addClass('fa-plus-circle text-success').removeClass('fa-minus-circle text-danger');
                    row.child.hide();
                    detailRows.splice(idx, 1);
                } else {
            i.removeClass('fa-plus-circle text-success').addClass('fa-minus-circle text-danger');
                    row.child(get_product_details(row.data())).show();
            if (idx === -1) { detailRows.push(tr.attr('id')); }
                }
            });

            $('#opening_stock_modal').on('hidden.bs.modal', function(e) {
                product_table.ajax.reload();
            });

    // Delete product
            $('table#product_table tbody').on('click', 'a.delete-product', function(e) {
                e.preventDefault();
        swal({ title: LANG.sure, icon: "warning", buttons: true, dangerMode: true }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                    method: "DELETE", url: href, dataType: "json",
                            success: function(result) {
                        if (result.success == true) { toastr.success(result.msg); product_table.ajax.reload(); }
                        else { toastr.error(result.msg); }
                            }
                        });
                    }
                });
            });

    // Mass actions
            $(document).on('click', '#delete-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    $('input#selected_rows').val(selected_rows);
            swal({ title: LANG.sure, icon: "warning", buttons: true, dangerMode: true }).then((willDelete) => {
                if (willDelete) { $('form#mass_delete_form').submit(); }
                    });
                } else {
                    $('input#selected_rows').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
            });

            $(document).on('click', '#deactivate-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    $('input#selected_products').val(selected_rows);
            swal({ title: LANG.sure, icon: "warning", buttons: true, dangerMode: true }).then((willDelete) => {
                        if (willDelete) {
                    var form = $('form#mass_deactivate_form');
                            $.ajax({
                        method: form.attr('method'), url: form.attr('action'), dataType: 'json', data: form.serialize(),
                                success: function(result) {
                            if (result.success == true) { toastr.success(result.msg); product_table.ajax.reload(); form.find('#selected_products').val(''); }
                            else { toastr.error(result.msg); }
                        }
                            });
                        }
                    });
                } else {
                    $('input#selected_products').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
    });

            $(document).on('click', '#activate-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    $('input#selected_products').val(selected_rows);
            swal({ title: LANG.sure, icon: "warning", buttons: true, successMode: true }).then((willDelete) => {
                        if (willDelete) {
                    var form = $('form#mass_activate_form');
                            $.ajax({
                        method: form.attr('method'), url: form.attr('action'), dataType: 'json', data: form.serialize(),
                                success: function(result) {
                            if (result.success == true) { toastr.success(result.msg); product_table.ajax.reload(); form.find('#selected_products').val(''); }
                            else { toastr.error(result.msg); }
                        }
                            });
                        }
                    });
                } else {
                    $('input#selected_products').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
    });

            $(document).on('click', '#edit-selected', function(e) {
                e.preventDefault();
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    $('input#selected_products_for_edit').val(selected_rows);
                    $('form#bulk_edit_form').submit();
                } else {
                    $('input#selected_products').val('');
                    swal('@lang('lang_v1.no_row_selected')');
                }
    });

    // Activate/Deactivate single product
            $('table#product_table tbody').on('click', 'a.activate-product', function(e) {
                e.preventDefault();
        $.ajax({ method: "get", url: $(this).attr('href'), dataType: "json", success: function(result) {
            if (result.success == true) { toastr.success(result.msg); product_table.ajax.reload(); }
            else { toastr.error(result.msg); }
        }});
    });

            $('table#product_table tbody').on('click', 'a.deactivate-product', function(e) {
                e.preventDefault();
        $.ajax({ method: "post", url: $(this).attr('href'), dataType: "json", success: function(result) {
            if (result.success == true) { toastr.success(result.msg); product_table.ajax.reload(); }
            else { toastr.error(result.msg); }
        }});
    });

    // Filter changes
    $(document).on('change', '#product_list_filter_type, #product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #repair_model_id', function() {
        if ($("#product_list_tab").hasClass('active')) { product_table.ajax.reload(); }
        if (typeof stock_report_table !== 'undefined' && $("#product_stock_report").hasClass('active')) { stock_report_table.ajax.reload(); }
    });

    $(document).on('change', '#not_for_selling, #woocommerce_enabled', function() {
        if ($("#product_list_tab").hasClass('active')) { product_table.ajax.reload(); }
        if (typeof stock_report_table !== 'undefined' && $("#product_stock_report").hasClass('active')) { stock_report_table.ajax.reload(); }
    });

    $('#product_location').select2({ dropdownParent: $('#product_location').closest('.modal') });

            @if ($is_woocommerce)
                $(document).on('click', '.toggle_woocomerce_sync', function(e) {
                    e.preventDefault();
                    var selected_rows = getSelectedRows();
                    if (selected_rows.length > 0) {
                        $('#woocommerce_sync_modal').modal('show');
                        $("input#woocommerce_products_sync").val(selected_rows);
                    } else {
                        $('input#selected_products').val('');
                        swal('@lang('lang_v1.no_row_selected')');
                    }
                });

                $(document).on('submit', 'form#toggle_woocommerce_sync_form', function(e) {
                    e.preventDefault();
                    var url = $('form#toggle_woocommerce_sync_form').attr('action');
                    var method = $('form#toggle_woocommerce_sync_form').attr('method');
                    var data = $('form#toggle_woocommerce_sync_form').serialize();
                    var ladda = Ladda.create(document.querySelector('.ladda-button'));
                    ladda.start();
                    $.ajax({
            method: method, dataType: "json", url: url, data: data,
                        success: function(result) {
                            ladda.stop();
                            if (result.success) {
                                $("input#woocommerce_products_sync").val('');
                                $('#woocommerce_sync_modal').modal('hide');
                                toastr.success(result.msg);
                                product_table.ajax.reload();
                } else { toastr.error(result.msg); }
                        }
                    });
                });
            @endif
        });

// View product modal handler
$(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal, #view_product_modal', function() {
                var div = $(this).find('#view_product_stock_details');
                if (div.length) {
                    $.ajax({
            url: "{{ action([\App\Http\Controllers\ReportController::class, 'getStockReport']) }}" + '?for=view_product&product_id=' + div.data('product_id'),
                        dataType: 'html',
            success: function(result) { div.html(result); __currency_convert_recursively(div); }
                    });
                }
                __currency_convert_recursively($(this));
            });

// Function to sync stock report header and body column widths
function syncStockReportColumnWidths() {
    var $headerTable = $('#stock_report_table_wrapper .dataTables_scrollHead table');
    var $bodyTable = $('#stock_report_table_wrapper .dataTables_scrollBody table');
    
    if ($headerTable.length && $bodyTable.length) {
        // Get all header cells and body cells
        var $headerCells = $headerTable.find('thead th');
        var $bodyCells = $bodyTable.find('tbody tr:first td');
        
        // If no body rows yet, try to get column count from header
        if ($bodyCells.length === 0) {
            $bodyCells = $bodyTable.find('tbody tr td');
        }
        
        // Ensure we have matching number of cells
        var cellCount = Math.min($headerCells.length, $bodyCells.length);
        
        // Sync widths: use body cell widths as source of truth
        for (var i = 0; i < cellCount; i++) {
            var $headerCell = $headerCells.eq(i);
            var $bodyCell = $bodyCells.eq(i);
            
            if ($bodyCell.length && $bodyCell.is(':visible')) {
                // Get the actual rendered width of body cell
                var bodyWidth = $bodyCell[0].offsetWidth || $bodyCell.outerWidth();
                
                // Apply to header cell
                if ($headerCell.length && bodyWidth > 0) {
                    $headerCell.css({
                        'width': bodyWidth + 'px',
                        'min-width': bodyWidth + 'px',
                        'max-width': bodyWidth + 'px'
                    });
                }
            }
        }
        
        // Force table recalculation
        if (typeof stock_report_table !== 'undefined') {
            stock_report_table.columns.adjust();
        }
    }
}

// Function to sync horizontal scroll between header and body (stock report)
// Matches the pattern used for product_table
function syncStockReportHorizontalScroll() {
    var $scrollBody = $('#stock_report_table_wrapper .dataTables_scrollBody');
    var $scrollHead = $('#stock_report_table_wrapper .dataTables_scrollHead');  
    
    if (!$scrollBody.length || !$scrollHead.length) return;
    
    // When body scrolls horizontally, move the header by the same amount
    $scrollBody.off('scroll.stockSync').on('scroll.stockSync', function() {
        var scrollLeft = $(this).scrollLeft();
        $scrollHead.scrollLeft(scrollLeft);
        // Also sync the inner scroll head table if it exists (critical for proper alignment)
        $scrollHead.find('.dataTables_scrollHeadInner').scrollLeft(scrollLeft);
    });
}

// Function to sync horizontal scroll between header and body (product list)
function syncProductTableHorizontalScroll() {
    var $wrapper    = $('#product_table_wrapper');
    var $scrollBody = $wrapper.find('.dataTables_scrollBody');
    var $scrollHead = $wrapper.find('.dataTables_scrollHead');

    if ($scrollBody.length && $scrollHead.length) {
        $scrollBody.off('scroll.syncHeader').on('scroll.syncHeader', function () {
            var scrollLeft = this.scrollLeft;
            $scrollHead.scrollLeft(scrollLeft);
        });
    }
}

// Listen for window resize to re-sync widths
$(window).on('resize', function() {
    if ($('#stock_report_table_wrapper').length && typeof stock_report_table !== 'undefined') {
        setTimeout(function() {
            syncStockReportColumnWidths();
            stock_report_table.columns.adjust();
            syncStockReportHorizontalScroll();
        }, 100);
    }
    if ($('#product_table_wrapper').length && typeof product_table !== 'undefined') {
        setTimeout(function() {
            syncProductTableHorizontalScroll();
        }, 100);
    }
});

// Stock report tab
        var stock_report_table_initialized = false;
$('a[data-toggle="tab"], .amazon-tab-btn').on('shown.bs.tab click', function(e) {
    var target = $(e.target).attr('href') || $(e.currentTarget).attr('href');
    if (target == '#product_stock_report') {
                if (!stock_report_table_initialized) {
            var stock_report_cols = [
                { data: 'action', name: 'action', searchable: false, orderable: false },
                { data: 'sku', name: 'variations.sub_sku' },
                { data: 'product', name: 'p.name' },
                { data: 'variation', name: 'variation' },
                { data: 'category_name', name: 'c.name' },
                { data: 'location_name', name: 'l.name' },
                { data: 'unit_price', name: 'variations.sell_price_inc_tax' },
                { data: 'stock', name: 'stock', searchable: false },
                { data: 'webstock', name: 'webstock', searchable: false },
                    ];
                    if ($('th.stock_price').length) {
                stock_report_cols.push({ data: 'stock_price', name: 'stock_price', searchable: false });
                stock_report_cols.push({ data: 'stock_value_by_sale_price', name: 'stock_value_by_sale_price', searchable: false, orderable: false });
                stock_report_cols.push({ data: 'potential_profit', name: 'potential_profit', searchable: false, orderable: false });
            }
            stock_report_cols.push({ data: 'total_sold', name: 'total_sold', searchable: false });
            stock_report_cols.push({ data: 'total_transfered', name: 'total_transfered', searchable: false });
            stock_report_cols.push({ data: 'total_adjusted', name: 'total_adjusted', searchable: false });
            stock_report_cols.push({ data: 'product_custom_field1', name: 'p.product_custom_field1', searchable: false, orderable: false, visible: false });
            stock_report_cols.push({ data: 'product_custom_field2', name: 'p.product_custom_field2', searchable: false, orderable: false, visible: false });
            stock_report_cols.push({ data: 'product_custom_field3', name: 'p.product_custom_field3', searchable: false, orderable: false, visible: false });
            stock_report_cols.push({ data: 'product_custom_field4', name: 'p.product_custom_field4', searchable: false, orderable: false, visible: false });
                    if ($('th.current_stock_mfg').length) {
                stock_report_cols.push({ data: 'total_mfg_stock', name: 'total_mfg_stock', searchable: false });
            }

                    stock_report_table = $('#stock_report_table').DataTable({
                order: [[1, 'asc']],
                        processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                        serverSide: true,
                        scrollY: '60vh',
                        scrollX: true,
                        scrollCollapse: true,
                        autoWidth: false,
                        fixedHeader: false,
                        ajax: {
                            url: '/reports/stock-report',
                            data: function(d) {
                                d.location_id = $('#location_id').val();
                                d.category_id = $('#product_list_filter_category_id').val();
                                d.brand_id = $('#product_list_filter_brand_id').val();
                                d.unit_id = $('#product_list_filter_unit_id').val();
                                d.type = $('#product_list_filter_type').val();
                                d.active_state = $('#active_state').val();
                                d.not_for_selling = $('#not_for_selling').is(':checked');
                        if ($('#repair_model_id').length == 1) { d.repair_model_id = $('#repair_model_id').val(); }
                    }
                },
                columnDefs: [{ targets: [2], createdCell: function (td) { $(td).css({ 'white-space': 'normal', 'word-break': 'break-word', 'max-width': '300px' }); }}],
                        columns: stock_report_cols,
                        buttons: [
                    { text: '<i class="fa fa-filter"></i> Filters', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2', action: function () { $('#filterModal').modal('show'); }},
                    { extend: 'csv', text: '<i class="fa fa-file-csv" aria-hidden="true"></i> CSV', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2', exportOptions: { columns: ':visible' }, footer: true },
                    { extend: 'excel', text: '<i class="fa fa-file-excel" aria-hidden="true"></i> Excel', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2', exportOptions: { columns: ':visible' }, footer: true },
                    {
                        text: '<i class="fa fa-print" aria-hidden="true"></i> Print',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function (e, dt, node, config) {
                            // Build a simple HTML document from the visible DataTable data and print it
                            var exportData = dt.buttons.exportData({
                                columns: ':visible',
                                stripHtml: true
                            });

                            // Remove first column (Action) from export
                            if (exportData.header && exportData.header.length) {
                                exportData.header = exportData.header.slice(1);
                            }
                            if (exportData.body && exportData.body.length) {
                                exportData.body = exportData.body.map(function (row) {
                                    return row.slice(1);
                                });
                            }

                            var win = window.open('', '', 'height=800,width=1200');
                            if (!win) {
                                alert('Please allow popups for this site to print the stock report.');
                                return;
                            }

                            var doc = win.document;
                            doc.write('<html><head><title>Stock Report - Smokevana</title>');
                            doc.write('<style>');
                            doc.write('body{font-family: Arial, sans-serif;font-size:10px;margin:16px;}');
                            doc.write('h2{margin-bottom:12px;}');
                            doc.write('table{border-collapse:collapse;width:100%;}');
                            doc.write('th,td{border:1px solid #000;padding:4px;text-align:left;white-space:nowrap;}');
                            doc.write('thead th{background:#f0f0f0;font-weight:bold;}');
                            doc.write('tfoot td{font-weight:bold;background:#f9f9f9;}');
                            doc.write('</style></head><body>');
                            doc.write('<h2>Stock Report</h2>');
                            doc.write('<table><thead><tr>');

                            // Headers
                            exportData.header.forEach(function (header) {
                                doc.write('<th>' + header + '</th>');
                            });
                            doc.write('</tr></thead><tbody>');

                            // Body rows
                            exportData.body.forEach(function (row) {
                                doc.write('<tr>');
                                row.forEach(function (cell) {
                                    doc.write('<td>' + cell + '</td>');
                                });
                                doc.write('</tr>');
                            });
                            doc.write('</tbody></table></body></html>');
                            doc.close();
                            win.focus();
                            win.print();
                        }
                    },
                    { extend: 'colvis', text: '<i class="fa fa-columns" aria-hidden="true"></i> Columns', className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2' },
                ],
                initComplete: function() {
                    // Sync column widths between header and body tables
                    var api = this.api();
                    api.columns.adjust();
                    
                    // Force width sync after a short delay to ensure DOM is ready
                    setTimeout(function() {
                        syncStockReportColumnWidths();
                        api.columns.adjust();
                        // Setup horizontal scroll sync
                        syncStockReportHorizontalScroll();
                    }, 200);
                },
                fnDrawCallback: function(oSettings) { 
                    __currency_convert_recursively($('#stock_report_table'));
                    var api = this.api();
                    
                    // Sync column widths on each draw after content is rendered
                    setTimeout(function() {
                        syncStockReportColumnWidths();
                        api.columns.adjust();
                        // Re-setup scroll sync in case DOM changed
                        syncStockReportHorizontalScroll();
                    }, 50);
                },
                        "footerCallback": function(row, data, start, end, display) {
                    var footer_total_stock = 0, footer_total_sold = 0, footer_total_transfered = 0, total_adjusted = 0, total_stock_price = 0, footer_stock_value_by_sale_price = 0, total_potential_profit = 0, footer_total_mfg_stock = 0;
                            for (var r in data) {
                        footer_total_stock += $(data[r].stock).data('orig-value') ? parseFloat($(data[r].stock).data('orig-value')) : 0;
                        footer_total_sold += $(data[r].total_sold).data('orig-value') ? parseFloat($(data[r].total_sold).data('orig-value')) : 0;
                        footer_total_transfered += $(data[r].total_transfered).data('orig-value') ? parseFloat($(data[r].total_transfered).data('orig-value')) : 0;
                        total_adjusted += $(data[r].total_adjusted).data('orig-value') ? parseFloat($(data[r].total_adjusted).data('orig-value')) : 0;
                        total_stock_price += $(data[r].stock_price).data('orig-value') ? parseFloat($(data[r].stock_price).data('orig-value')) : 0;
                        footer_stock_value_by_sale_price += $(data[r].stock_value_by_sale_price).data('orig-value') ? parseFloat($(data[r].stock_value_by_sale_price).data('orig-value')) : 0;
                        total_potential_profit += $(data[r].potential_profit).data('orig-value') ? parseFloat($(data[r].potential_profit).data('orig-value')) : 0;
                        footer_total_mfg_stock += $(data[r].total_mfg_stock).data('orig-value') ? parseFloat($(data[r].total_mfg_stock).data('orig-value')) : 0;
                    }
                    $('.footer_total_stock').html(__currency_trans_from_en(footer_total_stock, false));
                    $('.footer_total_stock_price').html(__currency_trans_from_en(total_stock_price));
                    $('.footer_total_sold').html(__currency_trans_from_en(footer_total_sold, false));
                    $('.footer_total_transfered').html(__currency_trans_from_en(footer_total_transfered, false));
                    $('.footer_total_adjusted').html(__currency_trans_from_en(total_adjusted, false));
                    $('.footer_stock_value_by_sale_price').html(__currency_trans_from_en(footer_stock_value_by_sale_price));
                    $('.footer_potential_profit').html(__currency_trans_from_en(total_potential_profit));
                    if ($('th.current_stock_mfg').length) { $('.footer_total_mfg_stock').html(__currency_trans_from_en(footer_total_mfg_stock, false)); }
                }
                    });
                    stock_report_table_initialized = true;
                } else {
                    stock_report_table.ajax.reload();
                }
            } else {
        if (typeof product_table !== 'undefined') { product_table.ajax.reload(); }
            }
            $('.btn-default').removeClass('btn-default');
            $('.tw-dw-btn-outline').removeClass('btn');
        });

// Update product location
        $(document).on('click', '.update_product_location', function(e) {
            e.preventDefault();
            var selected_rows = getSelectedRows();
            if (selected_rows.length > 0) {
                $('input#selected_products').val(selected_rows);
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
        if (type == 'add') { modal.find('.remove_from_location_title').addClass('hide'); modal.find('.add_to_location_title').removeClass('hide'); }
        else if (type == 'remove') { modal.find('.add_to_location_title').addClass('hide'); modal.find('.remove_from_location_title').removeClass('hide'); }
                modal.modal('show');
        modal.find('#product_location').select2({ dropdownParent: modal });
                modal.find('#product_location').val('').change();
                modal.find('#update_type').val(type);
                modal.find('#products_to_update_location').val(selected_rows);
            } else {
                $('input#selected_products').val('');
                swal('@lang('lang_v1.no_row_selected')');
            }
        });

        $(document).on('submit', 'form#edit_product_location_form', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
        method: $(this).attr('method'), url: $(this).attr('action'), dataType: 'json', data: form.serialize(),
        beforeSend: function(xhr) { __disable_submit_button(form.find('button[type="submit"]')); },
                success: function(result) {
                    if (result.success == true) {
                        $('div#edit_product_location_modal').modal('hide');
                        toastr.success(result.msg);
                        product_table.ajax.reload();
                $('form#edit_product_location_form').find('button[type="submit"]').attr('disabled', false);
            } else { toastr.error(result.msg); }
        }
            });
        });
    </script>
@endsection

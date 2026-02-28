@extends('layouts.app')

@section('title', __('bookkeeping.chart_of_accounts'))

@section('css')
<style>
/* Chart of Accounts - Professional Purple Theme */
.coa-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 80px;
    position: relative;
    z-index: 1;
}

/* Ensure tables and content stay above footer */
.coa-page .coa-account-section {
    position: relative;
    z-index: 10;
}

.coa-page .coa-table {
    position: relative;
    z-index: 10;
    background: #fff;
}

/* Header Banner - Amazon style */
.coa-header-banner {
    background: #37475a;
    border-radius: 6px;
    padding: 22px 28px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
}

.coa-header-banner::before { display: none; }

.coa-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.coa-header-content h1 i {
    font-size: 28px;
    color: #fff !important;
}

.coa-header-content .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.coa-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}

.coa-header-actions .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    font-size: 14px;
    border: none;
    transition: all 0.3s ease;
}

.coa-header-actions .btn-light {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.4);
}

.coa-header-actions .btn-light:hover {
    background: rgba(255,255,255,0.3);
    color: #fff;
    transform: translateY(-2px);
}

.coa-header-actions .btn-amazon-orange {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}

.coa-header-actions .btn-amazon-orange:hover {
    color: #fff !important;
    opacity: 0.95;
}

.coa-header-actions .btn-group .dropdown-toggle-split {
    background: linear-gradient(to bottom, #E47911 0%, #d96d0f 100%) !important;
    border-color: #C7511F !important;
    color: #fff !important;
}

/* Summary Stats */
.coa-summary-stats {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 1400px) {
    .coa-summary-stats { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
    .coa-summary-stats { grid-template-columns: repeat(2, 1fr); }
}

.coa-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    border-left: 4px solid #e5e7eb;
    transition: all 0.3s ease;
}

.coa-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(139, 92, 246, 0.12);
}

.coa-stat-card.asset { border-left-color: #10b981; }
.coa-stat-card.liability { border-left-color: #ef4444; }
.coa-stat-card.equity { border-left-color: #3b82f6; }
.coa-stat-card.income { border-left-color: #8b5cf6; }
.coa-stat-card.expense { border-left-color: #f59e0b; }
.coa-stat-card.cogs { border-left-color: #6366f1; }

.coa-stat-count {
    font-size: 28px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
}

.coa-stat-card.asset .coa-stat-count { color: #059669; }
.coa-stat-card.liability .coa-stat-count { color: #dc2626; }
.coa-stat-card.equity .coa-stat-count { color: #2563eb; }
.coa-stat-card.income .coa-stat-count { color: #7c3aed; }
.coa-stat-card.expense .coa-stat-count { color: #d97706; }
.coa-stat-card.cogs .coa-stat-count { color: #4f46e5; }

.coa-stat-label {
    font-size: 13px;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    font-weight: 700;
    margin-top: 6px;
}

.coa-stat-balance {
    font-size: 15px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px solid #e5e7eb;
    color: #111827;
    letter-spacing: 0.2px;
}

/* Filters Section - Enhanced UI/UX */
.coa-filters {
    background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 28px;
    box-shadow: 0 4px 24px rgba(139, 92, 246, 0.12);
    border: 2px solid rgba(139, 92, 246, 0.1);
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 24px;
    align-items: end;
    position: relative;
    overflow: visible;
}

.coa-filters::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
}

.coa-filters .form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
}

.coa-filters .form-group label {
    font-size: 13px;
    font-weight: 700;
    color: #4b5563;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    gap: 8px;
    line-height: 1.4;
}

.coa-filters .form-group label i {
    font-size: 14px;
    color: #8b5cf6;
    opacity: 0.8;
}

.coa-filters .form-control,
.coa-filters .form-select {
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 500;
    color: #111827 !important;
    background-color: #ffffff !important;
    width: 100%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    line-height: 1.6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    min-height: 44px;
}

/* Ensure select text is always visible */
.coa-filters .form-select option {
    color: #111827 !important;
    background-color: #ffffff !important;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 500;
}

.coa-filters .form-select option:checked {
    background-color: #faf5ff !important;
    color: #7c3aed !important;
    font-weight: 600;
}

.coa-filters .form-select option:hover {
    background-color: #f3f4f6 !important;
}

.coa-filters .form-control:hover,
.coa-filters .form-select:hover {
    border-color: #c4b5fd;
    box-shadow: 0 2px 6px rgba(139, 92, 246, 0.08);
}

.coa-filters .form-control::placeholder {
    color: #6b7280 !important;
    font-weight: 400;
    opacity: 1 !important;
    font-size: 15px;
}

.coa-filters .form-control:focus,
.coa-filters .form-select:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15), 0 4px 12px rgba(139, 92, 246, 0.1);
    outline: none;
    background-color: #ffffff !important;
    color: #111827 !important;
    transform: translateY(-1px);
}

.coa-filters .form-control:focus::placeholder {
    color: #9ca3af !important;
    opacity: 0.7 !important;
}

.coa-filters .form-select {
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%238b5cf6' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 18px center;
    padding-right: 50px;
    background-size: 14px;
    color: #111827 !important;
    -webkit-text-fill-color: #111827 !important;
}

/* Force text color on select elements across all browsers */
.coa-filters select.form-select,
.coa-filters select.form-select option {
    -webkit-text-fill-color: #111827 !important;
    color: #111827 !important;
}

.coa-filters select.form-select option[value=""] {
    -webkit-text-fill-color: #6b7280 !important;
    color: #6b7280 !important;
}

/* Ensure selected value text is visible */
.coa-filters .form-select:not([value=""]) {
    color: #111827 !important;
    font-weight: 600;
}

.coa-filters .form-select[value=""] {
    color: #6b7280 !important;
}

.coa-filters .form-group:last-child {
    grid-column: 1 / -1;
}

.coa-filters .form-group:last-child .form-control {
    padding-left: 48px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 18px center;
    background-size: 18px;
}

.coa-filters .form-group:last-child .form-control:focus {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%238b5cf6' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
}

/* Filter active state indicator */
.coa-filters .form-control:not(:placeholder-shown) {
    border-color: #a78bfa;
    background-color: #faf5ff;
    color: #111827 !important;
}

.coa-filters .form-select:not([value=""]) {
    border-color: #a78bfa;
    background-color: #faf5ff;
    color: #111827 !important;
}

/* Ensure placeholder text is visible */
.coa-filters .form-control::placeholder {
    color: #6b7280 !important;
    opacity: 1 !important;
    font-weight: 400;
    font-size: 15px;
}

/* Clear filter button */
.coa-filter-clear {
    position: absolute;
    top: 28px;
    right: 32px;
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
    display: flex;
    align-items: center;
    gap: 6px;
}

.coa-filter-clear:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
}

.coa-filter-clear:active {
    transform: translateY(0);
}

/* Filter results count indicator */
.coa-filter-results {
    display: inline-block;
    background: #ede9fe;
    color: #7c3aed;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
    margin-left: 12px;
    vertical-align: middle;
}

/* Loading state for filters */
.coa-filters.loading .form-control,
.coa-filters.loading .form-select {
    opacity: 0.6;
    pointer-events: none;
}

/* Empty filter results message */
.coa-no-results {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    margin-top: 24px;
    display: none;
}

.coa-no-results.show {
    display: block;
}

.coa-no-results i {
    font-size: 64px;
    color: #ddd6fe;
    margin-bottom: 20px;
}

.coa-no-results h4 {
    color: #1e1b4b;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 12px;
}

.coa-no-results p {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 0;
}

/* Account Type Sections */
.coa-account-section {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    margin-bottom: 24px;
    overflow: visible;
}

.coa-account-section .coa-section-header {
    border-radius: 16px 16px 0 0;
}

.coa-account-section.collapsed .coa-section-header {
    border-radius: 16px;
}

.coa-section-header {
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.2s ease;
}

.coa-section-header:hover {
    background: #faf5ff;
}

.coa-section-header.asset { border-left: 4px solid #10b981; }
.coa-section-header.liability { border-left: 4px solid #ef4444; }
.coa-section-header.equity { border-left: 4px solid #3b82f6; }
.coa-section-header.income { border-left: 4px solid #8b5cf6; }
.coa-section-header.expense { border-left: 4px solid #f59e0b; }
.coa-section-header.cogs { border-left: 4px solid #6366f1; }

.coa-section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 17px;
    font-weight: 700;
    color: #111827;
    margin: 0;
    line-height: 1.5;
}

.coa-section-title i {
    font-size: 20px;
}

.coa-section-header.asset .coa-section-title i { color: #10b981; }
.coa-section-header.liability .coa-section-title i { color: #ef4444; }
.coa-section-header.equity .coa-section-title i { color: #3b82f6; }
.coa-section-header.income .coa-section-title i { color: #8b5cf6; }
.coa-section-header.expense .coa-section-title i { color: #f59e0b; }
.coa-section-header.cogs .coa-section-title i { color: #6366f1; }

.coa-section-badge {
    background: #ede9fe;
    color: #7c3aed;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
}

.coa-section-meta {
    display: flex;
    align-items: center;
    gap: 16px;
}

.coa-section-total {
    font-size: 17px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
    letter-spacing: 0.3px;
}

.coa-section-header.asset .coa-section-total { color: #059669; }
.coa-section-header.liability .coa-section-total { color: #dc2626; }
.coa-section-header.equity .coa-section-total { color: #2563eb; }
.coa-section-header.income .coa-section-total { color: #7c3aed; }
.coa-section-header.expense .coa-section-total { color: #d97706; }
.coa-section-header.cogs .coa-section-total { color: #4f46e5; }

.coa-toggle-icon {
    color: #9ca3af;
    transition: transform 0.3s ease;
}

.coa-section-header.collapsed .coa-toggle-icon {
    transform: rotate(-90deg);
}

/* Collapse container background */
.coa-account-section .collapse {
    background: #fff;
    border-radius: 0 0 16px 16px;
}

/* Account Table */
.coa-table {
    width: 100%;
    border-collapse: collapse;
}

.coa-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 14px 18px;
    text-align: left;
    line-height: 1.4;
}

.coa-table thead th:last-child {
    text-align: center;
}

.coa-table tbody td {
    padding: 16px 18px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 15px;
    color: #1f2937;
    vertical-align: middle;
    line-height: 1.6;
    font-weight: 500;
}

.coa-table tbody tr {
    transition: background 0.2s ease;
    background: #fff;
}

.coa-table tbody tr:hover {
    background: #faf5ff;
}

.coa-table tbody tr.sub-account {
    background: #fafafa;
}

.coa-table tbody tr.sub-account:hover {
    background: #f5f3ff;
}

.coa-table tbody tr:last-child td {
    border-bottom: none;
}

/* Ensure table has solid background */
.coa-table {
    background: #fff;
}

.coa-table tbody {
    background: #fff;
}

.coa-account-code {
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 14px;
    font-weight: 700;
    color: #4b5563;
    background: #f3f4f6;
    padding: 6px 12px;
    border-radius: 6px;
    display: inline-block;
    letter-spacing: 0.3px;
}

.coa-account-name {
    font-weight: 600;
    color: #111827;
    font-size: 15px;
    line-height: 1.5;
}

.coa-account-name .sub-indicator {
    color: #a5b4fc;
    margin-right: 6px;
}

.coa-system-badge {
    background: #dbeafe;
    color: #2563eb;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    margin-left: 8px;
}

.coa-inactive-badge {
    background: #f3f4f6;
    color: #6b7280;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
    margin-left: 8px;
}

.coa-balance {
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.2px;
}

.coa-balance.positive { color: #059669; }
.coa-balance.negative { color: #dc2626; }

/* Action Buttons */
.coa-actions-dropdown {
    position: relative;
}

.coa-actions-dropdown .btn {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
}

.coa-actions-dropdown .btn:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
}

.coa-actions-dropdown .dropdown-menu {
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    border: 1px solid #e5e7eb;
    padding: 8px;
    z-index: 1050;
    min-width: 160px;
}

/* Auto-detect if dropdown should open upward for rows near bottom */
.coa-table tbody tr:nth-last-child(-n+3) .coa-actions-dropdown .dropdown-menu {
    top: auto;
    bottom: 100%;
    margin-bottom: 2px;
    margin-top: 0;
}

.coa-actions-dropdown .dropdown-menu a {
    border-radius: 8px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.coa-actions-dropdown .dropdown-menu a:hover {
    background: #faf5ff;
    color: #7c3aed;
}

.coa-actions-dropdown .dropdown-menu a i {
    width: 18px;
    margin-right: 8px;
}

/* Ensure dropdown is visible when open */
.coa-actions-dropdown.open .dropdown-menu,
.coa-actions-dropdown .dropdown-menu.show {
    display: block;
}

/* Dropup styling for rows near bottom of table */
.coa-actions-dropdown.dropup .dropdown-menu {
    top: auto !important;
    bottom: 100% !important;
    margin-bottom: 2px;
    margin-top: 0;
}

.coa-actions-dropdown.dropup .dropdown-toggle::after {
    border-top: 0;
    border-bottom: 4px solid;
}

/* Empty State */
.coa-empty {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
}

.coa-empty i {
    font-size: 60px;
    color: #ddd6fe;
    margin-bottom: 20px;
}

.coa-empty h4 {
    color: #1e1b4b;
    margin-bottom: 10px;
}

.coa-empty p {
    color: #6b7280;
    margin-bottom: 24px;
}

.coa-empty .btn {
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
}

/* Animation */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.coa-account-section {
    animation: fadeInUp 0.4s ease forwards;
}

/* Responsive Design - Enhanced Mobile Experience */
@media (max-width: 1024px) {
    .coa-filters {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .coa-filter-clear {
        position: static;
        grid-column: 1 / -1;
        justify-self: start;
        margin-top: 8px;
    }
}

@media (max-width: 768px) {
    .coa-header-banner {
        flex-direction: column;
        text-align: center;
        padding: 24px;
    }
    
    .coa-header-actions {
        justify-content: center;
        width: 100%;
    }
    
    .coa-filters {
        grid-template-columns: 1fr;
        padding: 24px 20px;
        gap: 20px;
    }
    
    .coa-filters .form-group {
        width: 100%;
    }
    
    .coa-filters .form-control,
    .coa-filters .form-select {
        width: 100%;
        font-size: 16px; /* Prevent zoom on iOS */
        padding: 14px 18px;
    }
    
    .coa-filters .form-group:last-child .form-control {
        padding-left: 46px;
        background-position: left 16px center;
    }
    
    .coa-filter-clear {
        position: static;
        width: 100%;
        justify-self: stretch;
        margin-top: 4px;
    }
    
    .coa-summary-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .coa-table {
        font-size: 13px;
        display: block;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }
    
    .coa-table thead th,
    .coa-table tbody td {
        padding: 12px 10px;
        white-space: nowrap;
    }
    
    .coa-table thead th {
        font-size: 11px;
    }
    
    .coa-account-name {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Ensure dropdowns visible on mobile */
    .coa-actions-dropdown .dropdown-menu {
        position: fixed;
        z-index: 1060;
    }
}

@media (max-width: 480px) {
    .coa-filters {
        padding: 20px 16px;
    }
    
    .coa-filters .form-group label {
        font-size: 12px;
    }
    
    .coa-summary-stats {
        grid-template-columns: 1fr;
    }
    
    .coa-stat-card {
        padding: 16px;
    }
}
</style>
@endsection

@section('content')
<section class="content coa-page">
    
    <!-- Header Banner -->
    <div class="coa-header-banner">
        <div class="coa-header-content">
            <h1><i class="fas fa-sitemap"></i> Chart of Accounts</h1>
            <p class="subtitle">Manage all your bookkeeping accounts</p>
        </div>
        <div class="coa-header-actions">
            <div class="btn-group">
                <a href="{{ route('bookkeeping.accounts.create') }}" class="btn btn-amazon-orange">
                    <i class="fas fa-plus"></i> @lang('messages.add')
                </a>
                <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" onclick="toastr.info('Import feature coming soon')"><i class="fas fa-file-import text-info"></i> Import Accounts</a></li>
                    <li><a href="#" onclick="toastr.info('Export feature coming soon')"><i class="fas fa-file-export text-success"></i> Export Accounts</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#" onclick="toastr.info('Merge feature coming soon')"><i class="fas fa-compress-arrows-alt text-warning"></i> Merge Accounts</a></li>
                    <li><a href="#" onclick="toastr.info('Templates feature coming soon')"><i class="fas fa-th-large text-purple"></i> Industry Templates</a></li>
                </ul>
            </div>
            <a href="{{ route('bookkeeping.dashboard') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> @lang('messages.back')
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="coa-summary-stats">
        @php
            $typeIcons = [
                'asset' => 'fa-wallet',
                'liability' => 'fa-credit-card',
                'equity' => 'fa-landmark',
                'income' => 'fa-arrow-up',
                'expense' => 'fa-arrow-down',
                'cost_of_goods_sold' => 'fa-boxes'
            ];
        @endphp
        @foreach($accountTypes as $type => $typeLabel)
        @php
            $count = isset($groupedAccounts[$type]) ? $groupedAccounts[$type]->count() : 0;
            $totalBalance = isset($groupedAccounts[$type]) ? $groupedAccounts[$type]->sum('current_balance') : 0;
        @endphp
        <div class="coa-stat-card {{ str_replace('cost_of_goods_sold', 'cogs', $type) }}">
            <div class="coa-stat-count">{{ $count }}</div>
            <div class="coa-stat-label">{{ $typeLabel }}</div>
            <div class="coa-stat-balance">@format_currency($totalBalance)</div>
        </div>
        @endforeach
    </div>

    <!-- Filters Section - Enhanced UI/UX -->
    <div class="coa-filters">
        <div class="form-group">
            <label for="account_type_filter">
                <i class="fas fa-filter"></i>
                Account type
            </label>
            <select class="form-control form-select" id="account_type_filter" aria-label="Account type" style="color: #111827 !important; background-color: #ffffff !important;">
                <option value="" style="color: #6b7280; background-color: #ffffff;">All account types</option>
                @foreach($accountTypes as $type => $label)
                    <option value="{{ $type }}" style="color: #111827; background-color: #ffffff;">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="status_filter">
                <i class="fas fa-toggle-on"></i>
                Status
            </label>
            <select class="form-control form-select" id="status_filter" aria-label="Status" style="color: #111827 !important; background-color: #ffffff !important;">
                <option value="" style="color: #6b7280; background-color: #ffffff;">All statuses</option>
                <option value="active" style="color: #111827; background-color: #ffffff;">Active</option>
                <option value="inactive" style="color: #111827; background-color: #ffffff;">Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label for="search_filter">
                <i class="fas fa-search"></i>
                Search
            </label>
            <input type="text" 
                   class="form-control" 
                   id="search_filter" 
                   placeholder="Search accounts..."
                   aria-label="Search accounts"
                   autocomplete="off"
                   style="color: #111827 !important; background-color: #ffffff !important;">
        </div>
        <button type="button" class="coa-filter-clear" id="clear_filters" title="Clear all filters" style="display: none;">
            <i class="fas fa-times-circle"></i>
            <span>Clear Filters</span>
        </button>
    </div>

    <!-- Account Sections by Type -->
    @forelse($accountTypes as $type => $typeLabel)
        @if(isset($groupedAccounts[$type]) && $groupedAccounts[$type]->count() > 0)
        @php
            $typeClass = str_replace('cost_of_goods_sold', 'cogs', $type);
            $totalBalance = $groupedAccounts[$type]->sum('current_balance');
        @endphp
        <div class="coa-account-section" data-type="{{ $type }}">
            <div class="coa-section-header {{ $typeClass }}" data-toggle="collapse" data-target="#section_{{ $type }}">
                <h3 class="coa-section-title">
                    <i class="fas {{ $typeIcons[$type] ?? 'fa-folder' }}"></i>
                    {{ $typeLabel }}
                    <span class="coa-section-badge">{{ $groupedAccounts[$type]->count() }}</span>
                </h3>
                <div class="coa-section-meta">
                    <span class="coa-section-total">@format_currency($totalBalance)</span>
                    <i class="fas fa-chevron-down coa-toggle-icon"></i>
                </div>
            </div>
            <div class="collapse show" id="section_{{ $type }}">
                <table class="coa-table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Account Code</th>
                            <th>Account Name</th>
                            <th style="width: 20%;">Detail Type</th>
                            <th style="width: 15%; text-align: right;">Balance</th>
                            <th style="width: 12%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedAccounts[$type] as $account)
                        <tr class="account-row {{ !$account->is_active ? 'text-muted' : '' }}" 
                            data-name="{{ strtolower($account->name) }}"
                            data-code="{{ strtolower($account->account_code) }}"
                            data-active="{{ $account->is_active ? 'active' : 'inactive' }}">
                            <td>
                                <span class="coa-account-code">{{ $account->account_code }}</span>
                            </td>
                            <td>
                                <span class="coa-account-name">
                                    {{ $account->name }}
                                    @if($account->is_system_account)
                                        <span class="coa-system-badge"><i class="fas fa-lock"></i> System</span>
                                    @endif
                                    @if(!$account->is_active)
                                        <span class="coa-inactive-badge">Inactive</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span style="color: #4b5563; font-weight: 500; font-size: 14px;">{{ ucwords(str_replace('_', ' ', $account->detail_type ?? '-')) }}</span>
                            </td>
                            <td style="text-align: right;">
                                @php
                                    $balance = $account->current_balance ?? 0;
                                    $balanceClass = $balance >= 0 ? 'positive' : 'negative';
                                @endphp
                                <span class="coa-balance {{ $balanceClass }}">@format_currency($balance)</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="coa-actions-dropdown btn-group">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                        Actions <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="{{ route('bookkeeping.accounts.ledger', $account->id) }}">
                                                <i class="fas fa-book text-info"></i> @lang('bookkeeping.view_ledger')
                                            </a>
                                        </li>
                                        @if(!$account->is_system_account)
                                        <li>
                                            <a href="{{ route('bookkeeping.accounts.edit', $account->id) }}">
                                                <i class="fas fa-edit text-primary"></i> @lang('messages.edit')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="delete-account text-danger" data-id="{{ $account->id }}">
                                                <i class="fas fa-trash text-danger"></i> @lang('messages.delete')
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @if($account->children && $account->children->count() > 0)
                            @foreach($account->children as $child)
                            <tr class="account-row sub-account {{ !$child->is_active ? 'text-muted' : '' }}"
                                data-name="{{ strtolower($child->name) }}"
                                data-code="{{ strtolower($child->account_code) }}"
                                data-active="{{ $child->is_active ? 'active' : 'inactive' }}">
                                <td>
                                    <span class="coa-account-code">{{ $child->account_code }}</span>
                                </td>
                                <td style="padding-left: 40px;">
                                    <span class="coa-account-name">
                                        <span class="sub-indicator">↳</span>
                                        {{ $child->name }}
                                        @if(!$child->is_active)
                                            <span class="coa-inactive-badge">Inactive</span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ ucwords(str_replace('_', ' ', $child->detail_type ?? '-')) }}</span>
                                </td>
                                <td style="text-align: right;">
                                    @php
                                        $childBalance = $child->current_balance ?? 0;
                                        $childBalanceClass = $childBalance >= 0 ? 'positive' : 'negative';
                                    @endphp
                                    <span class="coa-balance {{ $childBalanceClass }}">@format_currency($childBalance)</span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="coa-actions-dropdown btn-group">
                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                            Actions <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="{{ route('bookkeeping.accounts.ledger', $child->id) }}">
                                                    <i class="fas fa-book text-info"></i> @lang('bookkeeping.view_ledger')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('bookkeeping.accounts.edit', $child->id) }}">
                                                    <i class="fas fa-edit text-primary"></i> @lang('messages.edit')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="delete-account text-danger" data-id="{{ $child->id }}">
                                                    <i class="fas fa-trash text-danger"></i> @lang('messages.delete')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @empty
        <div class="coa-empty">
            <i class="fas fa-sitemap"></i>
            <h4>@lang('bookkeeping.no_accounts_found')</h4>
            <p>@lang('bookkeeping.no_accounts_message')</p>
            <a href="{{ route('bookkeeping.accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> @lang('bookkeeping.create_first_account')
            </a>
        </div>
    @endforelse

    <!-- No Results Message - Hidden by default, shown only when filters return no results -->
    <div class="coa-no-results" style="display: none;">
        <i class="fas fa-search"></i>
        <h4>@lang('bookkeeping.no_accounts_found')</h4>
        <p>Try adjusting your filters or search terms to find accounts.</p>
    </div>

</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Ensure no-results message is hidden on page load
    $('.coa-no-results').hide();
    
    // Function to check if any filters are active
    function checkFiltersActive() {
        var accountType = $('#account_type_filter').val();
        var status = $('#status_filter').val();
        var search = $('#search_filter').val().trim();
        
        if (accountType || status || search) {
            $('#clear_filters').fadeIn(200);
        } else {
            $('#clear_filters').fadeOut(200);
        }
    }

    // Function to apply all filters
    function applyFilters() {
        var selectedType = $('#account_type_filter').val();
        var selectedStatus = $('#status_filter').val();
        var searchTerm = $('#search_filter').val().toLowerCase().trim();
        var totalVisibleRows = 0;

        // First, show all sections to allow row filtering
        $('.coa-account-section').show();

        // Filter rows within each section
        $('.account-row').each(function() {
            var $row = $(this);
            var $section = $row.closest('.coa-account-section');
            var sectionType = $section.data('type') || '';
            
            var name = ($row.data('name') || '').toString().toLowerCase();
            var code = ($row.data('code') || '').toString().toLowerCase();
            var isActive = ($row.data('active') || '').toString();
            
            // Also search in the visible text content
            var rowText = $row.text().toLowerCase();
            
            // Check type filter
            var matchesType = selectedType === '' || sectionType === selectedType;
            
            // Check status filter
            var matchesStatus = selectedStatus === '' || isActive === selectedStatus;
            
            // Check search filter - search in name, code, and visible text
            var matchesSearch = searchTerm === '' || 
                name.indexOf(searchTerm) !== -1 || 
                code.indexOf(searchTerm) !== -1 ||
                rowText.indexOf(searchTerm) !== -1;

            if (matchesType && matchesStatus && matchesSearch) {
                $row.show();
                totalVisibleRows++;
            } else {
                $row.hide();
            }
        });

        // Hide sections with no visible rows
        $('.coa-account-section').each(function() {
            var $section = $(this);
            var visibleRows = $section.find('.account-row:visible').length;
            if (visibleRows === 0) {
                $section.hide();
            } else {
                $section.show();
            }
        });

        // Show/hide "No Results" message - only when filters are active AND no results found
        var hasActiveFilters = selectedType !== '' || selectedStatus !== '' || searchTerm !== '';
        if (totalVisibleRows === 0 && hasActiveFilters) {
            $('.coa-no-results').fadeIn(200);
        } else {
            $('.coa-no-results').hide();
        }

        checkFiltersActive();
    }

    // Ensure text is visible in select elements
    function ensureSelectTextVisible() {
        $('#account_type_filter, #status_filter').each(function() {
            var $select = $(this);
            var selectedValue = $select.val();
            if (selectedValue === '') {
                $select.css({
                    'color': '#6b7280',
                    '-webkit-text-fill-color': '#6b7280'
                });
            } else {
                $select.css({
                    'color': '#111827',
                    '-webkit-text-fill-color': '#111827'
                });
            }
        });
    }

    // Filter by account type
    $('#account_type_filter').on('change', function() {
        ensureSelectTextVisible();
        applyFilters();
    });

    // Filter by status
    $('#status_filter').on('change', function() {
        ensureSelectTextVisible();
        applyFilters();
    });

    // Initialize text visibility on page load
    ensureSelectTextVisible();

    // Search filter with debounce
    var searchTimeout;
    $('#search_filter').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            applyFilters();
        }, 300);
    });

    // Clear all filters
    $('#clear_filters').on('click', function() {
        $('#account_type_filter').val('').trigger('change');
        $('#status_filter').val('').trigger('change');
        $('#search_filter').val('').trigger('keyup');
        ensureSelectTextVisible();
        $(this).fadeOut(200);
        
        // Focus on search input for better UX
        $('#search_filter').focus();
    });

    // Toggle collapse icon
    $('.coa-section-header').on('click', function() {
        $(this).toggleClass('collapsed');
    });

    // Delete account
    $(document).on('click', '.delete-account', function(e) {
        e.preventDefault();
        var accountId = $(this).data('id');
        
        swal({
            title: "@lang('messages.are_you_sure')",
            text: "@lang('bookkeeping.delete_account_warning')",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "{{ url('bookkeeping/accounts') }}/" + accountId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            location.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.msg || '@lang("messages.something_went_wrong")');
                    }
                });
            }
        });
    });

    // Dynamic dropdown positioning - open upward if near bottom of viewport
    $(document).on('show.bs.dropdown', '.coa-actions-dropdown', function() {
        var $dropdown = $(this);
        var $menu = $dropdown.find('.dropdown-menu');
        var $button = $dropdown.find('.dropdown-toggle');
        
        // Get button position relative to viewport
        var buttonRect = $button[0].getBoundingClientRect();
        var viewportHeight = $(window).height();
        var spaceBelow = viewportHeight - buttonRect.bottom;
        var menuHeight = 150; // Approximate dropdown height
        
        // If not enough space below, open upward
        if (spaceBelow < menuHeight) {
            $dropdown.addClass('dropup');
        } else {
            $dropdown.removeClass('dropup');
        }
    });

    // Reset dropdown direction when hidden
    $(document).on('hidden.bs.dropdown', '.coa-actions-dropdown', function() {
        $(this).removeClass('dropup');
    });
});
</script>
@endsection

@extends('layouts.app')
@section('title', 'Accounts Receivable')

@section('css')
<style>
/* Accounts Receivable - Enhanced UI/UX with Micro-interactions */
.ar-page { 
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%); 
    min-height: 100vh; 
    padding: 20px;
    padding-bottom: 40px; 
}

/* Header Banner with Enhanced Animation */
.ar-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px; 
    padding: 28px 32px; 
    margin-bottom: 24px;
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    flex-wrap: wrap; 
    gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25); 
    position: relative; 
    overflow: hidden;
    animation: slideDown 0.5s ease-out;
}
.ar-header-banner::before { 
    content: ''; 
    position: absolute; 
    top: -50%; 
    right: -10%; 
    width: 300px; 
    height: 300px; 
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); 
    border-radius: 50%; 
    animation: pulse 4s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.1; }
    50% { transform: scale(1.1); opacity: 0.15; }
}
.ar-header-banner h1, .ar-header-banner .subtitle, .ar-header-banner i { color: #fff !important; }
.ar-header-banner h1 { 
    font-size: 28px; 
    font-weight: 700; 
    margin: 0 0 6px 0; 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}
.ar-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }
.ar-btn-back { 
    background: rgba(255,255,255,0.15); 
    color: #fff; 
    border: 1px solid rgba(255,255,255,0.3); 
    padding: 12px 24px; 
    border-radius: 10px; 
    font-weight: 600; 
    font-size: 14px; 
    text-decoration: none; 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.ar-btn-back::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}
.ar-btn-back:hover::before {
    width: 300px;
    height: 300px;
}
.ar-btn-back:hover { 
    background: rgba(255,255,255,0.25); 
    color: #fff; 
    text-decoration: none; 
    transform: translateX(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Total AR Banner with Enhanced Styling */
.ar-total-banner {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    border-radius: 16px;
    padding: 24px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(5, 150, 105, 0.25);
    animation: slideUp 0.5s ease-out 0.1s both;
    position: relative;
    overflow: hidden;
}
.ar-total-banner::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    animation: shimmer 3s infinite;
}
@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}
.ar-total-main {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 1;
}
.ar-total-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    transition: transform 0.3s ease;
}
.ar-total-banner:hover .ar-total-icon {
    transform: scale(1.1) rotate(5deg);
}
.ar-total-info {}
.ar-total-label { color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 4px; }
.ar-total-value { color: #fff; font-size: 36px; font-weight: 700; font-family: 'SF Mono', Monaco, monospace; }
.ar-total-stats {
    display: flex;
    gap: 32px;
    position: relative;
    z-index: 1;
}
.ar-total-stat {
    text-align: center;
    padding-left: 32px;
    border-left: 1px solid rgba(255,255,255,0.2);
    transition: transform 0.2s ease;
}
.ar-total-stat:hover {
    transform: translateY(-2px);
}
.ar-total-stat:first-child { border-left: none; padding-left: 0; }
.ar-total-stat-value { color: #fff; font-size: 24px; font-weight: 700; transition: transform 0.2s ease; }
.ar-total-stat:hover .ar-total-stat-value {
    transform: scale(1.05);
}
.ar-total-stat-label { color: rgba(255,255,255,0.8); font-size: 12px; margin-top: 4px; }

/* Enhanced Filter Section */
.ar-filters-section {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1px solid rgba(139, 92, 246, 0.06);
    animation: slideUp 0.5s ease-out 0.2s both;
}

.ar-filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f1f5f9;
}

.ar-filters-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 600;
    color: #1e1b4b;
    margin: 0;
}

.ar-filters-title i {
    color: #7c3aed;
    font-size: 18px;
}

.ar-filters-toggle {
    background: #f3f4f6;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ar-filters-toggle:hover {
    background: #e5e7eb;
    color: #374151;
}

.ar-filters-toggle i {
    transition: transform 0.3s ease;
}

.ar-filters-toggle.collapsed i {
    transform: rotate(-90deg);
}

.ar-filters-body {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    transition: all 0.3s ease;
}

.ar-filters-body.collapsed {
    display: none;
}

.ar-filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ar-filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ar-filter-label i {
    color: #7c3aed;
    font-size: 13px;
}

.ar-filter-input,
.ar-filter-select {
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    background-color: #ffffff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
}

.ar-filter-input:focus,
.ar-filter-select:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    transform: translateY(-1px);
}

.ar-filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}

.ar-filter-btn {
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.ar-filter-btn-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.ar-filter-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
}

.ar-filter-btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.ar-filter-btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

.ar-filter-btn-export {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.ar-filter-btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
}

/* Aging Cards with Enhanced Interactions */
.ar-aging-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.ar-aging-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideUp 0.5s ease-out 0.2s both;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}
.ar-aging-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(124, 58, 237, 0.05), transparent);
    transition: left 0.5s ease;
}
.ar-aging-card:hover::before {
    left: 100%;
}
.ar-aging-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}
.ar-aging-card.current { border-left: 4px solid #10b981; }
.ar-aging-card.warning { border-left: 4px solid #f59e0b; }
.ar-aging-card.danger { border-left: 4px solid #ef4444; }
.ar-aging-card.critical { border-left: 4px solid #7c3aed; }
.ar-aging-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}
.ar-aging-card:hover .ar-aging-icon {
    transform: scale(1.1) rotate(5deg);
}
.ar-aging-card.current .ar-aging-icon { background: #d1fae5; color: #059669; }
.ar-aging-card.warning .ar-aging-icon { background: #fef3c7; color: #d97706; }
.ar-aging-card.danger .ar-aging-icon { background: #fee2e2; color: #dc2626; }
.ar-aging-card.critical .ar-aging-icon { background: #ede9fe; color: #7c3aed; }
.ar-aging-info { flex: 1; min-width: 0; }
.ar-aging-value { 
    font-size: 20px; 
    font-weight: 700; 
    color: #1e1b4b; 
    font-family: 'SF Mono', Monaco, monospace;
    transition: transform 0.2s ease;
}
.ar-aging-card:hover .ar-aging-value {
    transform: scale(1.05);
}
.ar-aging-label { font-size: 12px; color: #6b7280; margin-top: 4px; }
.ar-aging-percent { 
    font-size: 11px; 
    padding: 4px 10px; 
    border-radius: 12px; 
    background: #f3f4f6; 
    color: #6b7280; 
    margin-top: 6px;
    display: inline-block;
    transition: all 0.2s ease;
}
.ar-aging-card:hover .ar-aging-percent {
    background: #e5e7eb;
    transform: translateX(2px);
}

/* Enhanced Cards */
.ar-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    overflow: hidden;
    animation: slideUp 0.5s ease-out 0.3s both;
    border: 1px solid rgba(139, 92, 246, 0.06);
    transition: box-shadow 0.3s ease;
}
.ar-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
.ar-card-header {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}
.ar-card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
}
.ar-card-title {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ar-card-badge {
    background: rgba(255,255,255,0.2);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    transition: all 0.2s ease;
}
.ar-card-badge:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.05);
}
.ar-card-body { padding: 0; }

/* Enhanced Table */
.ar-table {
    width: 100%;
    border-collapse: collapse;
}
.ar-table th {
    background: #f8f9fe;
    padding: 14px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #f1f5f9;
    position: sticky;
    top: 0;
    z-index: 10;
}
.ar-table td {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #374151;
    transition: background 0.2s ease;
}
.ar-table tbody tr {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.ar-table tbody tr::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 3px;
    height: 100%;
    background: #7c3aed;
    transform: scaleY(0);
    transition: transform 0.2s ease;
}
.ar-table tbody tr:hover::before {
    transform: scaleY(1);
}
.ar-table tbody tr:hover {
    background: #f5f3ff;
    transform: translateX(2px);
}
.ar-customer-name {
    font-weight: 600;
    color: #1e1b4b;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.2s ease;
}
.ar-table tbody tr:hover .ar-customer-name {
    color: #7c3aed;
}
.ar-customer-name i { 
    color: #8b5cf6;
    transition: transform 0.2s ease;
}
.ar-table tbody tr:hover .ar-customer-name i {
    transform: scale(1.2) rotate(5deg);
}
.ar-customer-code { 
    font-size: 11px; 
    color: #9ca3af; 
    margin-top: 4px;
    font-family: 'SF Mono', Monaco, monospace;
}
.ar-amount { 
    font-family: 'SF Mono', Monaco, monospace; 
    font-weight: 500;
    transition: transform 0.2s ease;
}
.ar-table tbody tr:hover .ar-amount {
    transform: scale(1.05);
}
.ar-amount.positive { color: #059669; }
.ar-action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    position: relative;
    overflow: hidden;
}
.ar-action-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(124, 58, 237, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.4s, height 0.4s;
}
.ar-action-btn:hover::before {
    width: 100px;
    height: 100px;
}
.ar-action-btn.view {
    background: #ede9fe;
    color: #7c3aed;
}
.ar-action-btn.view:hover {
    background: #7c3aed;
    color: #fff;
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

/* Grid Layout */
.ar-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
}

/* Enhanced Sidebar Cards */
.ar-sidebar-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 20px;
    animation: slideUp 0.5s ease-out 0.4s both;
    border: 1px solid rgba(139, 92, 246, 0.06);
    transition: all 0.3s ease;
}
.ar-sidebar-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.ar-sidebar-header {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    padding: 14px 20px;
    position: relative;
}
.ar-sidebar-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
}
.ar-sidebar-header h3 {
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ar-sidebar-body { padding: 16px 20px; }

/* Enhanced Payment Item */
.ar-payment-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    border-radius: 12px;
    margin-bottom: 10px;
    background: #f8f9fe;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}
.ar-payment-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 3px;
    height: 100%;
    background: #10b981;
    transform: scaleY(0);
    transition: transform 0.3s ease;
}
.ar-payment-item:hover::before {
    transform: scaleY(1);
}
.ar-payment-item:hover { 
    background: #ede9fe; 
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.ar-payment-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #059669;
    font-size: 16px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.ar-payment-item:hover .ar-payment-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}
.ar-payment-info { flex: 1; min-width: 0; }
.ar-payment-customer { 
    font-weight: 600; 
    color: #1e1b4b; 
    font-size: 13px;
    transition: color 0.2s ease;
}
.ar-payment-item:hover .ar-payment-customer {
    color: #7c3aed;
}
.ar-payment-details { 
    font-size: 11px; 
    color: #6b7280; 
    margin-top: 4px;
}
.ar-payment-amount { 
    font-weight: 700; 
    color: #059669; 
    font-family: 'SF Mono', Monaco, monospace; 
    font-size: 14px;
    transition: transform 0.2s ease;
}
.ar-payment-item:hover .ar-payment-amount {
    transform: scale(1.1);
}

/* Empty State */
.ar-empty {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}
.ar-empty i { 
    font-size: 40px; 
    margin-bottom: 12px; 
    opacity: 0.5;
    animation: float 3s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.ar-empty p { margin: 0; font-size: 14px; }

/* Animations */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* DataTables Enhanced Styling */
.ar-dt-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    flex-wrap: wrap;
    gap: 12px;
    background: #f8f9fe;
    border-bottom: 1px solid #f1f5f9;
}
.ar-dt-header .dataTables_length select {
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    transition: all 0.2s ease;
}
.ar-dt-header .dataTables_length select:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    outline: none;
}
.ar-dt-header .dataTables_filter input {
    padding: 10px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    min-width: 250px;
    transition: all 0.2s ease;
}
.ar-dt-header .dataTables_filter input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    outline: none;
}
.ar-dt-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-top: 1px solid #f1f5f9;
    background: #f8f9fe;
}
.ar-dt-footer .dataTables_paginate .paginate_button {
    padding: 8px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    margin: 0 2px;
    transition: all 0.2s ease;
}
.ar-dt-footer .dataTables_paginate .paginate_button:hover {
    background: #ede9fe;
    border-color: #7c3aed;
    transform: translateY(-2px);
}
.ar-dt-footer .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    border-color: #7c3aed;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

/* Loading State */
.ar-loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(124, 58, 237, 0.3);
    border-radius: 50%;
    border-top-color: #7c3aed;
    animation: spin 1s ease-in-out infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Tooltip */
.ar-tooltip {
    position: relative;
    cursor: help;
}
.ar-tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 6px 12px;
    background: #1e1b4b;
    color: #fff;
    font-size: 12px;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    margin-bottom: 8px;
}
.ar-tooltip:hover::after {
    opacity: 1;
}

/* Responsive */
@media (max-width: 1200px) {
    .ar-grid { grid-template-columns: 1fr; }
    .ar-aging-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .ar-page {
        padding: 15px;
    }
    .ar-aging-grid { grid-template-columns: 1fr; }
    .ar-filters-body {
        grid-template-columns: 1fr;
    }
    .ar-filter-actions {
        flex-direction: column;
    }
    .ar-filter-btn {
        width: 100%;
        justify-content: center;
    }
    .ar-header-banner {
        flex-direction: column;
        text-align: center;
    }
    .ar-total-banner {
        flex-direction: column;
        text-align: center;
    }
    .ar-total-stats {
        flex-direction: column;
        gap: 16px;
    }
    .ar-total-stat {
        border-left: none;
        border-top: 1px solid rgba(255,255,255,0.2);
        padding: 16px 0 0;
    }
    .ar-total-stat:first-child {
        border-top: none;
        padding-top: 0;
    }
}
</style>
@endsection

@section('content')
<section class="content ar-page">
    <!-- Header Banner -->
    <div class="ar-header-banner">
        <div>
            <h1><i class="fas fa-hand-holding-usd"></i> Accounts Receivable</h1>
            <p class="subtitle">Customer balances & aging analysis following US GAAP/IFRS standards</p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('bookkeeping.credit-notes.create') }}" class="ar-btn-back" style="background: rgba(255,255,255,0.95); color: #7c3aed; border-color: transparent;">
                <i class="fas fa-file-invoice-dollar"></i> New Credit Note
            </a>
            <a href="{{ route('bookkeeping.credit-notes.index') }}" class="ar-btn-back">
                <i class="fas fa-list"></i> Credit Notes
            </a>
        <a href="{{ route('bookkeeping.dashboard') }}" class="ar-btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
    </div>

    <!-- Total AR Banner -->
    <div class="ar-total-banner">
        <div class="ar-total-main">
            <div class="ar-total-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="ar-total-info">
                <div class="ar-total-label">Total Accounts Receivable</div>
                <div class="ar-total-value">${{ number_format($summary['total_ar'] ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="ar-total-stats">
            <div class="ar-total-stat">
                <div class="ar-total-stat-value">{{ $summary['total_customers'] ?? 0 }}</div>
                <div class="ar-total-stat-label">Customers</div>
            </div>
            @if(($summary['total_credit_notes'] ?? 0) > 0)
            <div class="ar-total-stat" style="border-left-color: rgba(255,100,100,0.5);">
                <div class="ar-total-stat-value" style="color: #fca5a5;">${{ number_format($summary['total_credit_notes'] ?? 0, 2) }}</div>
                <div class="ar-total-stat-label">Credit Notes</div>
            </div>
            @endif
            @if($arAccount)
            <div class="ar-total-stat">
                <div class="ar-total-stat-value">{{ $arAccount->account_code }}</div>
                <div class="ar-total-stat-label">GL Account</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Aging Cards -->
    <div class="ar-aging-grid">
        <div class="ar-aging-card current" onclick="filterByAging('current')">
            <div class="ar-aging-icon"><i class="fas fa-check-circle"></i></div>
            <div class="ar-aging-info">
                <div class="ar-aging-value">${{ number_format($summary['current_0_30'] ?? 0, 2) }}</div>
                <div class="ar-aging-label">Current (0-30 Days)</div>
                @if($summary['total_ar'] > 0)
                <span class="ar-aging-percent">{{ number_format(($summary['current_0_30'] / $summary['total_ar']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
        <div class="ar-aging-card warning" onclick="filterByAging('warning')">
            <div class="ar-aging-icon"><i class="fas fa-clock"></i></div>
            <div class="ar-aging-info">
                <div class="ar-aging-value">${{ number_format($summary['days_31_60'] ?? 0, 2) }}</div>
                <div class="ar-aging-label">31-60 Days</div>
                @if($summary['total_ar'] > 0)
                <span class="ar-aging-percent">{{ number_format(($summary['days_31_60'] / $summary['total_ar']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
        <div class="ar-aging-card danger" onclick="filterByAging('danger')">
            <div class="ar-aging-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="ar-aging-info">
                <div class="ar-aging-value">${{ number_format($summary['days_61_90'] ?? 0, 2) }}</div>
                <div class="ar-aging-label">61-90 Days</div>
                @if($summary['total_ar'] > 0)
                <span class="ar-aging-percent">{{ number_format(($summary['days_61_90'] / $summary['total_ar']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
        <div class="ar-aging-card critical" onclick="filterByAging('critical')">
            <div class="ar-aging-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="ar-aging-info">
                <div class="ar-aging-value">${{ number_format($summary['over_90'] ?? 0, 2) }}</div>
                <div class="ar-aging-label">Over 90 Days</div>
                @if($summary['total_ar'] > 0)
                <span class="ar-aging-percent">{{ number_format(($summary['over_90'] / $summary['total_ar']) * 100, 1) }}%</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Filters Section -->
    <div class="ar-filters-section">
        <div class="ar-filters-header">
            <h3 class="ar-filters-title"><i class="fas fa-filter"></i> Filters & Search</h3>
            <button type="button" class="ar-filters-toggle" id="filtersToggle" onclick="toggleFilters()">
                <i class="fas fa-chevron-down"></i> <span>Toggle</span>
            </button>
        </div>
        <div class="ar-filters-body" id="filtersBody">
            <div class="ar-filter-group">
                <label class="ar-filter-label"><i class="fas fa-search"></i> Search Customer</label>
                <input type="text" id="filterSearch" class="ar-filter-input" placeholder="Customer name or code...">
            </div>
            <div class="ar-filter-group">
                <label class="ar-filter-label"><i class="fas fa-dollar-sign"></i> Min Amount</label>
                <input type="number" id="filterMinAmount" class="ar-filter-input" placeholder="0.00" step="0.01" min="0">
            </div>
            <div class="ar-filter-group">
                <label class="ar-filter-label"><i class="fas fa-dollar-sign"></i> Max Amount</label>
                <input type="number" id="filterMaxAmount" class="ar-filter-input" placeholder="No limit" step="0.01" min="0">
            </div>
            <div class="ar-filter-group">
                <label class="ar-filter-label"><i class="fas fa-calendar-alt"></i> Aging Bucket</label>
                <select id="filterAging" class="ar-filter-select">
                    <option value="">All Aging</option>
                    <option value="current">Current (0-30 Days)</option>
                    <option value="31-60">31-60 Days</option>
                    <option value="61-90">61-90 Days</option>
                    <option value="over-90">Over 90 Days</option>
                </select>
            </div>
            <div class="ar-filter-actions">
                <button type="button" class="ar-filter-btn ar-filter-btn-primary" onclick="applyFilters()">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <button type="button" class="ar-filter-btn ar-filter-btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button type="button" class="ar-filter-btn ar-filter-btn-export" onclick="exportData()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Balances Table -->
    <div class="ar-card" style="margin-bottom: 24px;">
        <div class="ar-card-header">
            <h3 class="ar-card-title"><i class="fas fa-users"></i> Customer Balances</h3>
            <span class="ar-card-badge" id="customerCount">{{ $customers->count() }} Customers</span>
        </div>
        <div class="ar-card-body" style="padding: 16px;">
            @if($customers->count() > 0)
            <table class="ar-table" id="ar-customers-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th style="text-align: right;">Total Invoiced</th>
                        <th style="text-align: right;">Total Paid</th>
                        <th style="text-align: right;">Credit Notes</th>
                        <th style="text-align: right;">Balance Due</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr data-customer="{{ strtolower($customer->display_name) }}" 
                        data-amount="{{ $customer->balance_due }}"
                        data-aging-current="{{ $customer->current_0_30 ?? 0 }}"
                        data-aging-31-60="{{ $customer->days_31_60 ?? 0 }}"
                        data-aging-61-90="{{ $customer->days_61_90 ?? 0 }}"
                        data-aging-over-90="{{ $customer->over_90 ?? 0 }}">
                        <td>
                            <div class="ar-customer-name"><i class="fas fa-user-circle"></i> {{ $customer->display_name }}</div>
                            @if($customer->customer_code)<div class="ar-customer-code">{{ $customer->customer_code }}</div>@endif
                        </td>
                        <td style="text-align: right;" data-order="{{ $customer->total_invoice }}">
                            <span class="ar-amount">${{ number_format($customer->total_invoice, 2) }}</span>
                        </td>
                        <td style="text-align: right;" data-order="{{ $customer->invoice_received }}">
                            <span class="ar-amount positive">${{ number_format($customer->invoice_received, 2) }}</span>
                        </td>
                        <td style="text-align: right;" data-order="{{ $customer->credit_notes_total ?? 0 }}">
                            @if(($customer->credit_notes_total ?? 0) > 0)
                            <span class="ar-amount" style="color: #dc2626;">-${{ number_format($customer->credit_notes_total, 2) }}</span>
                            @else
                            <span class="ar-amount" style="color: #9ca3af;">$0.00</span>
                            @endif
                        </td>
                        <td style="text-align: right;" data-order="{{ $customer->balance_due }}">
                            <span class="ar-amount" style="font-weight: 700; color: #059669;">${{ number_format($customer->balance_due, 2) }}</span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$customer->id]) }}?type=customer" 
                               class="ar-action-btn view" 
                               title="View Customer Details"
                               data-tooltip="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f5f3ff; font-weight: 700;">
                        <td><strong>TOTAL</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($customers->sum('total_invoice'), 2) }}</strong></td>
                        <td style="text-align: right;"><strong>${{ number_format($customers->sum('invoice_received'), 2) }}</strong></td>
                        <td style="text-align: right; color: #dc2626;"><strong>-${{ number_format($summary['total_credit_notes'] ?? 0, 2) }}</strong></td>
                        <td style="text-align: right; color: #059669;"><strong>${{ number_format($summary['total_ar'], 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
            <div class="ar-empty">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                <p>No outstanding receivables!</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="ar-grid">
        <!-- Journal Entries -->
        <div class="ar-card">
            <div class="ar-card-header">
                <h3 class="ar-card-title"><i class="fas fa-book" style="color: #a78bfa;"></i> AR Journal Entries</h3>
                <a href="{{ route('bookkeeping.journal.index') }}" class="ar-btn-back" style="padding: 8px 16px; font-size: 12px;">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="ar-card-body" style="padding: 0;">
                @if(isset($arJournalEntries) && $arJournalEntries->count() > 0)
                <table class="ar-table">
                    <thead>
                        <tr>
                            <th>Entry #</th>
                            <th>Date</th>
                            <th>Memo</th>
                            <th style="text-align: right;">Debit</th>
                            <th style="text-align: right;">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arJournalEntries as $entry)
                        <tr>
                            <td><a href="{{ route('bookkeeping.journal.show', $entry->id) }}" style="color: #7c3aed; font-weight: 600; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#6d28d9'" onmouseout="this.style.color='#7c3aed'">{{ $entry->entry_number }}</a></td>
                            <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('M d, Y') }}</td>
                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $entry->memo ?? '-' }}</td>
                            <td style="text-align: right;">@if($entry->ar_debit > 0)<span style="color: #059669;">${{ number_format($entry->ar_debit, 2) }}</span>@else-@endif</td>
                            <td style="text-align: right;">@if($entry->ar_credit > 0)<span style="color: #dc2626;">${{ number_format($entry->ar_credit, 2) }}</span>@else-@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="ar-empty" style="padding: 40px;">
                    <i class="fas fa-book-open" style="color: #a78bfa;"></i>
                    <p>No AR journal entries found.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Recent Payments -->
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-header">
                    <h3><i class="fas fa-money-bill-wave"></i> Recent Payments</h3>
                </div>
                <div class="ar-sidebar-body">
                    @forelse($recentPayments as $payment)
                    <div class="ar-payment-item">
                        <div class="ar-payment-icon"><i class="fas fa-check"></i></div>
                        <div class="ar-payment-info">
                            <div class="ar-payment-customer">{{ $payment->display_name }}</div>
                            <div class="ar-payment-details">{{ $payment->invoice_no }} &bull; {{ \Carbon\Carbon::parse($payment->paid_on)->format('M d') }}</div>
                        </div>
                        <div class="ar-payment-amount">${{ number_format($payment->amount, 2) }}</div>
                    </div>
                    @empty
                    <div class="ar-empty" style="padding: 30px;"><i class="fas fa-inbox"></i><p>No recent payments</p></div>
                    @endforelse
                </div>
            </div>

            <!-- Outstanding Invoices -->
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-header" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                    <h3><i class="fas fa-file-invoice"></i> Outstanding Invoices</h3>
                </div>
                <div class="ar-sidebar-body">
                    @forelse($topOutstandingInvoices as $invoice)
                    <div class="ar-payment-item" style="background: #fef3c7;">
                        <div class="ar-payment-icon" style="background: #fde68a; color: #d97706;"><i class="fas fa-file-alt"></i></div>
                        <div class="ar-payment-info">
                            <div class="ar-payment-customer">{{ $invoice->invoice_no }}</div>
                            <div class="ar-payment-details">{{ $invoice->display_name }} &bull; {{ $invoice->days_outstanding }} days</div>
                        </div>
                        <div class="ar-payment-amount" style="color: #d97706;">${{ number_format($invoice->balance, 2) }}</div>
                    </div>
                    @empty
                    <div class="ar-empty" style="padding: 30px;"><i class="fas fa-check-circle" style="color: #10b981;"></i><p>All invoices paid!</p></div>
                    @endforelse
                </div>
            </div>

            <!-- GL Account Info -->
            @if($arAccount)
            <div class="ar-sidebar-card">
                <div class="ar-sidebar-header" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <h3><i class="fas fa-landmark"></i> GL Account</h3>
                </div>
                <div class="ar-sidebar-body">
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; transition: background 0.2s ease;" onmouseover="this.style.background='#f8f9fe'" onmouseout="this.style.background='transparent'">
                        <span style="color: #6b7280;">Account Code</span>
                        <span style="font-weight: 600; color: #1e1b4b;">{{ $arAccount->account_code }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; transition: background 0.2s ease;" onmouseover="this.style.background='#f8f9fe'" onmouseout="this.style.background='transparent'">
                        <span style="color: #6b7280;">Name</span>
                        <span style="font-weight: 600; color: #1e1b4b;">{{ $arAccount->name }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; transition: background 0.2s ease;" onmouseover="this.style.background='#f8f9fe'" onmouseout="this.style.background='transparent'">
                        <span style="color: #6b7280;">Balance</span>
                        <span style="font-weight: 700; color: #059669; font-family: 'SF Mono', Monaco, monospace;">${{ number_format($arAccount->current_balance ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
let table;
$(document).ready(function() {
    if ($('#ar-customers-table').length) {
        table = $('#ar-customers-table').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[4, 'desc']],
            columnDefs: [{ orderable: false, targets: [5] }],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers...",
                lengthMenu: "Show _MENU_",
                info: "Showing _START_ to _END_ of _TOTAL_",
            },
            dom: '<"ar-dt-header"lf>rt<"ar-dt-footer"ip>',
            drawCallback: function() {
                updateCustomerCount();
            }
        });
    }
});

function toggleFilters() {
    const body = document.getElementById('filtersBody');
    const toggle = document.getElementById('filtersToggle');
    body.classList.toggle('collapsed');
    toggle.classList.toggle('collapsed');
}

function applyFilters() {
    if (!table) return;
    
    const search = $('#filterSearch').val().toLowerCase();
    const minAmount = parseFloat($('#filterMinAmount').val()) || 0;
    const maxAmount = parseFloat($('#filterMaxAmount').val()) || Infinity;
    const aging = $('#filterAging').val();
    
    // Custom filter function
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            const row = table.row(dataIndex).node();
            const customer = $(row).data('customer') || '';
            const amount = parseFloat($(row).data('amount')) || 0;
            
            // Search filter
            if (search && !customer.includes(search)) {
                return false;
            }
            
            // Amount filter
            if (amount < minAmount || amount > maxAmount) {
                return false;
            }
            
            // Aging filter
            if (aging) {
                const agingMap = {
                    'current': 'aging-current',
                    '31-60': 'aging-31-60',
                    '61-90': 'aging-61-90',
                    'over-90': 'aging-over-90'
                };
                const agingKey = agingMap[aging];
                const agingValue = parseFloat($(row).data(agingKey)) || 0;
                if (agingValue <= 0) {
                    return false;
                }
            }
            
            return true;
        }
    );
    
    table.draw();
    updateCustomerCount();
}

function resetFilters() {
    $('#filterSearch').val('');
    $('#filterMinAmount').val('');
    $('#filterMaxAmount').val('');
    $('#filterAging').val('');
    
    // Remove custom filters
    $.fn.dataTable.ext.search.pop();
    table.draw();
    updateCustomerCount();
}

function filterByAging(type) {
    const agingMap = {
        'current': 'current',
        'warning': '31-60',
        'danger': '61-90',
        'critical': 'over-90'
    };
    
    $('#filterAging').val(agingMap[type] || '');
    applyFilters();
}

function updateCustomerCount() {
    if (table) {
        const count = table.rows({search: 'applied'}).count();
        $('#customerCount').text(count + ' Customers');
    }
}

function exportData() {
    if (!table) return;
    
    // Get filtered data
    const data = table.rows({search: 'applied'}).data();
    let csv = 'Customer,Total Invoiced,Total Paid,Credit Notes,Balance Due\n';
    
    data.each(function(row) {
        const cells = $(row);
        const customer = cells.eq(0).text().trim();
        const invoiced = cells.eq(1).text().trim();
        const paid = cells.eq(2).text().trim();
        const credits = cells.eq(3).text().trim();
        const due = cells.eq(4).text().trim();
        csv += `"${customer}","${invoiced}","${paid}","${credits}","${due}"\n`;
    });
    
    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'accounts_receivable_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Real-time search
$('#filterSearch').on('keyup', function() {
    applyFilters();
});

// Enter key support
$('.ar-filter-input, .ar-filter-select').on('keypress', function(e) {
    if (e.which === 13) {
        applyFilters();
    }
});
</script>
@endsection

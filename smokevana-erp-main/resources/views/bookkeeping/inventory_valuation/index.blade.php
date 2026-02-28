@extends('layouts.app')
@section('title', 'Inventory Valuation')

@section('css')
<style>
/* Inventory Valuation - Professional Purple Theme */
.iv-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 40px;
}

/* Header Banner - Amazon style */
.iv-header-banner {
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

.iv-header-banner::before { display: none; }

.iv-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.iv-header-content h1 i {
    font-size: 28px;
    color: #fff !important;
}

.iv-header-content .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.iv-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}

.iv-header-actions .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    font-size: 14px;
    border: none;
    transition: all 0.3s ease;
}

.iv-header-actions .btn-light {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.4);
}

.iv-header-actions .btn-light:hover {
    background: rgba(255,255,255,0.3);
    color: #fff;
    transform: translateY(-2px);
}

.iv-header-actions .btn-amazon-orange {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}

.iv-header-actions .btn-amazon-orange:hover {
    color: #fff !important;
    opacity: 0.95;
}

/* Summary Stats */
.iv-summary-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 1024px) {
    .iv-summary-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .iv-summary-stats { grid-template-columns: 1fr; }
}

.iv-stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    border-left: 4px solid #e5e7eb;
    transition: all 0.3s ease;
}

.iv-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(139, 92, 246, 0.12);
}

.iv-stat-card.units { border-left-color: #3b82f6; }
.iv-stat-card.cost { border-left-color: #2563eb; }
.iv-stat-card.retail { border-left-color: #10b981; }

.iv-stat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.iv-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.iv-stat-card.units .iv-stat-icon {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #2563eb;
}

.iv-stat-card.cost .iv-stat-icon {
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
    color: #1e40af;
}

.iv-stat-card.retail .iv-stat-icon {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #059669;
}

.iv-stat-label {
    font-size: 13px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    font-weight: 700;
}

.iv-stat-value {
    font-size: 28px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
    color: #1e1b4b;
    margin-top: 8px;
    line-height: 1.2;
}

.iv-stat-card.units .iv-stat-value { color: #2563eb; }
.iv-stat-card.cost .iv-stat-value { color: #1e40af; }
.iv-stat-card.retail .iv-stat-value { color: #059669; }

/* Main Content Cards */
.iv-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 1024px) {
    .iv-content-grid { grid-template-columns: 1fr; }
}

.iv-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    overflow: hidden;
}

.iv-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f3f4f6;
    background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.iv-card-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1e1b4b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.iv-card-header h3 i {
    color: #8b5cf6;
}

.iv-card-body {
    padding: 24px;
}

/* Calculate Form */
.iv-calculate-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.iv-form-group {
    margin-bottom: 0;
}

.iv-form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.iv-form-group .form-control,
.iv-form-group .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    background-color: #ffffff;
    transition: all 0.3s ease;
}

.iv-form-group .form-control:focus,
.iv-form-group .form-select:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    outline: none;
}

.iv-form-group textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

.iv-checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.iv-checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.iv-checkbox-group label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
}

.iv-btn-calculate {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
    color: #fff;
    border: 1px solid #C7511F;
    border-radius: 10px;
    padding: 14px 24px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.iv-btn-calculate:hover {
    opacity: 0.95;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(199, 81, 31, 0.3);
}

.iv-btn-calculate:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* History Table */
.iv-history-table {
    width: 100%;
    border-collapse: collapse;
}

.iv-history-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 14px 16px;
    text-align: left;
}

.iv-history-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #374151;
}

.iv-history-table tbody tr:hover {
    background: #faf5ff;
}

.iv-history-table tbody tr:last-child td {
    border-bottom: none;
}

.iv-empty-state {
    text-align: center;
    padding: 60px 20px;
}

.iv-empty-state i {
    font-size: 64px;
    color: #ddd6fe;
    margin-bottom: 20px;
}

.iv-empty-state h4 {
    color: #1e1b4b;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

.iv-empty-state p {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 24px;
}

/* Loading State */
.iv-loading {
    text-align: center;
    padding: 40px;
}

.iv-loading i {
    font-size: 32px;
    color: #8b5cf6;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Breakdown Tables */
.iv-breakdown-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 1024px) {
    .iv-breakdown-section { grid-template-columns: 1fr; }
}

.iv-breakdown-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
}

.iv-breakdown-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 12px 14px;
    text-align: left;
    position: sticky;
    top: 0;
}

.iv-breakdown-table thead th:first-child {
    border-radius: 8px 0 0 0;
}

.iv-breakdown-table thead th:last-child {
    border-radius: 0 8px 0 0;
    text-align: right;
}

.iv-breakdown-table tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 13px;
    color: #374151;
}

.iv-breakdown-table tbody tr:hover {
    background: #faf5ff;
}

.iv-breakdown-table tbody tr:last-child td {
    border-bottom: none;
}

.iv-breakdown-table tbody td:last-child {
    text-align: right;
    font-weight: 600;
    font-family: 'SF Mono', Monaco, monospace;
}

.iv-breakdown-table .category-name,
.iv-breakdown-table .brand-name {
    font-weight: 600;
    color: #1e1b4b;
}

.iv-breakdown-table .category-name.unassigned,
.iv-breakdown-table .brand-name.unassigned {
    color: #9ca3af;
    font-style: italic;
}

.iv-breakdown-table .units {
    color: #2563eb;
    font-weight: 500;
}

.iv-breakdown-table .cost-value {
    color: #1e40af;
}

.iv-breakdown-table .retail-value {
    color: #059669;
}

.iv-breakdown-total {
    background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    font-weight: 700;
    border-top: 2px solid #8b5cf6;
}

.iv-breakdown-total td {
    padding: 14px !important;
    font-size: 14px !important;
}

.iv-breakdown-empty {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.iv-breakdown-empty i {
    font-size: 32px;
    margin-bottom: 12px;
    opacity: 0.5;
}

/* Top Selling Products */
.iv-top-selling {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.iv-top-product-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.iv-top-product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
    border-color: #8b5cf6;
}

.iv-top-product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.iv-top-product-rank {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.iv-top-product-name {
    font-size: 15px;
    font-weight: 600;
    color: #1e1b4b;
    margin-bottom: 8px;
    line-height: 1.4;
}

.iv-top-product-meta {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 12px;
}

.iv-top-product-stats {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.iv-top-stat {
    flex: 1;
    text-align: center;
}

.iv-top-stat-label {
    font-size: 10px;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.iv-top-stat-value {
    font-size: 14px;
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
}

.iv-top-stat-value.quantity {
    color: #2563eb;
}

.iv-top-stat-value.revenue {
    color: #059669;
}

/* Enhanced Reporting Section */
.iv-reporting-section {
    margin-bottom: 24px;
}

.iv-report-filters {
    background: #fff;
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.iv-report-filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 180px;
}

.iv-report-filter-group label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.iv-report-filter-group select {
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #111827;
    background-color: #ffffff;
    transition: all 0.3s ease;
}

.iv-report-filter-group select:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    outline: none;
}

.iv-report-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.iv-btn-filter {
    padding: 10px 20px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.iv-btn-filter:hover {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.iv-btn-reset {
    padding: 10px 20px;
    background: #f3f4f6;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.iv-btn-reset:hover {
    background: #e5e7eb;
    color: #374151;
}

.iv-report-table-wrapper {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    overflow: hidden;
}

.iv-report-table-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.iv-report-table-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: #1e1b4b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.iv-report-table {
    width: 100%;
    border-collapse: collapse;
}

.iv-report-table thead th {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 12px 16px;
    text-align: left;
    position: sticky;
    top: 0;
    z-index: 10;
}

.iv-report-table thead th:last-child {
    text-align: right;
}

.iv-report-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 13px;
    color: #374151;
}

.iv-report-table tbody tr:hover {
    background: #faf5ff;
}

.iv-report-table tbody tr:last-child td {
    border-bottom: none;
}

.iv-report-table tbody td:last-child {
    text-align: right;
    font-weight: 600;
    font-family: 'SF Mono', Monaco, monospace;
}

.iv-report-table .product-name {
    font-weight: 600;
    color: #1e1b4b;
}

.iv-report-table .product-sku {
    font-size: 11px;
    color: #9ca3af;
    font-family: 'SF Mono', Monaco, monospace;
}

.iv-report-table .category-badge,
.iv-report-table .brand-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.iv-report-table .category-badge {
    background: #ede9fe;
    color: #7c3aed;
}

.iv-report-table .brand-badge {
    background: #dbeafe;
    color: #2563eb;
}

.iv-report-table .units {
    color: #2563eb;
    font-weight: 600;
}

.iv-report-table .cost-value {
    color: #1e40af;
}

.iv-report-table .retail-value {
    color: #059669;
}

.iv-report-summary {
    background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    font-weight: 700;
    border-top: 2px solid #8b5cf6;
}

.iv-report-summary td {
    padding: 14px 16px !important;
    font-size: 14px !important;
}

/* Responsive */
@media (max-width: 768px) {
    .iv-top-selling {
        grid-template-columns: 1fr;
    }

    .iv-report-filters {
        flex-direction: column;
    }

    .iv-report-filter-group {
        width: 100%;
    }

    .iv-report-actions {
        width: 100%;
        margin-left: 0;
    }

    .iv-report-table {
        font-size: 12px;
    }

    .iv-report-table thead th,
    .iv-report-table tbody td {
        padding: 10px 12px;
    }
}
</style>
@endsection

@section('content')
<section class="content iv-page">
    
    <!-- Header Banner -->
    <div class="iv-header-banner">
        <div class="iv-header-content">
            <h1><i class="fas fa-boxes"></i> Inventory Valuation</h1>
            <p class="subtitle">Track and calculate your inventory value</p>
        </div>
        <div class="iv-header-actions">
            <a href="{{ route('bookkeeping.dashboard') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="iv-summary-stats">
        <div class="iv-stat-card units">
            <div class="iv-stat-header">
                <div class="iv-stat-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <div class="iv-stat-label">Total Units</div>
                    <div class="iv-stat-value">{{ number_format($stockSummary->total_units ?? 0, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="iv-stat-card cost">
            <div class="iv-stat-header">
                <div class="iv-stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <div class="iv-stat-label">Cost Value</div>
                    <div class="iv-stat-value">${{ number_format($stockSummary->total_cost_value ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="iv-stat-card retail">
            <div class="iv-stat-header">
                <div class="iv-stat-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <div class="iv-stat-label">Retail Value</div>
                    <div class="iv-stat-value">${{ number_format($stockSummary->total_retail_value ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="iv-content-grid">
        <!-- Calculate Valuation Card -->
        <div class="iv-card">
            <div class="iv-card-header">
                <h3><i class="fas fa-calculator"></i> Calculate Valuation</h3>
            </div>
            <div class="iv-card-body">
                <form id="calculate_valuation_form" class="iv-calculate-form">
                    @csrf
                    <div class="iv-form-group">
                        <label>Valuation Method</label>
                        <select name="valuation_method" class="form-control form-select" required>
                            @foreach(\App\Models\InventoryValuation::getValuationMethods() as $method => $label)
                                <option value="{{ $method }}" {{ $method == 'fifo' ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="iv-form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" class="form-control" placeholder="Add any notes about this valuation..."></textarea>
                    </div>
                    <div class="iv-form-group">
                        <div class="iv-checkbox-group">
                            <input type="checkbox" name="create_journal_entry" id="create_journal_entry" value="1">
                            <label for="create_journal_entry">Create journal entry for inventory adjustment</label>
                        </div>
                    </div>
                    <button type="submit" class="iv-btn-calculate" id="calculate_btn">
                        <i class="fas fa-calculator"></i> Calculate Valuation
                    </button>
                </form>
            </div>
        </div>

        <!-- Latest Valuation Card -->
        <div class="iv-card">
            <div class="iv-card-header">
                <h3><i class="fas fa-history"></i> Latest Valuation</h3>
            </div>
            <div class="iv-card-body">
                @if($latestValuation)
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Date</div>
                        <div style="font-size: 16px; font-weight: 600; color: #1e1b4b;">
                            {{ $latestValuation->valuation_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Method</div>
                        <div style="font-size: 14px; color: #374151;">
                            {{ \App\Models\InventoryValuation::getValuationMethods()[$latestValuation->valuation_method] ?? $latestValuation->valuation_method }}
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Cost Value</div>
                        <div style="font-size: 18px; font-weight: 700; color: #2563eb;">
                            ${{ number_format($latestValuation->total_cost_value, 2) }}
                        </div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Retail Value</div>
                        <div style="font-size: 18px; font-weight: 700; color: #059669;">
                            ${{ number_format($latestValuation->total_retail_value, 2) }}
                        </div>
                    </div>
                    @if($latestValuation->notes)
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Notes</div>
                        <div style="font-size: 13px; color: #6b7280;">
                            {{ $latestValuation->notes }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="iv-empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>No Valuation Yet</h4>
                        <p>Calculate your first inventory valuation to get started.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Opening Stock Breakdown by Category and Brand -->
    <div class="iv-card" style="margin-bottom: 24px;">
        <div class="iv-card-header">
            <div>
                <h3 style="margin: 0 0 4px 0;"><i class="fas fa-chart-pie"></i> Opening Stock Breakdown</h3>
                <p style="margin: 0; font-size: 12px; color: #6b7280;">Current inventory value broken down by category and brand for reporting</p>
            </div>
        </div>
    </div>

    <div class="iv-breakdown-section">
        <!-- By Category -->
        <div class="iv-card">
            <div class="iv-card-header">
                <h3><i class="fas fa-tags"></i> By Category</h3>
                <button type="button" class="btn btn-sm btn-light" onclick="exportBreakdown('category')" style="font-size: 12px; text-decoration: none; border: none; cursor: pointer; padding: 6px 12px;">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
            <div class="iv-card-body" style="padding: 0;">
                @if($stockByCategory && $stockByCategory->count() > 0)
                    <div style="max-height: 500px; overflow-y: auto;">
                        <table class="iv-breakdown-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Units</th>
                                    <th>Cost Value</th>
                                    <th>Retail Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $catTotalUnits = 0;
                                    $catTotalCost = 0;
                                    $catTotalRetail = 0;
                                @endphp
                                @foreach($stockByCategory as $category)
                                    @php
                                        $catTotalUnits += $category->total_units ?? 0;
                                        $catTotalCost += $category->total_cost_value ?? 0;
                                        $catTotalRetail += $category->total_retail_value ?? 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="category-name {{ $category->category_name == 'Unassigned' ? 'unassigned' : '' }}">
                                                {{ $category->category_name }}
                                            </span>
                                        </td>
                                        <td class="units">{{ number_format($category->total_units ?? 0, 0) }}</td>
                                        <td class="cost-value">${{ number_format($category->total_cost_value ?? 0, 2) }}</td>
                                        <td class="retail-value">${{ number_format($category->total_retail_value ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="iv-breakdown-total">
                                    <td><strong>Total</strong></td>
                                    <td class="units"><strong>{{ number_format($catTotalUnits, 0) }}</strong></td>
                                    <td class="cost-value"><strong>${{ number_format($catTotalCost, 2) }}</strong></td>
                                    <td class="retail-value"><strong>${{ number_format($catTotalRetail, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="iv-breakdown-empty">
                        <i class="fas fa-tags"></i>
                        <p>No category data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- By Brand -->
        <div class="iv-card">
            <div class="iv-card-header">
                <h3><i class="fas fa-certificate"></i> By Brand</h3>
                <button type="button" class="btn btn-sm btn-light" onclick="exportBreakdown('brand')" style="font-size: 12px; text-decoration: none; border: none; cursor: pointer; padding: 6px 12px;">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
            <div class="iv-card-body" style="padding: 0;">
                @if($stockByBrand && $stockByBrand->count() > 0)
                    <div style="max-height: 500px; overflow-y: auto;">
                        <table class="iv-breakdown-table">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th>Units</th>
                                    <th>Cost Value</th>
                                    <th>Retail Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $brandTotalUnits = 0;
                                    $brandTotalCost = 0;
                                    $brandTotalRetail = 0;
                                @endphp
                                @foreach($stockByBrand as $brand)
                                    @php
                                        $brandTotalUnits += $brand->total_units ?? 0;
                                        $brandTotalCost += $brand->total_cost_value ?? 0;
                                        $brandTotalRetail += $brand->total_retail_value ?? 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="brand-name {{ $brand->brand_name == 'Unassigned' ? 'unassigned' : '' }}">
                                                {{ $brand->brand_name }}
                                            </span>
                                        </td>
                                        <td class="units">{{ number_format($brand->total_units ?? 0, 0) }}</td>
                                        <td class="cost-value">${{ number_format($brand->total_cost_value ?? 0, 2) }}</td>
                                        <td class="retail-value">${{ number_format($brand->total_retail_value ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="iv-breakdown-total">
                                    <td><strong>Total</strong></td>
                                    <td class="units"><strong>{{ number_format($brandTotalUnits, 0) }}</strong></td>
                                    <td class="cost-value"><strong>${{ number_format($brandTotalCost, 2) }}</strong></td>
                                    <td class="retail-value"><strong>${{ number_format($brandTotalRetail, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="iv-breakdown-empty">
                        <i class="fas fa-certificate"></i>
                        <p>No brand data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    @if($topSellingProducts && $topSellingProducts->count() > 0)
    <div class="iv-card">
        <div class="iv-card-header">
            <div>
                <h3><i class="fas fa-fire"></i> Top Selling Products (Last 30 Days)</h3>
                <p style="margin: 0; font-size: 12px; color: #6b7280;">Best performing products by revenue</p>
            </div>
        </div>
        <div class="iv-card-body">
            <div class="iv-top-selling">
                @foreach($topSellingProducts as $index => $product)
                <div class="iv-top-product-card">
                    <div class="iv-top-product-rank">#{{ $index + 1 }}</div>
                    <div class="iv-top-product-name">{{ $product->product_name }}</div>
                    <div class="iv-top-product-meta">
                        <div><i class="fas fa-tag"></i> {{ $product->category_name }}</div>
                        <div><i class="fas fa-certificate"></i> {{ $product->brand_name }}</div>
                    </div>
                    <div class="iv-top-product-stats">
                        <div class="iv-top-stat">
                            <div class="iv-top-stat-label">Sold</div>
                            <div class="iv-top-stat-value quantity">{{ number_format($product->total_quantity_sold, 0) }}</div>
                        </div>
                        <div class="iv-top-stat">
                            <div class="iv-top-stat-label">Revenue</div>
                            <div class="iv-top-stat-value revenue">${{ number_format($product->total_revenue, 2) }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Enhanced Inventory Reporting -->
    <div class="iv-reporting-section">
        <div class="iv-card">
            <div class="iv-card-header">
                <div>
                    <h3><i class="fas fa-chart-bar"></i> Detailed Inventory Report</h3>
                    <p style="margin: 0; font-size: 12px; color: #6b7280;">Filter and analyze inventory by category and brand combinations</p>
                </div>
            </div>
            <div class="iv-card-body" style="padding: 0;">
                <!-- Filters -->
                <div class="iv-report-filters">
                    <div class="iv-report-filter-group">
                        <label><i class="fas fa-tags"></i> Category</label>
                        <select id="report_category_filter" class="form-control">
                            <option value="">All Categories</option>
                            <option value="0">Unassigned</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="iv-report-filter-group">
                        <label><i class="fas fa-certificate"></i> Brand</label>
                        <select id="report_brand_filter" class="form-control">
                            <option value="">All Brands</option>
                            <option value="0">Unassigned</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="iv-report-filter-group">
                        <label><i class="fas fa-sort"></i> Sort By</label>
                        <select id="report_sort_filter" class="form-control">
                            <option value="name">Product Name</option>
                            <option value="units">Units (High to Low)</option>
                            <option value="cost">Cost Value (High to Low)</option>
                            <option value="retail">Retail Value (High to Low)</option>
                        </select>
                    </div>
                    <div class="iv-report-actions">
                        <button type="button" class="iv-btn-filter" onclick="applyReportFilters()">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <button type="button" class="iv-btn-reset" onclick="resetReportFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="button" class="iv-btn-reset" onclick="exportReportTable()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="iv-report-table-wrapper">
                    <div class="iv-report-table-header">
                        <h3><i class="fas fa-list"></i> Inventory Details</h3>
                        <span id="report_count" style="font-size: 12px; color: #6b7280;">{{ $inventoryBreakdown->count() }} products</span>
                    </div>
                    <div style="max-height: 600px; overflow-y: auto;">
                        <table class="iv-report-table" id="inventory_report_table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Units</th>
                                    <th>Cost Value</th>
                                    <th>Retail Value</th>
                                </tr>
                            </thead>
                            <tbody id="report_table_body">
                                @php
                                    $reportTotalUnits = 0;
                                    $reportTotalCost = 0;
                                    $reportTotalRetail = 0;
                                @endphp
                                @foreach($inventoryBreakdown as $item)
                                    @php
                                        $reportTotalUnits += $item->total_units ?? 0;
                                        $reportTotalCost += $item->total_cost_value ?? 0;
                                        $reportTotalRetail += $item->total_retail_value ?? 0;
                                    @endphp
                                    <tr class="report-row" 
                                        data-category-id="{{ $item->category_id }}"
                                        data-brand-id="{{ $item->brand_id }}"
                                        data-units="{{ $item->total_units ?? 0 }}"
                                        data-cost="{{ $item->total_cost_value ?? 0 }}"
                                        data-retail="{{ $item->total_retail_value ?? 0 }}"
                                        data-name="{{ strtolower($item->product_name) }}">
                                        <td>
                                            <div class="product-name">{{ $item->product_name }}</div>
                                        </td>
                                        <td>
                                            <div class="product-sku">{{ $item->sku ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="category-badge">{{ $item->category_name }}</span>
                                        </td>
                                        <td>
                                            <span class="brand-badge">{{ $item->brand_name }}</span>
                                        </td>
                                        <td class="units">{{ number_format($item->total_units ?? 0, 0) }}</td>
                                        <td class="cost-value">${{ number_format($item->total_cost_value ?? 0, 2) }}</td>
                                        <td class="retail-value">${{ number_format($item->total_retail_value ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="iv-report-summary" id="report_summary_row">
                                    <td colspan="4"><strong>Total</strong></td>
                                    <td class="units"><strong id="summary_units">{{ number_format($reportTotalUnits, 0) }}</strong></td>
                                    <td class="cost-value"><strong id="summary_cost">${{ number_format($reportTotalCost, 2) }}</strong></td>
                                    <td class="retail-value"><strong id="summary_retail">${{ number_format($reportTotalRetail, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Valuation History -->
    <div class="iv-card">
        <div class="iv-card-header">
            <h3><i class="fas fa-list"></i> Valuation History</h3>
            <a href="{{ route('bookkeeping.inventory.history') }}" class="btn btn-sm btn-light" style="font-size: 12px; text-decoration: none;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="iv-card-body">
            @if($valuationHistory && $valuationHistory->count() > 0)
                <table class="iv-history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Units</th>
                            <th>Cost Value</th>
                            <th>Retail Value</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($valuationHistory as $valuation)
                        <tr>
                            <td>{{ $valuation->valuation_date->format('M d, Y') }}</td>
                            <td>{{ \App\Models\InventoryValuation::getValuationMethods()[$valuation->valuation_method] ?? $valuation->valuation_method }}</td>
                            <td>{{ number_format($valuation->total_units, 0) }}</td>
                            <td style="font-weight: 600; color: #2563eb;">${{ number_format($valuation->total_cost_value, 2) }}</td>
                            <td style="font-weight: 600; color: #059669;">${{ number_format($valuation->total_retail_value, 2) }}</td>
                            <td>{{ $valuation->createdBy ? $valuation->createdBy->first_name . ' ' . $valuation->createdBy->last_name : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="iv-empty-state">
                    <i class="fas fa-history"></i>
                    <h4>No History Available</h4>
                    <p>Your valuation history will appear here after you calculate valuations.</p>
                </div>
            @endif
        </div>
    </div>

</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Calculate Valuation Form Submission
    $('#calculate_valuation_form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = $('#calculate_btn');
        var originalText = submitBtn.html();
        
        // Disable submit button
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Calculating...');
        
        $.ajax({
            url: "{{ route('bookkeeping.inventory.calculate') }}",
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg || 'Inventory valuation calculated successfully.');
                    // Reload page after 1.5 seconds
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'An error occurred while calculating valuation.');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'An error occurred while calculating the valuation';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join('<br>');
                }
                toastr.error(errorMsg);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Store original inventory breakdown data
    var originalBreakdownData = {!! json_encode($inventoryBreakdown) !!};

    // Apply report filters
    function applyReportFilters() {
        var categoryId = $('#report_category_filter').val();
        var brandId = $('#report_brand_filter').val();
        var sortBy = $('#report_sort_filter').val();
        
        var visibleRows = $('.report-row').filter(function() {
            var $row = $(this);
            var rowCategoryId = $row.data('category-id');
            var rowBrandId = $row.data('brand-id');
            
            // Match category: empty = all, specific ID = match, or 0 = unassigned
            var matchCategory = !categoryId || 
                (categoryId && rowCategoryId == categoryId) || 
                (categoryId == '0' && rowCategoryId == 0);
            
            // Match brand: empty = all, specific ID = match, or 0 = unassigned
            var matchBrand = !brandId || 
                (brandId && rowBrandId == brandId) || 
                (brandId == '0' && rowBrandId == 0);
            
            return matchCategory && matchBrand;
        });
        
        // Hide all rows first
        $('.report-row').hide();
        
        // Show matching rows
        visibleRows.show();
        
        // Sort rows
        var $tbody = $('#report_table_body');
        var rows = visibleRows.toArray();
        
        rows.sort(function(a, b) {
            var $a = $(a), $b = $(b);
            var valA, valB;
            
            switch(sortBy) {
                case 'units':
                    valA = parseFloat($a.data('units')) || 0;
                    valB = parseFloat($b.data('units')) || 0;
                    return valB - valA;
                case 'cost':
                    valA = parseFloat($a.data('cost')) || 0;
                    valB = parseFloat($b.data('cost')) || 0;
                    return valB - valA;
                case 'retail':
                    valA = parseFloat($a.data('retail')) || 0;
                    valB = parseFloat($b.data('retail')) || 0;
                    return valB - valA;
                default:
                    valA = $a.data('name') || '';
                    valB = $b.data('name') || '';
                    return valA.localeCompare(valB);
            }
        });
        
        // Reorder rows (keep summary row at bottom)
        var $summaryRow = $('#report_summary_row');
        $summaryRow.detach();
        rows.forEach(function(row) {
            $tbody.append(row);
        });
        $tbody.append($summaryRow);
        
        // Update summary
        updateReportSummary(visibleRows);
        
        // Update count
        $('#report_count').text(visibleRows.length + ' products');
    }

    // Update report summary totals
    function updateReportSummary(rows) {
        var totalUnits = 0, totalCost = 0, totalRetail = 0;
        
        rows.each(function() {
            totalUnits += parseFloat($(this).data('units')) || 0;
            totalCost += parseFloat($(this).data('cost')) || 0;
            totalRetail += parseFloat($(this).data('retail')) || 0;
        });
        
        $('#summary_units').text(totalUnits.toLocaleString('en-US', {maximumFractionDigits: 0}));
        $('#summary_cost').text('$' + totalCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summary_retail').text('$' + totalRetail.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    // Reset report filters
    function resetReportFilters() {
        $('#report_category_filter').val('');
        $('#report_brand_filter').val('');
        $('#report_sort_filter').val('name');
        
        $('.report-row').show();
        var allRows = $('.report-row');
        updateReportSummary(allRows);
        $('#report_count').text(allRows.length + ' products');
        
        // Reset sort
        var $tbody = $('#report_table_body');
        var rows = $('.report-row').toArray();
        rows.sort(function(a, b) {
            var valA = $(a).data('name') || '';
            var valB = $(b).data('name') || '';
            return valA.localeCompare(valB);
        });
        var $summaryRow = $('#report_summary_row');
        $summaryRow.detach();
        rows.forEach(function(row) {
            $tbody.append(row);
        });
        $tbody.append($summaryRow);
    }

    // Export report table
    function exportReportTable() {
        var table = document.getElementById('inventory_report_table');
        var visibleRows = $('.report-row:visible');
        
        if (visibleRows.length === 0) {
            toastr.warning('No data to export. Please adjust your filters.');
            return;
        }
        
        // Create CSV content
        var csv = [];
        
        // Header
        var header = [];
        $(table.querySelectorAll('thead th')).each(function() {
            header.push('"' + $(this).text().trim() + '"');
        });
        csv.push(header.join(','));
        
        // Data rows
        visibleRows.each(function() {
            var row = [];
            $(this).find('td').each(function() {
                var text = $(this).text().trim().replace(/"|'/g, '');
                row.push('"' + text + '"');
            });
            csv.push(row.join(','));
        });
        
        // Summary row
        var summaryRow = [];
        $('#report_summary_row').find('td').each(function() {
            var text = $(this).text().trim().replace(/"|'/g, '');
            summaryRow.push('"' + text + '"');
        });
        csv.push(summaryRow.join(','));
        
        // Download CSV
        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'inventory_detailed_report_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        toastr.success('Report exported successfully');
    }

    // Auto-apply filters on change
    $('#report_category_filter, #report_brand_filter').on('change', function() {
        applyReportFilters();
    });

    // Export breakdown data
    function exportBreakdown(type) {
        var tableId = type === 'category' ? 'category_breakdown' : 'brand_breakdown';
        var table = document.querySelector(type === 'category' ? '.iv-breakdown-section .iv-card:first-child .iv-breakdown-table' : '.iv-breakdown-section .iv-card:last-child .iv-breakdown-table');
        
        if (!table) {
            toastr.error('No data available to export');
            return;
        }

        // Create CSV content
        var csv = [];
        var rows = table.querySelectorAll('tr');
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (var j = 0; j < cols.length; j++) {
                var data = cols[j].innerText.replace(/"|'/g, '');
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(','));
        }

        // Download CSV
        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'inventory_' + type + '_breakdown_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        toastr.success('Export started');
    }
});
</script>
@endsection

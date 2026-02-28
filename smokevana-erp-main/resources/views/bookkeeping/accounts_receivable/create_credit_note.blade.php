@extends('layouts.app')
@section('title', 'Create Credit Note')

@section('css')
<style>
/* Credit Note Create - Professional Interactive UI */
.cn-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding: 20px;
    padding-bottom: 60px;
}

/* Header Banner */
.cn-header-banner {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(220, 38, 38, 0.25);
    position: relative;
    overflow: hidden;
    animation: slideDown 0.5s ease-out;
}

.cn-header-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.cn-header-banner h1,
.cn-header-banner .subtitle,
.cn-header-banner i { color: #fff !important; }

.cn-header-banner h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.cn-header-banner .subtitle { font-size: 14px; opacity: 0.9; margin: 0; }

.cn-btn-back {
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
    transition: all 0.3s ease;
}

.cn-btn-back:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
    text-decoration: none;
    transform: translateX(-3px);
}

/* Main Form Container */
.cn-form-container {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 24px;
    animation: slideUp 0.5s ease-out 0.1s both;
}

@media (max-width: 1200px) {
    .cn-form-container { grid-template-columns: 1fr; }
}

/* Card Styling */
.cn-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: visible;
    border: 1px solid rgba(220, 38, 38, 0.08);
    transition: box-shadow 0.3s ease;
}

.cn-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.cn-card-header {
    padding: 18px 24px;
    position: relative;
    border-radius: 16px 16px 0 0;
    border-bottom: 3px solid #dc2626;
}

.cn-card-header h3 {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cn-card-header h3 i { color: #f87171; }

.cn-card-body { 
    padding: 24px;
    overflow: visible;
}

/* Form Styling */
.cn-form-section {
    margin-bottom: 28px;
    position: relative;
    overflow: visible;
}

.cn-form-section:last-child { margin-bottom: 0; }

.cn-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e1b4b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f1f5f9;
}

.cn-section-title i { color: #dc2626; font-size: 16px; }

.cn-form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
}

.cn-form-row.single { grid-template-columns: 1fr; }

@media (max-width: 768px) {
    .cn-form-row { grid-template-columns: 1fr; }
}

.cn-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.cn-form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 6px;
}

.cn-form-group label .required {
    color: #dc2626;
    font-weight: 700;
}

.cn-form-group label i {
    color: #8b5cf6;
    font-size: 12px;
}

.cn-input,
.cn-select,
.cn-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    background: #fff;
    transition: all 0.3s ease;
}

.cn-input:focus,
.cn-select:focus,
.cn-textarea:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
}

.cn-input::placeholder { color: #9ca3af; }

.cn-textarea {
    min-height: 100px;
    resize: vertical;
}

.cn-input-money {
    position: relative;
}

.cn-input-money::before {
    content: '$';
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-weight: 600;
    font-size: 16px;
}

.cn-input-money .cn-input {
    padding-left: 32px;
    font-family: 'SF Mono', Monaco, monospace;
    font-size: 18px;
    font-weight: 600;
}

/* Customer Select with Search */
.cn-customer-select-wrapper {
    position: relative;
}

.cn-customer-search {
    position: relative;
}

.cn-customer-search .cn-input {
    padding-right: 40px;
}

.cn-customer-search-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.cn-customer-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 2px solid #dc2626;
    border-top: none;
    border-radius: 0 0 10px 10px;
    max-height: 350px;
    overflow-y: auto;
    z-index: 9999;
    display: none;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}

.cn-customer-dropdown.active { display: block !important; }

.cn-customer-select-wrapper {
    position: relative;
    z-index: 1000;
}

.cn-customer-item {
    padding: 14px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cn-customer-item:last-child { border-bottom: none; }

.cn-customer-item:hover {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.cn-customer-item.selected {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: #fff;
}

.cn-customer-info {
    display: flex;
    flex-direction: column;
}

.cn-customer-name {
    font-weight: 600;
    font-size: 14px;
}

.cn-customer-code {
    font-size: 11px;
    color: #6b7280;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-customer-item.selected .cn-customer-code { color: rgba(255,255,255,0.8); }

.cn-customer-balance {
    font-weight: 700;
    font-family: 'SF Mono', Monaco, monospace;
    color: #059669;
    font-size: 14px;
}

.cn-customer-item.selected .cn-customer-balance { color: #fff; }

/* Selected Customer Card */
.cn-selected-customer {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 2px solid #dc2626;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 12px;
    animation: fadeIn 0.3s ease;
}

.cn-selected-customer-avatar {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
}

.cn-selected-customer-info { flex: 1; }

.cn-selected-customer-name {
    font-weight: 700;
    font-size: 16px;
    color: #1e1b4b;
}

.cn-selected-customer-balance {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
}

.cn-selected-customer-balance strong {
    color: #dc2626;
    font-family: 'SF Mono', Monaco, monospace;
}

.cn-change-customer {
    background: #fff;
    border: 2px solid #dc2626;
    color: #dc2626;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cn-change-customer:hover {
    background: #dc2626;
    color: #fff;
}

/* Reason Category Cards */
.cn-reason-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

@media (max-width: 768px) {
    .cn-reason-grid { grid-template-columns: repeat(2, 1fr); }
}

.cn-reason-card {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.cn-reason-card:hover {
    border-color: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
}

.cn-reason-card.active {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    border-color: #dc2626;
    color: #fff;
}

.cn-reason-card-icon {
    font-size: 24px;
    margin-bottom: 8px;
    color: #dc2626;
}

.cn-reason-card.active .cn-reason-card-icon { color: #fff; }

.cn-reason-card-label {
    font-size: 12px;
    font-weight: 600;
}

/* Submit Section */
.cn-submit-section {
    background: linear-gradient(135deg, #f8f9fe 0%, #f1f5f9 100%);
    border-radius: 12px;
    padding: 20px;
    margin-top: 24px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
}

.cn-submit-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.cn-btn {
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.cn-btn-primary {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.cn-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
}

.cn-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.cn-btn-secondary {
    background: #fff;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.cn-btn-secondary:hover {
    background: #f3f4f6;
    transform: translateY(-1px);
}

.cn-auto-approve {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    background: #fff;
    border-radius: 10px;
    border: 2px solid #e5e7eb;
}

.cn-auto-approve input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.cn-auto-approve label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
}

/* Preview Card */
.cn-preview-card {
    position: sticky;
    top: 20px;
}

.cn-preview-header {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    padding: 20px 24px;
}

.cn-preview-header h3 {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cn-preview-body { padding: 24px; }

.cn-preview-number {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-radius: 12px;
    margin-bottom: 20px;
}

.cn-preview-number-label {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.cn-preview-number-value {
    font-size: 24px;
    font-weight: 700;
    color: #dc2626;
    font-family: 'SF Mono', Monaco, monospace;
    margin-top: 4px;
}

.cn-preview-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.cn-preview-item:last-child { border-bottom: none; }

.cn-preview-label {
    font-size: 13px;
    color: #6b7280;
}

.cn-preview-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e1b4b;
    text-align: right;
    max-width: 60%;
    word-break: break-word;
}

.cn-preview-amount {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin-top: 20px;
}

.cn-preview-amount-label {
    font-size: 12px;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.cn-preview-amount-value {
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    font-family: 'SF Mono', Monaco, monospace;
    margin-top: 4px;
}

/* Help Tips */
.cn-help-tip {
    background: #fffbeb;
    border: 1px solid #fbbf24;
    border-radius: 10px;
    padding: 14px 16px;
    margin-top: 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.cn-help-tip i {
    color: #f59e0b;
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 2px;
}

.cn-help-tip-content {
    font-size: 13px;
    color: #92400e;
    line-height: 1.5;
}

.cn-help-tip-content strong { color: #78350f; }

/* Animations */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Character Counter */
.cn-char-counter {
    font-size: 11px;
    color: #9ca3af;
    text-align: right;
    margin-top: 4px;
}

.cn-char-counter.warning { color: #f59e0b; }
.cn-char-counter.error { color: #dc2626; }
</style>
@endsection

@section('content')
<section class="content cn-page">
    <!-- Header Banner -->
    <div class="cn-header-banner">
        <div>
            <h1><i class="fas fa-file-invoice-dollar"></i> Create Credit Note</h1>
            <p class="subtitle">Issue a credit note to reduce customer balance</p>
        </div>
        <a href="{{ route('bookkeeping.accounts-receivable.index') }}" class="cn-btn-back">
            <i class="fas fa-arrow-left"></i> Back to AR
        </a>
    </div>

    <!-- Main Form -->
    <form id="credit_note_form" class="cn-form-container">
        @csrf
        
        <!-- Left Column - Form -->
        <div class="cn-card">
            <div class="cn-card-header">
                <h3><i class="fas fa-edit"></i> Credit Note Details</h3>
            </div>
            <div class="cn-card-body">
                
                <!-- Customer Selection -->
                <div class="cn-form-section">
                    <div class="cn-section-title">
                        <i class="fas fa-user"></i> Customer Information
                    </div>
                    
                    <div class="cn-form-group">
                        <label><i class="fas fa-user-circle"></i> Select Customer <span class="required">*</span></label>
                        <div class="cn-customer-select-wrapper">
                            <div class="cn-customer-search" id="customerSearchWrapper">
                                <input type="text" 
                                       class="cn-input" 
                                       id="customer_search" 
                                       placeholder="Search customer by name or code..."
                                       autocomplete="off">
                                <i class="fas fa-search cn-customer-search-icon"></i>
                                <div class="cn-customer-dropdown" id="customerDropdown">
                                    @forelse($customers as $customer)
                                    <div class="cn-customer-item" 
                                         data-id="{{ $customer->id }}"
                                         data-name="{{ $customer->display_name ?? $customer->name }}"
                                         data-code="{{ $customer->customer_code ?? '' }}"
                                         data-balance="{{ $customer->balance_due ?? 0 }}">
                                        <div class="cn-customer-info">
                                            <span class="cn-customer-name">{{ $customer->display_name ?? $customer->name }}</span>
                                            @if(!empty($customer->customer_code))
                                            <span class="cn-customer-code">{{ $customer->customer_code }}</span>
                                            @endif
                                        </div>
                                        <span class="cn-customer-balance" style="color: {{ ($customer->balance_due ?? 0) > 0 ? '#059669' : '#9ca3af' }};">
                                            ${{ number_format($customer->balance_due ?? 0, 2) }}
                                        </span>
                                    </div>
                                    @empty
                                    <div style="padding: 20px; text-align: center; color: #6b7280;">
                                        <i class="fas fa-users" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                        No customers found
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                            <input type="hidden" name="contact_id" id="contact_id" required>
                        </div>
                        
                        <div id="selectedCustomerCard" class="cn-selected-customer" style="display: none;">
                            <div class="cn-selected-customer-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="cn-selected-customer-info">
                                <div class="cn-selected-customer-name" id="selectedCustomerName"></div>
                                <div class="cn-selected-customer-balance">
                                    Current Balance: <strong id="selectedCustomerBalance"></strong>
                                </div>
                            </div>
                            <button type="button" class="cn-change-customer" onclick="clearCustomer()">
                                <i class="fas fa-times"></i> Change
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Credit Note Info -->
                <div class="cn-form-section">
                    <div class="cn-section-title">
                        <i class="fas fa-file-alt"></i> Credit Details
                    </div>
                    
                    <div class="cn-form-row">
                        <div class="cn-form-group">
                            <label><i class="fas fa-calendar-alt"></i> Credit Date <span class="required">*</span></label>
                            <input type="date" 
                                   class="cn-input" 
                                   name="credit_date" 
                                   id="credit_date"
                                   value="{{ date('Y-m-d') }}" 
                                   required>
                        </div>
                        <div class="cn-form-group">
                            <label><i class="fas fa-dollar-sign"></i> Credit Amount <span class="required">*</span></label>
                            <div class="cn-input-money">
                                <input type="number" 
                                       class="cn-input" 
                                       name="amount" 
                                       id="amount"
                                       step="0.01" 
                                       min="0.01" 
                                       placeholder="0.00"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reason Category -->
                <div class="cn-form-section">
                    <div class="cn-section-title">
                        <i class="fas fa-tag"></i> Reason for Credit
                    </div>
                    
                    <input type="hidden" name="reason_category" id="reason_category" required>
                    
                    <div class="cn-reason-grid">
                        @foreach($reasonCategories as $key => $label)
                        <div class="cn-reason-card" data-reason="{{ $key }}" onclick="selectReason('{{ $key }}')">
                            <div class="cn-reason-card-icon">
                                @if($key == 'return')
                                    <i class="fas fa-undo-alt"></i>
                                @elseif($key == 'discount')
                                    <i class="fas fa-percent"></i>
                                @elseif($key == 'error_correction')
                                    <i class="fas fa-exclamation-triangle"></i>
                                @elseif($key == 'price_adjustment')
                                    <i class="fas fa-balance-scale"></i>
                                @elseif($key == 'damaged_goods')
                                    <i class="fas fa-box-open"></i>
                                @elseif($key == 'goodwill')
                                    <i class="fas fa-heart"></i>
                                @else
                                    <i class="fas fa-file-alt"></i>
                                @endif
                            </div>
                            <div class="cn-reason-card-label">{{ $label }}</div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="cn-form-group">
                        <label><i class="fas fa-align-left"></i> Detailed Reason <span class="required">*</span></label>
                        <textarea class="cn-textarea" 
                                  name="reason_description" 
                                  id="reason_description"
                                  placeholder="Please provide a detailed explanation for this credit note. Include relevant invoice numbers, product details, or any other supporting information..."
                                  required
                                  minlength="10"
                                  maxlength="1000"></textarea>
                        <div class="cn-char-counter" id="charCounter">0 / 1000 characters</div>
                    </div>
                </div>

                <!-- Reference Information -->
                <div class="cn-form-section">
                    <div class="cn-section-title">
                        <i class="fas fa-link"></i> Reference Information (Optional)
                    </div>
                    
                    <div class="cn-form-row">
                        <div class="cn-form-group">
                            <label><i class="fas fa-file-invoice"></i> Reference Type</label>
                            <select class="cn-select" name="reference_type" id="reference_type">
                                <option value="">-- Select --</option>
                                <option value="invoice">Original Invoice</option>
                                <option value="return">Return Reference</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="cn-form-group">
                            <label><i class="fas fa-hashtag"></i> Reference Number</label>
                            <input type="text" 
                                   class="cn-input" 
                                   name="reference_number" 
                                   id="reference_number"
                                   placeholder="e.g., INV-000123">
                        </div>
                    </div>
                    
                    <div class="cn-form-group">
                        <label><i class="fas fa-sticky-note"></i> Internal Notes</label>
                        <textarea class="cn-textarea" 
                                  name="internal_notes" 
                                  id="internal_notes"
                                  placeholder="Add any internal notes or comments (not visible to customer)..."
                                  style="min-height: 70px;"></textarea>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="cn-submit-section">
                    <div class="cn-auto-approve">
                        <input type="checkbox" name="auto_approve" id="auto_approve" value="1">
                        <label for="auto_approve">
                            <i class="fas fa-check-circle" style="color: #059669;"></i>
                            Auto-approve & create journal entry
                        </label>
                    </div>
                    <div class="cn-submit-actions">
                        <button type="button" class="cn-btn cn-btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="cn-btn cn-btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Create Credit Note
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Preview -->
        <div class="cn-preview-card cn-card">
            <div class="cn-preview-header">
                <h3><i class="fas fa-eye"></i> Live Preview</h3>
            </div>
            <div class="cn-preview-body">
                <div class="cn-preview-number">
                    <div class="cn-preview-number-label">Credit Note Number</div>
                    <div class="cn-preview-number-value">{{ $nextNumber }}</div>
                </div>
                
                <div class="cn-preview-item">
                    <span class="cn-preview-label">Customer</span>
                    <span class="cn-preview-value" id="previewCustomer">Not selected</span>
                </div>
                <div class="cn-preview-item">
                    <span class="cn-preview-label">Date</span>
                    <span class="cn-preview-value" id="previewDate">{{ date('M d, Y') }}</span>
                </div>
                <div class="cn-preview-item">
                    <span class="cn-preview-label">Reason</span>
                    <span class="cn-preview-value" id="previewReason">Not selected</span>
                </div>
                <div class="cn-preview-item">
                    <span class="cn-preview-label">Reference</span>
                    <span class="cn-preview-value" id="previewReference">-</span>
                </div>
                
                <div class="cn-preview-amount">
                    <div class="cn-preview-amount-label">Credit Amount</div>
                    <div class="cn-preview-amount-value" id="previewAmount">$0.00</div>
                </div>
                
                <div class="cn-help-tip">
                    <i class="fas fa-lightbulb"></i>
                    <div class="cn-help-tip-content">
                        <strong>What happens next?</strong><br>
                        Once created, the credit note will reduce the customer's balance. 
                        If auto-approved, a journal entry will be created automatically (Debit: Sales Returns, Credit: AR).
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Pre-select customer if provided
    @if($selectedCustomerId)
        var preselectedItem = $('.cn-customer-item[data-id="{{ $selectedCustomerId }}"]');
        if (preselectedItem.length) {
            selectCustomer(
                '{{ $selectedCustomerId }}',
                preselectedItem.data('name'),
                preselectedItem.data('balance')
            );
        }
    @endif
    
    // Customer search functionality - show dropdown on focus/click
    $('#customer_search').on('focus click', function(e) {
        e.stopPropagation();
        $('#customerDropdown').addClass('active');
        // Show all customers when first clicking
        if ($(this).val() === '') {
            $('.cn-customer-item').show();
        }
    });
    
    // Filter customers as user types
    $('#customer_search').on('input keyup', function() {
        var search = ($(this).val() || '').toLowerCase().trim();
        
        $('.cn-customer-item').each(function() {
            var name = ($(this).data('name') || '').toString().toLowerCase();
            var code = ($(this).data('code') || '').toString().toLowerCase();
            if (search === '' || name.includes(search) || code.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#customerDropdown').addClass('active');
    });
    
    // Customer item click
    $('.cn-customer-item').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var balance = $(this).data('balance');
        selectCustomer(id, name, balance);
    });
    
    // Close dropdown on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.cn-customer-select-wrapper').length) {
            $('#customerDropdown').removeClass('active');
        }
    });
    
    // Amount input - update preview
    $('#amount').on('input', function() {
        var amount = parseFloat($(this).val()) || 0;
        $('#previewAmount').text('$' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    });
    
    // Date input - update preview
    $('#credit_date').on('change', function() {
        var date = new Date($(this).val());
        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        $('#previewDate').text(date.toLocaleDateString('en-US', options));
    });
    
    // Reference inputs - update preview
    $('#reference_type, #reference_number').on('change input', function() {
        var type = $('#reference_type').val();
        var number = $('#reference_number').val();
        if (type || number) {
            $('#previewReference').text((type ? type.charAt(0).toUpperCase() + type.slice(1) + ': ' : '') + (number || '-'));
        } else {
            $('#previewReference').text('-');
        }
    });
    
    // Character counter for description
    $('#reason_description').on('input', function() {
        var len = $(this).val().length;
        var max = 1000;
        $('#charCounter').text(len + ' / ' + max + ' characters');
        
        if (len > max * 0.9) {
            $('#charCounter').addClass('warning');
        } else {
            $('#charCounter').removeClass('warning');
        }
        
        if (len >= max) {
            $('#charCounter').addClass('error');
        } else {
            $('#charCounter').removeClass('error');
        }
    });
    
    // Form submission
    $('#credit_note_form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate
        if (!$('#contact_id').val()) {
            toastr.error('Please select a customer.');
            return;
        }
        
        if (!$('#reason_category').val()) {
            toastr.error('Please select a reason category.');
            return;
        }
        
        if ($('#reason_description').val().length < 10) {
            toastr.error('Please provide a detailed reason (at least 10 characters).');
            return;
        }
        
        var submitBtn = $('#submitBtn');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
        
        $.ajax({
            url: "{{ route('bookkeeping.credit-notes.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'An error occurred.');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var msg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    msg = xhr.responseJSON.msg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                toastr.error(msg);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

function selectCustomer(id, name, balance) {
    $('#contact_id').val(id);
    $('#customer_search').val(name);
    $('#customerDropdown').removeClass('active');
    
    // Show selected customer card
    $('#customerSearchWrapper').hide();
    $('#selectedCustomerCard').show();
    $('#selectedCustomerName').text(name);
    $('#selectedCustomerBalance').text('$' + parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    
    // Update preview
    $('#previewCustomer').text(name);
}

function clearCustomer() {
    $('#contact_id').val('');
    $('#customer_search').val('');
    $('#selectedCustomerCard').hide();
    $('#customerSearchWrapper').show();
    $('#previewCustomer').text('Not selected');
    $('.cn-customer-item').show();
}

function selectReason(reason) {
    // Update hidden input
    $('#reason_category').val(reason);
    
    // Update visual state
    $('.cn-reason-card').removeClass('active');
    $('.cn-reason-card[data-reason="' + reason + '"]').addClass('active');
    
    // Update preview
    var label = $('.cn-reason-card[data-reason="' + reason + '"] .cn-reason-card-label').text();
    $('#previewReason').text(label);
}
</script>
@endsection

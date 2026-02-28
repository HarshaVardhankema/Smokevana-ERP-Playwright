@extends('layouts.app')

@section('title', __('subscription::lang.add_subscription'))

@section('content')
<style>
    .create-subscription-page {
        /* Amazon-style light grey app background */
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        /* Amazon-style dark banner – same as Add new product / Manage Order */
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 24px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }
    
    .page-header-card h1 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .page-header-card h1 i {
        color: #fef3c7;
        font-size: 22px;
    }
    
    .page-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }
    
    .form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .form-card .card-header {
        background: #fff;
        border-bottom: 2px solid #f0f0f0;
        padding: 18px 24px;
    }
    
    .form-card .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-card .card-header h4 i {
        color: #FF9900;
        font-size: 18px;
    }
    
    .form-card .card-body {
        padding: 24px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-group label .required {
        color: #d32f2f;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e8e8e8;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #fff !important;
        color: #131921 !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #FF9900;
        box-shadow: 0 0 0 4px rgba(255, 153, 0, 0.15);
    }
    
    .form-text {
        font-size: 12px;
        color: #888;
        margin-top: 6px;
    }
    
    /* Plan Selection Cards */
    .plan-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    
    .plan-option {
        border: 2px solid #e8e8e8;
        border-radius: 14px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .plan-option:hover {
        border-color: #FF9900;
        transform: translateY(-2px);
    }
    
    .plan-option.selected {
        border-color: #FF9900;
        background: linear-gradient(135deg, #FFF8F0, #ffffff);
        box-shadow: 0 4px 15px rgba(255, 153, 0, 0.2);
    }
    
    .plan-option.prime {
        border-color: #FF9900;
    }
    
    .plan-option.prime.selected {
        background: linear-gradient(135deg, #FFF8F0, #ffffff);
        border-color: #FF9900;
        box-shadow: 0 4px 18px rgba(255, 153, 0, 0.25);
    }
    
    .plan-option input[type="radio"] {
        display: none;
    }
    
    .plan-option .plan-badge {
        position: absolute;
        top: -10px;
        right: 16px;
        padding: 4px 12px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .plan-option .plan-badge.prime {
        background: linear-gradient(135deg, #FF9900, #e88b00);
        color: #fff;
    }
    
    .plan-option .plan-badge.featured {
        background: linear-gradient(135deg, #FF9900, #e88b00);
        color: #fff;
    }
    
    .plan-option .plan-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 14px;
    }
    
    .plan-option .plan-icon.standard {
        background: linear-gradient(135deg, #FF9900, #e88b00);
        color: #fff;
    }
    
    .plan-option .plan-icon.prime {
        background: linear-gradient(135deg, #FF9900, #e88b00);
        color: #fff;
    }
    
    .plan-option .plan-name {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 4px;
    }
    
    .plan-option .plan-description {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 12px;
    }
    
    .plan-option .plan-price {
        font-size: 24px;
        font-weight: 700;
        color: #111;
    }
    
    .plan-option.prime .plan-price {
        color: #e88b00;
    }
    
    .plan-option .plan-price small {
        font-size: 12px;
        color: #999;
        font-weight: 400;
    }
    
    .plan-option .plan-features {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px dashed #e8e8e8;
    }
    
    .plan-option .plan-features li {
        font-size: 12px;
        color: #666;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .plan-option .plan-features li i {
        color: #28a745;
        font-size: 10px;
    }
    
    /* Customer Search – icon must not overlap text */
    .customer-search-container {
        position: relative;
    }
    
    .customer-search-container .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 14px;
        pointer-events: none;
        z-index: 3;
    }
    
    .customer-search-container #customer_search {
        padding-left: 48px !important;
        box-sizing: border-box;
    }
    
    .customer-search-wrapper {
        position: relative;
    }
    
    .customer-suggestions {
        position: fixed;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-height: 280px;
        overflow-y: auto;
        z-index: 9999;
    }
    
    .customer-suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        color: #131921;
    }
    .customer-suggestion-item:last-child {
        border-bottom: none;
    }
    .customer-suggestion-item:hover,
    .customer-suggestion-item:focus {
        background: #FFF3E0;
    }
    .customer-suggestion-item.no-results {
        cursor: default;
        color: #6c757d;
        font-style: italic;
    }
    
    .selected-customer {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        margin-top: 16px;
    }
    
    .selected-customer .customer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF9900, #e88b00);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
    }
    
    .selected-customer .customer-info h5 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .selected-customer .customer-info span {
        font-size: 13px;
        color: #6c757d;
    }
    
    .selected-customer .customer-info .badge {
        font-size: 10px;
        padding: 4px 10px;
        border-radius: 10px;
        margin-left: 10px;
    }
    
    .selected-customer .btn-change {
        margin-left: auto;
        background: #f0f0f0;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        color: #666;
    }
    
    .selected-customer .btn-change:hover {
        background: #e0e0e0;
        color: #333;
    }
    
    /* Sidebar */
    .sidebar-form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .sidebar-form-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 20px;
    }
    
    .sidebar-form-card .card-header h5 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .sidebar-form-card .card-body {
        padding: 20px;
    }
    
    /* Summary Card */
    .summary-card {
        border: 2px solid #FF9900;
        background: #ffffff;
    }
    
    .summary-card .card-header {
        background: linear-gradient(135deg, #FFF8F0, #fff);
        border-bottom: 1px solid #FFE5CC;
    }
    
    .summary-card .card-header h5 {
        color: #131921;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #e8e8e8;
    }
    
    .summary-item:last-child {
        border-bottom: none;
    }
    
    .summary-item label {
        font-size: 13px;
        color: #666;
        margin: 0;
    }
    
    .summary-item span {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
    }
    
    .summary-total {
        display: flex;
        justify-content: space-between;
        padding: 16px 0 0 0;
        margin-top: 10px;
        border-top: 2px solid #FF9900;
    }
    
    .summary-total label {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }
    
    .summary-total span {
        font-size: 24px;
        font-weight: 700;
        color: #e88b00;
    }
    
    /* Toggle Switch */
    .toggle-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .toggle-switch:last-child {
        border-bottom: none;
    }
    
    .toggle-switch label {
        font-size: 14px;
        font-weight: 500;
        color: #333;
        margin: 0;
    }
    
    .switch {
        position: relative;
        width: 50px;
        height: 26px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 26px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background: linear-gradient(135deg, #FF9900, #e88b00);
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    /* Submit Buttons */
    .btn-submit {
        /* Amazon-style orange primary button */
        background: linear-gradient(90deg, #FFD814 0%, #FCD200 100%);
        border: 1px solid #FCD200;
        color: #131921;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(213, 217, 217, 0.5);
    }
    
    .btn-submit:hover {
        background: linear-gradient(90deg, #F7CA00 0%, #F2C200 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(213, 217, 217, 0.5);
        color: #131921;
    }
    
    .btn-cancel {
        background: #f0f0f0;
        border: none;
        color: #666;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
        margin-top: 12px;
    }
    
    .btn-cancel:hover {
        background: #e0e0e0;
        color: #333;
    }
    
    @media (max-width: 992px) {
        .plan-selector {
            grid-template-columns: 1fr;
        }
    }
    
    /* Payment Method dropdown - Amazon-style light appearance */
    .create-subscription-page .form-select,
    .create-subscription-page .form-select option {
        background-color: #fff !important;
        color: #131921 !important;
    }
    
    /* Select2 light dropdown (appended to body) */
    .subscription-payment-dropdown {
        background-color: #fff !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
    }
    .subscription-payment-dropdown .select2-results__option {
        background-color: #fff !important;
        color: #131921 !important;
        padding: 10px 16px;
    }
    .subscription-payment-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #FFF3E0 !important;
        color: #131921 !important;
    }
    .subscription-payment-dropdown .select2-results__option[aria-selected=true] {
        background-color: #FFF8F0 !important;
        color: #e88b00 !important;
    }
    
    /* Amazon-style datepicker calendar */
    .create-subscription-page ~ .datepicker,
    .datepicker.dropdown-menu {
        background: #fff !important;
        border: 1px solid #d5d9d9 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
    }
    .datepicker-dropdown table thead tr:first-child th {
        background: #fff !important;
        color: #131921 !important;
        border: none !important;
    }
    .datepicker-dropdown .datepicker-switch:hover,
    .datepicker-dropdown .prev:hover,
    .datepicker-dropdown .next:hover {
        background: #FFF3E0 !important;
        color: #131921 !important;
    }
    .datepicker-dropdown table tbody td {
        color: #131921 !important;
    }
    .datepicker-dropdown table tbody td.day:hover {
        background: #FFF3E0 !important;
        color: #131921 !important;
    }
    .datepicker-dropdown table tbody td.active,
    .datepicker-dropdown table tbody td.today.active {
        background: #FF9900 !important;
        color: #fff !important;
    }
    .datepicker-dropdown table tbody td.today {
        background: #FFF3E0 !important;
        color: #131921 !important;
    }
    .create-subscription-page .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 2px solid #e8e8e8;
        border-radius: 10px;
    }
    .create-subscription-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #131921 !important;
    }
</style>

<div class="create-subscription-page">
    <div class="container-fluid">
        {{-- Page Header – Amazon-style banner --}}
        <div class="page-header-card">
            <h1>
                <i class="fas fa-user-plus"></i>
                Add New Subscription
            </h1>
            <p class="page-header-subtitle">
                Create a new subscription for a customer. Select customer, plan, and subscription details.
            </p>
        </div>

        <form action="{{ route('subscription.subscriptions.store') }}" method="POST" id="subscription_form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    {{-- Customer Selection --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-user"></i> Select Customer</h4>
                        </div>
                        <div class="card-body">
                            <div class="customer-search-wrapper" id="customer_search_wrapper">
                                <div class="customer-search-container">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" id="customer_search" class="form-control" placeholder="Search customer by name, email or phone..." autocomplete="off">
                                    <input type="hidden" name="contact_id" id="contact_id" required>
                                </div>
                            </div>
                            
                            <div id="selected_customer_display" style="display: none;">
                                <div class="selected-customer">
                                    <div class="customer-avatar" id="customer_initials">JD</div>
                                    <div class="customer-info">
                                        <h5 id="customer_name">John Doe <span class="badge bg-success" id="customer_existing_badge" style="display: none;">Existing Subscriber</span></h5>
                                        <span id="customer_email">john@example.com</span>
                                    </div>
                                    <button type="button" class="btn btn-change" id="change_customer">Change</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Plan Selection --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-tag"></i> Select Plan</h4>
                        </div>
                        <div class="card-body">
                            @if($plans->isEmpty())
                                <div class="alert alert-info d-flex align-items-center" style="padding: 20px; border-radius: 12px; background: #e8f4fd; border: 1px solid #b3d9f2;">
                                    <i class="fas fa-info-circle fa-2x mr-3" style="color: #0c5460;"></i>
                                    <div>
                                        <strong>No plans available.</strong>
                                        <p class="mb-0 mt-1">Create subscription plans first to add a subscription. Go to Plans to add new plans.</p>
                                        <a href="{{ route('subscription.plans.create') }}" class="btn btn-primary btn-sm mt-2 mr-1">
                                            <i class="fas fa-plus"></i> Add Plan
                                        </a>
                                        <a href="{{ route('subscription.plans.index') }}" class="btn btn-sm mt-2" style="background: #fff; color: #37475a; border: 2px solid #37475a;">
                                            <i class="fas fa-list"></i> Manage Plans
                                        </a>
                                    </div>
                                </div>
                            @else
                            <div class="plan-selector">
                                @foreach($plans as $plan)
                                    <label class="plan-option {{ $plan->is_prime ? 'prime' : '' }}">
                                        <input type="radio" name="plan_id" value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-cycle="{{ $plan->billing_cycle }}" required>
                                        @if($plan->is_prime)
                                            <div class="plan-badge prime"><i class="fas fa-crown"></i> Prime</div>
                                        @elseif($plan->is_featured)
                                            <div class="plan-badge featured">Featured</div>
                                        @endif
                                        <div class="plan-icon {{ $plan->is_prime ? 'prime' : 'standard' }}">
                                            <i class="fas {{ $plan->is_prime ? 'fa-crown' : 'fa-tag' }}"></i>
                                        </div>
                                        <div class="plan-name">{{ $plan->name }}</div>
                                        <div class="plan-description">{{ Str::limit($plan->description, 50) }}</div>
                                        <div class="plan-price">
                                            ${{ number_format($plan->price, 2) }}
                                            <small>/{{ $plan->billing_cycle }}</small>
                                        </div>
                                        @if($plan->is_prime && ($plan->discount_percentage > 0 || $plan->fast_delivery_enabled || $plan->bnpl_enabled))
                                            <ul class="plan-features list-unstyled">
                                                @if($plan->discount_percentage > 0)
                                                    <li><i class="fas fa-check"></i> {{ $plan->discount_percentage }}% off all orders</li>
                                                @endif
                                                @if($plan->fast_delivery_enabled)
                                                    <li><i class="fas fa-check"></i> Priority fast delivery</li>
                                                @endif
                                                @if($plan->bnpl_enabled)
                                                    <li><i class="fas fa-check"></i> Buy Now Pay Later</li>
                                                @endif
                                            </ul>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Subscription Details --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-calendar-alt"></i> Subscription Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date <span class="required">*</span></label>
                                        <input type="text" name="start_date" id="start_date" class="form-control subscription-date-picker" value="{{ date('d-m-Y') }}" placeholder="dd-mm-yyyy" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date (Optional)</label>
                                        <input type="text" name="end_date" id="end_date" class="form-control subscription-date-picker" placeholder="dd-mm-yyyy" autocomplete="off">
                                        <div class="form-text">Leave empty for auto-calculation based on billing cycle</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Notes (Internal)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Any internal notes about this subscription..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Summary --}}
                    <div class="sidebar-form-card summary-card">
                        <div class="card-header">
                            <h5><i class="fas fa-receipt"></i> Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <label>Plan</label>
                                <span id="summary_plan">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Billing Cycle</label>
                                <span id="summary_cycle">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Customer</label>
                                <span id="summary_customer">-</span>
                            </div>
                            <div class="summary-total">
                                <label>Total</label>
                                <span id="summary_total">$0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="sidebar-form-card">
                        <div class="card-header">
                            <h5>Subscription Options</h5>
                        </div>
                        <div class="card-body">
                            <div class="toggle-switch">
                                <label>Auto-Renewal</label>
                                <label class="switch">
                                    <input type="checkbox" name="auto_renew" value="1" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="toggle-switch">
                                <label>Send Welcome Email</label>
                                <label class="switch">
                                    <input type="checkbox" name="send_email" value="1" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="toggle-switch">
                                <label>Start Trial</label>
                                <label class="switch">
                                    <input type="checkbox" name="start_trial" value="1">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="sidebar-form-card">
                        <div class="card-header">
                            <h5>Payment</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label>Payment Method</label>
                                <select name="payment_method" id="payment_method_select" class="form-select select2">
                                    @foreach($payment_methods ?? [] as $value => $label)
                                        <option value="{{ $value }}" {{ $value === 'manual' ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="sidebar-form-card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-check"></i> Create Subscription
                            </button>
                            <a href="{{ route('subscription.subscriptions.index') }}" class="btn btn-cancel">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Datepicker for Start Date and End Date - Amazon-style calendar
    if ($.fn.datepicker) {
        $('.subscription-date-picker').datepicker({
            format: typeof datepicker_date_format !== 'undefined' ? datepicker_date_format : 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto'
        });
    }
    
    // Payment method dropdown - use Select2 with light Amazon-style dropdown
    if ($.fn.select2 && $('#payment_method_select').length) {
        try {
            if ($('#payment_method_select').data('select2')) {
                $('#payment_method_select').select2('destroy');
            }
            $('#payment_method_select').select2({ dropdownCssClass: 'subscription-payment-dropdown' });
        } catch (e) {
            $('#payment_method_select').select2({ dropdownCssClass: 'subscription-payment-dropdown' });
        }
    }
    
    // Customer search – single input with suggestions dropdown (no duplicate fields)
    var customerSearchTimer, lastCustomerResults = [];
    var $suggestionsEl = $('<div id="customer_suggestions" class="customer-suggestions" style="display: none;"></div>').appendTo('body');
    $('#customer_search').on('input focus', function() {
        var term = $(this).val().trim();
        clearTimeout(customerSearchTimer);
        if (term.length < 1) {
            $('#customer_suggestions').hide().empty();
            return;
        }
        customerSearchTimer = setTimeout(function() {
            $.get('/contacts/customers', { q: term }, function(data) {
                var items = (typeof data === 'string') ? JSON.parse(data) : (data || []);
                lastCustomerResults = items;
                var $list = $('#customer_suggestions').empty();
                if (items.length === 0) {
                    $list.append('<div class="customer-suggestion-item no-results">No customers found</div>');
                } else {
                    items.forEach(function(item, idx) {
                        var name = (item.text || '').split(' (')[0];
                        var sub = item.mobile ? item.mobile : (item.email || '');
                        var $row = $('<div class="customer-suggestion-item" data-idx="' + idx + '">');
                        $row.append($('<strong>').text(name));
                        if (sub) $row.append($('<br>')).append($('<small class="text-muted">').text(sub));
                        $list.append($row);
                    });
                }
                positionSuggestionsDropdown();
                $list.show();
            });
        }, 250);
    });
    function positionSuggestionsDropdown() {
        var $input = $('#customer_search');
        var $list = $('#customer_suggestions');
        if (!$input.length || !$list.length) return;
        var rect = $input[0].getBoundingClientRect();
        $list.css({
            top: (rect.bottom + 4) + 'px',
            left: rect.left + 'px',
            width: rect.width + 'px'
        });
    }
    $(document).on('click', '.customer-suggestion-item:not(.no-results)', function() {
        var item = lastCustomerResults[$(this).data('idx')];
        if (item) {
            selectCustomer({ id: item.id, name: (item.text || '').split(' (')[0], email: item.email || '', mobile: item.mobile || '' });
        }
        $('#customer_search').val('');
        $('#customer_suggestions').hide().empty();
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.customer-search-wrapper, #customer_suggestions').length) {
            $('#customer_suggestions').hide();
        }
    });
    $(window).on('scroll resize', function() {
        if ($('#customer_suggestions').is(':visible')) {
            positionSuggestionsDropdown();
        }
    });
    function selectCustomer(customer) {
        $('#contact_id').val(customer.id);
        $('#customer_name').text(customer.name || customer.text);
        $('#customer_email').text(customer.email || customer.mobile || 'No email');
        $('#customer_initials').text((customer.name || customer.text || '?').charAt(0).toUpperCase());
        $('#summary_customer').text(customer.name || customer.text);
        $('#customer_search_wrapper').hide();
        $('#selected_customer_display').show();
    }
    $('#change_customer').on('click', function() {
        $('#contact_id').val('');
        $('#customer_search').val('');
        $('#customer_suggestions').hide().empty();
        $('#customer_search_wrapper').show();
        $('#selected_customer_display').hide();
        $('#summary_customer').text('-');
    });
    
    // Form submit – show toaster if customer not selected
    $('#subscription_form').on('submit', function(e) {
        if (!$('#contact_id').val() || $('#contact_id').val().trim() === '') {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select a customer.');
            } else {
                alert('Please select a customer.');
            }
            $('#customer_search').focus();
            return false;
        }
    });
    
    // Plan selection
    $('.plan-option input[type="radio"]').on('change', function() {
        $('.plan-option').removeClass('selected');
        $(this).closest('.plan-option').addClass('selected');
        
        var price = $(this).data('price');
        var cycle = $(this).data('cycle');
        var planName = $(this).closest('.plan-option').find('.plan-name').text();
        
        $('#summary_plan').text(planName);
        $('#summary_cycle').text(cycle);
        $('#summary_total').text('$' + parseFloat(price).toFixed(2));
    });
});
</script>
@endsection

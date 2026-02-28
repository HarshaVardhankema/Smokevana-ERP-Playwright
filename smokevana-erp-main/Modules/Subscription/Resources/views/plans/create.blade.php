@extends('layouts.app')

@section('title', __('subscription::lang.add_plan'))

@section('content')
<style>
    .create-plan-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
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
        color: #11998e;
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
        border: 2px solid #d5d9d9;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #ffffff !important;
        color: #0f1111 !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #37475a;
        box-shadow: 0 0 0 3px rgba(55, 71, 90, 0.15);
    }
    
    /* Amazon-style dropdowns – light background, dark text */
    .create-plan-page select.form-select,
    .create-plan-page .form-select {
        background-color: #ffffff !important;
        color: #0f1111 !important;
        border-color: #d5d9d9 !important;
    }
    
    .create-plan-page .select2-container--default .select2-selection--single,
    .create-plan-page .select2-container--default .select2-selection--multiple {
        background-color: #ffffff !important;
        border: 2px solid #d5d9d9 !important;
        color: #0f1111 !important;
    }
    
    .create-plan-page .select2-container--default .select2-selection--single .select2-selection__rendered,
    .create-plan-page .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: #0f1111 !important;
    }
    
    .form-text {
        font-size: 12px;
        color: #888;
        margin-top: 6px;
    }
    
    .input-group-addon,
    .input-group .input-group-addon {
        background: #f8f9fa;
        border: 2px solid #e8e8e8;
        border-left: none;
        border-radius: 0 10px 10px 0;
        font-weight: 600;
        color: #666;
    }
    
    .input-group .form-control {
        border-radius: 10px 0 0 10px;
    }
    
    /* Prime Benefits Card */
    .prime-card {
        border: 2px solid #ffd700;
        background: linear-gradient(135deg, #fffbeb 0%, #fff 100%);
    }
    
    .prime-card .card-header {
        background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
        border-bottom: none;
    }
    
    .prime-card .card-header h4 {
        color: #333;
    }
    
    .prime-card .card-header h4 i {
        color: #333;
    }
    
    /* Custom Checkbox */
    .custom-check {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .custom-check:hover {
        background: #f0f0f0;
    }
    
    .custom-check input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: #11998e;
    }
    
    .custom-check span {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }
    
    /* Benefit Row */
    .benefit-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 16px;
    }
    .benefit-row.flex-wrap {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
    
    .benefit-item {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        background: #fff;
        border: 2px solid #e8e8e8;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .benefit-item:hover {
        border-color: #ffd700;
    }
    
    .benefit-item.checked {
        border-color: #ffd700;
        background: #fffbeb;
    }
    
    .benefit-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #ffd700;
    }
    
    .benefit-item i {
        color: #ffd700;
        font-size: 16px;
    }
    
    .benefit-item span {
        font-size: 13px;
        font-weight: 500;
        color: #333;
    }
    
    .benefit-item small {
        display: block;
        width: 100%;
        margin-top: 4px;
        margin-left: 0;
        font-size: 11px;
    }
    
    /* Sidebar Cards */
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
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    /* Submit Button */
    .btn-submit {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: #fff;
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
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(17, 153, 142, 0.5);
        color: #fff;
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
    
    @media (max-width: 768px) {
        .benefit-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="create-plan-page">
    <div class="container-fluid">
        {{-- Page Header – Amazon-style banner --}}
        <div class="page-header-card">
            <h1>
                <i class="fas fa-plus-circle"></i>
                Create Subscription Plan
            </h1>
            <p class="page-header-subtitle">
                Create a new subscription plan with pricing, billing cycle, and benefits.
            </p>
        </div>

        <form action="{{ route('subscription.plans.store') }}" method="POST" id="plan_form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    {{-- Basic Info --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Plan Name <span class="required">*</span></label>
                                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="e.g., Prime Monthly">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Describe what this plan offers...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-dollar-sign"></i> Pricing & Billing</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Price <span class="required">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="price" class="form-control" step="0.01" min="0" required value="{{ old('price', 0) }}">
                                            <span class="input-group-addon">USD</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Setup Fee</label>
                                        <div class="input-group">
                                            <input type="number" name="setup_fee" class="form-control" step="0.01" min="0" value="{{ old('setup_fee', 0) }}">
                                            <span class="input-group-addon">USD</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Currency</label>
                                        <input type="text" name="currency" class="form-control" value="{{ old('currency', 'USD') }}" maxlength="3">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Billing Type <span class="required">*</span></label>
                                        <select name="billing_type" class="form-select" required>
                                            <option value="recurring">Recurring</option>
                                            <option value="one_time">One-time</option>
                                            <option value="date_based">Date-based</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Billing Cycle <span class="required">*</span></label>
                                        <select name="billing_cycle" id="billing_cycle" class="form-select" required>
                                            @foreach($billing_cycles as $key => $cycle)
                                                <option value="{{ $key }}">{{ $cycle['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" id="custom_interval_container" style="display: none;">
                                    <div class="form-group">
                                        <label>Custom Interval (Days)</label>
                                        <input type="number" name="billing_interval_days" class="form-control" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Trial Period --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-gift"></i> Trial Period</h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label class="custom-check">
                                        <input type="checkbox" name="has_trial" value="1" id="has_trial">
                                        <span>Enable Free Trial Period</span>
                                    </label>
                                </div>
                                <div class="col-md-6" id="trial_days_container" style="display: none;">
                                    <div class="form-group mb-0">
                                        <label>Trial Days</label>
                                        <input type="number" name="trial_days" class="form-control" min="1" value="14">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Group Mapping --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-users"></i> Customer Group Mapping</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Group</label>
                                        <select name="customer_group_id" class="form-select">
                                            @foreach($customer_groups as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Subscribers will be assigned to this group automatically</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Selling Price Group</label>
                                        <select name="selling_price_group_id" class="form-select">
                                            @foreach($selling_price_groups as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Prime Benefits --}}
                    <div class="form-card prime-card">
                        <div class="card-header">
                            <h4><i class="fas fa-crown"></i> Prime Benefits</h4>
                        </div>
                        <div class="card-body">
                            <label class="custom-check" style="background: #fff;">
                                <input type="checkbox" name="is_prime" value="1" id="is_prime">
                                <span><strong>Mark as Prime Plan</strong> - Enable exclusive benefits for this plan</span>
                            </label>

                            <div id="prime_settings" style="display: none;">
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Discount Percentage</label>
                                            <div class="input-group">
                                                <input type="number" name="discount_percentage" class="form-control" min="0" max="100" step="0.01" value="0">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                            <div class="form-text">Discount on all orders for Prime members</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Reward Points Multiplier</label>
                                            <div class="input-group">
                                                <input type="number" name="reward_points_multiplier" class="form-control" min="1" value="2">
                                                <span class="input-group-addon">x</span>
                                            </div>
                                            <div class="form-text">Multiply reward points earned</div>
                                        </div>
                                    </div>
                                </div>

                                <label class="mt-3 mb-2" style="font-weight: 600; color: #333;">Additional Benefits:</label>
                                <div class="benefit-row">
                                    <label class="benefit-item">
                                        <input type="checkbox" name="fast_delivery_enabled" value="1">
                                        <i class="fas fa-shipping-fast"></i>
                                        <span>Fast Delivery Priority</span>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="prime_products_access" value="1">
                                        <i class="fas fa-lock-open"></i>
                                        <span>Prime Products Access</span>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="bnpl_enabled" value="1" id="bnpl_enabled">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Buy Now Pay Later</span>
                                    </label>
                                </div>

                                <label class="mt-4 mb-2" style="font-weight: 600; color: #333;">Support:</label>
                                <div class="benefit-row flex-wrap">
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[support][]" value="concierge_support">
                                        <i class="fas fa-headset"></i>
                                        <span>Concierge Support</span>
                                        <small class="d-block text-muted">Receive personalized support for all your business needs</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[support][]" value="vip_concierge_support">
                                        <i class="fas fa-crown"></i>
                                        <span>VIP Concierge Support</span>
                                        <small class="d-block text-muted">Personalized support with a dedicated concierge for your business</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[support][]" value="priority_support">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span>Priority Support</span>
                                        <small class="d-block text-muted">Priority support for faster customer service and issue resolution</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[support][]" value="basic_support">
                                        <i class="fas fa-question-circle"></i>
                                        <span>Basic Support</span>
                                        <small class="d-block text-muted">Basic support to get started and resolve simple queries</small>
                                    </label>
                                </div>

                                <label class="mt-4 mb-2" style="font-weight: 600; color: #333;">Product Access:</label>
                                <div class="benefit-row flex-wrap">
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[product_access][]" value="vip_product_access">
                                        <i class="fas fa-star"></i>
                                        <span>VIP Product Access</span>
                                        <small class="d-block text-muted">Access premium products before they're available to the public</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[product_access][]" value="elite_products">
                                        <i class="fas fa-gem"></i>
                                        <span>Elite Products</span>
                                        <small class="d-block text-muted">Access top-tier, exclusive products that aren't available anywhere else</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[product_access][]" value="premium_products">
                                        <i class="fas fa-award"></i>
                                        <span>Premium Products</span>
                                        <small class="d-block text-muted">Exclusive access to premium products with high-demand pricing</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[product_access][]" value="limited_products_access">
                                        <i class="fas fa-medal"></i>
                                        <span>Limited Products Access</span>
                                        <small class="d-block text-muted">Gain access to limited-edition products exclusive to Prime Gold members</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[product_access][]" value="select_products">
                                        <i class="fas fa-box-open"></i>
                                        <span>Select Products</span>
                                        <small class="d-block text-muted">Access a range of basic products designed for small businesses</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[product_access][]" value="basic_product_access">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>Basic Product Access</span>
                                        <small class="d-block text-muted">Access to basic products to get your business started</small>
                                    </label>
                                </div>

                                <label class="mt-4 mb-2" style="font-weight: 600; color: #333;">Delivery:</label>
                                <div class="benefit-row flex-wrap">
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[delivery][]" value="next_day_delivery">
                                        <i class="fas fa-truck"></i>
                                        <span>Next-Day Delivery</span>
                                        <small class="d-block text-muted">Enjoy fast, free next-day delivery on all orders</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[delivery][]" value="priority_delivery">
                                        <i class="fas fa-shipping-fast"></i>
                                        <span>Priority Delivery</span>
                                        <small class="d-block text-muted">Enjoy priority shipping on all your orders</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[delivery][]" value="free_delivery">
                                        <i class="fas fa-gift"></i>
                                        <span>Free Delivery</span>
                                        <small class="d-block text-muted">Enjoy free shipping on all your orders, no matter the size</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[delivery][]" value="standard_delivery">
                                        <i class="fas fa-box"></i>
                                        <span>Standard Delivery</span>
                                        <small class="d-block text-muted">Enjoy standard delivery on all your orders</small>
                                    </label>
                                </div>

                                <label class="mt-4 mb-2" style="font-weight: 600; color: #333;">Volume Guarantee:</label>
                                <div class="benefit-row flex-wrap">
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[volume_guarantee][]" value="volume_guarantee_monthly">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Volume Guarantee</span>
                                        <small class="d-block text-muted">Hold for 3 months with monthly volume requirements</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[volume_guarantee][]" value="volume_guarantee_high">
                                        <i class="fas fa-chart-bar"></i>
                                        <span>Volume Guarantee (High)</span>
                                        <small class="d-block text-muted">Hold for 3 months with high-volume needs</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[volume_guarantee][]" value="volume_guarantee_higher">
                                        <i class="fas fa-chart-area"></i>
                                        <span>Volume Guarantee (Higher)</span>
                                        <small class="d-block text-muted">Hold for 3 months with higher volume needs</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[volume_guarantee][]" value="volume_guarantee_moderate">
                                        <i class="fas fa-balance-scale"></i>
                                        <span>Volume Guarantee (Moderate)</span>
                                        <small class="d-block text-muted">Hold for 3 months with moderate volume requirements</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[volume_guarantee][]" value="volume_guarantee_lower">
                                        <i class="fas fa-layer-group"></i>
                                        <span>Volume Guarantee (Lower)</span>
                                        <small class="d-block text-muted">Hold for 3 months with lower monthly volume</small>
                                    </label>
                                    <label class="benefit-item">
                                        <input type="checkbox" name="benefits[volume_guarantee][]" value="volume_guarantee_low">
                                        <i class="fas fa-chart-pie"></i>
                                        <span>Volume Guarantee (Low)</span>
                                        <small class="d-block text-muted">Hold for 3 months with low-volume monthly requirements</small>
                                    </label>
                                </div>

                                <div id="bnpl_settings" class="row mt-4" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>BNPL Credit Limit</label>
                                            <div class="input-group">
                                                <input type="number" name="bnpl_limit" class="form-control" min="0" step="0.01" value="500">
                                                <span class="input-group-addon">USD</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Period</label>
                                            <div class="input-group">
                                                <input type="number" name="bnpl_days" class="form-control" min="1" value="30">
                                                <span class="input-group-addon">days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Status & Display --}}
                    <div class="sidebar-form-card">
                        <div class="card-header">
                            <h5>Status & Display</h5>
                        </div>
                        <div class="card-body">
                            <div class="toggle-switch">
                                <label>Active</label>
                                <label class="switch">
                                    <input type="checkbox" name="is_active" value="1" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="toggle-switch">
                                <label>Featured</label>
                                <label class="switch">
                                    <input type="checkbox" name="is_featured" value="1">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="toggle-switch">
                                <label>Public</label>
                                <label class="switch">
                                    <input type="checkbox" name="is_public" value="1" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Badge --}}
                    <div class="sidebar-form-card">
                        <div class="card-header">
                            <h5>Badge (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Badge Text</label>
                                <input type="text" name="badge_text" class="form-control" placeholder="e.g., Best Value">
                            </div>
                            <div class="form-group mb-0">
                                <label>Badge Color</label>
                                <input type="color" name="badge_color" class="form-control" value="#ffc107" style="height: 45px;">
                            </div>
                        </div>
                    </div>

                    {{-- Limits --}}
                    <div class="sidebar-form-card">
                        <div class="card-header">
                            <h5>Limits</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label>Max Subscribers</label>
                                <input type="number" name="max_subscribers" class="form-control" min="0" placeholder="Unlimited">
                                <div class="form-text">Leave empty for unlimited</div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="sidebar-form-card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Create Plan
                            </button>
                            <a href="{{ route('subscription.plans.index') }}" class="btn btn-cancel">
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
    // Billing cycle change
    $('#billing_cycle').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#custom_interval_container').show();
        } else {
            $('#custom_interval_container').hide();
        }
    });

    // Trial toggle
    $('#has_trial').on('change', function() {
        if ($(this).is(':checked')) {
            $('#trial_days_container').slideDown();
        } else {
            $('#trial_days_container').slideUp();
        }
    });

    // Prime toggle
    $('#is_prime').on('change', function() {
        if ($(this).is(':checked')) {
            $('#prime_settings').slideDown();
        } else {
            $('#prime_settings').slideUp();
        }
    });

    // BNPL toggle
    $('#bnpl_enabled').on('change', function() {
        if ($(this).is(':checked')) {
            $('#bnpl_settings').slideDown();
            $(this).closest('.benefit-item').addClass('checked');
        } else {
            $('#bnpl_settings').slideUp();
            $(this).closest('.benefit-item').removeClass('checked');
        }
    });

    // Benefit item styling
    $('.benefit-item input[type="checkbox"]').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.benefit-item').addClass('checked');
        } else {
            $(this).closest('.benefit-item').removeClass('checked');
        }
    });
});
</script>
@endsection

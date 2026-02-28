@extends('layouts.app')

@section('title', __('subscription::lang.edit_subscription'))

@section('content')
<style>
    .edit-subscription-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }
    
    .page-header-card h1 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .page-header-card h1 .icon-box {
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 10px;
        display: flex;
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
        color: #667eea;
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
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e8e8e8;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-text {
        font-size: 12px;
        color: #888;
        margin-top: 6px;
    }
    
    /* Customer Card */
    .customer-display {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border: 2px solid #e8e8e8;
        border-radius: 12px;
    }
    
    .customer-display .avatar {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
    }
    
    .customer-display .info h5 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .customer-display .info span {
        font-size: 13px;
        color: #6c757d;
    }
    
    /* Plan Display */
    .plan-display {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        background: linear-gradient(135deg, #fffbeb, #fff);
        border: 2px solid #ffd700;
        border-radius: 12px;
    }
    
    .plan-display .plan-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffd700, #ffb700);
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .plan-display .info h5 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .plan-display .info .price {
        font-size: 18px;
        font-weight: 700;
        color: #f5a623;
    }
    
    .plan-display .info .price small {
        font-size: 12px;
        color: #999;
        font-weight: 400;
    }
    
    /* Toggle Switch */
    .toggle-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 12px;
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
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    
    /* Sidebar Cards */
    .sidebar-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .sidebar-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 20px;
    }
    
    .sidebar-card .card-header h5 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .sidebar-card .card-body {
        padding: 20px;
    }
    
    /* Status Change Warning */
    .status-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 10px;
        padding: 14px;
        margin-top: 12px;
        font-size: 13px;
        color: #856404;
    }
    
    /* Submit Buttons */
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
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
</style>

<div class="edit-subscription-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <h1>
                <div class="icon-box">
                    <i class="fas fa-edit"></i>
                </div>
                Edit Subscription
            </h1>
        </div>

        <form action="{{ route('subscription.subscriptions.update', $subscription->id) }}" method="POST" id="subscription_form">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-8">
                    {{-- Customer Info (Read Only) --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-user"></i> Customer</h4>
                        </div>
                        <div class="card-body">
                            <div class="customer-display">
                                <div class="avatar">{{ strtoupper(substr($subscription->contact->name ?? 'N', 0, 1)) }}</div>
                                <div class="info">
                                    <h5>{{ $subscription->contact->name ?? 'N/A' }}</h5>
                                    <span>{{ $subscription->contact->email ?? 'No email' }} | {{ $subscription->contact->mobile ?? 'No phone' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Plan Info (Read Only) --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-tag"></i> Current Plan</h4>
                        </div>
                        <div class="card-body">
                            @if($subscription->plan)
                                <div class="plan-display">
                                    <div class="plan-icon">
                                        <i class="fas {{ $subscription->plan->is_prime ? 'fa-crown' : 'fa-tag' }}"></i>
                                    </div>
                                    <div class="info">
                                        <h5>{{ $subscription->plan->name }}</h5>
                                        <div class="price">
                                            ${{ number_format($subscription->plan->price, 2) }}
                                            <small>/{{ $subscription->plan->billing_cycle }}</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">No plan assigned</p>
                            @endif

                            @if($plans->count() > 1)
                                <div class="mt-3">
                                    <label class="form-label" style="font-weight: 600;">Change Plan (Optional)</label>
                                    <select name="plan_id" class="form-select">
                                        <option value="">-- Keep Current Plan --</option>
                                        @foreach($plans as $plan)
                                            @if($plan->id != $subscription->plan_id)
                                                <option value="{{ $plan->id }}">${{ number_format($plan->price, 2) }}/{{ $plan->billing_cycle }} - {{ $plan->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="form-text">Changing the plan will take effect immediately.</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Status Management --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-toggle-on"></i> Status Management</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Current Status</label>
                                <select name="status" class="form-select" id="status_select">
                                    <option value="active" {{ $subscription->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="trial" {{ $subscription->status === 'trial' ? 'selected' : '' }}>Trial</option>
                                    <option value="paused" {{ $subscription->status === 'paused' ? 'selected' : '' }}>Paused</option>
                                    <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div id="cancellation_fields" style="display: {{ $subscription->status === 'cancelled' ? 'block' : 'none' }};">
                                <div class="form-group">
                                    <label>Cancellation Reason</label>
                                    <textarea name="cancellation_reason" class="form-control" rows="3">{{ $subscription->cancellation_reason }}</textarea>
                                </div>
                            </div>

                            <div class="status-warning" id="status_warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> <span id="warning_text"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-card">
                        <div class="card-header">
                            <h4><i class="fas fa-sticky-note"></i> Internal Notes</h4>
                        </div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="4" placeholder="Add any internal notes about this subscription...">{{ $subscription->notes }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Subscription Settings --}}
                    <div class="sidebar-card">
                        <div class="card-header">
                            <h5>Subscription Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="toggle-switch">
                                <label>Auto-Renewal</label>
                                <label class="switch">
                                    <input type="checkbox" name="auto_renew" value="1" {{ $subscription->auto_renew ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="toggle-switch">
                                <label>Send Renewal Reminders</label>
                                <label class="switch">
                                    <input type="checkbox" name="send_reminders" value="1" {{ $subscription->send_reminders ?? true ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Subscription Info --}}
                    <div class="sidebar-card">
                        <div class="card-header">
                            <h5>Current Info</h5>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div style="display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #f0f0f0;">
                                <span style="color: #666;">Subscription #</span>
                                <strong>{{ $subscription->subscription_no }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #f0f0f0;">
                                <span style="color: #666;">Created</span>
                                <strong>{{ $subscription->created_at->format('M d, Y') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #f0f0f0;">
                                <span style="color: #666;">Current Period Ends</span>
                                <strong>{{ $subscription->current_period_end ? $subscription->current_period_end->format('M d, Y') : 'N/A' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 12px 20px;">
                                <span style="color: #666;">Amount Paid</span>
                                <strong style="color: #28a745;">${{ number_format($subscription->amount_paid, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="sidebar-card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('subscription.subscriptions.show', $subscription->id) }}" class="btn btn-cancel">
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
    // Status change handling
    $('#status_select').on('change', function() {
        var status = $(this).val();
        var $warning = $('#status_warning');
        var $cancelFields = $('#cancellation_fields');
        
        // Show/hide cancellation fields
        if (status === 'cancelled') {
            $cancelFields.slideDown();
        } else {
            $cancelFields.slideUp();
        }
        
        // Show warnings
        var warnings = {
            'cancelled': 'Cancelling will end this subscription. The customer will lose all Prime benefits.',
            'paused': 'Pausing will temporarily suspend all Prime benefits until resumed.',
            'active': ''
        };
        
        if (warnings[status]) {
            $('#warning_text').text(warnings[status]);
            $warning.slideDown();
        } else {
            $warning.slideUp();
        }
    });
});
</script>
@endsection

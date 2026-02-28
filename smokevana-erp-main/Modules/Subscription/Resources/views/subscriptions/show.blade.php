@extends('layouts.app')

@section('title', __('subscription::lang.subscription_details'))

@section('content')
<style>
    .subscription-detail-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #FF9900;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .page-header-card h1 {
        color: #131921;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }
    
    .page-header-card .subscription-no {
        color: #6c757d;
        font-size: 14px;
    }
    
    .page-header-card .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .page-header-card .header-actions form,
    .page-header-card .header-actions a {
        display: flex;
        align-items: center;
    }
    
    .page-header-card .header-actions .btn {
        background: linear-gradient(90deg, #FF9900 0%, #e88b00 100%);
        border: 1px solid #e88b00;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(255, 153, 0, 0.3);
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }
    
    .page-header-card .header-actions .btn:hover {
        background: linear-gradient(90deg, #e88b00 0%, #d97d00 100%);
        color: #fff;
    }
    
    .page-header-card .header-actions .btn-danger {
        background: linear-gradient(90deg, #c82333 0%, #bd2130 100%);
        border: 1px solid #bd2130;
        color: #fff;
    }
    
    .page-header-card .header-actions .btn-danger:hover {
        background: linear-gradient(90deg, #bd2130 0%, #a71d2a 100%);
        color: #fff;
    }
    
    /* Status Badge */
    .status-badge-large {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge-large.active { background: #d4edda; color: #155724; }
    .status-badge-large.trial { background: #cce5ff; color: #004085; }
    .status-badge-large.pending { background: #fff3cd; color: #856404; }
    .status-badge-large.cancelled { background: #f8d7da; color: #721c24; }
    .status-badge-large.expired { background: #e2e3e5; color: #383d41; }
    .status-badge-large.paused { background: #d6d8db; color: #1b1e21; }
    
    /* Info Cards */
    .info-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .info-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 18px 24px;
    }
    
    .info-card .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-card .card-header h4 i {
        color: #FF9900;
    }
    
    .info-card .card-body {
        padding: 24px;
    }
    
    /* Info Row */
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 14px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row label {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }
    
    .info-row span {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
    }
    
    /* Customer Card */
    .customer-info-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 12px;
        border: 1px solid #e8e8e8;
    }
    
    .customer-info-card .avatar {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: linear-gradient(135deg, #FF9900, #e88b00);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
    }
    
    .customer-info-card .customer-details h5 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .customer-info-card .customer-details span {
        font-size: 13px;
        color: #6c757d;
    }
    
    .customer-info-card .customer-details a {
        color: #e88b00;
    }
    
    /* Plan Card */
    .plan-info-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: linear-gradient(135deg, #fffbeb, #fff);
        border-radius: 12px;
        border: 2px solid #ffd700;
    }
    
    .plan-info-card .plan-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: linear-gradient(135deg, #ffd700, #ffb700);
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .plan-info-card .plan-details h5 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .plan-info-card .plan-details .price {
        font-size: 20px;
        font-weight: 700;
        color: #f5a623;
    }
    
    .plan-info-card .plan-details .price small {
        font-size: 12px;
        color: #999;
        font-weight: 400;
    }
    
    /* Timeline */
    .timeline-card .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-card .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e8e8e8;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #FF9900;
        border: 2px solid #fff;
        box-shadow: 0 0 0 4px rgba(255, 153, 0, 0.2);
    }
    
    .timeline-item .time {
        font-size: 11px;
        color: #999;
        margin-bottom: 4px;
    }
    
    .timeline-item .event {
        font-size: 14px;
        font-weight: 500;
        color: #1a1a2e;
    }
    
    .timeline-item .event-detail {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }
    
    /* Benefits List */
    .benefits-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .benefit-item i {
        color: #28a745;
    }
    
    .benefit-item span {
        font-size: 13px;
        font-weight: 500;
        color: #333;
    }
    
    .benefit-item.inactive {
        opacity: 0.5;
    }
    
    .benefit-item.inactive i {
        color: #999;
    }
    
    /* Quick Actions - Amazon orange buttons */
    .subscription-detail-page .info-card .btn-success,
    .subscription-detail-page .info-card .btn-primary {
        background: linear-gradient(90deg, #FFD814 0%, #FCD200 100%) !important;
        border: 1px solid #FCD200 !important;
        color: #131921 !important;
        font-weight: 600;
    }
    
    .subscription-detail-page .info-card .btn-success:hover,
    .subscription-detail-page .info-card .btn-primary:hover {
        background: linear-gradient(90deg, #F7CA00 0%, #F2C200 100%) !important;
        color: #131921 !important;
    }
    
    .subscription-detail-page .info-card .btn-outline-secondary {
        border: 2px solid #d5d9d9;
        color: #131921;
        background: #fff;
        font-weight: 500;
    }
    
    .subscription-detail-page .info-card .btn-outline-secondary:hover {
        border-color: #FF9900;
        color: #e88b00;
        background: #FFF8F0;
    }
    
    /* Cancel modal - Amazon-style light dropdown */
    #cancelModal .form-select,
    #cancelModal .form-select option {
        background-color: #fff !important;
        color: #131921 !important;
        border: 2px solid #d5d9d9;
        border-radius: 8px;
        padding: 10px 14px;
    }
    
    #cancelModal .form-select:focus {
        border-color: #FF9900;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.2);
        outline: none;
    }
    
    #cancelModal .cancel-type-dropdown.select2-dropdown,
    #cancelModal .cancel-type-dropdown {
        background-color: #fff !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 8px !important;
    }
    #cancelModal .cancel-type-dropdown .select2-results__option {
        background-color: #fff !important;
        color: #131921 !important;
    }
    #cancelModal .cancel-type-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #FFF3E0 !important;
    }
    #cancelModal .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 2px solid #d5d9d9 !important;
        color: #131921 !important;
    }
</style>

<div class="subscription-detail-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <div>
                <h1>{{ $subscription->contact->name ?? 'Customer' }}'s Subscription</h1>
                <div class="subscription-no">{{ $subscription->subscription_no }}</div>
                <div class="mt-3">
                    <span class="status-badge-large {{ $subscription->status }}">{{ ucfirst($subscription->status) }}</span>
                </div>
            </div>
            <div class="header-actions">
                @if($subscription->status === 'paused')
                    <form action="{{ route('subscription.subscriptions.resume', $subscription->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn"><i class="fas fa-play"></i> Resume</button>
                    </form>
                @elseif(in_array($subscription->status, ['active', 'trial']))
                    <form action="{{ route('subscription.subscriptions.pause', $subscription->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn"><i class="fas fa-pause"></i> Pause</button>
                    </form>
                @endif
                
                @if(!in_array($subscription->status, ['cancelled', 'expired']))
                    <a href="{{ route('subscription.subscriptions.edit', $subscription->id) }}" class="btn"><i class="fas fa-edit"></i> Edit</a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal"><i class="fas fa-times"></i> Cancel</button>
                @endif
                
                <a href="{{ route('subscription.subscriptions.index') }}" class="btn"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Customer Info --}}
                <div class="info-card">
                    <div class="card-header">
                        <h4><i class="fas fa-user"></i> Customer</h4>
                    </div>
                    <div class="card-body">
                        <div class="customer-info-card">
                            <div class="avatar">{{ strtoupper(substr($subscription->contact->name ?? 'N', 0, 1)) }}</div>
                            <div class="customer-details">
                                <h5>{{ $subscription->contact->name ?? 'N/A' }}</h5>
                                <span>{{ $subscription->contact->email ?? 'No email' }}</span><br>
                                <span>{{ $subscription->contact->mobile ?? 'No phone' }}</span>
                                @if($subscription->contact)
                                    <br><a href="{{ action([\App\Http\Controllers\ContactController::class, 'show'], [$subscription->contact->id]) }}">View Customer Profile →</a>
                                @endif
                            </div>
                        </div>
                        
                        @if($subscription->contact)
                            <div class="tw-flex tw-flex-wrap tw-px-5 tw-mt-4">
                                @php
                                    $customerGroup = $subscription->contact->customerGroup;
                                    $planCustomerGroup = $subscription->plan?->customerGroup;
                                @endphp
                                <div class="tw-w-full tw-mb-3">
                                    <div class="tw-flex tw-items-center tw-gap-3">
                                        <span class="tw-text-gray-600 tw-text-sm">Current Customer Group:</span>
                                        @if($customerGroup)
                                            <span class="tw-px-3 tw-py-1 tw-rounded-md tw-text-sm tw-font-medium" 
                                                  style="background-color: {{ $customerGroup->color ?? '#e8e8e8' }}20; 
                                                         color: {{ $customerGroup->color ?? '#666' }}; 
                                                         border: 1px solid {{ $customerGroup->color ?? '#ddd' }};">
                                                {{ $customerGroup->name }}
                                            </span>
                                        @else
                                            <span class="tw-px-3 tw-py-1 tw-rounded-md tw-text-sm tw-bg-gray-100 tw-text-gray-600">None</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($planCustomerGroup && (!$customerGroup || $customerGroup->id !== $planCustomerGroup->id))
                                    <div class="tw-w-full">
                                        <div class="tw-p-3 tw-rounded-lg tw-bg-yellow-50 tw-border tw-border-yellow-200">
                                            <div class="tw-flex tw-items-center tw-gap-2 tw-text-yellow-800">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span class="tw-text-sm tw-font-medium">Customer group mismatch!</span>
                                            </div>
                                            <p class="tw-text-sm tw-text-yellow-700 tw-mt-1 tw-mb-2">
                                                Plan "{{ $subscription->plan->name }}" requires customer group: 
                                                <strong>{{ $planCustomerGroup->name }}</strong>
                                            </p>
                                            <form action="{{ route('subscription.subscriptions.sync-group', $subscription->id) }}" method="POST" class="tw-inline">
                                                @csrf
                                                <button type="submit" class="tw-px-3 tw-py-1 tw-text-sm tw-bg-yellow-600 tw-text-white tw-rounded hover:tw-bg-yellow-700">
                                                    <i class="fas fa-sync-alt tw-mr-1"></i> Sync Customer Group
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Plan Info --}}
                <div class="info-card">
                    <div class="card-header">
                        <h4><i class="fas fa-tag"></i> Subscription Plan</h4>
                    </div>
                    <div class="card-body">
                        @if($subscription->plan)
                            <div class="plan-info-card">
                                <div class="plan-icon">
                                    <i class="fas {{ $subscription->plan->is_prime ? 'fa-crown' : 'fa-tag' }}"></i>
                                </div>
                                <div class="plan-details">
                                    <h5>{{ $subscription->plan->name }}</h5>
                                    <div class="price">
                                        ${{ number_format($subscription->plan->price, 2) }}
                                        <small>/{{ $subscription->plan->billing_cycle }}</small>
                                    </div>
                                </div>
                            </div>

                            @if($subscription->plan->is_prime)
                                <div class="mt-4">
                                    <h6 style="font-weight: 600; color: #333; margin-bottom: 12px;">Prime Benefits:</h6>
                                    <div class="benefits-list">
                                        <div class="benefit-item {{ $subscription->plan->discount_percentage > 0 ? '' : 'inactive' }}">
                                            <i class="fas fa-percent"></i>
                                            <span>{{ $subscription->plan->discount_percentage }}% Discount on Orders</span>
                                        </div>
                                        <div class="benefit-item {{ $subscription->plan->fast_delivery_enabled ? '' : 'inactive' }}">
                                            <i class="fas fa-shipping-fast"></i>
                                            <span>Fast Delivery Priority</span>
                                        </div>
                                        <div class="benefit-item {{ $subscription->plan->prime_products_access ? '' : 'inactive' }}">
                                            <i class="fas fa-lock-open"></i>
                                            <span>Prime Products Access</span>
                                        </div>
                                        <div class="benefit-item {{ $subscription->plan->bnpl_enabled ? '' : 'inactive' }}">
                                            <i class="fas fa-credit-card"></i>
                                            <span>Buy Now Pay Later (Up to ${{ number_format($subscription->plan->bnpl_limit, 2) }})</span>
                                        </div>
                                        <div class="benefit-item {{ $subscription->plan->reward_points_multiplier > 1 ? '' : 'inactive' }}">
                                            <i class="fas fa-star"></i>
                                            <span>{{ $subscription->plan->reward_points_multiplier }}x Reward Points</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <p class="text-muted">No plan information available.</p>
                        @endif
                    </div>
                </div>

                {{-- Activity Log --}}
                <div class="info-card timeline-card">
                    <div class="card-header">
                        <h4><i class="fas fa-history"></i> Activity Log</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($subscription->logs as $log)
                                <div class="timeline-item">
                                    <div class="time">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                    <div class="event">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</div>
                                    @if($log->description)
                                        <div class="event-detail">{{ $log->description }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Subscription Details --}}
                <div class="info-card">
                    <div class="card-header">
                        <h4><i class="fas fa-info-circle"></i> Details</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Status</label>
                            <span><span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'trial' ? 'info' : 'secondary') }}">{{ ucfirst($subscription->status) }}</span></span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Subscribed At</label>
                            <span>{{ $subscription->subscribed_at ? $subscription->subscribed_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Current Period Start</label>
                            <span>{{ $subscription->current_period_start ? $subscription->current_period_start->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Current Period End</label>
                            <span>{{ $subscription->current_period_end ? $subscription->current_period_end->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Expires At</label>
                            <span>{{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'Never' }}</span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Auto Renewal</label>
                            <span>{{ $subscription->auto_renew ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Amount Paid</label>
                            <span class="text-success">${{ number_format($subscription->amount_paid, 2) }}</span>
                        </div>
                        <div class="info-row" style="padding: 14px 24px;">
                            <label>Source</label>
                            <span>{{ ucfirst(str_replace('_', ' ', $subscription->source ?? 'Manual')) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="info-card">
                    <div class="card-header">
                        <h4><i class="fas fa-bolt"></i> Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        @if(!$subscription->isPaid() && $subscription->plan)
                            <a href="{{ route('subscription.invoices.create-for-subscription', $subscription->id) }}" class="btn btn-success btn-block mb-2" style="width: 100%;">
                                <i class="fas fa-file-invoice-dollar"></i> Create Invoice
                            </a>
                        @endif
                        
                        @if(in_array($subscription->status, ['active', 'expired', 'paused']))
                            <form action="{{ route('subscription.subscriptions.renew', $subscription->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block mb-2" style="width: 100%;">
                                    <i class="fas fa-redo"></i> Renew Subscription
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('subscription.subscriptions.edit', $subscription->id) }}" class="btn btn-outline-secondary btn-block" style="width: 100%;">
                            <i class="fas fa-cog"></i> Edit Settings
                        </a>
                    </div>
                </div>

                {{-- Invoices --}}
                @if($subscription->invoices && $subscription->invoices->count() > 0)
                    <div class="info-card">
                        <div class="card-header">
                            <h4><i class="fas fa-file-invoice"></i> Invoices</h4>
                        </div>
                        <div class="card-body p-0">
                            @foreach($subscription->invoices->take(5) as $invoice)
                                <div class="info-row" style="padding: 12px 24px;">
                                    <div>
                                        <strong>{{ $invoice->invoice_no }}</strong><br>
                                        <small class="text-muted">${{ number_format($invoice->total, 2) }}</small>
                                    </div>
                                    <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Subscription</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('subscription.subscriptions.cancel', $subscription->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel this subscription?</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Cancellation Reason</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Optional - Why is this subscription being cancelled?"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Cancellation Type</label>
                        <select name="cancellation_type" id="cancellation_type_select" class="form-select select2">
                            <option value="end_of_period">Cancel at end of current period</option>
                            <option value="immediate">Cancel immediately</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#cancelModal').on('shown.bs.modal', function() {
        if ($.fn.select2 && $('#cancellation_type_select').length && !$('#cancellation_type_select').data('select2')) {
            $('#cancellation_type_select').select2({
                dropdownParent: $('#cancelModal'),
                dropdownCssClass: 'cancel-type-dropdown',
                minimumResultsForSearch: Infinity
            });
        }
    });
});
</script>
@endsection

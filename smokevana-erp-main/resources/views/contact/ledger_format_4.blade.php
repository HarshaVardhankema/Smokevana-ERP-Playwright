@if (!empty($for_pdf))
    <link rel="stylesheet" href="{{ asset('css/app.css?v=' . $asset_v) }}">
@endif
<style>
/* ========================================
   Amazon-Style Customer Details & Account Summary
   Fully Responsive Design
   ======================================== */
:root {
    --amazon-orange: #FF9900;
    --amazon-orange-hover: #FFB84D;
    --amazon-navy: #232F3E;
    --amazon-navy-light: #37475A;
    --amazon-border: #D5D9D9;
    --amazon-bg-light: #F7F8F8;
    --amazon-text-dark: #0F1111;
    --amazon-text-medium: #565959;
    --amazon-success: #00A86B;
    --amazon-purple: #9333ea;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 8px rgba(0,0,0,0.12);
    --shadow-lg: 0 8px 16px rgba(0,0,0,0.16);
}

.customer {
    display: flex;
    gap: 24px;
    width: 100%;
    margin-bottom: 24px;
}

.customer1, .customer2 {
    flex: 1;
    min-width: 0;
}

/* Amazon-Style Card */
.amazon-ledger-card {
    background: white;
    border-radius: 12px;
    border: 1.5px solid var(--amazon-border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: fit-content;
}

.amazon-ledger-card:hover {
    box-shadow: var(--shadow-md);
    border-color: #C4C9CC;
}

/* Card Header - Amazon Style */
.amazon-card-header-ledger {
    background: linear-gradient(135deg, var(--amazon-navy) 0%, var(--amazon-navy-light) 100%);
    color: white;
    padding: 16px 24px;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.amazon-card-header-ledger::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--amazon-orange), transparent);
}

/* Card Body */
.amazon-card-body-ledger {
    padding: 24px;
    font-size: 14px;
}

/* Charges & Credit Boxes - Enhanced */
.charges-credit-container {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
}

.charge-credit-box {
    flex: 1;
    padding: 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, #F7F8F8 0%, #FFFFFF 100%);
    border: 2px solid var(--amazon-border);
    transition: all 0.3s;
    text-align: center;
}

.charge-credit-box:hover {
    border-color: var(--amazon-orange);
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
}

.charge-credit-box.charges {
    border-left: 4px solid var(--amazon-success);
}

.charge-credit-box.credit {
    border-left: 4px solid var(--amazon-purple);
}

.charge-credit-label {
    color: var(--amazon-text-dark);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.charge-credit-value {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.charge-credit-value.charges {
    color: var(--amazon-success);
}

.charge-credit-value.credit {
    color: var(--amazon-purple);
}

/* Info Grid Layout */
.info-grid-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Info Row Styling */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #E4E4E4;
    transition: background-color 0.2s;
}

.info-row:hover {
    background-color: var(--amazon-bg-light);
    margin: 0 -8px;
    padding-left: 8px;
    padding-right: 8px;
    border-radius: 6px;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: var(--amazon-text-medium);
    font-size: 13px;
    font-weight: 500;
}

.info-value {
    color: var(--amazon-text-dark);
    font-size: 14px;
    font-weight: 600;
    text-align: right;
}

/* Date Range Badge */
.date-range-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #E6F7FF 0%, #F0F9FF 100%);
    border: 1.5px solid #B3E5FC;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 600;
    color: var(--amazon-text-dark);
}

.date-range-badge i {
    color: #0288D1;
    font-size: 16px;
}

/* Summary Section */
.summary-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid var(--amazon-border);
}

.summary-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--amazon-text-dark);
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--amazon-border);
}

/* Balance Due Highlight */
.balance-due-section {
    margin-top: 20px;
    padding: 16px;
    background: linear-gradient(135deg, #FFF8F0 0%, #FFFFFF 100%);
    border: 2px solid var(--amazon-orange);
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.balance-due-label {
    font-size: 16px;
    font-weight: 700;
    color: var(--amazon-text-dark);
}

.balance-due-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--amazon-orange);
}

/* Receive Payment Button - Amazon Style */
.receive-payment-btn-amazon {
    background: linear-gradient(to bottom, var(--amazon-success) 0%, #009966 100%);
    border: 1.5px solid #008055;
    color: white;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0, 168, 107, 0.2);
}

.receive-payment-btn-amazon:hover {
    background: linear-gradient(to bottom, #00C07A 0%, var(--amazon-success) 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 168, 107, 0.3);
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .customer {
        flex-direction: column;
        gap: 20px;
    }
    
    .customer1, .customer2 {
        width: 100%;
    }
    
    .info-grid-container {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .charges-credit-container {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .amazon-card-body-ledger {
        padding: 16px;
    }
    
    .charge-credit-value {
        font-size: 20px;
    }
    
    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .info-value {
        text-align: left;
    }
}
</style>
<div class="customer" style="width: 100%;">
    <!-- Customer Details Card -->
    <div class="customer1">
        <div class="amazon-ledger-card">
            <!-- Card Header -->
            @if ($contact->type == 'customer')
                <div class="amazon-card-header-ledger">
                    <span><i class="fa fa-user-circle" style="margin-right: 8px;"></i>Customer Details</span>
                    @if (in_array($contact->type, ['customer', 'both']) && auth()->user()->can('sell.payments'))
                        <a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], ['contact_id' => $contact->id]) }}?due_payment_type=sell" 
                           class="receive-payment-btn-amazon pay_sale_due" 
                           data-container=".pay_contact_due_modal">
                            <i class="fa fa-money-bill-alt"></i> Receive Payment
                        </a>
                    @endif
                </div>
            @elseif ($contact->type == 'supplier')
                <div class="amazon-card-header-ledger">
                    <span><i class="fa fa-truck" style="margin-right: 8px;"></i>Vendor Details</span>
                </div>
            @elseif ($contact->type == 'both')
                <div class="amazon-card-header-ledger">
                    <span><i class="fa fa-users" style="margin-right: 8px;"></i>Customer & Vendor Details</span>
                    @if (in_array($contact->type, ['customer', 'both']) && auth()->user()->can('sell.payments'))
                        <a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], ['contact_id' => $contact->id]) }}?due_payment_type=sell" 
                           class="receive-payment-btn-amazon pay_sale_due" 
                           data-container=".pay_contact_due_modal">
                            <i class="fa fa-money-bill-alt"></i> Receive Payment
                        </a>
                    @endif
                </div>
            @endif
            
            <div class="amazon-card-body-ledger">

                  @if ($contact->type == 'supplier' )
        <div class=" gradiantDiv"
            style=" color: white; padding: 8px 16px;font-weight:700; border-top-left-radius: 8px; border-top-right-radius: 8px; font-size: 16px;">
            Vendor Details
        </div>
             @endif
                           @if ($contact->type == 'both')
        <div class="gradiantDiv"
            style="color: white; padding: 8px 16px;font-weight:700; border-top-left-radius: 8px; border-top-right-radius: 8px; font-size: 16px;">
            <span>V and C Details</span>
        </div>
             @endif
        <div style="padding: 16px; font-size: 14px;">


            @php
                $amount_due = 0;
                $current_due = 0;
                $due_1_30_days = 0;
                $due_30_60_days = 0;
                $due_60_90_days = 0;
                $due_over_90_days = 0;
            @endphp

            @foreach ($ledger_details['ledger'] as $data)
                @php
                    if (empty($data['total_due'])) {
                        continue;
                    }
                    if ($data['payment_status'] != 'paid' && !empty($data['due_date'])) {
                        if (!empty($data['transaction_type']) && $data['transaction_type'] == 'ledger_discount') {
                            $data['total_due'] = -1 * $data['total_due'];
                        }
                        $amount_due += $data['total_due'];

                        $days_diff = $data['due_date']->diffInDays();
                        if ($days_diff == 0) {
                            $current_due += $data['total_due'];
                        } elseif ($days_diff > 0 && $days_diff <= 30) {
                            $due_1_30_days += $data['total_due'];
                        } elseif ($days_diff > 30 && $days_diff <= 60) {
                            $due_30_60_days += $data['total_due'];
                        } elseif ($days_diff > 60 && $days_diff <= 90) {
                            $due_60_90_days += $data['total_due'];
                        } elseif ($days_diff > 90) {
                            $due_over_90_days += $data['total_due'];
                        }
                    }
                @endphp
            @endforeach

                @php
                    // Overall net due (customer + supplier)
                    $overall_due = $ledger_details['all_balance_due'] ?? 0;

                    // Supplier / purchase-side due: total purchases minus purchase payments
                    $purchase_total = $ledger_details['all_total_purchase'] ?? 0;
                    $purchase_paid = $ledger_details['all_purchase_paid'] ?? 0;
                    $purchase_due = $purchase_total - $purchase_paid;
                @endphp

                <!-- Charges, Credit & Vendor Due Cards -->
                <div class="charges-credit-container">
                    <!-- Main Balance card -->
                    <div class="charge-credit-box charges">
                        <span class="charge-credit-label">@lang('lang_v1.balance_due')</span>
                        <p class="charge-credit-value charges">@format_currency($overall_due)</p>
                    </div>

                    <!-- Contact credit / advance card (same as before) -->
                    <div class="charge-credit-box credit">
                        <span class="charge-credit-label">@lang('contact.credit')</span>
                        <p class="charge-credit-value credit">@format_currency(($contact->balance ?? 0) - $overall_due)</p>
                    </div>

                    <!-- NEW: Vendor purchase due card (only when supplier or both) -->
                    @if ($contact->type == 'supplier' || $contact->type == 'both')
                        <div class="charge-credit-box credit">
                            <span class="charge-credit-label">@lang('contact.total_purchase_due')</span>
                            {{-- Highlight vendor due in red --}}
                            <p class="charge-credit-value credit" style="color: #dc2626;">@format_currency($purchase_due)</p>
                        </div>
                    @endif
                </div>

                <!-- Customer Information Grid -->
                <div class="info-grid-container">
                    <!-- Left Column - Customer Info -->
                    <div>
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">FEIN LICENSE</span>
                                <span class="info-value">{{ $contact->tax_number ?? 'N/A' }}</span>
                            </div>
                        @endif
                        
                        @if ($contact->type == 'supplier' || $contact->type == 'both')
                            <div
                                style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                                <div style="color: #A9A9A9; font-size: 14px;">Contact Name</div>
                                <div style="color: #000; font-size: 14px; font-weight: 500;">
                                    {{ $contact->name ?? 'N/A' }}
                                </div>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Main Phone</span>
                                <span class="info-value">{{ $contact->mobile ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'supplier')
                            <div class="info-row">
                                <span class="info-label">Phone 1</span>
                                <span class="info-value">{{ $contact->mobile ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Main E-Mail</span>
                                <span class="info-value">{{ $contact->email ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'supplier')
                            <div class="info-row">
                                <span class="info-label">E-Mail</span>
                                <span class="info-value">{{ $contact->email ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Work Phone</span>
                                <span class="info-value">{{ $contact->alternate_number ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'supplier')
                            <div class="info-row">
                                <span class="info-label">Phone 2</span>
                                <span class="info-value">{{ $contact->alternate_number ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Account Opened</span>
                                <span class="info-value">{{ $contact->created_at ? @format_date($contact->created_at) : 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Salesperson</span>
                                <span class="info-value">
                                    @if (!empty($selsRepInfo))
                                        @foreach ($selsRepInfo as $rep)
                                            <span>{{ $rep->username }}</span>@if(!$loop->last), @endif
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Last Payment</span>
                                <span class="info-value">
                                    @isset($last_payment->amount)
                                        @format_currency($last_payment->amount)
                                    @else
                                        N/A
                                    @endisset
                                </span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Last Payment Date</span>
                                <span class="info-value">
                                    @isset($last_payment->paid_on)
                                        {{ \Carbon\Carbon::parse($last_payment->paid_on)->format('d-m-Y') }}
                                    @else
                                        N/A
                                    @endisset
                                </span>
                            </div>
                        @endif
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Invoice Terms</span>
                                <span class="info-value">{{ $contact->pay_term_number ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($contact->type == 'supplier' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Vendor Terms</span>
                                <span class="info-value">{{ $contact->pay_term_number ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Right Column - Payment Aging -->
                    <div>
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div class="info-row">
                                <span class="info-label">Unapplied Payments</span>
                                <span class="info-value">@format_currency(0)</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Current Due</span>
                                <span class="info-value">@format_currency($current_due)</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">1-30 Days Past Due</span>
                                <span class="info-value">@format_currency($due_1_30_days)</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">31-60 Days Past Due</span>
                                <span class="info-value">@format_currency($due_30_60_days)</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">61-90 Days Past Due</span>
                                <span class="info-value">@format_currency($due_60_90_days)</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Over 90 Days Past Due</span>
                                <span class="info-value">@format_currency($due_over_90_days)</span>
                            </div>
                        @endif

                         @if ($contact->type == 'supplier' || $contact->type == 'both')
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px; font-weight: 400;">Credit Limit</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;"> {{ $contact->credit_limit?? 'N/A' }}</div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px; font-weight: 400;">Y-T_D Payments</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;"> </div>

                    </div>
                      @if ($contact->type == 'supplier' )
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px; font-weight: 400;">Payment Hold</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;"> </div>
                    </div>
                    @endif
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">Temporary Vendor</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;"> </div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">Eligile for 1099</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;"></div>
                    </div>
                  
                      @endif
                </div>
            </div>
        </div>
    </div>

    <div class=" customer2 tw-bg-white tw-rounded-lg tw-shadow-lg tw-border tw-border-gray-500 "
        style=" border: 1px solid rgba(0, 0, 13, 0.24); height: fit-content;">
        <div class="tw-text-white tw-py-2 tw-px-4 tw-rounded-t-lg tw-font-bold gradiantDiv"
            style="padding: 8px 16px; border-top-left-radius: 8px; border-top-right-radius: 8px; font-size: 16px;">
            Account Summary
        </div>
        <div style="padding: 16px; font-size: 14px;">
            <div
                style="padding: 8px 16px; font-size: 14px; color: #4b5563; font-weight: 600; display: flex; gap: 6px; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 15 16"
                    fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M14.1667 8.0013C14.1667 11.9133 10.9953 15.0846 7.08333 15.0846C3.17131 15.0846 0 11.9133 0 8.0013C0 4.08928 3.17131 0.917969 7.08333 0.917969C10.9953 0.917969 14.1667 4.08928 14.1667 8.0013ZM7.08333 12.0742C7.37672 12.0742 7.61458 11.8364 7.61458 11.543V7.29297C7.61458 6.99958 7.37672 6.76172 7.08333 6.76172C6.78994 6.76172 6.55208 6.99958 6.55208 7.29297V11.543C6.55208 11.8364 6.78994 12.0742 7.08333 12.0742ZM7.08333 4.45964C7.47455 4.45964 7.79167 4.77677 7.79167 5.16797C7.79167 5.55917 7.47455 5.8763 7.08333 5.8763C6.69212 5.8763 6.375 5.55917 6.375 5.16797C6.375 4.77677 6.69212 4.45964 7.08333 4.45964Z"
                        fill="#6BE2F2" />
                </svg>
                <span style="color: #000; font-size: 16px; font-style: normal; font-weight: 700; line-height: 28px;">
                    {{ $ledger_details['start_date'] }} to {{ $ledger_details['end_date'] }}
                </span>
            </div>
            <div style="display: flex; gap: 20px;">
                <div style="width: 100%;">
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 0px 46px 16px 15px;">
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div style="color: #A9A9A9; font-size: 14px;">Total Invoice</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['total_invoice'])</div>
                        @endif

                        @if ($contact->type == 'supplier' || $contact->type == 'both')
                            <div style="color: #A9A9A9; font-size: 14px;">@lang('report.total_purchase')</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['all_total_purchase'])</div>
                        @endif
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">@lang('sale.total_paid')</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['total_paid'])
                        </div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; padding: 14px 46px 0px 15px;">
                        <div
                            style="color: #000;  font-size: 16px; font-style: normal; font-weight: 700; line-height: 28px;">
                            Overall Summary
                        </div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div style="color: #A9A9A9; font-size: 14px;">Total Invoice</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['all_total_invoice'])</div>
                        @endif


                        @if ($contact->type == 'supplier' || $contact->type == 'both')
                            <tr>
                                <div style="color: #A9A9A9; font-size: 14px;">@lang('report.total_purchase')</div>
                                <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['all_total_purchase'])</div>
                            </tr>
                        @endif

                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                        @if ($contact->type == 'customer' || $contact->type == 'both')
                            <div style="color: #A9A9A9; font-size: 14px;">Total Paid</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['all_invoice_paid'])</div>
                        @endif
                        @if ($contact->type == 'supplier' || $contact->type == 'both')
                            <tr>
                                <div style="color: #A9A9A9; font-size: 14px;">@lang('sale.total_paid')</div>
                                <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['all_purchase_paid'])</div>
                            </tr>
                        @endif
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2px; padding: 14px 46px 16px 15px;">
                        <div
                            style="color: #000;  font-size: 16px; font-style: normal; line-height: 28px; font-weight: 700;">
                            Balance Due
                        </div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">@format_currency($ledger_details['all_balance_due'])</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-sm-12 @if (!empty($for_pdf)) width-100 @endif">
    <p class="text-center" style="text-align: center;"><strong>@lang('lang_v1.ledger_table_heading', ['start_date' => $ledger_details['start_date'], 'end_date' => $ledger_details['end_date']])</strong></p>
    <div class="table-responsive">
        <table class="table table-striped @if (!empty($for_pdf)) table-pdf td-border @endif"
            id="ledger_table" style="text-align: center;">
            <thead>
                <tr class="row-border">
                    <th width="10%" class="text-center">@lang('lang_v1.date')</th>
                    <th width="9%" class="text-center">@lang('purchase.ref_no')</th>
                    <th width="8%" class="text-center">@lang('lang_v1.type')</th>
                    <th width="10%" class="text-center">@lang('sale.location')</th>
                    <th width="10%" class="text-center">PAYMENT STATUS</th>
                    <th width="10%" class="text-right">@lang('sale.amount')</th>
                    <th width="12%" class="text-center">@lang('lang_v1.payment_method')</th>
                    <th width="15%" class="text-center">@lang('report.others')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ledger_details['ledger'] as $data)
                    @if ($data['type'] == 'Opening Balance')
                        @continue
                    @endif
                    @php
                        $row_amount = (isset($data['debit']) && $data['debit'] != '') ? $data['debit'] : (isset($data['credit']) && $data['credit'] != '' ? $data['credit'] : '');
                    @endphp
                    <tr @if (!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
                        <td class="row-border">{{ @format_datetime($data['date']) }}</td>
                        <td>{{ $data['ref_no'] }}</td>
                        <td>{{ str_replace('Sell', 'Sale', $data['type']) }}</td>
                        <td>{{ $data['location'] }}</td>
                        <td>{{ strtoupper($data['payment_status'] ?? '') }}</td>
                        <td class="ws-nowrap align-right">
                            @if ($row_amount != '')
                                @format_currency($row_amount)
                            @endif
                        </td>
                        <td>
                            {{ $data['payment_method'] ?? '' }}
                            @if ($row_amount != '')
                                <br>@format_currency($row_amount)
                            @endif
                        </td>
                        <td>
                            {!! $data['others'] !!}
                            @if (!empty($is_admin) && !empty($data['transaction_id']) && $data['transaction_type'] == 'ledger_discount')
                                <br>
                                <button type="button"
                                    class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_ledger_discount"
                                    data-href="{{ action([\App\Http\Controllers\LedgerDiscountController::class, 'destroy'], ['ledger_discount' => $data['transaction_id']]) }}"><i
                                        class="fas fa-trash"></i></button>
                                <button type="button"
                                    class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary btn-modal"
                                    data-href="{{ action([\App\Http\Controllers\LedgerDiscountController::class, 'edit'], ['ledger_discount' => $data['transaction_id']]) }}"
                                    data-container="#edit_ledger_discount_modal"><i class="fas fa-edit"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.summary_hidden').hide();
        $('#show_info_btn').click(function() {
            $('.summary_hidden').toggle('slow');
        });
    });
</script>



<!-- Include CSS for PDF -->
{{-- @if (!empty($for_pdf))
    <link rel="stylesheet" href="{{ asset('css/app.css?v=' . $asset_v) }}">
@endif

<div class="tw-flex tw-gap-4 tw-p-4" style="width: 100%; gap: 60px;">
    <!-- Customer Details Section -->
    <div style="width: 50%; border: 1px solid rgba(0, 0, 13, 0.24); background-color: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(270deg, #6BE2F2 0%, #428CD9 11.22%, #4913B7 100%); color: white; padding: 8px 16px; font-weight: 700; border-top-left-radius: 8px; border-top-right-radius: 8px; font-size: 16px; display: flex; justify-content: space-between; align-items: center;">
            <span>Customer Details</span>
            @if (in_array($contact->type, ['customer', 'both']) && auth()->user()->can('sell.payments'))
                <a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], ['contact_id' => $contact->id]) }}?due_payment_type=sell" 
                   class="btn btn-sm btn-success receive-payment-btn pay_sale_due" 
                   style="background-color: #28a745; border-color: #28a745; color: white; padding: 4px 12px; font-size: 12px; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;"
                   data-container=".pay_contact_due_modal">
                    <i class="fa fa-money-bill-alt"></i> Receive Payment
                </a>
            @endif
        </div>
        <!-- Content -->
        <div style="padding: 16px; font-size: 14px;">
            @php
                $amount_due = 0;
                $current_due = 0;
                $due_1_30_days = 0;
                $due_30_60_days = 0;
                $due_60_90_days = 0;
                $due_over_90_days = 0;
                $last_payment = null;
                $last_payment_date = null;

                if (!empty($ledger_details['ledger']) && is_array($ledger_details['ledger'])) {
                    foreach ($ledger_details['ledger'] as $data) {
                        if (empty($data['total_due']) || $data['payment_status'] === 'paid' || empty($data['due_date'])) {
                            continue;
                        }
                        $total_due = $data['total_due'];
                        if (!empty($data['transaction_type']) && $data['transaction_type'] === 'ledger_discount') {
                            $total_due = -1 * $data['total_due'];
                        }
                        $amount_due += $total_due;

                        try {
                            $due_date = is_string($data['due_date'])
                                ? \Carbon\Carbon::parse($data['due_date'])
                                : $data['due_date'];
                            $days_diff = \Carbon\Carbon::today()->diffInDays($due_date, false);

                            if ($days_diff <= 0) {
                                $current_due += $total_due;
                            } elseif ($days_diff <= 30) {
                                $due_1_30_days += $total_due;
                            } elseif ($days_diff <= 60) {
                                $due_30_60_days += $total_due;
                            } elseif ($days_diff <= 90) {
                                $due_60_90_days += $total_due;
                            } else {
                                $due_over_90_days += $total_due;
                            }
                        } catch (\Exception $e) {
                            continue;
                        }

                        if ($data['payment_status'] === 'paid' && !empty($data['credit']) && !empty($data['date'])) {
                            $last_payment = ['amount' => $data['credit'], 'paid_on' => $data['date']];
                            $last_payment_date = $data['date'];
                        }
                    }
                }
            @endphp

            <!-- Charges and Credit Row -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 16px; padding: 0px 50px 0px 39px; border: 2px solid #B3B3B3; border-radius: 10px; width: 100%;">
                <div style="width: 48%; border-right: 2px solid gray; padding: 10px;">
                    <span style="color: #000; font-size: 16px; font-style: normal; font-weight: 600; line-height: 29px;">Charges</span><br>
                    <span style="color: #9333ea; font-size: 16px;">{{ !empty($ledger_details['all_balance_due']) ? @format_currency($ledger_details['all_balance_due']) : '' }}</span>
                </div>
                <div style="width: 50%; text-align: left; padding: 10px 10px 10px 25px;">
                    <span style="color: #000; font-size: 16px; font-style: normal; font-weight: 600; line-height: 29px;">Credit</span><br>
                    <span style="color: #9333ea; font-size: 16px;">{{ !empty($ledger_details['all_balance_due']) && !empty($contact->balance) ? @format_currency($ledger_details['all_balance_due'] - $contact->balance) : '' }}</span>
                </div>
            </div>

            <!-- Two-Column Layout for Other Fields -->
            <div style="display: flex; gap: 20px;">
                <!-- Left Column -->
                <div style="width: 48%;">
                    <div style="width: 90%; margin: auto;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">FEIN LICENSE</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ $contact->tax_number ?? '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Main Phone</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ $contact->mobile ?? '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Main E-Mail</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ $contact->email ?? '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Work Phone</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ $contact->mobile ?? '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Account Opened</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($contact->created_at) ? @format_date($contact->created_at) : '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Salesperson</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">
                                @if (!empty($selsRepInfo) && is_array($selsRepInfo))
                                    @foreach ($selsRepInfo as $rep)
                                        <div class="tw-text-black tw-px-2 tw-rounded tw-text-sm tw-rounded-md" style="width:100px; text-align:end;">
                                            <span>{{ $rep->username ?? '' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span></span>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Last Payment</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($last_payment['amount']) ? @format_currency($last_payment['amount']) : '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Last Payment Date</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($last_payment_date) ? \Carbon\Carbon::parse($last_payment_date)->format('d-m-Y') : '' }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Invoice Terms</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ $contact->pay_term_number ?? '' }}</div>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div style="width: 42%; text-align: right;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px; font-weight: 400;">Unapplied Payments</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['unapplied_payments']) ? @format_currency($ledger_details['unapplied_payments']) : '' }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px; font-weight: 400;">Current Due</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($current_due) ? @format_currency($current_due) : '' }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">1-30 Days Past Due</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($due_1_30_days) ? @format_currency($due_1_30_days) : '' }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">31-60 Days Past Due</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($due_30_60_days) ? @format_currency($due_30_60_days) : '' }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">61-90 Days Past Due</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($due_60_90_days) ? @format_currency($due_60_90_days) : '' }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 11px 0px 11px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">Over 90 Days Past Due</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($due_over_90_days) ? @format_currency($due_over_90_days) : '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Summary Section -->
    <div class="tw-bg-white tw-rounded-lg tw-shadow-lg tw-border tw-border-gray-500" style="width: 50%; border: 1px solid rgba(0, 0, 13, 0.24); height: fit-content;">
        <!-- Header -->
        <div class="tw-text-white tw-py-2 tw-px-4 tw-rounded-t-lg tw-font-bold" style="background: linear-gradient(270deg, #6BE2F2 0%, #428CD9 11.22%, #4913B7 100%); color: white; padding: 8px 16px; border-top-left-radius: 8px; border-top-right-radius: 8px; font-size: 16px;">
            Account Summary
        </div>
        <!-- Date Range -->
        <div style="padding: 16px; font-size: 14px;">
            <div style="padding: 8px 16px; font-size: 14px; color: #4b5563; font-weight: 600; display: flex; gap: 6px; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 15 16" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.1667 8.0013C14.1667 11.9133 10.9953 15.0846 7.08333 15.0846C3.17131 15.0846 0 11.9133 0 8.0013C0 4.08928 3.17131 0.917969 7.08333 0.917969C10.9953 0.917969 14.1667 4.08928 14.1667 8.0013ZM7.08333 12.0742C7.37672 12.0742 7.61458 11.8364 7.61458 11.543V7.29297C7.61458 6.99958 7.37672 6.76172 7.08333 6.76172C6.78994 6.76172 6.55208 6.99958 6.55208 7.29297V11.543C6.55208 11.8364 6.78994 12.0742 7.08333 12.0742ZM7.08333 4.45964C7.47455 4.45964 7.79167 4.77677 7.79167 5.16797C7.79167 5.55917 7.47455 5.8763 7.08333 5.8763C6.69212 5.8763 6.375 5.55917 6.375 5.16797C6.375 4.77677 6.69212 4.45964 7.08333 4.45964Z" fill="#6BE2F2" />
                </svg>
                <span style="color: #000; font-size: 16px; font-style: normal; font-weight: 700; line-height: 28px;">
                    {{ !empty($ledger_details['start_date']) ? $ledger_details['start_date'] : 'N/A' }} to {{ !empty($ledger_details['end_date']) ? $ledger_details['end_date'] : 'N/A' }}
                </span>
            </div>
            <!-- Content -->
            <div style="display: flex; gap: 20px;">
                <div style="width: 100%;">
                    @if ($contact->type == 'customer' || $contact->type == 'both')
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 0px 46px 16px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Total Invoice</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['total_invoice']) ? @format_currency($ledger_details['total_invoice']) : '' }}</div>
                        </div>
                    @endif
                    @if ($contact->type == 'supplier' || $contact->type == 'both')
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 0px 46px 16px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">@lang('report.total_purchase')</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['all_total_purchase']) ? @format_currency($ledger_details['all_total_purchase']) : '' }}</div>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                        <div style="color: #A9A9A9; font-size: 14px;">@lang('sale.total_paid')</div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['total_paid']) ? @format_currency($ledger_details['total_paid']) : '' }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; padding: 14px 46px 0px 15px;">
                        <div style="color: #000; font-family: 'Amazon Ember'; font-size: 16px; font-style: normal; font-weight: 700; line-height: 28px;">
                            Overall Summary
                        </div>
                    </div>
                    @if ($contact->type == 'customer' || $contact->type == 'both')
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Total Invoice</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['all_total_invoice']) ? @format_currency($ledger_details['all_total_invoice']) : '' }}</div>
                        </div>
                    @endif
                    @if ($contact->type == 'supplier' || $contact->type == 'both')
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">@lang('report.total_purchase')</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['all_total_purchase']) ? @format_currency($ledger_details['all_total_purchase']) : '' }}</div>
                        </div>
                    @endif
                    @if ($contact->type == 'customer' || $contact->type == 'both')
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">Total Paid</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['all_invoice_paid']) ? @format_currency($ledger_details['all_invoice_paid']) : '' }}</div>
                        </div>
                    @endif
                    @if ($contact->type == 'supplier' || $contact->type == 'both')
                        <div style="display: flex; justify-content: space-between; margin-bottom: 2px; border-bottom: 1px solid #E4E4E4; padding: 14px 46px 16px 15px;">
                            <div style="color: #A9A9A9; font-size: 14px;">@lang('sale.total_paid')</div>
                            <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['all_purchase_paid']) ? @format_currency($ledger_details['all_purchase_paid']) : '' }}</div>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; padding: 14px 46px 16px 15px;">
                        <div style="color: #000; font-family: 'Amazon Ember'; font-size: 16px; font-style: normal; line-height: 28px; font-weight: 700;">
                            Balance Due
                        </div>
                        <div style="color: #000; font-size: 14px; font-weight: 500;">{{ !empty($ledger_details['all_balance_due']) ? @format_currency($ledger_details['all_balance_due']) : '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-sm-12 @if (!empty($for_pdf)) width-100 @endif">
    <p class="text-center" style="text-align: center;"><strong>@lang('lang_v1.ledger_table_heading', ['start_date' => !empty($ledger_details['start_date']) ? $ledger_details['start_date'] : 'N/A', 'end_date' => !empty($ledger_details['end_date']) ? $ledger_details['end_date'] : 'N/A'])</strong></p>
    <div class="table-responsive">
        <table class="table table-striped @if (!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table" style="text-align: center;">
            <thead>
                <tr class="row-border">
                    <th width="10%" class="text-center">@lang('lang_v1.date')</th>
                    <th width="9%" class="text-center">@lang('purchase.ref_no')</th>
                    <th width="8%" class="text-center">@lang('lang_v1.type')</th>
                    <th width="10%" class="text-center">@lang('sale.location')</th>
                    <th width="10%" class="text-center">PAYMENT STATUS</th>
                    <th width="10%" class="text-right">@lang('sale.amount')</th>
                    <th width="12%" class="text-center">@lang('lang_v1.payment_method')</th>
                    <th width="15%" class="text-center">@lang('report.others')</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($ledger_details['ledger']) && is_array($ledger_details['ledger']))
                    @foreach ($ledger_details['ledger'] as $data)
                        @if ($data['type'] === 'Opening Balance')
                            @continue
                        @endif
                        @php
                            $row_amount_2 = (isset($data['debit']) && $data['debit'] != '') ? $data['debit'] : (isset($data['credit']) && $data['credit'] != '' ? $data['credit'] : '');
                        @endphp
                        <tr @if (!empty($for_pdf) && $loop->iteration % 2 == 0) class="odd" @endif>
                            <td class="row-border">{{ !empty($data['date']) ? @format_datetime($data['date']) : '' }}</td>
                            <td>{{ $data['ref_no'] ?? '' }}</td>
                            <td>{{ str_replace('Sell', 'Sale', $data['type'] ?? '') }}</td>
                            <td>{{ $data['location'] ?? '' }}</td>
                            <td>{{ strtoupper($data['payment_status'] ?? '') }}</td>
                            <td class="ws-nowrap align-right">{{ $row_amount_2 != '' ? @format_currency($row_amount_2) : '' }}</td>
                            <td>
                                {{ $data['payment_method'] ?? '' }}
                                @if ($row_amount_2 != '')
                                    <br>@format_currency($row_amount_2)
                                @endif
                            </td>
                            <td>
                                {!! $data['others'] ?? '' !!}
                                @if (!empty($is_admin) && !empty($data['transaction_id']) && $data['transaction_type'] === 'ledger_discount')
                                    <br>
                                    <button type="button" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error delete_ledger_discount" data-href="{{ action([\App\Http\Controllers\LedgerDiscountController::class, 'destroy'], ['ledger_discount' => $data['transaction_id']]) }}"><i class="fas fa-trash"></i></button>
                                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary btn-modal" data-href="{{ action([\App\Http\Controllers\LedgerDiscountController::class, 'edit'], ['ledger_discount' => $data['transaction_id']]) }}" data-container="#edit_ledger_discount_modal"><i class="fas fa-edit"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle visibility on button click
        $('.summary_hidden').hide();
        $('#show_info_btn').click(function() {
            $('.summary_hidden').toggle('slow');
        });
    });
</script> --}}

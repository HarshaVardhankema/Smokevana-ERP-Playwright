@php
    $header_display_name = !empty($contact->supplier_business_name) ? $contact->supplier_business_name : $contact->name;
    $billing_address = trim(collect([
        $contact->address_line_1,
        $contact->city,
        $contact->state,
        $contact->zip_code,
    ])->filter()->implode(', '));

    // If a dedicated shipping address is not stored, show billing address.
    $shipping_address = trim(collect([
        $contact->shipping_address ?? null,
    ])->filter()->implode(', '));
    if (empty($shipping_address)) {
        $shipping_address = $billing_address;
    }
@endphp

<style>
    /* Amazon theme card - #37475a */
    .contact-header-card {
        display: flex;
        gap: 18px;
        align-items: stretch;
        padding: 18px 20px;
        background: #37475a;
        border-radius: 10px;
        border: 1px solid #4a5d6e;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    .contact-header-left { flex: 0 0 280px; min-width: 0; }
    .contact-header-middle { flex: 1 1 auto; min-width: 0; padding-left: 18px; border-left: 1px solid #4a5d6e; }
    .contact-header-right { flex: 0 0 240px; border-left: 1px solid #4a5d6e; padding-left: 18px; }

    .contact-header-title {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .contact-header-title i { color: #ff9900; }
    .contact-header-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        color: #b8c4ce;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .contact-header-meta i { color: #9ca8b5; }

    .contact-header-addresses { font-size: 13px; }
    .contact-header-addresses-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        align-items: start;
    }
    .contact-address-card {
        border: 1px solid #4a5d6e;
        border-radius: 10px;
        padding: 10px 12px;
        background: rgba(0, 0, 0, 0.2);
        min-height: 64px;
    }
    .contact-address-title {
        font-size: 12px;
        font-weight: 700;
        color: #e5e7eb;
        margin-bottom: 6px;
    }
    .contact-address-value { color: #d1d5db; line-height: 1.4; word-break: break-word; }
    .contact-address-subtitle {
        font-size: 12px;
        color: #b8c4ce;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        word-break: break-word;
    }
    .contact-address-subtitle i { color: #ff9900; }

    .contact-badges { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .contact-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .badge-tier { background: #ffeeac; color: #5c4900; border-color: #362b00; }
    .badge-brand { background: #c8e6c9; color: #1b5e20; border-color: #1b5e20; }
    .badge-rep { background: #bbdefb; color: #0d47a1; border-color: #0d47a1; }

    /* Financial Summary - on dark card */
    .financial-summary-container {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .financial-summary-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #4a5d6e;
        white-space: nowrap;
    }
    .financial-summary-header i {
        color: #ff9900;
        font-size: 16px;
    }
    .financial-summary-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .financial-summary-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        border-left: 3px solid transparent;
    }
    .financial-summary-item.open-balance {
        border-left-color: #ff9900;
        background: linear-gradient(90deg, rgba(255, 153, 0, 0.15) 0%, rgba(0, 0, 0, 0.2) 100%);
    }
    .financial-summary-item.overdue-balance {
        border-left-color: #ef4444;
        background: linear-gradient(90deg, rgba(239, 68, 68, 0.2) 0%, rgba(0, 0, 0, 0.2) 100%);
    }
    .financial-item-icon {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .financial-item-icon.orange { background-color: #ff9900; }
    .financial-item-icon.red { background-color: #ef4444; }
    .financial-item-content {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }
    .financial-item-label {
        color: #b8c4ce;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .financial-item-value {
        font-weight: 700;
        color: #ffffff;
        font-size: 16px;
        line-height: 1.3;
        white-space: nowrap;
    }
    .contact-header-card .display_currency { color: #ffffff; }

    @media (max-width: 1199px) {
        .contact-header-right { flex: 0 0 220px; }
        .financial-item-value { font-size: 15px; }
    }
    @media (max-width: 991px) {
        .contact-header-card { flex-direction: column; }
        .contact-header-left { flex-basis: auto; }
        .contact-header-middle { border-left: 0; border-top: 1px solid #4a5d6e; padding-left: 0; padding-top: 14px; }
        .contact-header-right { border-left: 0; border-top: 1px solid #4a5d6e; padding-left: 0; padding-top: 14px; flex-basis: auto; }
        .contact-header-addresses-grid { grid-template-columns: 1fr; }
        .financial-summary-items { flex-direction: row; gap: 16px; }
        .financial-summary-item { flex: 1; }
    }
    @media (max-width: 576px) {
        .financial-summary-items { flex-direction: column; }
    }
</style>

<div class="contact-header-card">
    <div class="contact-header-left">
        <div class="contact-header-title">
            <i class="fas fa-user-tie" aria-hidden="true"></i>
            <span title="{{ $header_display_name }}">{{ $header_display_name }}</span>
        </div>
        <div class="contact-header-meta">
            <span>  
                <i class="fas fa-user" aria-hidden="true"></i>
                {{ !empty($contact->name) ? $contact->name : 'N/A' }}
            </span>
        </div>
        {{-- <div class="contact-header-meta">
            <span>
                <i class="fas fa-envelope" aria-hidden="true"></i>
                {{ !empty($contact->email) ? $contact->email : 'N/A' }}
            </span>
            <span>
                <i class="fas fa-phone" aria-hidden="true"></i>
                {{ !empty($contact->mobile) ? $contact->mobile : 'N/A' }}
            </span>
        </div> --}}

        <div class="contact-badges">
            <span class="contact-badge badge-tier">Customer Tier: {{ $customerTier }}</span>
            @if(!empty($brandName))
                <span class="contact-badge badge-brand">Brand: {{ $brandName }}</span>
            @endif
            @if ($contact->type == 'customer' || $contact->type == 'both')
                @if (!empty($selsRepInfo))
                    @foreach ($selsRepInfo as $rep)
                        <span class="contact-badge badge-rep">Rep: {{ $rep->username }}</span>
                    @endforeach
                @else
                    <span class="contact-badge badge-rep">Rep: N/A</span>
                @endif
                @if($contact->is_tax_exempt)
                    <span class="contact-badge" style="background: #d1fae5; color: #065f46; border-color: #065f46;">
                        <i class="fa fa-check-circle"></i> Tax Exempt
                    </span>
                @endif
            @endif
        </div>
    </div>

    <div class="contact-header-middle">
        <div class="contact-header-addresses">
            <div class="contact-header-addresses-grid">
                <div class="contact-address-card">
                    <div class="contact-address-title">Billing Address</div>
                    <div class="contact-address-subtitle">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <span>{{ !empty($contact->email) ? $contact->email : 'N/A' }}</span>
                    </div>
                    <div class="contact-address-value">{{ !empty($billing_address) ? $billing_address : 'N/A' }}</div>
                </div>
                <div class="contact-address-card">
                    <div class="contact-address-title">Shipping Address</div>
                    <div class="contact-address-subtitle">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <span>{{ !empty($contact->mobile) ? $contact->mobile : 'N/A' }}</span>
                    </div>
                    <div class="contact-address-value">{{ !empty($shipping_address) ? $shipping_address : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-header-right">
        <div class="financial-summary-container">
            <div class="financial-summary-header">
                <i class="fas fa-chart-line" aria-hidden="true"></i>
                <span>Financial Summary</span>
            </div>

            <div class="financial-summary-items">
                <div class="financial-summary-item open-balance">
                    <span class="financial-item-icon orange"></span>
                    <div class="financial-item-content">
                        <div class="financial-item-label">Open Balance</div>
                        <div class="financial-item-value">
                            <span class="display_currency" data-balance-due="{{ $contact->balance }}" data-currency_symbol=true>
                                {{ $contact->balance }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="financial-summary-item overdue-balance">
                    <span class="financial-item-icon red"></span>
                    <div class="financial-item-content">
                        <div class="financial-item-label">Overdue Payment</div>
                        <div class="financial-item-value">
                            <span class="display_currency" data-balance-due="{{ $balance_due_header }}" data-currency_symbol=true>
                                {{ $balance_due_header }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if( $contact->type == 'supplier' || $contact->type == 'both')
                @if(($contact->total_purchase - $contact->purchase_paid) > 0)
                    <div style="margin-top: 12px;">
                        <a href="{{action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$contact->id])}}?type=purchase"
                           class="pay_purchase_due tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right">
                            <i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang("contact.pay_due_amount")
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@php
    $transaction_types = [];
    if (in_array($contact->type, ['both', 'supplier'])) {
        $transaction_types['purchase'] = __('lang_v1.purchase');
        $transaction_types['purchase_return'] = __('lang_v1.purchase_return');
    }

    if (in_array($contact->type, ['both', 'customer'])) {
        $transaction_types['sell'] = __('sale.sale');
        $transaction_types['sell_return'] = __('lang_v1.sell_return');
    }

    $transaction_types['opening_balance'] = __('lang_v1.opening_balance');
@endphp

<style>
    /* =============================================
       LEDGER TAB - AMAZON THEME REDESIGN
       ============================================= */
    
    /* Full Width Container */
    .ledger-container {
        width: 100%;
        padding: 0;
        margin: 0;
    }
    
    /* Controls Bar - Amazon Style */
    .ledger-controls-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 16px 20px;
        background: linear-gradient(180deg, #FFFFFF 0%, #FAFAFA 100%);
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    
    .ledger-controls-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .ledger-controls-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* Date Range - Separate Start/End Pickers */
    .ledger-date-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .ledger-date-section .date-label {
        font-size: 13px;
        font-weight: 600;
        color: #232F3E;
        white-space: nowrap;
    }
    
    .date-picker-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .date-picker-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .date-picker-item label {
        font-size: 12px;
        font-weight: 500;
        color: #565959;
        margin: 0;
        white-space: nowrap;
    }
    
    .date-picker-item input {
        width: 140px;
        height: 36px;
        padding: 6px 12px;
        border: 1px solid #D5D9D9;
        border-radius: 6px;
        font-size: 13px;
        color: #0F1111;
        background: #FFFFFF;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    
    .date-picker-item input:hover {
        border-color: #FF9900;
    }
    
    .date-picker-item input:focus {
        border-color: #FF9900;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15);
        outline: none;
    }
    
    .date-separator {
        color: #9CA3AF;
        font-weight: 500;
    }
    
    /* Hidden Format Input - Using Format 4 only */
    .ledger-format-hidden {
        display: none;
    }
    
    /* Action Buttons - Amazon Style */
    .ledger-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        padding: 0;
        border: 1px solid #D5D9D9;
        border-radius: 6px;
        background: linear-gradient(180deg, #FFFFFF 0%, #F7F8F8 100%);
        color: #0F1111;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
    }
    
    .ledger-action-btn:hover {
        background: linear-gradient(180deg, #F7FAFA 0%, #E3E6E6 100%);
        border-color: #BBBFBF;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }
    
    .ledger-action-btn:active {
        transform: translateY(0);
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .ledger-action-btn i {
        font-size: 15px;
    }
    
    /* Button Color Variants */
    .ledger-action-btn.pdf-btn {
        color: #B12704;
        border-color: #FECACA;
    }
    .ledger-action-btn.pdf-btn:hover {
        background: linear-gradient(180deg, #FEF2F2 0%, #FEE2E2 100%);
        border-color: #B12704;
    }
    
    .ledger-action-btn.email-btn {
        color: #007185;
        border-color: #BFDBFE;
    }
    .ledger-action-btn.email-btn:hover {
        background: linear-gradient(180deg, #EFF6FF 0%, #DBEAFE 100%);
        border-color: #007185;
    }
    
    .ledger-action-btn.cart-btn {
        color: #7C3AED;
        border-color: #DDD6FE;
    }
    .ledger-action-btn.cart-btn:hover {
        background: linear-gradient(180deg, #F5F3FF 0%, #EDE9FE 100%);
        border-color: #7C3AED;
    }
    
    .ledger-action-btn.price-btn {
        color: #067D62;
        border-color: #A7F3D0;
    }
    .ledger-action-btn.price-btn:hover {
        background: linear-gradient(180deg, #ECFDF5 0%, #D1FAE5 100%);
        border-color: #067D62;
    }
    
    .ledger-action-btn.edit-btn {
        color: #0891B2;
        border-color: #A5F3FC;
    }
    .ledger-action-btn.edit-btn:hover {
        background: linear-gradient(180deg, #ECFEFF 0%, #CFFAFE 100%);
        border-color: #0891B2;
    }
    
    .ledger-action-btn.merge-btn {
        color: #9333EA;
        border-color: #E9D5FF;
    }
    .ledger-action-btn.merge-btn:hover {
        background: linear-gradient(180deg, #FAF5FF 0%, #F3E8FF 100%);
        border-color: #9333EA;
    }
    
    /* Tooltip Styling */
    .ledger-action-btn[data-toggle="tooltip"] {
        position: relative;
    }
    
    /* Ledger Content Area */
    .ledger-content-area {
        width: 100%;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        overflow: hidden;
    }
    
    /* Ledger Table Full Width */
    #contact_ledger_div {
        width: 100%;
        padding: 0;
    }
    
    #contact_ledger_div .table-responsive {
        width: 100%;
        overflow-x: auto;
        margin: 0;
        padding: 0;
    }
    
    #contact_ledger_div table,
    #ledger_table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse;
    }
    
    #contact_ledger_div table thead,
    #ledger_table thead {
        background: linear-gradient(180deg, #232F3E 0%, #1A252F 100%) !important;
    }
    
    #contact_ledger_div table thead th,
    #ledger_table thead th {
        background: transparent !important;
        color: #FFFFFF !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.3px !important;
        padding: 14px 16px !important;
        border: none !important;
        white-space: nowrap;
    }
    
    #contact_ledger_div table tbody tr,
    #ledger_table tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.15s ease;
    }
    
    #contact_ledger_div table tbody tr:hover,
    #ledger_table tbody tr:hover {
        background: #FFF8E7 !important;
    }
    
    #contact_ledger_div table tbody td,
    #ledger_table tbody td {
        padding: 12px 16px !important;
        font-size: 13px !important;
        color: #0F1111 !important;
        border: none !important;
        vertical-align: middle;
    }
    
    /* Amount Columns Styling */
    #contact_ledger_div table tbody td:nth-child(n+4),
    #ledger_table tbody td:nth-child(n+4) {
        font-weight: 500;
        font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
    }
    
    /* Footer Row */
    #contact_ledger_div table tfoot tr,
    #ledger_table tfoot tr {
        background: linear-gradient(180deg, #F7F8F8 0%, #EAEDED 100%);
        font-weight: 700;
    }
    
    #contact_ledger_div table tfoot td,
    #ledger_table tfoot td {
        padding: 14px 16px !important;
        border-top: 2px solid #FF9900 !important;
    }
    
    /* Hidden Location Filter */
    .ledger-location-hidden {
        display: none;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        .ledger-controls-bar {
            flex-direction: column;
            align-items: stretch;
        }
        
        .ledger-controls-left,
        .ledger-controls-right {
            justify-content: center;
        }
        
        .date-picker-group {
            flex-direction: column;
            gap: 8px;
        }
        
        .date-separator {
            display: none;
        }
    }
    
    @media (max-width: 576px) {
        .ledger-date-section {
            flex-direction: column;
            align-items: stretch;
        }
        
        .date-picker-item {
            flex-direction: column;
            align-items: stretch;
        }
        
        .date-picker-item input {
            width: 100%;
        }
    }
</style>

<div class="ledger-container">
    {{-- Controls Bar --}}
    <div class="ledger-controls-bar">
        <div class="ledger-controls-left">
            {{-- Date Range Selection with Calendar Pickers --}}
            <div class="ledger-date-section">
                <span class="date-label"><i class="fas fa-calendar-alt" style="color: #FF9900; margin-right: 6px;"></i> Date Range:</span>
                <div class="date-picker-group">
                    <div class="date-picker-item">
                        <label for="ledger_start_date">From</label>
                        <input type="text" id="ledger_start_date" class="ledger-date-input" placeholder="Start Date" readonly>
                    </div>
                    <span class="date-separator">—</span>
                    <div class="date-picker-item">
                        <label for="ledger_end_date">To</label>
                        <input type="text" id="ledger_end_date" class="ledger-date-input" placeholder="End Date" readonly>
                    </div>
                </div>
            </div>
            
            {{-- Hidden Format - Always use Format 4 --}}
            <div class="ledger-format-hidden">
                <input type="hidden" name="ledger_format" value="format_4" id="ledger_format_value">
                {{-- Keep original input for compatibility --}}
                {!! Form::text('ledger_date_range', null, [
                    'id' => 'ledger_date_range',
                    'class' => 'form-control',
                    'style' => 'display: none;',
                ]) !!}
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div class="ledger-controls-right">
            {{-- Download PDF --}}
            <button type="button" class="ledger-action-btn pdf-btn" id="print_ledger_pdf"
                data-href="{{ action([\App\Http\Controllers\ContactController::class, 'getLedger']) }}?contact_id={{ $contact->id }}&action=pdf"
                data-toggle="tooltip" data-placement="top" title="Download PDF">
                <i class="fas fa-file-pdf"></i>
            </button>

            {{-- Send Email --}}
            <button type="button" class="ledger-action-btn email-btn" id="send_ledger"
                data-toggle="tooltip" data-placement="top" title="Email Ledger">
                <i class="fas fa-paper-plane"></i>
            </button>

            {{-- Customer Cart --}}
            @can('ecom_controller')
                @if (in_array($contact->type, ['both', 'customer']))
                    <button type="button" class="ledger-action-btn cart-btn" id="customer_cart"
                        data-toggle="tooltip" data-placement="top" title="View Cart">
                        <i class="fas fa-shopping-cart"></i>
                    </button>

                    <button type="button" class="ledger-action-btn price-btn" id="customer_prices"
                        data-toggle="tooltip" data-placement="top" title="Special Prices">
                        <i class="fas fa-tags"></i>
                    </button>
                @endif
            @endcan

            {{-- Edit Customer --}}
            @can('customer.update')
                <button type="button" class="ledger-action-btn edit-btn" id="customer_edit"
                    data-toggle="tooltip" data-placement="top" title="Edit Customer">
                    <i class="fas fa-user-edit"></i>
                </button>
            @endcan

            {{-- Merge Account --}}
            @php
                $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) || auth()->user()->can('admin');
            @endphp
            @if ($is_admin && in_array($contact->type, ['customer', 'both']))
                <button type="button" class="ledger-action-btn merge-btn" id="merge_customer_account"
                    data-toggle="tooltip" data-placement="top" title="Merge Account"
                    data-contact-id="{{ $contact->id }}">
                    <i class="fas fa-compress-arrows-alt"></i>
                </button>
            @endif
        </div>
    </div>
    
    {{-- Hidden Location Filter --}}
    <div class="ledger-location-hidden">
        {!! Form::label('ledger_location', __('purchase.business_location') . ':') !!}
        {!! Form::select('ledger_location', $business_locations, null, [
            'class' => 'form-control select2',
            'id' => 'ledger_location',
        ]) !!}
    </div>
    
    {{-- Ledger Content Area --}}
    <div class="ledger-content-area">
        <div id="contact_ledger_div"></div>
    </div>
</div>

{{-- JavaScript for Date Pickers --}}
<script>
$(document).ready(function() {
    // Initialize date pickers
    var startDate = moment().subtract(29, 'days');
    var endDate = moment();
    
    // Initialize Start Date Picker
    $('#ledger_start_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        startDate: startDate,
        locale: {
            format: moment_date_format
        }
    });
    
    // Initialize End Date Picker
    $('#ledger_end_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        startDate: endDate,
        locale: {
            format: moment_date_format
        }
    });
    
    // Update hidden date range field when dates change and trigger ledger load
    function updateDateRange() {
        var start = $('#ledger_start_date').val();
        var end = $('#ledger_end_date').val();
        $('#ledger_date_range').val(start + ' ~ ' + end).trigger('change');
        
        // Trigger ledger load if get_contact_ledger function exists
        if (typeof get_contact_ledger === 'function') {
            get_contact_ledger();
        }
    }
    
    $('#ledger_start_date, #ledger_end_date').on('apply.daterangepicker', function() {
        updateDateRange();
    });
    
    // Set initial values
    $('#ledger_start_date').val(startDate.format(moment_date_format));
    $('#ledger_end_date').val(endDate.format(moment_date_format));
    
    // Override format selection - always use format_4
    $('input[name="ledger_format"]').val('format_4');
    
    // Trigger initial load after a short delay to ensure everything is initialized
    setTimeout(function() {
        if (typeof get_contact_ledger === 'function') {
            get_contact_ledger();
        } else {
            updateDateRange();
        }
    }, 500);
});
</script>

{{-- Merge Customer Account Modal --}}
@if ($is_admin && in_array($contact->type, ['customer', 'both']))
<div class="modal fade" id="merge_customer_modal" tabindex="-1" role="dialog" aria-labelledby="mergeCustomerModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.15);">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="mergeCustomerModalLabel"><i class="fas fa-compress-arrows-alt" style="color: #FF9900;"></i> Merge Customer Account</h4>
            </div>
            <div class="modal-body">
                <div class="alert" style="background: #FFF8E7; border: 1px solid #FFE4B5; color: #92400E; border-radius: 8px;">
                    <strong><i class="fas fa-exclamation-triangle"></i> Warning:</strong> This action will merge all data from the source account into the selected target account. 
                    The source account will be frozen after the merge. This action cannot be undone.
                </div>
                <div class="form-group">
                    <label style="font-weight: 600; color: #232F3E;">Source Account:</label>
                    <input type="text" class="form-control" value="{{ $contact->name }} ({{ $contact->contact_id }})" readonly style="background: #F7F8F8;">
                    <input type="hidden" id="source_contact_id" value="{{ $contact->id }}">
                </div>
                <div class="form-group">
                    <label for="target_contact_id" style="font-weight: 600; color: #232F3E;">Select Target Account: <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="target_contact_id" style="width: 100%;">
                        <option value="">-- Select Customer Account --</option>
                    </select>
                    <small class="help-block" style="color: #6B7280;">All data from the source account will be migrated to this account.</small>
                </div>
                <div id="merge_preview" style="display: none; margin-top: 15px;">
                    <h5 style="font-weight: 600; color: #232F3E;">Data to be migrated:</h5>
                    <ul id="migration_summary" class="list-unstyled"></ul>
                </div>
            </div>
            <div class="modal-footer" style="background: #37475A; color: #ffffff; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                <button type="button" class="btn" data-dismiss="modal" style="background: linear-gradient(180deg, #FFFFFF 0%, #F7F8F8 100%); border: 1px solid #D5D9D9; color: #0F1111;">Cancel</button>
                <button type="button" class="btn" id="confirm_merge_btn" disabled style="background: linear-gradient(180deg, #FF9900 0%, #E47911 100%); border: 1px solid #C7511F; color: #0F1111; font-weight: 600;">
                    <i class="fa fa-check"></i> Confirm Merge
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Merge Confirmation Modal --}}
<div class="modal fade" id="merge_confirmation_modal" tabindex="-1" role="dialog" aria-labelledby="mergeConfirmationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #37475A; color: #ffffff; border-bottom: 1px solid rgba(255, 255, 255, 0.15);">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="mergeConfirmationModalLabel"><i class="fas fa-exclamation-circle"></i> Confirm Merge Action</h4>
            </div>
            <div class="modal-body">
                <div class="alert" style="background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; border-radius: 8px;">
                    <strong><i class="fas fa-exclamation-triangle"></i> Warning:</strong> This action will merge all data from the source account into the target account and freeze the source account. This action cannot be undone!
                </div>
                <div class="form-group">
                    <label for="merge_confirmation_input" style="font-weight: 600; color: #232F3E;">Type "Merge" to confirm: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="merge_confirmation_input" placeholder="Type 'Merge' here" autocomplete="off" style="border: 1px solid #D5D9D9;">
                    <small class="help-block text-danger" id="merge_confirmation_error" style="display: none;">You must type "Merge" exactly to confirm.</small>
                </div>
            </div>
            <div class="modal-footer" style="background: #37475A; color: #ffffff; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                <button type="button" class="btn" data-dismiss="modal" style="background: linear-gradient(180deg, #FFFFFF 0%, #F7F8F8 100%); border: 1px solid #D5D9D9; color: #0F1111;">Cancel</button>
                <button type="button" class="btn" id="final_confirm_merge_btn" style="background: linear-gradient(180deg, #B91C1C 0%, #991B1B 100%); border: 1px solid #7F1D1D; color: #FFFFFF; font-weight: 600;">
                    <i class="fa fa-check"></i> Confirm Merge
                </button>
            </div>
        </div>
    </div>
</div>
@endif

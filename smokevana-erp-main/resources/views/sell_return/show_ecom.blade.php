<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header tw-justify-between">
            <h4 class="modal-title" id="modalTitle">
                <i class="fa fa-undo"></i> Ecom Return Verification
                <span class="badge badge-light ml-2">#{{ $return->invoice_no }}</span>
            </h4>
            <p>@lang('messages.date'): {{ @format_date($return->transaction_date) }}</p>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -50px">
                @if ($return->status === 'pending' || $return->status === 'in_transit')
                <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white" id="verify-return-btn">
                    <i class="fa fa-check-circle"></i> Save
                </button>
                @endif
                @if ($return->status === 'varified')
                <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white" id="create-sell-return-btn">
                    <i class="fa fa-check-circle"></i> Create Sell Return
                </button>
                @endif
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    @lang('messages.close')
                </button>
            </div>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-2">
                    <b>Return Invoice:</b> {{ $return->invoice_no }}<br>
                    <b>Return Date:</b> {{ @format_date($return->transaction_date) }}<br>
                    <b>Status:</b> {{ $return->status }}<br>
                    <b>Payment Status:</b> {{ $return->payment_status }}<br>
                    @if (!empty($return->additional_notes))
                        <b>Notes:</b> {{ $return->additional_notes }}<br>
                    @endif
                    @php
                        $return_images = [];
                        if (!empty($return->document)) {
                            $decoded = json_decode($return->document, true);
                            if (is_array($decoded)) {
                                $return_images = $decoded;
                            } elseif (is_string($return->document) && strpos($return->document, 'return_images/') !== false) {
                                $return_images = [$return->document];
                            }
                        }
                    @endphp
                    @if (!empty($return_images))
                        <b>Return Images:</b> {{ count($return_images) }} image(s)<br>
                    @endif
                </div>

                <div class="col-sm-2">
                    @php
                        $is_b2c_location = !empty($return->location) && $return->location->is_b2c == 1;
                    @endphp
                    <b>Customer:</b> {{ $return->contact->name }}<br>
                    @if (!$is_b2c_location)
                        @if (!empty($return->contact->contact_id))
                            <b>Account No:</b> {{ $return->contact->contact_id }}<br>
                        @endif
                        @if (!empty($return->contact->supplier_business_name))
                            <b>Business Name:</b> {{ $return->contact->supplier_business_name }}<br>
                        @endif
                        @if ($return->contact->mobile)
                            <b>Mobile:</b> {{ $return->contact->mobile }}<br>
                        @endif
                        @if ($return->contact->email)
                            <b>Email:</b> {{ $return->contact->email }}<br>
                        @endif
                    @endif
                </div>

                <div class="col-sm-2">
                    <b>Business Location:</b> {{ $return->location->name }}<br>
                    @if (!empty($return->location->address))
                        <b>Address:</b> {{ $return->location->address }}<br>
                    @endif
                    @if (!empty($return->location->city))
                        <b>City:</b> {{ $return->location->city }}<br>
                    @endif
                    @if (!empty($return->location->state))
                        <b>State:</b> {{ $return->location->state }}<br>
                    @endif
                </div>

                <div class="col-sm-2">
                    <b>Total Before Tax:</b> {{ number_format($return->total_before_tax, 2) }}<br>
                    <b>Tax Amount:</b> {{ number_format($return->tax_amount, 2) }}<br>
                    <b>Discount:</b> {{ number_format($return->discount_amount, 2) }}<br>
                    <b>Final Total:</b> {{ number_format($return->final_total, 2) }}<br>
                </div>

                <div class="col-sm-2">
                    @if ($return->return_parent)
                        <b>Original Invoice:</b> {{ $return->return_parent->invoice_no }}<br>
                        <b>Original Date:</b> {{ @format_date($return->return_parent->transaction_date) }}<br>
                    @endif
                    <b>Return Type:</b> Ecom Return<br>
                    <b>Items Count:</b> {{ count($return->return_lines_ecom) }}<br>
                </div>
            </div>

            @php
                $return_images = [];
                if (!empty($return->document)) {
                    $decoded = json_decode($return->document, true);
                    if (is_array($decoded)) {
                        $return_images = $decoded;
                    } elseif (is_string($return->document) && strpos($return->document, 'return_images/') !== false) {
                        $return_images = [$return->document];
                    }
                }
            @endphp
            
            @if (!empty($return_images))
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-sm-12">
                    <h4>Return Images ({{ count($return_images) }}):</h4>
                    <div class="tw-flex tw-flex-wrap tw-gap-4">
                        @foreach($return_images as $image)
                            <div class="tw-relative" style="max-width: 200px;">
                                <a href="{{ asset('uploads/' . $image) }}" target="_blank" class="tw-block">
                                    <img src="{{ asset('uploads/' . $image) }}" 
                                         alt="Return Image" 
                                         class="tw-rounded tw-shadow-md tw-cursor-pointer hover:tw-opacity-80"
                                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-sm-12 col-xs-12 tw-flex tw-justify-between">
                    <h4>Return Items:</h4>
                    <div class="tw-relative tw-inline-block tw-mb-1 tw-gap-5">
                        <button id="edit-return-notes" class="btn-modal-cl" style="width: 150px;">
                            📝 Return Notes
                        </button>
                        <button id="edit-return-reason" class="btn-modal-cl" style="width: 150px;">
                            ❓ Return Reason
                        </button>
                    </div>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive" style="max-height: 50vh; min-height: 50vh; overflow-y: auto;">
                        <table class="table table-striped" style="background: rgb(239 254 238); margin-bottom: 0;" id='sale_return_ecom_table'>
                            <thead style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Sale Qty</th>
                                    <th style="width: 150px" class="tw-text-center">Quantity</th>
                                    <th>Sale Price</th>
                                    <th style="width: 190px">Return Price</th>
                                    <th class="tw-text-center" style="width: 190px">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($return->return_lines_ecom as $index => $return_line)
                                    <tr data-item-id="{{ $return_line->id }}" style="background-color:#fce6e1;">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="tw-flex tw-items-center">
                                                @if($return_line->product->image)
                                                    <img src="{{ $return_line->product->image_url }}"
                                                        alt="{{ $return_line->product->name }}" class="img-thumbnail tw-mr-2"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary text-white rounded tw-flex tw-items-center tw-justify-center tw-mr-2"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="fa fa-image"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong
                                                        class="text-muted">{{ $return_line->product->sku ?? '' }}</strong>
                                                    <span>{{ $return_line->product->name }}</span>
                                                    @if($return_line->variations)
                                                        <br><small
                                                            class="text-muted">{{ $return_line->variations->name ?? '' }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $return_line->parent_sell_line->quantity }}</span>
                                        </td>
                                        <td class="tw-text-center">
                                            <input type="number" class="form-control return-quantity-input" value="{{ $return_line->quantity }}" max="{{ $return_line->parent_sell_line->quantity }}" min="1" data-original-quantity="{{ $return_line->parent_sell_line->quantity }}">
                                            <div class="error-message-quantity text-danger" style="font-size: 12px; margin-top: 5px;"></div>
                                        </td>
                                        <td>
                                            <span class="text-muted">$ {{ number_format($return_line->parent_sell_line->unit_price_inc_tax, 2) }}</span>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="number" class="form-control return-price-input"
                                                    value="{{ $return_line->return_price }}" step="0.01" min="0"
                                                    data-original-price="{{ $return_line->parent_sell_line->unit_price_inc_tax }}"
                                                    data-item-id="{{ $return_line->id }}" max="{{ $return_line->parent_sell_line->unit_price_inc_tax }}">
                                            </div>
                                            <div class="error-message-price text-danger" style="font-size: 12px; margin-top: 5px;"></div>
                                        </td>
                                        <td class="tw-text-center">
                                            <span class="item-total" data-item-id="{{ $return_line->id }}" data-item-total="{{ $return_line->return_price * $return_line->quantity }}">
                                                ${{ number_format($return_line->return_price * $return_line->quantity, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fa fa-inbox fa-3x mb-3"></i>
                                            <p>No return items found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="table-responsive hidden" style="max-height: 120px; overflow-y: auto;">
                        <table class="table table-striped" style="background: rgb(239 254 238); margin-bottom: 0;">
                            <thead style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th style="background: inherit;">Return Summary</th>
                                    <th style="background: inherit;">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Total Items</strong></td>
                                    <td>{{ count($return->return_lines_ecom) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td><span
                                            id="summary-total-amount">${{ number_format($return->final_total, 2) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 tw-mt-1">
                    <div class="table-responsive"
                        style="border: 1px solid rgb(228, 226, 226); background: #fce6e1; border-radius: 5px;">
                        <table>
                            <tbody>
                                <tr>
                                    <th class="tw-px-3 tw-py-0" style="background: #fce6e1;">
                                        Subtotal</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true" id="subtotal-amount">
                                            {{ number_format($return->total_before_tax, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="tw-px-3 tw-py-0" style="background: #fce6e1;">
                                        Tax</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true" id="tax-amount">
                                            {{ number_format($return->tax_amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="tw-px-3 tw-py-0" style="background: #fce6e1;">
                                        Discount</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true" id="discount-amount">
                                            {{ number_format($return->discount_amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="tw-px-3 tw-py-0" style="background: #fce6e1;">
                                        Final Total</th>
                                    <td class="tw-px-3 tw-py-0">
                                        <span class="display_currency" data-currency_symbol="true"
                                            id="final-total-amount">
                                            {{ number_format($return->final_total, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Notes Modal -->
<div class="modal fade" id="return-notes-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Notes</h5>
                <div class="tw-flex tw-justify-end tw-gap-5" style="margin-top: -25px">
                    {{-- <button type="button" class="btn btn-primary" id="save-return-notes-btn">Save Notes</button> --}}
                    <button type="button" class="btn btn-danger" id="cancel-return-notes-btn">Cancel</button>
                </div>
            </div>
            <div class="modal-body">
                <form id="return-notes-form">
                    <div class="form-group">
                        <label>Return Notes</label>
                        <textarea class="form-control" id="return-notes-text" rows="5"
                            placeholder="Enter return notes here...">{{ $return->additional_notes ?? '' }}</textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Return Reason Modal -->
<div class="modal fade" id="return-reason-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Reason</h5>
                <div class="tw-flex tw-justify-end tw-gap-5" style="margin-top: -25px">
                    {{-- <button type="button" class="btn btn-primary" id="save-return-reason-btn">Save Reason</button> --}}
                    <button type="button" class="btn btn-danger" id="cancel-return-reason-btn">Cancel</button>
                </div>
            </div>
            <div class="modal-body">
                <form id="return-reason-form">
                    {{-- <div class="form-group">
                        <label>Return Reason</label>
                        <select class="form-control" id="return-reason-select">
                            <option value="">Select a reason</option>
                            <option value="defective">Defective Product</option>
                            <option value="wrong_size">Wrong Size</option>
                            <option value="not_as_described">Not as Described</option>
                            <option value="damaged">Damaged in Transit</option>
                            <option value="customer_change_mind">Customer Changed Mind</option>
                            <option value="duplicate_order">Duplicate Order</option>
                            <option value="other">Other</option>
                        </select>
                    </div> --}}
                    <div class="form-group">
                        <label>Additional Details</label>
                        <textarea class="form-control" id="return-reason-details" rows="3"
                            placeholder="Provide additional details about the return reason..."></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verification-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($return->status === 'pending')
                    Approve Return
                    @else
                    Verify Return
                    @endif
                </h5>
                <div class="tw-flex tw-justify-end tw-gap-5" style="margin-top: -25px">
                    @if ($return->status === 'pending' || $return->status === 'in_transit')
                    <button type="button" class="btn btn-success" id="confirm-verification-btn">
                        @if ($return->status === 'pending')
                        Approve Return
                        @else
                        Verify Return
                        @endif
                    </button>
                    @endif
                    <button type="button" class="btn btn-danger" id="cancel-verification-btn">Cancel</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                    <h5>Are you sure you want to verify this return?</h5>
                    <p class="text-muted">This action will mark the return as verified and cannot be undone.</p>
                </div>
                <div class="alert alert-info">
                    <strong>Return Summary:</strong><br>
                    <span id="verification-summary"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .modal-xl {
        max-width: 95%;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .table th {
        border-top: none;
        font-weight: 600;
    }

    .btn-group .btn {
        border-radius: 0.25rem !important;
    }

    .btn-group .btn:not(:last-child) {
        margin-right: 0.25rem;
    }

    .img-thumbnail {
        border-radius: 0.375rem;
    }



    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modal-xl {
            max-width: 100%;
            margin: 0;
        }

        .card-body {
            padding: 0.75rem;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
<script>
    $(document).ready(function () {
        // Global error handling function
        function showError(message, type = 'error') {
            if (typeof toastr !== 'undefined') {
                if (type === 'error') {
                    toastr.error(message);
                } else if (type === 'warning') {
                    toastr.warning(message);
                } else {
                    toastr.info(message);
                }
            } else {
                alert(message);
            }
        }

        // Global success function
        function showSuccess(message) {
            if (typeof toastr !== 'undefined') {
                toastr.success(message);
            } else {
                alert(message);
            }
        }

        // Clear all errors function
        function clearAllErrors() {
            $('.error-message-quantity').text('');
            $('.error-message-price').text('');
        }

        // Validate input value function
        function validateInput(value, min, max, fieldName) {
            if (value === '' || value === null || value === undefined) {
                return `${fieldName} cannot be empty`;
            }
            
            const numValue = parseFloat(value);
            if (isNaN(numValue)) {
                return `${fieldName} must be a valid number`;
            }
            
            if (min !== null && numValue < min) {
                return `${fieldName} cannot be less than ${min}`;
            }
            
            if (max !== null && numValue > max) {
                return `${fieldName} cannot be greater than ${max}`;
            }
            
            return null;
        }

        // Safe calculation function
        function safeCalculate(price, quantity) {
            try {
                const numPrice = parseFloat(price) || 0;
                const numQuantity = parseInt(quantity) || 0;
                return numPrice * numQuantity;
            } catch (error) {
                console.error('Calculation error:', error);
                return 0;
            }
        }

        // Handle price input changes
        $('.return-price-input').on('input', function () {
            try {
                const $input = $(this);
                const itemId = $input.data('item-id');
                const newPrice = $input.val();
                const originalPrice = $input.data('original-price');
                
                // Clear previous error
                $input.closest('td').find('.error-message-price').text('');
                
                // Validate input
                const priceError = validateInput(newPrice, 0, originalPrice, 'Price');
                if (priceError) {
                    $input.closest('td').find('.error-message-price').text(priceError);
                    showError(priceError, 'warning');
                    return;
                }
                
                const numPrice = parseFloat(newPrice);
                const quantity = parseInt($input.closest('tr').find('.return-quantity-input').val()) || 0;
                
                // Calculate total safely
                const total = safeCalculate(numPrice, quantity);
                
                // Update total display
                const $totalElement = $(`.item-total[data-item-id="${itemId}"]`);
                if ($totalElement.length) {
                    $totalElement.text('$' + total.toFixed(2));
                    $totalElement.data('item-total', total);
                }
                
                // Update summary
                updateSummary();
                
            } catch (error) {
                console.error('Error in price input handler:', error);
                showError('An error occurred while processing the price input');
            }
        });

        // Handle quantity input changes
        $('.return-quantity-input').on('input', function () {
            try {
                const $input = $(this);
                const row = $input.closest('tr');
                const itemId = row.data('item-id');
                const newQuantity = $input.val();
                const originalQuantity = $input.data('original-quantity');
                
                // Clear previous error
                $input.closest('td').find('.error-message-quantity').text('');
                
                // Validate input
                const quantityError = validateInput(newQuantity, 1, originalQuantity, 'Quantity');
                if (quantityError) {
                    $input.closest('td').find('.error-message-quantity').text(quantityError);
                    showError(quantityError, 'warning');
                    return;
                }
                
                const numQuantity = parseInt(newQuantity);
                const price = parseFloat(row.find('.return-price-input').val()) || 0;
                
                // Calculate total safely
                const total = safeCalculate(price, numQuantity);
                
                // Update total display
                const $totalElement = $(`.item-total[data-item-id="${itemId}"]`);
                if ($totalElement.length) {
                    $totalElement.text('$' + total.toFixed(2));
                    $totalElement.data('item-total', total);
                }
                
                // Update summary
                updateSummary();
                
            } catch (error) {
                console.error('Error in quantity input handler:', error);
                showError('An error occurred while processing the quantity input');
            }
        });

        // Return Notes Modal
        $('#edit-return-notes').on('click', function () {
            try {
                $('#return-notes-modal').modal('show');
            } catch (error) {
                console.error('Error opening return notes modal:', error);
                showError('Failed to open return notes modal');
            }
        });

        // Save return notes
        $('#save-return-notes-btn').on('click', function () {
            try {
                const notes = $('#return-notes-text').val();
                
                if (!notes || notes.trim() === '') {
                    showError('Please enter return notes before saving', 'warning');
                    return;
                }
                
                // Here you would typically send the notes to the server
                console.log('Saving return notes:', notes);
                
                // Simulate API call
                setTimeout(() => {
                    showSuccess('Return notes saved successfully!');
                    $('#return-notes-modal').modal('hide');
                }, 500);
                
            } catch (error) {
                console.error('Error saving return notes:', error);
                showError('Failed to save return notes');
            }
        });

        // Return Reason Modal
        $('#edit-return-reason').on('click', function () {
            try {
                $('#return-reason-modal').modal('show');
            } catch (error) {
                console.error('Error opening return reason modal:', error);
                showError('Failed to open return reason modal');
            }
        });

        // Cancel return notes
        $('#cancel-return-notes-btn').on('click', function () {
            try {
                $('#return-notes-modal').modal('hide');
            } catch (error) {
                console.error('Error closing return notes modal:', error);
            }
        });

        // Cancel return reason
        $('#cancel-return-reason-btn').on('click', function () {
            try {
                $('#return-reason-modal').modal('hide');
            } catch (error) {
                console.error('Error closing return reason modal:', error);
            }
        });

        // Cancel verification
        $('#cancel-verification-btn').on('click', function () {
            try {
                $('#verification-modal').modal('hide');
            } catch (error) {
                console.error('Error closing verification modal:', error);
            }
        });

        // Save return reason
        $('#save-return-reason-btn').on('click', function () {
            try {
                const reason = $('#return-reason-select').val();
                const details = $('#return-reason-details').val();

                if (!reason) {
                    showError('Please select a return reason before saving', 'warning');
                    return;
                }

                // Here you would typically send the reason to the server
                console.log('Saving return reason:', { reason, details });

                // Simulate API call
                setTimeout(() => {
                    showSuccess('Return reason saved successfully!');
                    $('#return-reason-modal').modal('hide');
                }, 500);
                
            } catch (error) {
                console.error('Error saving return reason:', error);
                showError('Failed to save return reason');
            }
        });

        // Verify return button
        $('#verify-return-btn').on('click', function () {
            try {
                clearAllErrors();
                
                let hasErrors = false;
                let errorCount = 0;
                
                // Validate all rows
                $('#sale_return_ecom_table tbody tr').each(function () {
                    const $row = $(this);
                    const $priceInput = $row.find('.return-price-input');
                    const $quantityInput = $row.find('.return-quantity-input');
                    
                    if ($priceInput.length && $quantityInput.length) {
                        const price = parseFloat($priceInput.val()) || 0;
                        const quantity = parseInt($quantityInput.val()) || 0;
                        const originalPrice = $priceInput.data('original-price');
                        const originalQuantity = $quantityInput.data('original-quantity');
                        
                        // Validate price
                        if (price > originalPrice) {
                            $row.find('.error-message-price').text('Price cannot be greater than the original price');
                            hasErrors = true;
                            errorCount++;
                        }
                        
                        // Validate quantity
                        if (quantity > originalQuantity) {
                            $row.find('.error-message-quantity').text('Quantity cannot be greater than the original quantity');
                            hasErrors = true;
                            errorCount++;
                        }
                        
                        // Validate minimum values
                        if (price < 0) {
                            $row.find('.error-message-price').text('Price cannot be negative');
                            hasErrors = true;
                            errorCount++;
                        }
                        
                        if (quantity < 1) {
                            $row.find('.error-message-quantity').text('Quantity must be at least 1');
                            hasErrors = true;
                            errorCount++;
                        }
                    }
                });
                
                if (hasErrors) {
                    showError(`Please fix ${errorCount} validation error(s) before verifying the return`, 'warning');
                    return;
                }
                
                // Check if there are any items
                const totalItems = $('#sale_return_ecom_table tbody tr').length;
                if (totalItems === 0) {
                    showError('No return items found to verify', 'warning');
                    return;
                }
                
                // Show verification modal
                const totalAmount = $('#final-total-amount').text();
                $('#verification-summary').html(`
                    <strong>Total Items:</strong> ${totalItems}<br>
                    <strong>Total Amount:</strong> ${totalAmount}
                `);
                
                $('#verification-modal').modal('show');
                
            } catch (error) {
                console.error('Error in verify return handler:', error);
                showError('An error occurred while verifying the return');
            }
        });

        // Confirm verification
        $('#confirm-verification-btn').on('click', function () {
            try {
                $('#verification-modal').modal('hide');
                
                // Collect products data
                const products = [];
                let hasInvalidData = false;
                
                $('#sale_return_ecom_table tbody tr').each(function () {
                    const $row = $(this);
                    const itemId = $row.data('item-id');
                    const quantity = $row.find('.return-quantity-input').val();
                    const price = $row.find('.return-price-input').val();
                    
                    // Validate data before adding
                    if (!itemId || !quantity || !price) {
                        hasInvalidData = true;
                        return false; // Break the loop
                    }
                    
                    products.push({
                        "return_line_id": itemId,
                        "quantity": quantity,
                        "unit_price": price,
                        "unit_price_inc_tax": price,
                    });
                });
                
                if (hasInvalidData || products.length === 0) {
                    showError('Invalid data found. Please refresh the page and try again.', 'error');
                    return;
                }
                
                // Show loading state
                const $btn = $('#confirm-verification-btn');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                
                // Make AJAX call
                const url = `/sell-return-ecom/{{ $return->id }}`;
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { products: products },
                    timeout: 30000, // 30 second timeout
                    success: function (result) {
                        try {
                            if (result && result.status) {
                                showSuccess(result.message || 'Return verified successfully!');
                                
                                // Update UI to show verified status
                                $('#verify-return-btn')
                                    .removeClass('tw-dw-btn-success')
                                    .addClass('tw-dw-btn-secondary')
                                    .prop('disabled', true)
                                    .html('<i class="fa fa-check-circle"></i> Verified');
                                
                                // Reload page after delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                showError(result.message || 'Failed to verify return');
                            }
                        } catch (error) {
                            console.error('Error processing success response:', error);
                            showError('An error occurred while processing the response');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', { xhr, status, error });
                        
                        let errorMessage = 'Failed to verify return';
                        
                        if (status === 'timeout') {
                            errorMessage = 'Request timed out. Please try again.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Validation error. Please check your input.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please try again later.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Return not found. Please refresh the page.';
                        }
                        
                        showError(errorMessage);
                    },
                    complete: function () {
                        // Restore button state
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
                
            } catch (error) {
                console.error('Error in confirm verification handler:', error);
                showError('An error occurred while confirming verification');
                
                // Restore button state
                $('#confirm-verification-btn').prop('disabled', false).html('Verify Return and Create Shipment');
            }
        });

        // Update summary calculations
        function updateSummary() {
            try {
                let subtotal = 0;
                let itemCount = 0;

                $('.item-total').each(function () {
                    const total = parseFloat($(this).data('item-total')) || 0;
                    if (!isNaN(total)) {
                        subtotal += total;
                        itemCount++;
                    }
                });

                // Validate calculation result
                if (isNaN(subtotal) || subtotal < 0) {
                    console.error('Invalid subtotal calculation:', subtotal);
                    subtotal = 0;
                }

                const formattedSubtotal = '$' + subtotal.toFixed(2);
                
                // Update all summary elements
                $('#subtotal-amount').text(formattedSubtotal);
                $('#final-total-amount').text(formattedSubtotal);
                $('#summary-total-amount').text(formattedSubtotal);
                
            } catch (error) {
                console.error('Error updating summary:', error);
                showError('Failed to update summary calculations');
            }
        }

        // Initialize the page
        try {
            // Initialize summary
            updateSummary();
            
            // Add error handling for missing elements
            if ($('#sale_return_ecom_table tbody tr').length === 0) {
                console.warn('No return items found in table');
            }
            
            // Validate required data attributes
            $('.return-price-input').each(function() {
                const $input = $(this);
                if (!$input.data('original-price') || !$input.data('item-id')) {
                    console.error('Missing required data attributes for price input:', $input);
                }
            });
            
            $('.return-quantity-input').each(function() {
                const $input = $(this);
                if (!$input.data('original-quantity')) {
                    console.error('Missing required data attributes for quantity input:', $input);
                }
            });
            
        } catch (error) {
            console.error('Error during page initialization:', error);
            showError('Failed to initialize the page properly');
        }

        // Global error handler for unhandled errors
        window.addEventListener('error', function(e) {
            console.error('Unhandled error:', e.error);
            showError('An unexpected error occurred. Please refresh the page.');
        });

        // Handle modal errors
        $(document).on('hidden.bs.modal', function (e) {
            try {
                // Clear any error messages when modals are closed
                clearAllErrors();
            } catch (error) {
                console.error('Error handling modal close:', error);
            }
        });
        $('#verify-return-btn').addClass('hide');
        $('.return-quantity-input').prop('disabled', true);
        $('.return-price-input').prop('disabled', true);

        if ('{{ $return->status }}' === 'pending') {
            $('#verify-return-btn').removeClass('hide');
            $('.return-quantity-input').prop('disabled', false);
            $('.return-price-input').prop('disabled', false);
        }
        if ('{{ $return->status }}' === 'in_transit') {
            $('#verify-return-btn').removeClass('hide');
            $('.return-quantity-input').prop('disabled', false);
        }
        $('#create-sell-return-btn').on('click', function () {
            $('#create-sell-return-btn').prop('disabled', true);
            
            // Show loading state
            $('#create-sell-return-btn').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url: '/sell-return-ecom-create-sell-return/{{ $return->id }}',
                method: 'POST',
                timeout: 30000, // 30 second timeout
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (result) {
                    if (result && result.status) {
                        showSuccess(result.message || 'Return verified successfully!');
                        $('#verify-return-btn')
                            .removeClass('tw-dw-btn-success')
                            .addClass('tw-dw-btn-secondary')
                            .prop('disabled', true)
                            .html('<i class="fa fa-check-circle"></i> Verified');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showError(result.message || 'Failed to verify return');
                    }
                },
                error: function (xhr, status, error) {
                    showError(error);
                    console.error('AJAX Error:', {status, error, xhr});
                },
                complete: function () {
                    // Reset button state
                    $('#create-sell-return-btn').prop('disabled', false);
                    $('#create-sell-return-btn').html('Create Sell Return');
                }
            });
        });
    });
</script>
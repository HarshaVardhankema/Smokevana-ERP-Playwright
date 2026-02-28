<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-purple" style="background: #37475A; color: #ffffff;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                <i class="fa fa-forward"></i> Bypass Order - Partial Fulfillment
            </h4>
        </div>
        <div class="modal-body">
            <input type="hidden" id="bypass_order_id" value="{{ $order->id }}">
            
            {{-- Order Info --}}
            <div class="row mb-3" style="margin-bottom: 15px;">
                <div class="col-md-6">
                    <p><strong>Order #:</strong> {{ $order->invoice_no }}</p>
                    <p><strong>Customer:</strong> {{ $order->contact->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->transaction_date)->format('M d, Y h:i A') }}</p>
                    <p><strong>Status:</strong> <span class="label bg-info">{{ ucfirst($order->status) }}</span></p>
                </div>
            </div>
            
            <div class="alert alert-info" style="background: #e3f2fd; border-color: #90caf9; color: #1565c0;">
                <i class="fa fa-info-circle"></i>
                <strong>Partial Fulfillment:</strong> Enter the quantity you can fulfill for each product. 
                Items with fulfillable quantity less than ordered will be partially fulfilled.
            </div>
            
            {{-- Products Table --}}
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="bypass_products_table">
                    <thead style="position: sticky; top: 0; background: #f5f5f5; z-index: 10;">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 10%;">Image</th>
                            <th style="width: 35%;">Product</th>
                            <th style="width: 12%;" class="text-center">Available Stock</th>
                            <th style="width: 12%;" class="text-center">Ordered Qty</th>
                            <th style="width: 16%;" class="text-center">Fulfillable Qty</th>
                            <th style="width: 10%;" class="text-center">Short</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = 1; @endphp
                        @foreach($order->sell_lines as $line)
                            @php
                                $variation = $line->variations;
                                $product = $line->product;
                                $availableStock = $variation->variation_location_details[0]->qty_available ?? 0;
                                $orderedQty = $line->quantity;
                                $maxFulfillable = min($orderedQty, $availableStock);
                                $isOutOfStock = $availableStock <= 0;
                            @endphp
                            <tr data-line-id="{{ $line->id }}" class="bypass-line-row {{ $isOutOfStock ? 'out-of-stock-row' : '' }}" style="{{ $isOutOfStock ? 'background-color: #ffebee;' : '' }}">
                                <td>{{ $counter++ }}</td>
                                <td>
                                    @if(!empty($variation->media) && isset($variation->media[0]))
                                        <img src="{{ $variation->media[0]->display_url }}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; {{ $isOutOfStock ? 'opacity: 0.5;' : '' }}">
                                    @elseif(!empty($product->image_url))
                                        <img src="{{ $product->image_url }}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; {{ $isOutOfStock ? 'opacity: 0.5;' : '' }}">
                                    @else
                                        <img src="{{ asset('images/default-product.png') }}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; {{ $isOutOfStock ? 'opacity: 0.5;' : '' }}">
                                    @endif
                                </td>
                                <td style="{{ $isOutOfStock ? 'opacity: 0.7;' : '' }}">
                                    <strong>{{ $variation->sub_sku ?? '' }}</strong><br>
                                    {{ $product->name }}
                                    @if(!empty($variation->name) && $variation->name !== 'DUMMY')
                                        <br><small class="text-muted"><i>{{ $variation->name }}</i></small>
                                    @endif
                                    @if($isOutOfStock)
                                        <br><span class="label bg-red" style="font-size: 10px;"><i class="fa fa-exclamation-circle"></i> OUT OF STOCK</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="label {{ $availableStock >= $orderedQty ? 'bg-green' : ($availableStock > 0 ? 'bg-yellow' : 'bg-red') }}">
                                        {{ round($availableStock) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ round($orderedQty) }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($isOutOfStock)
                                        {{-- Disabled input for out of stock products --}}
                                        <div class="input-group" style="width: 120px; margin: 0 auto; opacity: 0.6;">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-sm" disabled>
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </span>
                                            <input type="number" 
                                                   class="form-control text-center bypass-fulfillable-qty" 
                                                   data-line-id="{{ $line->id }}"
                                                   data-ordered="{{ round($orderedQty) }}"
                                                   data-stock="0"
                                                   value="0"
                                                   min="0"
                                                   max="0"
                                                   disabled
                                                   readonly
                                                   style="width: 60px; padding: 5px; background-color: #f5f5f5; cursor: not-allowed;">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-sm" disabled>
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </span>
                                        </div>
                                    @else
                                        {{-- Enabled input for products with stock --}}
                                        <div class="input-group" style="width: 120px; margin: 0 auto;">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-sm bypass-qty-minus">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </span>
                                            <input type="number" 
                                                   class="form-control text-center bypass-fulfillable-qty" 
                                                   data-line-id="{{ $line->id }}"
                                                   data-ordered="{{ round($orderedQty) }}"
                                                   data-stock="{{ round($availableStock) }}"
                                                   value="{{ round($maxFulfillable) }}"
                                                   min="0"
                                                   max="{{ round($orderedQty) }}"
                                                   style="width: 60px; padding: 5px;">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-sm bypass-qty-plus">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center bypass-short-qty" style="color: #d32f2f; font-weight: bold;">
                                    {{ round(max(0, $orderedQty - $maxFulfillable)) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background: #f5f5f5;">
                        <tr>
                            <td colspan="4" class="text-right"><strong>Totals:</strong></td>
                            <td class="text-center"><strong id="bypass_total_ordered">{{ round($order->sell_lines->sum('quantity')) }}</strong></td>
                            <td class="text-center"><strong id="bypass_total_fulfillable">{{ round($order->sell_lines->sum(function($line) {
                                $stock = $line->variations->variation_location_details[0]->qty_available ?? 0;
                                return min($line->quantity, $stock);
                            })) }}</strong></td>
                            <td class="text-center" style="color: #d32f2f;"><strong id="bypass_total_short">{{ round($order->sell_lines->sum(function($line) {
                                $stock = $line->variations->variation_location_details[0]->qty_available ?? 0;
                                return max(0, $line->quantity - $stock);
                            })) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            {{-- Summary Alert --}}
            <div id="bypass_summary_alert" class="alert alert-warning" style="margin-top: 15px; display: none;">
                <i class="fa fa-exclamation-triangle"></i>
                <span id="bypass_summary_text"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-success" id="bypass_fill_max_btn">
                <i class="fa fa-arrow-up"></i> Fill Max Available
            </button>
            <button type="button" class="btn btn-primary" id="bypass_confirm_btn" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); border: none;">
                <i class="fa fa-check"></i> Confirm Bypass
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update short quantity and totals when fulfillable qty changes
    function updateBypassTotals() {
        let totalFulfillable = 0;
        let totalShort = 0;
        let hasPartialFulfillment = false;
        let hasZeroFulfillment = false;
        let hasOutOfStock = false;
        
        $('.bypass-line-row').each(function() {
            const row = $(this);
            const input = row.find('.bypass-fulfillable-qty');
            const orderedQty = parseInt(input.data('ordered'));
            const stock = parseInt(input.data('stock'));
            const fulfillableQty = parseInt(input.val()) || 0;
            const shortQty = Math.max(0, orderedQty - fulfillableQty);
            const isOutOfStock = row.hasClass('out-of-stock-row') || stock <= 0;
            
            row.find('.bypass-short-qty').text(shortQty);
            totalFulfillable += fulfillableQty;
            totalShort += shortQty;
            
            if (fulfillableQty < orderedQty && fulfillableQty > 0) {
                hasPartialFulfillment = true;
            }
            if (fulfillableQty === 0) {
                hasZeroFulfillment = true;
            }
            if (isOutOfStock) {
                hasOutOfStock = true;
            }
            
            // Update row styling (skip out-of-stock rows as they have fixed styling)
            if (!isOutOfStock) {
                if (fulfillableQty === 0) {
                    row.css('background-color', '#ffebee');
                } else if (fulfillableQty < orderedQty) {
                    row.css('background-color', '#fff8e1');
                } else {
                    row.css('background-color', '#e8f5e9');
                }
            }
        });
        
        $('#bypass_total_fulfillable').text(totalFulfillable);
        $('#bypass_total_short').text(totalShort);
        
        // Show summary alert
        const summaryAlert = $('#bypass_summary_alert');
        const summaryText = $('#bypass_summary_text');
        
        if (totalShort > 0) {
            summaryAlert.show();
            if (hasOutOfStock && hasPartialFulfillment) {
                summaryText.html(`<strong>${totalShort} items</strong> will be marked as short. Some products are out of stock and others have partial availability.`);
            } else if (hasOutOfStock) {
                summaryText.html(`<strong>${totalShort} items</strong> will be marked as short. Some products are <strong>OUT OF STOCK</strong> and cannot be fulfilled.`);
            } else if (hasZeroFulfillment && hasPartialFulfillment) {
                summaryText.html(`<strong>${totalShort} items</strong> will be marked as short. Some products will be skipped entirely.`);
            } else if (hasZeroFulfillment) {
                summaryText.html(`<strong>${totalShort} items</strong> will be marked as short. Some products have zero fulfillable quantity.`);
            } else {
                summaryText.html(`<strong>${totalShort} items</strong> will be marked as short due to insufficient stock.`);
            }
        } else {
            summaryAlert.hide();
        }
    }
    
    // Quantity input change
    $(document).on('input change', '.bypass-fulfillable-qty', function() {
        const input = $(this);
        const max = parseInt(input.attr('max'));
        const min = parseInt(input.attr('min'));
        let val = parseInt(input.val()) || 0;
        
        // Validate
        if (val < min) {
            input.val(min);
            val = min;
        }
        if (val > max) {
            input.val(max);
            val = max;
            toastr.warning('Quantity cannot exceed ordered quantity');
        }
        
        updateBypassTotals();
    });
    
    // Minus button
    $(document).on('click', '.bypass-qty-minus', function() {
        const input = $(this).closest('.input-group').find('.bypass-fulfillable-qty');
        let val = parseInt(input.val()) || 0;
        if (val > 0) {
            input.val(val - 1).trigger('change');
        }
    });
    
    // Plus button
    $(document).on('click', '.bypass-qty-plus', function() {
        const input = $(this).closest('.input-group').find('.bypass-fulfillable-qty');
        const max = parseInt(input.attr('max'));
        let val = parseInt(input.val()) || 0;
        if (val < max) {
            input.val(val + 1).trigger('change');
        }
    });
    
    // Fill Max Available button
    $('#bypass_fill_max_btn').on('click', function() {
        $('.bypass-fulfillable-qty:not(:disabled)').each(function() {
            const input = $(this);
            const ordered = parseInt(input.data('ordered'));
            const stock = parseInt(input.data('stock'));
            input.val(Math.min(ordered, stock)).trigger('change');
        });
    });
    
    // Confirm Bypass button
    $('#bypass_confirm_btn').on('click', function() {
        const orderId = $('#bypass_order_id').val();
        const quantities = {};
        let totalFulfillable = 0;
        
        $('.bypass-fulfillable-qty').each(function() {
            const lineId = $(this).data('line-id');
            const qty = parseInt($(this).val()) || 0;
            quantities[lineId] = qty;
            totalFulfillable += qty;
        });
        
        if (totalFulfillable === 0) {
            swal({
                title: 'No Items to Fulfill',
                text: 'All items have zero fulfillable quantity. Are you sure you want to bypass this order with no items?',
                icon: 'warning',
                buttons: {
                    cancel: 'Cancel',
                    confirm: {
                        text: 'Yes, Bypass Anyway',
                        className: 'bg-danger'
                    }
                },
                dangerMode: true
            }).then((confirmed) => {
                if (confirmed) {
                    processConfirmBypass(orderId, quantities);
                }
            });
            return;
        }
        
        const totalShort = parseInt($('#bypass_total_short').text()) || 0;
        
        if (totalShort > 0) {
            swal({
                title: 'Confirm Partial Bypass',
                text: `${totalShort} items will be marked as short. Do you want to proceed?`,
                icon: 'warning',
                buttons: {
                    cancel: 'Cancel',
                    confirm: {
                        text: 'Yes, Proceed',
                        className: 'bg-purple'
                    }
                },
                dangerMode: true
            }).then((confirmed) => {
                if (confirmed) {
                    processConfirmBypass(orderId, quantities);
                }
            });
        } else {
            processConfirmBypass(orderId, quantities);
        }
    });
    
    function processConfirmBypass(orderId, quantities) {
        const btn = $('#bypass_confirm_btn');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: '/bypass-order-partial',
            type: 'POST',
            data: {
                order_id: orderId,
                quantities: quantities,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Bypass');
                
                if (response.status) {
                    toastr.success(response.message);
                    $('#bypass_order_modal').modal('hide');
                    
                    // Reload the processing orders table
                    if (typeof processingOrdersTable !== 'undefined') {
                        processingOrdersTable.ajax.reload(null, false);
                    }
                    if (typeof packingOrdersTable !== 'undefined') {
                        packingOrdersTable.ajax.reload(null, false);
                    }
                } else {
                    toastr.error(response.message || 'Failed to bypass order');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Bypass');
                toastr.error(xhr.responseJSON?.message || 'Error occurred while bypassing order');
            }
        });
    }
    
    // Initial totals calculation
    updateBypassTotals();
});
</script>

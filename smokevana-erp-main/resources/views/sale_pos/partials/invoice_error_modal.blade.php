<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header" style="background: #37475A; color: #ffffff; border: none;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" style="color: #fff;">
                <i class="{{ $error_icon ?? 'fas fa-exclamation-circle' }}"></i> 
                {{ $error_title ?? 'Error' }}
            </h4>
        </div>
        <div class="modal-body" style="padding: 30px; text-align: center;">
            <div style="margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: {{ $error_color ?? '#ef4444' }}15; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="{{ $error_icon ?? 'fas fa-exclamation-circle' }}" style="font-size: 40px; color: {{ $error_color ?? '#ef4444' }};"></i>
                </div>
            </div>
            
            <h4 style="color: #1f2937; margin-bottom: 15px; font-weight: 600;">
                {{ $error_title ?? 'Error' }}
            </h4>
            
            <p style="color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 20px;">
                {{ $error_message ?? 'An error occurred.' }}
            </p>
            
            @if(isset($current_order))
            <div style="background: #f3f4f6; border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: left;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280; font-size: 13px;">Current Order:</span>
                    <span style="color: #1f2937; font-weight: 600;">{{ $current_order->invoice_no }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #6b7280; font-size: 13px;">Order Type:</span>
                    <span style="color: #1f2937; font-weight: 500;">
                        @if($current_order->type === 'erp_sales_order')
                            <span class="label" style="background: #10b981;">ERP Fulfilled</span>
                        @elseif($current_order->type === 'wp_sales_order')
                            <span class="label" style="background: #8b5cf6;">WC Vendor Dropship</span>
                        @elseif($current_order->type === 'erp_dropship_order')
                            <span class="label" style="background: #f59e0b;">ERP Vendor Dropship</span>
                        @else
                            {{ ucfirst(str_replace('_', ' ', $current_order->type)) }}
                        @endif
                    </span>
                </div>
                @if(isset($show_parent_link) && $show_parent_link && isset($parent_invoice_no))
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #e5e7eb; margin-top: 10px;">
                    <span style="color: #6b7280; font-size: 13px;">Parent Order:</span>
                    <span style="color: #059669; font-weight: 600;">
                        <i class="fas fa-arrow-up"></i> {{ $parent_invoice_no }}
                    </span>
                </div>
                @endif
            </div>
            @endif
            
            @if(isset($show_parent_link) && $show_parent_link && isset($parent_id))
            <div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #10b981; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                <p style="color: #065f46; font-size: 14px; margin-bottom: 10px;">
                    <i class="fas fa-info-circle"></i> 
                    <strong>To create an invoice:</strong> Open the parent order and create the invoice from there. 
                    The invoice will automatically include all items from child orders.
                </p>
                <a href="#" 
                   data-href="{{ action([\App\Http\Controllers\SellController::class, 'saleInvoiceCreate'], [$parent_id]) }}" 
                   class="btn-modal tw-dw-btn tw-dw-btn-success tw-text-white" 
                   data-container=".view_modal"
                   style="display: inline-block;">
                    <i class="fas fa-external-link-alt"></i> Open Parent Order ({{ $parent_invoice_no }})
                </a>
            </div>
            @endif
        </div>
        <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 15px 20px;">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

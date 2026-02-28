@extends('layouts.app')
@section('title', __('sale.sell_details'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        @if ($sell->type == 'sales_order')
            @lang('restaurant.order_no'): {{ $sell->invoice_no }}
        @else
            @lang('sale.invoice_no'): {{ $sell->invoice_no }}
        @endif
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            {{-- Include the modal content without modal wrapper --}}
            <style>
                .btn-modal-cl {
                    padding: 4px 8px;
                    font-size: 12px;
                    line-height: 1.2;
                    border-radius: 4px;
                }
                /* Override modal styles for full page display */
                .sell-details-wrapper .modal-dialog {
                    width: 100% !important;
                    max-width: 100% !important;
                    margin: 0 !important;
                }
                .sell-details-wrapper .modal-content {
                    box-shadow: none !important;
                    border: none !important;
                    background: #fff;
                    border-radius: 12px;
                }
                .sell-details-wrapper .modal-header {
                    border-radius: 12px 12px 0 0;
                    background: #f8f9fa;
                }
                /* Hide close button on full page */
                .sell-details-wrapper #close_button {
                    display: none !important;
                }
            </style>
            <div class="sell-details-wrapper">
                @include('sale_pos.show')
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize any JavaScript needed for the page
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2();
    }
    
    // Print invoice functionality
    $(document).on('click', '.print-invoice', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        if (href) {
            window.open(href, '_blank');
        }
    });
    
    // Add back button
    var backBtn = $('<a href="javascript:history.back()" class="tw-dw-btn tw-dw-btn-sm" style="background: #fff; border: 1px solid #d1d5db; color: #485769; font-weight: 500; border-radius: 6px; margin-bottom: 10px;"><i class="fa fa-arrow-left" style="margin-right: 4px;"></i> Back</a>');
    $('.content-header h1').before(backBtn);
});
</script>
@endsection

<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <style>
                /* Container with static gray background */
                .progress {
                    background-color: #d6d6d6;
                    /* clean gray shade */
                    border-radius: 10px;
                    height: 20px;
                    width: 100%;
                    overflow: hidden;
                    box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.15);
                }

                /* Progress bar with animated gradient */
                .progress-bar {
                    height: 100%;
                    border-radius: 10px;
                    text-align: center;
                    font-weight: 600;
                    font-size: 12px;
                    color: #fff;
                    line-height: 20px;
                    background-image: linear-gradient(270deg, #c8ff00, #0072ff, #d9ff00);
                    background-size: 200% 100%;
                    background-position: 100% 0;
                    animation: barFlow 3s linear infinite;
                    transition: width 0.5s ease-in-out;
                }

                /* Animated flow */
                @keyframes barFlow {
                    0% {
                        background-position: 100% 0;
                    }

                    100% {
                        background-position: 0 0;
                    }
                }
            </style>

            <!-- Use it -->
            <div class="col-xs-2">
                <div class="progress">
                    <div class="progress-bar" role="progressbar"
                        style="width: {{ number_format($fulfilledPercentage, 2) }}%;"
                        aria-valuenow="{{ number_format($fulfilledPercentage, 2) }}" aria-valuemin="0"
                        aria-valuemax="100">
                        {{ number_format($fulfilledPercentage, 2) }}%
                    </div>
                </div>
            </div>


        </div>

        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellController::class, 'manualPickStore'], [$pickingOrders->id]),
            'method' => 'post',
            'id' => 'edit_pos_sell_form',
            'class' => 'form-validation',
        ]) !!}
        <input type="hidden" name="transaction_id" value="<?php echo $pickingOrders->id; ?>" />
        <div class="modal-body">
            <table class="table table-slim mb-0 bg-light-gray" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th style="white-space: nowrap;">SKU</th>
                        <th style="white-space: nowrap;">Barcode</th>
                        <th style="white-space: nowrap;">Ordered Quantity</th>
                        <th style="white-space: nowrap;">Stock Quantity</th>
                        <th style="white-space: nowrap;">Picked Quantity</th>
                    </tr>
                </thead>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item['sku'] }}</td>
                        <td>{{ $item['barcode'] }}</td>
                        <td>{{ $item['ordered_quantity'] }}</td>
                        <td>{{ $item['in_hand_stock'] }}</td>
                        <td>
                            <input type="number" class="form-control picked-quantity"
                                name="picked_quantity[{{ $item['line_id'] }}]"
                                id="picked_quantity_{{ $item['line_id'] }}" min="0"
                                value="{{ $item['picked_quantity'] }}" max="{{ $item['ordered_quantity'] }}"
                                data-in-hand-stock="{{ $item['in_hand_stock'] }}" required />
                            <label class="error" id="error_{{ $item['line_id'] }}"
                                style="display:none; color: red;">Invalid quantity</label>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save" aria-hidden="true"></i> Save Picking
            </button>
            <a href="#" data-href="" class="btn-modal hide" data-container=".view_modal">
                <i class="fas fa-file-alt" aria-hidden="true"></i> @lang('lang_v1.packing_slip')
            </a>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#edit_pos_sell_form').submit(function(e) {
            e.preventDefault(); // 
            let isValid = true;
            $('input.picked-quantity').each(function() {
                let pickedQuantity = parseInt($(this).val());
                let maxPickedQuantity = parseInt($(this).attr('max'));
                let inHandStock = parseInt($(this).data('in-hand-stock'));
                let lineId = $(this).attr('id').split('_')[2];
                let errorLabel = $('#error_' + lineId);
                if (pickedQuantity > maxPickedQuantity || pickedQuantity > inHandStock) {
                    $(this).addClass('is-invalid');
                    errorLabel.text("invalid input");
                    errorLabel.show();
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                    errorLabel.hide();
                }
            });

            if (isValid) {
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error(
                            'There was an error processing your request. Please try again.'
                        );
                    }
                });
            } else {
                toastr.error('Please fix the errors before submitting the form.');
            }
        });
    });
</script>

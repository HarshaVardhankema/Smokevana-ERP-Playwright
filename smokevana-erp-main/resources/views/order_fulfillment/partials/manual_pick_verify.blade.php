<div class="modal-dialog modal-xl no-print" id='manual_pick_verify' role="document">
    <input type="hidden" id="order_id" value="{{ $data->id }}">
    <div class="modal-content">
        {{-- Header with Branding --}}
        <div class="tw-bg-gray-100 tw-p-4 tw-border-b no-print" style="position: relative;">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 5px; right: 10px; z-index: 1000; background: rgba(255, 255, 255, 0.9); border: 1px solid #ddd; border-radius: 3px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 16px; color: #333; transition: all 0.3s; padding: 0; line-height: 1;" onmouseover="this.style.background='#f44336'; this.style.color='white';" onmouseout="this.style.background='rgba(255, 255, 255, 1)'; this.style.color='#333';">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="tw-flex tw-justify-between tw-items-center">
                <div class="tw-flex tw-items-center">
                    @php
                        $business_logo = session('business.logo');
                        if (!empty($business_logo)) {
                            $business_logo = asset('/uploads/business_logos/' . $business_logo);
                        } else {
                            $business_name = session('business.name');
                        }
                    @endphp
                    @if ($business_logo)
                        <img src="{{ $business_logo }}" alt="Logo"
                            class="tw-h-12 tw-mr-4 tw-w-32 tw-object-contain no-print">
                    @else
                        <h2 class="tw-text-xl tw-font-bold">{{ $business_name }}</h2>
                    @endif
                    <div>
                        <h2 class="tw-text-xl tw-font-bold">{{ $data->invoice_no }}</h2>
                        <p class="tw-text-gray-600">Order Date: {{ $data->transaction_date }}</p>
                    </div>
                </div>
                <div class="tw-flex tw-gap-2 no-print">
                    <span id="response-message" class="center-notification"></span>
                </div>
                {{-- Action Buttons --}}
                <div class="tw-flex tw-justify-end tw-gap-2 tw-mt-4 tw-p-4 no-print">
                    <button class="btn btn-info" id="print-picking">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button class="btn btn-primary" id="save-picking">Save
                        {{ $isVerifier ? 'Verification' : 'Picking' }}</button>
                    <button class="btn btn-success" id="finish-picking">Complete</button>
                    @if (!$isVerifier)
                        <button class="btn btn-danger" id="global-mark-short">
                            <i class="fas fa-exclamation-triangle"></i> Mark All Short
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <style>
            .modal-content .close:hover {
                opacity: 1 !important;
                color: #000;
            }
        </style>
        {{-- Print Header (Hidden by default) --}}
        <div class="print-header" style="display: none;">
            <div class="tw-flex tw-justify-between tw-items-center tw-mb-4"
                style="display:flex ;no-print; justify-content:space-between">
                <div class="tw-flex tw-items-center">
                    @if ($business_logo)
                        <img src="{{ $business_logo }}" alt="Logo" class="tw-w-32 tw-object-contain "
                            width="50">
                    @else
                        <h2 class="tw-text-xl tw-font-bold">{{ $business_name }}</h2>
                    @endif
                    <div class="tw-ml-4">
                        <h2 class="tw-text-xl tw-font-bold">{{ $data->invoice_no }}</h2>
                        <p class="tw-text-gray-600">Order Date: {{ $data->transaction_date }}</p>
                    </div>
                </div>
                <div class="tw-text-right">
                    <h2 class="tw-text-xl tw-font-bold tw-text-gray-800">Smokevana (ERP Suit)</h2>
                    <p class="tw-text-gray-600">Powered by Phantasm.in</p>
                </div>
            </div>
        </div>

        <div class="tab-pane active ">
            <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 tw-items-center tw-w-full tw-mb-4">
                {{-- Barcode Input and Camera Button --}}
                <div class="tw-flex tw-w-full tw-items-center tw-gap-2 tw-flex-wrap no-print">
                    <input type="text" id="barcode_scanner_input" placeholder="Scan barcode"
                        class="form-control tw-flex-1 tw-min-w-[200px]" />

                    <button
                        class="btn btn-secondary tw-flex tw-items-center tw-justify-center tw-px-4 tw-py-2 tw-text-lg"
                        id="start-camera-scan" type="button">
                        <i class="fas fa-camera tw-text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Camera View --}}
            <div id="camera-scanner" class="tw-w-full" style="display:none; margin-bottom: 20px;"></div>
            <div class="tw-overflow-y-auto" style="max-height: 60vh;">
                <table style="" class="table table-bordered table-striped ">
                    <thead style="white-space: nowrap;position: sticky; top: 0; z-index: 9;">
                        <tr>
                            <th>S. No.</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th class="tw-text-center">Stock</th>
                            <th class="tw-text-center">Ordered Qty</th>
                            <th class="tw-text-center">Picked Qty</th>
                            @if ($isVerifier)
                                <th class="tw-text-center">Verify Qty</th>
                            @endif
                            @if (!$isVerifier)
                                <th class="tw-text-center">Short Qty</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = 1; @endphp
                        @foreach ($data->sell_lines as $item)
                            <tr data-line-id="{{ $item->id }}" class="quantity-row"
                                data-ordered-qty="{{ $item->ordered_quantity }}"
                                data-picked-qty="{{ $item->picked_quantity }}" data-is-picked="{{ $item->is_picked }}"
                                style="{{$item->verified_qty == 0? 'background-color: #ffcccc;': ($isVerifier? ($item->verified_qty == $item->ordered_quantity? 'background-color: #ccffd6;': 'background-color: #fff7cc;') : ($item->ordered_quantity == $item->picked_quantity? 'background-color: #ccffd6;' : ($item->is_picked || $item->ordered_quantity > $item->picked_quantity ? 'background-color: #ffcccc;': '')))}}">

                                <td data-id="{{ $item->id }}">{{ $counter++ }}</td>
                                <td>
                                    @if (!empty($item->variations->media) || !empty($item->product->image_url))
                                        <img width="50" height="50"
                                            src="{{ $item->variations->media[0]->display_url ?? ($item->product->image_url ?? '') }}"
                                            alt="Product Image" class="tw-w-16 tw-h-16 tw-object-cover">
                                    @else
                                        <img width="50" height="50"
                                            src="{{ asset('images/default-product.png') }}" alt="Product Image"
                                            class="tw-w-16 tw-h-16 tw-object-cover">
                                    @endif
                                </td>

                                <td>
                                    <b
                                        data-sub-sku="{{ $item->variations->sub_sku ?? '' }}">{{ $item->variations->sub_sku ?? '' }}</b>
                                    <b
                                        data-barcode="{{ $item->variations->var_barcode_no ?? '' }}">{{ $item->variations->var_barcode_no ?? '' }}</b>
                                    {{ $item->product->name }}
                                    @if (!empty($item->variations->name) && $item->variations->name !== 'DUMMY')
                                        ( <b style="font-size: 12px;"><i>{{ $item->variations->name }} </i></b>)
                                    @endif
                                </td>
                                <td class="tw-text-center"><i class="fas fa-box-open"></i>
                                    {{ round($item->variations->variation_location_details[0]->qty_available ?? 0) }}
                                </td>
                                <td class="tw-text-center"> {{ round($item->ordered_quantity) }}</td>

                                {{-- Picked Qty Column --}}
                                @if (!$isVerifier)
                                    <td class="tw-text-center">

                                        <div class="qty-input-group">
                                            <button type="button" class="qty-btn minus-btn no-print">-</button>
                                            <input type="number" name="picked_qty[]" class="form-control inline-pick "
                                                value="{{ $item->picked_quantity ?? 0 }}" min="0"
                                                max="{{ $item->ordered_quantity }}" step="1"
                                                data-line-id="{{ $item->id }}"
                                                data-max="{{ $item->ordered_quantity }}"
                                                data-stock="{{ $item->variations->variation_location_details[0]->qty_available ?? 0 }}"
                                                data-enable-stock="{{ $item->product->enable_stock ?? 1 }}"
                                                data-barcode="{{ $item->variations->var_barcode_no ?? '' }}"
                                                data-sub-sku="{{ $item->variations->sub_sku ?? '' }}"
                                                style="height: 28px; width:34px" />
                                            <button type="button" class="qty-btn plus-btn no-print">+</button>
                                        </div>
                                    </td>
                                @else
                                    <td class="tw-text-center">{{ $item->picked_quantity ?? 0 }}</td>
                                @endif

                                @if ($isVerifier)
                                    <td class="tw-text-center">
                                        <div class="qty-input-group">
                                            <div class="quantity-controls" style="display: flex">
                                                <button type="button" class="qty-btn minus-btn no-print"
                                                    @if ($item->isVerified && ($item->picked_quantity ?? 0) > ($item->verified_qty ?? 0)) disabled @endif>-</button>
                                                <input type="number" name="verify_qty[]"
                                                    class="form-control inline-pick"
                                                    value="{{ $item->verified_qty ?? 0 }}" min="0"
                                                    max="{{ $item->ordered_quantity }}" step="1"
                                                    data-line-id="{{ $item->id }}"
                                                    data-max="{{ $item->ordered_quantity }}"
                                                    data-stock="{{ $item->variations->variation_location_details[0]->qty_available ?? 0 }}"
                                                    data-enable-stock="{{ $item->product->enable_stock ?? 1 }}"
                                                    data-barcode="{{ $item->variations->var_barcode_no ?? '' }}"
                                                    data-sub-sku="{{ $item->variations->sub_sku ?? '' }}"
                                                    style="height: 28px; width:34px"
                                                    @if ($item->isVerified && ($item->picked_quantity ?? 0) > ($item->verified_qty ?? 0)) readonly @endif />
                                                <button type="button" class="qty-btn plus-btn no-print"
                                                    @if ($item->isVerified && ($item->picked_quantity ?? 0) > ($item->verified_qty ?? 0)) disabled @endif>+</button>
                                                @if (($item->verified_qty ?? 0) >= 0 && ($item->picked_quantity ?? 0) > ($item->verified_qty ?? 0))
                                                    <input type="checkbox" class="mark-short-checkbox"
                                                        style="margin-left: 8px;"
                                                        {{ $item->isVerified ? 'checked' : '' }}
                                                        onchange="toggleQuantityControls(this, {{ $item->id }})" />
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                @if (!$isVerifier)
                                    <td class="short-qty tw-text-center" data-line-id="{{ $item->id }}">
                                        @if ($item->ordered_quantity != $item->picked_quantity)
                                            @if ($item->is_picked)
                                                <button type="button" class="btn btn-warning btn-sm revert-short-btn"
                                                    data-toggle="tooltip" title="Click to revert shorted status">
                                                    <i class="fas fa-undo"></i> Revert Short
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-danger btn-sm mark-short-btn"
                                                    data-toggle="tooltip" title="Click to mark as shorted">
                                                    <i class="fas fa-exclamation-triangle"></i> Mark as Short
                                                </button>
                                            @endif
                                            <span class="tw-ml-2 tw-text-sm tw-text-gray-600">
                                                ({{ $item->ordered_quantity - $item->picked_quantity }} remaining)
                                            </span>
                                        @else
                                            <span class="tw-text-sm tw-text-success">
                                                <i class="fas fa-check-circle"></i> Complete
                                            </span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="tw-font-bold tw-bg-gray-100">
                            <td colspan=5 class="tw-text-right">Totals:</td>
                            <td class="tw-text-center">{{ $totalPicked }}</td>
                            @if ($isVerifier)
                                <td class="tw-text-center">{{$totalVerified}}</td>
                            @endif
                            @if (!$isVerifier)
                                <td class="tw-text-center">{{ $totalOrdered - $totalPicked }}</td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>


        </div>
    </div>
</div>

{{-- @include('layouts.partials.javascripts') --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // Configure toastr to show all notifications at top center
    toastr.options = {
        positionClass: "toast-top-center",
        timeOut: 5000,
        extendedTimeOut: 2000,
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
        showDuration: "300",
        hideDuration: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

    // View the JSON data in the browser's console
    $(document).ready(function() {
        let lastKeyPressTime = 0;
        const DELAY_BETWEEN_KEYPRESSES = 1;
        let qtyChangeTimer = null;
        let lastButtonClickTime = 0;
        const DELAY_BETWEEN_CLICKS = 500;
        let barcodeTimer = null;
        let pasteQueue = [];
        let isProcessingPaste = false;
        let html5QrCode;
        const cameraContainer = $('#camera-scanner');
        const inputField = $('#barcode_scanner_input');

        let pickingStarted = false;
        let pickingStartTime = null;
        const type = '{{ $isVerifier ? 'verifier' : 'picker' }}';

        // Focus on barcode scanner input when modal opens
        $('#barcode_scanner_input').focus();

        // Function to refocus on barcode scanner input
        function refocusBarcodeInput() {
            setTimeout(() => {
                $('#barcode_scanner_input').focus();
            }, 100);
        }


        function markPickingStartIfNeeded() {
            if (!pickingStarted) {
                pickingStarted = true;
                pickingStartTime = new Date();

                $.post('{{ route('pick.startTime') }}', {
                        transaction_id: '{{ $data->id }}',
                        type: type,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done(function(response) {
                        if (!response.status) {
                            toastr.error(response.message);
                            pickingStarted = false;
                            pickingStartTime = null;
                        }
                    })
                    .fail(function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Error recording picking start time');
                        pickingStarted = false;
                        pickingStartTime = null;
                    });
            }
        }

        function toggleQuantityControls(checkbox, lineId) {
            const input = document.querySelector(`input[data-line-id="${lineId}"]`);
            const minusBtn = input.previousElementSibling;
            const plusBtn = input.nextElementSibling;

            if (checkbox.checked) {
                input.setAttribute('readonly', 'readonly');
                minusBtn.setAttribute('disabled', 'disabled');
                plusBtn.setAttribute('disabled', 'disabled');
            } else {
                input.removeAttribute('readonly');
                minusBtn.removeAttribute('disabled');
                plusBtn.removeAttribute('disabled');
            }
        }
        // Keyboard navigation for quantity inputs
        

        $(document).on('keydown', function(e) {
            if (e.repeat) return; // Prevent repeat keypresses
            markPickingStartIfNeeded();
            const focusedInput = $('.inline-pick:focus');
            if (!focusedInput.length) return;

            // Only prevent default for arrow keys
            if (e.keyCode === 37 || e.keyCode === 39) {
                e.preventDefault();
            }

            const qtyInputGroup = focusedInput.closest('.qty-input-group');
            if (e.keyCode === 37) { // Left arrow key

                qtyInputGroup.find('.minus-btn').trigger('click');
            } else if (e.keyCode === 39) { // Right arrow key

                qtyInputGroup.find('.plus-btn').trigger('click');
            }
        });

        // Function to update row color and remaining quantity
        
        function updateRowStatus(row) {
            const orderedQty = parseInt(row.attr('data-ordered-qty'));
            const pickedQty = parseInt(row.attr('data-picked-qty') || 0);
            const isPicked = row.attr('data-is-picked') === '1';


            @if ($isVerifier)
                const verifyQty = parseInt(row.find('.inline-pick').val() || 0);
                const isCheckboxChecked = row.find('.mark-short-checkbox').is(':checked');

                // Update row color based on checkbox state and verifier logic
                if (isCheckboxChecked) {
                    row.css('background-color', '#fff7cc'); // yellow when checkbox is checked
                } else {
                    // Original verifier quantity comparison logic
                    if (verifyQty === pickedQty) {
                        row.css('background-color', '#ccffd6'); // Green for matching quantities
                    } else if (verifyQty < pickedQty) {
                        row.css('background-color', '#ffcccc');
                    } else {
                        row.css('background-color', '#ffcccc'); // Red for more than picked
                    }
                }
                // Dynamically update checkbox visibility
                const checkboxCell = row.find('.quantity-controls');
                if (verifyQty >= 0 && pickedQty > verifyQty) {
                    if (!checkboxCell.find('.mark-short-checkbox').length) {
                        const checkbox = $('<input>', {
                            type: 'checkbox',
                            class: 'mark-short-checkbox',
                            style: 'margin-left: 8px;',
                            checked: false
                        }).attr('onchange', `toggleQuantityControls(this, ${row.data('line-id')})`);
                        checkboxCell.append(checkbox);
                    }
                } else {
                    checkboxCell.find('.mark-short-checkbox').remove();
                }
            @else
                // Original picker logic
                if (isPicked) {
                    row.css('background-color', '#fff7cc');
                } else if (pickedQty === orderedQty) {
                    row.css('background-color', '#ccffd6');
                } else if (pickedQty < orderedQty) {
                    row.css('background-color', '#ffcccc');
                }
            @endif

            // Update remaining quantity
            const remainingQty = orderedQty - pickedQty;
            const shortCell = row.find('.short-qty');

            if (pickedQty === orderedQty) {
                shortCell.html(`
            <span class="tw-text-sm tw-text-success">
                <i class="fas fa-check-circle"></i> Complete
            </span>
        `);
            } else {
                if (isPicked) {

                    shortCell.html(`
                <button type="button" 
                    class="btn btn-warning btn-sm revert-short-btn"
                    data-toggle="tooltip"
                    title="Click to revert shorted status">
                    <i class="fas fa-undo"></i> Revert Short
                </button>
                <span class="tw-ml-2 tw-text-sm tw-text-gray-600">
                    (${remainingQty} remaining)
                </span>
            `);
                } else {

                    shortCell.html(`
                <button type="button" 
                    class="btn btn-danger btn-sm mark-short-btn"
                    data-toggle="tooltip"
                    title="Click to mark as shorted">
                    <i class="fas fa-exclamation-triangle"></i> Mark as Short
                </button>
                <span class="tw-ml-2 tw-text-sm tw-text-gray-600">
                    (${remainingQty} remaining)
                </span>
            `);
                }
            }
            $('[data-toggle="tooltip"]').tooltip();

            // Update totals
            updateTotals();
        }
        const isVerifier = '{{ $isVerifier }}' === '1';
        // Function to update totals
        function updateTotals() {
            let totalPicked = 0;
            let totalShort = 0;
            let totalVerified = 0;
            $('.quantity-row').each(function() {
                const row = $(this);
                const orderedQty = parseInt(row.attr('data-ordered-qty'));
                const pickedQty = parseInt(row.find('.inline-pick').val() || 0);
                if(isVerifier){
                    const verifyQty = parseInt(row.find('.inline-pick').val() || 0);
                    totalVerified += verifyQty; 
                    $('tr.tw-font-bold td:eq(2)').text(totalVerified);
                }else{
                    totalPicked += pickedQty;
                    totalShort += (orderedQty - pickedQty);
                    // Update the totals row
                    $('tr.tw-font-bold td:eq(1)').text(totalPicked);
                    $('tr.tw-font-bold td:eq(2)').text(totalShort);
                }
                
            });

        }

        // Input validation and real-time updates
        $('.inline-pick').on('input', function() {
            const input = $(this);
            const row = input.closest('tr');
            const orderedQty = parseInt(row.attr('data-ordered-qty'));
            const pickedQty = parseInt(input.val() || 0);

            // Remove any existing error class
            input.removeClass('is-invalid');

            // Validate quantity
            if (pickedQty > orderedQty) {
                input.addClass('is-invalid');
                toastr.error(`Quantity cannot exceed ordered quantity (${orderedQty})`);
                input.val(orderedQty);
            }

            // Update row status
            updateRowStatus(row);
        });


        $('.inline-pick').on('change', function() {

            markPickingStartIfNeeded();
            const input = $(this);
            const lineId = input.data('line-id');
            let quantity = parseInt(input.val()) || 0;
            const max = parseInt(input.data('max'));
            const stock = parseInt(input.data('stock'));
            const enable_stock = parseInt(input.data('enable-stock') || 1);
            const isVerifier = '{{ $isVerifier }}' === '1';
            const pickedQty = parseInt(input.closest('tr').attr('data-picked-qty'));
            const orderedQty = parseInt(input.closest('tr').attr('data-ordered-qty'));

            // Only validate against stock if enable_stock == 1
            if (enable_stock == 1 && quantity > max) {
                toastr.error("Quantity exceeds ordered or stock.");
                input.val(Math.min(max, stock));
                // Remove recursive trigger
                updateRowStatus(input.closest('tr'));
                return;
            } else if (enable_stock != 1 && quantity > max) {
                // If stock is disabled, only check against ordered quantity
                toastr.error("Quantity exceeds ordered quantity.");
                input.val(max);
                // Remove recursive trigger
                updateRowStatus(input.closest('tr'));
                return;
            }
            if (isVerifier && quantity > orderedQty) {

                toastr.error(`Verified quantity cannot exceed ordered quantity (${orderedQty})`);
                input.val(orderedQty);
                // Remove recursive trigger
                updateRowStatus(input.closest('tr'));
                return;
            }

            updateQuantity(lineId, quantity, 'manual');
            updateRowStatus(input.closest('tr'));
        });
        //Minus button click handler
        $('.minus-btn').on('click', function() {
            markPickingStartIfNeeded();
            const currentTime = new Date().getTime();
            if (currentTime - lastButtonClickTime < DELAY_BETWEEN_CLICKS) {
                return;
            }
            lastButtonClickTime = currentTime;

            const input = $(this).closest('.qty-input-group').find('.inline-pick');
            let value = parseInt(input.val()) || 0;
            if (value > 0) {
                input.val(value - 1);
                clearTimeout(qtyChangeTimer);
                qtyChangeTimer = setTimeout(() => {
                    input.trigger('change');
                    updateRowStatus(input.closest('tr'));
                }, 1000);
            }
        });

        // Plus button click handler
        $('.plus-btn').on('click', function() {
            markPickingStartIfNeeded();
            const currentTime = new Date().getTime();
            if (currentTime - lastButtonClickTime < DELAY_BETWEEN_CLICKS) {
                return;
            }
            lastButtonClickTime = currentTime;

                    const input = $(this).closest('.qty-input-group').find('.inline-pick');
                    let value = parseInt(input.val()) || 0;
                    const max = parseInt(input.data('max'));
                    const stock = parseInt(input.data('stock'));
                    const enable_stock = parseInt(input.data('enable-stock') || 1);

                    // Only check stock limit if enable_stock == 1
                    if (enable_stock == 1) {
                        if (value < max && value < stock) {
                            input.val(value + 1);
                            clearTimeout(qtyChangeTimer);
                            qtyChangeTimer = setTimeout(() => {
                                input.trigger('change');
                                updateRowStatus(input.closest('tr'));
                            }, 1000);
                        } else {
                            toastr.warning('Limit reached');
                        }
                    } else {
                        // If stock is disabled, only check against ordered quantity
                        if (value < max) {
                            input.val(value + 1);
                            clearTimeout(qtyChangeTimer);
                            qtyChangeTimer = setTimeout(() => {
                                input.trigger('change');
                                updateRowStatus(input.closest('tr'));
                            }, 1000);
                        } else {
                            toastr.warning('Limit reached');
                        }
                    }
        });

        // Barcode scanner input handler
        $('#barcode_scanner_input').on('input', function() {
            markPickingStartIfNeeded();
            clearTimeout(barcodeTimer);
            const inputEl = $(this);

            barcodeTimer = setTimeout(() => {
                const scanned = inputEl.val().trim();
                if (!scanned) return;

                let matched = false;
                let matchedLine = null;
                let matchedBarcode = null;
                let allMatches = [];

                $('.inline-pick').each(function() {
                    const input = $(this);
                    const barcode = String(input.data('barcode'));
                    const sku = String(input.data('sub-sku'));
                    let val = parseInt(input.val()) || 0;
                    const max = parseInt(input.data('max'));
                    const stock = parseInt(input.data('stock'));
                    const enable_stock = parseInt(input.data('enable-stock') || 1);
                    const isVerifier = '{{ $isVerifier }}' === '1';
                    const pickedQty = parseInt(input.closest('tr').attr(
                        'data-picked-qty'));

                    const isPicked = input.closest('tr').attr('data-is-picked') === '1';
                    const orderedQty = parseInt(input.closest('tr').attr('data-ordered-qty'));

                    // For verifier mode, we want to process lines that have been picked but not fully verified
                    // For picker mode, we want to process lines that haven't been fully picked
                    if (isVerifier) {
                        // In verifier mode, skip if already fully verified or if nothing was picked
                        if (val >= pickedQty || pickedQty <= 0) {
                            return;
                        }
                    } else {
                        // In picker mode, skip if already fully picked
                        if (isPicked || orderedQty <= pickedQty) {
                            return;
                        }
                    }
                    if (scanned === barcode || scanned === sku) {
                        // Store all matches instead of returning early
                        allMatches.push({
                            input: input,
                            val: val,
                            max: max,
                            stock: stock,
                            enable_stock: enable_stock,
                            pickedQty: pickedQty,
                            orderedQty: orderedQty,
                            isVerifier: isVerifier
                        });
                    }
                });

                // Process the best match (prioritize incomplete lines)
                if (allMatches.length > 0) {
                    // Sort matches to prioritize incomplete lines
                    allMatches.sort((a, b) => {
                        if (a.isVerifier) {
                            // For verifier mode, sort by remaining verification (picked_qty - verified_qty)
                            const aRemaining = a.pickedQty - a.val;
                            const bRemaining = b.pickedQty - b.val;
                            return bRemaining - aRemaining; // Sort by remaining verification (descending)
                        } else {
                            // For picker mode, sort by remaining picking (ordered_qty - picked_qty)
                            const aRemaining = a.orderedQty - a.val;
                            const bRemaining = b.orderedQty - b.val;
                            return bRemaining - aRemaining; // Sort by remaining quantity (descending)
                        }
                    });

                    const bestMatch = allMatches[0];
                    
                    // Check if the best match is already completed
                    const isCompleted = bestMatch.isVerifier ? 
                        (bestMatch.val >= bestMatch.pickedQty || bestMatch.pickedQty <= 0) : 
                        (bestMatch.val >= bestMatch.orderedQty);
                    
                    if (!isCompleted) {
                        matched = true;
                        matchedLine = bestMatch.input;
                        matchedBarcode = scanned;

                        if (bestMatch.isVerifier) {
                            // In verifier mode, increment if verified_qty < picked_qty
                            if (bestMatch.val < bestMatch.pickedQty) {
                                bestMatch.input.val(bestMatch.val + 1);
                            }
                        } else {
                            // In picker mode, increment if within limits
                            // Only check stock limit if enable_stock == 1
                            if (bestMatch.enable_stock == 1) {
                                if (bestMatch.val < bestMatch.max && bestMatch.val < bestMatch.stock) {
                                    bestMatch.input.val(bestMatch.val + 1);
                                }
                            } else {
                                // If stock is disabled, only check against ordered quantity
                                if (bestMatch.val < bestMatch.max) {
                                    bestMatch.input.val(bestMatch.val + 1);
                                }
                            }
                        }
                    } else {
                        // All matching lines are completed, don't process the scan
                        toastr.warning('Barcode scanned but all matching lines are already completed');
                        
                    }
                }

                updateQuantityWithBarcode(scanned, matched ? (parseInt(matchedLine.val()) || 0) : 0);

                inputEl.val('');
            }, 100);
        });

        // Paste handler for multiple barcodes
        $('#barcode_scanner_input').on('paste', function(e) {
            markPickingStartIfNeeded();
            const clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
            const pastedText = clipboardData.getData('text');
            const entries = pastedText.split(/[\r\n\t, ]+/).filter(Boolean);
            pasteQueue.push(...entries);
            $(this).val('');

            if (!isProcessingPaste) {
                processNextPastedBarcode();
            }

            e.preventDefault();
        });

        function processNextPastedBarcode() {
            if (pasteQueue.length === 0) {
                isProcessingPaste = false;
                return;
            }

            isProcessingPaste = true;
            const scanned = pasteQueue.shift().trim();
            if (!scanned) {
                processNextPastedBarcode();
                return;
            }

            let matched = false;
            let matchedLine = null;
            let matchedBarcode = null;
            let allMatches = [];

            $('.inline-pick').each(function() {
                const input = $(this);
                const barcode = String(input.data('barcode'));
                const sku = String(input.data('sub-sku'));
                let val = parseInt(input.val()) || 0;
                const max = parseInt(input.data('max'));
                const stock = parseInt(input.data('stock'));
                const enable_stock = parseInt(input.data('enable-stock') || 1);
                const isVerifier = '{{ $isVerifier }}' === '1';
                const pickedQty = parseInt(input.closest('tr').attr('data-picked-qty'));

                const isPicked = input.closest('tr').attr('data-is-picked') === '1';
                const orderedQty = parseInt(input.closest('tr').attr('data-ordered-qty'));

                // For verifier mode, we want to process lines that have been picked but not fully verified
                // For picker mode, we want to process lines that haven't been fully picked
                if (isVerifier) {
                    // In verifier mode, skip if already fully verified or if nothing was picked
                    if (val >= pickedQty || pickedQty <= 0) {
                        return;
                    }
                } else {
                    // In picker mode, skip if already fully picked
                    if (isPicked || orderedQty <= pickedQty) {
                        return;
                    }
                }

                if (scanned === barcode || scanned === sku) {
                    // Store all matches instead of returning early
                    allMatches.push({
                        input: input,
                        val: val,
                        max: max,
                        stock: stock,
                        enable_stock: enable_stock,
                        pickedQty: pickedQty,
                        orderedQty: orderedQty,
                        isVerifier: isVerifier
                    });
                }
            });

            // Process the best match (prioritize incomplete lines)
            if (allMatches.length > 0) {
                // Sort matches to prioritize incomplete lines
                allMatches.sort((a, b) => {
                    if (a.isVerifier) {
                        // For verifier mode, sort by remaining verification (picked_qty - verified_qty)
                        const aRemaining = a.pickedQty - a.val;
                        const bRemaining = b.pickedQty - b.val;
                        return bRemaining - aRemaining; // Sort by remaining verification (descending)
                    } else {
                        // For picker mode, sort by remaining picking (ordered_qty - picked_qty)
                        const aRemaining = a.orderedQty - a.val;
                        const bRemaining = b.orderedQty - b.val;
                        return bRemaining - aRemaining; // Sort by remaining quantity (descending)
                    }
                });

                const bestMatch = allMatches[0];
                
                // Check if the best match is already completed
                const isCompleted = bestMatch.isVerifier ? 
                    (bestMatch.val >= bestMatch.pickedQty || bestMatch.pickedQty <= 0) : 
                    (bestMatch.val >= bestMatch.orderedQty);
                
                if (!isCompleted) {
                    matched = true;
                    matchedLine = bestMatch.input;
                    matchedBarcode = scanned;

                    if (bestMatch.isVerifier) {
                        // In verifier mode, increment if verified_qty < picked_qty
                        if (bestMatch.val < bestMatch.pickedQty) {
                            bestMatch.input.val(bestMatch.val + 1);
                        }
                    } else {
                        // In picker mode, increment if within limits
                        // Only check stock limit if enable_stock == 1
                        if (bestMatch.enable_stock == 1) {
                            if (bestMatch.val < bestMatch.max && bestMatch.val < bestMatch.stock) {
                                bestMatch.input.val(bestMatch.val + 1);
                            }
                        } else {
                            // If stock is disabled, only check against ordered quantity
                            if (bestMatch.val < bestMatch.max) {
                                bestMatch.input.val(bestMatch.val + 1);
                            }
                        }
                    }
                } else {
                    toastr.warning('Barcode scanned but all matching lines are already completed');
                }
            }


            updateQuantityWithBarcode(scanned, matched ? (parseInt(matchedLine.val()) || 0) : 0);

            processNextPastedBarcode();
        }
        
        // Updated function to handle barcode-based quantity updates
        function updateQuantityWithBarcode(barcode, qty) {
            let url = "{{ route('pick.store') }}";
            const isVerifier = '{{ $isVerifier }}' === '1';
            if (isVerifier) {
                url += "?type=verifier";
            }

            $.post(url, {
                transaction_id: '{{ $data->id }}',
                picked_quantity: {
                    [barcode]: qty
                },
                type: 'barcode',
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {
                if (res.status) {
                    toastr.success(res.message);
                    // Update row status if we have updated_line data
                    if (res.updated_line && res.updated_line.length > 0) {
                        const updatedLine = res.updated_line[0];
                        const row = $(`tr[data-line-id="${updatedLine.line_id}"]`);
                        if (row.length) {
                            // Update the input value based on mode
                            const input = row.find('.inline-pick');
                            const qtyToSet = isVerifier ? updatedLine.verified_qty : updatedLine
                                .picked_qty;
                            input.val(qtyToSet);

                            // Update row data attributes with fresh values from backend
                            row.attr('data-ordered-qty', updatedLine.ordered_qty);
                            row.attr('data-picked-qty', updatedLine.picked_qty);
                            row.attr('data-is-picked', updatedLine.is_picked ? '1' : '0');

                            // Update row color and status
                            updateRowStatus(row);
                        }
                    }
                    // Refocus on barcode scanner input after successful operation
                    refocusBarcodeInput();
                } else {
                    if (res.type === 'wrong_order') {
                        // Create custom toastr container for product details
                        const toastrContainer = $('<div>').addClass('custom-toastr');

                        // Add product image if available
                        if (res.product.image) {
                            const imgContainer = $('<div>').addClass('product-image-container');
                            const img = $('<img>')
                                .attr('src', res.product.image)
                                .addClass('product-image')
                                .css({
                                    'width': '100px',
                                    'height': '100px',
                                    'object-fit': 'contain',
                                    'margin-bottom': '10px'
                                });
                            imgContainer.append(img);
                            toastrContainer.append(imgContainer);
                        }

                        // Add product details
                        const detailsContainer = $('<div>').addClass('product-details');
                        detailsContainer.append($('<div>').text(`Product: ${res.product.name}`));
                        detailsContainer.append($('<div>').text(`SKU: ${res.product.sku}`));
                        detailsContainer.append($('<div>').text(`Barcode: ${res.product.barcode}`));
                        detailsContainer.append($('<div>').text(
                            'This product does not belong to this order'));
                        toastrContainer.append(detailsContainer);

                        // Show custom toastr
                        toastr.error(toastrContainer, null, {
                            timeOut: 8000,
                            extendedTimeOut: 2000,
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-center",
                            preventDuplicates: true,
                            showDuration: "300",
                            hideDuration: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut"
                        });
                    } else if (res.type === 'invalid_barcode') {
                        toastr.error(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                    // Refocus on barcode scanner input after error
                    refocusBarcodeInput();
                }
            }).fail(function() {
                toastr.error("Failed to update quantity.");
                // Refocus on barcode scanner input after network error
                refocusBarcodeInput();
            });
        }
        // Original function for manual updates

        function updateQuantity(lineId, qty, type = 'manual') {
            let url = "{{ route('pick.store') }}";
            @if ($isVerifier)
                url += "?type=verifier";
            @endif


            $.post(url, {
                transaction_id: '{{ $data->id }}',
                picked_quantity: {
                    [lineId]: qty
                },
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {

                if (res.status) {
                    toastr.success(res.message);

                    if (res.updated_line && res.updated_line.length > 0) {
                        const updatedLine = res.updated_line[0];
                        const row = $(`tr[data-line-id="${updatedLine.line_id}"]`);
                        if (row.length) {
                            const input = row.find('.inline-pick');
                            const isVerifier = '{{ $isVerifier }}' === '1';
                            input.val(isVerifier ? updatedLine.verified_qty : updatedLine.picked_qty);
                            row.attr('data-ordered-qty', updatedLine.ordered_qty);
                            row.attr('data-picked-qty', updatedLine.picked_qty);
                            row.attr('data-is-picked', updatedLine.is_picked ? '1' : '0');
                            updateRowStatus(row);
                        }
                    }
                   
                } else {
                    toastr.error(res.message);
                }
            }).fail(function() {
                toastr.error("Failed to update quantity.");
            });
        }
        // Camera scanner functionality
        $('#start-camera-scan').on('click', function() {
            if (cameraContainer.is(':visible')) {
                stopCamera();
                return;
            }

            cameraContainer.show();
            startCamera();
        });

        function startCamera() {
            html5QrCode = new Html5Qrcode("camera-scanner");
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    html5QrCode = new Html5Qrcode("camera-scanner");
                    html5QrCode.start({
                                facingMode: "environment"
                            }, {
                                fps: 24,
                                qrbox: {
                                    width: 350,
                                    height: 350
                                },
                                formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128,
                                    Html5QrcodeSupportedFormats.EAN_13
                                ]
                            },
                            qrCodeMessage => {
                                inputField.val(qrCodeMessage).trigger('input');
                                stopCamera();
                            }
                        )
                        .catch(err => {
                            toastr.error("Unable to start scanner: " + err);
                        });
                }
            }).catch(err => {
                toastr.error("Camera not accessible: " + err);
            });
        }

        function stopCamera() {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                cameraContainer.hide();
            }).catch(err => {
                toastr.error("Error stopping camera: " + err);
            });
        }

        $('#save-picking').on('click', function() {
            const endTime = new Date();
            let url = "{{ route('pick.endTime') }}";
            @if ($isVerifier)
                url += "?type=verifier";
            @endif
            // const startTime = new Date('{{ $data->start_time }}');
            $.post(url, {
                transaction_id: '{{ $data->id }}',
                type: 'save',
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {
                if (res.status) {
                    const durationInSeconds = Math.round((endTime - pickingStartTime) / 1000);
                    let message = '';

                    if (durationInSeconds < 60) {
                        message = `${durationInSeconds} sec`;
                    } else if (durationInSeconds < 3600) {
                        const minutes = Math.floor(durationInSeconds / 60);
                        message = `${minutes} min`;
                    } else {
                        const hours = Math.floor(durationInSeconds / 3600);
                        const remainingMinutes = Math.floor((durationInSeconds % 3600) / 60);
                        message = `${hours} hr ${remainingMinutes} min`;
                    }

                    toastr.success(`Picking completed in ${message}`);

                    @if (auth()->user()->hasRole('Admin#' . request()->session()->get('user.business_id')))
                        pickingOrdersTable.ajax.reload();
                        $('#manual_pick_verify_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    @elseif (auth()->user()->can('pickerman'))
                        pickingOrdersTable.ajax.reload();
                        $('#manual_pick_verify').modal('hide');
                    @endif
                } else {
                    toastr.error(res.message);
                    // Refocus on barcode scanner input after error
                    refocusBarcodeInput();
                }
            }).fail(function() {
                toastr.error("Failed to save picking.");
                // Refocus on barcode scanner input after network error
                refocusBarcodeInput();
            });
        });
        $('#finish-picking').on('click', function() {
            const endTime = new Date();
            let url = "{{ route('pick.endTime') }}";
            @if ($isVerifier)
                url += "?type=verifier";
            @endif
            // const startTime = new Date('{{ $data->start_time }}');
            $.post(url, {
                transaction_id: '{{ $data->id }}',
                type: 'finish',
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {
                if (res.status) {
                    const durationInSeconds = Math.round((endTime - pickingStartTime) / 1000);
                    let message = '';

                    if (durationInSeconds < 60) {
                        message = `${durationInSeconds} seconds`;
                    } else if (durationInSeconds < 3600) {
                        const minutes = Math.floor(durationInSeconds / 60);
                        message = `${minutes} minutes`;
                    } else {
                        const hours = Math.floor(durationInSeconds / 3600);
                        const remainingMinutes = Math.floor((durationInSeconds % 3600) / 60);
                        message = `${hours} hours ${remainingMinutes} minutes`;
                    }

                    toastr.success(`Picking completed in ${message}`);

                    @if (auth()->user()->hasRole('Admin#' . request()->session()->get('user.business_id')))

                        $('#manual_pick_verify_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();

                        pickingOrdersTable.ajax.reload()
                    @elseif (auth()->user()->can('pickerman'))
                        currentTable.table().ajax.reload();
                        $('#manual_pick_verify').modal('hide');
                        pickingOrdersTable.ajax.reload()
                    @endif
                } else {
                    toastr.error(res.message);
                    // Refocus on barcode scanner input after error
                    refocusBarcodeInput();
                }
            }).fail(function() {
                toastr.error("Failed to finish picking.");
                // Refocus on barcode scanner input after network error
                refocusBarcodeInput();
            });
        });


        // Print Button Handler
        $('#print-picking').on('click', function() {
            var divToPrint = document.getElementById('manual_pick_verify');
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write('<html><head><title>Print Picking List</title>');
            newWin.document.write('<link rel="stylesheet" href="{{ asset('css/app.css') }}">');
            newWin.document.write('<style>');
            newWin.document.write(`
            @media print {
                body { margin: 0; }
                .no-print { display: none !important; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 8px; border: 1px solid #ddd; }
                th { background-color: #f8f9fa; }
                .tw-max-h-\\[60vh\\] { max-height: none !important; }
                .tw-overflow-y-auto { overflow: visible !important; }
                .print-header { display: flex !important; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 24px; }
                .print-header img { width: 128px !important; height: auto !important; }
            }
        `);
            newWin.document.write('</style>');
            newWin.document.write('</head><body>');

            // Only print the print-header and the main table/content
            newWin.document.write($('.print-header').html());

            // Print only the table/content, not the modal header
            var content = $(divToPrint).clone();
            content.find('.no-print').remove();
            // Remove the print-header from the content to avoid duplication
            content.find('.print-header').remove();
            newWin.document.write(content.html());

            newWin.document.write('</body></html>');
            newWin.document.close();

            newWin.onload = function() {
                if (typeof __currency_convert_recursively === 'function') {
                    __currency_convert_recursively($(newWin.document.body).find('table'));
                }
                newWin.print();
            };
        });

        // Prevent modal from closing on button click unless save/finish is successful
        $('#manual_pick_verify').on('hide.bs.modal', function(e) {
            if (!$(e.target).hasClass('modal-dialog')) {
                e.preventDefault();
                return false;
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Update mark as short button handler
        $(document).on('click', '.mark-short-btn', function() {
            const lineId = $(this).closest('td').data('line-id');
            const currentPickedQty = $(`input[data-line-id="${lineId}"]`).val();
            const row = $(this).closest('tr');

            swal({
                title: 'Mark as Short?',
                text: 'Are you sure you want to mark this line as shorted?',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: false,
                        visible: true,
                        className: "btn btn-default",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Mark as Short",
                        value: true,
                        visible: true,
                        className: "btn btn-danger",
                    }
                },
                dangerMode: true,
            }).then((willShort) => {
                if (willShort) {
                    $.post("{{ route('pick.store') }}", {
                        transaction_id: '{{ $data->id }}',
                        picked_quantity: {
                            [lineId]: currentPickedQty
                        },
                        type: 'shorted',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            if (res.updated_line && res.updated_line.length > 0) {
                                const updatedLine = res.updated_line[0];

                                row.attr('data-ordered-qty', updatedLine.ordered_qty);
                                row.attr('data-picked-qty', updatedLine.picked_qty);
                                row.attr('data-is-picked', updatedLine.is_picked ? '1' :
                                    '0');

                                updateRowStatus(row);
                                $('[data-toggle="tooltip"]').tooltip();
                            }
                            // Refocus on barcode scanner input after successful operation
                            refocusBarcodeInput();
                        } else {
                            toastr.error(res.message ||
                                'Failed to mark line as shorted');
                            // Refocus on barcode scanner input after error
                            refocusBarcodeInput();
                        }
                    }).fail(function() {
                        toastr.error("Failed to mark line as shorted.");
                        // Refocus on barcode scanner input after network error
                        refocusBarcodeInput();
                    });
                }
            });
        });

        // Add click handler for revert short button
        $(document).on('click', '.revert-short-btn', function() {
            const lineId = $(this).closest('td').data('line-id');
            const row = $(this).closest('tr');

            swal({
                title: 'Revert Short Status?',
                text: 'Are you sure you want to revert the shorted status of this line?',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: false,
                        visible: true,
                        className: "btn btn-default",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Revert Short",
                        value: true,
                        visible: true,
                        className: "btn btn-warning",
                    }
                },
                dangerMode: true,
            }).then((willRevert) => {
                if (willRevert) {
                    $.post("{{ route('pick.revert') }}", {
                        transaction_id: '{{ $data->id }}',
                        line_id: lineId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            if (res.updated_line && res.updated_line.length > 0) {
                                const updatedLine = res.updated_line[0];



                                row.attr('data-ordered-qty', updatedLine.ordered_qty);
                                row.attr('data-picked-qty', updatedLine.picked_qty);
                                row.attr('data-is-picked', updatedLine.is_picked ? '1' :
                                    '0');
                                updateRowStatus(row);
                                $('[data-toggle="tooltip"]').tooltip();
                            }
                            // Refocus on barcode scanner input after successful operation
                            refocusBarcodeInput();
                        } else {
                            toastr.error(res.message ||
                                'Failed to revert shorted status');
                            // Refocus on barcode scanner input after error
                            refocusBarcodeInput();
                        }
                    }).fail(function() {
                        toastr.error("Failed to revert shorted status.");
                        // Refocus on barcode scanner input after network error
                        refocusBarcodeInput();
                    });
                }
            });
        });

        $(document).on('change', '.mark-short-checkbox', function() {
            const checkbox = $(this);
            const lineId = checkbox.closest('tr').data('line-id');
            const row = checkbox.closest('tr');
            const currentVerifyQty = row.find('.inline-pick').val();
            const isVerifier = '{{ $isVerifier }}' === '1';

            if (!isVerifier) return; // Run only in verifier mode

            if (checkbox.is(':checked')) {
                // Checkbox checked: Mark as short
                swal({
                    title: 'Mark as Short?',
                    text: 'Are you sure you want to mark this line as shorted?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Cancel',
                            value: false,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes, Mark as Short',
                            value: true,
                            visible: true,
                            className: 'btn btn-danger',
                        }
                    },
                    dangerMode: true,
                }).then((willShort) => {
                    if (willShort) {
                        $.post("{{ route('pick.store') }}?type=verifier", {
                            transaction_id: '{{ $data->id }}',
                            picked_quantity: {
                                [lineId]: currentVerifyQty
                            },
                            type: 'markVerified',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }, function(res) {
                            if (res.status) {
                                toastr.success(res.message);
                                if (res.updated_line && res.updated_line.length > 0) {
                                    const updatedLine = res.updated_line[0];
                                    row.attr('data-ordered-qty', updatedLine
                                        .ordered_qty);
                                    row.attr('data-picked-qty', updatedLine.picked_qty);
                                    row.attr('data-is-picked', updatedLine.is_picked ?
                                        '1' : '0');
                                    updateRowStatus(row);
                                    $('[data-toggle="tooltip"]').tooltip();
                                }
                                // Refocus on barcode scanner input after successful operation
                                refocusBarcodeInput();
                            } else {
                                toastr.error(res.message ||
                                    'Failed to mark line as shorted');
                                checkbox.prop('checked',
                                    false); // Revert checkbox on failure
                                // Refocus on barcode scanner input after error
                                refocusBarcodeInput();
                            }
                        }).fail(function() {
                            toastr.error('Failed to mark line as shorted.');
                            checkbox.prop('checked',
                                false); // Revert checkbox on failure
                            // Refocus on barcode scanner input after network error
                            refocusBarcodeInput();
                        });
                    } else {
                        checkbox.prop('checked', false); // Revert checkbox if canceled
                    }
                });
            } else {
                // Checkbox unchecked: Revert short status
                swal({
                    title: 'Revert Short Status?',
                    text: 'Are you sure you want to revert the shorted status of this line?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Cancel',
                            value: false,
                            visible: true,
                            className: 'btn btn-default',
                            closeModal: true,
                        },
                        confirm: {
                            text: 'Yes, Revert Short',
                            value: true,
                            visible: true,
                            className: 'btn btn-warning',
                        }
                    },
                    dangerMode: true,
                }).then((willRevert) => {
                    if (willRevert) {
                        $.post("{{ route('pick.revert') }}?type=verifier", {
                            transaction_id: '{{ $data->id }}',
                            line_id: lineId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }, function(res) {
                            if (res.status) {
                                toastr.success(res.message);
                                if (res.updated_line && res.updated_line.length > 0) {
                                    const updatedLine = res.updated_line[0];
                                    row.attr('data-ordered-qty', updatedLine
                                        .ordered_qty);
                                    row.attr('data-picked-qty', updatedLine.picked_qty);
                                    row.attr('data-is-picked', updatedLine.is_picked ?
                                        '1' : '0');
                                    updateRowStatus(row);
                                    $('[data-toggle="tooltip"]').tooltip();
                                }
                                // Refocus on barcode scanner input after successful operation
                                refocusBarcodeInput();
                            } else {
                                toastr.error(res.message ||
                                    'Failed to revert shorted status');
                                checkbox.prop('checked',
                                    true); // Revert checkbox on failure
                                // Refocus on barcode scanner input after error
                                refocusBarcodeInput();
                            }
                        }).fail(function() {
                            toastr.error('Failed to revert shorted status.');
                            checkbox.prop('checked',
                                true); // Revert checkbox on failure
                            // Refocus on barcode scanner input after network error
                            refocusBarcodeInput();
                        });
                    } else {
                        checkbox.prop('checked', true); // Revert checkbox if canceled
                    }
                });
            }
        });


        // Global Mark as Short button handler
        $('#global-mark-short').on('click', function() {
            const pickedQuantities = {};
            let hasRemainingItems = false;

            // Collect all items with remaining quantities
            $('.quantity-row').each(function() {
                const row = $(this);
                const lineId = row.data('line-id');
                const orderedQty = parseInt(row.attr('data-ordered-qty'));
                const pickedQty = parseInt(row.find('.inline-pick').val() || 0);
                const stock = parseInt(row.find('.inline-pick').data('stock') || 0);
                const enable_stock = parseInt(row.find('.inline-pick').data('enable-stock') || 1);

                if (pickedQty < orderedQty) {
                    hasRemainingItems = true;
                    // If stock is disabled, use picked quantity. Otherwise, if we have stock, use the picked quantity, otherwise use 0
                    if (enable_stock == 0) {
                        pickedQuantities[lineId] = pickedQty;
                    } else {
                        pickedQuantities[lineId] = stock > 0 ? pickedQty : 0;
                    }
                }
            });

            if (!hasRemainingItems) {
                toastr.warning('No items have remaining quantities to mark as short.');
                return;
            }

            swal({
                title: 'Mark All Remaining as Short?',
                text: 'Are you sure you want to mark all remaining quantities as shorted?',
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: false,
                        visible: true,
                        className: "btn btn-default",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Mark All Short",
                        value: true,
                        visible: true,
                        className: "btn btn-danger",
                    }
                },
                dangerMode: true,
            }).then((willShort) => {
                if (willShort) {
                    $.post("{{ route('pick.store') }}", {
                        transaction_id: '{{ $data->id }}',
                        picked_quantity: pickedQuantities,
                        type: 'shorted',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function(res) {
                        if (res.status) {
                            toastr.success(res.message);
                            if (res.updated_line && res.updated_line.length > 0) {
                                res.updated_line.forEach(updatedLine => {
                                    const row = $(
                                        `tr[data-line-id="${updatedLine.line_id}"]`
                                    );
                                    if (row.length) {
                                        row.attr('data-ordered-qty', updatedLine
                                            .ordered_qty);
                                        row.attr('data-picked-qty', updatedLine
                                            .picked_qty);
                                        row.attr('data-is-picked', updatedLine
                                            .is_picked ? '1' : '0');
                                        updateRowStatus(row);
                                    }
                                });
                                $('[data-toggle="tooltip"]').tooltip();
                            }
                            // Refocus on barcode scanner input after successful operation
                            refocusBarcodeInput();
                        } else {
                            toastr.error(res.message ||
                                'Failed to mark items as shorted');
                            // Refocus on barcode scanner input after error
                            refocusBarcodeInput();
                        }
                    }).fail(function() {
                        toastr.error("Failed to mark items as shorted.");
                        // Refocus on barcode scanner input after network error
                        refocusBarcodeInput();
                    });
                }
            });
        });
    });
</script>

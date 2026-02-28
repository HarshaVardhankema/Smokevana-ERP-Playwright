@extends('layouts.app')
@section('title', 'Order Fulfillment')

@section('content')
    <section class="content-header" style="position: relative;">
        <button type="button" class="close" id="close-picking" aria-label="Close" style="position: absolute; top: 10px; right: 10px; width: 35px; height: 35px; font-size: 1.5rem; z-index: 10; background: #dc3545; border: none; border-radius: 50%; cursor: pointer; color: #fff; display: flex; align-items: center; justify-content: center; padding: 0; line-height: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <span aria-hidden="true" style="color: #fff; font-weight: bold;">&times;</span>
        </button>
        <div class="tw-flex tw-justify-between tw-items-center">
                @php
                    $type = request('type'); // or request()->get('type')
                    $heading = $type === 'verifier' ? 'Verify' : 'Pick';
                    $buttonText = $type === 'verifier' ? 'Verification' : 'Picking';
                @endphp

                <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
                    {{ $heading }} Sales Orders
                </h1>
                <div class="tw-flex tw-justify-end tw-mt-4 tw-gap-2">
                    <button class="btn btn-primary" id="save-picking">Save {{ $buttonText }}</button>
                    <button class="btn btn-success" id="finish-picking">Finish {{ $buttonText }}</button>
                </div>
        </div>
    </section>

    <section class="content">
        <style>
            .progress {
                background-color: #d6d6d6;
                border-radius: 10px;
                height: 20px;
                width: 100%;
                overflow: hidden;
                box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.15);
            }

            .progress-bar {
                height: 100%;
                border-radius: 10px;
                text-align: center;
                font-weight: 600;
                font-size: 12px;
                color: #fff;
                /* line-height: 20px; */
                background-image: linear-gradient(270deg, #c8ff00, #0072ff, #d9ff00);
                background-size: 200% 100%;
                background-position: 100% 0;
                animation: barFlow 3s linear infinite;
                transition: width 0.5s ease-in-out;
            }

            @keyframes barFlow {
                0% {
                    background-position: 100% 0;
                }

                100% {
                    background-position: 0 0;
                }
            }

            .qty-input-group {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .qty-btn {
                padding: 2px 8px;
                margin: 0 4px;
                border: 1px solid #ddd;
                background: #f8f9fa;
                cursor: pointer;
            }

            .qty-btn:hover {
                background: #e9ecef;
            }

            .inline-pick {
                width: 60px !important;
                text-align: center;
            }

            .close:hover {
                background: #c82333 !important;
                transform: scale(1.1);
                transition: all 0.2s ease;
            }
        </style>
        @component('components.widget', ['class' => 'box-primary',])
        @slot('tool')
        <div class="box-tools">
            <div class="tw-flex tw-flex-col md:tw-flex-row tw-gap-4 tw-items-center tw-w-full">

                {{-- Progress Bar --}}
                <div class="tw-w-full md:tw-w-1/3">
                    <div class="progress tw-h-6">
                        <div class="progress-bar tw-text-sm tw-font-medium tw-text-white" role="progressbar"
                            style="width: {{ number_format($fulfilledPercentage, 2) }}%;"
                            aria-valuenow="{{ number_format($fulfilledPercentage, 2) }}" aria-valuemin="0"
                            aria-valuemax="100">
                            {{ number_format($fulfilledPercentage, 2) }}%
                        </div>
                    </div>
                </div>

                {{-- Hidden Transaction ID --}}
                <input type="hidden" name="transaction_id" value="{{ $pickingOrders->id }}" />

                {{-- Barcode Input and Camera Button --}}
                <div class="tw-flex tw-w-full md:tw-w-2/3 tw-items-center tw-gap-2 tw-flex-wrap">
                    <input type="text" id="barcode_scanner_input" placeholder="Scan barcode"
                        class="form-control tw-flex-1 tw-min-w-[200px]" />

                    <button class="btn btn-secondary tw-flex tw-items-center tw-justify-center tw-px-4 tw-py-2 tw-text-lg"
                        id="start-camera-scan" type="button">
                        <i class="fas fa-camera tw-text-xl"></i>
                    </button>
                </div>


            </div>
        </div>
        @endslot
        {{-- Camera View --}}
        <div id="camera-scanner" class="tw-w-full" style="display:none; margin-bottom: 20px;"></div>

        <div class="table-responsive">
            <table id="picking-table" class="table table-bordered nowrap">
                <thead>
                    <tr>

                        <th>Product</th>
                        <th>SKU</th>
                        <th>Barcode</th>
                        <th>Stock</th>
                        <th>Ordered</th>
                        <th>Picked</th>
                    </tr>
                </thead>
            </table>
        </div>
        @endcomponent
    </section>

    @section('javascript')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

        <script>
            $(document).ready(function () {

                let pickingStarted = false;
                let pickingStartTime = null;
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get('type');
                function markPickingStartIfNeeded() {
                    if (!pickingStarted) {
                        pickingStarted = true;
                        pickingStartTime = new Date();

                        $.post('{{ route('pick.startTime') }}', {
                            transaction_id: '{{ $pickingOrders->id }}',
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

                $('#close-picking').on('click', function () {
                    @if(auth()->user()->hasRole('Admin#' . request()->session()->get('user.business_id')))
                        window.location.href = '/order-fulfillment';
                    @elseif(auth()->user()->can('pickerman'))
                        window.location.href = '/order-fulfillment-picker';
                    @else
                        window.location.href = '/order-fulfillment';
                    @endif
                });

                $('#save-picking').on('click', function () {
                    const endTime = new Date();
                    $.post('{{ route('pick.endTime') }}', {
                        transaction_id: '{{ $pickingOrders->id }}',
                        type:'save',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function (res) {
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

                            @if(auth()->user()->hasRole('Admin#' . request()->session()->get('user.business_id')))
                                window.location.href = '/order-fulfillment';
                            @elseif(auth()->user()->can('pickerman'))
                                window.location.href = '/order-fulfillment-picker';
                            @endif
                        }
                    });
                });
                $('#finish-picking').on('click', function () {
                    const endTime = new Date();
                    $.post('{{ route('pick.endTime') }}', {
                        transaction_id: '{{ $pickingOrders->id }}',
                        type:'finish',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function (res) {
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

                            @if(auth()->user()->hasRole('Admin#' . request()->session()->get('user.business_id')))
                                window.location.href = '/order-fulfillment';
                            @elseif(auth()->user()->can('pickerman'))
                                window.location.href = '/order-fulfillment-picker';
                            @endif
                        }
                    });
                });


                const transactionId = '{{ $pickingOrders->id }}';
                let lastKeyPressTime = 0;
                const DELAY_BETWEEN_KEYPRESSES = 1; // 500ms delay
                let qtyChangeTimer = null;
                
                const pickingTable = $('#picking-table').DataTable({
                    processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    serverSide: true,
                    ajax: `/sells-picking/${transactionId}?type=${type}`,
                    columns: [
                        { data: 'product_name', name: 'product_name' },
                        { data: 'sku', name: 'sku' },
                        { data: 'barcode', name: 'barcode' },
                        { data: 'in_hand_stock', name: 'in_hand_stock' },
                        { data: 'ordered_quantity', name: 'ordered_quantity' },
                        {
                            data: 'picked_quantity_input',
                            name: 'picked_quantity_input',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                return `<div class="qty-input-group">
                                                        <button type="button" class="qty-btn minus-btn">-</button>
                                                        ${data}
                                                        <button type="button" class="qty-btn plus-btn">+</button>
                                                    </div>`;
                            }
                        }
                    ],
                    initComplete: function () {
                        pickingTable.on('xhr', function () {
                            const json = pickingTable.ajax.json();
                            const fulfilled = json?.fulfilled_percentage ?? 0;
                            $('.progress-bar')
                                .css('width', fulfilled + '%')
                                .text(fulfilled.toFixed(2) + '%');
                        });
                        markPickingStartIfNeeded();
                    }
                });

                $(document).on('keydown', function (e) {
                    const focusedInput = $('.inline-pick:focus');
                    if (!focusedInput.length) return;

                    const currentTime = new Date().getTime();
                    if (currentTime - lastKeyPressTime < DELAY_BETWEEN_KEYPRESSES) {
                        return;
                    }
                    lastKeyPressTime = currentTime;

                    if (e.keyCode === 37) { // Left arrow key
                        let value = parseInt(focusedInput.val()) || 0;
                        if (value > 0) {
                            focusedInput.val(value - 1);
                            clearTimeout(qtyChangeTimer);
                            qtyChangeTimer = setTimeout(() => {
                                focusedInput.trigger('change');
                            }, 1000);
                        }
                    } else if (e.keyCode === 39) { // Right arrow key
                        let value = parseInt(focusedInput.val()) || 0;
                        const max = parseInt(focusedInput.data('max'));
                        const stock = parseInt(focusedInput.data('stock'));
                        const enable_stock = parseInt(focusedInput.data('enable-stock') || 1);

                        // Only check stock limit if enable_stock == 1
                        if (enable_stock == 1) {
                            if (value < max && value < stock) {
                                focusedInput.val(value + 1);
                                clearTimeout(qtyChangeTimer);
                                qtyChangeTimer = setTimeout(() => {
                                    focusedInput.trigger('change');
                                }, 1000);
                            } else {
                                toastr.warning('Limit reached');
                            }
                        } else {
                            // If stock is disabled, only check against ordered quantity
                            if (value < max) {
                                focusedInput.val(value + 1);
                                clearTimeout(qtyChangeTimer);
                                qtyChangeTimer = setTimeout(() => {
                                    focusedInput.trigger('change');
                                }, 1000);
                            } else {
                                toastr.warning('Limit reached');
                            }
                        }
                    }
                });

                let lastButtonClickTime = 0;
                const DELAY_BETWEEN_CLICKS = 500; // 500ms delay

                $('#picking-table').on('click', '.minus-btn', function () {
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
                        }, 1000);
                    }
                });

                $('#picking-table').on('click', '.plus-btn', function () {
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
                            }, 1000);
                        } else {
                            toastr.warning('Limit reached');
                        }
                    }
                });

                $('#picking-table').on('change', '.inline-pick', function () {
                    const input = $(this);
                    const lineId = input.data('line-id');
                    let quantity = parseInt(input.val()) || 0;
                    const max = parseInt(input.data('max'));
                    const stock = parseInt(input.data('stock'));
                    const enable_stock = parseInt(input.data('enable-stock') || 1);

                    // Only validate against stock if enable_stock == 1
                    if (enable_stock == 1) {
                        if (quantity > max || quantity > stock) {
                            toastr.error("Picked quantity exceeds ordered or stock.");
                            input.val(Math.min(max, stock));
                            clearTimeout(qtyChangeTimer);
                            qtyChangeTimer = setTimeout(() => {
                                input.trigger('change');
                            }, 2000);
                            return;
                        }
                    } else {
                        // If stock is disabled, only check against ordered quantity
                        if (quantity > max) {
                            toastr.error("Picked quantity exceeds ordered quantity.");
                            input.val(max);
                            clearTimeout(qtyChangeTimer);
                            qtyChangeTimer = setTimeout(() => {
                                input.trigger('change');
                            }, 2000);
                            return;
                        }
                    }

                    updatePickedQuantity(lineId, quantity);
                });

                let barcodeTimer = null;
                let pasteQueue = [];
                let isProcessingPaste = false;

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

                    $('.inline-pick').each(function () {
                        const input = $(this);
                        const barcode = String(input.data('barcode'));
                        const sku = String(input.data('sub-sku'));
                        let val = parseInt(input.val()) || 0;
                        const max = parseInt(input.data('max'));
                        const stock = parseInt(input.data('stock'));
                        const enable_stock = parseInt(input.data('enable-stock') || 1);

                        if (scanned === barcode || scanned === sku) {
                            matched = true;
                            // Only check stock limit if enable_stock == 1
                            if (enable_stock == 1) {
                                if (val < max && val < stock) {
                                    val++;
                                    input.val(val).trigger('change');
                                    toastr.success(`+1 picked for ${scanned}`);
                                } else {
                                    toastr.warning('Limit reached');
                                }
                            } else {
                                // If stock is disabled, only check against ordered quantity
                                if (val < max) {
                                    val++;
                                    input.val(val).trigger('change');
                                    toastr.success(`+1 picked for ${scanned}`);
                                } else {
                                    toastr.warning('Limit reached');
                                }
                            }
                            return false; // break
                        }
                    });

                    if (!matched) {
                        toastr.error(`Barcode "${scanned}" not found`);
                    }

                    processNextPastedBarcode();
                }

                $('#barcode_scanner_input').on('input', function () {
                    clearTimeout(barcodeTimer);
                    const inputEl = $(this);

                    barcodeTimer = setTimeout(() => {
                        const scanned = inputEl.val().trim();
                        if (!scanned) return;

                        let matched = false;

                        $('.inline-pick').each(function () {
                            const input = $(this);
                            const barcode = String(input.data('barcode'));
                            const sku = String(input.data('sub-sku'));
                            let val = parseInt(input.val()) || 0;
                            const max = parseInt(input.data('max'));
                            const stock = parseInt(input.data('stock'));
                            const enable_stock = parseInt(input.data('enable-stock') || 1);

                            if (scanned === barcode || scanned === sku) {
                                matched = true;
                                // Only check stock limit if enable_stock == 1
                                if (enable_stock == 1) {
                                    if (val < max && val < stock) {
                                        val++;
                                        input.val(val).trigger('change');
                                        toastr.success(`+1 picked for ${scanned}`);
                                    } else {
                                        toastr.warning('Limit reached');
                                    }
                                } else {
                                    // If stock is disabled, only check against ordered quantity
                                    if (val < max) {
                                        val++;
                                        input.val(val).trigger('change');
                                        toastr.success(`+1 picked for ${scanned}`);
                                    } else {
                                        toastr.warning('Limit reached');
                                    }
                                }
                                return false;
                            }
                        });

                        if (!matched) {
                            toastr.error(`Barcode "${scanned}" not found`);
                        }

                        inputEl.val('');
                    }, 100); // debounce
                });

                $('#barcode_scanner_input').on('paste', function (e) {
                    const clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
                    const pastedText = clipboardData.getData('text');

                    // Support barcodes separated by newlines, tabs, commas, or spaces
                    const entries = pastedText.split(/[\r\n\t, ]+/).filter(Boolean);
                    pasteQueue.push(...entries);

                    $(this).val('');

                    if (!isProcessingPaste) {
                        processNextPastedBarcode();
                    }

                    e.preventDefault(); // Prevent default paste handling
                });


                function updatePickedQuantity(lineId, qty) {
                    markPickingStartIfNeeded();
                    $.post("{{ route('pick.store') }}", {
                        transaction_id: '{{ $pickingOrders->id }}',
                        picked_quantity: { [lineId]: qty },
                        type: type,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function (res) {
                        if (res.status) {
                            // pickingTable.ajax.reload(null, false);
                            toastr.success(res.message);
                            const fulfilled = res.fulfilled_percentage ?? 0;
                            $('.progress-bar')
                                .css('width', fulfilled + '%')
                                .text(fulfilled.toFixed(2) + '%');
                        } else {
                            toastr.error(res.message);
                        }
                    }).fail(function () {
                        toastr.error("Failed to update picked quantity.");
                    });
                }
                $('#edit_pos_sell_form').on('submit', function (e) {
                    e.preventDefault();
                    toastr.info("Changes are already being saved automatically.");
                });
                var html5QrCode;
                const cameraContainer = $('#camera-scanner');
                const inputField = $('#barcode_scanner_input');

                $('#start-camera-scan').on('click', function () {
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
                            html5QrCode.start(
                                { facingMode: "environment" },
                                {
                                    fps: 24,
                                    qrbox: { width: 350, height: 350 },
                                    formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.EAN_13]
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

            });

        </script>

    @endsection
@endsection
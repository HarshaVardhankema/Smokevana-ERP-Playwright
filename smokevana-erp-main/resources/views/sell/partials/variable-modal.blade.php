<div class="modal-dialog modal-lg no-print" id="metrix_modal" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="modalTitle">{{ $product->name }}</h4>
        </div>

        @php
            $overSellingQuantity = session()->get('business.overselling_qty_limit');
            $pos_settings = session()->get('business.pos_settings');
            $isOverSelling = json_decode($pos_settings)->allow_overselling ?? '';
        @endphp

        <div class="modal-body">
            <input type="text" name='matrix_product_id' value={{ $product->id }} class="hide" id="matrix_product_id">
            <div class="row">
                <div class="col-sm-9">
                    <div class="col-sm-4 invoice-col">
                        <b>@lang('product.sku'):</b> {{ $product->sku }}<br>
                        @if ($product->ml)
                            <b>Tax Values:</b>
                            ML: {{ $product->ml }}
                            @if ($product->ct)
                                & CT: {{ $product->ct }}
                            @endif
                            @if ($product->ct)
                                & type
                                {{-- {{$product->locationTaxType}} --}}
                            @endif
                            <br>
                        @endif
                        <b>@lang('product.brand'):</b> {{ $product->brand->name ?? '--' }}<br>
                        <b>@lang('product.unit'):</b> {{ $product->unit->short_name ?? '--' }}<br>
                        <b>@lang('product.barcode_type'):</b> {{ $product->barcode_type ?? '--' }}

                        @php
                            $custom_labels = json_decode(session('business.custom_labels'), true);
                        @endphp

                        @for ($i = 1; $i <= 20; $i++)
                            @php
                                $db_field = 'product_custom_field' . $i;
                                $label = 'custom_field_' . $i;
                            @endphp

                            @if (!empty($product->$db_field))
                                <br>
                                <b>{{ $custom_labels['product'][$label] ?? '' }}:</b>
                                {{ $product->$db_field }}
                            @endif
                        @endfor

                        <br>
                        <strong>@lang('lang_v1.available_in_locations'):</strong>
                        @if (count($product->product_locations) > 0)
                            {{ implode(', ', $product->product_locations->pluck('name')->toArray()) }}
                        @else
                            @lang('lang_v1.none')
                        @endif

                        @if (!empty($product->media->first()))
                            <br>
                            <strong>@lang('lang_v1.product_brochure'):</strong>
                            <a href="{{ $product->media->first()->display_url }}"
                                download="{{ $product->media->first()->display_name }}">
                                <span class="label label-info">
                                    <i class="fas fa-download"></i>
                                    {{ $product->media->first()->display_name }}
                                </span>
                            </a>
                        @endif
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>@lang('product.category'):</b> {{ $product->category->name ?? '--' }}<br>
                        <b>@lang('product.sub_category'):</b> {{ $product->sub_category->name ?? '--' }}<br>
                        <b>@lang('product.web_category'):</b>
                        @if (!empty($product->webcategories))
                            @foreach ($product->webcategories as $category)
                                {{ $category['name'] }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        @else
                            --
                        @endif
                        <br>
                        <b>@lang('product.manage_stock'):</b>
                        @if ($product->enable_stock)
                            @lang('messages.yes')
                        @else
                            @lang('messages.no')
                        @endif
                        <br>
                        @if ($product->enable_stock)
                            <b>@lang('product.alert_quantity'):</b> {{$product->alert_quantity ?? '--' }}
                        @endif

                        @if (!empty($product->warranty))
                            <br>
                            <b>@lang('lang_v1.warranty'):</b> {{ $product->warranty->display_name }}
                        @endif
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>@lang('product.expires_in'):</b>
                        @php
                            $expiry_array = [
                                'months' => __('product.months'),
                                'days' => __('product.days'),
                                '' => __('product.not_applicable'),
                            ];
                        @endphp
                        @if (!empty($product->expiry_period) && !empty($product->expiry_period_type))
                            {{ $product->expiry_period }} {{ $expiry_array[$product->expiry_period_type] }}
                        @else
                            {{ $expiry_array[''] }}
                        @endif
                        <br>
                        @if ($product->weight)
                            <b>@lang('lang_v1.weight'):</b> {{ $product->weight }}<br>
                        @endif
                        <b>@lang('product.applicable_tax'):</b>
                        {{ $product->product_tax->name ?? __('lang_v1.none') }}<br>
                        @php
                            $tax_type = [
                                'inclusive' => __('product.inclusive'),
                                'exclusive' => __('product.exclusive'),
                            ];
                        @endphp
                        <b>@lang('product.selling_price_tax_type'):</b> {{ $tax_type[$product->tax_type] }}<br>
                        <b>@lang('product.product_type'):</b> @lang('lang_v1.' . $product->type)
                    </div>

                    <div class="col-sm-2 hide">
                        <label for="price_level">Price Level</label>
                        <select class="form-control" id="price_level">
                            <option value="" disabled selected>Select Price Level</option>
                            <option value="1">Silver Selling Price</option>
                            <option value="2">Gold Selling Price</option>
                            <option value="3">Platinum Selling Price</option>
                            <option value="4">Lowest Selling Price</option>
                            <option value="5">Diamond Selling Price</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>
                    <br>
                </div>

                <div class="col-sm-2 col-md-2 invoice-col tw-m-0 tw-p-0">
                    <div class="tw-w-32 tw-h-32 tw-overflow-hidden tw-flex tw-items-center tw-justify-center">
                        <img src="{{ $product->image_url }}" class="tw-w-full tw-h-full tw-object-contain"
                            alt="Product image">
                    </div>
                </div>
            </div>

            <div class="row tw-m-0 tw-p-0">
                <div class="col-md-12">
                    <h4>@lang('product.variations'):</h4>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table bg-gray">
                            <thead class="bg-green" style="position: sticky; top: 0;">
                                <tr>
                                    <th>@lang('product.sku')/@lang('product.barcode_no')</th>
                                    <th>@lang('product.variations')</th>
                                    <th>@lang('product.price')</th>
                                    <th>@lang('stock_adjustment.available_stock')</th>
                                    <th>@lang('product.quantity_needed')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->variations as $variation)
                                    <tr data-variation-id="{{ $variation->id }}">
                                        <td>
                                            {{ $variation->sub_sku }}<br>
                                            {{ $variation->var_barcode_no }}
                                        </td>
                                        <td>{{ $variation->name }}</td>
                                        <td class="price-column tw-w-10 tw-text-center">
                                            @php
                                                $default_price = collect($variation->group_prices)
                                                    ->where('price_group_id', 1)
                                                    ->first();
                                            @endphp
                                            <span class="display_currency price-value"
                                                data-variation-id="{{ $variation->id }}">
                                                {{ $default_price ? number_format($default_price->price_inc_tax, 2) : number_format($variation->sell_price_inc_tax, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span data-is_quantity="true" class="display_currency"
                                                data-currency_symbol="false">
                                                {{ (int) $variation->in_hand_qty }}
                                            </span> Pc(s)
                                        </td>
                                        <td tw-w-24 tw-text-center>
                                            @php
                                                $max_qty_value = $variation->in_hand_qty;
                                                if ($isOverSelling) {
                                                    if ($overSellingQuantity) {
                                                        $max_qty_value = $variation->in_hand_qty + $overSellingQuantity;
                                                    } else {
                                                        $max_qty_value = $variation->in_hand_qty + 5000;
                                                    }
                                                } else {
                                                    $max_qty_value = $variation->in_hand_qty;
                                                }
                                            @endphp
                                            @if (empty($is_purchase))
                                                <input type="number"
                                                    class="quantity-input tw-w-12 tw-text-center tw-mx-1 tw-border tw-rounded tw-text-xs form-control"
                                                    value="" min="0" @if ($isOverSelling) max="{{ intval($max_qty_value) }}"
                                                    data-max="{{ intval($max_qty_value) }}" @else
                                                    max="{{ intval($max_qty_value) }}" data-max="{{ intval($max_qty_value) }}"
                                                    @endif>
                                            @else
                                                <input type="number"
                                                    class="quantity-input tw-w-12 tw-text-center tw-mx-1 tw-border tw-rounded tw-text-xs form-control"
                                                    value=""  max="{{ intval(999999999999999999) }}" data-max="{{ intval(999999999999999999) }}">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <span class="tw-text-sm tw-font-semibold">
                Total Qty: <span id="totalQty">0</span>
            </span>
            <button type="button" id="save_button_metrix" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print"
                aria-label="Print">
                Add
            </button>
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var prices = @json($product->variations->pluck('group_prices', 'id'));
        var initialPriceGroupId = {{ $priceGroupId }};

        function updateTotalQty() {
            let total = 0;
            $('.quantity-input').each(function () {
                let val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    total += val;
                }
            });
            $('#totalQty').text(total);
        }

        $(document).on('input', '.quantity-input', function () {
            updateTotalQty();
        });

        $('.modal').on('shown.bs.modal', function () {
            updateTotalQty();
        });

        $(document).on('focus', 'input[type=number]', function () {
            $(this).on('wheel.disableScroll', function (e) {
                e.preventDefault();
            });
        });

        $(document).on('blur', 'input[type=number]', function () {
            $(this).off('wheel.disableScroll');
        });

        function updatePrices() {
            $('.price-value').each(function () {
                var variationId = $(this).data('variation-id');
                var priceColumn = $(this);

                if (prices[variationId]) {
                    var selectedPrice = prices[variationId].find(price => price.price_group_id == initialPriceGroupId);
                    if (selectedPrice) {
                        priceColumn.text(parseFloat(selectedPrice.price_inc_tax).toFixed(2));
                    }
                }
            });
        }

        updatePrices();

        $('#price_level').on('change', function () {
            initialPriceGroupId = $(this).val();
            updatePrices();
        });

        function showErrorInTd($input, message) {
            $input.css('border', '1px solid red');
            let $td = $input.closest('td');

            let $error = $td.find('.error-message');
            if ($error.length === 0) {
                $error = $('<div class="error-message"></div>').css({
                    'color': 'red',
                    'font-size': '12px',
                    'margin-top': '2px',
                    'display': 'block'
                });
                $td.append($error);
            }

            $error.text(message).css('display', 'block');
            $input.focus();
        }

        function clearErrorInTd($input) {
            $input.css('border', '');
            $input.closest('td').find('.error-message').css('display', 'none');
        }

        $('.quantity-input').on('input', function () {
            let $input = $(this);
            let maxVal = parseFloat($input.data('max')) || 0;
            let currentVal = parseFloat($input.val()) || 0;

            if (currentVal > maxVal) {
                showErrorInTd($input, `Quantity cannot exceed ${maxVal}`);
            } else if (currentVal < 0) {
                showErrorInTd($input, 'Quantity cannot be negative');
            } else {
                clearErrorInTd($input);
            }
        });

        $(".price-value").on("dblclick", function () {
            let $span = $(this);
            let currentValue = $span.text().trim();
            let input = $("<input>", {
                type: "number",
                value: currentValue,
                class: "form-control tw-border tw-px-1 tw-py-0.5 tw-w-16 tw-text-sm tw-text-center"
            });

            $span.hide().after(input);
            input.focus();

            input.on("blur keypress", function (event) {
                if (event.type === "blur" || (event.type === "keypress" && event.key === "Enter")) {
                    let newValue = input.val().trim();
                    if (newValue !== "") {
                        $span.text(newValue);
                    }
                    input.remove();
                    $span.show();
                }
            });
        });
    });
</script>
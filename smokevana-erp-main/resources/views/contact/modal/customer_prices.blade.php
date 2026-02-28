<style type="text/css">
    .modal {
        overflow-y: auto;
    }

    .price-input {
        width: 100px;
        display: inline-block;
    }

    .price-update-btn {
        visibility: hidden;
        opacity: 0;
        margin-left: 5px;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    .price-update-container:hover .price-update-btn,
    .price-input:focus+.price-update-btn {
        visibility: visible;
        opacity: 1;
    }

    .price-update-container {
        position: relative;
        display: inline-block;
    }

    .ui-menu {
        z-index: 9999;
    }
</style>
<div class="modal-dialog no-print  modal-xl" id='metrix_modal' role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">Customer Prices</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                    <div class="table-responsive">
                        {{-- {{$contact_id}} --}}
                        <table class="table table-bordered" id="customer_pricerecll_table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select_all">
                                    </th>
                                    <th>Image</th>
                                    <th>Product/Variation</th>
                                    <th>Price <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top"
                                            title="Price Incl. Tax"></i></th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Last Price</th>
                                    <th>Price Recall</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if ($priceRecalls)
                            
                                @foreach ($priceRecalls as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="row-select" value="{{ $product['key'] }}">
                                    </td>
                                    <td><img src="{{ $product['product_image'] }}" alt="Product Image"
                                            style="width: 50px; height: 50px;"></td>
                                    <td>{{ $product['product_name'] }}<br><b>{{ $product['variation_name'] }}</b>
                                        <small>{{
                                            $product['sku'] }}</small>
                                    </td>
                                    <td>{{ $product['product_price_with_tax'] }} <i class="fa fa-info-circle"
                                            data-toggle="tooltip" data-placement="top"
                                            title="{{ $product['product_price'] }} without tax"></i></td>
                                    <td>{{ $product['recall_createdBy'] }}</td>
                                    <td>{{ $product['recall_createdAt'] }}</td>
                                    <td>{{ $product['old_recall_price'] }} </td>
                                    <td>
                                        @if ($product['has_price_recall'])
                                        <div class="price-recall-info">
                                            <div class="price-update-container"
                                                style="flex-direction: column; display: flex;">
                                                {{-- <span class="badge badge-warning">Letest Recall: {{
                                                    $product['recalled_price']
                                                    }}
                                                    <i class="fa fa-info-circle" data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="{{ $product['last_recall_updated_at'] }} by {{ $product['last_recall_updated_by'] }}"></i>
                                                </span> --}}
                                                <div class="tw-justify-center">
                                                    <input type="text" class="form-control price-input"
                                                        value="{{ $product['recalled_price'] }}"
                                                        data-product-id="{{ $product['product_id'] }}"
                                                        data-variation-id="{{ $product['variation_id'] }}"
                                                        data-contact-id="{{ $contact_id }}">
                                                    <button class="btn btn-sm btn-primary price-update-btn">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="price-update-container">
                                            <input type="text" class="form-control price-input" value=""
                                                data-product-id="{{ $product['product_id'] }}"
                                                data-variation-id="{{ $product['variation_id'] }}"
                                                data-contact-id="{{ $contact_id }}">
                                            <button class="btn btn-sm btn-primary price-update-btn">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            
                            @endif
                        </tbody>
                            <tfoot>
                                <td>
                                    <i class="fa fa-trash" id="delete_selected_btn"
                                        style="font-size:20px;color:red"></i>
                                </td>
                                <td> {!! Form::label('search_product','Add Product:') !!}</td>
                                <td>
                                    <div class="form-group">

                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-search"></i>
                                            </span>
                                            <input type="hidden" value="" id="variation_id">
                                            {!! Form::text('search_product', null, ['class' => 'form-control', 'id'
                                            => 'search_product', 'placeholder' =>
                                            __('lang_v1.search_product_placeholder'), 'autofocus']);!!}
                                        </div>
                                    </div>
                                </td>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" id='close_button'
                data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        let contact_id=@json($contact_id);
        function fetchProductRow(variationId) {
            $.ajax({
                url: '/sells/price-recall/get_product_row/' + variationId+'/'+contact_id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    console.log('Product Data:', response);
                   //var recalled_price = response.group_prices[0].price_inc_tax;
                    var recalled_price = 0;
                    // Safe price handling logic
                    if (response.group_prices && response.group_prices.length > 0) {
                        //recalled_price = response.group_prices.price_inc_tax;
                        recalled_price = response.group_prices[0].price_inc_tax;
                    } else if (response.sell_price_inc_tax) {
                        recalled_price = response.sell_price_inc_tax;
                    } else if (response.price_with_tax) {
                        recalled_price = response.price_with_tax;
                    } else if (response.recalled_price) {
                        recalled_price = response.recalled_price;
                    }
                   // <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="${response.group_prices[0].price_inc_tax} without tax"></i>
                    var newRow = `
        <tr>
            <td>
                                        <input type="checkbox" class="row-select">
                                    </td>
            <td><img src="${response.product.image_url}" alt="Product Image" style="width: 50px; height: 50px;"></td>
            <td>
                ${response.product.name}<br>
                <b>${response.name}</b> 
                <small>${response.sub_sku}</small>
            </td>
            <td>
                ${response.price_with_tax ?? recalled_price}
                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="${response.group_prices && response.group_prices.length > 0 ? response.group_prices[0].price_inc_tax : 'N/A'} without tax"></i>
            </td>
            <td>
                ${response.created_by_name || '-'}
            </td>
            <td>
                ${response.created_at || '-'}
            </td>
            <td>
                ${response.last_price || '-'}
            </td>
            <td>
                <div class="price-update-container">
                    <input type="text" min="0"   class="form-control price-input" 
                        value="${recalled_price}" 
                        data-product-id="${response.product_id}" 
                        data-variation-id="${response.id}" 
                        data-contact-id="{{ $contact_id }}">
                    <button class="btn btn-sm btn-primary price-update-btn">
                        <i class="fas fa-save"></i>
                    </button>
                </div>
            </td>
        </tr>`;

                    $('#customer_pricerecll_table tbody').append(newRow);
                    $('[data-toggle="tooltip"]').tooltip(); // Reinitialize tooltips
                },

                error: function (xhr, status, error) {
                    console.error('Error fetching product:', error);
                    console.error('Response:', xhr.responseText);
                    toastr.error('Failed to load product data. Please try again.');
                }
            });
        }

        if ($('#search_product').length > 0) {
            $('#search_product').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: '/purchases/get_products?check_enable_stock=false',
                        dataType: 'json',
                        data: {
                            term: request.term,
                        },
                        success: function (data) {
                            response(
                                $.map(data, function (v, i) {
                                    if (v.variation_id) {
                                        return { label: v.text, value: v.variation_id };
                                    }
                                    return false;
                                })
                            );
                        },
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                    // console.log(ui.item.value);
                    // fetchProductRow(ui.item.value)
                    console.log('Selected product variation ID:', ui.item.value);
                    console.log('Selected product label:', ui.item.label);
                    fetchProductRow(ui.item.value);
                },
                focus: function (event, ui) {
                    event.preventDefault();
                    $(this).val(ui.item.label);
                },
            });
        }
        // Handle select all checkbox
        $('#select_all').on('change', function () {
            $('.row-select').prop('checked', $(this).prop('checked'));
        });

        // Optional: if user manually checks/unchecks individual checkbox, auto-update "select all" status
        $(document).on('change', '.row-select', function () {
            if ($('.row-select:checked').length === $('.row-select').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
        $('#delete_selected_btn').click(function () {
            var selectedIds = [];

            $('.row-select:checked').each(function () {
                selectedIds.push(Number($(this).val()));
            });

            if (selectedIds.length === 0) {
                swal("No rows selected", "Please select at least one item to delete.", "info");
                return;
            }
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: '/delete-recall-price',
                        type: 'POST',
                        data: {
                            ids: selectedIds,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            swal("Deleted!", "Selected items have been deleted.", "success");

                            // Remove deleted rows
                            selectedIds.forEach(function (id) {
                                $('input.row-select[value="' + id + '"]').closest('tr').fadeOut(500, function () {
                                    $(this).remove();
                                });
                            });
                        },
                        error: function (xhr) {
                            swal("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });

        });
        $(document).on('input', '.price-input', function () {
            let value = parseFloat($(this).val());
            if (isNaN(value) || value < 0) {
                $(this).val(0); // Auto-correct to 0 if invalid
            }
        });

    })
</script>
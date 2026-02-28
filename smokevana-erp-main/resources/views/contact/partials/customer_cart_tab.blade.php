<style>
    /* Modal Styles */
    .modal {
        overflow-y: auto;
    }

    .modal-dialog {
        width: 80%;
    }

    /* Cart Container Styles */
    .customer-cart-container {
        font-family: 'Segoe UI', sans-serif;
    }

    /* Address Box Styles */
    .address-box {
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 0.5rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .address-box .edit-icon {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        cursor: pointer;
        color: #6a11cb;
    }

    /* Order Summary Styles */
    .order-summary-box {
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 1rem;
        background-color: #f5f7ff;
        font-size: 14px;
    }

    .summary-total {
        font-size: 20px;
        color: #6a11cb;
        font-weight: bold;
    }

    /* Cart Table Styles */
    .cartTablediv {
        min-height: 50vh;
        max-height: 50vh;
        border: 2px solid rgb(211, 211, 211);
        overflow: hidden;
        position: relative;
    }

    .table-responsive {
        width: 100%;
        margin-bottom: 1rem;
        overflow-y: auto;
        max-height: 50vh;
        -webkit-overflow-scrolling: touch;
    }

    #cartTable {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    #cartTable thead {
        background-color: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 3;
    }

    #cartTable thead th {
        position: sticky;
        top: 0;
        border-bottom: 2px solid #dee2e6;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    #cartTable th,
    #cartTable td {
        white-space: nowrap;
        padding: 8px;
        vertical-align: middle;
        border: none;
    }

    #cartTable th {
        border-bottom: 1px solid #dee2e6;
    }

    #cartTable td {
        height: 60px;
    }

    #cartTable tbody tr {
        border-bottom: 1px solid #dee2e6;
    }

    #cartTable tbody tr:last-child {
        border-bottom: none;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .product-details {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .product-name {
        font-weight: 500;
    }

    .variation-name {
        font-size: 0.9em;
        color: #666;
    }

    .sku {
        font-size: 0.8em;
        color: #888;
    }

    .price-update-container {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .price-input {
        width: 80px !important;
        padding: 4px 8px !important;
        font-size: 13px !important;
    }

    .cart_price_update_btn {
        padding: 4px 8px !important;
        font-size: 12px !important;
    }

    .row_discount_amount {
        width: 70px !important;
        padding: 4px 8px !important;
        font-size: 13px !important;
    }

    .row_discount_type {
        width: 50px !important;
        padding: 4px 8px !important;
        font-size: 13px !important;
    }

    .quantity-input {
        width: 60px !important;
        padding: 4px 8px !important;
        font-size: 13px !important;
    }

    @media screen and (max-width: 768px) {
        .cartTablediv {
            margin: 0 -15px;
            border-left: none;
            border-right: none;
        }

        #cartTable th,
        #cartTable td {
            padding: 6px;
        }
    }

    /* Utility Classes */
    .tw-flex {
        display: flex;
    }

    .tw-justify-end {
        justify-content: flex-end;
    }

    .tw-gap-2 {
        gap: 0.5rem;
    }

    .tw-mt-2 {
        margin-top: 0.5rem;
    }

    .tw-mb-2 {
        margin-bottom: 0.5rem;
    }

    .tw-items-center {
        align-items: center;
    }
</style>

<div class="modal-dialog" role="document">
    <div class="modal-content customer-cart-container">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4>Customer Cart</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -35px">
                <button data-bs-toggle="tooltip" title="Copy Cart" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-info text-green" id="copyCartButton">
                    <i class="fa fa-copy"></i>
                    <i class="fa fa-shopping-cart"></i>
                </button>
                <button class="btn btn-primary edit_hide" id="edit_cart_display">Edit</button>
                <button class="btn btn-primary edit_hide" id="place_cart_order">Place Order</button>
                <button class="btn btn-primary hide edit_show" id="cancel_edit">Done</button>
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <!-- Address Section -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="address-box">
                        <i class="fa fa-edit edit-icon hide edit_show" id="edit_billing_address"></i>
                        <strong>Billing Address</strong><br>
                        <span class="billing_business_name">{{ $billing_address->business_name }}</span><br>
                        <span class="billing_full_name">{{ $billing_address->full_name}}</span><br>
                        <span class="billing_full_address">{{$billing_address->full_address}}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="address-box">
                        <i class="fa fa-edit edit-icon hide edit_show" id="edit_shipping_address"></i>
                        <strong>Shipping Address</strong><br>
                        <span class="shipping_business_name">{{ $shipping_address->business_name }}</span><br>
                        <span class="shipping_full_name">{{ $shipping_address->full_name}}</span><br>
                        <span class="shipping_full_address">{{$shipping_address->full_address}}</span>
                    </div>
                </div>
            </div>

            <!-- Cart Table Section -->
            <div class="cartTablediv">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="cartTable">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Image</th>
                                <th style="min-width: 500px;">Product/Variation</th>
                                <th style="width: 100px;">Price</th>
                                <th style="width: 80px;">Stock</th>
                                <th style="width: 100px;">Quantity</th>
                                <th style="width: 140px;">Discount</th>
                                <th style="width: 160px;" class="text-center">Price Recall</th>
                                <th class="hide edit_show">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartData as $cart)
                                <tr data-item-id={{ $cart['item_id'] }} 
                                    data-product-id={{ $cart['product_id'] }}
                                    data-variation-id={{ $cart['variation_id'] }} 
                                    data-item-tax={{ $cart['product_tax'] }}>
                                    <td>
                                        <img src="{{ $cart['product_image'] }}" alt="Product Image" style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <div class="product-details">
                                                <span class="product-name">{{ $cart['product_name'] }}</span>
                                                <div>
                                                    <span class="variation-name">{{ $cart['variation_name'] }}</span>
                                                    <span class="sku">{{ $cart['sku'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price_inc_tax">$ {{ number_format($cart['product_price_with_tax'], 2) }}</td>
                                    <td>{{ $cart['stock'] }}
                                        <input type="number" class="quantity_available hide" name="quantity_available" value={{ $cart['stock'] }}>
                                    </td>
                                    <td>
                                        <input type="number" value="{{ $cart['qty'] }}" class="form-control quantity-input edit_enable" readonly min="1" data-min-quantity="1">
                                        <input type="number" value="{{ $cart['qty'] }}" class="form-control hide quantity-last">
                                    </td>
                                    <td>
                                        <div class="input-group" style="width: 140px;">
                                            {!! Form::text("discount_amount", 0, [
                                                'class' => 'form-control input_number row_discount_amount edit_enable',
                                                'readonly' => true
                                            ]) !!}
                                            {!! Form::select("discount_type", ['fixed' => '$', 'percentage' => '%'], "", [
                                                'class' => 'form-control row_discount_type edit_enable', 
                                                'disabled' => true
                                            ]) !!}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="edit_hide">
                                            @if (!empty($cart['recalled_price']))
                                                <strong>Yes</strong>
                                            @else
                                                <strong>No</strong>
                                            @endif
                                        </div>
                                        <div class="price-update-container hide edit_show">
                                            <input type="text" class="form-control price-input" value="{{ $cart['recalled_price'] ?? '' }}"
                                                data-product-id="{{ $cart['product_id'] }}"
                                                data-variation-id="{{ $cart['variation_id'] }}"
                                                data-contact-id="{{ $contact_id }}">
                                            <button class="btn btn-sm btn-primary cart_price_update_btn">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="hide edit_show text-center" id="delete_row">
                                        <button><i class="fa fa-trash" style="color: red"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="hide edit_show">
                            <tr>
                                <td colspan="2">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                            {!! Form::text('search_product', null, [
                                                'class' => 'form-control', 
                                                'id' => 'search_product', 
                                                'placeholder' => __('lang_v1.search_product_placeholder'), 
                                                'autofocus'
                                            ]) !!}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="row tw-mt-2">
                <div class="col-md-4">
                    <textarea class="form-control" rows="6" placeholder="Add Memo"></textarea>
                </div>
                <div class="col-md-4">
                    <div class="order-summary-box">
                        <label>Shipping</label>
                        <div class="tw-flex tw-items-center tw-gap-2 tw-mb-2">
                            <input type="radio" id="flat_rate" name="shipping" value="flat_rate">
                            <label for="flat_rate">Flat rate: $15.00</label>
                        </div>
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <input type="radio" id="pickup" name="shipping" value="pickup" checked>
                            <label for="pickup">Pickup</label>
                        </div>
                        <small>Shipping to <b>
                            <span class="shipping_business_name">{{ $shipping_address->business_name }}</span>,
                            <span class="shipping_full_name">{{ $shipping_address->full_name}}</span>,
                            <span class="shipping_full_address">{{$shipping_address->full_address}}</span>
                        </b></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="order-summary-box">
                        <strong>Order Summary</strong><br>
                        <div>Order Sub Total: <span class="float-end cart_subtotal">$0.00</span></div>
                        <div>Discount: <span class="float-end">$0.00</span></div>
                        <div>Tax: <span class="float-end cart_total_tax">$0.00</span></div>
                        <div>Shipping: <span class="float-end cart_shipping_chargrs">$0.00</span></div>
                        <div class="mt-2 summary-total">Total: <span class="float-end text-purple cart_final_total">$0.00</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="AddressModal" tabindex="-1" role="dialog" aria-labelledby="AddressModalLabel">
    <div class="modal-dialog" style="width:60%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AddressModalLabel">Edit Address</h4>
                <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -35px">
                    <button type="button" class="btn btn-primary" id="saveAddress">Save changes</button>
                    <button type="button" class="btn btn-danger close_address">Close</button>
                </div>
            </div>
            <div class="modal-body">
                <form id="AddressForm">
                    <div class="row">
                        <div class="form-group col-md-4">
                            {!! Form::label('company', 'Company Name' . ':') !!}
                            {!! Form::text('company', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {!! Form::label('first_name', 'First name' . ':') !!}
                            {!! Form::text('first_name', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {!! Form::label('last_name', 'Last Name' . ':') !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                    <div class="row hideinput">
                        <div class="form-group col-md-6">
                            {!! Form::label('phone', 'Phone' . ':') !!}
                            {!! Form::text('phone', null, [
                                'class' => 'form-control', 
                                'required' => true, 
                                'pattern' => '^\+?[\d\s-]{10,}$'
                            ]) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('email', 'Email' . ':') !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                    <x-address-autocomplete 
                        addressInput="address_1" 
                        cityInput="city_locality"
                        stateInput="state_province" 
                        stateFormat="short_name" 
                        zipInput="postal_code"
                        countryInput="country_code" 
                        countryFormat="short_name" 
                    />
                    <div class="row">
                        <div class="form-group col-md-12">
                            {!! Form::label('address_1', 'Address 1' . ':') !!}
                            {!! Form::text('address_1', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                        <div class="form-group col-md-8">
                            {!! Form::label('address_2', 'Address 2' . ':') !!}
                            {!! Form::text('address_2', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {!! Form::label('city_locality', 'City/Locality' . ':') !!}
                            {!! Form::text('city_locality', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {!! Form::label('state_province', 'State/Province' . ':') !!}
                            {!! Form::text('state_province', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {!! Form::label('postal_code', 'Postal Code' . ':') !!}
                            {!! Form::text('postal_code', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                        <div class="form-group col-md-4">
                            {!! Form::label('country_code', 'Country Code' . ':') !!}
                            {!! Form::text('country_code', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@php
    $overSellingQuantity = session()->get('business.overselling_qty_limit');
    $pos_settings = session()->get('business.pos_settings');
    $isOverSelling=json_decode($pos_settings)->allow_overselling??'' ;
@endphp

<!-- Data Holders -->
<div id="overSellingQuantity-data-holder" data-overSellingQuantity='@json($overSellingQuantity)' class="hide"></div>
<div id="isOverSelling-data-holder" data-isOverSelling='@json($isOverSelling)' class="hide"></div>

<div id="cart-data-holder" data-cart='@json($cartData)' class="hide"></div>
<div id="shipping-data-holder" data-shipping='@json($shipping_address)' class="hide"></div>
<div id="billing-data-holder" data-billing='@json($billing_address)' class="hide"></div>
<div id="isCartAvailable-data-holder" data-isCartAvailable='@json($isCartAvailable)' class="hide"></div>
<input type="hidden" id="contact_id" value="{{ $contact_id }}">

<!-- Scripts -->
<script src="{{ asset('js/cart.js?v=' . $asset_v) }}"></script>
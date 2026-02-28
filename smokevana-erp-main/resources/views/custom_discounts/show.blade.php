<!-- Custom Discount Show Modal -->
<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content shadow-xl border-0 rounded-lg">
    <div class="modal-header bg-primary text-white align-items-center"
      style="border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
      <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
        style="opacity:1; font-size:2rem;">
        <span aria-hidden="true">&times;</span>
      </button>
      <h5 class="modal-title d-flex align-items-center">
        <i class="fas fa-gift mr-2"></i> View Offer
      </h5>
    </div>
    <div class="modal-body p-4 bg-light" style="border-bottom-left-radius: .5rem; border-bottom-right-radius: .5rem;">
      <style>
        /* (Copy all CSS from create.blade.php here for consistency) */
        .alert {
          margin-bottom: 1rem;
        }

        .alert-success {
          color: #155724;
          background-color: #d4edda;
          border-color: #c3e6cb;
        }

        .alert-danger {
          color: #721c24;
          background-color: #f8d7da;
          border-color: #f5c6cb;
        }

        .loading {
          opacity: 0.7;
          cursor: not-allowed !important;
        }

        button[type="submit"]:disabled {
          cursor: not-allowed;
          opacity: 0.7;
        }

        .switch .slider {
          transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .switch .slider span {
          transition: right 0.3s ease, transform 0.3s ease;
        }

        .switch input:checked+.slider {
          background-color: #4a90e2 !important;
          transform: scale(1.05);
        }

        .switch input:not(:checked)+.slider {
          background-color: #ccc !important;
          transform: scale(1);
        }

        .switch input:checked+.slider span {
          right: 2px !important;
          transform: scale(1.1);
        }

        .switch input:not(:checked)+.slider span {
          right: 22px !important;
          transform: scale(1);
        }

        .switch:hover .slider {
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .switch input:checked+.slider:hover {
          background-color: #357abd !important;
        }

        .switch input:not(:checked)+.slider:hover {
          background-color: #bbb !important;
        }

        .input-error {
          color: #d9534f;
          font-size: 0.95em;
          margin-top: 2px;
        }

        .input-invalid,
        .select2-container--default .select2-selection--single.input-invalid,
        .select2-container--default .select2-selection--multiple.input-invalid {
          border: 1.5px solid #d9534f !important;
          box-shadow: 0 0 2px #d9534f;
        }
      </style>


      <section class="content">
        <input type="text" class="hide discount_id" value={{$custom_discount->id}}>
        @php
      // Helper to get old value or from model
      function field($name, $default = null)
      {
        return old($name, $default);
      }
      $filter = $custom_discount->filter ? json_decode($custom_discount->filter, true) : null;
      $rulesOnCustomer = $custom_discount->rulesOnCustomer ? json_decode($custom_discount->rulesOnCustomer, true) : null;
      $custom_meta = $custom_discount->custom_meta ? json_decode($custom_discount->custom_meta, true) : null;
      $rulesOnCart = $custom_discount->rulesOnCart ? json_decode($custom_discount->rulesOnCart, true) : null;
    @endphp
        <!-- ================= Basic Information Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Basic Information', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
          <div class="col-md-3">
            {!! Form::label('rule_name', 'Rule Name *') !!}
            {!! Form::text('rule_name', $custom_discount->couponName, ['class' => 'form-control', 'readonly']) !!}
          </div>
          <div class="col-md-3">
            {!! Form::label('rule_type', 'Rule Type *') !!}
            {!! Form::select('rule_type', ['productAdjustment' => 'Product Adjustment', 'buyXgetY' => 'Buy X Get Y', 'cartAdjustment' => 'Cart Adjustment', 'freeShipping' => 'Free Shipping'], $custom_discount->discountType, ['class' => 'form-control', 'disabled']) !!}
          </div>
          <div class="col-md-3">
            <div class="d-flex align-items-center">
              {!! Form::checkbox('coupon_code_based', 1, !empty($custom_discount->couponCode), ['id' => 'coupon_code_based', 'disabled']) !!}
              {!! Form::label('coupon_code_based', 'Coupon Code Based', ['style' => 'margin-left:5px;']) !!}
            </div>
            {!! Form::text('coupon_code', $custom_discount->couponCode, ['class' => 'form-control', 'disabled']) !!}
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <div>
              {!! Form::label('is_active', 'Rule is Enable') !!}<br>
              <label class="switch">
                {!! Form::checkbox('is_active', 1, !$custom_discount->isDisabled, ['class' => 'form-control', 'style' => 'display:none', 'disabled']) !!}
                <span class="slider round"
                  style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                  <span
                    style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                </span>
              </label>
            </div>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <div>
              {!! Form::label('is_apply_for_all', 'Apply for all Products') !!}<br>
              <label class="switch">
                {!! Form::checkbox('is_apply_for_all', 1, empty($custom_discount->filter), ['class' => 'form-control', 'style' => 'display:none', 'disabled']) !!}
                <span class="slider round"
                  style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                  <span
                    style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                </span>
              </label>
            </div>
          </div>
          @if( session('business')->enable_referal_program)
          <div class="col-md-2 d-flex align-items-end">
            <div>
              {!! Form::label('is_referal_program_discount', 'Is Referal Program Discount') !!}<br>
              <label class="switch">
                {!! Form::checkbox('is_referal_program_discount', 1, $custom_discount->is_referal_program_discount, ['class' => 'form-control', 'style' => 'display:none', 'disabled']) !!}
                <span class="slider round" style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
                  <span style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
                </span>
              </label>
            </div>
          </div>
          @endif
        </div>
        @endcomponent
        <!-- ================= Filter Section ================= -->
        @if(!empty($custom_discount->filter))
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Filter', 'title_svg' => '<i class=\'fa fa-filter\'></i>'])
        <div id="filter-rows">
          @if($filter)
          @foreach($filter as $key => $value)
          @php
        $type = $key;
        $isIn = strpos($key, 'not_') === false;
        $typeName = str_replace('not_', '', $key);
        $selectType = $typeName === 'categories' ? 'category' : ($typeName === 'brand' ? 'brand' : 'product_ids');
        $ids = $value['ids'] ?? [];
        @endphp
          <div class="row align-items-end filter-row">
          <div class="col-md-3">
          <label>{{ $is_b2c ? '(Categories/Product)' : '(Categories/Brand/Product)' }}</label>
          <select class="form-control filter-type-select" name="filter_type" disabled>
          <option value="categories" {{ $typeName == 'categories' ? 'selected' : '' }}>Categories</option>
          <option value="brand" {{ $typeName == 'brand' ? 'selected' : '' }}>Brand</option>
          <option value="products" {{ $typeName == 'product_ids' ? 'selected' : '' }}>Products</option>
          </select>
          </div>
          <div class="col-md-6 filter-categories-group {{ $typeName != 'categories' ? 'hide' : '' }}">
          <label>Categories</label>
          <select class="form-control select2 multi-search" name="categories" multiple data-type="category" disabled>
          @php
              $categoryOptions = $type === 'not_categories' ? ($notCategories ?? []) : ($selectedCategories ?? []);
          @endphp
          @foreach($categoryOptions as $id => $name)
          <option value="{{ $id }}" selected>{{ $name }}</option>
        @endforeach
          </select>
          </div>
          <div class="col-md-6 filter-brand-group {{ $typeName != 'brand' ? 'hide' : '' }}">
          <label> Brand</label>
          <select class="form-control select2 multi-search" name="brand" multiple data-type="brand" disabled>
          @php
              $brandOptions = $type === 'not_brand' ? ($notBrands ?? []) : ($selectedBrands ?? []);
          @endphp
          @foreach($brandOptions as $id => $name)
          <option value="{{ $id }}" selected>{{ $name }}</option>
        @endforeach
          </select>
          </div>
          <div class="col-md-6 filter-products-group {{ $typeName != 'product_ids' ? 'hide' : '' }}">
          <label> Products</label>
          <select class="form-control select2 multi-search" name="products" multiple data-type="product" disabled>
          @php
              $productOptions = $type === 'not_product_ids' ? ($notProducts ?? []) : ($selectedProducts ?? []);
          @endphp
          @foreach($productOptions as $id => $name)
          <option value="{{ $id }}" selected>{{ $name }}</option>
        @endforeach
          </select>
          </div>
          <div class="col-md-1 d-flex">
          <div>
          <label>Is In</label><br>
          <label class="switch">
          <input type="checkbox" class="form-control" name="is_filter_in" style="display:none" {{ $isIn ? 'checked' : '' }} disabled>
          <span class="slider round"
          style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
          <span
          style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
          </span>
          </label>
          </div>
          </div>
          {{-- <div class="col-md-2 d-flex flex-column">
          <label>&nbsp;</label>
          <div class="mt-auto d-flex">
          <button type="button" class="btn btn-danger btn-sm ml-2 delete-filter-row" disabled><i
          class="fa fa-trash"></i></button>
          <a href="#" class="text-primary add-filter-row disabled" style="pointer-events:none;"><i
          class="fa fa-plus-circle"></i> Add another filter</a>
          </div>
          </div> --}}
          </div>
        @endforeach
        @endif
        </div>
        @endcomponent
    @endif
        <!-- ================= Discount Configuration Section ================= -->
        @if($custom_discount->discountType == 'productAdjustment')
      @component('components.widget', ['class' => 'box-primary', 'title' => ' Discount Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
      <div class="row">
        <div class="col-md-4">
        {!! Form::label('discount_type', 'Discount Type') !!}
        {!! Form::select('discount_type', ['Percentage Discount' => 'Percentage Discount', 'Fixed Discount' => 'Fixed Discount'], $custom_discount->discount == 'percentageDiscount' ? 'Percentage Discount' : 'Fixed Discount', ['class' => 'form-control', 'disabled']) !!}
        </div>
        <div class="col-md-4">
        {!! Form::label('discount_value', 'Discount Value') !!}
        {!! Form::number('discount_value', $custom_discount->discountValue, ['class' => 'form-control', 'placeholder' => 'e.g.10', 'min' => '0.01', 'step' => '0.01', 'readonly']) !!}
        </div>
        <div class="col-md-2">
        {!! Form::label('minimum_quantity', 'Minimum Quantity') !!}
        {!! Form::number('minimum_quantity', $custom_discount->minBuyQty, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1', 'readonly']) !!}
        </div>
        <div class="col-md-2">
        {!! Form::label('maximum_quantity', 'Maximum Quantity') !!}
        {!! Form::number('maximum_quantity', $custom_discount->maxBuyQty, ['class' => 'form-control', 'placeholder' => 'e.g. 2', 'min' => '1', 'step' => '1', 'readonly']) !!}
        </div>
      </div>
      @endcomponent
    @endif
        <!-- ================= Buy X Get Y Configuration Section ================= -->
        @if($custom_discount->discountType == 'buyXgetY')
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Buy X Get Y Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
        <div class="row">
          <div class="col-md-3">
          {!! Form::label('buy_quantity', 'Minimum Quantity') !!}
          {!! Form::number('buy_quantity', $custom_meta['buy_quantity'] ?? null, ['class' => 'form-control', 'readonly']) !!}
          </div>
          <div class="col-md-1 d-flex ">
          <div>
            {!! Form::label('is_recursive', 'Is Recursive') !!}<br>
            <label class="switch">
            {!! Form::checkbox('is_recursive', 1, $custom_meta['is_recursive'] ?? false, ['class' => 'form-control', 'style' => 'display:none', 'disabled']) !!}
            <span class="slider round"
              style="display:inline-block;width:40px;height:20px;background:#4a90e2;position:relative;border-radius:20px;vertical-align:middle;cursor:pointer;">
              <span
              style="position:absolute;right:2px;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:0.2s;"></span>
            </span>
            </label>
          </div>
          </div>
        </div>
        <div class="bogo_products">
          @if(isset($selectedGetYProducts) && is_array($selectedGetYProducts) && count($selectedGetYProducts))
          <div class="row">
        <div class="col-md-6">
          <label> Product & Variation</label>
        </div>
        <div class="col-md-2">
          <label>Quantity</label>
        </div>
        </div>
          @foreach($selectedGetYProducts as $bogo)
        <div class="row bogo-product-row">
        <div class="col-md-6 bogo-products-group">
          <select class="form-control select2 bogo-single-select" name="bogo_products[]"
          data-type="product_variations" disabled>
          <option value="{{ $bogo['id'] }}" selected>{{ $bogo['text'] }}</option>
          </select>
        </div>
        <div class="col-md-2">

          <input type="number" name="bogo_quantity[]" class="form-control" placeholder="e.g. 2" min="1" step="1"
          value="{{ $bogo['quantity'] }}" readonly>
        </div>
        </div>
        @endforeach
        @endif
        </div>
        @endcomponent
    @endif
        <!-- ================= Cart Adjustment Configuration Section ================= -->
        @if($custom_discount->discountType == 'cartAdjustment')
      @component('components.widget', ['class' => 'box-primary', 'title' => 'Cart Adjustment Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
      <div class="row">
        <div class="col-md-2">
        {!! Form::label('min_order_value_cart_adjustment', 'Minimum Order Value') !!}
        {!! Form::number('min_order_value_cart_adjustment', $rulesOnCart['minOrderValue'] ?? null, ['class' => 'form-control', 'readonly']) !!}
        </div>
        <div class="col-md-2">
        {!! Form::label('max_discount_amount_cart_adjustment', 'Maximum discount Amount') !!}
        {!! Form::number('max_discount_amonut_cart_adjustment', $rulesOnCart['maxDiscountAmount'] ?? null, ['class' => 'form-control', 'readonly']) !!}
        </div>
        <div class="col-md-4">
        {!! Form::label('discount_type_cart_adjustment', 'Discount Type') !!}
        {!! Form::select('discount_type_cart_adjustment', ['Percentage Discount' => 'Percentage Discount', 'Fixed Discount' => 'Fixed Discount'], $custom_discount->discount == 'percentageDiscount' ? 'Percentage Discount' : 'Fixed Discount', ['class' => 'form-control', 'disabled']) !!}
        </div>
        <div class="col-md-2">
        {!! Form::label('discount_value_cart_adjustment', 'discount Percent') !!}
        {!! Form::number('discount_value_cart_adjustment', $custom_discount->discountValue, ['class' => 'form-control', 'readonly']) !!}
        </div>
      </div>
      @endcomponent
    @endif
        <!-- ================= Free Shipping Configuration Section ================= -->
        @if($custom_discount->discountType == 'freeShipping')
      @component('components.widget', ['class' => 'box-primary', 'title' => 'Free Shipping Configuration', 'title_svg' => '<i class="fa fa-tags" aria-hidden="true"></i>'])
      <div class="row">
        <div class="col-md-2">
        {!! Form::label('min_order_value_shipping', 'Minimum Order Value') !!}
        {!! Form::number('min_order_value_shipping', $rulesOnCart['minOrderValue'] ?? null, ['class' => 'form-control', 'readonly']) !!}
        </div>
        <div class="col-md-2" hidden>
        {!! Form::label('max_discount_amount_shipping', 'Maximum discount Amount') !!}
        {!! Form::number('max_discount_amonut_shipping', $rulesOnCart['maxDiscountAmount'] ?? null, ['class' => 'form-control', 'readonly']) !!}
        </div>
        <div class="col-md-4">
        {!! Form::label('discount_type_shipping', 'Discount Type') !!}
        {!! Form::select('discount_type_shipping', ['free' => "FREE"], $custom_discount->discount == 'free' ? 'free' : ($custom_discount->discount == 'percentageDiscount' ? 'Percentage Discount' : 'Fixed Discount'), ['class' => 'form-control', 'disabled']) !!}
        </div>
        <div class="col-md-4" hidden>
        {!! Form::label('discount_value_shipping', 'Discount Value') !!}
        {!! Form::number('discount_value_shipping', $custom_discount->discountValue, ['class' => 'form-control', 'readonly']) !!}
        </div>
      </div>
      @endcomponent
    @endif
        <!-- ================= Validity & Usage Limits Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Validity & Usage limits', 'style' => 'z-index:999;position:relative;', 'title_svg' => '<i class="fa fa-clock"></i>'])
        <div class="row">
          <div class="col-md-4">
            {!! Form::label('valid_from', 'Rule Valid From') !!}
            {!! Form::text('valid_from', $custom_discount->applyDate, ['class' => 'form-control', 'readonly']) !!}
          </div>
          <div class="col-md-4">
            {!! Form::label('valid_to', 'Rule Valid To') !!}
            {!! Form::text('valid_to', $custom_discount->endDate, ['class' => 'form-control', 'readonly']) !!}
          </div>
          <div class="col-md-2" hidden>
            {!! Form::label('per_customer_limit', 'Per Customer Limit') !!}
            {!! Form::number('per_customer_limit', $custom_discount->per_customer_limit, ['class' => 'form-control', 'readonly']) !!}
          </div>
          <div class="col-md-2" hidden>
            {!! Form::label('max_usage_limit', 'Maximum Usage Limit') !!}
            {!! Form::number('max_usage_limit', $custom_discount->useLimit, ['class' => 'form-control', 'readonly']) !!}
          </div>
        </div>
        @endcomponent
        <!-- ================= Description & Customer Groups Section ================= -->
        <div class="row">
          <div class="col-md-6">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Description', 'title_svg' => '<i class="fa fa-file"></i>'])
            <div class="form-group">
                <style>
                   .description *{
                       font-size: 1em; /* Shrinks everything proportionally */
                      }
                </style>
              <div class="description">
                {!! $custom_discount->description !!}
              </div>
            </div>
            @endcomponent
          </div>
          <div class="col-md-6">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Customer Groups', 'title_svg' => '<i class="fa fa-users"></i>'])
            <div class="form-group">
              {!! Form::select('customer_groups_type', ['all_customers' => 'All Customers', 'customers_list' => 'Customers List', 'customers_group_list' => 'Customers Groups List'], isset($rulesOnCustomer['applyOn']) ? ($rulesOnCustomer['applyOn'] == 'customer-list' ? 'customers_list' : ($rulesOnCustomer['applyOn'] == 'customer-group' ? 'customers_group_list' : 'all_customers')) : 'all_customers', ['class' => 'form-control', 'id' => 'customer_groups_type', 'disabled']) !!}
            </div>
            @php
              $hasCustomersList = !empty($selectedCustomers) && count($selectedCustomers) > 0;
              $hasCustomerGroups = !empty($selectedCustomerGroups) && count($selectedCustomerGroups) > 0;
            @endphp
            @if($hasCustomersList)
            <div id="customers_list_box">
              <div class="row">
                <div class="col-md-11">
                  {!! Form::label('customers_list[]', 'Customers list') !!}
                  <select name="customers_list[]" class="form-control select2 multi-search" multiple
                    data-type="customer" disabled>
                    @foreach($selectedCustomers ?? [] as $id => $name)
            <option value="{{ $id }}" selected>{{ $name }}</option>
          @endforeach
                  </select>
                </div>
              </div>
            </div>
            @endif
            @if($hasCustomerGroups)
            <div id="customers_group_list_box">
              <div class="row">
                <div class="col-md-11">
                  {!! Form::label('customers_group_list[]', 'Customers Groups') !!}
                  <select name="customers_group_list[]" class="form-control select2 multi-search" multiple
                    data-type="customers_group" disabled>
                    @foreach($selectedCustomerGroups ?? [] as $id => $name)
            <option value="{{ $id }}" selected>{{ $name }}</option>
          @endforeach
                  </select>
                </div>
              </div>
            </div>
            @endif
            <div class="form-group tw-mt-3">
              {!! Form::label('customer_order_type', 'Order Value Type') !!}
              {!! Form::select('customer_order_type', ['all_orders' => 'All Orders', 'first_order' => 'On First Order', 'on_last_order' => 'On Last Order Value'], ($rulesOnCustomer['on-last-order-value'] ?? false) ? 'on_last_order' : (($rulesOnCustomer['on-first-order'] ?? false) ? 'first_order' : 'all_orders'), ['class' => 'form-control', 'id' => 'customer_order_type', 'disabled']) !!}
              @if(!empty($rulesOnCustomer['last-order-value']))
              <div class="last_order_value_div tw-mt-3">
                {!! Form::label('last_order_value', 'Last Order Value') !!}
                {!! Form::number('last_order_value', $rulesOnCustomer['last-order-value'] ?? null, ['class' => 'form-control', 'readonly']) !!}
              </div>
              @endif
            </div>
            @endcomponent
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Business Locations', 'title_svg' => '<i class="fa fa-file"></i>'])
            <div class="form-group">
                {{-- Business Locations Select --}}
                {!! Form::label('business_location', 'Business Locations') !!}
                {!! Form::select('business_location', $business_locations, $selectedLocation, ['class' => 'form-control select select_location_id multi-search' ,"readonly" => "readonly" ,"disabled"]) !!}
            </div>
            @if($is_b2c)
            <div class="form-group">
                {{-- Brand Select --}}
                {!! Form::label('brand_id', 'Brand') !!}
                {!! Form::select('brand_id', $brands, $selectedBrandids, ['class' => 'form-control select2  multi-search' ,"required" ,"multiple" ,"disabled"]) !!}
            </div>
            @endif
            @endcomponent
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    $('.multi-search').select2({
        placeholder: 'Search...',
        minimumInputLength: 1,
        width: '100%',
        ajax: {
            url: '/multi-select/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    s: params.term,
                    type: $(this).data('type')
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.result, function (item) {
                        if (item.name && item.id) {
                            return { id: item.id, text: item.name };
                        } else if (item.sku && item.name) {
                            return { id: item.id, text: item.name + ' (' + item.sku + ')' };
                        }
                        return { id: item.id, text: item.name || item.sku || item.id };
                    })
                };
            },
            cache: true
        }
    });
  });
  // Initialize visibility based on data presence
  function initializeFieldVisibility() { 
    var customersListBox = $('#customers_list_box');
    var customersGroupListBox = $('#customers_group_list_box');
    if (customersListBox.length > 0) {
      customersListBox.show();
    }
    if (customersGroupListBox.length > 0) {
      customersGroupListBox.show();
    }
  }
  initializeFieldVisibility();
</script>
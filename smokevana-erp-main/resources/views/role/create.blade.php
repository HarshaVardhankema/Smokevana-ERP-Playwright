@extends('layouts.app')
@section('title', __('role.add_role'))

@section('css')
<style>
/* Amazon Theme - Add Role */
.amazon-role-container {
    background: #EAEDED;
    min-height: calc(100vh - 60px);
    padding: 20px 24px;
}

/* Banner */
.amazon-role-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
}

.amazon-role-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
}

.amazon-role-banner__content {
    padding: 18px 24px;
}

.amazon-role-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-role-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-role-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

/* Form Card */
.amazon-role-card {
    background: #ffffff;
    border: 1px solid #d5d9d9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    padding: 20px 24px;
    margin-bottom: 20px;
}

.amazon-role-card .box-primary {
    background: transparent;
    border: none;
    box-shadow: none;
    margin-bottom: 0;
}

.amazon-role-card .form-group label {
    color: #0f1111;
    font-weight: 600;
    font-size: 13px;
}

.amazon-role-card .form-control {
    border: 1px solid #d5d9d9;
    border-radius: 6px;
    height: 38px;
    font-size: 13px;
}

.amazon-role-card .form-control:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

/* Submit Button */
.amazon-role-card .tw-dw-btn-primary {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: 1px solid #e47911 !important;
    border-radius: 6px !important;
    padding: 10px 24px !important;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.3);
    transition: all 0.2s ease;
}

.amazon-role-card .tw-dw-btn-primary:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')
<div class="amazon-role-container">

    <!-- Banner -->
    <div class="amazon-role-banner">
        <div class="amazon-role-banner__stripe"></div>
        <div class="amazon-role-banner__content">
            <h1 class="amazon-role-banner__title">
                <i class="fas fa-user-tag"></i>
                @lang('role.add_role')
            </h1>
            <p class="amazon-role-banner__subtitle">Create a new role and define permissions for your team</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="amazon-role-card">
    <!-- Main content -->
    <section class="content">
    @php
      $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
    @endphp
    @component('components.widget', ['class' => 'box-primary'])
        {!! Form::open(['url' => action([\App\Http\Controllers\RoleController::class, 'store']), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
              {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
          </div>
        </div>
        </div>
        <div class="row">
        <div class="col-md-3">
          <label>@lang( 'user.permissions' ):</label> 
        </div>
        </div>

        <div class="row check_group">
          <div class="col-md-1">
            <h4>@lang( 'lang_v1.others' )</h4>
          </div>
          <div class="col-md-2">
            <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                </label>
              </div>
          </div>
          <div class="col-md-9">
              @if(in_array('service_staff', $enabled_modules))
                <div class="col-md-12">
                  <div class="checkbox">
                    <label>
                      {!! Form::checkbox('is_service_staff', 1, false, 
                      [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
                    </label>
                    @show_tooltip(__('restaurant.tooltip_service_staff'))
                  </div>
                </div>
              @endif

              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'view_export_buttons', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_export_buttons' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'navigation_page_access', false, 
                    [ 'class' => 'input-icheck']); !!} {{ 'Navigation Page Access' }}
                  </label>
                </div>
              </div>
          </div>
        </div>
        <hr>

        <div class="row check_group">
        <div class="col-md-1">
          <h4>Home</h4>
        </div>
        <div class="col-md-2">
            <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'home.view', false, 
                [ 'class' => 'input-icheck']); !!} View Home
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'home.access', false, 
                [ 'class' => 'input-icheck']); !!} Access Home Module
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'dashboard.data', false, 
                [ 'class' => 'input-icheck']); !!} View Dashboard Data
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>

        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.user' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'user.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'user.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'user.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'user.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'user.roles' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'roles.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'roles.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'roles.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'roles.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.supplier' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="radio-group">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[supplier_view]', 'supplier.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_supplier' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[supplier_view]', 'supplier.view_own', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_supplier' ) }}
              </label>
            </div>
          </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'supplier.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'supplier.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'supplier.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.customer' ) @show_tooltip(__('lang_v1.customer_permissions_tooltip'))</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view]', 'customer.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_customer' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view]', 'customer.view_own', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_customer' ) }}
              </label>
            </div>
            <hr>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view_by_sell]', 'customer_with_no_sell_one_month', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_one_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view_by_sell]', 'customer_with_no_sell_three_month', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_three_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view_by_sell]', 'customer_with_no_sell_six_month', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_six_month' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view_by_sell]', 'customer_with_no_sell_one_year', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_with_no_sell_one_year' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[customer_view_by_sell]', 'customer_irrespective_of_sell', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.customer_irrespective_of_sell' ) }}
              </label>
            </div>
            <hr>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_customer_group', false, 
                [ 'class' => 'input-icheck']); !!} {{ "View Customer Group"}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'customer.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'customer.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'customer.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>Complaints</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'complaint.view', false, 
                [ 'class' => 'input-icheck']); !!} View Complaints
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'complaint.create', false, 
                [ 'class' => 'input-icheck']); !!} Create Complaint
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'complaint.update', false, 
                [ 'class' => 'input-icheck']); !!} Update Complaint
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'complaint.delete', false, 
                [ 'class' => 'input-icheck']); !!} Delete Complaint
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>Business Identifications</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'business_identification.view', false, 
                [ 'class' => 'input-icheck']); !!} View Business Identifications
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'business_identification.create', false, 
                [ 'class' => 'input-icheck']); !!} Create Business Identification
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'business_identification.update', false, 
                [ 'class' => 'input-icheck']); !!} Update Business Identification
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'business_identification.delete', false, 
                [ 'class' => 'input-icheck']); !!} Delete Business Identification
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'business.product' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'product.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'product.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'product.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'product.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'product.opening_stock', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_purchase_price', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.view_purchase_price') }}
              </label>
              @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
            </div>
          </div>
        </div>
        </div>
        <hr>
        @if(in_array('purchases', $enabled_modules) || in_array('stock_adjustment', $enabled_modules) )
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.purchase' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[purchase_view]', 'purchase.view', false, 
                [ 'class' => 'input-icheck']); !!} {{"View All Purchase"}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[purchase_view]', 'view_own_purchase', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.view_own_purchase') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[stock_view]', 'stock.view', false, 
                [ 'class' => 'input-icheck']); !!} {{"View All Stock Adjustment"}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[stock_view]', 'view_own_stock', false, 
                [ 'class' => 'input-icheck']); !!} {{"View Own Stock Adjustment"}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'purchase.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ "Purchase Create"}}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'purchase.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ "Purchase Update" }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'purchase.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ "Purchase Delete" }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'stock.create', false, 
                [ 'class' => 'input-icheck']); !!} {{"Stock Adjustment Create" }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'stock.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ 'Stock Adjustment Update' }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'stock.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ 'Stock Adjustment Delete' }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'purchase.payments', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.add_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'edit_purchase_payment', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'delete_purchase_payment', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.delete_purchase_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'purchase.update_status', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.update_status') }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @endif

        @if(!empty($common_settings['enable_purchase_requisition']))
          <div class="row check_group">
            <div class="col-md-1">
              <h4>@lang( 'lang_v1.purchase_requisition' )</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                  <label>
                    <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                  </label>
                </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::radio('radio_option[purchase_requisition_view]', 'purchase_requisition.view_all', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::radio('radio_option[purchase_requisition_view]', 'purchase_requisition.view_own', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'purchase_requisition.create', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_purchase_requisition' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'purchase_requisition.delete', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_purchase_requisition' ) }}
                  </label>
                </div>
              </div>

            </div>
          </div>
          <hr>
        @endif

        @if(!empty($common_settings['enable_purchase_order']))
          <div class="row check_group">
            <div class="col-md-1">
              <h4>@lang( 'lang_v1.purchase_order' )</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                  <label>
                    <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                  </label>
                </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::radio('radio_option[purchase_order_view]', 'purchase_order.view_all', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::radio('radio_option[purchase_order_view]', 'purchase_order.view_own', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'purchase_order.create', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'purchase_order.update', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_purchase_order' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'purchase_order.delete', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_purchase_order' ) }}
                  </label>
                </div>
              </div>

            </div>
          </div>
          <hr>
        @endif
        <div class="row check_group">
            <div class="col-md-1">
                <h4>@lang( 'sale.pos_sale' )</h4>
            </div>
            <div class="col-md-2">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                    </label>
                </div>
            </div>
            <div class="col-md-9">
            @if(in_array('pos_sale', $enabled_modules))
                <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('permissions[]', 'sell.view', false, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
                      </label>
                    </div>
                </div>
                <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'sell.create', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
                  </label>
                </div>
              </div>
                @endif
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'sell.update', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'sell.delete', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_pos_screen', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.edit_product_price_from_pos_screen') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_pos_screen', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'edit_pos_payment', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.add_edit_payment') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'print_invoice', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.print_invoice') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_pay_checkout', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_pay_checkout') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_draft', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_draft') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_express_checkout', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_express_checkout') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_discount', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_discount') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_suspend_sale', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_suspend_sale') }}
                  </label>
                </div>
              </div>

              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_credit_sale', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_credit_sale_button') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_quotation', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_quotation') }}
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'disable_card', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.disable_card') }}
                  </label>
                </div>
              </div>
            </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'sale.sale' ) @show_tooltip(__('lang_v1.sell_permissions_tooltip'))</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          @if(in_array('add_sale', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[sell_view]', 'direct_sell.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_sale' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[sell_view]', 'view_own_sell_only', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_paid_sells_only', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_paid_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_due_sells_only', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_due_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_partial_sells_only', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_partially_paid_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_overdue_sells_only', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_overdue_sells_only' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'direct_sell.access', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_sell' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'direct_sell.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.update_sale' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'direct_sell.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_sell' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_commission_agent_sell', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_commission_agent_sell' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'sell.payments', false, ['class' => 'input-icheck']); !!}
                {{ __('lang_v1.add_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'edit_sell_payment', false, ['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'delete_sell_payment', false, ['class' => 'input-icheck']); !!}
                {{ __('lang_v1.delete_sell_payment') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'edit_product_price_from_sale_screen', false, ['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_product_price_from_sale_screen') }}
              </label>
            </div>
          </div>
          
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'edit_product_discount_from_sale_screen', false, ['class' => 'input-icheck']); !!}
                {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'discount.access', false, ['class' => 'input-icheck']); !!}
                {{ __('lang_v1.discount.access') }}
              </label>
            </div>
          </div>
          @if(in_array('types_of_service', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'access_types_of_service', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_types_of_service' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'access_sell_return', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_all_sell_return' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'access_own_sell_return', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_own_sell_return' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'edit_invoice_number', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_edit_invoice_number' ) }}
              </label>
            </div>
          </div>

        </div>
        </div>
        <hr>
      @if(!empty($pos_settings['enable_sales_order']))
        <div class="row check_group">
          <div class="col-md-1">
            <h4>@lang( 'lang_v1.sales_order' )</h4>
          </div>
          <div class="col-md-2">
            <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                </label>
              </div>
          </div>
          <div class="col-md-9">
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! Form::radio('radio_option[so_view]', 'so.view_all', false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! Form::radio('radio_option[so_view]', 'so.view_own', false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('permissions[]', 'so.create', false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.create_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('permissions[]', 'so.update', false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('permissions[]', 'so.delete', false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_so' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="checkbox">
                <label>
                  {!! Form::checkbox('permissions[]', 'order_fulfillment.held', false, 
                  [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.order_fulfillment_held' ) }}
                </label>
              </div>
            </div>

          </div>
        </div>
        <hr>
      @endif
      <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'sale.draft' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::radio('radio_option[draft_view]', 'draft.view_all', false, 
            [ 'class' => 'input-icheck']) !!} {{ __( 'lang_v1.view_all_drafts' ) }}
          </label>
        </div>
      </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[draft_view]', 'draft.view_own', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_drafts' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'draft.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_draft' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'draft.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_draft' ) }}
              </label>
            </div>
          </div>

        </div>
      </div>
      <hr>
      <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'lang_v1.quotation' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
        <div class="checkbox">
          <label>
            {!! Form::radio('radio_option[quotation_view]', 'quotation.view_all', false, 
            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_quotations' ) }}
          </label>
        </div>
      </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[quotation_view]', 'quotation.view_own', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_quotations' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'quotation.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_quotation' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'quotation.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_quotation' ) }}
              </label>
            </div>
          </div>

        </div>
      </div>
      <hr>
      <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'lang_v1.shipments' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::radio('radio_option[shipping_view]', 'access_shipping', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_all_shipments') }}
                  </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::radio('radio_option[shipping_view]', 'access_own_shipping', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_own_shipping') }}
                  </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'access_pending_shipments_only', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_pending_shipments_only') }}
                  </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'access_commission_agent_shipping', false, ['class' => 'input-icheck']); !!}
                    {{ __('lang_v1.access_commission_agent_shipping') }}
                  </label>
                </div>
            </div>
        </div>
    </div>
    <hr>
        <div class="row check_group">
      <div class="col-md-1">
        <h4>@lang( 'cash_register.cash_register' )</h4>
      </div>
      <div class="col-md-2">
        <div class="checkbox">
            <label>
              <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
            </label>
          </div>
      </div>
      <div class="col-md-9">
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'view_cash_register', false, 
              [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_cash_register' ) }}
            </label>
          </div>
        </div>
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('permissions[]', 'close_cash_register', false, 
              [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.close_cash_register' ) }}
            </label>
          </div>
        </div>
      </div>
      </div>
        <hr>
        
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.brand' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'brand.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'brand.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'brand.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'brand.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.tax_rate' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'tax_rate.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'tax_rate.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'tax_rate.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'tax_rate.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.unit' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'unit.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'unit.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'unit.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'unit.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'category.category' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'category.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'category.create', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'category.update', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'category.delete', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.report' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
            @if(in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules))
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'purchase_n_sell_report.view', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_n_sell_report.view' ) }}
                  </label>
                </div>
              </div>
            @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'tax_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'contacts_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.contacts_report.view' ) }}
              </label>
            </div>
          </div>
          @if(in_array('expenses', $enabled_modules))
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'expense_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expense_report.view' ) }}
              </label>
            </div>
          </div>
          @endif
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'profit_loss_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'stock_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'trending_product_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_product_report.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'register_report.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.register_report.view' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'sales_representative.view', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'view_product_stock_value', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_product_stock_value' ) }}
              </label>
            </div>
          </div> 

        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'role.settings' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'business_settings.access', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'barcode_settings.access', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'invoice_settings.access', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'access_printers', false,['class' => 'input-icheck']); !!}
                {{ __('lang_v1.access_printers') }}
              </label>
            </div>
          </div>
        </div>
        </div>
        @if(in_array('expenses', $enabled_modules))
            <hr>
            <div class="row check_group">
                <div class="col-md-1">
                  <h4>@lang( 'lang_v1.expense' )</h4>
                </div>
                <div class="col-md-2">
                  <div class="checkbox">
                      <label>
                        <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                      </label>
                    </div>
                </div>
                <div class="col-md-9">
                  <div class="col-md-12">
                        <div class="checkbox">
                          <label>
                            {!! Form::radio('radio_option[expense_view]', 'all_expense.access', false, 
                            [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_all_expense' ) }}
                          </label>
                        </div>
                      </div>
                    <div class="col-md-12">
                        <div class="checkbox">
                      <label>
                        {!! Form::radio('radio_option[expense_view]', 'view_own_expense', false,['class' => 'input-icheck']); !!}
                        {{ __('lang_v1.view_own_expense') }}
                      </label>
                        </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('permissions[]', 'expense.add', false, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'expense.add_expense' ) }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('permissions[]', 'expense.edit', false, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'expense.edit_expense' ) }}
                      </label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('permissions[]', 'expense.delete', false, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_expense' ) }}
                      </label>
                    </div>
                  </div>
                </div>
            </div>
        @endif
        <hr>
        <div class="row check_group">
        <div class="col-md-3">
          <h4>@lang( 'role.dashboard' ) @show_tooltip(__('tooltip.dashboard_permission'))</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'dashboard.data', true, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        <div class="row check_group">
        <div class="col-md-3">
          <h4>@lang( 'account.account' )</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'account.access', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'edit_account_transaction', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.edit_account_transaction' ) }}
              </label>
            </div>
          </div>

          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'delete_account_transaction', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_account_transaction' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @if(in_array('booking', $enabled_modules))
        <div class="row check_group">
        <div class="col-md-1">
          <h4>@lang( 'restaurant.bookings' )</h4>
        </div>
        <div class="col-md-2">
          <div class="checkbox">
              <label>
                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
              </label>
            </div>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[bookings_view]', 'crud_all_bookings', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::radio('radio_option[bookings_view]', 'crud_own_bookings', false, 
                [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
              </label>
            </div>
          </div>
        </div>
        </div>
        <hr>
        @endif
        <div class="row">
        <div class="col-md-3">
          <h4>@lang( 'lang_v1.access_selling_price_groups' )</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('permissions[]', 'access_default_selling_price', true, 
                [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
              </label>
            </div>
          </div>
          @if(count($selling_price_groups) > 0)
          @foreach($selling_price_groups as $selling_price_group)
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id, false, 
                [ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
              </label>
            </div>
          </div>
          @endforeach
          @endif
        </div>
        </div>
        @if(in_array('tables', $enabled_modules))
          <div class="row">
            <div class="col-md-3">
              <h4>@lang( 'restaurant.restaurant' )</h4>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'access_tables', false, 
                    [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_tables') }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        @endif

          <hr>
          <div class="row check_group">
            <div class="col-md-1">
              <h4>E-commerce</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'ecom_controller', false, ['class' => 'input-icheck']) !!}
                    Ecom Controller
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'ecom_contact_us', false, ['class' => 'input-icheck']) !!}
                    Contact Us Access
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'ecom_newsletter', false, ['class' => 'input-icheck']) !!}
                    Newsletter Access
                  </label>
                </div>
              </div>
            </div>
          </div>                 
          <hr>
          <div class="row check_group">
            <div class="col-md-1">
              <h4>Picker Man</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'pickerman', false, ['class' => 'input-icheck']) !!}
                    Consider Picker
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'pickerman.view', false, ['class' => 'input-icheck']) !!}
                    Can View
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'pickerman.edit', false, ['class' => 'input-icheck']) !!}
                    Can Edit
                  </label>
                </div>
              </div>
              
            </div>
          </div>
          <hr>
          {{-- Bookkeeping Permissions --}}
          <div class="row check_group">
            <div class="col-md-1">
              <h4>Bookkeeping</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.access', false, ['class' => 'input-icheck']) !!}
                    Access Bookkeeping Module
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.view_dashboard', false, ['class' => 'input-icheck']) !!}
                    View Bookkeeping Dashboard
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_chart_of_accounts', false, ['class' => 'input-icheck']) !!}
                    Manage Chart of Accounts
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_journal_entries', false, ['class' => 'input-icheck']) !!}
                    Manage Journal Entries
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_bank_deposits', false, ['class' => 'input-icheck']) !!}
                    Manage Bank Deposits
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_accounts_receivable', false, ['class' => 'input-icheck']) !!}
                    Manage Accounts Receivable
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_accounts_payable', false, ['class' => 'input-icheck']) !!}
                    Manage Accounts Payable
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_liabilities', false, ['class' => 'input-icheck']) !!}
                    Manage Liabilities
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_partner_transactions', false, ['class' => 'input-icheck']) !!}
                    Manage Partner Transactions
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_inventory_valuation', false, ['class' => 'input-icheck']) !!}
                    Manage Inventory Valuation
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.manage_pl_transactions', false, ['class' => 'input-icheck']) !!}
                    Manage P&L Transactions
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.view_trial_balance', false, ['class' => 'input-icheck']) !!}
                    View Trial Balance
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'bookkeeping.view_reports', false, ['class' => 'input-icheck']) !!}
                    View Bookkeeping Reports
                  </label>
                </div>
              </div>
            </div>
          </div>
          <hr>
          {{-- Vendor Permissions --}}
          <div class="row check_group">
            <div class="col-md-1">
              <h4>Vendor</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'vendor.view_portal', false, ['class' => 'input-icheck']) !!}
                    View Vendor Portal
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'vendor.manage_product_requests', false, ['class' => 'input-icheck']) !!}
                    Manage Product Requests
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'vendor.assign_products', false, ['class' => 'input-icheck']) !!}
                    Assign Products to Vendors
                  </label>
                </div>
              </div>
            </div>
          </div>
          <hr>
          {{-- Dropship Vendor Permissions --}}
          <div class="row check_group">
            <div class="col-md-1">
              <h4>Dropship Vendor</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.vendor.access_portal', false, ['class' => 'input-icheck']) !!}
                    Access Vendor Portal
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.vendor.view_products', false, ['class' => 'input-icheck']) !!}
                    View Mapped Products
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.vendor.manage_stock', false, ['class' => 'input-icheck']) !!}
                    Manage Products & Stock
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.vendor.view_orders', false, ['class' => 'input-icheck']) !!}
                    View Assigned Orders
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.vendor.fulfill_orders', false, ['class' => 'input-icheck']) !!}
                    Manage & Fulfill Orders
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.vendor.view_earnings', false, ['class' => 'input-icheck']) !!}
                    View Earnings
                  </label>
                </div>
              </div>
            </div>
          </div>
          <hr>
          {{-- Dropship Admin Permissions --}}
          <div class="row check_group">
            <div class="col-md-1">
              <h4>Dropship Admin</h4>
            </div>
            <div class="col-md-2">
              <div class="checkbox">
                <label>
                  <input type="checkbox" class="check_all input-icheck"> {{ __( 'role.select_all' ) }}
                </label>
              </div>
            </div>
            <div class="col-md-9">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.admin.access_dashboard', false, ['class' => 'input-icheck']) !!}
                    Access Admin Dashboard
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.admin.manage_vendors', false, ['class' => 'input-icheck']) !!}
                    Manage Dropship Vendors
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    {!! Form::checkbox('permissions[]', 'dropship.admin.view_all_orders', false, ['class' => 'input-icheck']) !!}
                    View All Dropship Orders
                  </label>
                </div>
              </div>
            </div>
          </div>                 
        @include('role.partials.module_permissions')
        <div class="row">
        <div class="col-md-12 text-center">
           <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang( 'messages.save' )</button>
        </div>
        </div>
        
        {!! Form::close() !!}
    @endcomponent
    </section>
    <!-- /.content -->
    </div>
</div>
@endsection
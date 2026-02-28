<div class="modal-dialog contact-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
  <style>
    /* === Customer Create Modal - Amazon theme, responsive at 50%/80%/100% zoom === */
    .contact-form-modal { box-sizing: border-box; }
    .contact-form-modal .modal-content,
    .contact-form-modal .modal-body,
    .contact-form-modal .customer-create-card .form-control { box-sizing: border-box; }
    .contact-form-modal .modal-content {
      border-radius: 8px;
      overflow: hidden;
      border: none;
      box-shadow: 0 4px 24px rgba(0,0,0,0.2);
      display: flex;
      flex-direction: column;
    }
    .contact-form-modal .modal-header {
      background: #232f3e;
      color: #fff;
      padding: 1rem 1.25rem;
      border-bottom: none;
      flex-shrink: 0;
    }
    .contact-form-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
    .contact-form-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; margin-top: -0.25rem; }
    .contact-form-modal .modal-body {
      background: #37475a;
      padding: 1rem 1.25rem;
      max-height: min(85vh, 720px);
      overflow-y: auto;
      overflow-x: hidden;
      flex: 0 1 auto;
      min-height: 0;
    }
    .contact-form-modal .modal-footer {
      background: #232f3e;
      border-top: 1px solid rgba(255,255,255,0.15);
      padding: 0.75rem 1.25rem;
      flex-shrink: 0;
    }

    /* Cards - white fields on Amazon background */
    .contact-form-modal .customer-create-card {
      background: #fff;
      border-radius: 8px;
      padding: 1rem 1.25rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .contact-form-modal .customer-create-card-title {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #232F3E;
      margin: 0 0 0.75rem 0;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #D5D9D9;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .contact-form-modal .customer-create-card-title i { color: #FF9900; }

    /* Form groups - consistent spacing (zoom-safe) */
    .contact-form-modal .customer-create-card .form-group {
      margin-bottom: 0.75rem;
    }
    .contact-form-modal .customer-create-card .form-group:last-child,
    .contact-form-modal .customer-create-card .row:last-child .form-group { margin-bottom: 0; }
    .contact-form-modal .customer-create-card label,
    .contact-form-modal .customer-create-card .control-label,
    .contact-form-modal .customer-create-card .radio-inline,
    .contact-form-modal .customer-create-card .help-block,
    .contact-form-modal .customer-create-card .text-muted {
      color: #0F1111 !important;
      font-size: 0.8125rem;
    }
    .contact-form-modal .customer-create-card .help-block { margin: 0.25rem 0 0; color: #565959 !important; font-size: 0.75rem; }
    .contact-form-modal .customer-create-card .form-control {
      background: #fff;
      border: 1px solid #D5D9D9;
      color: #0F1111;
      font-size: 0.8125rem;
      padding: 0.375rem 0.5rem;
      min-height: 2rem;
      max-width: 100%;
      box-sizing: border-box;
    }
    .contact-form-modal .customer-create-card .form-control:focus {
      border-color: #FF9900;
      outline: none;
      box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .contact-form-modal .customer-create-card .input-group-addon {
      background: #F7F8F8;
      color: #232F3E;
      border-color: #D5D9D9;
      font-size: 0.8125rem;
      padding: 0.375rem 0.5rem;
      min-width: 2.25rem;
    }
    .contact-form-modal .customer-create-card .input-group .form-control { border-left-color: #D5D9D9; }
    .contact-form-modal .customer-create-card input[type="radio"],
    .contact-form-modal .customer-create-card input[type="checkbox"] { accent-color: #FF9900; }
    .contact-form-modal .customer-create-card .exempt-tax-checkbox-label span,
    .contact-form-modal .customer-create-card .exempt-tax-field span { color: #0F1111 !important; }
    .contact-form-modal .customer-create-card .exempt-tax-checkbox-label:hover { color: #C7511F !important; }
    .contact-form-modal .customer-create-card .exempt-tax-field .help-block { color: #565959 !important; }
    .contact-form-modal .exempt-tax-field .exempt-tax-cb { width: 1.125rem; height: 1.125rem; cursor: pointer; margin: 0; vertical-align: middle; flex-shrink: 0; }

    /* Row gaps inside cards */
    .contact-form-modal .customer-create-card .row { margin-left: -0.375rem; margin-right: -0.375rem; }
    .contact-form-modal .customer-create-card .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

    /* Buttons - Amazon orange */
    .contact-form-modal .modal-footer .btn-primary,
    .contact-form-modal .modal-footer .btn-primary:hover,
    .contact-form-modal .modal-footer .tw-dw-btn-primary,
    .contact-form-modal .modal-footer .tw-dw-btn-primary:hover {
      background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
      border-color: #C7511F !important;
      color: #fff !important;
      font-weight: 500;
      padding: 0.375rem 1rem;
    }
    .contact-form-modal .modal-footer .btn-default,
    .contact-form-modal .modal-footer .tw-dw-btn-neutral {
      background: transparent !important;
      border: 1px solid rgba(255,255,255,0.6) !important;
      color: #fff !important;
    }
    .contact-form-modal .modal-footer .btn-default:hover,
    .contact-form-modal .modal-footer .tw-dw-btn-neutral:hover {
      background: rgba(255,255,255,0.1) !important;
      color: #fff !important;
    }

    /* Vendor/Dropshipping section - same card style */
    .contact-form-modal .supplier_fields .customer-create-card { margin-top: 1rem; }
    .contact-form-modal .supplier_fields h4 { color: #232F3E; font-size: 1rem; margin-bottom: 0.75rem; }

    /* More div / financial section */
    .contact-form-modal #more_div .customer-create-card { margin-top: 0; }
    .contact-form-modal #more_div hr { border-color: rgba(255,255,255,0.25); margin: 1rem 0; }

    /* Responsive: stack on narrow */
    @media (max-width: 768px) {
      .contact-form-modal .modal-dialog { width: 100% !important; max-width: 100% !important; margin: 0.5rem; }
      .contact-form-modal .customer-create-card .row > [class*="col-"] { margin-bottom: 0.5rem; }
    }
  </style>
  @php
    $form_id = 'contact_add_form';
    if(isset($quick_add)){
      $form_id = 'quick_add_contact';
    }

    if(isset($store_action)) {
      $url = $store_action;
      $type = 'lead';
      $customer_groups = [];
    } else {
      $url = action([\App\Http\Controllers\ContactController::class, 'store']);
      $type = isset($selected_type) ? $selected_type : '';
      $sources = [];
      $life_stages = [];
    }
  @endphp
    {!! Form::open(['url' => $url, 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Add a new @if($type == 'customer') Customer @else Vendor @endif</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <!-- Card: Contact Type & ID -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fa fa-user"></i> @lang('contact.contact_type') & @lang('lang_v1.contact_id')</h5>
                    <div class="row">
            <div class="col-md-4 contact_type_div">
                <div class="form-group">
                    {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('type', $types, $type , ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" style="margin-top: 1.75rem;">
                <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="inlineRadio1" value="individual">
                    @lang('lang_v1.individual')
                </label>
                <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="inlineRadio2" value="business" checked>
                    @lang('business.business')
                </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-id-badge"></i>
                        </span>
                        {!! Form::text('contact_id', null, ['class' => 'form-control','placeholder' => __('lang_v1.contact_id')]); !!}
                    </div>
                    <p class="help-block">
                        @lang('lang_v1.leave_empty_to_autogenerate')
                    </p>
                </div>
            </div>
                    </div>
                </div>
            </div>

            <!-- Card: Business Details -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fa fa-briefcase"></i> @lang('business.business_name') & @lang('purchase.business_location')</h5>
                    <div class="row">
            <div class="col-md-4 ">
                <div class="form-group " >
                    {!! Form::label('supplier_business_name', __('business.business_name') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-briefcase"></i>
                        </span>
                        {!! Form::text('supplier_business_name', null, ['class' => 'form-control', 'placeholder' => __('business.business_name'), 'id' => 'supplier_business_name', 'required']); !!}
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-4 individual" >

            </div> --}}
            
            @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select') ,'required']); !!}
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($is_b2c))
            @if(!auth()->user()->can('access_all_locations') || !auth()->user()->can('admin'))
                <div class="col-md-4 brand_select_div_for_non customer_fields">
                    <div class="form-group">
                        {{ Form::label('brand_id', 'Select Brand' . ':') }}
                        {{ Form::select('brand_id', $brands, null, [
                            'class' => 'form-control select', 
                            'placeholder' => __('messages.please_select')
                        ]) }}
                    </div>
                </div>
            @endif
            @endif

             {{-- Customer Group for Non-Admin Users (Non-B2C only) --}}
             @if(empty($is_b2c))
             @if(!auth()->user()->can('access_all_locations') && !auth()->user()->can('admin'))
                 <div class="col-md-4 customer_group_select_div_for_non customer_fields">
                     <div class="form-group">
                         {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':*') !!}
                         <div class="input-group">
                             <span class="input-group-addon">
                                 <i class="fa fa-users"></i>
                             </span>
                             {!! Form::select('customer_group_id', $customer_groups, '', ['class' => 'form-control','required']); !!}
                         </div>
                     </div>
                 </div>
             @endif
             @endif
             
             {{-- Customer Group Selection for Admin Users (Hidden by default, shown for non-B2C) --}}
             @if((auth()->user()->can('access_all_locations') || auth()->user()->can('admin')) && empty($is_b2c))
                 <div class="col-md-4 customer_group_select_form_admin hide customer_fields">
                     <div class="form-group">
                         {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':*') !!}
                         <div class="input-group">
                             <span class="input-group-addon">
                                 <i class="fa fa-users"></i>
                             </span>
                             <select name="customer_group_id" class="form-control customer_group_id_select_form_admin" required>
                                 <option value="">{{ __('messages.please_select') }}</option>
                             </select>
                         </div>
                     </div>
                 </div>
             @endif
            
            {{-- Brand Selection for Admin Users (Hidden by default) --}}
            @if(auth()->user()->can('access_all_locations') || auth()->user()->can('admin'))
                <div class="col-md-4 brand_select_form_admin hide customer_fields">
                    <div class="form-group">
                        {{ Form::label('brand_id', 'Select Brand' . ':') }}
                       <select name="brand_id" class="form-control select brand_id_select_form_admin" placeholder="Select Brand">
                        <option value="">Select Brand</option>
                       </select>
                    </div>
                </div>
            @endif
                    </div>
                </div>
            </div>

            <!-- Card: Name -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fa fa-user"></i> @lang('user.name')</h5>
                    <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('prefix', __( 'business.prefix' ) . ':') !!}
                    {!! Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group">
                    {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group">
                    {!! Form::label('middle_name', __( 'lang_v1.middle_name' ) . ':') !!}
                    {!! Form::text('middle_name', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.middle_name' ) ]); !!}
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="form-group">
                    {!! Form::label('last_name', __( 'business.last_name' ) . ':*') !!}
                    {!! Form::text('last_name', null, ['class' => 'form-control','required', 'placeholder' => __( 'business.last_name' ) ]); !!}
                </div>
            </div>
                    </div>
                </div>
            </div>

            <!-- Card: Contact Information -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fa fa-phone"></i> @lang('contact.mobile') & @lang('business.email')</h5>
                    <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-mobile"></i>
                        </span>
                        {!! Form::text('mobile', null, ['class' => 'form-control', 'required', 'placeholder' => __('contact.mobile'), 'pattern' => '[0-9]{10,}','message' =>('Please enter a valid mobile number with at least 10 digits.')]); !!}
                    </div>
                </div>
                
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>
                        {!! Form::text('alternate_number', null, ['class' => 'form-control', 'placeholder' => __('contact.alternate_contact_number')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('landline', __('contact.landline') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>
                        {!! Form::text('landline', null, ['class' => 'form-control', 'placeholder' => __('contact.landline')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('email', __('business.email') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                        {!! Form::email('email', null, ['class' => 'form-control','required','placeholder' => __('business.email')]); !!}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-4 individual" style="display: none;">
                <div class="form-group">
                    {!! Form::label('dob', __('lang_v1.dob') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>
                        
                        {!! Form::text('dob', null, ['class' => 'form-control dob-date-picker','placeholder' => __('lang_v1.dob'), 'readonly']); !!}
                    </div>
                </div>
            </div>

            <!-- lead additional field -->
            <div class="col-md-4 lead_additional_div">
              <div class="form-group">
                  {!! Form::label('crm_source', __('lang_v1.source') . ':' ) !!}
                  <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fas fa fa-search"></i>
                      </span>
                      {!! Form::select('crm_source', $sources, null , ['class' => 'form-control', 'id' => 'crm_source','placeholder' => __('messages.please_select')]); !!}
                  </div>
              </div>
            </div>
            
            <div class="col-md-4 lead_additional_div">
              <div class="form-group">
                  {!! Form::label('crm_life_stage', __('lang_v1.life_stage') . ':' ) !!}
                  <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fas fa fa-life-ring"></i>
                      </span>
                      {!! Form::select('crm_life_stage', $life_stages, null , ['class' => 'form-control', 'id' => 'crm_life_stage','placeholder' => __('messages.please_select')]); !!}
                  </div>
              </div>
            </div>

            <!-- User in create leads -->
            <div class="col-md-6 lead_additional_div">
                  <div class="form-group">
                      {!! Form::label('user_id', __('lang_v1.assigned_to') . ':*' ) !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                          </span>
                          {!! Form::select('user_id[]', $users ?? [], null , ['class' => 'form-control select2', 'id' => 'user_id', 'multiple', 'required', 'style' => 'width: 100%;']); !!}
                      </div>
                  </div>
            </div>

                <!-- User in create customer & supplier -->
                @if (config('constants.enable_contact_assign') && $type !== 'lead')
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('assigned_to_users', __('lang_v1.assigned_to') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('assigned_to_users[]', $users ?? [], null, [
                                    'class' => 'form-control select2',
                                    'id' => 'assigned_to_users',
                                    'multiple',
                                    'style' => 'width: 100%;',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                @endif

                    </div>
                </div>
            </div>

            <!-- Card: User Account -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fa fa-id-badge"></i> User Account</h5>
                    <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_u_name', __('User Name') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-id-badge"></i>
                        </span>
                        {!! Form::text('customer_u_name', null, ['class' => 'form-control','placeholder' => __('User name')]); !!}
                    </div>
                    <p class="help-block">
                      @lang('lang_v1.leave_empty_to_autogenerate')
                  </p>
                </div>
              </div>
            {{-- custom field for password  --}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('password', __('New Password') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-lock"></i>
                        </span>
                        {{-- Using Laravel Form::password to ensure proper handling --}}
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'autocomplete'=>'new-password']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-2 customer_fields">
                <div class="form-group">
                        <label style="margin-top: 1.75rem; display: inline-block;">
                            {!! Form::checkbox('isApproved', true, ['class' => 'input-icheck', 'id' => 'enable_selling']) !!} <strong>Approved</strong>
                        </label>
                    </div>
                </div>
                    </div>
                </div>
            </div>

            {{-- Dropshipping Vendor Section - Only shown for suppliers --}}
            <div class="row supplier_fields" style="display: none;">
                <div class="col-md-12">
                    <div class="customer-create-card">
                        <h5 class="customer-create-card-title"><i class="fas fa-shipping-fast"></i> Vendor Settings</h5>
                        <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('vendor_type', 'Vendor Type:') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-truck"></i>
                            </span>
                            {!! Form::select('vendor_type', ['normal' => 'Normal Vendor', 'dropshipping' => 'Dropshipping Vendor'], 'normal', ['class' => 'form-control', 'id' => 'vendor_type_select']) !!}
                        </div>
                        <p class="help-block">Select whether this is a normal vendor or a dropshipping vendor</p>
                    </div>
                </div>
                
                {{-- Dropshipping specific fields - shown only when vendor type is dropshipping --}}
                <div class="dropshipping_fields" style="display: none;">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('commission_type', 'Commission Type:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-percent"></i>
                                </span>
                                {!! Form::select('commission_type', ['percentage' => 'Percentage', 'fixed' => 'Fixed'], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('commission_value', 'Commission Value:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-money-bill-alt"></i>
                                </span>
                                {!! Form::text('commission_value', null, ['class' => 'form-control input_number', 'placeholder' => 'Commission Value']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('default_markup_percentage', 'Default Markup Percentage:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-percent"></i>
                                </span>
                                {!! Form::text('default_markup_percentage', null, ['class' => 'form-control input_number', 'placeholder' => 'Default Markup']) !!}
                                <span class="input-group-addon">%</span>
                            </div>
                            <p class="help-block">Default markup to apply on products from this vendor</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('margin_percentage', 'Margin Percentage:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-chart-line"></i>
                                </span>
                                {!! Form::text('margin_percentage', null, ['class' => 'form-control input_number', 'placeholder' => 'Margin Percent']) !!}
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('dropship_payment_terms', 'Payment Terms:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::select('dropship_payment_terms', ['immediate' => 'Immediate', 'weekly' => 'Weekly', 'biweekly' => 'Bi-Weekly', 'monthly' => 'Monthly'], null, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('dropship_payment_method', 'Payment Method:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-credit-card"></i>
                                </span>
                                {!! Form::text('dropship_payment_method', null, ['class' => 'form-control', 'placeholder' => 'Payment Method']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('lead_time_days', 'Lead Time:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-clock"></i>
                                </span>
                                {!! Form::number('lead_time_days', null, ['class' => 'form-control', 'min' => 0, 'placeholder' => 'Lead Time']) !!}
                                <span class="input-group-addon">Days</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('min_order_qty', 'Min Order Quantity:') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-boxes"></i>
                                </span>
                                {!! Form::number('min_order_qty', null, ['class' => 'form-control', 'min' => 1, 'placeholder' => 'Min Order Quantity']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            <label style="accent-color: green;">
                                {!! Form::checkbox('auto_forward_orders', 1, false, ['class' => 'input-icheck']) !!}
                                <strong>Auto Forward Orders</strong>
                            </label>
                            <p class="help-block">Automatically forward orders to this vendor</p>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('dropship_notes', 'Dropship Notes:') !!}
                            {!! Form::textarea('dropship_notes', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter any notes about the dropshipping arrangement with this vendor']) !!}
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Dropshipping Vendor Section --}}

            <div class="row">
                {{-- <div class="col-md-12">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm center-block more_btn" data-target="#more_div">@lang('lang_v1.more_info') <i class="fa fa-chevron-down"></i></button>
            </div> --}}

                <div id="more_div">
                    {!! Form::hidden('position', null, ['id' => 'position']) !!}
                    <div class="col-md-12">
                        <hr />
                    </div>

            <!-- Card: Financial -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fas fa-money-bill-alt"></i> Tax, Balance & Limits</h5>
                    <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                      {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
                        <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-info"></i>
                          </span>
                          {!! Form::text('tax_number', null, ['class' => 'form-control','placeholder' => __('contact.tax_no')]); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-3 customer_fields">
                    <div class="form-group exempt-tax-field">
                        <label class="control-label" style="display: block; margin-bottom: 8px;">Exempt Tax</label>
                        <label class="exempt-tax-checkbox-label" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0; cursor: pointer; font-weight: normal;">
                            {!! Form::checkbox('is_tax_exempt', 1, false, [
                                'id' => 'is_tax_exempt',
                                'class' => 'exempt-tax-cb'
                            ]) !!}
                            <span style="font-size: 13px; color: #333;">Enable tax exemption for this customer</span>
                        </label>
                        <p class="help-block" style="font-size: 11px; margin-top: 6px; margin-bottom: 0; color: #6c757d;">
                            <i class="fas fa-info-circle"></i> When enabled, all sales orders for this customer will automatically exclude taxes
                        </p>
                    </div>
                </div>

                <div class="col-md-3 opening_balance">
                  <div class="form-group">
                      {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
                                            @show_tooltip(__('tooltip.opening_balance_negative'))
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fas fa-money-bill-alt"></i>
                          </span>
                          {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number']); !!}
                      </div>
                       <span class="help-block text-muted" style="font-size: 11px;">
                          <i class="fas fa-info-circle"></i> Use negative value if vendor owes you
                      </span>

                  </div>
                </div>

                    <div class="col-md-3 pay_term">
                        <div class="form-group">
                            <div class="multi-input">
                                {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
                                <br />
                                {!! Form::number('pay_term_number', null, [
                                    'class' => 'form-control width-40 pull-left',
                                    'min' => '0',
                                    'placeholder' => __('contact.pay_term'),
                                ]) !!}

                                {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], '', [
                                    'class' => 'form-control width-60 pull-left',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="clearfix"></div> --}}
                    @php
                        $common_settings = session()->get('business.common_settings');
                        $default_credit_limit = !empty($common_settings['default_credit_limit'])
                            ? $common_settings['default_credit_limit']
                            : null;
                    @endphp
                    <div class="col-md-3 customer_fields">
                        <div class="form-group">
                            {!! Form::label('credit_limit', __('lang_v1.credit_limit') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-money-bill-alt"></i>
                                </span>
                                {!! Form::text('credit_limit', $default_credit_limit ?? null, ['class' => 'form-control input_number']) !!}
                            </div>
                            <p class="help-block">@lang('lang_v1.credit_limit_help')</p>
                        </div>
                    </div>
                    <div class="col-md-3 customer_fields">
                        <div class="form-group">
                            {!! Form::label('transaction_limit','Transaction Limit' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fas fa-money-bill-alt"></i>
                                </span>
                                {!! Form::text('transaction_limit', null, ['class' => 'form-control input_number']) !!}
                            </div>
                            <p class="help-block">Keep it blank for no limit</p>
                        </div>
                    </div>
                    <div class="col-md-3 customer_fields">
                        <div class="form-group">
                            {!! Form::label('is_auto_send_due_notification','Auto Send Due Notification' . ':') !!}
                            {!! Form::checkbox('is_auto_send_due_notification', true, false) !!}
                        </div>
                    </div>

                    </div>
                </div>
            </div>

                    <div class="col-md-12">
                        <hr />
                    </div>
                    <div class="clearfix"></div>

            <!-- Card: Address -->
            <div class="col-md-12">
                <div class="customer-create-card">
                    <h5 class="customer-create-card-title"><i class="fa fa-map-marker"></i> @lang('business.address')</h5>
                    <div class="row">
                    <x-address-autocomplete addressInput="address_line_1" cityInput="city" stateInput="state"
                        stateFormat="short_name" zipInput="zip_code" countryInput="country"
                        countryFormat="short_name" />
                    {{-- <div class="col-md-12 address_type_div">
                    <div class="form-group">
                    {!! Form::label('type', ('address_type') . ':*' ) !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('type', $types, $type , ['class' => 'form-control', 'id' => 'address_type','placeholder' => __('messages.please_select'), 'required']); !!}
                        </div>
                    </div>
                </div> --}}

                    <div class="col-md-12"
                        style="display: flex; gap: 12px; margin-bottom: 24px; align-items: center;">
                        <label class="radio-inline ">
                            <input type="radio" name="address_type" id="inlineRadio3" value="Billing"
                                style=" accent-color: red;  ">
                            Billing
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="address_type" id="inlineRadio4" value="Shipping" checked
                                style="accent-color: red;">
                            Shipping
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="address_type" id="inlineRadio5" value="Both" checked
                                style="accent-color: red;">
                            Both
                        </label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                    <div class="form-group">
                            {!! Form::label('address_line_1', __('lang_v1.address_line_1') . ':*') !!}
                            <!-- Dummy field to mislead autofill -->
                            <input type="text" style="display:none;" name="fake_address_line_1"
                                autocomplete="street-address">
                            {!! Form::text('address_line_1', null, [
                                'class' => 'form-control',
                                'placeholder' => __('lang_v1.address_line_1'),
                                'rows' => 3,
                                'required',
                                'autocomplete' => 'off',
                                'role' => 'presentation',
                            ]) !!}
                        </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_2', __('lang_v1.address_line_2') . ':') !!}
                        {!! Form::text('address_line_2', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.address_line_2'), 'rows' => 3,]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
              <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('city', __('business.city') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                        {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('business.city'),'required']); !!}
                    </div>
                </div>
              </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('state', __('business.state') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('business.state'),'required']); !!}
                </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('country', __('business.country') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('business.country'),'required']); !!}
                </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('zip_code', __('business.zip_code') . ':*') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::text('zip_code', null, ['class' => 'form-control', 
                    'placeholder' => __('business.zip_code_placeholder'),'required']); !!}
                </div>
            </div>
          </div>

                    </div>
                </div>
            </div>

                    <div class="hide">
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <hr />
                        </div>
                        @php
                            $custom_labels = json_decode(session('business.custom_labels'), true);
                            $contact_custom_field1 = !empty($custom_labels['contact']['custom_field_1'])
                                ? $custom_labels['contact']['custom_field_1']
                                : __('lang_v1.contact_custom_field1');
                            $contact_custom_field2 = !empty($custom_labels['contact']['custom_field_2'])
                                ? $custom_labels['contact']['custom_field_2']
                                : __('lang_v1.contact_custom_field2');
                            $contact_custom_field3 = !empty($custom_labels['contact']['custom_field_3'])
                                ? $custom_labels['contact']['custom_field_3']
                                : __('lang_v1.contact_custom_field3');
                            $contact_custom_field4 = !empty($custom_labels['contact']['custom_field_4'])
                                ? $custom_labels['contact']['custom_field_4']
                                : __('lang_v1.contact_custom_field4');
                            $contact_custom_field5 = !empty($custom_labels['contact']['custom_field_5'])
                                ? $custom_labels['contact']['custom_field_5']
                                : __('lang_v1.custom_field', ['number' => 5]);
                            $contact_custom_field6 = !empty($custom_labels['contact']['custom_field_6'])
                                ? $custom_labels['contact']['custom_field_6']
                                : __('lang_v1.custom_field', ['number' => 6]);
                            $contact_custom_field7 = !empty($custom_labels['contact']['custom_field_7'])
                                ? $custom_labels['contact']['custom_field_7']
                                : __('lang_v1.custom_field', ['number' => 7]);
                            $contact_custom_field8 = !empty($custom_labels['contact']['custom_field_8'])
                                ? $custom_labels['contact']['custom_field_8']
                                : __('lang_v1.custom_field', ['number' => 8]);
                            $contact_custom_field9 = !empty($custom_labels['contact']['custom_field_9'])
                                ? $custom_labels['contact']['custom_field_9']
                                : __('lang_v1.custom_field', ['number' => 9]);
                            $contact_custom_field10 = !empty($custom_labels['contact']['custom_field_10'])
                                ? $custom_labels['contact']['custom_field_10']
                                : __('lang_v1.custom_field', ['number' => 10]);
                        @endphp
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field1', $contact_custom_field1 . ':') !!}
                                {!! Form::text('custom_field1', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field1]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field2', $contact_custom_field2 . ':') !!}
                                {!! Form::text('custom_field2', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field2]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field3', $contact_custom_field3 . ':') !!}
                                {!! Form::text('custom_field3', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field3]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field4', $contact_custom_field4 . ':') !!}
                                {!! Form::text('custom_field4', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field4]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field5', $contact_custom_field5 . ':') !!}
                                {!! Form::text('custom_field5', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field5]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field6', $contact_custom_field6 . ':') !!}
                                {!! Form::text('custom_field6', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field6]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field7', $contact_custom_field7 . ':') !!}
                                {!! Form::text('custom_field7', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field7]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field8', $contact_custom_field8 . ':') !!}
                                {!! Form::text('custom_field8', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field8]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field9', $contact_custom_field9 . ':') !!}
                                {!! Form::text('custom_field9', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field9]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field10', $contact_custom_field10 . ':') !!}
                                {!! Form::text('custom_field10', null, ['class' => 'form-control', 'placeholder' => $contact_custom_field10]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 shipping_addr_div"></div>
                    <div class="col-md-8 col-md-offset-2 shipping_addr_div mb-10 hide">
                        <strong>{{ __('lang_v1.shipping_address') }}</strong><br>
                        {!! Form::text('shipping_address', null, [
                            'class' => 'form-control',
                            'placeholder' => __('lang_v1.search_address'),
                            'id' => 'shipping_address',
                        ]) !!}
                        <div class="mb-10" id="map"></div>
                    </div>
                    @php
                        $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1'])
                            ? $custom_labels['shipping']['custom_field_1']
                            : '';

                        $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2'])
                            ? $custom_labels['shipping']['custom_field_2']
                            : '';

                        $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3'])
                            ? $custom_labels['shipping']['custom_field_3']
                            : '';

                        $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4'])
                            ? $custom_labels['shipping']['custom_field_4']
                            : '';

                        $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5'])
                            ? $custom_labels['shipping']['custom_field_5']
                            : '';
                    @endphp

                    @if (!empty($custom_labels['shipping']['is_custom_field_1_contact_default']) && !empty($shipping_custom_label_1))
                        @php
                            $label_1 = $shipping_custom_label_1 . ':';
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_1', $label_1) !!}
                                {!! Form::text('shipping_custom_field_details[shipping_custom_field_1]', null, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_1,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($custom_labels['shipping']['is_custom_field_2_contact_default']) && !empty($shipping_custom_label_2))
                        @php
                            $label_2 = $shipping_custom_label_2 . ':';
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_2', $label_2) !!}
                                {!! Form::text('shipping_custom_field_details[shipping_custom_field_2]', null, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_2,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($custom_labels['shipping']['is_custom_field_3_contact_default']) && !empty($shipping_custom_label_3))
                        @php
                            $label_3 = $shipping_custom_label_3 . ':';
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_3', $label_3) !!}
                                {!! Form::text('shipping_custom_field_details[shipping_custom_field_3]', null, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_3,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($custom_labels['shipping']['is_custom_field_4_contact_default']) && !empty($shipping_custom_label_4))
                        @php
                            $label_4 = $shipping_custom_label_4 . ':';
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_4', $label_4) !!}
                                {!! Form::text('shipping_custom_field_details[shipping_custom_field_4]', null, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_4,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($custom_labels['shipping']['is_custom_field_5_contact_default']) && !empty($shipping_custom_label_5))
                        @php
                            $label_5 = $shipping_custom_label_5 . ':';
                        @endphp

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('shipping_custom_field_5', $label_5) !!}
                                {!! Form::text('shipping_custom_field_details[shipping_custom_field_5]', null, [
                                    'class' => 'form-control',
                                    'placeholder' => $shipping_custom_label_5,
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    @if (!empty($common_settings['is_enabled_export']))
                        <div class="col-md-12 mb-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_export" class="form-check-input"
                                    id="is_customer_export">
                                <label class="form-check-label" for="is_customer_export">@lang('lang_v1.is_export')</label>
                            </div>
                        </div>
                        @php
                            $i = 1;
                        @endphp
                        @for ($i; $i <= 6; $i++)
                            <div class="col-md-4 export_div" style="display: none;">
                                <div class="form-group">
                                    {!! Form::label('export_custom_field_' . $i, __('lang_v1.export_custom_field' . $i) . ':') !!}
                                    {!! Form::text('export_custom_field_' . $i, null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('lang_v1.export_custom_field' . $i),
                                    ]) !!}
                                </div>
                            </div>
                        @endfor
                    @endif
                </div>
            </div>
            @include('layouts.partials.module_form_part')
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
            <button type="button" class="btn btn-default tw-dw-btn tw-dw-btn-neutral tw-text-white"
                data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('{{ $form_id }}');
    
    // On form submit, dynamically change the name attribute to something Chrome won't recognize
    form.addEventListener('submit', function () {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.type !== 'hidden' && input.type !== 'submit') {
                // Append a random string to the name to make it unique and unrecognizable
                const randomStr = Math.random().toString(36).substring(2, 15);
                input.setAttribute('name', input.getAttribute('name') + '-' + randomStr);
            }
        });
    });

    // Also, prevent autofill by setting a random value on focus (optional)
    // const addressInputs = form.querySelectorAll('input[name="address_line_1"], input[name="address_line_2"], input[name="city"], input[name="state"], input[name="country"], input[name="zip_code"]');
    const addressInputs = form.querySelectorAll('input[name="address_line_1"]');
    addressInputs.forEach(input => {
        input.addEventListener('focus', function () {
            if (!input.value) {
                input.value = ' '; // Set a single space to prevent autofill
            }
        });
        input.addEventListener('blur', function () {
            if (input.value === ' ') {
                input.value = ''; // Clear the space if the user didn't enter anything
            }
        });
    });
});
$('.select_location_id').on('change', function () {
    $.ajax({
        url: '/business-location/' + $(this).val(),
        type: 'GET',
        data: {location_id: $(this).val()},
        success: function (response) {
            console.log(response);
            // Handle Brand Selection for B2C locations
            if(response.is_b2c == 1){
                $('.brand_select_form_admin').removeClass('hide');
                response.brands.forEach(function (brand) {
                    $('.brand_id_select_form_admin').append('<option value="' + brand.id + '">' + brand.name + '</option>');
                });
            }else{
                $('.brand_select_form_admin').addClass('hide');
                $('.brand_id_select_form_admin').empty();
                $('.brand_id_select_form_admin').append('<option value="">Select Brand</option>');
            }
            
            // Handle Customer Group Selection for Non-B2C locations
            if(response.is_b2c == 1){
                // Hide customer group for B2C locations
                $('.customer_group_select_form_admin').addClass('hide');
                $('.customer_group_select_div_for_non').addClass('hide');
                $('.customer_group_id_select_form_admin').empty();
                $('.customer_group_id_select_form_admin').append('<option value="">{{ __('messages.please_select') }}</option>');
            }else{
                // Show customer group for Non-B2C locations
                // Check which field exists and show the appropriate one
                if($('.customer_group_select_form_admin').length > 0) {
                    // Admin field exists - show it and hide non-admin field
                    $('.customer_group_select_div_for_non').addClass('hide');
                    $('.customer_group_select_form_admin').removeClass('hide');
                    $('.customer_group_id_select_form_admin').empty();
                    $('.customer_group_id_select_form_admin').append('<option value="">{{ __('messages.please_select') }}</option>');
                    if(response.customer_groups){
                        response.customer_groups.forEach(function (group) {
                            $('.customer_group_id_select_form_admin').append('<option value="' + group.id + '">' + group.name + '</option>');
                        });
                    }
                } else {
                    // Non-admin field exists - show it
                    $('.customer_group_select_div_for_non').removeClass('hide');
                }
            }
        }
    });
});

// Toggle supplier fields (including dropshipping) based on contact type
function toggleSupplierFields() {
    var contactType = $('#contact_type').val();
    if (contactType === 'supplier' || contactType === 'both') {
        $('.supplier_fields').show();
    } else {
        $('.supplier_fields').hide();
    }
}

// Toggle dropshipping fields based on vendor type selection
function toggleDropshippingFields() {
    var vendorType = $('#vendor_type_select').val();
    if (vendorType === 'dropshipping') {
        $('.dropshipping_fields').slideDown();
    } else {
        $('.dropshipping_fields').slideUp();
    }
}

// Initialize on page load
$(document).ready(function() {
    // Initial toggle based on selected type
    toggleSupplierFields();
    toggleDropshippingFields();
    
    // Ensure only one Customer Group field is visible (hide non-admin field if admin field exists)
    if($('.customer_group_select_form_admin').length > 0) {
        $('.customer_group_select_div_for_non').addClass('hide');
    }
    
    // Listen for contact type changes
    $('#contact_type').on('change', function() {
        toggleSupplierFields();
    });
    
    // Listen for vendor type changes
    $('#vendor_type_select').on('change', function() {
        toggleDropshippingFields();
    });
});
</script>
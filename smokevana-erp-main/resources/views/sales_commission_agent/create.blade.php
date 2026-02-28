<div class="modal-dialog modal-xl" role="document" data-backdrop="static" data-keyboard="false">
  <div class="modal-content sca-amz">

    <style>
      /* Amazon-ish, clearly different look from default bootstrap form */
      .sca-amz .modal-header {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #0f172a; /* slate-900 */
        color: #fff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      }
      .sca-amz .modal-title {
        font-weight: 800;
        letter-spacing: 0.2px;
      }
      .sca-amz .modal-body {
        background: #f6f7fb;
        padding-top: 18px;
      }
      .sca-amz .amz-subtitle {
        color: rgba(255, 255, 255, 0.75);
        font-size: 12px;
        margin-top: 4px;
      }
      .sca-amz .amz-actions .tw-dw-btn {
        border-radius: 999px;
        padding: 8px 14px;
      }
      .sca-amz .amz-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 14px 6px;
        margin-bottom: 14px;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
      }
      .sca-amz .amz-card h4 {
        font-size: 14px;
        margin: 0 0 12px;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .sca-amz .amz-card h4 .amz-pill {
        font-size: 11px;
        font-weight: 700;
        color: #334155;
        background: #eef2ff;
        padding: 2px 8px;
        border-radius: 999px;
        border: 1px solid #e0e7ff;
      }
      .sca-amz .form-control,
      .sca-amz .select2-container--default .select2-selection--single,
      .sca-amz .select2-container--default .select2-selection--multiple {
        border-radius: 10px !important;
        border-color: #d1d5db;
        box-shadow: none;
        min-height: 38px;
      }
      .sca-amz textarea.form-control {
        min-height: 88px;
      }
      .sca-amz .form-group label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
      }
      .sca-amz .help-block {
        margin-top: 6px;
        color: #6b7280;
        font-size: 12px;
      }
      .sca-amz .amz-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 12px 0 16px;
      }
      .sca-amz .amz-inline {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
      }
      .sca-amz .amz-inline .checkbox {
        margin: 0;
      }
      /* Hide advanced commission fields (match 2nd reference) */
      .sca-amz .sca-advanced-commission {
        display: none;
      }

    </style>

    {!! Form::open(['url' => action([\App\Http\Controllers\SalesCommissionAgentController::class, 'store']), 'method' => 'post', 'id' => 'sale_commission_agent_form' ]) !!}

    <div class="modal-header">
      <div class="tw-flex tw-items-start tw-justify-between tw-gap-4">
        <div>
          <h4 class="modal-title" id="modalTitle">@lang( 'lang_v1.add_sales_commission_agent' )</h4>
          
        </div>
        <div class="amz-actions tw-flex tw-items-center tw-gap-2">
          <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">
            <i class="fa fa-save"></i> @lang( 'messages.save' )
          </button>
          <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal" id="close_button">
            <i class="fa fa-times"></i> @lang('messages.close')
          </button>
        </div>
      </div>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-7">
          <div class="amz-card">
            <h4><i class="fa fa-id-card-o"></i> Identity <span class="amz-pill">Basics</span></h4>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
                  {!! Form::text('surname', null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                  {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                  {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
                </div>
              </div>
            </div>

            <div class="amz-divider"></div>

            <h4><i class="fa fa-envelope-o"></i> Contact <span class="amz-pill">Reachability</span></h4>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('email', __( 'business.email' ) . ':') !!}
                  {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => __( 'business.email' ) ]); !!}
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('contact_no', __( 'lang_v1.contact_no' ) . ':') !!}
                  {!! Form::text('contact_no', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.contact_no' ) ]); !!}
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('address', __( 'business.address' ) . ':') !!}
                  {!! Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => __( 'business.address'), 'rows' => 3 ]); !!}
                </div>
              </div>
            </div>
          </div>

          <div class="amz-card">
            <h4><i class="fa fa-line-chart"></i> Commission Setup <span class="amz-pill">Rates</span></h4>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('b2b_customers', __( 'lang_v1.select_b2b_customers' ) . ':') !!} @show_tooltip(__('lang_v1.b2b_customer_access_help'))
                  {!! Form::select('b2b_customers[]', [], null, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'b2b_customers', 'style' => 'width: 100%']) !!}
                  <p class="help-block">Search and add customers this agent can access.</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('max_discount_percent', 'Max Discount Percentage (%):') !!}
                  {!! Form::text('max_discount_percent', null, ['class' => 'form-control input_number', 'placeholder' => 'Max Discount %', 'min' => '0', 'max' => '100', 'step' => '0.01']); !!}
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':*') !!}
                  {!! Form::text('cmmsn_percent', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ), 'required' ]); !!}
                </div>
              </div>
           
              <div class="col-md-6 col-md-offset-6 sca-advanced-commission">
                <div class="form-group">
                  {!! Form::label('commission_type', __( 'lang_v1.commission_type' ) . ':') !!}
                  {!! Form::select('commission_type', ['invoice_value' => __( 'lang_v1.invoice_value'), 'payment_received' => __( 'lang_v1.payment_received' ), 'by_category' => __( 'lang_v1.by_category')], null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.commission_type') ]); !!}
                </div>
              </div>

              <div class="col-md-4 sca-advanced-commission">
                <div class="form-group">
                  {!! Form::label('percentage_value', __( 'lang_v1.percentage_value' ) . ':') !!}
                  {!! Form::text('percentage_value', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.percentage_value' ) ]); !!}
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  {!! Form::label('status', __('business.is_active') . ':') !!}
                  <div class="amz-inline">
                    <div class="checkbox">
                      <label style="font-weight: 600;">
                        {!! Form::hidden('status', 'inactive'); !!}
                        {!! Form::checkbox('status', 'active', true, ['id' => 'status']); !!}
                        @lang('is Active')
                      </label>
                    </div>
                  </div>
                  <p class="help-block">Disable to keep agent inactive.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <div class="amz-card">
            <h4><i class="fa fa-lock"></i> Login & Access <span class="amz-pill">Security</span></h4>

            <div class="form-group">
              <div class="checkbox">
                <label style="font-weight: 700;">
                  {!! Form::checkbox('allow_login', 1, false, ['class' => 'input-icheck', 'id' => 'allow_login']) !!}
                  @lang('lang_v1.allow_login')
                </label>
              </div>
              <p class="help-block">Enable login only if this agent needs to access the system.</p>
            </div>

            <div class="user_auth_fields hide">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('username', __('business.username') . ':') !!}
                    {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('business.username')]) !!}
                    <p class="help-block">@lang('lang_v1.username_help')</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    {!! Form::label('password', __('business.password') . ':') !!}
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __('business.password')]) !!}
                    <p class="help-block">@lang('lang_v1.password_help')</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="amz-divider"></div>

            <h4><i class="fa fa-map-marker"></i> Access Locations <span class="amz-pill">Permissions</span></h4>
            <p class="help-block" style="margin-top:-6px;">Choose which locations this agent can access.</p>
            <div class="row">
              @foreach($locations as $location)
                <div class="col-sm-6">
                  <div class="checkbox" style="margin-top: 6px;">
                    <label style="font-weight: 600;">
                      {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, false, ['class' => 'input-icheck']) !!}
                      {{ $location->name }} @if(!empty($location->location_id))({{ $location->location_id}}) @endif
                    </label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="amz-card">
            <h4><i class="fa fa-user"></i> Personal Details <span class="amz-pill">Optional</span></h4>
            <div class="row">
              @include('sales_commission_agent.partial.form_part')
            </div>
          </div>
        </div>
      </div>
    </div>


    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
$(document).ready(function(){
    // Initialize iCheck for checkboxes
    $('input[type="checkbox"].input-icheck').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
    });

    // Handle allow_login checkbox
    $('#allow_login').on('ifChecked', function(event){
        $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
        $('div.user_auth_fields').addClass('hide');
    });

    // Initialize B2B customers select2
    $('#b2b_customers').select2({
        ajax: {
            url: '{{ action("App\Http\Controllers\ContactController@getCustomers", ["type" => "customer"]) }}',
            dataType: 'json',
            delay: 1000,
            data: { location_id: '1' },
            processResults: function (data, params) {
                return {
                    results: data || [],
                };
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
            },
            cache: true
        },
        minimumInputLength: 2,
        width: '100%'
    });

    // Initialize DOB calendar (Bootstrap datepicker) on #agent_dob
    if ($('#agent_dob').length && $.fn.datepicker) {
        $('#agent_dob').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: datepicker_date_format
        });
    }

    // Keep functionality while hiding the advanced fields:
    // - Default commission_type to invoice_value if empty
    // - Keep percentage_value in sync with cmmsn_percent
    function __syncCommissionHiddenFields() {
        var cmmsn = $('input[name="cmmsn_percent"]').val();
        if (!$('#commission_type').val()) {
            $('#commission_type').val('invoice_value').trigger('change');
        }
        if (cmmsn !== undefined && cmmsn !== null && cmmsn !== '') {
            $('input[name="percentage_value"]').val(cmmsn);
        }
    }

    $(document).on('change keyup', 'input[name="cmmsn_percent"]', function(){
        __syncCommissionHiddenFields();
    });

    $('form#sale_commission_agent_form').on('submit', function(){
        __syncCommissionHiddenFields();
    });

});
</script>
@extends('layouts.app')
@section('title', __('business.business_settings'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
@endsection

@section('content')
<div class="admin-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('business.business_settings')</h1>
    <br>
    @include('layouts.partials.search_settings')
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\BusinessController::class, 'postBusinessSettings']), 'method' => 'post', 'id' => 'bussiness_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
       <!--  <pos-tab-container> -->
        {{-- <div class="col-xs-12 pos-tab-container"> --}}
        @component('components.widget', ['class' =>  'pos-tab-container'])
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu tw-rounded-lg">
                <div class="list-group">
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base  active">@lang('business.business')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.referal_program')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base hide">@lang('business.tax') @show_tooltip(__('tooltip.business_tax'))</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.product')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('contact.contact')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.sale')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base hide">@lang('sale.pos_sale')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('purchase.purchases')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.payment')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.dashboard')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('business.system')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.prefixes')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.email_settings')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.sms_settings')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.reward_point_settings')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base">@lang('lang_v1.modules')</a>
                    <a href="#" class="list-group-item text-center tw-font-bold tw-text-sm md:tw-text-base hide">@lang('lang_v1.custom_labels')</a>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                <!-- tab 1 start -->
                @include('business.partials.settings_business')
                <!-- tab 1 end -->
                <!-- tab 1.5 start -->
                @include('business.partials.settings_referal_program')
                <!-- tab 1.5 end -->
                <!-- tab 2 start -->
                @include('business.partials.settings_tax')
                <!-- tab 2 end -->
                <!-- tab 3 start -->
                @include('business.partials.settings_product')

                @include('business.partials.settings_contact')
                <!-- tab 3 end -->
                <!-- tab 4 start -->
                @include('business.partials.settings_sales')
                @include('business.partials.settings_pos')
                <!-- tab 4 end -->
                <!-- tab 5 start -->
                @include('business.partials.settings_purchase')

                @include('business.partials.settings_payment')
                <!-- tab 5 end -->
                <!-- tab 6 start -->
                @include('business.partials.settings_dashboard')
                <!-- tab 6 end -->
                <!-- tab 7 start -->
                @include('business.partials.settings_system')
                <!-- tab 7 end -->
                <!-- tab 8 start -->
                @include('business.partials.settings_prefixes')
                <!-- tab 8 end -->
                <!-- tab 9 start -->
                @include('business.partials.settings_email')
                <!-- tab 9 end -->
                <!-- tab 10 start -->
                @include('business.partials.settings_sms')
                <!-- tab 10 end -->
                <!-- tab 11 start -->
                @include('business.partials.settings_reward_point')
                <!-- tab 11 end -->
                <!-- tab 12 start -->
                @include('business.partials.settings_modules')
                <!-- tab 12 end -->
                @include('business.partials.settings_custom_labels')
            </div>
            </div>
        @endcomponent
        {{-- </div> --}}
        <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <button class="tw-dw-btn tw-dw-btn-error tw-dw-btn-lg tw-text-white" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->
</div>
@stop
@section('javascript')
<script type="text/javascript">
    __page_leave_confirmation('#bussiness_edit_form');
    $(document).on('ifToggled', '#use_superadmin_settings', function() {
        if ($('#use_superadmin_settings').is(':checked')) {
            $('#toggle_visibility').addClass('hide');
            $('.test_email_btn').addClass('hide');
        } else {
            $('#toggle_visibility').removeClass('hide');
            $('.test_email_btn').removeClass('hide');
        }
    });

    $(document).ready(function(){

    
        $('#test_email_btn').click( function() {
            var data = {
                mail_driver: $('#mail_driver').val(),
                mail_host: $('#mail_host').val(),
                mail_port: $('#mail_port').val(),
                mail_username: $('#mail_username').val(),
                mail_password: $('#mail_password').val(),
                mail_encryption: $('#mail_encryption').val(),
                mail_from_address: $('#mail_from_address').val(),
                mail_from_name: $('#mail_from_name').val(),
            };
            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testEmailConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });
        });

        $('#test_sms_btn').click( function() {
            var test_number = $('#test_number').val();
            if (test_number.trim() == '') {
                toastr.error('{{__("lang_v1.test_number_is_required")}}');
                $('#test_number').focus();

                return false;
            }

            var data = {
                url: $('#sms_settings_url').val(),
                send_to_param_name: $('#send_to_param_name').val(),
                msg_param_name: $('#msg_param_name').val(),
                request_method: $('#request_method').val(),
                param_1: $('#sms_settings_param_key1').val(),
                param_2: $('#sms_settings_param_key2').val(),
                param_3: $('#sms_settings_param_key3').val(),
                param_4: $('#sms_settings_param_key4').val(),
                param_5: $('#sms_settings_param_key5').val(),
                param_6: $('#sms_settings_param_key6').val(),
                param_7: $('#sms_settings_param_key7').val(),
                param_8: $('#sms_settings_param_key8').val(),
                param_9: $('#sms_settings_param_key9').val(),
                param_10: $('#sms_settings_param_key10').val(),

                param_val_1: $('#sms_settings_param_val1').val(),
                param_val_2: $('#sms_settings_param_val2').val(),
                param_val_3: $('#sms_settings_param_val3').val(),
                param_val_4: $('#sms_settings_param_val4').val(),
                param_val_5: $('#sms_settings_param_val5').val(),
                param_val_6: $('#sms_settings_param_val6').val(),
                param_val_7: $('#sms_settings_param_val7').val(),
                param_val_8: $('#sms_settings_param_val8').val(),
                param_val_9: $('#sms_settings_param_val9').val(),
                param_val_10: $('#sms_settings_param_val10').val(),
                test_number: test_number
            };

            $.ajax({
                method: 'post',
                data: data,
                url: "{{ action([\App\Http\Controllers\BusinessController::class, 'testSmsConfiguration']) }}",
                dataType: 'json',
                success: function(result) {
                    if (result.success == true) {
                        swal({
                            text: result.msg,
                            icon: 'success'
                        });
                    } else {
                        swal({
                            text: result.msg,
                            icon: 'error'
                        });
                    }
                },
            });

        });

        $('select.custom_labels_products').change(function(){
            value = $(this).val();
            textarea = $(this).parents('div.custom_label_product_div').find('div.custom_label_product_dropdown');
            if(value == 'dropdown'){
                textarea.removeClass('hide');
            } else{
                textarea.addClass('hide');
            }
        })

        // Referral Program Settings
        // Handle enable/disable referral program
        $('#enable_referal_program').on('ifToggled', function() {
            if ($(this).is(':checked')) {
                $('#referal_program_fields').removeClass('hide');
            } else {
                $('#referal_program_fields').addClass('hide');
            }
        });

        // Initialize custom discount multi-select dropdown
        $('#referal_program_custom_discount_id').select2({
            placeholder: '{{ __("business.select_custom_discount") }}',
            allowClear: true,
            width: '100%'
        });

        // Handle B2C checkbox to show/hide brand multiselect
        $('#referal_available_for_b2c').on('ifToggled', function() {
            if ($(this).is(':checked')) {
                $('#referal_brand_list_container').show();
            } else {
                $('#referal_brand_list_container').hide();
            }
        });

        // Initialize brand multiselect with AJAX search
        $('#referal_brand_list').select2({
            placeholder: '{{ __("business.referal_brand_list_placeholder") }}',
            allowClear: true,
            minimumInputLength: 1,
            width: '100%',
            ajax: {
                url: "{{ url('/multi-select/search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        s: params.term || '',
                        type: 'brand'
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.result, function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                cache: true
            }
        });

        // Modern Switches Logic
        // Product Expiry Toggle
        $('#enable_product_expiry').change(function() {
            if ($(this).is(':checked')) {
                $('#expiry_type_container').removeClass('hide');
                $('#expiry_type').prop('disabled', false);
                $('#on_expiry_div').removeClass('hide');
            } else {
                $('#expiry_type_container').addClass('hide');
                $('#expiry_type').prop('disabled', true);
                $('#on_expiry_div').addClass('hide');
            }
        });

        // Enable Category/Sub-category Logic
        $('#enable_category').change(function() {
            if ($(this).is(':checked')) {
                $('.enable_sub_category').removeClass('hide');
            } else {
                $('.enable_sub_category').addClass('hide');
            }
        });

        // On Expiry Selection Logic
        $('#on_product_expiry').change(function() {
            if ($(this).val() == 'stop_selling') {
                $('#stop_selling_before').prop('disabled', false);
                $('#stop_selling_before').prop('required', true);
            } else {
                $('#stop_selling_before').prop('disabled', true);
                $('#stop_selling_before').prop('required', false);
            }
        });

        // Destroy iCheck for all modern switches to ensure native events work
        $('.modern-switch input[type="checkbox"]').iCheck('destroy');
    });
</script>
@endsection
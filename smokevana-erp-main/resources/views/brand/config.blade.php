@extends('layouts.app')
@section('title', __('brand.brand_configuration'))

@section('content')

<!-- Amazon-style Banner Header -->
<div class="amazon-brand-config-banner">
    <div class="amazon-banner-content">
        <h1 class="amazon-banner-title">
            <i class="fa fa-cog"></i>
            @lang('brand.brand_configuration')
        </h1>
        <p class="amazon-banner-subtitle">{{ $brand->name }}</p>
    </div>
</div>

<!-- Main content -->
<section class="content amazon-brand-config-content">
    @if(session('status'))
        @if(session('status')['success'])
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('status')['msg'] }}
            </div>
        @else
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('status')['msg'] }}
            </div>
        @endif
    @endif

    {!! Form::open(['url' => action([\App\Http\Controllers\BrandController::class, 'saveConfig'], [$brand->id]), 'method' => 'post', 'id' => 'brand_config_form']) !!}
    
    <div class="row">
        <div class="col-xs-12">
            @component('components.widget', ['class' => 'box-primary amazon-config-card', 'title' => __('brand.email_settings')])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mail_host', __('lang_v1.mail_host') . ':*') !!}
                            {!! Form::text('mail_host', !empty($brandConfig->email_settings['mail_host']) ? $brandConfig->email_settings['mail_host'] : '', ['class' => 'form-control', 'id' => 'mail_host', 'placeholder' => 'smtp.gmail.com']); !!}
                            <p class="help-block"><small>Example: smtp.gmail.com, smtp.mailgun.org</small></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mail_port', __('lang_v1.mail_port') . ':*') !!}
                            {!! Form::number('mail_port', !empty($brandConfig->email_settings['mail_port']) ? $brandConfig->email_settings['mail_port'] : '587', ['class' => 'form-control', 'id' => 'mail_port', 'placeholder' => '587']); !!}
                            <p class="help-block"><small>587 for TLS, 465 for SSL</small></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mail_username', __('lang_v1.mail_username') . ':*') !!}
                            {!! Form::text('mail_username', !empty($brandConfig->email_settings['mail_username']) ? $brandConfig->email_settings['mail_username'] : '', ['class' => 'form-control', 'id' => 'mail_username', 'placeholder' => __('lang_v1.mail_username')]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mail_password', __('lang_v1.mail_password') . ':*') !!}
                            {!! Form::text('mail_password', !empty($brandConfig->email_settings['mail_password']) ? $brandConfig->email_settings['mail_password'] : '', ['class' => 'form-control', 'id' => 'mail_password', 'type' => 'password', 'placeholder' => __('lang_v1.mail_password')]); !!}
                            <p class="help-block"><small>For Gmail: Use App Password (16 characters), not regular password</small></p>
                            @if(!empty($brandConfig->email_settings['mail_password']))
                                <p class="help-block text-success"><small><i class="fa fa-check"></i> Password is already saved. Leave blank to keep current password.</small></p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mail_from_address', __('lang_v1.mail_from_address') . ':*') !!}
                            {!! Form::email('mail_from_address', !empty($brandConfig->email_settings['mail_from_address']) ? $brandConfig->email_settings['mail_from_address'] : '', ['class' => 'form-control', 'id' => 'mail_from_address', 'placeholder' => __('lang_v1.mail_from_address')]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('mail_from_name', __('lang_v1.mail_from_name') . ':') !!}
                            {!! Form::text('mail_from_name', !empty($brandConfig->email_settings['mail_from_name']) ? $brandConfig->email_settings['mail_from_name'] : '', ['class' => 'form-control', 'id' => 'mail_from_name', 'placeholder' => __('lang_v1.mail_from_name')]); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary amazon-test-email-btn" id="test_brand_email_btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            @lang('brand.test_email')
                        </button>
                        <span class="amazon-help-text">
                            <strong>Note:</strong> Save your settings first, then click "Test Email" to verify configuration.
                        </span>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-primary amazon-config-card', 'title' => __('brand.email_templates')])
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom amazon-tabs">
                    <ul class="nav nav-tabs amazon-nav-tabs">
                        @php $index = 0; @endphp
                        @foreach($notificationTemplates as $templateType => $templateInfo)
                            <li @if($index == 0) class="active" @endif>
                                <a href="#cn_{{ $templateType }}" data-toggle="tab" aria-expanded="true">
                                    {{ $templateInfo['name'] }}
                                </a>
                            </li>
                            @php $index++; @endphp
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @php $index = 0; @endphp
                        @foreach($notificationTemplates as $templateType => $templateInfo)
                            <div class="tab-pane @if($index == 0) active @endif" id="cn_{{ $templateType }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if(!empty($templateInfo['extra_tags']))
                                            <strong>@lang('lang_v1.available_tags'):</strong>
                                            @foreach($templateInfo['extra_tags'] as $tagGroup)
                                                <p class="help-block">
                                                    {{ implode(', ', $tagGroup) }}
                                                </p>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-12 mt-10">
                                        <div class="form-group">
                                            {!! Form::label($templateType . '_subject', __('lang_v1.email_subject') . ':*') !!}
                                            {!! Form::text('template_data[' . $templateType . '][subject]', isset($existingTemplates[$templateType]) ? $existingTemplates[$templateType]['subject'] : '', ['class' => 'form-control', 'placeholder' => __('lang_v1.email_subject'), 'id' => $templateType . '_subject']); !!}
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label($templateType . '_cc', 'CC:') !!}
                                            {!! Form::text('template_data[' . $templateType . '][cc]', isset($existingTemplates[$templateType]) ? $existingTemplates[$templateType]['cc'] : '', ['class' => 'form-control', 'placeholder' => 'CC', 'id' => $templateType . '_cc']); !!}
                                            <p class="help-block"><small>@lang('brand.cc_help')</small></p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label($templateType . '_bcc', 'BCC:') !!}
                                            {!! Form::text('template_data[' . $templateType . '][bcc]', isset($existingTemplates[$templateType]) ? $existingTemplates[$templateType]['bcc'] : '', ['class' => 'form-control', 'placeholder' => 'BCC', 'id' => $templateType . '_bcc']); !!}
                                            <p class="help-block"><small>@lang('brand.bcc_help')</small></p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label($templateType . '_email_body', __('lang_v1.email_body') . ':') !!}
                                            {!! Form::textarea('template_data[' . $templateType . '][template_body]', isset($existingTemplates[$templateType]) ? $existingTemplates[$templateType]['template_body'] : '', ['class' => 'form-control ckeditor', 'placeholder' => __('lang_v1.email_body'), 'id' => $templateType . '_email_body', 'rows' => 6]); !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $index++; @endphp
                        @endforeach
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row amazon-form-actions">
        <div class="col-md-12 text-center">
            <button type="submit" class="amazon-btn-primary amazon-btn-save">
                @lang('messages.save')
            </button>
            <a href="{{ action([\App\Http\Controllers\BrandController::class, 'index']) }}" class="amazon-btn-secondary amazon-btn-back">
                Back
            </a>
        </div>
    </div>

    {!! Form::close() !!}
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize TinyMCE for all ckeditor textareas
        $('textarea.ckeditor').each(function() {
            var editor_id = $(this).attr('id');
            tinymce.init({
                selector: 'textarea#' + editor_id,
                convert_urls: false,
                relative_urls: false,
                remove_script_host: false,
                document_base_url: '{{ url('/') }}',
                valid_elements: '*[*]',
                verify_html: false,
                cleanup: false,
            });
        });

        // Form submission
        $('#brand_config_form').submit(function(e) {
            // Sync all TinyMCE editors before submit
            tinymce.triggerSave();
        });

        // Test email configuration
        $('#test_brand_email_btn').click(function() {
            swal({
                title: '@lang("brand.test_email")',
                text: '@lang("brand.enter_email_to_send_test")',
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "email@example.com",
                        type: "email",
                    },
                },
                buttons: {
                    cancel: {
                        text: "@lang('messages.cancel')",
                        value: null,
                        visible: true,
                    },
                    confirm: {
                        text: "@lang('messages.send')",
                        value: true,
                    }
                }
            }).then((email) => {
                if (email) {
                    // Validate email
                    if (!isValidEmail(email)) {
                        swal({
                            title: 'Error',
                            text: 'Invalid email format',
                            icon: 'error'
                        });
                        return;
                    }

                    // Show loading
                    swal({
                        title: 'Sending Test Email...',
                        text: 'Please wait',
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });

                    $.ajax({
                        method: 'POST',
                        url: '{{ action([\App\Http\Controllers\BrandController::class, "testEmailConfig"], [$brand->id]) }}',
                        data: {
                            test_email: email,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                swal({
                                    title: 'Success!',
                                    text: result.msg,
                                    icon: 'success'
                                });
                            } else {
                                var errorMsg = result.msg;
                                if (result.technical_details) {
                                    errorMsg += '\n\nTechnical Details: ' + result.technical_details;
                                }
                                swal({
                                    title: 'Error',
                                    text: errorMsg,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            swal({
                                title: 'Error',
                                text: 'Request failed: ' + error,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        // Email validation function
        function isValidEmail(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    });
</script>
@endsection

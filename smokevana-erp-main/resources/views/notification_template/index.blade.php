@extends('layouts.app')
@section('title', __('lang_v1.notification_templates'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.notification-templates-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.notification-templates-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.notification-templates-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.notification-templates-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.notification-templates-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.notification-templates-page .content-header .notification-banner-subtitle {
    font-size: 13px; color: rgba(255,255,255,0.88) !important; margin: 8px 0 0 0;
}
.notification-templates-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
    margin-bottom: 20px;
}
.notification-templates-page .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.notification-templates-page .box-title { color: #fff !important; font-weight: 600; }
.notification-templates-page .nav-tabs-custom {
    border: none; margin: 0; background: #fff; border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15,17,17,0.08);
}
.notification-templates-page .nav-tabs-custom > .nav-tabs {
    border-bottom: none; margin: 0; background: #f8f9fa;
    border-radius: 10px 10px 0 0; padding: 8px 8px 0 8px;
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li {
    margin-bottom: 0;
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li > a {
    color: #37475a; border: none; border-radius: 8px;
    margin-right: 4px; padding: 12px 20px; background: transparent;
    font-weight: 500; font-size: 14px; transition: all 0.3s ease;
    position: relative; overflow: hidden;
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li > a::before {
    content: ''; position: absolute; top: 0; left: -100%; width: 100%;
    height: 100%; background: linear-gradient(90deg, transparent, rgba(255,153,0,0.1), transparent);
    transition: left 0.5s ease;
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li > a:hover {
    background: #fff; color: #ff9900; transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255,153,0,0.15);
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li > a:hover::before {
    left: 100%;
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li.active > a {
    background: linear-gradient(135deg, #ff9900 0%, #e47911 100%);
    color: #fff; font-weight: 600; border: none;
    box-shadow: 0 4px 15px rgba(255,153,0,0.3);
    transform: translateY(-1px);
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li.active > a::after {
    content: ''; position: absolute; bottom: 0; left: 50%;
    transform: translateX(-50%); width: 30px; height: 3px; border-radius: 2px;
}
/* Remove blue focus outline when selecting a tab; use orange ring instead */
.notification-templates-page .nav-tabs-custom > .nav-tabs > li > a:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(255,153,0,0.3);
}
.notification-templates-page .nav-tabs-custom > .nav-tabs > li.active > a:focus {
    outline: none !important;
    box-shadow: 0 4px 15px rgba(255,153,0,0.4), 0 0 0 3px rgba(255,153,0,0.3);
}
.notification-templates-page .form-control {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.notification-templates-page .form-control:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.notification-templates-page .callout-warning {
    background: #fff8e7; border-left: 4px solid #ff9900; border-radius: 6px;
    color: #0f1111;
}
.notification-templates-page .text-center .tw-dw-btn-error {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; padding: 10px 24px; border-radius: 8px;
}
/* Tab content styling */
.notification-templates-page .tab-content {
    background: #fff; padding: 24px; border-radius: 0 0 10px 10px;
    border: 1px solid #e9ecef; border-top: none;
}
.notification-templates-page .tab-pane {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.notification-templates-page .form-group {
    margin-bottom: 20px;
}
.notification-templates-page .form-group label {
    font-weight: 600; color: #37475a; margin-bottom: 8px;
}
.notification-templates-page .help-block {
    color: #6c757d; font-size: 13px; margin-top: 6px;
    font-style: italic;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page notification-templates-page">
<!-- Amazon-style banner -->
<section class="content-header amazon-theme-banner">
    <h1>
        <i class="fa fa-bell page-header-icon"></i>
        {{ __('lang_v1.notification_templates') }}
    </h1>
    <p class="notification-banner-subtitle">Configure email and SMS notification templates for your business</p>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\NotificationTemplateController::class, 'store']), 'method' => 'post' ]) !!}

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.notifications') . ':'])
                @include('notification_template.partials.tabs', ['templates' => $general_notifications])
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.customer_notifications') . ':'])
                @include('notification_template.partials.tabs', ['templates' => $customer_notifications])
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.supplier_notifications') . ':'])
                @include('notification_template.partials.tabs', ['templates' => $supplier_notifications])

                <div class="callout callout-warning">
                    <p>@lang('lang_v1.logo_not_work_in_sms'):</p>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="tw-dw-btn tw-dw-btn-error tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
        </div>
    </div>
    {!! Form::close() !!}

</section>
<!-- /.content -->
</div>
@stop
@section('javascript')
<script type="text/javascript">
    $('textarea.ckeditor').each( function(){
        var editor_id = $(this).attr('id');
        tinymce.init({
            selector: 'textarea#'+editor_id,
            convert_urls: false,
            relative_urls: false,
            remove_script_host: false,
            document_base_url: '{{ url('/') }}',
            valid_elements: '*[*]',
            verify_html: false,
            cleanup: false,
        });
    });
</script>
@endsection
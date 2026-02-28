@extends('layouts.app')
@section('title', __('printer.add_printer'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* ========== Add Printer Page – Amazon theme ========== */
.printer-add-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.printer-add-page .amazon-printer-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 10px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
    overflow: hidden;
}
.printer-add-page .amazon-printer-banner::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    opacity: 0.9;
}
.printer-add-page .amazon-printer-banner .banner-inner {
    display: flex; align-items: center; gap: 18px;
}
.printer-add-page .amazon-printer-banner .banner-icon {
    width: 52px; height: 52px; min-width: 52px;
    border-radius: 10px; background: rgba(255,255,255,0.1);
    color: #fff; font-size: 24px;
    display: flex; align-items: center; justify-content: center;
}
.printer-add-page .amazon-printer-banner .banner-text { display: flex; flex-direction: column; gap: 6px; }
.printer-add-page .amazon-printer-banner .banner-title {
    font-size: 24px; font-weight: 700; margin: 0; color: #fff;
}
.printer-add-page .amazon-printer-banner .banner-subtitle {
    font-size: 13px; color: rgba(255,255,255,0.78); margin: 0;
}

/* Amazon style cards */
.printer-add-page .printer-section-card {
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #D5D9D9;
}
.printer-add-page .printer-section-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 600;
    position: relative;
}
.printer-add-page .printer-section-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: #ff9900;
}
.printer-add-page .printer-section-header i,
.printer-add-page .printer-section-header .fa { color: #FF9900; font-size: 18px; }
.printer-add-page .printer-section-body {
    background: #fff;
    padding: 1.25rem 1.5rem;
}
.printer-add-page .printer-section-body .form-group { margin-bottom: 0.75rem; }
.printer-add-page .printer-section-body .form-group:last-child { margin-bottom: 0; }
.printer-add-page .printer-section-body label { color: #0F1111 !important; font-size: 0.8125rem; }
.printer-add-page .printer-section-body .form-control {
    background: #fff; border: 1px solid #D5D9D9; color: #0F1111;
    font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-height: 2.25rem;
}
.printer-add-page .printer-section-body .form-control:focus {
    border-color: #FF9900; outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.printer-add-page .printer-section-body .input-group-addon {
    background: #F7F8F8; border: 1px solid #D5D9D9; color: #232F3E;
    font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-width: 2.25rem;
}
.printer-add-page .printer-section-body .help-block {
    font-size: 0.75rem; color: #565959; margin-top: 0.25rem;
}
.printer-add-page .select2-container--default .select2-selection {
    border-color: #D5D9D9;
}
.printer-add-page .select2-container--default.select2-container--focus .select2-selection {
    border-color: #FF9900;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}

/* Save button */
.printer-add-page .btn-printer-save {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #a88734 !important;
    color: #0f1111 !important;
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 8px;
}
.printer-add-page .btn-printer-save:hover {
    color: #0f1111 !important;
    opacity: 0.95;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page printer-add-page">
    <!-- Amazon banner -->
    <div class="amazon-printer-banner amazon-theme-banner">
        <div class="banner-inner">
            <div class="banner-icon"><i class="fa fa-print" aria-hidden="true"></i></div>
            <div class="banner-text">
                <h1 class="banner-title">@lang('printer.add_printer')</h1>
                <p class="banner-subtitle">Configure your printer connection and settings</p>
            </div>
        </div>
    </div>

    <section class="content">
        {!! Form::open(['url' => action([\App\Http\Controllers\PrinterController::class, 'store']), 'method' => 'post', 'id' => 'add_printer_form']) !!}

        <!-- Card: Printer Details -->
        <div class="printer-section-card">
            <div class="printer-section-header"><i class="fa fa-print"></i> Printer Details</div>
            <div class="printer-section-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('name', __('printer.name') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.printer_name_help')]); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Connection Settings -->
        <div class="printer-section-card">
            <div class="printer-section-header"><i class="fa fa-plug"></i> Connection Settings</div>
            <div class="printer-section-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('connection_type', __('printer.connection_type') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-exchange-alt"></i></span>
                                {!! Form::select('connection_type', $connection_types, null, ['class' => 'form-control select2', 'id' => 'connection_type']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('capability_profile', __('printer.capability_profile') . ':*') !!}
                            @show_tooltip(__('tooltip.capability_profile'))
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-cogs"></i></span>
                                {!! Form::select('capability_profile', $capability_profiles, null, ['class' => 'form-control select2']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('char_per_line', __('printer.character_per_line') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-text-width"></i></span>
                                {!! Form::number('char_per_line', 42, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.char_per_line_help')]); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Network Configuration (shown for Network connection) -->
        <div class="printer-section-card" id="network_config_card">
            <div class="printer-section-header"><i class="fa fa-network-wired"></i> Network Configuration</div>
            <div class="printer-section-body">
                <div class="row">
                    <div class="col-sm-12" id="ip_address_div">
                        <div class="form-group">
                            {!! Form::label('ip_address', __('printer.ip_address') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-server"></i></span>
                                {!! Form::text('ip_address', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.ip_address_help')]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="port_div">
                        <div class="form-group">
                            {!! Form::label('port', __('printer.port') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-plug"></i></span>
                                {!! Form::text('port', 9100, ['class' => 'form-control', 'required']); !!}
                            </div>
                            <span class="help-block">@lang('lang_v1.port_help')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Device Path (shown for Windows/Linux connection) -->
        <div class="printer-section-card" id="path_div">
            <div class="printer-section-header"><i class="fa fa-folder-open"></i> Device Path</div>
            <div class="printer-section-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('path', __('printer.path') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-folder"></i></span>
                                {!! Form::text('path', null, ['class' => 'form-control', 'required']); !!}
                            </div>
                            <span class="help-block">
                                <b>@lang('lang_v1.connection_type_windows'):</b> @lang('lang_v1.windows_type_help') <code>LPT1</code> (parallel) / <code>COM1</code> (serial).<br/>
                                <b>@lang('lang_v1.connection_type_linux'):</b> @lang('lang_v1.linux_type_help') <code>/dev/lp0</code> (parallel), <code>/dev/usb/lp1</code> (USB), <code>/dev/ttyUSB0</code> (USB-Serial), <code>/dev/ttyS0</code> (serial).
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="printer-section-card">
            <div class="printer-section-body text-center">
                <button type="submit" class="btn btn-printer-save">@lang('messages.save')</button>
            </div>
        </div>

        {!! Form::close() !!}
    </section>
</div>
@endsection


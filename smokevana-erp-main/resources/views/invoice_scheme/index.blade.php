@extends('layouts.app')
@section('title', __('invoice.invoice_settings'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* ========== Invoice Settings – Amazon look & feel ========== */
.invoice-settings-page {
    background: #EAEDED;
    min-height: 100%;
    padding-bottom: 2rem;
}
.invoice-settings-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e;
    border-radius: 10px;
    padding: 24px 32px !important;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.06);
    position: relative;
    overflow: hidden;
}
.invoice-settings-page .content-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    opacity: 0.9;
}
.invoice-settings-page .content-header h1 {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.5rem !important;
    color: #fff !important;
    margin: 0 !important;
}
.invoice-settings-page .content-header h1 .inv-header-icon { color: #ffffff !important; }
.invoice-settings-page .content-header h1 small {
    display: block;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #b8c4ce !important;
    margin-top: 4px;
}

/* Main card */
.invoice-settings-page .box-primary,
.invoice-settings-page .tw-mb-4,
.invoice-settings-page .components.widget > div:first-child {
    background: #ffffff !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 5px rgba(15, 17, 17, 0.08) !important;
    overflow: hidden;
}

/* Tabs – Amazon style */
.invoice-settings-page .nav-tabs {
    border-bottom: 2px solid #e5e7eb;
    padding: 0 20px 0 0;
    background: #f7f8f8;
    margin: 0;
}
.invoice-settings-page .nav-tabs > li > a {
    border: none !important;
    border-radius: 8px 8px 0 0;
    color: #565959 !important;
    font-weight: 500;
    padding: 14px 20px;
    margin-right: 4px;
    transition: all 0.2s ease;
}
.invoice-settings-page .nav-tabs > li > a:hover {
    background: rgba(255, 153, 0, 0.1);
    color: #0f1111 !important;
}
.invoice-settings-page .nav-tabs > li.active > a,
.invoice-settings-page .nav-tabs > li.active > a:hover {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    color: #0f1111 !important;
    border: none !important;
    font-weight: 600;
}
.invoice-settings-page .tab-content {
    padding: 20px 24px;
    background: #fff;
}

/* Section title + Add button – bar with vertical alignment */
.invoice-settings-page #tab_1 h4,
.invoice-settings-page #tab_2 h4 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff !important;
    padding: 14px 20px;
    margin: 0 0 16px 0 !important;
    border-radius: 8px;
    border-bottom: 2px solid #ff9900;
    font-weight: 600;
}
.invoice-settings-page #tab_1 h4 .pull-right,
.invoice-settings-page #tab_2 h4 .pull-right {
    margin: 0;
    flex-shrink: 0;
}
.invoice-settings-page #dynamic_button,
.invoice-settings-page #tab_2 .tw-dw-btn-primary {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important;
    color: #0f1111 !important;
    font-weight: 600;
    border-radius: 8px;
    padding: 8px 18px;
    margin: 0 !important;
    box-shadow: 0 2px 5px rgba(15, 17, 17, 0.15);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    vertical-align: middle;
}
.invoice-settings-page #dynamic_button:hover,
.invoice-settings-page #tab_2 .tw-dw-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15, 17, 17, 0.2);
    color: #0f1111 !important;
}

/* Table */
.invoice-settings-page #invoice_table_wrapper { padding: 0; }
.invoice-settings-page #invoice_table thead th {
    background: #232f3e !important;
    color: #fff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
    font-weight: 600;
    font-size: 13px;
}
.invoice-settings-page #invoice_table tbody td {
    padding: 12px 14px;
    color: #0f1111;
    border-color: #e5e7eb;
    font-size: 13px;
}
.invoice-settings-page #invoice_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.invoice-settings-page #invoice_table tbody tr:hover td { background: #fff8e7 !important; }
.invoice-settings-page #invoice_table .label-success {
    background: #067d62 !important;
    color: #fff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

/* Action buttons */
.invoice-settings-page #invoice_table .table-action-btn-edit {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border: 1px solid #a88734 !important;
    color: #0f1111 !important;
    border-radius: 6px;
    font-weight: 500;
}
.invoice-settings-page #invoice_table .delete_invoice_button {
    background: #c45500 !important;
    border: 1px solid #9a3e00 !important;
    color: #fff !important;
    border-radius: 6px;
}
.invoice-settings-page #invoice_table .set_default_invoice {
    background: #067d62 !important;
    border: 1px solid #056952 !important;
    color: #fff !important;
    border-radius: 6px;
}
.invoice-settings-page #invoice_table .tw-dw-btn-accent[disabled] {
    background: #37475a !important;
    color: #b8c4ce !important;
    border-radius: 6px;
}
.invoice-settings-page #invoice_table tbody .tw-dw-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }

/* DataTables search / length / pagination */
.invoice-settings-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    padding: 8px 12px;
}
.invoice-settings-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
}
.invoice-settings-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important;
    color: #0f1111 !important;
}
.invoice-settings-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: #ff9900;
    background: #fff8e7 !important;
}

/* Tab 2 – Invoice Layouts cards */
.invoice-settings-page .icon-link {
    background: #fff;
    border: 1px solid #D5D9D9;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
    margin-bottom: 16px;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.invoice-settings-page .icon-link:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #ff9900;
}
.invoice-settings-page .icon-link a {
    color: #0066c0 !important;
    font-weight: 600;
    text-decoration: none !important;
}
.invoice-settings-page .icon-link a:hover { color: #c45500 !important; }
.invoice-settings-page .icon-link .fa-file-alt { color: #37475a; margin-bottom: 8px; }
.invoice-settings-page .icon-link .badge.bg-green {
    background: #067d62 !important;
    color: #fff;
    margin-top: 8px;
    border-radius: 6px;
}
.invoice-settings-page .icon-link .link-des {
    font-size: 12px;
    color: #565959;
    margin-top: 8px;
    line-height: 1.4;
}

/* Modals */
.invoice-settings-page .invoice_modal .modal-content,
.invoice-settings-page .invoice_edit_modal .modal-content {
    border: 1px solid #D5D9D9;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(15, 17, 17, 0.15);
}
.invoice-settings-page .invoice_modal .modal-header,
.invoice-settings-page .invoice_edit_modal .modal-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    border-bottom: 2px solid #ff9900 !important;
    color: #fff !important;
}
.invoice-settings-page .invoice_modal .modal-title,
.invoice-settings-page .invoice_edit_modal .modal-title { color: #fff !important; }
.invoice-settings-page .invoice_modal .modal-header .close,
.invoice-settings-page .invoice_edit_modal .modal-header .close { color: #fff !important; opacity: 0.9; }
.invoice-settings-page .invoice_modal .modal-header .close:hover,
.invoice-settings-page .invoice_edit_modal .modal-header .close:hover { color: #ff9900 !important; }
</style>
@endsection

@section('content')
<div class="admin-amazon-page invoice-settings-page">
<section class="content-header">
    <h1>
        <i class="fa fa-file-invoice inv-header-icon"></i>
        @lang( 'invoice.invoice_settings' )
        <small>@lang( 'invoice.manage_your_invoices' )</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget')
            <div class="">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">@lang('invoice.invoice_schemes')</a></li>
                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">@lang('invoice.invoice_layouts')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>@lang( 'invoice.all_your_invoice_schemes' )
                                    <button id="dynamic_button" class="tw-dw-btn pull-right btn-modal"
                                        data-href="{{ action([\App\Http\Controllers\InvoiceSchemeController::class, 'create']) }}"
                                        data-container=".invoice_modal">
                                        <i class="fa fa-plus"></i> @lang('messages.add')
                                    </button>
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="invoice_table">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'invoice.name' ) @show_tooltip(__('tooltip.invoice_scheme_name'))</th>
                                                <th>@lang( 'invoice.prefix' ) @show_tooltip(__('tooltip.invoice_scheme_prefix'))</th>
                                                <th>@lang( 'invoice.number_type' ) @show_tooltip(__('invoice.number_type_tooltip'))</th>
                                                <th>@lang( 'invoice.start_number' ) @show_tooltip(__('tooltip.invoice_scheme_start_number'))</th>
                                                <th>@lang( 'invoice.invoice_count' ) @show_tooltip(__('tooltip.invoice_scheme_count'))</th>
                                                <th>@lang( 'invoice.total_digits' ) @show_tooltip(__('tooltip.invoice_scheme_total_digits'))</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>@lang( 'invoice.all_your_invoice_layouts' )
                                    <a class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm pull-right" href="{{ action([\App\Http\Controllers\InvoiceLayoutController::class, 'create']) }}">
                                        <i class="fa fa-plus"></i> @lang( 'messages.add' )
                                    </a>
                                </h4>
                            </div>
                            <div class="col-md-12">
                                @foreach( $invoice_layouts as $layout)
                                <div class="col-md-3">
                                    <div class="icon-link">
                                        <a href="{{ action([\App\Http\Controllers\InvoiceLayoutController::class, 'edit'], [$layout->id]) }}">
                                            <i class="fa fa-file-alt fa-4x"></i>
                                            {{ $layout->name }}
                                        </a>
                                        @if( $layout->is_default )
                                        <span class="badge bg-green">@lang("barcode.default")</span>
                                        @endif
                                        @if($layout->locations->count())
                                        <span class="link-des">
                                            <b>@lang('invoice.used_in_locations'):</b><br>
                                            @foreach($layout->locations as $location)
                                            {{ $location->name }}@if (!$loop->last), @endif
                                            @endforeach
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @if( $loop->iteration % 4 == 0 )
                                <div class="clearfix"></div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="modal fade invoice_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade invoice_edit_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
</div>
@endsection
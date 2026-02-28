@extends('layouts.app')
@section('title', __('lang_v1.import_contacts'))

@section('css')
<style>
    /* ========== Page: Amazon look & feel ========== */
    .import-contacts-page {
        background: #EAEDED;
        min-height: 100%;
        padding-bottom: 2rem;
    }
    .import-contacts-page .content-header {
        margin-bottom: 0;
    }

    /* ----- Banner: gradient, orange accent, depth ----- */
    .import-contacts-header-banner {
        background: linear-gradient(180deg, #37475a 0%, #232f3e 100%);
        border-radius: 10px;
        padding: 24px 32px;
        margin-bottom: 20px;
        border: 1px solid #4a5d6e;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        position: relative;
        overflow: hidden;
    }
    .import-contacts-header-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff9900, #e47911);
        opacity: 0.9;
    }
    .import-contacts-header-content {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .import-contacts-header-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
        letter-spacing: -0.02em;
    }
    .import-contacts-header-title i {
        font-size: 1.5rem;
        color: #ff9900 !important;
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));
    }
    .import-contacts-header-subtitle {
        font-size: 13px;
        color: #b8c4ce;
        margin: 0;
        line-height: 1.4;
    }

    /* ----- Quick actions card: Amazon CTA style ----- */
    .import-contacts-page .box-primary {
        background: #ffffff !important;
        border: 1px solid #D5D9D9 !important;
        border-radius: 10px !important;
        box-shadow: 0 2px 5px rgba(15, 17, 17, 0.08) !important;
        padding: 24px 28px !important;
        margin-bottom: 20px !important;
    }
    .import-contacts-page .import-actions-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }
    .import-contacts-page .import-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(15, 17, 17, 0.15);
    }
    .import-contacts-page .import-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 17, 17, 0.2);
    }
    .import-contacts-page .import-action-btn--primary {
        background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%);
        color: #0f1111;
        border: 1px solid #a88734;
    }
    .import-contacts-page .import-action-btn--primary:hover {
        background: linear-gradient(to bottom, #f7ca6b 0%, #f0b83d 5%, #e47911 100%);
    }
    .import-contacts-page .import-action-btn--secondary {
        background: linear-gradient(to bottom, #f7f8f8 0%, #e5e7eb 100%);
        color: #0f1111;
        border: 1px solid #D5D9D9;
    }
    .import-contacts-page .import-action-btn--secondary:hover {
        background: #e8eaed;
        border-color: #ff9900;
    }
    .import-contacts-page .import-action-btn i {
        font-size: 1.1rem;
    }

    /* ----- Amazon "Important" callout ----- */
    .import-contacts-important {
        background: linear-gradient(to bottom, #fff8e7 0%, #fef3d9 100%);
        border: 1px solid #f0c14b;
        border-radius: 8px;
        padding: 14px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(240, 193, 75, 0.2);
    }
    .import-contacts-important i {
        color: #c45500;
        font-size: 1.25rem;
        margin-top: 1px;
    }
    .import-contacts-important strong {
        color: #0f1111;
        display: block;
        margin-bottom: 2px;
    }
    .import-contacts-important span {
        color: #565959;
        font-size: 13px;
        line-height: 1.4;
    }

    /* ----- Instructions card: white card, warm table ----- */
    .import-contacts-page .box-primary .box-header,
    .import-contacts-page .tw-mb-4 .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border-bottom: 2px solid #ff9900 !important;
        border-radius: 10px 10px 0 0 !important;
        padding: 16px 24px !important;
    }
    .import-contacts-page .box-title {
        color: #fff !important;
        font-weight: 600;
    }
    /* Instructions box: let table stretch full width by breaking out of widget padding (sm:tw-px-5 = 20px) */
    .import-contacts-page .instructions-box .tw-flow-root {
        overflow: visible;
    }
    .import-contacts-page .instructions-box .instructions-card-body {
        background: #ffffff !important;
        padding: 20px 24px !important;
        border: 1px solid #D5D9D9;
        border-top: none;
        border-radius: 0 0 10px 10px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        margin-left: -20px;
        margin-right: -20px;
        width: calc(100% + 40px);
        max-width: none;
    }
    .import-contacts-page .instructions-card-body .table-striped {
        width: 100% !important;
        max-width: 100%;
        table-layout: auto;
    }
    /* Hide Column Number column (first column) */
    .import-contacts-page .table-striped th:first-child,
    .import-contacts-page .table-striped td:first-child {
        display: none !important;
    }
    .import-contacts-page .instructions-intro {
        color: #0f1111;
        margin-bottom: 16px;
        line-height: 1.5;
    }
    .import-contacts-page .table-striped {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
    .import-contacts-page .table-striped thead th {
        background: #f7f8f8 !important;
        color: #0f1111 !important;
        border-color: #D5D9D9 !important;
        padding: 12px 16px !important;
        font-weight: 600;
        font-size: 13px;
    }
    .import-contacts-page .table-striped tbody td {
        padding: 12px 16px;
        border-color: #e5e7eb;
        color: #0f1111;
        font-size: 13px;
    }
    .import-contacts-page .table-striped tbody tr:nth-child(even) {
        background: #f9fafb !important;
    }
    .import-contacts-page .table-striped tbody tr:hover {
        background: #fff8e7 !important;
    }
    /* Hide optional instruction rows (not deleted, only hidden) */
    .import-contacts-page .table-striped tr.import-instruction-optional {
        display: none !important;
    }
    .import-contacts-page .col-no-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        background: #37475a;
        color: #fff;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }
    .import-contacts-page .text-muted {
        color: #565959 !important;
    }
    .import-contacts-page small.text-muted {
        font-size: 12px;
    }

    /* ----- Modals: Amazon header + tip ----- */
    .import-contacts-page #importModal .modal-content,
    .import-contacts-page #exportModal .modal-content {
        border: 1px solid #D5D9D9;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(15, 17, 17, 0.15);
    }
    /* Ensure modals appear above backdrop so they are visible and not just a dim screen */
    #importModal.modal,
    #exportModal.modal {
        z-index: 1060 !important;
        padding-right: 0 !important;
    }
    #importModal .modal-dialog,
    #exportModal .modal-dialog {
        z-index: 1061 !important;
        position: relative;
        margin: 30px auto;
        visibility: visible !important;
        opacity: 1 !important;
    }
    /* Ensure modal backdrop doesn't cover the modal dialog */
    .import-contacts-page .modal-backdrop {
        z-index: 1040 !important;
    }
    /* Force modal and its content to be visible when open */
    #importModal.modal.in,
    #importModal.modal.show,
    #exportModal.modal.in,
    #exportModal.modal.show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    #importModal.modal.in .modal-dialog,
    #importModal.modal.show .modal-dialog,
    #exportModal.modal.in .modal-dialog,
    #exportModal.modal.show .modal-dialog {
        visibility: visible !important;
        opacity: 1 !important;
        transform: none !important;
    }
    .import-contacts-page #importModal .modal-header,
    .import-contacts-page #exportModal .modal-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-bottom: 2px solid #ff9900 !important;
        padding: 18px 24px !important;
    }
    .import-contacts-page #importModal .modal-title,
    .import-contacts-page #exportModal .modal-title {
        color: #fff !important;
        font-weight: 600;
    }
    .import-contacts-page #importModal .modal-header .close,
    .import-contacts-page #exportModal .modal-header .close {
        color: #fff !important;
        opacity: 0.9;
        text-shadow: none;
    }
    .import-contacts-page #importModal .modal-header .close:hover,
    .import-contacts-page #exportModal .modal-header .close:hover {
        color: #ff9900 !important;
    }
    .import-contacts-page .modal-subtitle {
        color: #b8c4ce;
        font-size: 12px;
        margin-top: 4px;
    }
    .import-contacts-page .modal-body {
        background: #fff;
        padding: 24px;
    }
    .import-contacts-page .modal-footer {
        background: #f7f8f8 !important;
        border-top: 1px solid #e5e7eb;
        padding: 16px 24px;
    }
    .import-contacts-page .modal-footer .tw-dw-btn-primary {
        background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
        border: 1px solid #a88734 !important;
        color: #0f1111 !important;
        font-weight: 600;
        border-radius: 8px;
    }
    .import-contacts-page .modal-footer .tw-dw-btn-neutral {
        background: #f7f8f8 !important;
        border: 1px solid #D5D9D9 !important;
        color: #0f1111 !important;
        border-radius: 8px;
    }
    .import-contacts-page .modal-footer .tw-dw-btn-neutral:hover {
        background: #e8eaed !important;
        border-color: #ff9900 !important;
    }
    .import-contacts-page input[type="file"].form-control {
        padding: 10px 14px;
        border: 1px solid #D5D9D9;
        border-radius: 6px;
        background: #fff;
    }

    /* ----- Alert ----- */
    .import-contacts-page .alert-danger {
        border-radius: 8px;
        border: 1px solid #c45500;
        background: #fff4e5;
    }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div class="import-contacts-header-banner amazon-theme-banner">
        <div class="import-contacts-header-content">
            <h1 class="import-contacts-header-title">
                <i class="fas fa-file-import"></i>
                @lang('lang_v1.import_contacts')
            </h1>
            <p class="import-contacts-header-subtitle">
                Bulk import customers and suppliers from Excel. Download the template and follow the column instructions.
            </p>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content import-contacts-page">
    
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif
    
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="import-actions-row">
                    <button type="button" class="import-action-btn import-action-btn--primary" data-toggle="modal" data-target="#importModal">
                        <i class="fa fa-upload"></i> Import Contacts
                    </button>
                    <a href="{{ asset('files/import_contacts_csv_template.xls') }}" class="import-action-btn import-action-btn--secondary" download>
                        <i class="fa fa-download"></i> @lang('lang_v1.download_template_file')
                    </a>
                    <button type="button" class="import-action-btn import-action-btn--secondary" data-toggle="modal" data-target="#exportModal">
                        <i class="fa fa-download"></i> Export Contacts
                    </button>
                </div>
            @endcomponent

            <!-- Import Modal (data-backdrop/data-keyboard so dim can be dismissed by click or Escape) -->
            <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" data-backdrop="true" data-keyboard="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="importModalLabel">Import Contacts</h4>
                            <p class="modal-subtitle">Upload an Excel file (.xls). Column order must match the template.</p>
                        </div>
                        {!! Form::open(['url' => action([\App\Http\Controllers\ContactController::class, 'postImportContacts']), 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'import_form']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                {!! Form::label('contacts_csv', __( 'product.file_to_import' ) . ':*') !!}
                                {!! Form::file('contacts_csv', ['accept'=> '.xls', 'required' => 'required', 'class' => 'form-control', 'id' => 'contacts_csv']) !!}
                                <small class="text-muted">Select the Excel file to import</small>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('location_id', __('business.business_location') . ':') !!}
                                        {!! Form::select('location_id', $business_locations ?? [], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'import_location_id', 'placeholder' => __('lang_v1.all')]); !!}
                                        <small class="text-muted">Default location for contacts if not specified in file</small>
                                    </div>
                                </div>
                                
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                                <i class="fa fa-upload"></i> Import
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <!-- Export Modal (data-backdrop/data-keyboard so dim can be dismissed by click or Escape) -->
            <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" data-backdrop="true" data-keyboard="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="exportModalLabel">Export Contacts</h4>
                            <p class="modal-subtitle">Download contacts as Excel. Choose location and type to filter.</p>
                        </div>
                        {!! Form::open(['url' => action([\App\Http\Controllers\ContactController::class, 'exportContacts']), 'method' => 'post', 'id' => 'export_form', 'target' => 'export_download_frame']) !!}
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                {!! Form::label('export_location_id', __('business.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations ?? [], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'export_location_id', 'placeholder' => __('lang_v1.all')]); !!}
                                <small class="text-muted">Select location to export contacts from (optional)</small>
                            </div>
                            <div class="form-group">
                                {!! Form::label('export_contact_type', __('lang_v1.contact_type') . ':') !!}
                                {!! Form::select('contact_type', ['all' => __('lang_v1.all'), 'customer' => __('contact.customer'), 'supplier' => __('report.supplier')], 'all', ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'export_contact_type']); !!}
                                <small class="text-muted">Select contact type to export</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                                <i class="fa fa-download"></i> Export
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <iframe name="export_download_frame" id="export_download_frame" style="display:none" title="Export download"></iframe>
    <div class="import-contacts-important">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>@lang('lang_v1.instruction_line1')</strong>
            <span>@lang('lang_v1.instruction_line2')</span>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary instructions-box', 'title' => __('lang_v1.instructions')])
                <div class="instructions-card-body">
                <p class="instructions-intro"><strong>@lang('lang_v1.instruction_line1')</strong> @lang('lang_v1.instruction_line2')</p>
                <table class="table table-striped">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>@lang('contact.contact_type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>{!! __('lang_v1.import_contact_type_ins') !!}</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>2</td>
                        <td>@lang('business.prefix') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('business.first_name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>4</td>
                        <td>@lang('lang_v1.middle_name') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>5</td>
                        <td>@lang('business.last_name') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>@lang('business.business_name') <br><small class="text-muted">(@lang('lang_v1.required_if_supplier'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>7</td>
                        <td>@lang('lang_v1.contact_id') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.contact_id_ins')</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>8</td>
                        <td>@lang('contact.tax_no') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>9</td>
                        <td>@lang('lang_v1.opening_balance') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>10</td>
                        <td>@lang('contact.pay_term') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>11</td>
                        <td>@lang('contact.pay_term_period') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td><strong>@lang('lang_v1.pay_term_period_ins')</strong></td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>12</td>
                        <td>@lang('lang_v1.credit_limit') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>13</td>
                        <td>@lang('business.email') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td>@lang('contact.mobile') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>15</td>
                        <td>@lang('contact.alternate_contact_number') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>16</td>
                        <td>@lang('contact.landline') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>17</td>
                        <td>@lang('business.city') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>18</td>
                        <td>@lang('business.state') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>19</td>
                        <td>@lang('business.country') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>20</td>
                        <td>@lang('lang_v1.address_line_1') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>21</td>
                        <td>@lang('lang_v1.address_line_2') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>22</td>
                        <td>@lang('business.zip_code') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>23</td>
                        <td>@lang('lang_v1.dob') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.dob_ins') ({{\Carbon::now()->format('Y-m-d')}})</td>
                    </tr>
                   
                    <tr>
                        <td>24</td>
                        <td>Password <small class="text-muted">(Required if you have customer login)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>25</td>
                        <td>Is Approved <small class="text-muted">(Permission to login at ecom)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>26</td>
                        <td>Customer Username <small class="text-muted">(Required if you have customer login)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>27</td>
                        <td>Shipping First Name <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>28</td>
                        <td>Shipping Last Name <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>29</td>
                        <td>Shipping Company <small class="text-muted">(Optional)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>30</td>
                        <td>Shipping Address 1 <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>31</td>
                        <td>Shipping Address 2 <small class="text-muted">(Optional)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>32</td>
                        <td>Shipping City <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>33</td>
                        <td>Shipping State <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>34</td>
                        <td>Shipping Zip <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>35</td>
                        <td>Shipping Country <small class="text-muted">(Required if you have shipstations)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>36</td>
                        <td>Contact Status <small class="text-muted">(keep it inactive or active)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>37</td>
                        <td>Customer Group <small class="text-muted">(Optional, if you have customer group fill ids like 1,2,3..)</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>38</td>
                        <td>Location ID <small class="text-muted">(Optional)</small></td>
                        <td>Business location ID for the contact (1 = B2B, 2 = B2C)</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>39</td>
                        <td>Brand ID <small class="text-muted">(Optional)</small></td>
                        <td>Brand ID for the contact</td>
                    </tr>
                </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@stop

@section('javascript')
<script>
$(document).ready(function() {
    // Fix: Remove any leftover modal backdrop when Import Contacts page loads (prevents dim page)
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
    $('body').css({ 'padding-right': '', 'overflow': '' });

    // Load brands based on selected location in import modal
    $(document).on('change', '#import_location_id', function() {
        var locationId = $(this).val();
        var brandSelect = $('#import_brand_id');
        
        brandSelect.empty().append('<option value="">@lang('lang_v1.all')</option>');
        
        if (locationId) {
            $.ajax({
                url: '/get-brands-for-location/' + locationId,
                method: 'GET',
                success: function(response) {
                    if (response && response.length > 0) {
                        $.each(response, function(index, brand) {
                            brandSelect.append('<option value="' + brand.id + '">' + brand.name + '</option>');
                        });
                        brandSelect.trigger('change');
                    }
                },
                error: function() {
                    console.log('Error loading brands');
                }
            });
        }
    });

    // Ensure modals are visible when shown (fix for dim screen issue)
    $('#importModal').on('show.bs.modal', function () {
        $(this).css({ 'display': 'block', 'visibility': 'visible', 'opacity': '1' });
        $(this).addClass('in show');
        $(this).find('.modal-dialog').css({ 'visibility': 'visible', 'opacity': '1' });
    });
    $('#importModal').on('shown.bs.modal', function () {
        $(this).css({ 'display': 'block', 'visibility': 'visible', 'opacity': '1' });
        $(this).addClass('in show');
        $(this).find('.modal-dialog').css({ 'visibility': 'visible', 'opacity': '1' });
        // Ensure this modal is on top of backdrop
        $('.modal-backdrop').css('z-index', 1040);
        $(this).css('z-index', 1060);
        $(this).find('.modal-dialog').css('z-index', 1061);
        $('#import_location_id, #import_brand_id').select2({
            dropdownParent: $('#importModal')
        });
    });
    $('#importModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').css({ 'padding-right': '', 'overflow': '' });
    });
    // Close Import modal by removing visibility and backdrop (works even if Bootstrap modal('hide') fails)
    function closeImportModal() {
        $('#importModal').removeClass('in show').css({ 'display': 'none', 'visibility': 'hidden', 'opacity': '0' });
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').css({ 'padding-right': '', 'overflow': '' });
    }
    $(document).on('click', '#importModal .close, #importModal [data-dismiss="modal"]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        closeImportModal();
    });
    $('#exportModal').on('show.bs.modal', function () {
        $(this).css({ 'display': 'block', 'visibility': 'visible', 'opacity': '1' });
        $(this).addClass('in show');
        $(this).find('.modal-dialog').css({ 'visibility': 'visible', 'opacity': '1' });
    });
    $('#exportModal').on('shown.bs.modal', function () {
        $(this).css({ 'display': 'block', 'visibility': 'visible', 'opacity': '1' });
        $(this).addClass('in show');
        $(this).find('.modal-dialog').css({ 'visibility': 'visible', 'opacity': '1' });
        $('.modal-backdrop').css('z-index', 1040);
        $(this).css('z-index', 1060);
        $(this).find('.modal-dialog').css('z-index', 1061);
        $('#export_location_id, #export_contact_type').select2({
            dropdownParent: $('#exportModal')
        });
    });
    $('#exportModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').css({ 'padding-right': '', 'overflow': '' });
    });
    function closeExportModal() {
        $('#exportModal').removeClass('in show').css({ 'display': 'none', 'visibility': 'hidden', 'opacity': '0' });
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').css({ 'padding-right': '', 'overflow': '' });
    }
    $(document).on('click', '#exportModal .close, #exportModal [data-dismiss="modal"]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        closeExportModal();
    });

    // Export contacts: download file via fetch so the page is not replaced
    $('#export_form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var url = $(form).attr('action');
        var submitBtn = $(form).find('button[type="submit"]');
        var originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var headers = { 'X-Requested-With': 'XMLHttpRequest' };
        if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');

        fetch(url, {
            method: 'POST',
            body: new FormData(form),
            headers: headers
        }).then(function(response) {
            var contentType = (response.headers.get('Content-Type') || '').toLowerCase();
            if (!response.ok) {
                return response.text().then(function(text) {
                    var msg = 'Export failed (' + response.status + ').';
                    try {
                        var j = JSON.parse(text);
                        if (j.message) msg = j.message;
                    } catch (e) {}
                    throw new Error(msg);
                });
            }
            if (contentType.indexOf('html') !== -1) {
                return response.text().then(function(text) {
                    throw new Error('Server returned an error page. Please try again.');
                });
            }
            var disposition = response.headers.get('Content-Disposition');
            var filename = 'contacts-export.xlsx';
            if (disposition && disposition.indexOf('filename=') !== -1) {
                var match = disposition.match(/filename[*]?=["']?([^"';]+)/i);
                if (match && match[1]) filename = match[1].trim();
            }
            return response.blob().then(function(blob) {
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(blob);
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(a.href);
            });
        }).then(function() {
            closeExportModal();
        }).catch(function(err) {
            closeExportModal();
            alert('Export failed. Please try again or contact support.\n\n' + (err.message || err));
        }).finally(function() {
            submitBtn.prop('disabled', false).html(originalHtml);
        });
    });
});
</script>
@stop
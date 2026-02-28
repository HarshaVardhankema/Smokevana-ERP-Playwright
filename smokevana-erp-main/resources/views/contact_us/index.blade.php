@extends('layouts.app')

@section('title', 'Contact Us')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .amazon-contact-page { background: #EAEDED; min-height: calc(100vh - 120px); padding: 20px 0; padding-bottom: 2rem; }
    .amazon-contact-page .page-header-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        border-radius: 10px;
        padding: 24px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.06);
    }
    .amazon-contact-page .page-header-card h1 {
        color: #fff; font-size: 24px; font-weight: 700; margin: 0;
        display: flex; align-items: center; gap: 14px;
    }
    .amazon-contact-page .page-header-card h1 .icon-box {
        background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; display: flex;
        width: 52px; height: 52px; min-width: 52px; align-items: center; justify-content: center; font-size: 24px;
    }
    .amazon-contact-page .page-header-subtitle { font-size: 13px; color: rgba(255,255,255,0.78); margin: 4px 0 0 0; }
    .amazon-contact-page .content-card {
        border-radius: 10px; overflow: hidden; margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #D5D9D9;
    }
    .amazon-contact-page .content-card .box { background: transparent !important; border: none !important; }
    .amazon-contact-page .content-card .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important; border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .amazon-contact-page .content-card .box-header::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900;
    }
    .amazon-contact-page .content-card .box-title { color: #fff !important; font-weight: 600; font-size: 1rem; }
    .amazon-contact-page .content-card .box-body { background: #f7f8f8 !important; padding: 1rem 1.25rem; }
    .amazon-contact-page #contact_us_table thead th {
        background: #e8e9e9 !important; color: #232F3E !important;
        border-bottom: 2px solid #D5D9D9; padding: 14px 16px;
        font-weight: 600; font-size: 12px; text-transform: uppercase;
    }
    .amazon-contact-page #contact_us_table tbody td { padding: 14px 16px; color: #0F1111; background: #fff; }
    .amazon-contact-page #contact_us_table tbody tr:hover td { background: #f7f8f8 !important; }
    .amazon-contact-page .dt-buttons .btn,
    .amazon-contact-page .dt-buttons button {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #fff !important; border-color: #C7511F !important;
        border-radius: 6px; font-weight: 600;
    }
    .amazon-contact-page .dt-buttons .btn:hover,
    .amazon-contact-page .dt-buttons button:hover {
        color: #fff !important; opacity: 0.95;
    }
    .amazon-contact-page .dataTables_filter input {
        border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
    }
    .amazon-contact-page .dataTables_filter input:focus {
        border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .amazon-contact-page .dataTables_length select { border: 1px solid #D5D9D9; border-radius: 6px; }
    .amazon-contact-page .dropdown .btn-default { background: #fff !important; color: #37475a !important; border: 1px solid #D5D9D9; border-radius: 6px; }
    /* Action button: always visible, Amazon-style pill with clear hover/open states */
    .amazon-contact-page #contact_us_table .btn-group.dropdown,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown {
        min-width: 100px;
    }
    .amazon-contact-page #contact_us_table .btn-group.dropdown .dropdown-toggle,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown .dropdown-toggle {
        background: linear-gradient(to bottom, #37475a 0%, #232f3e 100%) !important;
        color: #fff !important;
        border: 1px solid #232f3e !important;
        border-radius: 20px !important;
        padding: 6px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
        transition: all 0.2s ease !important;
    }
    .amazon-contact-page #contact_us_table .btn-group.dropdown .dropdown-toggle .caret,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown .dropdown-toggle .caret {
        margin-left: 6px; border-top-color: #fff; opacity: 1;
    }
    .amazon-contact-page #contact_us_table .btn-group.dropdown .dropdown-toggle:hover,
    .amazon-contact-page #contact_us_table .btn-group.dropdown .dropdown-toggle:focus,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown .dropdown-toggle:hover,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown .dropdown-toggle:focus {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #fff !important;
        border-color: #C7511F !important;
        box-shadow: 0 2px 8px rgba(255,153,0,0.35) !important;
        transform: translateY(-1px);
    }
    .amazon-contact-page #contact_us_table .btn-group.dropdown .dropdown-toggle:hover .caret,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown .dropdown-toggle:hover .caret,
    .amazon-contact-page #contact_us_table .btn-group.dropdown.open .dropdown-toggle .caret,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown.open .dropdown-toggle .caret {
        border-top-color: #fff;
    }
    .amazon-contact-page #contact_us_table .btn-group.dropdown.open .dropdown-toggle,
    .amazon-contact-page #contact_us_table_wrapper .btn-group.dropdown.open .dropdown-toggle {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #fff !important;
        border-color: #C7511F !important;
        box-shadow: 0 2px 8px rgba(255,153,0,0.35) !important;
    }
    .amazon-contact-page #contact_us_table .dropdown-menu,
    .amazon-contact-page #contact_us_table_wrapper .dropdown-menu {
        border-radius: 8px; border: 1px solid #D5D9D9; box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .amazon-contact-page #contact_us_table .dropdown-menu li a:hover,
    .amazon-contact-page #contact_us_table_wrapper .dropdown-menu li a:hover {
        background: #f7f8f8; color: #C7511F;
    }
</style>
@endsection

@section('content')
<div class="amazon-contact-page">
    <div class="container-fluid">
        <div class="page-header-card amazon-theme-banner">
            <h1>
                <div class="icon-box"><i class="fas fa-envelope"></i></div>
                Contact Us
            </h1>
            <p class="page-header-subtitle">Manage your web contact messages</p>
        </div>

        <div class="content-card">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('Contact Us')])
    
    <div class="table-responsive">
        <table class="table nowrap table-bordered table-striped" id="contact_us_table">
            <thead>
                <tr>
                    <th style="min-width: 150px">{{ __('Date') }}</th>
                    <th style="min-width: 100px">{{ __('Name') }}</th>
                    <th style="min-width: 100px">{{ __('Email') }}</th>
                    <th style="min-width: 100px">{{ __('Subject') }}</th>
                    <th style="min-width: 100px">{{ __('Message') }}</th>
                    <th style="min-width: 150px">{{ __('Send Mail') }}</th>
                    <th style="min-width: 100px">{{ __('Location') }}</th>
                    <th style="min-width: 120px">Website</th>
                    <th style="min-width: 100px">{{ __('Action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('report.filters')</h4>
            </div>
            <div class="modal-body" style="padding: 0px; margin-top: 10px;">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('inventory_type', 'Inventory Type:') !!}
                        {!! Form::select(
                            'inventory_type',
                            ['all' => 'All', 'b2b' => 'B2B Inventory', 'b2c' => 'B2C Inventory'],
                            null,
                            [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'contact_us_filter_inventory_type',
                                'placeholder' => 'All',
                            ],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-3" id="brand_filter_container" style="display: none;">
                    <div class="form-group">
                        {!! Form::label('brand_id', 'Brand:') !!}
                        {!! Form::select('brand_id', $b2c_brands ?? [], null, [
                            'class' => 'form-control select2',
                            'style' => 'width:100%',
                            'id' => 'contact_us_filter_brand_id',
                            'placeholder' => 'Select Brand',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade mail_writer_popup" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade contactShowModal" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" role="dialog"></div>
@endsection
@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize select2 when modal is shown
        $('#filterModal').on('shown.bs.modal', function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                dropdownParent: $('#filterModal')
            });
        });

        // Show/hide brand filter based on inventory type selection
        $(document).on('change', '#contact_us_filter_inventory_type', function() {
            var inventoryType = $(this).val();
            if (inventoryType === 'b2c') {
                $('#brand_filter_container').slideDown(300);
            } else {
                $('#brand_filter_container').slideUp(300);
                $('#contact_us_filter_brand_id').val('').trigger('change');
            }
        });

        // Auto-reload DataTable when filter values change (like products)
        $(document).on('change', '#contact_us_filter_inventory_type, #contact_us_filter_brand_id', function() {
            // Check if contact_us_table exists in global scope
            if (typeof window.contact_us_table !== 'undefined' && window.contact_us_table) {
                window.contact_us_table.ajax.reload();
            } else {
                // Try to find the table by ID
                var table = $('#contact_us_table').DataTable();
                if (table) {
                    table.ajax.reload();
                }
            }
        });

        // Clean up select2 when modal is hidden
        $('#filterModal').on('hidden.bs.modal', function() {
            // Safely destroy select2 instances
            $('.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    try {
                        $(this).select2('destroy');
                    } catch (e) {
                        console.log('Select2 destroy error (safe to ignore):', e);
                    }
                }
            });
        });
    });
</script>
@endsection
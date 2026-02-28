@extends('layouts.app')

@section('title', 'News Letter')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* ========== News Letter Page – Amazon theme ========== */
.amazon-newsletter-page { background: #EAEDED; min-height: calc(100vh - 120px); padding: 20px 0; padding-bottom: 2rem; }
.amazon-newsletter-page .page-header-card {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e;
    border-radius: 10px;
    padding: 24px 32px !important;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.amazon-newsletter-page .page-header-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    opacity: 0.9;
}
.amazon-newsletter-page .page-header-card h1 {
    color: #fff !important; font-size: 1.5rem !important; font-weight: 700; margin: 0;
    display: flex; align-items: center; gap: 14px;
}
.amazon-newsletter-page .page-header-card h1 .icon-box {
    background: rgba(255,255,255,0.15); border-radius: 10px; padding: 10px; display: flex;
    color: #FF9900;
}
.amazon-newsletter-page .page-header-subtitle { font-size: 13px; color: #b8c4ce !important; margin: 4px 0 0 0; }

/* Content card – Amazon style */
.amazon-newsletter-page .content-card {
    background: #fff !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 5px rgba(15,17,17,0.08) !important;
    overflow: hidden; margin-bottom: 24px;
}
.amazon-newsletter-page .content-card .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important;
    padding: 14px 20px !important;
    border-bottom: 2px solid #ff9900 !important;
}
.amazon-newsletter-page .content-card .box-title { color: #fff !important; font-weight: 600 !important; }
.amazon-newsletter-page .content-card .box-title i { color: #FF9900 !important; }

/* Table */
.amazon-newsletter-page #news_letter_table thead th {
    background: #232f3e !important;
    color: #fff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
    font-weight: 600;
    font-size: 13px;
}
.amazon-newsletter-page #news_letter_table tbody td {
    padding: 12px 14px;
    color: #0f1111;
    border-color: #e5e7eb;
    font-size: 13px;
}
.amazon-newsletter-page #news_letter_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.amazon-newsletter-page #news_letter_table tbody tr:hover td { background: #fff8e7 !important; }

/* DataTables buttons – Amazon orange */
.amazon-newsletter-page .dt-buttons .btn,
.amazon-newsletter-page .dt-buttons button {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    color: #0f1111 !important;
    border: 1px solid #a88734 !important;
    border-radius: 8px;
    font-weight: 600;
}
.amazon-newsletter-page .dt-buttons .btn:hover,
.amazon-newsletter-page .dt-buttons button:hover {
    opacity: 0.95;
    color: #0f1111 !important;
}

/* DataTables search / length / pagination */
.amazon-newsletter-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    padding: 8px 12px;
}
.amazon-newsletter-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.amazon-newsletter-page .dataTables_wrapper .dataTables_length select {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
}
.amazon-newsletter-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important;
    color: #0f1111 !important;
}
.amazon-newsletter-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: #ff9900;
    background: #fff8e7 !important;
}

/* Delete button */
.amazon-newsletter-page .btn-danger {
    background: #c45500 !important;
    border: 1px solid #9a3e00 !important;
    color: #fff !important;
    border-radius: 6px;
}
.amazon-newsletter-page .btn-danger:hover {
    background: #a84800 !important;
    border-color: #9a3e00 !important;
    color: #fff !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page amazon-newsletter-page">
    <div class="container-fluid">
        <div class="page-header-card amazon-theme-banner">
            <h1>
                <div class="icon-box"><i class="fas fa-newspaper"></i></div>
                News Letter
            </h1>
            <p class="page-header-subtitle">Manage your newsletter subscribers</p>
        </div>

        <div class="content-card">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('News Letter'), 'title_svg' => '<i class="fa fa-newspaper"></i>'])
            <div class="table-responsive">
                <table class="table nowrap table-bordered table-striped" id="news_letter_table">
                    <thead>
                        <tr>
                            <th style="min-width: 150px">{{ __('Date') }}</th>
                            <th style="min-width: 200px">{{ __('Email') }}</th>
                            <!-- NEWLY ADDED: Location and Brand columns to display names instead of IDs -->
                            <th style="min-width: 150px">{{ __('Location') }}</th>
                            <th style="min-width: 150px">{{ __('brand.brand_name') }}</th>
                            <th style="min-width: 100px">{{ __('Action') }}</th>

                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
        </div>
    </div>
</div>
    <div class="modal fade" id="newsletterFilterModal" tabindex="-1" role="dialog" aria-labelledby="newsletterFilterModalLabel">
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
                                    'id' => 'newsletter_filter_inventory_type',
                                    'placeholder' => 'All',
                                ],
                            ) !!}
                        </div>
                    </div>
                    <!-- NEWLY ADDED: Brand filter for B2C locations -->
                    <div class="col-md-3" id="newsletter_brand_filter_container" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('brand_id', 'Brand:') !!}
                            {!! Form::select('brand_id', $b2c_brands ?? [], null, [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'newsletter_filter_brand_id',
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
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize select2 when modal is shown
        $('#newsletterFilterModal').on('shown.bs.modal', function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                dropdownParent: $('#newsletterFilterModal')
            });
        });

        // Show/hide brand filter based on inventory type selection
        $(document).on('change', '#newsletter_filter_inventory_type', function() {
            var inventoryType = $(this).val();
            if (inventoryType === 'b2c') {
                $('#newsletter_brand_filter_container').slideDown(300);
            } else {
                $('#newsletter_brand_filter_container').slideUp(300);
                $('#newsletter_filter_brand_id').val('').trigger('change');
            }
        });

        // Auto-reload DataTable when filter values change
        $(document).on('change', '#newsletter_filter_inventory_type, #newsletter_filter_brand_id', function() {
            // Check if news_letter_table exists in global scope
            if (typeof window.news_letter_table !== 'undefined' && window.news_letter_table) {
                window.news_letter_table.ajax.reload();
            } else {
                // Try to find the table by ID
                var table = $('#news_letter_table').DataTable();
                if (table) {
                    table.ajax.reload();
                }
            }
        });

        // Clean up select2 when modal is hidden
        $('#newsletterFilterModal').on('hidden.bs.modal', function() {
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

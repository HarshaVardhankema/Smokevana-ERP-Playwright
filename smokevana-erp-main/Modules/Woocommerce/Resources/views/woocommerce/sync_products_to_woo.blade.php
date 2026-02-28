@extends('layouts.app')
@section('title', 'Sync Products to WooCommerce')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* Sync Products to WooCommerce Page - Amazon Theme */
    .woocommerce-sync-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    
    /* Header Banner */
    .woocommerce-sync-page .content-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .woocommerce-sync-page .content-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .woocommerce-sync-page .content-header h1 {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #fff !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .woocommerce-sync-page .content-header h1 i {
        color: #96588a;
    }
    
    /* Box/Card Styling */
    .woocommerce-sync-page .box-primary {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        background: #fff;
    }
    .woocommerce-sync-page .box-primary .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .woocommerce-sync-page .box-primary .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .woocommerce-sync-page .box-primary .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
    }
    .woocommerce-sync-page .box-primary .box-body {
        background: #f7f8f8 !important;
        padding: 1.25rem 1.5rem !important;
    }
    
    /* Form Controls */
    .woocommerce-sync-page .form-group label {
        color: #0F1111 !important;
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .woocommerce-sync-page .form-control,
    .woocommerce-sync-page select.form-control {
        background: #fff;
        border: 1px solid #D5D9D9;
        color: #0F1111;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-height: 2rem;
        box-sizing: border-box;
        border-radius: 4px;
    }
    .woocommerce-sync-page .form-control:focus,
    .woocommerce-sync-page select.form-control:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .woocommerce-sync-page .input-group-addon {
        background: #F7F8F8;
        color: #232F3E;
        border-color: #D5D9D9;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-width: 2.25rem;
    }
    
    /* Select2 Styling */
    .woocommerce-sync-page .select2-container--default .select2-selection--single {
        border: 1px solid #D5D9D9 !important;
        border-radius: 4px !important;
        min-height: 2rem;
    }
    .woocommerce-sync-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
        padding-left: 0.5rem;
        font-size: 0.8125rem;
    }
    .woocommerce-sync-page .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .woocommerce-sync-page .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #FF9900 !important;
        color: #fff !important;
    }
    
    /* Buttons */
    .woocommerce-sync-page .tw-dw-btn-primary,
    .woocommerce-sync-page #apply-filters {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
    }
    .woocommerce-sync-page .tw-dw-btn-primary:hover,
    .woocommerce-sync-page #apply-filters:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    .woocommerce-sync-page .tw-dw-btn-success,
    .woocommerce-sync-page #sync-selected-btn {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
    }
    .woocommerce-sync-page .tw-dw-btn-success:hover,
    .woocommerce-sync-page #sync-selected-btn:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    .woocommerce-sync-page .tw-dw-btn-success:disabled,
    .woocommerce-sync-page #sync-selected-btn:disabled {
        background: #D5D9D9 !important;
        border-color: #D5D9D9 !important;
        color: #565959 !important;
        opacity: 0.6;
    }
    .woocommerce-sync-page .tw-dw-btn-info,
    .woocommerce-sync-page #sync-all-not-synced-btn {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
    }
    .woocommerce-sync-page .tw-dw-btn-info:hover,
    .woocommerce-sync-page #sync-all-not-synced-btn:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    
    /* Table Styling */
    .woocommerce-sync-page #products-sync-table thead tr {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        position: relative;
    }
    .woocommerce-sync-page #products-sync-table thead tr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .woocommerce-sync-page #products-sync-table thead th {
        background: transparent !important;
        color: #fff !important;
        font-weight: 600;
        border-color: rgba(255,255,255,0.2) !important;
        padding: 12px 8px;
        position: relative;
        z-index: 2;
    }
    .woocommerce-sync-page #products-sync-table thead th.sorting::after,
    .woocommerce-sync-page #products-sync-table thead th.sorting_asc::after,
    .woocommerce-sync-page #products-sync-table thead th.sorting_desc::after {
        color: #ff9900 !important;
    }
    .woocommerce-sync-page #products-sync-table tbody tr {
        background: #fff;
    }
    .woocommerce-sync-page #products-sync-table tbody td {
        border-color: #D5D9D9;
        padding: 10px 8px;
    }
    
    /* Badge Styling */
    .woocommerce-sync-page .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .woocommerce-sync-page .badge-success {
        background: #28a745 !important;
        color: #fff !important;
    }
    .woocommerce-sync-page .badge-warning {
        background: #ffc107 !important;
        color: #0F1111 !important;
    }
    .woocommerce-sync-page .badge-secondary,
    .woocommerce-sync-page .badge-info {
        background: #6c757d !important;
        color: #fff !important;
    }
    
    /* Action Buttons in Table */
    .woocommerce-sync-page .sync-product {
        background: #28a745 !important;
        color: #fff !important;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .woocommerce-sync-page .sync-product:hover {
        background: #218838 !important;
        color: #fff !important;
    }
    .woocommerce-sync-page .sync-product:disabled {
        background: #D5D9D9 !important;
        color: #565959 !important;
        opacity: 0.6;
    }
    
    /* DataTables Controls */
    .woocommerce-sync-page .dataTables_wrapper .dataTables_filter input,
    .woocommerce-sync-page .dataTables_wrapper .dataTables_length select {
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 4px 8px;
    }
    .woocommerce-sync-page .dataTables_wrapper .dataTables_filter input:focus,
    .woocommerce-sync-page .dataTables_wrapper .dataTables_length select:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .woocommerce-sync-page .dt-buttons .btn,
    .woocommerce-sync-page .dt-buttons button,
    .woocommerce-sync-page .dt-buttons .dt-button {
        background: #232f3e !important;
        border: 1px solid #37475a !important;
        color: #fff !important;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.8125rem;
        margin-right: 4px;
    }
    .woocommerce-sync-page .dt-buttons .btn:hover,
    .woocommerce-sync-page .dt-buttons button:hover,
    .woocommerce-sync-page .dt-buttons .dt-button:hover {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
    }
    .woocommerce-sync-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #ff9900 !important;
        border-color: #ff9900 !important;
        color: #fff !important;
    }
    .woocommerce-sync-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: #ff9900;
        color: #232f3e;
    }
    
    /* Checkbox Styling */
    .woocommerce-sync-page input[type="checkbox"] {
        accent-color: #FF9900;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="woocommerce-sync-page">
<section class="content-header">
    <h1>
        <i class="fab fa-wordpress"></i> Sync Products to WooCommerce
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Select Products to Sync'])
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-success" id="sync-selected-btn" disabled>
                            <i class="fas fa-cloud-upload-alt"></i> Sync Selected
                        </button>
                        <button type="button" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-info" id="sync-all-not-synced-btn">
                            <i class="fas fa-sync"></i> Sync All Not Synced
                        </button>
                    </div>
                @endslot

                <div class="row tw-mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sync Status</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-sync"></i>
                                </span>
                                <select class="form-control" id="filter_sync_status">
                                    <option value="">All</option>
                                    <option value="not_synced">Not Synced</option>
                                    <option value="synced">Already Synced</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Product Source</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-box"></i>
                                </span>
                                <select class="form-control" id="filter_source_type">
                                    <option value="">All</option>
                                    <option value="in_house">In-House</option>
                                    <option value="dropshipped">Dropshipped</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="tw-dw-btn tw-dw-btn-primary" id="apply-filters">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="products-sync-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 30px;">
                                    <input type="checkbox" id="select-all-products">
                                </th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Type</th>
                                <th>Vendor</th>
                                <th>Price</th>
                                <th>Sync Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>

    <!-- Sync Progress Modal -->
    <div class="modal fade" id="sync-progress-modal" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-sync fa-spin"></i> Syncing Products...</h4>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                            <span class="progress-text">0%</span>
                        </div>
                    </div>
                    <div class="tw-mt-3">
                        <p><strong>Current:</strong> <span id="current-product">-</span></p>
                        <p><strong>Success:</strong> <span id="sync-success" class="text-success">0</span></p>
                        <p><strong>Failed:</strong> <span id="sync-failed" class="text-danger">0</span></p>
                    </div>
                    <div id="sync-errors" class="tw-mt-3 tw-max-h-40 tw-overflow-y-auto" style="display: none;">
                        <strong>Errors:</strong>
                        <ul id="error-list" class="tw-text-sm tw-text-red-600"></ul>
                    </div>
                </div>
                <div class="modal-footer" style="display: none;" id="sync-complete-footer">
                    <button type="button" class="tw-dw-btn tw-dw-btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var selectedProducts = [];

    // Initialize DataTable
    var productsTable = $('#products-sync-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/woocommerce/products-for-woo-sync',
            data: function(d) {
                d.sync_status = $('#filter_sync_status').val();
                d.source_type = $('#filter_source_type').val();
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="product-checkbox" value="' + row.id + '">';
                }
            },
            { data: 'name', name: 'name' },
            { data: 'sku', name: 'sku' },
            { data: 'product_type', name: 'product_type' },
            { data: 'vendor', name: 'vendor' },
            { data: 'price', name: 'price' },
            { data: 'sync_status', name: 'sync_status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    // Apply filters
    $('#apply-filters').on('click', function() {
        productsTable.ajax.reload();
    });

    // Select all
    $('#select-all-products').on('change', function() {
        var checked = $(this).prop('checked');
        $('.product-checkbox').prop('checked', checked);
        updateSelectedProducts();
    });

    // Individual checkbox
    $(document).on('change', '.product-checkbox', function() {
        updateSelectedProducts();
    });

    function updateSelectedProducts() {
        selectedProducts = [];
        $('.product-checkbox:checked').each(function() {
            selectedProducts.push($(this).val());
        });
        $('#sync-selected-btn').prop('disabled', selectedProducts.length === 0);
    }

    // Sync selected products
    $('#sync-selected-btn').on('click', function() {
        if (selectedProducts.length === 0) {
            toastr.warning('Please select products to sync');
            return;
        }
        syncProducts(selectedProducts);
    });

    // Sync all not synced
    $('#sync-all-not-synced-btn').on('click', function() {
        // Get all not synced products from current filter
        if (confirm('This will sync all products that are not yet in WooCommerce. Continue?')) {
            // Set filter to not synced and reload
            $('#filter_sync_status').val('not_synced');
            
            // Use bulk API
            $.ajax({
                url: '/woocommerce/products-for-woo-sync?sync_status=not_synced&length=1000',
                type: 'GET',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        var productIds = response.data.map(function(p) { return p.id; });
                        syncProducts(productIds);
                    } else {
                        toastr.info('All products are already synced!');
                    }
                },
                error: function() {
                    toastr.error('Failed to fetch products');
                }
            });
        }
    });

    // Sync individual product
    $(document).on('click', '.sync-product', function() {
        var productId = $(this).data('id');
        var btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '/woocommerce/sync-product-to-woo/' + productId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success('Product synced successfully');
                    productsTable.ajax.reload(null, false);
                } else {
                    toastr.error(response.message || 'Sync failed');
                    btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt"></i>');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Sync failed');
                btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt"></i>');
            }
        });
    });

    // Sync stock only
    $(document).on('click', '.sync-stock', function() {
        var productId = $(this).data('id');
        var btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '/woocommerce/sync-product-stock-to-woo/' + productId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    toastr.success('Stock synced successfully');
                } else {
                    toastr.error(response.message || 'Stock sync failed');
                }
                btn.prop('disabled', false).html('<i class="fas fa-boxes"></i>');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Stock sync failed');
                btn.prop('disabled', false).html('<i class="fas fa-boxes"></i>');
            }
        });
    });

    // Bulk sync with progress
    function syncProducts(productIds) {
        var total = productIds.length;
        var current = 0;
        var success = 0;
        var failed = 0;
        var errors = [];

        $('#sync-progress-modal').modal('show');
        $('#sync-complete-footer').hide();
        $('#sync-errors').hide();
        $('#error-list').empty();

        function syncNext() {
            if (current >= total) {
                // Complete
                $('.progress-bar').removeClass('active').addClass('progress-bar-success');
                $('.modal-title').html('<i class="fas fa-check"></i> Sync Complete');
                $('#sync-complete-footer').show();
                productsTable.ajax.reload();
                return;
            }

            var productId = productIds[current];
            var percent = Math.round(((current + 1) / total) * 100);
            
            $('.progress-bar').css('width', percent + '%');
            $('.progress-text').text(percent + '%');
            $('#current-product').text('Processing ' + (current + 1) + ' of ' + total);

            $.ajax({
                url: '/woocommerce/sync-product-to-woo/' + productId,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        success++;
                    } else {
                        failed++;
                        errors.push('Product #' + productId + ': ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    failed++;
                    errors.push('Product #' + productId + ': ' + (xhr.responseJSON?.message || 'Request failed'));
                },
                complete: function() {
                    current++;
                    $('#sync-success').text(success);
                    $('#sync-failed').text(failed);
                    
                    if (errors.length > 0) {
                        $('#sync-errors').show();
                        $('#error-list').html(errors.map(function(e) { return '<li>' + e + '</li>'; }).join(''));
                    }
                    
                    // Small delay to avoid overwhelming the server
                    setTimeout(syncNext, 200);
                }
            });
        }

        syncNext();
    }
});
</script>
@endsection

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
        margin: 0 0 4px 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .woocommerce-sync-page .content-header h1 i {
        color: #96588a;
    }
    .woocommerce-sync-page .content-header p {
        color: rgba(255,255,255,0.88) !important;
        font-size: 13px;
        margin: 0;
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
    .woocommerce-sync-page .btn-primary,
    .woocommerce-sync-page #apply-filters {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
    }
    .woocommerce-sync-page .btn-primary:hover,
    .woocommerce-sync-page #apply-filters:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    .woocommerce-sync-page .btn-success,
    .woocommerce-sync-page #sync-selected-btn {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
    }
    .woocommerce-sync-page .btn-success:hover,
    .woocommerce-sync-page #sync-selected-btn:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    .woocommerce-sync-page .btn-success:disabled,
    .woocommerce-sync-page #sync-selected-btn:disabled {
        background: #D5D9D9 !important;
        border-color: #D5D9D9 !important;
        color: #565959 !important;
        opacity: 0.6;
    }
    .woocommerce-sync-page .btn-warning,
    .woocommerce-sync-page #sync-all-btn {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 600;
    }
    .woocommerce-sync-page .btn-warning:hover,
    .woocommerce-sync-page #sync-all-btn:hover {
        color: #fff !important;
        opacity: 0.95;
    }
    
    /* Table Styling */
    .woocommerce-sync-page #products-table thead tr {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        position: relative;
    }
    .woocommerce-sync-page #products-table thead tr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .woocommerce-sync-page #products-table thead th {
        background: transparent !important;
        color: #fff !important;
        font-weight: 600;
        border-color: rgba(255,255,255,0.2) !important;
        padding: 12px 8px;
        position: relative;
        z-index: 2;
    }
    .woocommerce-sync-page #products-table thead th.sorting::after,
    .woocommerce-sync-page #products-table thead th.sorting_asc::after,
    .woocommerce-sync-page #products-table thead th.sorting_desc::after {
        color: #ff9900 !important;
    }
    .woocommerce-sync-page #products-table tbody tr {
        background: #fff;
    }
    .woocommerce-sync-page #products-table tbody td {
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
    .woocommerce-sync-page .badge[style*="background"] {
        background: #6c757d !important;
        color: #fff !important;
    }
    
    /* Action Buttons in Table */
    .woocommerce-sync-page .sync-single-product {
        background: #28a745 !important;
        color: #fff !important;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .woocommerce-sync-page .sync-single-product:hover {
        background: #218838 !important;
        color: #fff !important;
    }
    .woocommerce-sync-page .sync-single-product:disabled {
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
@include('woocommerce::layouts.nav')

<section class="content-header">
    <h1>
        <i class="fab fa-wordpress"></i> Sync Products to WooCommerce
    </h1>
    <p>Select Products to Sync</p>
</section>

<section class="content">
    <!-- Filter Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_sync_status">Sync Status:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-sync"></i>
                                    </span>
                                    <select class="form-control select2" id="filter_sync_status">
                                        <option value="all">All</option>
                                        <option value="synced">Synced</option>
                                        <option value="not_synced">Not Synced</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_product_source">Product Source:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-box"></i>
                                    </span>
                                    <select class="form-control select2" id="filter_product_source">
                                        <option value="all">All</option>
                                        <option value="in_house">In-House</option>
                                        <option value="dropshipped">Dropshipped</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" class="btn btn-primary" id="apply-filters">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list"></i> Products</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-success btn-sm" id="sync-selected-btn" disabled>
                            <i class="fas fa-sync"></i> Sync Selected
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" id="sync-all-btn">
                            <i class="fas fa-cloud-upload-alt"></i> Sync All Not Synced
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <label>
                                Show
                                <select class="form-control input-sm" id="page-length-select" style="display: inline-block; width: auto;">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                entries
                            </label>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="form-group" style="margin: 0;">
                                <div class="input-group" style="display: inline-block; width: auto;">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control input-sm" id="search-input" placeholder="Search..." style="display: inline-block; width: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="products-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="select-all-checkbox">
                                    </th>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Type</th>
                                    <th>Vendor</th>
                                    <th>Price</th>
                                    <th>Sync Status</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var productsTable = null;

    // Initialize DataTable
    function initDataTable() {
        productsTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, "syncToWooCommerceData"]) }}',
                data: function(d) {
                    d.sync_status = $('#filter_sync_status').val();
                    d.product_source = $('#filter_product_source').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'name', name: 'products.name' },
                { data: 'sku', name: 'products.sku' },
                { data: 'type_badge', name: 'products.product_source_type', orderable: false },
                { data: 'vendor_name', name: 'wp_vendors.name', defaultContent: '' },
                { data: 'price', name: 'price', orderable: false },
                { data: 'sync_status_badge', name: 'sync_status', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            drawCallback: function() {
                // Update select all checkbox
                var allChecked = $('.product-checkbox:checked').length === $('.product-checkbox').length && $('.product-checkbox').length > 0;
                $('#select-all-checkbox').prop('checked', allChecked);
                
                // Update sync selected button state
                var selectedCount = $('.product-checkbox:checked').length;
                $('#sync-selected-btn').prop('disabled', selectedCount === 0);
            }
        });
    }

    // Initialize on page load
    initDataTable();

    // Apply filters
    $('#apply-filters').on('click', function() {
        productsTable.ajax.reload();
    });

    // Page length change
    $('#page-length-select').on('change', function() {
        productsTable.page.len(parseInt($(this).val())).draw();
    });

    // Search
    $('#search-input').on('keyup', function() {
        productsTable.search($(this).val()).draw();
    });

    // Select all checkbox
    $('#select-all-checkbox').on('change', function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        var selectedCount = $('.product-checkbox:checked').length;
        $('#sync-selected-btn').prop('disabled', selectedCount === 0);
    });

    // Update sync selected button when individual checkboxes change
    $(document).on('change', '.product-checkbox', function() {
        var selectedCount = $('.product-checkbox:checked').length;
        $('#sync-selected-btn').prop('disabled', selectedCount === 0);
    });

    // Sync Selected Products
    $('#sync-selected-btn').on('click', function() {
        var selectedIds = [];
        $('.product-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            toastr.warning('Please select at least one product to sync.');
            return;
        }

        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');

        swal({
            title: 'Sync Selected Products?',
            text: 'This will sync ' + selectedIds.length + ' selected product(s) to WooCommerce.',
            icon: 'info',
            buttons: {
                cancel: 'Cancel',
                confirm: {
                    text: 'Sync Now',
                    className: 'btn-success'
                }
            }
        }).then(function(confirmed) {
            if (confirmed) {
                $.ajax({
                    url: '{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, "syncSelectedProducts"]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            productsTable.ajax.reload(null, false);
                            $('.product-checkbox').prop('checked', false);
                            $('#select-all-checkbox').prop('checked', false);
                            $('#sync-selected-btn').prop('disabled', true);
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to sync products');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            } else {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Sync All Not Synced
    $('#sync-all-btn').on('click', function() {
        var $btn = $(this);
        var originalHtml = $btn.html();

        swal({
            title: 'Sync All Not Synced Products?',
            text: 'This will sync all products that haven\'t been synced to WooCommerce. This may take a few minutes.',
            icon: 'warning',
            buttons: {
                cancel: 'Cancel',
                confirm: {
                    text: 'Sync All',
                    className: 'btn-warning'
                }
            }
        }).then(function(confirmed) {
            if (confirmed) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');

                $.ajax({
                    url: '{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, "syncAllNotSynced"]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            productsTable.ajax.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to sync products');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });

    // Sync single product
    $(document).on('click', '.sync-single-product', function() {
        var productId = $(this).data('id');
        var $btn = $(this);
        var originalHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, "syncSelectedProducts"]) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_ids: [productId]
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    productsTable.ajax.reload(null, false);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to sync product');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // View product (placeholder - you can implement this)
    $(document).on('click', '.view-product', function() {
        var productId = $(this).data('id');
        window.location.href = '/products/' + productId;
    });
});
</script>
@endsection

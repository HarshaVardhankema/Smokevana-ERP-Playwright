@extends('layouts.vendor_portal')
@section('title', 'Inventory')

@section('css')
<style>
/* Stock Summary Cards - Amazon Style */
.inventory-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.inventory-stat {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.2s ease;
}

.inventory-stat:hover {
    border-color: var(--amazon-orange);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.inventory-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 20px;
}

.inventory-stat-icon.green { background: #d4edda; color: var(--amazon-success); }
.inventory-stat-icon.yellow { background: #fff3cd; color: #856404; }
.inventory-stat-icon.red { background: #f8d7da; color: var(--amazon-error); }

.inventory-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.inventory-stat-label {
    font-size: 13px;
    color: var(--gray-600);
    font-weight: 600;
}

/* Product Image */
.product-thumb {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    object-fit: cover;
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
}

/* Stock Badges */
.stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.stock-badge.in-stock { background: #d4edda; color: var(--amazon-success); }
.stock-badge.low-stock { background: #fff3cd; color: #856404; }
.stock-badge.out-stock { background: #f8d7da; color: var(--amazon-error); }

/* Action Buttons */
.btn-action {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-action-stock {
    background: linear-gradient(to bottom, #f7dfa5, #f0c14b);
    border-color: #a88734;
    color: var(--gray-900);
}

.btn-action-stock:hover {
    background: linear-gradient(to bottom, #f5d78e, #eeba37);
}

/* DataTables Customization */
#vendor-products-table {
    width: 100% !important;
}

#vendor-products-table thead th {
    background: var(--amazon-navy) !important;
    color: #fff !important;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 14px;
    border: none !important;
}

#vendor-products-table tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-200);
    font-size: 13px;
}

#vendor-products-table tbody tr:hover {
    background: #fffbf3;
}

.dataTables_wrapper .row:first-child {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 12px 16px;
    margin: 0 !important;
}

.dataTables_wrapper .row:last-child {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 12px 16px;
    margin: 0 !important;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--gray-300);
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 13px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none;
    border-color: var(--amazon-orange);
    box-shadow: 0 0 0 2px rgba(255,153,0,0.15);
}

/* Product name link */
.product-name-link {
    color: var(--amazon-link);
    font-weight: 600;
    text-decoration: none;
}

.product-name-link:hover {
    color: var(--amazon-warning);
    text-decoration: underline;
}

/* SKU code */
.sku-code {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: var(--gray-600);
    background: var(--gray-100);
    padding: 3px 8px;
    border-radius: 3px;
}

/* Price display */
.price-display {
    font-weight: 600;
    color: var(--gray-800);
}

.price-display.cost {
    color: var(--amazon-teal);
}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="sc-page-header">
    <h1 class="sc-page-title"><strong>Inventory</strong> Management</h1>
</div>

<!-- Inventory Summary Cards -->
<div class="inventory-summary">
    <div class="inventory-stat">
        <div class="inventory-stat-icon green">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="inventory-stat-value" id="in-stock-count">-</div>
        <div class="inventory-stat-label">In Stock</div>
    </div>
    <div class="inventory-stat">
        <div class="inventory-stat-icon yellow">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="inventory-stat-value" id="low-stock-count">-</div>
        <div class="inventory-stat-label">Low Stock</div>
    </div>
    <div class="inventory-stat">
        <div class="inventory-stat-icon red">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="inventory-stat-value" id="out-stock-count">-</div>
        <div class="inventory-stat-label">Out of Stock</div>
    </div>
</div>

<!-- Products Table Card -->
<div class="sc-card">
    <div class="sc-card-header">
        <h3 class="sc-card-title">
            <i class="bi bi-boxes"></i>
            Your Products
        </h3>
        <span class="text-muted fs-sm">Manage your product inventory and stock levels</span>
    </div>
    <div style="padding: 0;">
        <table class="table" id="vendor-products-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Image</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Your Cost</th>
                    <th>Sell Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="width: 100px; text-align: center;">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="update-stock-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="bi bi-boxes"></i> Update Stock Level</h4>
            </div>
            <form id="update-stock-form">
                <div class="modal-body">
                    <input type="hidden" id="stock_product_id">
                    
                    <div style="background: var(--gray-100); border-radius: 8px; padding: 16px; margin-bottom: 20px; text-align: center;">
                        <div style="font-size: 12px; color: var(--gray-500); text-transform: uppercase; margin-bottom: 4px;">Current Stock</div>
                        <div style="font-size: 36px; font-weight: 700; color: var(--amazon-navy);" id="current-stock-display">0</div>
                    </div>
                    
                    <div class="sc-form-group">
                        <label class="sc-form-label">New Stock Quantity <span class="required">*</span></label>
                        <input type="number" class="sc-form-control" id="stock_qty" min="0" required placeholder="Enter new quantity">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="sc-btn sc-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="sc-btn sc-btn-primary">
                        <i class="bi bi-save"></i> Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var productsTable = $('#vendor-products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("vendor.products") }}',
            dataSrc: function(json) {
                // Update stock summary
                if (json.stockSummary) {
                    $('#in-stock-count').text(json.stockSummary.in_stock || 0);
                    $('#low-stock-count').text(json.stockSummary.low_stock || 0);
                    $('#out-stock-count').text(json.stockSummary.out_of_stock || 0);
                }
                return json.data;
            }
        },
        columns: [
            { 
                data: 'image_display', 
                name: 'p.image', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return '<img src="' + (data || 'https://via.placeholder.com/50') + '" class="product-thumb" onerror="this.src=\'https://via.placeholder.com/50\'">';
                }
            },
            { 
                data: 'name', 
                name: 'p.name',
                render: function(data, type, row) {
                    return '<span class="product-name-link">' + data + '</span>';
                }
            },
            { 
                data: 'sku', 
                name: 'p.sku',
                render: function(data) {
                    return data ? '<code class="sku-code">' + data + '</code>' : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'vendor_cost', 
                name: 'vendor_cost', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return '<span class="price-display cost">$' + parseFloat(data || 0).toFixed(2) + '</span>';
                }
            },
            { 
                data: 'selling_price', 
                name: 'selling_price', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return '<span class="price-display">$' + parseFloat(data || 0).toFixed(2) + '</span>';
                }
            },
            { 
                data: 'stock', 
                name: 'stock', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var qty = parseInt(data) || 0;
                    var badgeClass = 'in-stock';
                    var label = qty;
                    
                    if (qty <= 0) {
                        badgeClass = 'out-stock';
                        label = '0';
                    } else if (qty <= 10) {
                        badgeClass = 'low-stock';
                    }
                    
                    return '<span class="stock-badge ' + badgeClass + '">' + label + '</span>';
                }
            },
            { 
                data: 'status_display', 
                name: 'status', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var status = row.status || 'active';
                    var badgeClass = 'sc-badge-active';
                    var label = 'Active';
                    
                    if (status === 'out_of_stock') {
                        badgeClass = 'sc-badge-cancelled';
                        label = 'Out of Stock';
                    } else if (status === 'inactive') {
                        badgeClass = 'sc-badge-pending';
                        label = 'Inactive';
                    }
                    
                    return '<span class="sc-badge ' + badgeClass + '">' + label + '</span>';
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return '<button class="btn-action btn-action-stock update-stock" data-id="' + row.id + '" data-current="' + (row.stock || 0) + '">' +
                           '<i class="bi bi-pencil"></i> Update' +
                           '</button>';
                }
            }
        ],
        language: {
            processing: '<i class="bi bi-arrow-repeat"></i> Loading...',
            emptyTable: 'No products assigned to you yet',
            zeroRecords: 'No matching products found'
        },
        order: [[1, 'asc']]
    });

    // Fetch stock summary separately if not included in main response
    function fetchStockSummary() {
        $.get('{{ route("vendor.products") }}', { summary_only: true }, function(response) {
            if (response.stockSummary) {
                $('#in-stock-count').text(response.stockSummary.in_stock || 0);
                $('#low-stock-count').text(response.stockSummary.low_stock || 0);
                $('#out-stock-count').text(response.stockSummary.out_of_stock || 0);
            }
        });
    }

    // Update stock modal
    $(document).on('click', '.update-stock', function() {
        var productId = $(this).data('id');
        var currentStock = $(this).data('current') || 0;
        
        $('#stock_product_id').val(productId);
        $('#current-stock-display').text(currentStock);
        $('#stock_qty').val(currentStock);
        $('#update-stock-modal').modal('show');
    });

    $('#update-stock-form').on('submit', function(e) {
        e.preventDefault();
        var productId = $('#stock_product_id').val();
        var submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Updating...');
        
        $.ajax({
            url: '{{ url("vendor-portal/products") }}/' + productId + '/stock',
            method: 'POST',
            data: {
                stock_qty: $('#stock_qty').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#update-stock-modal').modal('hide');
                    productsTable.ajax.reload(null, false);
                } else {
                    toastr.error(response.msg);
                }
                submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Update Stock');
            },
            error: function() {
                toastr.error('Failed to update stock');
                submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Update Stock');
            }
        });
    });
});
</script>
@endsection

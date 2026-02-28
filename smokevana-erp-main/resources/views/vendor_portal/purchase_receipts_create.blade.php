@extends('layouts.vendor_portal')
@section('title', 'Create Purchase Receipt')

@section('css')
<style>
/* Page Header */
.page-header {
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.page-header-left {
    flex: 1;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--gray-600);
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--amazon-orange);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    margin-bottom: 16px;
}

.back-link:hover {
    color: var(--amazon-orange-hover);
    text-decoration: none;
}

/* Header Actions */
.header-actions {
    display: flex;
    gap: 12px;
}

.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--amazon-orange);
    border: none;
    border-radius: 8px;
    color: var(--amazon-navy-dark);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
}

.btn-save:hover {
    background: var(--amazon-orange-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 153, 0, 0.4);
}

.btn-save:disabled {
    background: var(--gray-300);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--gray-200);
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    color: var(--gray-700);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-cancel:hover {
    background: var(--gray-300);
    text-decoration: none;
    color: var(--gray-800);
}

/* Content Card */
.content-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 24px;
}

.content-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--amazon-navy);
    color: #fff;
}

.content-card-header i {
    color: #fff;
}

.content-card-title {
    font-size: 16px;
    font-weight: 700;
}

.content-card-body {
    padding: 20px;
}

/* Search Box */
.search-section {
    margin-bottom: 20px;
}

.search-box {
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 14px 16px 14px 48px;
    border: 2px solid var(--gray-300);
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.search-box input:focus {
    outline: none;
    border-color: var(--amazon-orange);
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
    cursor: text;
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
    font-size: 18px;
}

.search-hint {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 8px;
}

/* Products Table */
.products-table-wrapper {
    max-height: 450px;
    overflow-y: auto;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
}

.products-table {
    width: 100%;
    border-collapse: collapse;
}

.products-table thead th {
    background: var(--amazon-navy);
    color: #fff;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
}

.products-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--gray-200);
    font-size: 14px;
    vertical-align: middle;
}

.products-table tbody tr:hover {
    background: #f0fdf9;
}

.products-table tbody tr.total-row {
    background: var(--gray-100);
    font-weight: 600;
}

.products-table tbody tr.total-row td {
    border-top: 2px solid var(--gray-300);
}

/* Product Info in Table */
.product-info-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image {
    width: 45px;
    height: 45px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid var(--gray-200);
}

.product-image-placeholder {
    width: 45px;
    height: 45px;
    border-radius: 6px;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
}

.product-details {
    flex: 1;
}

.product-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.product-sku {
    font-size: 12px;
    color: var(--gray-500);
}

/* Input Fields in Table */
.input-qty, .input-cost {
    width: 100px;
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    font-size: 14px;
    text-align: right;
}

.input-qty:focus, .input-cost:focus {
    outline: none;
    border-color: #067d62;
    box-shadow: 0 0 0 2px rgba(6, 125, 98, 0.1);
}

.input-cost {
    width: 120px;
}

/* Remove Button */
.btn-remove {
    padding: 6px 10px;
    background: #fee2e2;
    border: none;
    border-radius: 6px;
    color: #dc2626;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-remove:hover {
    background: #fecaca;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-500);
}

.empty-state-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
    color: var(--amazon-orange);
}

.empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.empty-state-text {
    font-size: 14px;
    margin-bottom: 20px;
}

/* Summary Section */
.summary-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--gray-200);
}

.notes-section {
    flex: 1;
}

.notes-section label {
    display: block;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.notes-section textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    min-height: 100px;
}

.notes-section textarea:focus {
    outline: none;
    border-color: var(--amazon-orange);
}

.totals-section {
    min-width: 300px;
    background: rgba(255, 153, 0, 0.05);
    border: 1px solid rgba(255, 153, 0, 0.2);
    border-radius: 8px;
    padding: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 153, 0, 0.1);
}

.total-row:last-child {
    border-bottom: none;
    padding-top: 16px;
    margin-top: 8px;
    border-top: 2px solid var(--amazon-orange);
}

.total-label {
    color: var(--gray-600);
    font-size: 14px;
}

.total-value {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 14px;
}

.total-row:last-child .total-label,
.total-row:last-child .total-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--amazon-orange);
}

/* Search Results Dropdown */
.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 2px solid var(--amazon-orange);
    border-top: none;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    max-height: 450px;
    overflow-y: auto;
    z-index: 100;
    display: none;
}

.search-results.active {
    display: block;
}

.search-results-header {
    padding: 10px 16px;
    background: var(--amazon-navy);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}

.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid var(--gray-100);
    transition: background 0.15s ease;
}

.search-result-item:hover {
    background: rgba(255, 153, 0, 0.05);
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-image {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    background: var(--gray-100);
}

.search-result-info {
    flex: 1;
}

.search-result-name {
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.search-result-meta {
    font-size: 12px;
    color: var(--gray-500);
}

.search-result-stock {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
}

.search-result-stock.in-stock {
    background: #d4edda;
    color: #155724;
}

.search-result-stock.low-stock {
    background: #fff3cd;
    color: #856404;
}

.search-result-stock.out-of-stock {
    background: #f8d7da;
    color: #721c24;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: var(--gray-500);
}

/* Info Banner */
.info-banner {
    background: rgba(255, 153, 0, 0.05);
    border: 1px solid rgba(255, 153, 0, 0.3);
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.info-banner i {
    color: var(--amazon-orange);
    font-size: 20px;
}

.info-banner-text {
    flex: 1;
    font-size: 14px;
    color: var(--gray-700);
}

.info-banner-text strong {
    color: var(--amazon-orange);
}

/* Loading */
.loading-spinner {
    display: none;
    text-align: center;
    padding: 20px;
    color: var(--gray-500);
}

.loading-spinner.active {
    display: block;
}

.loading-spinner i {
    font-size: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')
<a href="{{ route('vendor.purchase-receipts') }}" class="back-link">
    <i class="bi bi-arrow-left"></i> Back to Purchase Receipts
</a>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title"><i class="bi bi-box-seam"></i> Receive Inventory</h1>
        <p class="page-subtitle">Record received products and update your inventory stock</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('vendor.purchase-receipts') }}" class="btn-cancel">
            <i class="bi bi-x"></i> Cancel
        </a>
        <button type="button" class="btn-save" id="submit-receipt-btn" disabled>
            <i class="bi bi-check"></i> Save & Update Stock
        </button>
    </div>
</div>

<!-- Info Banner -->
<div class="info-banner">
    <i class="bi bi-info-circle"></i>
    <div class="info-banner-text">
        <strong>Add Inventory:</strong> Select products from your inventory, enter the quantity received, and click "Save & Update Stock" to update your inventory levels.
    </div>
</div>

<!-- Product Search & Entry -->
<div class="content-card">
    <div class="content-card-header">
        <i class="bi bi-plus-circle"></i>
        <h3 class="content-card-title">Select Products to Receive</h3>
    </div>
    <div class="content-card-body">
        <!-- Search Box -->
        <div class="search-section">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="product-search" placeholder="Click here to select products from your inventory..." autocomplete="off">
                <div class="search-results" id="search-results"></div>
            </div>
            <p class="search-hint"><i class="bi bi-lightbulb"></i> Click the box to see all products. Type to filter. Click a product to add it.</p>
        </div>

        <!-- Products Table -->
        <div class="products-table-wrapper">
            <table class="products-table" id="receipt-table">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 35%">Product</th>
                        <th style="width: 12%">Current Stock</th>
                        <th style="width: 15%">Qty Received</th>
                        <th style="width: 15%">Unit Cost ($)</th>
                        <th style="width: 12%">Line Total</th>
                        <th style="width: 6%"></th>
                    </tr>
                </thead>
                <tbody id="receipt-lines">
                    <!-- Product lines will be added here -->
                </tbody>
            </table>
            
            <!-- Empty State -->
            <div class="empty-state" id="empty-state">
                <div class="empty-state-icon"><i class="bi bi-box-seam"></i></div>
                <h3 class="empty-state-title">No Products Added</h3>
                <p class="empty-state-text">Select products from your inventory above to record received quantities</p>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="notes-section">
                <label for="receipt-notes"><i class="bi bi-sticky"></i> Notes / Reference</label>
                <textarea id="receipt-notes" placeholder="Add any notes, supplier invoice number, or reference for this receipt..."></textarea>
            </div>
            <div class="totals-section">
                <div class="total-row">
                    <span class="total-label">Total Products:</span>
                    <span class="total-value" id="total-items">0</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Total Quantity:</span>
                    <span class="total-value" id="total-qty">0</span>
                </div>
                <div class="total-row">
                    <span class="total-label"><strong>Receipt Total:</strong></span>
                    <span class="total-value" id="total-amount">$0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // State
    let receiptLines = [];
    let lineIndex = 0;
    let searchTimeout = null;
    
    const searchInput = $('#product-search');
    const searchResults = $('#search-results');
    const receiptLinesBody = $('#receipt-lines');
    const emptyState = $('#empty-state');
    const submitBtn = $('#submit-receipt-btn');
    
    // Load all products when clicking/focusing on search box
    searchInput.on('focus click', function() {
        if (!searchResults.hasClass('active')) {
            const term = $(this).val().trim();
            searchProducts(term);
        }
    });
    
    // Search products on typing
    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const term = $(this).val().trim();
        
        searchTimeout = setTimeout(function() {
            searchProducts(term);
        }, 300);
    });
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box').length) {
            searchResults.removeClass('active');
        }
    });
    
    // Search products API call
    function searchProducts(term) {
        searchResults.html('<div class="loading-spinner active"><i class="bi bi-arrow-repeat"></i><p>Loading products...</p></div>').addClass('active');
        
        $.ajax({
            url: "{{ route('vendor.purchase-receipts.inventory-products') }}",
            method: 'GET',
            data: { term: term || '' },
            success: function(response) {
                if (response.success && response.products.length > 0) {
                    renderSearchResults(response.products);
                } else {
                    searchResults.html('<div class="no-results"><i class="bi bi-box-seam"></i><p>No products found in your inventory</p></div>');
                }
            },
            error: function(xhr) {
                searchResults.html('<div class="no-results text-danger"><i class="bi bi-exclamation-triangle"></i><p>Failed to load products</p></div>');
            }
        });
    }
    
    // Render search results
    function renderSearchResults(products) {
        let html = '<div class="search-results-header"><i class="bi bi-boxes"></i> ' + products.length + ' product(s) in your inventory</div>';
        
        products.forEach(function(product) {
            let stockClass = 'in-stock';
            let stockText = product.stock + ' in stock';
            
            if (product.stock <= 0) {
                stockClass = 'out-of-stock';
                stockText = 'Out of stock';
            } else if (product.stock <= 10) {
                stockClass = 'low-stock';
            }
            
            const imgHtml = product.image 
                ? '<img src="' + product.image + '" alt="" class="search-result-image">'
                : '<div class="search-result-image" style="display:flex;align-items:center;justify-content:center;"><i class="bi bi-box" style="color:#ccc;"></i></div>';
            
            html += `
                <div class="search-result-item" data-product='${JSON.stringify(product)}'>
                    ${imgHtml}
                    <div class="search-result-info">
                        <div class="search-result-name">${escapeHtml(product.name)}</div>
                        <div class="search-result-meta">SKU: ${escapeHtml(product.sku || 'N/A')} | Cost: $${parseFloat(product.cost_price || 0).toFixed(2)}</div>
                    </div>
                    <span class="search-result-stock ${stockClass}">${stockText}</span>
                </div>
            `;
        });
        
        searchResults.html(html);
    }
    
    // Click on search result to add product
    $(document).on('click', '.search-result-item', function() {
        const product = $(this).data('product');
        addProductLine(product);
        searchInput.val('').focus();
        searchResults.removeClass('active');
    });
    
    // Add product line to table
    function addProductLine(product) {
        // Check if product already exists
        const existingIndex = receiptLines.findIndex(p => 
            p.product_id === product.product_id && 
            p.variation_id === product.variation_id
        );
        
        if (existingIndex > -1) {
            // Increment quantity
            const existingRow = receiptLinesBody.find(`tr[data-index="${existingIndex}"]`);
            const qtyInput = existingRow.find('.input-qty');
            qtyInput.val(parseInt(qtyInput.val()) + 1).trigger('change');
            toastr.info('Product quantity updated');
            return;
        }
        
        // Add new line
        const line = {
            index: lineIndex,
            product_id: product.product_id,
            variation_id: product.variation_id,
            name: product.name,
            sku: product.sku,
            image: product.image,
            stock: product.stock,
            cost_price: parseFloat(product.cost_price || 0),
            quantity: 1
        };
        
        receiptLines.push(line);
        
        const imgHtml = line.image 
            ? '<img src="' + line.image + '" alt="" class="product-image">'
            : '<div class="product-image-placeholder"><i class="bi bi-box"></i></div>';
        
        const html = `
            <tr data-index="${lineIndex}">
                <td>${receiptLines.length}</td>
                <td>
                    <div class="product-info-cell">
                        ${imgHtml}
                        <div class="product-details">
                            <div class="product-name">${escapeHtml(line.name)}</div>
                            <div class="product-sku">SKU: ${escapeHtml(line.sku || 'N/A')}</div>
                        </div>
                    </div>
                </td>
                <td>${line.stock}</td>
                <td>
                    <input type="number" class="input-qty" value="1" min="1" data-index="${lineIndex}">
                </td>
                <td>
                    <input type="number" class="input-cost" value="${line.cost_price.toFixed(2)}" min="0" step="0.01" data-index="${lineIndex}">
                </td>
                <td class="line-total">$${line.cost_price.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn-remove" data-index="${lineIndex}" title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        receiptLinesBody.append(html);
        lineIndex++;
        
        updateTotals();
        emptyState.hide();
        submitBtn.prop('disabled', false);
        
        toastr.success('Product added');
    }
    
    // Update quantity
    $(document).on('change input', '.input-qty', function() {
        const index = $(this).data('index');
        const qty = parseInt($(this).val()) || 1;
        const row = $(this).closest('tr');
        const cost = parseFloat(row.find('.input-cost').val()) || 0;
        
        // Update line data
        const lineData = receiptLines.find(l => l.index === index);
        if (lineData) {
            lineData.quantity = qty;
        }
        
        // Update line total
        row.find('.line-total').text('$' + (qty * cost).toFixed(2));
        
        updateTotals();
    });
    
    // Update cost
    $(document).on('change input', '.input-cost', function() {
        const index = $(this).data('index');
        const cost = parseFloat($(this).val()) || 0;
        const row = $(this).closest('tr');
        const qty = parseInt(row.find('.input-qty').val()) || 1;
        
        // Update line data
        const lineData = receiptLines.find(l => l.index === index);
        if (lineData) {
            lineData.cost_price = cost;
        }
        
        // Update line total
        row.find('.line-total').text('$' + (qty * cost).toFixed(2));
        
        updateTotals();
    });
    
    // Remove line
    $(document).on('click', '.btn-remove', function() {
        const index = $(this).data('index');
        const row = $(this).closest('tr');
        
        // Remove from array
        receiptLines = receiptLines.filter(l => l.index !== index);
        
        // Remove row
        row.remove();
        
        // Update row numbers
        receiptLinesBody.find('tr').each(function(i) {
            $(this).find('td:first').text(i + 1);
        });
        
        updateTotals();
        
        if (receiptLines.length === 0) {
            emptyState.show();
            submitBtn.prop('disabled', true);
        }
        
        toastr.info('Product removed');
    });
    
    // Update totals
    function updateTotals() {
        let totalItems = receiptLines.length;
        let totalQty = 0;
        let totalAmount = 0;
        
        receiptLinesBody.find('tr').each(function() {
            const qty = parseInt($(this).find('.input-qty').val()) || 0;
            const cost = parseFloat($(this).find('.input-cost').val()) || 0;
            totalQty += qty;
            totalAmount += qty * cost;
        });
        
        $('#total-items').text(totalItems);
        $('#total-qty').text(totalQty);
        $('#total-amount').text('$' + totalAmount.toFixed(2));
    }
    
    // Submit Purchase Receipt
    submitBtn.on('click', function() {
        if (receiptLines.length === 0) {
            toastr.error('Please add at least one product');
            return;
        }
        
        // Gather data
        const products = [];
        receiptLinesBody.find('tr').each(function() {
            const index = parseInt($(this).data('index'));
            const lineData = receiptLines.find(l => l.index === index);
            
            if (lineData) {
                products.push({
                    product_id: lineData.product_id,
                    variation_id: lineData.variation_id,
                    quantity: parseInt($(this).find('.input-qty').val()) || 1,
                    unit_cost: parseFloat($(this).find('.input-cost').val()) || 0
                });
            }
        });
        
        const data = {
            _token: '{{ csrf_token() }}',
            products: products,
            notes: $('#receipt-notes').val()
        };
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Saving...');
        
        $.ajax({
            url: "{{ route('vendor.purchase-receipts.store') }}",
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    // Redirect to purchase receipts list
                    setTimeout(function() {
                        window.location.href = "{{ route('vendor.purchase-receipts') }}";
                    }, 1500);
                } else {
                    toastr.error(response.msg || 'Failed to create purchase receipt');
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check"></i> Save & Update Stock');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to create purchase receipt';
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    msg = xhr.responseJSON.msg;
                }
                toastr.error(msg);
                submitBtn.prop('disabled', false).html('<i class="bi bi-check"></i> Save & Update Stock');
            }
        });
    });
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Focus on search input on page load
    searchInput.focus();
});
</script>
@endsection

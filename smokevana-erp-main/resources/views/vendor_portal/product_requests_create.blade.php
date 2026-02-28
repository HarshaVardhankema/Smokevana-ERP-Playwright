@extends('layouts.vendor_portal')
@section('title', 'Request Product')

@section('css')
<style>
/* Page Header */
.page-header {
    margin-bottom: 24px;
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
    color: #ff9900;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    margin-bottom: 16px;
}

.back-link:hover {
    color: #e88a00;
    text-decoration: none;
}

/* Request Type Selector */
.request-type-selector {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.request-type-card {
    background: #fff;
    border: 2px solid var(--gray-300);
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.request-type-card:hover {
    border-color: #ff9900;
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.15);
}

.request-type-card.active {
    border-color: #ff9900;
    background: linear-gradient(135deg, rgba(255, 153, 0, 0.05) 0%, rgba(255, 173, 51, 0.08) 100%);
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.2);
}

.request-type-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #ff9900;
}

.request-type-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.request-type-desc {
    font-size: 13px;
    color: var(--gray-600);
}

/* Info Card */
.info-card {
    background: linear-gradient(135deg, rgba(255, 153, 0, 0.05) 0%, rgba(255, 173, 51, 0.08) 100%);
    border: 1px solid rgba(255, 153, 0, 0.3);
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 24px;
}

.info-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #e88a00;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-card ol {
    margin: 0;
    padding-left: 20px;
    color: var(--gray-700);
    font-size: 14px;
}

.info-card ol li {
    margin-bottom: 6px;
}

/* Content Card */
.content-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    overflow: hidden;
}

.content-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 10px;
}

.content-card-header i {
    color: var(--gray-600);
}

.content-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
}

.content-card-body {
    padding: 20px;
}

/* Search Box */
.search-box {
    position: relative;
    margin-bottom: 20px;
}

.search-box input {
    width: 100%;
    padding: 12px 16px 12px 44px;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #ff9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
}

.search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
}

/* Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    max-height: 500px;
    overflow-y: auto;
    padding: 4px;
}

@media (max-width: 992px) {
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
}

.product-card {
    background: #fff;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
}

.product-card:hover {
    border-color: var(--gray-400);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.product-card.selected {
    border-color: #ff9900;
    background: rgba(255, 153, 0, 0.03);
}

.product-image {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    background: var(--gray-100);
    object-fit: cover;
    flex-shrink: 0;
}

.product-image-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    flex-shrink: 0;
}

.product-info {
    flex: 1;
    min-width: 0;
}

.product-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-sku {
    font-size: 11px;
    color: var(--gray-600);
    margin-bottom: 2px;
}

.product-variants {
    font-size: 11px;
    color: #ff9900;
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-inventory-note {
    font-size: 11px;
    color: #16a34a;
    margin-top: 4px;
}

.btn-select {
    padding: 6px 12px;
    background: #ff9900;
    border: none;
    border-radius: 6px;
    color: #111;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-select:hover {
    background: #e88a00;
}

.btn-select.selected {
    background: #22c55e;
}

.btn-inventory {
    background: #e5e7eb;
    color: #111;
}

.btn-inventory:hover {
    background: #d1d5db;
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 40px;
    color: var(--gray-500);
}

.loading-state i {
    font-size: 32px;
    margin-bottom: 12px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: var(--gray-500);
}

/* Selected Products */
.selected-products {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-200);
}

.selected-products-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 12px;
}

.selected-products-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.selected-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 153, 0, 0.1);
    border-radius: 20px;
    font-size: 12px;
    color: #e88a00;
}

.selected-tag button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #e88a00;
    opacity: 0.7;
}

.selected-tag button:hover {
    opacity: 1;
}

/* Notes Field */
.notes-field {
    margin-top: 20px;
}

.notes-field label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.notes-field textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    min-height: 80px;
}

.notes-field textarea:focus {
    outline: none;
    border-color: #ff9900;
}

/* Submit Button */
.submit-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-cancel {
    padding: 12px 24px;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    color: var(--gray-700);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    background: var(--gray-200);
}

.btn-submit {
    padding: 12px 32px;
    background: linear-gradient(135deg, #ff9900 0%, #ffad33 100%);
    border: none;
    border-radius: 8px;
    color: #111;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-submit:hover {
    background: linear-gradient(135deg, #e88a00 0%, #ff9900 100%);
}

.btn-submit:disabled {
    background: var(--gray-300);
    cursor: not-allowed;
}

/* New Product Form */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.form-group label .required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background-color: #fff;
}

.form-control:focus {
    outline: none;
    border-color: #ff9900;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1);
}

/* Select Dropdown Styling */
select.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    cursor: pointer;
}

select.form-control:focus {
    border-color: #ff9900;
}

select.form-control option {
    padding: 10px;
    background: #fff;
    color: #111;
}

.form-hint {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
    font-style: italic;
}

/* Image Upload Area */
.image-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fafafa;
    position: relative;
}

.image-upload-area:hover {
    border-color: #ff9900;
    background: #fffbf5;
}

.image-upload-area.dragover {
    border-color: #ff9900;
    background: rgba(255, 153, 0, 0.05);
}

.upload-icon {
    font-size: 40px;
    color: #9ca3af;
    margin-bottom: 12px;
}

.upload-text {
    font-size: 14px;
    color: #374151;
    margin-bottom: 4px;
    font-weight: 500;
}

.upload-hint {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
}

.upload-preview {
    position: relative;
    display: inline-block;
}

.upload-preview img {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
}

.remove-image {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #ef4444;
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s ease;
}

.remove-image:hover {
    background: #dc2626;
    transform: scale(1.1);
}

/* Input with Prefix */
.input-with-prefix {
    position: relative;
    display: flex;
    align-items: stretch;
}

.input-prefix {
    background: #f3f4f6;
    border: 1px solid var(--gray-300);
    border-right: none;
    border-radius: 8px 0 0 8px;
    padding: 0 12px;
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.form-control.with-prefix {
    border-radius: 0 8px 8px 0;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .request-type-selector {
        grid-template-columns: 1fr;
    }
}

/* Load More */
.load-more-section {
    text-align: center;
    margin-top: 16px;
}

.btn-load-more {
    padding: 10px 24px;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    color: var(--gray-700);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-load-more:hover {
    background: var(--gray-200);
}

/* Variation Section */
.variation-section {
    margin-top: 24px;
    padding: 20px;
    background: #fafafa;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
}

.variation-section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.variation-section-title i {
    color: #ff9900;
}

.variation-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--gray-200);
}

.variation-table th {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff;
    padding: 12px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
}

.variation-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.variation-table tbody tr:hover {
    background: #fffbf5;
}

.variation-table input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    font-size: 13px;
}

.variation-table input:focus {
    outline: none;
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.1);
}

.btn-add-variation {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #ff9900 0%, #ffad33 100%);
    border: none;
    border-radius: 6px;
    color: #111;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 12px;
}

.btn-add-variation:hover {
    background: linear-gradient(135deg, #e88a00 0%, #ff9900 100%);
}

.btn-remove-variation {
    padding: 6px 10px;
    background: #fee2e2;
    border: none;
    border-radius: 6px;
    color: #dc2626;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-remove-variation:hover {
    background: #fecaca;
}

.variation-help-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 8px;
    font-style: italic;
}

.no-variations-msg {
    text-align: center;
    padding: 20px;
    color: #9ca3af;
    font-size: 14px;
}
</style>
@endsection

@section('content')
<a href="{{ route('vendor.product-requests') }}" class="back-link">
    <i class="bi bi-arrow-left"></i> Back to Requests
</a>

<div class="page-header">
    <h1 class="page-title">Request Product</h1>
    <p class="page-subtitle">Request access to existing products or propose new products</p>
</div>

<!-- Request Type Selector -->
<div class="request-type-selector">
    <div class="request-type-card active" data-type="existing" onclick="selectRequestType('existing')">
        <div class="request-type-icon">
            <i class="bi bi-boxes"></i>
        </div>
        <div class="request-type-title">Existing Products</div>
        <div class="request-type-desc">Request access to products in catalog</div>
    </div>
    <div class="request-type-card" data-type="new" onclick="selectRequestType('new')">
        <div class="request-type-icon">
            <i class="bi bi-plus"></i>
        </div>
        <div class="request-type-title">New Product</div>
        <div class="request-type-desc">Propose a new product to add</div>
    </div>
</div>

<!-- Existing Products Section -->
<div id="existing-products-section">
    <!-- Info Card -->
    <div class="info-card" style="display: none;">
        <div class="info-card-title">
            <i class="bi bi-info-circle"></i> Request Process:
        </div>
        <ol>
            <li>Select products you want to supply from the catalog below</li>
            <li>Add optional notes explaining why you want to supply these products</li>
            <li>Submit your request for admin approval</li>
            <li>Once approved, you can create Purchase Orders and Receipts for these products</li>
        </ol>
    </div>

    <!-- Search & Select Products -->
    <div class="content-card">
        <div class="content-card-header">
            <i class="bi bi-search"></i>
            <h3 class="content-card-title">Search & Select Products</h3>
        </div>
        <div class="content-card-body">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="product-search" placeholder="Search products by name or SKU...">
            </div>

            <div id="product-grid" class="product-grid">
                <div class="loading-state">
                    <i class="bi bi-arrow-repeat"></i>
                    <p>Loading products...</p>
                </div>
            </div>

            <div class="load-more-section" id="load-more-section" style="display: none;">
                <button type="button" class="btn-load-more" onclick="loadMoreProducts()">
                    <i class="bi bi-chevron-down"></i> Load More Products
                </button>
            </div>

            <!-- Selected Products -->
            <div class="selected-products" id="selected-products" style="display: none;">
                <div class="selected-products-title">
                    <i class="bi bi-check-circle-fill" style="color: #22c55e;"></i> 
                    Selected Products (<span id="selected-count">0</span>)
                </div>
                <div class="selected-products-list" id="selected-products-list"></div>
            </div>

            <!-- Notes -->
            <div class="notes-field">
                <label for="existing-notes">Notes (optional)</label>
                <textarea id="existing-notes" placeholder="Add any notes about your request..."></textarea>
            </div>

            <!-- Submit -->
            <div class="submit-section">
                <a href="{{ route('vendor.product-requests') }}" class="btn-cancel">Cancel</a>
                <button type="button" class="btn-submit" id="submit-existing-btn" onclick="submitExistingRequest()" disabled>
                    <i class="bi bi-send"></i> Submit Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Product Section -->
<div id="new-product-section" style="display: none;">
    <!-- Info Card -->
    <div class="info-card" style="display: none;">
        <div class="info-card-title">
            <i class="bi bi-info-circle"></i> New Product Request:
        </div>
        <ol>
            <li>Fill in the product details below</li>
            <li>Submit for admin review</li>
            <li>Once approved, the product will be created and assigned to you</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <i class="bi bi-plus-circle"></i>
            <h3 class="content-card-title">New Product Details</h3>
        </div>
        <div class="content-card-body">
            <form id="new-product-form" enctype="multipart/form-data">
                <!-- Image Upload -->
                <div class="form-group">
                    <label>Product Image</label>
                    <div class="image-upload-area" id="image-upload-area">
                        <input type="file" name="proposed_image" id="proposed_image" accept="image/png,image/jpg,image/jpeg" style="display: none;">
                        <div class="upload-placeholder" id="upload-placeholder">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <p class="upload-text">Click or drag to upload image</p>
                            <p class="upload-hint">PNG, JPG, JPEG up to 5MB</p>
                        </div>
                        <div class="upload-preview" id="upload-preview" style="display: none;">
                            <img id="preview-image" src="" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeImage()"><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Product Name -->
                <div class="form-group">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="proposed_name" required placeholder="Enter product name">
                </div>

                <!-- Product Type & SKU -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Type <span class="required">*</span></label>
                        <select class="form-control" name="proposed_type" id="proposed_type" onchange="toggleVariationSection()">
                            <option value="single">Single Product</option>
                            <option value="variable">Variable Product</option>
                        </select>
                        <span class="form-hint">Single: one version. Variable: multiple options (size, color, etc.)</span>
                    </div>
                    <div class="form-group" id="sku-field">
                        <label>SKU (Optional)</label>
                        <input type="text" class="form-control" name="proposed_sku" placeholder="Enter product SKU">
                        <span class="form-hint">If left blank, SKU will be auto-generated</span>
                    </div>
                </div>

                <!-- Variation Section (shown when product type is variable) -->
                <div class="variation-section" id="variation-section" style="display: none;">
                    <div class="variation-section-title">
                        <i class="bi bi-layers"></i> Product Variations
                    </div>
                    
                    <!-- Variation Template Selection -->
                    <div class="form-group">
                        <label>Variation Type <span class="required">*</span></label>
                        <select class="form-control" name="proposed_variation_template" id="variation_template">
                            <option value="">-- Select Variation Type --</option>
                            @foreach($variation_templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <span class="form-hint">E.g., Flavor, Color, Size, etc.</span>
                    </div>

                    <!-- Variation Values Table -->
                    <div id="variation-values-container">
                        <datalist id="variation-value-suggestions"></datalist>
                        <table class="variation-table" id="variation-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Value Name <span class="required">*</span></th>
                                    <th style="width: 15%;">SKU</th>
                                    <th style="width: 15%;">Barcode</th>
                                    <th style="width: 17%;">Cost Price</th>
                                    <th style="width: 17%;">Selling Price</th>
                                    <th style="width: 8%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="variation-tbody">
                                <tr class="no-variations-row">
                                    <td colspan="6" class="no-variations-msg">
                                        <i class="bi bi-info-circle"></i> Select a variation type first, then add variation values
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <button type="button" class="btn-add-variation" id="btn-add-variation" onclick="addVariationRow()" style="display: none;">
                            <i class="bi bi-plus"></i> Add Variation
                        </button>
                        <p class="variation-help-text">Add different variations like flavors, colors, or sizes with their individual prices.</p>
                    </div>
                </div>

                <!-- Barcode & Category -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Barcode (Optional)</label>
                        <input type="text" class="form-control" name="proposed_barcode" placeholder="Enter barcode/UPC">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="proposed_category_id">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Brand & Unit -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Brand</label>
                        <select class="form-control" name="proposed_brand_id">
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select class="form-control" name="proposed_unit">
                            <option value="">-- Select Unit --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->actual_name }} ({{ $unit->short_name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Cost & Selling Price (for single products) -->
                <div class="form-row" id="single-price-fields">
                    <div class="form-group">
                        <label>Cost Price</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">$</span>
                            <input type="number" class="form-control with-prefix" name="proposed_cost_price" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Selling Price</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">$</span>
                            <input type="number" class="form-control with-prefix" name="proposed_selling_price" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description (Optional)</label>
                    <textarea class="form-control" name="proposed_description" rows="4" placeholder="Enter product description..."></textarea>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label>Additional Notes</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Any additional information for the admin..."></textarea>
                </div>

                <div class="submit-section">
                    <a href="{{ route('vendor.product-requests') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
// State
let selectedProducts = [];
let currentPage = 1;
let hasMore = true;
let searchTerm = '';
let searchTimeout = null;
const inventoryUrl = "{{ route('vendor.products') }}";

// Select request type
function selectRequestType(type) {
    document.querySelectorAll('.request-type-card').forEach(card => {
        card.classList.remove('active');
    });
    document.querySelector(`.request-type-card[data-type="${type}"]`).classList.add('active');
    
    if (type === 'existing') {
        document.getElementById('existing-products-section').style.display = 'block';
        document.getElementById('new-product-section').style.display = 'none';
    } else {
        document.getElementById('existing-products-section').style.display = 'none';
        document.getElementById('new-product-section').style.display = 'block';
    }
}

// Load products
function loadProducts(append = false) {
    if (!append) {
        currentPage = 1;
        document.getElementById('product-grid').innerHTML = `
            <div class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p>Loading products...</p>
            </div>
        `;
    }

    $.ajax({
        url: "{{ route('vendor.product-requests.catalog') }}",
        method: 'GET',
        data: {
            search: searchTerm,
            page: currentPage
        },
        success: function(response) {
            console.log('Catalog response:', response);
            if (response.success) {
                hasMore = response.has_more;
                renderProducts(response.products, append);
                
                if (hasMore) {
                    document.getElementById('load-more-section').style.display = 'block';
                } else {
                    document.getElementById('load-more-section').style.display = 'none';
                }
            } else {
                document.getElementById('product-grid').innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>${response.msg || 'Failed to load products'}</p>
                    </div>
                `;
            }
        },
        error: function(xhr, status, error) {
            console.error('Catalog error:', xhr.responseText, status, error);
            document.getElementById('product-grid').innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Failed to load products. Please refresh the page.</p>
                    <small style="color:#999;">Error: ${error || status}</small>
                </div>
            `;
        }
    });
}

// Render products
function renderProducts(products, append) {
    const grid = document.getElementById('product-grid');
    
    if (!append) {
        grid.innerHTML = '';
    }

    if (products.length === 0 && !append) {
        grid.innerHTML = `
            <div class="empty-state" style="grid-column: 1 / -1;">
                <i class="bi bi-search"></i>
                <p>No products found</p>
            </div>
        `;
        return;
    }

    products.forEach(product => {
        const isSelected = selectedProducts.some(p => p.id === product.id);
        const card = document.createElement('div');
        card.className = `product-card ${isSelected ? 'selected' : ''}`;
        card.id = `product-${product.id}`;
        
        let imageHtml = product.image 
            ? `<img src="${product.image}" alt="${product.name}" class="product-image">`
            : `<div class="product-image-placeholder"><i class="bi bi-box"></i></div>`;
        
        let variantHtml = product.variant_count > 0 
            ? `<div class="product-variants"><i class="bi bi-layers"></i> ${product.variant_count} variants</div>`
            : '';

        const actionButton = product.in_inventory
            ? `<button type="button" class="btn-select btn-inventory" onclick="goToInventory()">
                    <i class="bi bi-box-arrow-up-right"></i> View Inventory
               </button>`
            : `<button type="button" class="btn-select ${isSelected ? 'selected' : ''}" onclick="toggleProduct(${product.id}, '${escapeHtml(product.name)}')">
                    ${isSelected ? '<i class="bi bi-check"></i> Selected' : '<i class="bi bi-plus"></i> Select'}
               </button>`;

        const inventoryNote = product.in_inventory
            ? `<div class="product-inventory-note"><i class="bi bi-check-circle-fill"></i> Already in your inventory</div>`
            : '';

        card.innerHTML = `
            ${imageHtml}
            <div class="product-info">
                <div class="product-name" title="${product.name}">${product.name}</div>
                <div class="product-sku">SKU: ${product.sku || 'N/A'}</div>
                ${variantHtml}
                ${inventoryNote}
            </div>
            ${actionButton}
        `;
        
        grid.appendChild(card);
    });
}

// Toggle product selection
function toggleProduct(productId, productName) {
    const index = selectedProducts.findIndex(p => p.id === productId);
    
    if (index > -1) {
        selectedProducts.splice(index, 1);
    } else {
        selectedProducts.push({ id: productId, name: productName });
    }
    
    updateSelectedUI();
    updateProductCardUI(productId);
}

// Remove product from selection
function removeProduct(productId) {
    selectedProducts = selectedProducts.filter(p => p.id !== productId);
    updateSelectedUI();
    updateProductCardUI(productId);
}

// Update selected products UI
function updateSelectedUI() {
    const container = document.getElementById('selected-products');
    const list = document.getElementById('selected-products-list');
    const countEl = document.getElementById('selected-count');
    const submitBtn = document.getElementById('submit-existing-btn');

    if (selectedProducts.length > 0) {
        container.style.display = 'block';
        countEl.textContent = selectedProducts.length;
        submitBtn.disabled = false;
        
        list.innerHTML = selectedProducts.map(p => `
            <span class="selected-tag">
                ${p.name}
                <button type="button" onclick="removeProduct(${p.id})"><i class="bi bi-x"></i></button>
            </span>
        `).join('');
    } else {
        container.style.display = 'none';
        submitBtn.disabled = true;
    }
}

// Update product card UI
function updateProductCardUI(productId) {
    const card = document.getElementById(`product-${productId}`);
    if (!card) return;
    
    const isSelected = selectedProducts.some(p => p.id === productId);
    const btn = card.querySelector('.btn-select');
    if (!btn || btn.classList.contains('btn-inventory')) {
        return;
    }
    
    if (isSelected) {
        card.classList.add('selected');
        btn.classList.add('selected');
        btn.innerHTML = '<i class="bi bi-check"></i> Selected';
    } else {
        card.classList.remove('selected');
        btn.classList.remove('selected');
        btn.innerHTML = '<i class="bi bi-plus"></i> Select';
    }
}

function goToInventory() {
    window.location.href = inventoryUrl;
}

// Load more products
function loadMoreProducts() {
    currentPage++;
    loadProducts(true);
}

// Submit existing product request
function submitExistingRequest() {
    if (selectedProducts.length === 0) {
        toastr.error('Please select at least one product');
        return;
    }

    const btn = document.getElementById('submit-existing-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Submitting...';

    $.ajax({
        url: "{{ route('vendor.product-requests.submit-existing') }}",
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            product_ids: selectedProducts.map(p => p.id),
            notes: document.getElementById('existing-notes').value
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.msg);
                window.location.href = "{{ route('vendor.product-requests') }}";
            } else {
                toastr.error(response.msg || 'Failed to submit request');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send"></i> Submit Request';
            }
        },
        error: function(xhr) {
            toastr.error('Failed to submit request. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send"></i> Submit Request';
        }
    });
}

// Submit new product request
$('#new-product-form').on('submit', function(e) {
    e.preventDefault();
    
    const btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Submitting...');
    
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{ route('vendor.product-requests.submit-new') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.msg);
                window.location.href = "{{ route('vendor.product-requests') }}";
            } else {
                toastr.error(response.msg || 'Failed to submit request');
                btn.prop('disabled', false).html('<i class="bi bi-send"></i> Submit New Product Request');
            }
        },
        error: function(xhr) {
            let msg = 'Failed to submit request. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                msg = errors.join(', ');
            }
            toastr.error(msg);
            btn.prop('disabled', false).html('<i class="bi bi-send"></i> Submit New Product Request');
        }
    });
});

// Search functionality
document.getElementById('product-search').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchTerm = e.target.value;
        loadProducts();
    }, 300);
});

// Escape HTML helper
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'");
}

// Image Upload functionality
const uploadArea = document.getElementById('image-upload-area');
const fileInput = document.getElementById('proposed_image');
const placeholder = document.getElementById('upload-placeholder');
const preview = document.getElementById('upload-preview');
const previewImage = document.getElementById('preview-image');

if (uploadArea) {
    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageFile(files[0]);
        }
    });
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleImageFile(e.target.files[0]);
        }
    });
}

function handleImageFile(file) {
    // Validate file type
    const validTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    if (!validTypes.includes(file.type)) {
        toastr.error('Please upload a PNG, JPG, or JPEG image');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        toastr.error('Image size must be less than 5MB');
        return;
    }
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.src = e.target.result;
        placeholder.style.display = 'none';
        preview.style.display = 'inline-block';
    };
    reader.readAsDataURL(file);
    
    // Update file input
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    fileInput.files = dataTransfer.files;
}

function removeImage() {
    fileInput.value = '';
    previewImage.src = '';
    placeholder.style.display = 'block';
    preview.style.display = 'none';
}

// =============================================
// VARIATION HANDLING
// =============================================
let variationRowIndex = 0;
const variationValues = @json($variation_values ?? []);

// Toggle variation section based on product type
function toggleVariationSection() {
    const productType = document.getElementById('proposed_type').value;
    const variationSection = document.getElementById('variation-section');
    const skuField = document.getElementById('sku-field');
    const singlePriceFields = document.getElementById('single-price-fields');
    
    if (productType === 'variable') {
        variationSection.style.display = 'block';
        singlePriceFields.style.display = 'none'; // Hide single product price fields
        // Change SKU label for variable products
        skuField.querySelector('label').innerHTML = 'Base SKU (Optional)';
        skuField.querySelector('.form-hint').innerHTML = 'Base SKU prefix for variations';
    } else {
        variationSection.style.display = 'none';
        singlePriceFields.style.display = 'grid'; // Show single product price fields
        skuField.querySelector('label').innerHTML = 'SKU (Optional)';
        skuField.querySelector('.form-hint').innerHTML = 'If left blank, SKU will be auto-generated';
        // Clear variations when switching back to single
        variationRowIndex = 0;
        document.getElementById('variation-tbody').innerHTML = `
            <tr class="no-variations-row">
                <td colspan="6" class="no-variations-msg">
                    <i class="bi bi-info-circle"></i> Select a variation type first, then add variation values
                </td>
            </tr>
        `;
        document.getElementById('btn-add-variation').style.display = 'none';
        document.getElementById('variation_template').value = '';
    }
}

// Handle variation template change
document.getElementById('variation_template').addEventListener('change', function() {
    const templateId = this.value;
    const tbody = document.getElementById('variation-tbody');
    const addBtn = document.getElementById('btn-add-variation');
    
    // Reset
    variationRowIndex = 0;
    tbody.innerHTML = '';
    
    if (templateId) {
        addBtn.style.display = 'inline-flex';
        updateVariationSuggestions(templateId);
        // Always start with a single empty row; suggestions will appear while typing
            addVariationRow();
    } else {
        addBtn.style.display = 'none';
        updateVariationSuggestions(null);
        tbody.innerHTML = `
            <tr class="no-variations-row">
                <td colspan="6" class="no-variations-msg">
                    <i class="bi bi-info-circle"></i> Select a variation type first, then add variation values
                </td>
            </tr>
        `;
    }
});

function updateVariationSuggestions(templateId) {
    const datalist = document.getElementById('variation-value-suggestions');
    datalist.innerHTML = '';
    if (!templateId) {
        return;
    }
    const templateValues = variationValues[templateId] || [];
    templateValues.forEach((val) => {
        const option = document.createElement('option');
        option.value = val.name;
        datalist.appendChild(option);
    });
}

// Add a new variation row
function addVariationRow(prefilledValue = '') {
    const tbody = document.getElementById('variation-tbody');
    const templateId = document.getElementById('variation_template').value;
    
    // Remove "no variations" message if present
    const noVarRow = tbody.querySelector('.no-variations-row');
    if (noVarRow) noVarRow.remove();
    
    const row = document.createElement('tr');
    row.className = 'variation-row';
    row.dataset.index = variationRowIndex;
    
    row.innerHTML = `
        <td>
            <input type="text" name="proposed_variations[${variationRowIndex}][value]" 
                   value="${escapeHtml(prefilledValue)}" placeholder="e.g., Cherry Pie" ${templateId ? 'list="variation-value-suggestions"' : ''} required>
        </td>
        <td>
            <input type="text" name="proposed_variations[${variationRowIndex}][sku]" placeholder="Auto">
        </td>
        <td>
            <input type="text" name="proposed_variations[${variationRowIndex}][barcode]" placeholder="Barcode/UPC">
        </td>
        <td>
            <div class="input-with-prefix" style="display: flex;">
                <span class="input-prefix" style="border-radius: 6px 0 0 6px; padding: 0 8px;">$</span>
                <input type="number" name="proposed_variations[${variationRowIndex}][cost_price]" 
                       step="0.01" min="0" placeholder="0.00" style="border-radius: 0 6px 6px 0;">
            </div>
        </td>
        <td>
            <div class="input-with-prefix" style="display: flex;">
                <span class="input-prefix" style="border-radius: 6px 0 0 6px; padding: 0 8px;">$</span>
                <input type="number" name="proposed_variations[${variationRowIndex}][selling_price]" 
                       step="0.01" min="0" placeholder="0.00" style="border-radius: 0 6px 6px 0;">
            </div>
        </td>
        <td style="text-align: center;">
            <button type="button" class="btn-remove-variation" onclick="removeVariationRow(this)" title="Remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    variationRowIndex++;
    
    // Focus on the value input of the new row
    row.querySelector('input[name*="[value]"]').focus();
}

// Remove a variation row
function removeVariationRow(btn) {
    const row = btn.closest('tr');
    const tbody = document.getElementById('variation-tbody');
    
    row.remove();
    
    // If no rows left, show the "no variations" message
    if (tbody.querySelectorAll('.variation-row').length === 0) {
        const templateId = document.getElementById('variation_template').value;
        if (templateId) {
            // Just add a new empty row
            addVariationRow();
        } else {
            tbody.innerHTML = `
                <tr class="no-variations-row">
                    <td colspan="6" class="no-variations-msg">
                        <i class="bi bi-info-circle"></i> Select a variation type first, then add variation values
                    </td>
                </tr>
            `;
        }
    }
}

// Validate variations before form submit
function validateVariations() {
    const productType = document.getElementById('proposed_type').value;
    
    if (productType !== 'variable') {
        return true;
    }
    
    const templateId = document.getElementById('variation_template').value;
    if (!templateId) {
        toastr.error('Please select a variation type for variable products');
        return false;
    }
    
    const variationRows = document.querySelectorAll('.variation-row');
    if (variationRows.length === 0) {
        toastr.error('Please add at least one variation');
        return false;
    }
    
    // Check if all variation values have names
    let hasEmptyValue = false;
    variationRows.forEach(row => {
        const valueInput = row.querySelector('input[name*="[value]"]');
        if (!valueInput.value.trim()) {
            hasEmptyValue = true;
            valueInput.style.borderColor = '#dc2626';
        } else {
            valueInput.style.borderColor = '';
        }
    });
    
    if (hasEmptyValue) {
        toastr.error('Please fill in all variation value names');
        return false;
    }
    
    return true;
}

// Override form submit to include variation validation
$('#new-product-form').off('submit').on('submit', function(e) {
    e.preventDefault();
    
    if (!validateVariations()) {
        return false;
    }
    
    const btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat"></i> Submitting...');
    
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');

    $.ajax({
        url: "{{ route('vendor.product-requests.submit-new') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.msg);
                window.location.href = "{{ route('vendor.product-requests') }}";
            } else {
                toastr.error(response.msg || 'Failed to submit request');
                btn.prop('disabled', false).html('<i class="bi bi-send"></i> Submit Request');
            }
        },
        error: function(xhr) {
            let msg = 'Failed to submit request. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                msg = errors.join(', ');
            }
            toastr.error(msg);
            btn.prop('disabled', false).html('<i class="bi bi-send"></i> Submit Request');
        }
    });
});

// Initialize
$(document).ready(function() {
    loadProducts();
    toggleVariationSection(); // Initialize variation section visibility
});
</script>
@endsection

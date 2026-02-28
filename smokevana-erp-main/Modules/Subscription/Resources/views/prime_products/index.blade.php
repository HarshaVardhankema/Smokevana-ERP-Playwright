@extends('layouts.app')

@section('title', __('subscription::lang.prime_products'))

@section('content')
<style>
    .prime-products-page {
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: linear-gradient(90deg, #232f3e 0%, #37475a 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(255, 215, 0, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header-card h1 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .page-header-card h1 .icon-box {
        background: rgba(0,0,0,0.25);
        border-radius: 12px;
        padding: 10px;
        display: flex;
    }
    
    .page-header-card h1 small {
        font-size: 13px;
        font-weight: 400;
        opacity: 0.8;
        display: block;
    }
    
    .btn-add-product {
        background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%);
        color: #0F1111;
        border: 1px solid #FFA500;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(255, 153, 0, 0.3);
    }
    
    .btn-add-product:hover {
        background: linear-gradient(180deg, #FFE033 0%, #FFB020 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
        color: #0F1111;
    }
    
    /* Stats */
    .prime-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .prime-stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .prime-stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    
    .prime-stat-card .stat-icon.gold {
        background: linear-gradient(135deg, #00a8e1, #0077a3);
        color: #fff;
    }
    
    .prime-stat-card .stat-icon.purple {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
    }
    
    .prime-stat-card .stat-icon.green {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: #fff;
    }
    
    .prime-stat-card .stat-content h3 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }
    
    .prime-stat-card .stat-content span {
        font-size: 13px;
        color: #6c757d;
    }
    
    /* Products Card */
    .products-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .products-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .products-card .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .products-card .card-header h3 i {
        color: #00a8e1;
    }
    
    /* Filter Bar */
    .filter-bar {
        display: flex;
        gap: 12px;
        padding: 16px 24px;
        background: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .filter-bar .form-control,
    .filter-bar .form-select {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 10px 14px;
        font-size: 13px;
        background-color: #fff !important;
        color: #131921 !important;
    }
    
    /* Category dropdown - Amazon-style light (dropdown is appended to body) */
    .prime-category-dropdown.select2-dropdown,
    .prime-category-dropdown {
        background-color: #fff !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
    }
    .prime-category-dropdown .select2-results__option {
        background-color: #fff !important;
        color: #131921 !important;
        padding: 10px 16px;
    }
    .prime-category-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #FFF3E0 !important;
        color: #131921 !important;
    }
    .prime-category-dropdown .select2-results__option[aria-selected=true] {
        background-color: #FFF8F0 !important;
        color: #e88b00 !important;
    }
    .prime-products-page .filter-bar .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
    }
    .prime-products-page .filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #131921 !important;
    }
    
    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        padding: 24px;
    }
    
    .product-item {
        background: #fff;
        border: 2px solid #f0f0f0;
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .product-item:hover {
        border-color: #00a8e1;
        box-shadow: 0 8px 30px rgba(0, 168, 225, 0.25);
        transform: translateY(-4px);
    }
    
    .product-item .prime-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: linear-gradient(135deg, #00a8e1, #0077a3);
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
        z-index: 1;
    }
    
    .product-item .product-image {
        height: 160px;
        background: linear-gradient(135deg, #f8f9fa, #e8e8e8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #ddd;
    }
    
    .product-item .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-item .product-info {
        padding: 16px;
    }
    
    .product-item .product-info h5 {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 6px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .product-item .product-info .sku {
        font-size: 11px;
        color: #999;
        margin-bottom: 10px;
    }
    
    .product-item .product-info .price {
        font-size: 18px;
        font-weight: 700;
        color: #111;
    }
    
    .product-item .product-actions {
        display: flex;
        gap: 8px;
        padding: 12px 16px;
        background: #f8f9fa;
        border-top: 1px solid #f0f0f0;
    }
    
    .product-item .product-actions .btn {
        flex: 1;
        padding: 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .btn-view-product {
        background: #e3f2fd;
        color: #1976d2;
        border: none;
    }
    
    .btn-view-product:hover {
        background: #1976d2;
        color: #fff;
    }
    
    .btn-remove-prime {
        background: #ffebee;
        color: #d32f2f;
        border: none;
    }
    
    .btn-remove-prime:hover {
        background: #d32f2f;
        color: #fff;
    }
    
    /* Empty State */
    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #00a8e1;
        margin-bottom: 20px;
    }
    
    .empty-state h4 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #6c757d;
        margin-bottom: 24px;
    }
    
    
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 992px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .prime-stats {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 576px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="prime-products-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <h1>
                <div class="icon-box">
                    <i class="fas fa-crown"></i>
                </div>
                <div>
                    Prime Products
                   
                </div>
            </h1>
            @can('subscription.create')
            <button type="button" class="btn btn-add-product" data-toggle="modal" data-target="#addPrimeProductModal">
                <i class="fas fa-plus"></i> Add Prime Product
            </button>
            @endcan
        </div>

        {{-- Stats --}}
        <div class="prime-stats">
            <div class="prime-stat-card">
                <div class="stat-icon gold">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_prime_products'] ?? 0 }}</h3>
                    <span>Prime Products</span>
                </div>
            </div>
            <div class="prime-stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['categories_count'] ?? 0 }}</h3>
                    <span>Categories</span>
                </div>
            </div>
            <div class="prime-stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['prime_orders'] ?? 0 }}</h3>
                    <span>Prime Orders (This Month)</span>
                </div>
            </div>
        </div>

        {{-- Products Card --}}
        <div class="products-card">
            <div class="card-header">
                <h3><i class="fas fa-boxes"></i> All Prime Products</h3>
            </div>
            
            <div class="filter-bar">
                <input type="text" id="search_products" class="form-control" placeholder="Search products..." style="width: 300px;">
                <select id="category_filter" class="form-select select2" style="width: 200px;">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="products_container">
                @if(isset($prime_products) && $prime_products->count() > 0)
                    <div class="products-grid">
                        @foreach($prime_products as $pp)
                            <div class="product-item">
                                <span class="prime-badge"><i class="fas fa-crown"></i> Prime</span>
                                <div class="product-image">
                                    @if($pp->product && $pp->product->image_url)
                                        <img src="{{ $pp->product->image_url }}" alt="{{ $pp->product->name ?? '' }}">
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div class="product-info">
                                    <h5>{{ $pp->product->name ?? 'Unknown Product' }}</h5>
                                    <div class="sku">SKU: {{ $pp->product->sku ?? 'N/A' }}</div>
                                    <div class="price">${{ number_format($pp->product->sell_price_inc_tax ?? 0, 2) }}</div>
                                </div>
                                <div class="product-actions">
                                    <a href="{{ action([\App\Http\Controllers\ProductController::class, 'view'], [$pp->product_id]) }}" class="btn btn-view-product">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button type="button" class="btn btn-remove-prime" data-id="{{ $pp->id }}">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-crown"></i>
                        <h4>No Prime Products Yet</h4>
                        <p>Start adding products to make them exclusive for Prime subscribers.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Add Prime Product Modal - Amazon themed --}}
<div class="modal fade" id="addPrimeProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content amazon-prime-modal">
            <div class="modal-header amazon-prime-modal-header">
                <h5 class="modal-title amazon-prime-modal-title">
                    <i class="fas fa-crown"></i>
                    Add Prime Product
                </h5>
                <button type="button" class="close amazon-prime-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('subscription.prime-products.store') }}" method="POST">
                @csrf
                <div class="modal-body amazon-prime-modal-body">
                    <div class="mb-3">
                        <label class="form-label amazon-prime-label">Select Product</label>
                        <select name="product_id" class="form-select select2 amazon-prime-input" required style="width: 100%;">
                            <option value="">Search and select a product...</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label amazon-prime-label">Start Date (Optional)</label>
                                <input type="date" name="prime_start_date" class="form-control amazon-prime-input">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label amazon-prime-label">End Date (Optional)</label>
                                <input type="date" name="prime_end_date" class="form-control amazon-prime-input">
                            </div>
                        </div>
                    </div>
                    <div class="form-check amazon-prime-exclusive-box">
                        <input type="checkbox" name="is_exclusive" value="1" class="form-check-input" id="is_exclusive">
                        <label class="form-check-label" for="is_exclusive">
                            Make this product <strong>exclusively</strong> available to Prime members only
                        </label>
                    </div>
                </div>
                <div class="modal-footer amazon-prime-modal-footer">
                    <button type="button" class="btn amazon-prime-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn amazon-prime-btn-primary">
                        <i class="fas fa-plus"></i> Add Product
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
    // Category filter - light Amazon-style dropdown
    if ($.fn.select2 && $('#category_filter').length) {
        try {
            if ($('#category_filter').data('select2')) $('#category_filter').select2('destroy');
            $('#category_filter').select2({ dropdownCssClass: 'prime-category-dropdown' });
        } catch (e) {
            $('#category_filter').select2({ dropdownCssClass: 'prime-category-dropdown' });
        }
    }
    
    // Initialize Select2 for product search
    if ($.fn.select2) {
        $('#addPrimeProductModal select[name="product_id"]').select2({
            dropdownParent: $('#addPrimeProductModal'),
            ajax: {
                url: '{{ route("subscription.prime-products.search-products") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { term: params.term };
                },
                processResults: function(data) {
                    return {
                        // Support both shapes: {results:[...]} or [...]
                        results: $.map((data && data.results) ? data.results : data, function(item) {
                            return {
                                id: item.id,
                                text: item.name + ' (SKU: ' + (item.sku || 'N/A') + ')'
                            };
                        })
                    };
                }
            },
            minimumInputLength: 2
        });
    }

    // Remove prime product
    $(document).on('click', '.btn-remove-prime', function() {
        var id = $(this).data('id');
        var btn = $(this);
        
        Swal.fire({
            title: 'Remove Prime Status?',
            text: "This will remove the Prime exclusive status from this product.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('subscription/prime-products') }}/" + id,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            btn.closest('.product-item').fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        });
    });
});
</script>
@endsection

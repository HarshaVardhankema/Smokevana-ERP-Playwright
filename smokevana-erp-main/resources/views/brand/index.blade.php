@extends('layouts.app')
@section('title', 'Brands')

@section('css')
<style>
    /* Amazon theme – keep existing */
    .brand-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(15,17,17,0.4);
    }
    .brand-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff; }
    .brand-header-banner .banner-title i { color: #fff !important; }
    .brand-header-banner .banner-subtitle { font-size: 13px; color: rgba(249,250,251,0.88); margin: 4px 0 0 0; }
    .amazon-orange-add {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: white !important;
    }
    .amazon-orange-add:hover { color: white !important; opacity: 0.95; }

    /* Summary cards – reference style */
    .brand-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 992px) {
        .brand-stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .brand-stats-row { grid-template-columns: 1fr; }
    }
    .brand-stat-card {
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(15,17,17,0.08);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .brand-stat-card__icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .brand-stat-card__icon--blue   { background: #e6f2ff; color: #232f3e; }
    .brand-stat-card__icon--green  { background: #e6f7ed; color: #067d62; }
    .brand-stat-card__icon--purple { background: #f0e6ff; color: #5c3d99; }
    .brand-stat-card__icon--yellow { background: #fff8e6; color: #c7511f; }
    .brand-stat-card__value { font-size: 1.5rem; font-weight: 700; color: #0f1111; line-height: 1.2; }
    .brand-stat-card__label { font-size: 0.8125rem; color: #565959; margin-top: 0.25rem; }

    /* Main table card */
    .brand-main-card {
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(15,17,17,0.08);
        overflow: hidden;
    }
    .brand-main-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e7e7e7;
    }
    .brand-main-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }
    .brand-main-title .icon-wrap {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        background: #e6f2ff;
        color: #232f3e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .brand-main-title .title-text { font-size: 1.25rem; font-weight: 700; color: #0f1111; }
    .brand-main-title .subtitle { font-size: 0.8125rem; color: #565959; display: block; margin-top: 2px; }

    /* Controls bar */
    .brand-controls-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        background: #f7f8f8;
        border-bottom: 1px solid #e7e7e7;
    }
    .brand-controls-left { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
    .brand-tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #0f1111;
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 6px;
    }
    .brand-tab-pill .badge {
        background: #232f3e;
        color: #fff;
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
    }
    .brand-controls-right { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
    .brand-filter-select {
        padding: 0.4rem 1.75rem 0.4rem 0.5rem;
        font-size: 0.8125rem;
        border: 1px solid #888c8c;
        border-radius: 4px;
        background: #fff;
        color: #0f1111;
    }
    .brand-filter-select:focus { outline: none; border-color: #ff9900; }

    /* Table overrides – Amazon style */
    .brand-main-card .dataTables_wrapper { padding: 0; }
    .brand-main-card #brands_table thead th {
        background: #f7f8f8 !important;
        border-bottom: 2px solid #e7e7e7 !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        color: #0f1111 !important;
    }
    .brand-main-card #brands_table tbody td {
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem !important;
        color: #0f1111 !important;
        vertical-align: middle !important;
    }
    .brand-main-card #brands_table tbody tr:hover { background: #f7f8f8 !important; }
    .brand-main-card .product-thumbnail-small {
        max-width: 40px;
        max-height: 40px;
        object-fit: contain;
        border-radius: 4px;
    }

    /* Action buttons – reference colors */
    .btn-brand-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 4px;
        border: 1px solid;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        transition: opacity 0.15s;
    }
    .btn-brand-action:hover { opacity: 0.9; text-decoration: none; }
    .btn-brand-edit   { background: #e6f2ff; border-color: #232f3e; color: #232f3e; }
    .btn-brand-delete { background: #fce8e6; border-color: #c7511f; color: #b12704; }
    .btn-brand-view   { background: #f0e6ff; border-color: #5c3d99; color: #5c3d99; }
    .btn-brand-settings { background: #fff8e6; border-color: #c7511f; color: #905a00; }

    /* Hide first column sorting icons */
    #brands_table th:nth-child(1).sorting:after,
    #brands_table th:nth-child(1).sorting_asc:after,
    #brands_table th:nth-child(1).sorting_desc:after { display: none !important; }
    .brands_modal .modal-content { overflow: hidden; }
    .brands_modal .modal-header { overflow: hidden; }
    .brands_modal .modal-header .close { margin-top: -2px; padding: 0 8px; }
</style>
@endsection

@section('content')
    <section class="content-header">
        <div class="brand-header-banner">
            <h1 class="banner-title"><i class="fas fa-tags"></i> @lang('brand.brands')</h1>
            <p class="banner-subtitle">@lang('brand.manage_your_brands')</p>
        </div>
    </section>

    <section class="content">
        {{-- Summary cards --}}
        <div class="brand-stats-row">
            <div class="brand-stat-card">
                <div class="brand-stat-card__icon brand-stat-card__icon--blue">
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <div class="brand-stat-card__value">{{ number_format($total_brands ?? 0) }}</div>
                    <div class="brand-stat-card__label">Total Brands</div>
                </div>
            </div>
            <div class="brand-stat-card">
                <div class="brand-stat-card__icon brand-stat-card__icon--green">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="brand-stat-card__value">{{ number_format($public_brands ?? 0) }}</div>
                    <div class="brand-stat-card__label">Public Brands</div>
                </div>
            </div>
            <div class="brand-stat-card">
                <div class="brand-stat-card__icon brand-stat-card__icon--purple">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <div class="brand-stat-card__value">{{ number_format($products_assigned ?? 0) }}</div>
                    <div class="brand-stat-card__label">Products Assigned</div>
                </div>
            </div>
            <div class="brand-stat-card">
                <div class="brand-stat-card__icon brand-stat-card__icon--yellow">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <div class="brand-stat-card__value">{{ number_format($top_brand_products ?? 0) }}</div>
                    <div class="brand-stat-card__label">Top Brand Products</div>
                </div>
            </div>
        </div>

        <div class="brand-main-card">
            <div class="brand-main-header">
                <div class="brand-main-title">
                    <div class="icon-wrap"><i class="fas fa-tag"></i></div>
                    <div>
                        <span class="title-text">@lang('brand.brands')</span>
                        <span class="subtitle">@lang('brand.manage_your_brands')</span>
                    </div>
                </div>
                @can('brand.create')
                    <a id="dynamic_button" class="btn btn-primary amazon-orange-add btn-modal"
                        data-href="{{ action([\App\Http\Controllers\BrandController::class, 'create']) }}"
                        data-container=".brands_modal">
                        <i class="fas fa-plus"></i> Add Brand
                    </a>
                @endcan
            </div>

            <div class="brand-controls-bar">
                <div class="brand-controls-left">
                    <span class="brand-tab-pill">
                        All Brands
                        <span class="badge">{{ number_format($total_brands ?? 0) }}</span>
                    </span>
                </div>
                <div class="brand-controls-right">
                    <select id="brand_visibility_filter" class="brand-filter-select">
                        <option value="all">All Visibility</option>
                        <option value="public">Public</option>
                        <option value="coming soon">Coming soon</option>
                        <option value="protected">Protected</option>
                    </select>
                    <span class="brand-filter-select" style="cursor: default; border: none; background: transparent;">Show 25</span>
                </div>
            </div>

            @can('brand.view')
                <div class="table-responsive">
                    <table style="min-width: max-content;" class="table table-bordered table-striped ajax_view hide-footer" id="brands_table">
                        <thead>
                            <tr>
                                <th>BRAND.LOGO</th>
                                <th>@lang('brand.brands')</th>
                                <th>Slug</th>
                                <th>Visibility</th>
                                <th>@lang('brand.note')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        </div>

        <div class="modal fade brands_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade" id="viewBrandModal2" tabindex="-1" role="dialog" aria-labelledby="viewBrandModalLabel"></div>
    </section>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).on('click', 'button.view_brand_button', function () {
    var url = $(this).data('href');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (result) {
            $('#viewBrandModal2').html(result);
            $('#viewBrandModal2').modal('show');
        },
        error: function () {
            alert("@lang('messages.something_went_wrong')");
        }
    });
});
$('#viewBrandModal').on('shown.bs.modal', function () {
    var table = $('#preview_table').DataTable({
        scrollX: true,
        scrollY: true,
        responsive: true
    });
    table.columns.adjust().draw();
});

// Visibility filter – reload table so ajax data callback sends new value
$(document).ready(function() {
    $(document).on('change', '#brand_visibility_filter', function() {
        if (typeof brands_table !== 'undefined') {
            brands_table.ajax.reload();
        }
    });
});
</script>
@endsection

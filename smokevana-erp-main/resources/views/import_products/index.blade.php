@extends('layouts.app')
@section('title', __('product.import_products'))

@section('css')
<style>
    .import-products-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }
    .import-products-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .import-products-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }
    .import-products-header-title i {
        font-size: 22px;
        color: #ffffff !important;
    }
    .import-products-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }
    /* Hide optional rows in instructions table */
    .import-products-page .table-striped tr.import-instruction-optional {
        display: none !important;
    }
    /* Hide Column Number column (first column) */
    .import-products-page .table-striped th:first-child,
    .import-products-page .table-striped td:first-child {
        display: none !important;
    }
    /* Instructions section: clean white background (override beige/cream) */
    .import-products-page .instructions-box,
    .import-products-page .instructions-box .tw-p-2,
    .import-products-page .instructions-box .tw-flow-root,
    .import-products-page .instructions-box .tw-py-2 {
        background: #ffffff !important;
    }
    .import-products-page .instructions-box .box-header {
        background: #f7f8f8 !important;
        color: #0f1111 !important;
        border-bottom: 1px solid #D5D9D9;
    }
    .import-products-page .instructions-box .table-striped {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff !important;
    }
    .import-products-page .instructions-box .table-striped thead th {
        background: #f7f8f8 !important;
        color: #0f1111 !important;
        border-color: #D5D9D9 !important;
        padding: 12px 16px !important;
        font-weight: 600;
        font-size: 13px;
    }
    .import-products-page .instructions-box .table-striped tbody td {
        padding: 12px 16px;
        border-color: #e5e7eb;
        color: #0f1111;
        font-size: 13px;
    }
    .import-products-page .instructions-box .table-striped tbody tr:nth-child(even) {
        background: #f9fafb !important;
    }
    .import-products-page .instructions-box .table-striped tbody tr:nth-child(odd) {
        background: #ffffff !important;
    }
    .import-products-page .instructions-box .table-striped tbody tr:hover {
        background: #fff8e7 !important;
    }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div class="import-products-header-banner">
        <div class="import-products-header-content">
            <h1 class="import-products-header-title">
                <i class="fas fa-file-import"></i>
                @lang('product.import_products')
            </h1>
            <p class="import-products-header-subtitle">
                Bulk import products from CSV/Excel. Download the template, fill your data, and import image mapping.
            </p>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content import-products-page">
    
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
                <div class="row">
                    <div class="col-sm-12">
                        <h4>Standard Import</h4>
                        {!! Form::open(['url' => action([\App\Http\Controllers\ImportProductsController::class, 'store']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                        {!! Form::file('products_csv', ['accept'=> '.xls, .xlsx, .csv', 'required' => 'required']); !!}
                                    </div>
                                </div>
                                <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.submit')</button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-sm-4">
                                <a href="{{ url('/import-products/download-template') }}" class="tw-dw-btn tw-dw-btn-success tw-text-white"><i class="fa fa-download"></i> Download Template for Products (CSV)</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <hr> --}}
                <div class="row" hidden>
                    <div class="col-sm-12">
                        <h4>Import with SKU Regeneration</h4>
                        <p class="text-muted">If a product with the same name exists, the old SKU will be removed and a new auto-generated SKU will be created. New products will be created normally.</p>
                        {!! Form::open(['url' => action([\App\Http\Controllers\ImportProductsController::class, 'storeWithSkuRegeneration']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                            <div class="row">
                                <div class="col-sm-6">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                        {!! Form::file('products_csv', ['accept'=> '.xls, .xlsx, .csv', 'required' => 'required', 'id' => 'products_csv_sku_regen']); !!}
                                      </div>
                                </div>
                                <div class="col-sm-4">
                                <br>
                                    <button type="submit" class="tw-dw-btn tw-dw-btn-warning tw-text-white">Import with SKU Regeneration</button>
                                </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-sm-12">
                        <h4>Import Image Mapping</h4>
                        {!! Form::open(['url' => action([\App\Http\Controllers\ImportProductsController::class, 'importImageMapping']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('name', 'Image Mapping CSV File:' ) !!}
                                        {!! Form::file('image_mapping_csv', ['accept'=> '.xls, .xlsx, .csv', 'required' => 'required']); !!}
                                        <small class="text-muted">CSV should contain SKU and Image File Name columns</small>
                                    </div>
                                </div>
                                <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">Import Image Mapping</button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-sm-4">
                                <a href="{{ url('/import-products/export-images') }}" class="tw-dw-btn tw-dw-btn-info tw-text-white"><i class="fa fa-file-excel-o"></i> Export Products & Variations Images SKU  (CSV/Excel)</a>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-info">
                            <strong><i class="fa fa-info-circle"></i> Important:</strong> The template has <strong>38 columns</strong>. Stock management fields have been removed. Products will be created without opening stock.
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary instructions-box', 'title' => __('lang_v1.instructions')])
                <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                <table class="table table-striped">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>@lang('product.product_name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.name_ins')</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>2</td>
                        <td>@lang('product.brand') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.brand_ins') <br><small class="text-muted">(@lang('lang_v1.brand_ins2'))</small></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('product.unit') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.unit_ins')</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>4</td>
                        <td>@lang('product.category') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.category_ins') <br><small class="text-muted">(@lang('lang_v1.category_ins2'))</small></td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>5</td>
                        <td>@lang('product.sub_category') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.sub_category_ins') <br><small class="text-muted">({!! __('lang_v1.sub_category_ins2') !!})</small></td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>6</td>
                        <td>@lang('product.sku') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.sku_ins')</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>7</td>
                        <td>@lang('product.barcode_type') <small class="text-muted">(@lang('lang_v1.optional'), @lang('lang_v1.default'): C128)</small></td>
                        <td>@lang('lang_v1.barcode_type_ins') <br>
                            <strong>@lang('lang_v1.barcode_type_ins2'): C128, C39, EAN-13, EAN-8, UPC-A, UPC-E, ITF-14</strong>
                        </td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>8</td>
                        <td>@lang('product.applicable_tax') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Tax rate name (e.g., GST 18%, VAT 5%). Tax must exist in system before import. Leave empty if no tax applicable.</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>@lang('product.selling_price_tax_type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('product.selling_price_tax_type') <br>
                            <strong>@lang('lang_v1.available_options'): inclusive, exclusive</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>@lang('product.product_type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('product.product_type') <br>
                            <strong>@lang('lang_v1.available_options'): single, variable, modifier, combo</strong></td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>@lang('product.variation_name') <small class="text-muted">(@lang('lang_v1.variation_name_ins'))</small></td>
                        <td><strong>For variable products only.</strong> Enter variation attribute name (e.g., Size, Color, Style). Leave empty for single products.</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>@lang('product.variation_values') <small class="text-muted">(@lang('lang_v1.variation_values_ins'))</small></td>
                        <td><strong>For variable products only.</strong> Enter variation values separated by pipe (|). Example: Small|Medium|Large|XL. Must match count of SKUs, prices, and stock.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>13</td>
                        <td>@lang('lang_v1.variation_sku') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td><strong>For variable products only.</strong> Enter unique SKU for each variation separated by pipe (|). Example: TSHIRT-S|TSHIRT-M|TSHIRT-L. Leave empty for auto-generation.</td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td> @lang('lang_v1.purchase_price_inc_tax')<br><small class="text-muted">(@lang('lang_v1.purchase_price_inc_tax_ins1'))</small></td>
                        <td><strong>Purchase price including tax.</strong> Single value for single products. For variable products, use pipe (|) separator (e.g., 500|500|550|550). One of Inc/Exc tax is required.</td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td>@lang('lang_v1.purchase_price_exc_tax')  <br><small class="text-muted">(@lang('lang_v1.purchase_price_exc_tax_ins1'))</small></td>
                        <td><strong>Purchase price excluding tax.</strong> Single value for single products. For variable products, use pipe (|) separator (e.g., 446|446|491|491). One of Inc/Exc tax is required.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>16</td>
                        <td>@lang('lang_v1.profit_margin') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td><strong>Profit margin percentage.</strong> Used to calculate selling price if selling price is empty. Example: 50 for 50% profit. For variable products, use pipe (|) separator (e.g., 50|50|50|50).</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>17</td>
                        <td>@lang('lang_v1.selling_price') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td><strong>Final selling price.</strong> If empty, calculated from purchase price + profit margin. For variable products, use pipe (|) separator (e.g., 750|750|825|825).</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>18</td>
                        <td>@lang('lang_v1.enable_imei_or_sr_no') <small class="text-muted">(@lang('lang_v1.optional'), @lang('lang_v1.default'): 0)</small></td>
                        <td><strong>1 = @lang('messages.yes')<br>
                            0 = @lang('messages.no')</strong><br>
                        </td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>19</td>
                        <td>@lang('lang_v1.weight') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Product weight in your default unit (e.g., 0.500 for 500 grams or 0.5 kg). Used for shipping calculations.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>20</td>
                        <td>@lang('lang_v1.image') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td><strong>Two options:</strong><br>
                        1. Image filename (e.g., product.jpg) - File must exist in <code>public/uploads/img/</code><br>
                        2. Full image URL (e.g., https://example.com/image.jpg) - Will be downloaded automatically</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>21</td>
                        <td>@lang('lang_v1.product_description') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Detailed product description. Can include HTML for formatting. Displayed on product pages.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>22</td>
                        <td>@lang('lang_v1.product_custom_field1') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Custom field 1. Can be configured from Settings. Use for any additional product attribute (e.g., Material, Origin, Certification).</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>23</td>
                        <td>@lang('lang_v1.product_custom_field2') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Custom field 2. Can be configured from Settings. Use for any additional product attribute.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>24</td>
                        <td>@lang('lang_v1.product_custom_field3') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Custom field 3. Can be configured from Settings. Use for any additional product attribute.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>25</td>
                        <td>@lang('lang_v1.product_custom_field4') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Custom field 4. Can be configured from Settings. Use for any additional product attribute.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>26</td>
                        <td>@lang('lang_v1.not_for_selling') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td><strong>1 = @lang('messages.yes')<br>
                            0 = @lang('messages.no')</strong><br>
                        </td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>27</td>
                        <td>@lang('lang_v1.product_locations') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Business locations where this product is available. Enter location names separated by comma (e.g., Main Store,Warehouse,Branch 1). Locations must exist in system.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>28</td>
                        <td>Is Inactive <small class="text-muted">(Optional, Default: 0)</small></td>
                        <td><strong>Product active status.</strong><br>
                        <strong>1 = Inactive</strong> (Product hidden from sales, not available for purchase)<br>
                        <strong>0 = Active</strong> (Product visible and available for sale)</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>29</td>
                        <td>Warranty <small class="text-muted">(Optional)</small></td>
                        <td>Warranty policy for this product. Enter warranty ID or warranty name. Warranty must exist in system (Settings → Warranties). Example: "1 Year Warranty" or warranty ID like "5".</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>30</td>
                        <td>Secondary Unit <small class="text-muted">(Optional)</small></td>
                        <td>Alternative unit for selling this product. Example: Primary unit is "Box" (12 pieces), secondary unit is "Pieces". Enter unit name or short name. Unit must exist in system.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>31</td>
                        <td>Preparation Time <small class="text-muted">(Optional)</small></td>
                        <td>Time required to prepare/manufacture this product in minutes. Useful for service businesses, food items, or made-to-order products. Example: 30 for 30 minutes preparation time.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>32</td>
                        <td>ML <small class="text-muted">(Optional)</small></td>
                        <td>Volume in milliliters. For liquid products like beverages, oils, perfumes. Example: 1000 for 1 liter, 500 for 500ml. Used for product specifications and comparisons.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>33</td>
                        <td>CT <small class="text-muted">(Optional)</small></td>
                        <td>Count or quantity in package. Number of items included in one unit. Example: 12 for a dozen, 24 for a case. Used for product specifications.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>34</td>
                        <td>Product Visibility <small class="text-muted">(Optional, Default: public)</small></td>
                        <td><strong>Controls product visibility in catalog and storefront.</strong><br>
                        <strong>public</strong> - Visible to everyone, searchable, appears in catalog<br>
                        <strong>private</strong> - Hidden from public catalog, accessible via direct link only<br>
                        <strong>hidden</strong> - Completely hidden, not accessible in storefront</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>35</td>
                        <td>Max Sale Limit <small class="text-muted">(Optional)</small></td>
                        <td>Maximum quantity allowed per single sale/transaction. Used to prevent bulk orders or limit high-demand items. Example: 5 means customer can buy maximum 5 units per order. Leave empty for no limit.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>36</td>
                        <td>Enable Selling <small class="text-muted">(Optional, Default: 0)</small></td>
                        <td><strong>Enable/disable selling for this product.</strong><br>
                        <strong>1 = Yes</strong> (Product can be sold)<br>
                        <strong>0 = No</strong> (Product cannot be sold, inventory-only item)</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>37</td>
                        <td>Top Selling <small class="text-muted">(Optional, Default: 0)</small></td>
                        <td><strong>Mark product as featured/top selling.</strong><br>
                        <strong>1 = Yes</strong> (Show in featured products, homepage, promotions)<br>
                        <strong>0 = No</strong> (Regular product)<br>
                        Used to highlight best-selling or promotional products.</td>
                    </tr>
                    <tr class="import-instruction-optional">
                        <td>38</td>
                        <td>Slug <small class="text-muted">(Optional)</small></td>
                        <td>SEO-friendly URL slug for product page. Example: "samsung-galaxy-s24" for URL like website.com/product/samsung-galaxy-s24. Use lowercase letters, numbers, and hyphens only. Auto-generated if left empty.</td>
                    </tr>

                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection
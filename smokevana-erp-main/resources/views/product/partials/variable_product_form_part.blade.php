<style>
    /* Enhanced Variation Section Header */
    .variation-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: clamp(16px, 2vw, 20px);
        padding-bottom: clamp(10px, 1.2vw, 14px);
        border-bottom: 2px solid var(--amazon-orange, #ff9900);
    }
    
    .variation-section-header h4 {
        margin: 0;
        font-size: clamp(15px, 1.6vw, 17px);
        font-weight: 600;
        color: var(--amazon-text, #111);
    }
    
    .variation-section-header .btn-add-variation {
        background: var(--amazon-orange, #ff9900);
        border: 1px solid var(--amazon-orange, #ff9900);
        color: #fff;
        padding: clamp(6px, 0.8vw, 8px) clamp(12px, 1.5vw, 16px);
        border-radius: 4px;
        font-weight: 500;
        font-size: clamp(12px, 1.3vw, 14px);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .variation-section-header .btn-add-variation:hover {
        background: var(--amazon-orange-dark, #e88900);
        border-color: var(--amazon-orange-dark, #e88900);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Enhanced Product Variation Table */
    #product_variation_form_part {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        border: 1px solid var(--amazon-border, #ddd);
        border-radius: 6px;
        overflow: hidden;
        margin-top: clamp(16px, 2vw, 20px);
    }
    
    #product_variation_form_part thead th {
        background: linear-gradient(to bottom, var(--amazon-gray-light, #fafafa), var(--amazon-gray, #f3f3f3));
        border: 1px solid var(--amazon-border, #ddd);
        border-bottom: 2px solid var(--amazon-border, #ddd);
        padding: clamp(10px, 1.2vw, 14px);
        font-weight: 600;
        color: var(--amazon-text, #111);
        text-align: left;
        font-size: clamp(12px, 1.3vw, 13px);
        white-space: nowrap;
    }
    
    #product_variation_form_part tbody td {
        border: 1px solid var(--amazon-border-light, #e5e5e5);
        padding: clamp(8px, 1vw, 12px);
        vertical-align: middle;
        background: #fff;
    }
    
    #product_variation_form_part tbody tr:hover {
        background: var(--amazon-gray-light, #fafafa);
    }
    
    #product_variation_form_part .form-control {
        border: 1px solid var(--amazon-border, #ddd);
        border-radius: 4px;
        padding: clamp(6px, 0.8vw, 8px) clamp(8px, 1vw, 10px);
        font-size: clamp(12px, 1.2vw, 13px);
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    #product_variation_form_part .form-control:focus {
        border-color: var(--amazon-orange, #ff9900);
        outline: none;
        box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.1);
    }
    
    /* Enhanced Variation Value Table */
    .variation_value_table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        font-size: clamp(11px, 1.2vw, 12px);
    }
    
    .variation_value_table thead th {
        background: var(--amazon-gray-light, #f8f9fa);
        border: 1px solid var(--amazon-border, #ddd);
        padding: clamp(8px, 1vw, 10px) clamp(6px, 0.8vw, 8px);
        font-weight: 600;
        color: var(--amazon-text, #111);
        text-align: left;
        font-size: clamp(11px, 1.2vw, 12px);
        white-space: nowrap;
    }
    
    .variation_value_table tbody td {
        border: 1px solid var(--amazon-border-light, #e5e5e5);
        padding: clamp(6px, 0.8vw, 8px);
        vertical-align: middle;
        background: #fff;
    }
    
    .variation_value_table tbody tr {
        border-bottom: 1px solid var(--amazon-border-light, #eee);
    }
    
    .variation_value_table tbody tr:hover {
        background: #f9f9f9;
    }
    
    .variation_value_table .form-control {
        border: 1px solid var(--amazon-border, #ddd);
        border-radius: 4px;
        padding: clamp(5px, 0.6vw, 6px) clamp(6px, 0.8vw, 8px);
        font-size: clamp(11px, 1.2vw, 12px);
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    .variation_value_table .form-control:focus {
        border-color: var(--amazon-orange, #ff9900);
        outline: none;
        box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.1);
    }
    
    /* Enhanced Variation Action Buttons */
    .btn-variation-action {
        padding: clamp(4px, 0.6vw, 6px) clamp(8px, 1vw, 10px);
        font-size: clamp(11px, 1.2vw, 12px);
        border-radius: 4px;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: clamp(28px, 3.5vw, 32px);
        min-height: clamp(28px, 3.5vw, 32px);
    }
    
    .btn-variation-add {
        background: var(--amazon-success, #28a745);
        border-color: var(--amazon-success, #28a745);
        color: #fff;
    }
    
    .btn-variation-add:hover {
        background: #218838;
        border-color: #1e7e34;
        color: #fff;
        transform: scale(1.05);
    }
    
    .btn-variation-remove {
        background: var(--amazon-danger, #dc3545);
        border-color: var(--amazon-danger, #dc3545);
        color: #fff;
    }
    
    .btn-variation-remove:hover {
        background: #c82333;
        border-color: #bd2130;
        color: #fff;
        transform: scale(1.05);
    }
    
    .btn-variation-delete {
        background: var(--amazon-danger, #dc3545);
        border-color: var(--amazon-danger, #dc3545);
        color: #fff;
        padding: clamp(6px, 0.8vw, 8px) clamp(10px, 1.2vw, 12px);
        font-size: clamp(12px, 1.3vw, 13px);
    }
    
    .btn-variation-delete:hover {
        background: #c82333;
        border-color: #bd2130;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .variation-section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: clamp(10px, 1.2vw, 12px);
        }
        
        .variation-section-header .btn-add-variation {
            width: 100%;
        }
        
        #product_variation_form_part {
            font-size: clamp(11px, 1.1vw, 12px);
        }
        
        .variation_value_table {
            font-size: clamp(10px, 1.1vw, 11px);
        }
    }
</style>

<div class="row">
    <div class="option-div-group hide">
        <div class="col-md-12">
            <label class="form-check-label" for="sku">@lang('product.variation_sku_format') </label>@show_tooltip(__('product.variation_sku_format_help_text'))
        </div>
            <div class="col-sm-4">
                <input class="form-check-input" type="radio" name="sku_type" checked id="with_out_variation" value="with_out_variation">
                <label class="form-check-label" for="with_out_variation">@lang('product.sku_number')</label>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-check-input" type="radio" name="sku_type" id="with_variation"
                        value="with_variation">
                    <label class="form-check-label" for="with_variation">@lang('product.sku_variation_number')</label>
                </div>
            </div>
    </div>
</div>

<div class="col-sm-12">
    <div class="variation-section-header">
        <h4>@lang('product.add_variation'):*</h4>
        <button type="button" class="btn-add-variation" id="add_variation" data-action="add">
            <i class="fa fa-plus"></i> Add Variation
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered add-product-price-table table-condensed variable-product-table" id="product_variation_form_part">
            <thead>
                <tr>
                    <th style="width: 60px;">Action</th>
                    <th style="width: 200px;">@lang('lang_v1.variation')</th>
                    <th>@lang('product.variation_values')</th>
                </tr>
            </thead>
            <tbody>
                @if ($action == 'add')
                    @include('product.partials.product_variation_row', ['row_index' => 0])
                @else
                    @forelse ($product_variations as $product_variation)
                        @include('product.partials.edit_product_variation_row', [
                            'row_index' => $action == 'edit' ? $product_variation->id : $loop->index,
                            'product_variation' => $product_variation,
                            'action' => $action,
                            'allowed_group_prices' => $allowed_group_prices ?? [],
                        ])
                    @empty
                        @include('product.partials.product_variation_row', ['row_index' => 0])
                    @endforelse

                @endif

            </tbody>
        </table>
    </div>
</div>

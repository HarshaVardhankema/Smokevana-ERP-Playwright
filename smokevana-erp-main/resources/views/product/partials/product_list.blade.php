@php 
    $colspan = 16;
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<table style="min-width: 100%; table-layout: fixed;" class="table table-bordered table-striped ajax_view hide-footer" id="product_table">
    <thead>
        <tr>
            <th class="col-checkbox"><input type="checkbox" id="select-all-row" data-table-id="product_table"></th>
            <th class="col-action">@lang('messages.action')</th>
            <th class="col-image">{{ __('lang_v1.product_image') }}</th>
            <th class="col-product">@lang('sale.product')</th>
            <th class="col-sku">@lang('product.sku')</th>
            <th class="col-date">@lang('product.created_at')</th>
            <th class="col-location">@lang('purchase.business_location') @show_tooltip(__('lang_v1.product_business_location_tooltip'))</th>
            @can('view_purchase_price')
                @php 
                    $colspan++;
                @endphp
                <th class="col-purchase-price">Unit Purchase Price</th>
            @endcan
            @can('access_default_selling_price')
                @php 
                    $colspan++;
                @endphp
                <th class="col-selling-price">@lang('lang_v1.selling_price')</th>
            @endcan
            <th class="col-stock">@lang('report.current_stock')</th>
            <th class="col-type">@lang('product.product_type')</th>
            <th class="col-fulfillment">Fulfillment</th>
            {{-- custom --}}
            <th class="col-tier-price">@lang('product.silver_price')</th>
            <th class="col-tier-price">@lang('product.gold_prices')</th>
            <th class="col-tier-price">@lang('product.platinum_price')</th>
            {{-- custom end --}}
            <th class="col-category">@lang('product.category')</th>
            <th class="col-brand">@lang('product.brand')</th>
            <th class="col-tax">@lang('product.tax')</th>
            <th class="col-hidden">Alert Quantity</th>
            {{-- <th >@lang('messages.action')</th> --}}
            {{-- <th id="cf_1">{{ $custom_labels['product']['custom_field_1'] ?? '' }}</th>
            <th id="cf_2">{{ $custom_labels['product']['custom_field_2'] ?? '' }}</th>
            <th id="cf_3">{{ $custom_labels['product']['custom_field_3'] ?? '' }}</th>
            <th id="cf_4">{{ $custom_labels['product']['custom_field_4'] ?? '' }}</th>
            <th id="cf_5">{{ $custom_labels['product']['custom_field_5'] ?? '' }}</th>
            <th id="cf_6">{{ $custom_labels['product']['custom_field_6'] ?? '' }}</th>
            <th id="cf_7">{{ $custom_labels['product']['custom_field_7'] ?? '' }}</th> --}}
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="{{$colspan}}">
            <div style="display: flex; width: 100%;">
                @can('product.delete')
                    {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massDestroy']), 'method' => 'post', 'id' => 'mass_delete_form' ]) !!}
                    {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
                    {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error', 'id' => 'delete-selected')) !!}
                    {!! Form::close() !!}
                @endcan

                
                    @can('product.update')
                    
                        @if(config('constants.enable_product_bulk_edit'))
                            &nbsp;
                            {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'bulkEdit']), 'method' => 'post', 'id' => 'bulk_edit_form' ]) !!}
                            {!! Form::hidden('selected_products', null, ['id' => 'selected_products_for_edit']); !!}
                            <button type="submit" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary" id="edit-selected"> <i class="fa fa-edit"></i>{{__('lang_v1.bulk_edit')}}</button>
                            {!! Form::close() !!}
                        @endif
                        &nbsp;
                        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-accent  update_product_location" data-type="add">@lang('lang_v1.add_to_location')</button>
                        &nbsp;
                        <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-neutral update_product_location" data-type="remove">@lang('lang_v1.remove_from_location')</button>
                    @endcan
                
                &nbsp;
                {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massDeactivate']), 'method' => 'post', 'id' => 'mass_deactivate_form' ]) !!}
                {!! Form::hidden('selected_products', null, ['id' => 'selected_products']); !!}
                {!! Form::submit("Deactivate", array('class' => 'tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-warning', 'id' => 'deactivate-selected')) !!}
                {!! Form::close() !!} @show_tooltip(__('lang_v1.deactive_product_tooltip'))

                {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massDiscontinue']), 'method' => 'post', 'id' => 'mass_discontinue_form' ]) !!}
                {!! Form::hidden('selected_products', null, ['id' => 'selected_products']); !!}
                {!! Form::submit("Discontinue", array('class' => 'tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-warning', 'id' => 'discontinue-selected')) !!}
                {!! Form::close() !!} @show_tooltip(__('lang_v1.discontinue_product_tooltip'))

                
                {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'massActivate']), 'method' => 'post', 'id' => 'mass_activate_form' ]) !!}
                {!! Form::hidden('selected_products', null, ['id' => 'selected_products']); !!}
                {!! Form::submit("Activates", array('class' => 'tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-success', 'id' => 'activate-selected')) !!}
                {!! Form::close() !!}
                &nbsp;
                @if($is_woocommerce)
                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-warning toggle_woocomerce_sync">
                        @lang('lang_v1.woocommerce_sync')
                    </button>
                @endif
                </div>
            </td>
        </tr>
    </tfoot>
</table>

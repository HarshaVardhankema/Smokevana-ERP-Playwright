<!-- @php
  $custom_labels = json_decode(session('business.custom_labels'), true);
  $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
  $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
  $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
  $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
@endphp
<table style="min-width: max-content;"  class="table   table-bordered table-striped ajax_view hide-footer" id="stock_report_table">
    <thead >
        <tr>
            <th >@lang('messages.action')</th>
            <th >SKU</th>
            <th style="min-width:400px">@lang('business.product')</th>
            <th >@lang('lang_v1.variation')</th>
            <th >@lang('product.category')</th>
            <th >@lang('sale.location')</th>
            <th >@lang('purchase.unit_selling_price')</th>
            <th >Stock Hand</th>
            <th >Stock Available</th>
            @can('view_product_stock_value')
            <th  class="stock_price">@lang('lang_v1.total_stock_price') <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="By perchase price"></i></th>
            <th >@lang('lang_v1.total_stock_price') <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="By sale price"></i></th>
            <th >@lang('lang_v1.potential_profit')</th>
            @endcan
            <th >@lang('report.total_unit_sold')</th>
            <th >@lang('lang_v1.total_unit_transfered')</th>
            <th >@lang('lang_v1.total_unit_adjusted')</th>
            <th >{{$product_custom_field1}}</th>
            <th >{{$product_custom_field2}}</th>
            <th >{{$product_custom_field3}}</th>
            <th >{{$product_custom_field4}}</th>
            @if($show_manufacturing_data)
                <th class="current_stock_mfg">@lang('manufacturing::lang.current_stock_mfg') @show_tooltip(__('manufacturing::lang.mfg_stock_tooltip'))</th>
            @endif
        </tr>
    </thead>
    <tfoot>
        <tr class="bg-gray font-17 text-left footer-total">
            <td colspan="7"><strong>@lang('sale.total'):</strong></td>
            <td class="footer_total_stock"></td>
            <td></td>
            @can('view_product_stock_value')
            <td class="footer_total_stock_price"></td>
            <td class="footer_stock_value_by_sale_price"></td>
            <td class="footer_potential_profit"></td>
            @endcan
            <td class="footer_total_sold"></td>
            <td class="footer_total_transfered"></td>
            <td class="footer_total_adjusted"></td>
            <td colspan="4"></td>
            @if($show_manufacturing_data)
                <td class="footer_total_mfg_stock"></td>
            @endif
        </tr>
    </tfoot>
</table> -->



@php
  $custom_labels = json_decode(session('business.custom_labels'), true);
  $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : __('lang_v1.product_custom_field1');
  $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : __('lang_v1.product_custom_field2');
  $product_custom_field3 = !empty($custom_labels['product']['custom_field_3']) ? $custom_labels['product']['custom_field_3'] : __('lang_v1.product_custom_field3');
  $product_custom_field4 = !empty($custom_labels['product']['custom_field_4']) ? $custom_labels['product']['custom_field_4'] : __('lang_v1.product_custom_field4');
@endphp
<table style="min-width: max-content; border-collapse: collapse !important; width: 100% !important; table-layout: auto !important;"  class="table table-bordered table-striped ajax_view hide-footer" id="stock_report_table">
    <thead style="background: #f8fafc !important;">
        <tr style="display: table-row !important;">
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; white-space: nowrap !important; min-width: 80px !important;">@lang('messages.action')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; white-space: nowrap !important; min-width: 100px !important;">SKU</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 300px !important;">@lang('business.product')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 120px !important;">@lang('lang_v1.variation')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 110px !important;">@lang('product.category')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">@lang('sale.location')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: right !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 110px !important;">@lang('purchase.unit_selling_price')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: center !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">Stock Hand</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: center !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 110px !important;">Stock Available</th>
            @can('view_product_stock_value')
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: right !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 130px !important;" class="stock_price">@lang('lang_v1.total_stock_price') <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="By perchase price"></i></th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: right !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 130px !important;">@lang('lang_v1.total_stock_price') <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="By sale price"></i></th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: right !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 110px !important;">@lang('lang_v1.potential_profit')</th>
            @endcan
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: center !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">@lang('report.total_unit_sold')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: center !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 110px !important;">@lang('lang_v1.total_unit_transfered')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: center !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 110px !important;">@lang('lang_v1.total_unit_adjusted')</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">{{$product_custom_field1}}</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">{{$product_custom_field2}}</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">{{$product_custom_field3}}</th>
            <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: left !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 100px !important;">{{$product_custom_field4}}</th>
            @if($show_manufacturing_data)
                <th style="background: #f8fafc !important; color: #0F1111 !important; font-weight: 600 !important; padding: 12px 16px !important; text-align: center !important; vertical-align: middle !important; border: 1px solid #e2e8f0 !important; min-width: 120px !important;" class="current_stock_mfg">@lang('manufacturing::lang.current_stock_mfg') @show_tooltip(__('manufacturing::lang.mfg_stock_tooltip'))</th>
            @endif
        </tr>
    </thead>
    <tfoot>
        <tr class="bg-gray font-17 text-left footer-total" style="display: table-row !important;">
            <td colspan="7" style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: left !important; vertical-align: middle !important;"><strong>@lang('sale.total'):</strong></td>
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: center !important; vertical-align: middle !important;" class="footer_total_stock"></td>
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important;"></td>
            @can('view_product_stock_value')
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: right !important; vertical-align: middle !important;" class="footer_total_stock_price"></td>
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: right !important; vertical-align: middle !important;" class="footer_stock_value_by_sale_price"></td>
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: right !important; vertical-align: middle !important;" class="footer_potential_profit"></td>
            @endcan
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: center !important; vertical-align: middle !important;" class="footer_total_sold"></td>
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: center !important; vertical-align: middle !important;" class="footer_total_transfered"></td>
            <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: center !important; vertical-align: middle !important;" class="footer_total_adjusted"></td>
            <td colspan="4" style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important;"></td>
            @if($show_manufacturing_data)
                <td style="padding: 12px 16px !important; border: 1px solid #e2e8f0 !important; text-align: center !important; vertical-align: middle !important;" class="footer_total_mfg_stock"></td>
            @endif
        </tr>
    </tfoot>
</table>
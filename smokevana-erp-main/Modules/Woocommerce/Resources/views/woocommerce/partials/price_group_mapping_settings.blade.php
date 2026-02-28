<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">@lang('woocommerce::lang.price_group_mapping_settings')</h3>
                    <p class="text-muted">@lang('woocommerce::lang.price_group_mapping_help')</p>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>@lang('woocommerce::lang.woocommerce_price_type')</th>
                                        <th>@lang('woocommerce::lang.erp_price_group')</th>
                                        <th>@lang('woocommerce::lang.price_group_id')</th>
                                        <th>@lang('woocommerce::lang.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $price_mappings = !empty($default_settings['price_group_mappings']) ? $default_settings['price_group_mappings'] : [];
                                        $woo_price_types = [
                                            'silver' => 'Silver Price',
                                            'gold' => 'Gold Price',
                                            'lowest' => 'Lowest Price',
                                            'platinum' => 'Platinum Price',
                                            'diamond' => 'Diamond Price'
                                        ];
                                    @endphp
                                    
                                    @foreach($woo_price_types as $price_type => $price_label)
                                        @php
                                            $current_mapping = !empty($price_mappings[$price_type]) ? $price_mappings[$price_type] : null;
                                            $is_enabled = !empty($current_mapping['enabled']) ? $current_mapping['enabled'] : false;
                                            $selected_group_id = !empty($current_mapping['erp_price_group_id']) ? $current_mapping['erp_price_group_id'] : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $price_label }}</strong>
                                                <br>
                                                <small class="text-muted">({{ $price_type }})</small>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    {!! Form::select(
                                                        "price_group_mappings[{$price_type}][erp_price_group_id]",
                                                        $price_groups,
                                                        $selected_group_id,
                                                        [
                                                            'class' => 'form-control select2',
                                                            'style' => 'width: 100%;',
                                                            'placeholder' => __('messages.please_select'),
                                                            'data-price-type' => $price_type
                                                        ]
                                                    ) !!}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="price-group-id-display" data-price-type="{{ $price_type }}">
                                                    @if($selected_group_id)
                                                        <span class="label label-info">{{ $selected_group_id }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="checkbox">
                                                        <label>
                                                            {!! Form::checkbox(
                                                                "price_group_mappings[{$price_type}][enabled]",
                                                                1,
                                                                $is_enabled,
                                                                ['class' => 'input-icheck price-mapping-enabled']
                                                            ) !!} 
                                                            <span class="enabled-status" data-price-type="{{ $price_type }}">
                                                                @if($is_enabled)
                                                                    <span class="label label-success">@lang('lang_v1.enabled')</span>
                                                                @else
                                                                    <span class="label label-default">@lang('lang_v1.disabled')</span>
                                                                @endif
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-info">
                                <h4><i class="fa fa-info-circle"></i> @lang('woocommerce::lang.price_mapping_info')</h4>
                                <ul>
                                    <li>@lang('woocommerce::lang.price_mapping_info_1')</li>
                                    <li>@lang('woocommerce::lang.price_mapping_info_2')</li>
                                    <li>@lang('woocommerce::lang.price_mapping_info_3')</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('javascript')
<script>
$(document).ready(function() {
    // Update price group ID display when selection changes
    $('select[name*="[erp_price_group_id]"]').on('change', function() {
        var priceType = $(this).data('price-type');
        var selectedOption = $(this).find('option:selected');
        var groupId = selectedOption.val();
        
        var displayElement = $('.price-group-id-display[data-price-type="' + priceType + '"]');
        if (groupId) {
            displayElement.html('<span class="label label-info">' + groupId + '</span>');
        } else {
            displayElement.html('<span class="text-muted">-</span>');
        }
    });
    
    // Update enabled status when checkbox changes
    $('.price-mapping-enabled').on('change', function() {
        var priceType = $(this).closest('label').find('.enabled-status').data('price-type');
        var isEnabled = $(this).is(':checked');
        var statusElement = $('.enabled-status[data-price-type="' + priceType + '"]');
        
        if (isEnabled) {
            statusElement.html('<span class="label label-success">@lang("lang_v1.enabled")</span>');
        } else {
            statusElement.html('<span class="label label-default">@lang("lang_v1.disabled")</span>');
        }
    });
});
</script>
@endpush 
<!-- referal program settings -->
<div class="pos-tab-content referral-program-settings" id="referal_program_tab">
    <h2 class="referral-section-title">@lang('business.referal_program')</h2>
    
    <div class="referral-main-card">
        {{-- Card 1: General Settings --}}
        <div class="referral-sub-card">
            <h3 class="referral-sub-card__title">General Settings</h3>
            <div class="referral-checkbox-group">
                <label class="referral-checkbox-wrapper">
                    {!! Form::checkbox('enable_referal_program', 1, $business->enable_referal_program ?? false, ['id' => 'enable_referal_program', 'class' => 'referral-checkbox-input']); !!}
                    <span class="referral-checkbox-label">@lang('business.enable_referal_program')</span>
                </label>
            </div>
        </div>

        <div id="referal_program_fields" class="{{ ($business->enable_referal_program ?? false) ? '' : 'hide' }}">
            {{-- Card 2: Discount Configuration --}}
            <div class="referral-sub-card">
                <h3 class="referral-sub-card__title">Discount Configuration</h3>
                <div class="referral-form-group">
                    {!! Form::label('referal_program_custom_discount_id', __('business.referal_program_custom_discount') . ':', ['class' => 'referral-label']) !!}
                    <select name="referal_program_custom_discount_id" id="referal_program_custom_discount_id" class="form-control select2 referral-input" style="width: 100%;">
                        <option value="">@lang('messages.please_select')</option>
                        @foreach($custom_discounts as $discount)
                            <option value="{{ $discount->id }}" 
                                {{ $business->referal_program_custom_discount_id == $discount->id ? 'selected' : '' }}>
                                {{ $discount->couponName }} 
                                @if($discount->couponCode)
                                    ({{ $discount->couponCode }})
                                @endif
                                - {{ ucfirst($discount->discountType) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="referral-helper-text">@lang('business.select_custom_discount_help')</p>
                </div>
            </div>

            {{-- Card 3: Availability Settings --}}
            <div class="referral-sub-card">
                <h3 class="referral-sub-card__title">Availability Settings</h3>
                <div class="referral-checkbox-group referral-checkbox-group--inline">
                    <label class="referral-checkbox-wrapper">
                        {!! Form::checkbox('referal_sent_to_both_sides', 1, $business->referal_sent_to_both_sides ?? false, ['id' => 'referal_sent_to_both_sides', 'class' => 'referral-checkbox-input']); !!}
                        <span class="referral-checkbox-label">@lang('business.referal_sent_to_both_sides')</span>
                    </label>
                </div>
                <div class="referral-availability-row">
                    <div class="referral-checkbox-group">
                        <label class="referral-checkbox-wrapper">
                            {!! Form::checkbox('referal_available_for_b2b', 1, $business->referal_available_for_b2b ?? false, ['id' => 'referal_available_for_b2b', 'class' => 'referral-checkbox-input']); !!}
                            <span class="referral-checkbox-label">@lang('business.referal_available_for_b2b')</span>
                        </label>
                    </div>
                    <div class="referral-checkbox-group">
                        <label class="referral-checkbox-wrapper">
                            {!! Form::checkbox('referal_available_for_b2c', 1, $business->referal_available_for_b2c ?? false, ['id' => 'referal_available_for_b2c', 'class' => 'referral-checkbox-input']); !!}
                            <span class="referral-checkbox-label">@lang('business.referal_available_for_b2c')</span>
                        </label>
                    </div>
                </div>
                <div class="referral-form-group" id="referal_brand_list_container" style="{{ ($business->referal_available_for_b2c ?? false) ? '' : 'display: none;' }}">
                    {!! Form::label('referal_brand_list', __('business.referal_brand_list') . ':', ['class' => 'referral-label']) !!}
                    <select name="referal_brand_list[]" id="referal_brand_list" class="form-control select2 referral-input" multiple="multiple" style="width: 100%;">
                        @foreach($b2c_brands ?? [] as $brand_id => $brand_name)
                            <option value="{{ $brand_id }}" 
                                {{ in_array((string)$brand_id, array_map('strval', $selected_referal_brands ?? [])) ? 'selected' : '' }}>
                                {{ $brand_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="referral-helper-text">@lang('business.referal_brand_list_help')</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Make checkbox wrapper fully clickable (works with or without iCheck)
    $(document).on('click', '.referral-checkbox-wrapper', function(e) {
        // Don't trigger if clicking directly on the checkbox or its iCheck wrapper
        if ($(e.target).is('.referral-checkbox-input') || $(e.target).closest('.iCheck-helper').length) {
            return;
        }
        var $checkbox = $(this).find('.referral-checkbox-input');
        if ($checkbox.length) {
            // Check if iCheck is initialized
            if (typeof $.fn.iCheck !== 'undefined' && $checkbox.data('iCheck')) {
                $checkbox.iCheck('toggle');
            } else {
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
            }
        }
    });
    
    // Update custom checkbox visual state when iCheck changes
    if (typeof $.fn.iCheck !== 'undefined') {
        $(document).on('ifChanged', '.referral-checkbox-input', function() {
            // Visual state is handled by CSS :checked pseudo-class
            // This ensures compatibility with iCheck
        });
    }
});
</script>

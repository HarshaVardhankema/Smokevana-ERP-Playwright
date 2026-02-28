<div class="pos-tab-content">
	@php $enabled_modules = $enabled_modules ?? ($business->enabled_modules ?? []); @endphp

    {{-- Core Modules Summary Card --}}
    <div class="modern-settings-card tw-mb-6">
        <div class="modern-settings-card__title">
            <i class="fa fa-star"></i> Core Modules
        </div>
        <div class="settings-cards-grid">
            <div class="modern-switch-container">
                <span class="modern-switch-label">Enable Inventory Module</span>
                <label class="modern-switch">
                    <input type="checkbox" name="enabled_modules[]" value="purchases" @if(in_array('purchases', $enabled_modules)) checked @endif>
                    <span class="modern-slider"></span>
                </label>
            </div>
            <div class="modern-switch-container">
                <span class="modern-switch-label">Enable POS Module</span>
                <label class="modern-switch">
                    <input type="checkbox" name="enabled_modules[]" value="pos_sale" @if(in_array('pos_sale', $enabled_modules)) checked @endif>
                    <span class="modern-slider"></span>
                </label>
            </div>
            <div class="modern-switch-container">
                <span class="modern-switch-label">Enable Accounting Module</span>
                <label class="modern-switch">
                    <input type="checkbox" name="enabled_modules[]" value="accounting" @if(in_array('accounting', $enabled_modules)) checked @endif>
                    <span class="modern-slider"></span>
                </label>
            </div>
            <div class="modern-switch-container">
                <span class="modern-switch-label">Enable CRM Module</span>
                <label class="modern-switch">
                    <input type="checkbox" name="enabled_modules[]" value="crm" @if(in_array('crm', $enabled_modules)) checked @endif>
                    <span class="modern-slider"></span>
                </label>
            </div>
        </div>
    </div>

	@if(!empty($modules))
		<div class="modern-settings-card">
			<div class="modern-settings-card__title">
				<i class="fa fa-th-large"></i> @lang('lang_v1.modules')
			</div>
			<div class="settings-cards-grid">
				@foreach($modules as $k => $v)
					@php $isChecked = in_array($k, $enabled_modules); @endphp
					<div class="modern-switch-container">
						<span class="modern-switch-label">
							{{$v['name']}}
							@if(!empty($v['tooltip'])) 
								@show_tooltip($v['tooltip']) 
							@endif
						</span>
						<label class="modern-switch">
							{!! Form::checkbox('enabled_modules[]', $k, $isChecked, ['class' => 'module-checkbox']); !!}
							<span class="modern-slider"></span>
						</label>
					</div>
				@endforeach
			</div>
		</div>
	@endif
</div>

<script>
$(document).ready(function() {
    // Force remove iCheck from these specific checkboxes if it was applied globally
    if (typeof $.fn.iCheck !== 'undefined') {
        $('.module-checkbox').iCheck('destroy');
    }
});
</script>
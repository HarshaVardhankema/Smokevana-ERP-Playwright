<strong><i class="fa fa-info margin-r-5"></i> @lang('contact.tax_no')</strong>
<p class="text-muted">
    {{ $contact->tax_number }}
</p>
@if($contact->pay_term_type)
    <strong><i class="fa fa-calendar margin-r-5"></i> @lang('contact.pay_term_period')</strong>
    <p class="text-muted">
        {{ __('lang_v1.' . $contact->pay_term_type) }}
    </p>
@endif
@if($contact->pay_term_number)
    <strong><i class="fas fa fa-handshake margin-r-5"></i> @lang('contact.pay_term')</strong>
    <p class="text-muted">
        {{ $contact->pay_term_number }}
    </p>
@endif
@if($contact->type == 'customer')
    <strong><i class="fa fa-check-circle margin-r-5"></i> Tax Exemption Status</strong>
    <p class="text-muted">
        @if($contact->is_tax_exempt)
            <span class="label label-success">
                <i class="fa fa-check"></i> Tax Exempt
            </span>
            <small class="text-muted" style="display: block; margin-top: 5px;">
                This customer is exempt from taxes on all orders
            </small>
        @else
            <span class="label label-default">
                <i class="fa fa-times"></i> Not Tax Exempt
            </span>
        @endif
    </p>
@endif
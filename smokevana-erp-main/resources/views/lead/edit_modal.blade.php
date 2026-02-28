<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\LeadController::class, 'update'], [$lead->id]),
            'method' => 'PUT',
            'id' => 'lead_edit_form',
        ]) !!}

        <div class="modal-header">
            <h4 class="modal-title">@lang('lang_v1.edit_lead')</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="submit"
                    class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">@lang('messages.update')</button>
                <button type="button" class="tw-dw-btn tw-dw-btn-danger tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>@lang('messages.close')</button>
            </div>
        </div>
        <div class="modal-body" style="max-height: 85vh; overflow-y: auto;">
            <!-- Basic Information -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Basic Information</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('reference_no', 'Reference No:') !!}
                        {!! Form::text('reference_no', $lead->reference_no, ['class' => 'form-control', 'readonly' => true]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status', 'Status:*') !!}
                        {!! Form::select('status', ['pending' => 'Pending', 'visited' => 'Visited'], $lead->status, [
                            'class' => 'form-control',
                            'required',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('lead_status', 'Lead Status') !!}
                        {!! Form::select(
                            'lead_status',
                            [
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'follow_up' => 'Follow Up',
                                'qualified' => 'Qualified',
                                'unqualified' => 'Unqualified',
                                'converted' => 'Converted',
                                'lost' => 'Lost',
                            ],
                            $lead->lead_status ?? 'new',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('store_name', __('lang_v1.store_name') . ':*') !!}
                        {!! Form::text('store_name', $lead->store_name, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.store_name'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('company_name', 'Company Name') !!}
                        {!! Form::text('company_name', $lead->company_name, [
                            'class' => 'form-control',
                            'placeholder' => 'Company Name',
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Contact Information</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contact_name', 'Contact Name') !!}
                        {!! Form::text('contact_name', $lead->contact_name, [
                            'class' => 'form-control',
                            'placeholder' => 'Contact Person Name',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contact_email', 'Contact Email') !!}
                        {!! Form::email('contact_email', $lead->contact_email, [
                            'class' => 'form-control',
                            'placeholder' => 'contact@example.com',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contact_phone', 'Contact Phone') !!}
                        {!! Form::text('contact_phone', $lead->contact_phone, [
                            'class' => 'form-control',
                            'placeholder' => '+1234567890',
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('website', 'Website') !!}
                        {!! Form::url('website', $lead->website, ['class' => 'form-control', 'placeholder' => 'https://example.com']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('industry', 'Industry') !!}
                        {!! Form::text('industry', $lead->industry, [
                            'class' => 'form-control',
                            'placeholder' => 'e.g., Retail, Manufacturing',
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Address Information</h5>
                </div>
            </div>

            <x-address-autocomplete addressInput="address_line_1" cityInput="city" stateInput="state"
                stateFormat="short_name" zipInput="zip_code" countryInput="country" countryFormat="short_name" 
                latInput="latitude" lngInput="longitude" />

            <!-- Hidden fields for coordinates -->
            {!! Form::hidden('latitude', $lead->latitude ?? null, ['id' => 'latitude']) !!}
            {!! Form::hidden('longitude', $lead->longitude ?? null, ['id' => 'longitude']) !!}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_1', __('lang_v1.address_line_1') . ':*') !!}
                        {!! Form::text('address_line_1', $lead->address_line_1, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.address_line_1'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_2', __('lang_v1.address_line_2')) !!}
                        {!! Form::text('address_line_2', $lead->address_line_2, [
                            'class' => 'form-control',
                            'placeholder' => __('lang_v1.address_line_2'),
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('city', __('lang_v1.city') . ':*') !!}
                        {!! Form::text('city', $lead->city, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.city'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('state', __('lang_v1.state') . ':*') !!}
                        {!! Form::text('state', $lead->state, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.state'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('zip_code', __('lang_v1.zip_code') . ':*') !!}
                        {!! Form::text('zip_code', $lead->zip_code, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.zip_code'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('country', __('lang_v1.country') . ':*') !!}
                        {!! Form::text('country', $lead->country, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.country'),
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Lead Management -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Lead Management</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('lead_source', 'Lead Source') !!}
                        {!! Form::select(
                            'lead_source',
                            [
                                'admin_panel' => 'Admin Panel',
                                'mobile_app' => 'Mobile App',
                                'website' => 'Website',
                                'referral' => 'Referral',
                                'cold_call' => 'Cold Call',
                                'email_campaign' => 'Email Campaign',
                                'social_media' => 'Social Media',
                                'trade_show' => 'Trade Show',
                                'other' => 'Other',
                            ],
                            $lead->lead_source ?? 'admin_panel',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('priority', 'Priority') !!}
                        {!! Form::select(
                            'priority',
                            [
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ],
                            $lead->priority ?? 'medium',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('funnel_stage', 'Funnel Stage') !!}
                        {!! Form::select(
                            'funnel_stage',
                            [
                                'initial_contact' => 'Initial Contact',
                                'qualification' => 'Qualification',
                                'proposal' => 'Proposal',
                                'negotiation' => 'Negotiation',
                                'closed_won' => 'Closed Won',
                                'closed_lost' => 'Closed Lost',
                                'nurturing' => 'Nurturing',
                            ],
                            $lead->funnel_stage ?? 'initial_contact',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('assigned_to', 'Assigned To') !!}
                        {!! Form::select('assigned_to', $users->pluck('name', 'id'), $lead->assigned_to, [
                            'class' => 'form-control',
                            'placeholder' => 'Select User',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('sales_rep_id', 'Sales Rep') !!}
                        {!! Form::select('sales_rep_id', $users->pluck('name', 'id'), $lead->sales_rep_id, [
                            'class' => 'form-control',
                            'placeholder' => 'Select Sales Rep',
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Value and Follow-up -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Value & Follow-up</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('estimated_value', 'Estimated Value') !!}
                        {!! Form::number('estimated_value', $lead->estimated_value, [
                            'class' => 'form-control',
                            'step' => '0.01',
                            'placeholder' => '0.00',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('currency', 'Currency') !!}
                        {!! Form::select(
                            'currency',
                            [
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                                'CAD' => 'CAD',
                                'AUD' => 'AUD',
                            ],
                            $lead->currency ?? 'USD',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('lead_score', 'Lead Score (0-100)') !!}
                        {!! Form::number('lead_score', $lead->lead_score ?? 0, [
                            'class' => 'form-control',
                            'min' => '0',
                            'max' => '100',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('rating', 'Rating (1-5)') !!}
                        {!! Form::select(
                            'rating',
                            [
                                '' => 'Select Rating',
                                '1' => '1 Star',
                                '2' => '2 Stars',
                                '3' => '3 Stars',
                                '4' => '4 Stars',
                                '5' => '5 Stars',
                            ],
                            $lead->rating,
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('next_follow_up_date', 'Next Follow-up Date') !!}
                        {!! Form::datetimeLocal(
                            'next_follow_up_date',
                            $lead->next_follow_up_date ? $lead->next_follow_up_date->format('Y-m-d\TH:i') : null,
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('preferred_contact_method', 'Preferred Contact Method') !!}
                        {!! Form::select(
                            'preferred_contact_method',
                            [
                                'phone' => 'Phone',
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'whatsapp' => 'WhatsApp',
                            ],
                            $lead->preferred_contact_method ?? 'phone',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
            </div>

            <!-- Notes and Additional Information -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Notes & Additional Information</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('notes', 'Public Notes') !!}
                        {!! Form::textarea('notes', $lead->notes, [
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => 'Notes visible to all users',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('internal_notes', 'Internal Notes') !!}
                        {!! Form::textarea('internal_notes', $lead->internal_notes, [
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => 'Private notes for internal use',
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Flags and Special Attributes -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Special Attributes</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_qualified', 1, $lead->is_qualified ?? false) !!}
                                Qualified Lead
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_hot_lead', 1, $lead->is_hot_lead ?? false) !!}
                                Hot Lead
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('requires_immediate_attention', 1, $lead->requires_immediate_attention ?? false) !!}
                                Requires Immediate Attention
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#lead_edit_form').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: "POST",
                url: form.attr('action'),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('div.lead_modal').modal('hide');
                        toastr.success(result.msg);
                        $('#leads_table').DataTable().ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    });
</script>

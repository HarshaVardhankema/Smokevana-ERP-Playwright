<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\LeadController::class, 'store']),
            'method' => 'post',
            'id' => 'lead_add_form',
        ]) !!}

        <div class="modal-header">
            <h4 class="modal-title">@lang('lang_v1.add_lead')</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -30px">
                <button type="submit"
                    class="tw-dw-btn tw-dw-btn-primary tw-text-white no-print">@lang('messages.save')</button>
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
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('store_name', __('lang_v1.store_name') . ':*') !!}
                        {!! Form::text('store_name', null, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.store_name'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('company_name', 'Company Name') !!}
                        {!! Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => 'Company Name']) !!}
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
                        {!! Form::text('contact_name', null, ['class' => 'form-control', 'placeholder' => 'Contact Person Name']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contact_email', 'Contact Email') !!}
                        {!! Form::email('contact_email', null, ['class' => 'form-control', 'placeholder' => 'contact@example.com']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contact_phone', 'Contact Phone') !!}
                        {!! Form::text('contact_phone', null, ['class' => 'form-control', 'placeholder' => '+1234567890']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('website', 'Website') !!}
                        {!! Form::url('website', null, ['class' => 'form-control', 'placeholder' => 'https://example.com']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('industry', 'Industry') !!}
                        {!! Form::text('industry', null, ['class' => 'form-control', 'placeholder' => 'e.g., Retail, Manufacturing']) !!}
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-primary">Address Information</h5>
                </div>
            </div>

            <!-- Hidden fields for coordinates - MUST BE BEFORE component -->
            {!! Form::hidden('latitude', null, ['id' => 'latitude']) !!}
            {!! Form::hidden('longitude', null, ['id' => 'longitude']) !!}

            <x-address-autocomplete addressInput="address_line_1" cityInput="city" stateInput="state"
                stateFormat="short_name" zipInput="zip_code" countryInput="country" countryFormat="short_name" 
                latInput="latitude" lngInput="longitude" />

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_1', __('lang_v1.address_line_1') . ':*') !!}
                        {!! Form::text('address_line_1', null, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.address_line_1'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('address_line_2', __('lang_v1.address_line_2')) !!}
                        {!! Form::text('address_line_2', null, [
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
                        {!! Form::text('city', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.city')]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('state', __('lang_v1.state') . ':*') !!}
                        {!! Form::text('state', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.state')]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('zip_code', __('lang_v1.zip_code') . ':*') !!}
                        {!! Form::text('zip_code', null, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => __('lang_v1.zip_code'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('country', __('lang_v1.country') . ':*') !!}
                        {!! Form::text('country', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.country')]) !!}
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
                            'admin_panel',
                            ['class' => 'form-control'],
                        ) !!}
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
                            'new',
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
                            'medium',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
            </div>

            <div class="row">
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
                            'initial_contact',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('assigned_to', 'Assigned To') !!}
                        {!! Form::select('assigned_to', $users->pluck('name', 'id'), null, [
                            'class' => 'form-control',
                            'placeholder' => 'Select User',
                            'id' => 'assigned_to',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sales_rep_id', 'Sales Rep') !!}
                        {!! Form::select('sales_rep_id', $users->pluck('name', 'id'), auth()->user()->id, [
                            'class' => 'form-control',
                            'placeholder' => 'Select Sales Rep',
                            'id' => 'sales_rep_id',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="auto_assign_location" checked>
                                Auto-assign based on location
                            </label>
                        </div>
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
                        {!! Form::number('estimated_value', null, [
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
                            'USD',
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('lead_score', 'Lead Score (0-100)') !!}
                        {!! Form::number('lead_score', 0, ['class' => 'form-control', 'min' => '0', 'max' => '100']) !!}
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
                            null,
                            ['class' => 'form-control'],
                        ) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('next_follow_up_date', 'Next Follow-up Date') !!}
                        {!! Form::datetimeLocal('next_follow_up_date', null, ['class' => 'form-control']) !!}
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
                            'phone',
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
                        {!! Form::textarea('notes', null, [
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => 'Notes visible to all users',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('internal_notes', 'Internal Notes') !!}
                        {!! Form::textarea('internal_notes', null, [
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
                                {!! Form::checkbox('is_qualified', 1, false) !!}
                                Qualified Lead
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_hot_lead', 1, false) !!}
                                Hot Lead
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('requires_immediate_attention', 1, false) !!}
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
        // Auto-assign current user as sales rep
        $('#sales_rep_id').val('{{ auth()->user()->id }}');

        // =============================================
        // STORE COORDINATES GLOBALLY when Google Places fires
        // =============================================
        window.lastSelectedPlace = null;
        
        // Listen to ALL Google Places events on the page
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).hasClass('pac-item')) {
                console.log('🎯 Google Places dropdown shown');
            }
        });
        
        // Override ANY autocomplete on address_line_1 field
        var checkAndAttachListener = function() {
            var addressField = document.getElementById('address_line_1');
            if (addressField && typeof google !== 'undefined' && google.maps) {
                console.log('🔧 Attaching coordinate capture...');
                
                // Create new autocomplete
                var autocomplete = new google.maps.places.Autocomplete(addressField, {
                    types: ['address'],
                    componentRestrictions: { country: 'us' }
                });
                
                // Capture coordinates when place changes
                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    console.log('📍 PLACE CHANGED EVENT FIRED');
                    console.log('Place object:', place);
                    
                    if (place && place.geometry && place.geometry.location) {
                        var lat = typeof place.geometry.location.lat === 'function' 
                            ? place.geometry.location.lat() 
                            : place.geometry.location.lat;
                        var lng = typeof place.geometry.location.lng === 'function' 
                            ? place.geometry.location.lng() 
                            : place.geometry.location.lng;
                        
                        console.log('✅ Extracted coordinates:', lat, lng);
                        
                        // Store globally
                        window.lastSelectedPlace = { lat: lat, lng: lng };
                        
                        // Set immediately
                        $('#latitude').val(lat);
                        $('#longitude').val(lng);
                        
                        // Verify
                        setTimeout(function() {
                            console.log('🔍 Verification - Lat field:', $('#latitude').val());
                            console.log('🔍 Verification - Lng field:', $('#longitude').val());
                        }, 100);
                    }
                });
                
                console.log('✅ Listener attached!');
            }
        };
        
        setTimeout(checkAndAttachListener, 1500);
        setTimeout(checkAndAttachListener, 3000); // Try again in case of delay
        // =============================================


        // Auto-assignment based on location checkbox
        $('#auto_assign_location').change(function() {
            if ($(this).is(':checked')) {
                $('#assigned_to').val('{{ auth()->user()->id }}');
                $('#assigned_to').prop('disabled', true);
            } else {
                $('#assigned_to').prop('disabled', false);
            }
        });

        // Form submission
        $('#lead_add_form').submit(function(e) {
            e.preventDefault();
            
            // FORCE coordinates from global storage if fields are empty
            if ((!$('#latitude').val() || !$('#longitude').val()) && window.selectedPlaceCoordinates) {
                console.log('🔧 FORCING coordinates from global store');
                $('#latitude').val(window.selectedPlaceCoordinates.lat);
                $('#longitude').val(window.selectedPlaceCoordinates.lng);
            }
            
            // DEBUG: Check coordinates before submit
            console.log('🚀 FORM SUBMIT - Checking coordinates...');
            console.log('Latitude field value:', $('#latitude').val());
            console.log('Longitude field value:', $('#longitude').val());
            console.log('Global coordinates store:', window.selectedPlaceCoordinates);
            
            var form = $(this);
            var data = form.serialize();
            
            console.log('📤 Serialized form data:', data);
            
            // FINAL CHECK: If still empty, manually append to data
            if (!$('#latitude').val() || !$('#longitude').val()) {
                console.warn('⚠️ Coordinates still empty in fields!');
                if (window.selectedPlaceCoordinates) {
                    // Force set fields one more time
                    $('#latitude').val(window.selectedPlaceCoordinates.lat);
                    $('#longitude').val(window.selectedPlaceCoordinates.lng);
                    
                    // Re-serialize with new values
                    data = form.serialize();
                    console.log('✅ FORCED coordinates into fields and re-serialized');
                    console.log('New data:', data);
                } else {
                    console.error('❌ No coordinates in global store - lead will be saved without location!');
                }
            } else {
                console.log('✅ Coordinates present in form fields!');
            }
            
            submitForm(form, data);
        });

        function submitForm(form, data) {
            $.ajax({
                method: "POST",
                url: form.attr('action'),
                dataType: "json",
                data: data,
                beforeSend: function() {
                    $('#lead_add_form button[type="submit"]').prop('disabled', true).html(
                        '<i class="fa fa-spinner fa-spin"></i> Creating...');
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div.lead_modal').modal('hide');
                        toastr.success(result.msg);
                        $('#leads_table').DataTable().ajax.reload();

                        // Send notification if lead was auto-assigned
                        if ($('#auto_assign_location').is(':checked')) {
                            sendNotification('New lead created and assigned to you!', 'success');
                        }
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while creating the lead.');
                },
                complete: function() {
                    $('#lead_add_form button[type="submit"]').prop('disabled', false).html('Save');
                }
            });
        }

        // Notification function
        function sendNotification(message, type) {
            if (type === 'success') {
                toastr.success(message);
            } else {
                toastr.info(message);
            }
        }
    });
</script>

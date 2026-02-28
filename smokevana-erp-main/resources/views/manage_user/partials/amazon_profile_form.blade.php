<!-- Personal Information Card -->
<div class="amazon-card">
    <div class="amazon-card-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        Personal Information
    </div>
    <div class="amazon-card-body">
        <div class="amazon-form-row">
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="user_dob">{{ __('lang_v1.dob') }}</label>
                    {!! Form::text('dob', !empty($user->dob) ? @format_date($user->dob) : null, ['class' => 'amazon-form-control', 'id' => 'user_dob', 'placeholder' => 'Select date', 'readonly']) !!}
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="gender">{{ __('lang_v1.gender') }}</label>
                    <select name="gender" id="gender" class="amazon-form-control">
                        <option value="">Please Select</option>
                        <option value="male" {{ (!empty($user->gender) && $user->gender == 'male') ? 'selected' : '' }}>{{ __('lang_v1.male') }}</option>
                        <option value="female" {{ (!empty($user->gender) && $user->gender == 'female') ? 'selected' : '' }}>{{ __('lang_v1.female') }}</option>
                        <option value="others" {{ (!empty($user->gender) && $user->gender == 'others') ? 'selected' : '' }}>{{ __('lang_v1.others') }}</option>
                    </select>
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="marital_status">{{ __('lang_v1.marital_status') }}</label>
                    <select name="marital_status" id="marital_status" class="amazon-form-control">
                        <option value="">Please Select</option>
                        <option value="married" {{ (!empty($user->marital_status) && $user->marital_status == 'married') ? 'selected' : '' }}>{{ __('lang_v1.married') }}</option>
                        <option value="unmarried" {{ (!empty($user->marital_status) && $user->marital_status == 'unmarried') ? 'selected' : '' }}>{{ __('lang_v1.unmarried') }}</option>
                        <option value="divorced" {{ (!empty($user->marital_status) && $user->marital_status == 'divorced') ? 'selected' : '' }}>{{ __('lang_v1.divorced') }}</option>
                    </select>
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="blood_group">{{ __('lang_v1.blood_group') }}</label>
                    <select name="blood_group" id="blood_group" class="amazon-form-control">
                        <option value="">Please Select</option>
                        <option value="A+" {{ (!empty($user->blood_group) && $user->blood_group == 'A+') ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ (!empty($user->blood_group) && $user->blood_group == 'A-') ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ (!empty($user->blood_group) && $user->blood_group == 'B+') ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ (!empty($user->blood_group) && $user->blood_group == 'B-') ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ (!empty($user->blood_group) && $user->blood_group == 'AB+') ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ (!empty($user->blood_group) && $user->blood_group == 'AB-') ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ (!empty($user->blood_group) && $user->blood_group == 'O+') ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ (!empty($user->blood_group) && $user->blood_group == 'O-') ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="amazon-section-divider"></div>

        <div class="amazon-form-row">
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="contact_number">{{ __('lang_v1.mobile_number') }}</label>
                    <div class="amazon-input-group">
                        <span class="amazon-input-group-prepend">+1</span>
                        {!! Form::text('contact_number', !empty($user->contact_number) ? $user->contact_number : null, ['class' => 'amazon-form-control', 'id' => 'contact_number', 'placeholder' => '1234567890', 'maxlength' => '10', 'style' => 'border-top-left-radius: 0; border-bottom-left-radius: 0;']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="alt_number">{{ __('business.alternate_number') }}</label>
                    <div class="amazon-input-group">
                        <span class="amazon-input-group-prepend">+1</span>
                        {!! Form::text('alt_number', !empty($user->alt_number) ? $user->alt_number : null, ['class' => 'amazon-form-control', 'id' => 'alt_number', 'placeholder' => '1234567890', 'maxlength' => '10', 'style' => 'border-top-left-radius: 0; border-bottom-left-radius: 0;']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="family_number">{{ __('lang_v1.family_contact_number') }}</label>
                    <div class="amazon-input-group">
                        <span class="amazon-input-group-prepend">+1</span>
                        {!! Form::text('family_number', !empty($user->family_number) ? $user->family_number : null, ['class' => 'amazon-form-control', 'id' => 'family_number', 'placeholder' => '1234567890', 'maxlength' => '10', 'style' => 'border-top-left-radius: 0; border-bottom-left-radius: 0;']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="amazon-section-divider"></div>

        <div class="amazon-form-row">
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="guardian_name">{{ __('lang_v1.guardian_name') }}</label>
                    {!! Form::text('guardian_name', !empty($user->guardian_name) ? $user->guardian_name : null, ['class' => 'amazon-form-control', 'id' => 'guardian_name', 'placeholder' => 'Enter guardian name']) !!}
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="id_proof_name">{{ __('lang_v1.id_proof_name') }}</label>
                    <select name="id_proof_name" id="id_proof_name" class="amazon-form-control">
                        <option value="">Please Select</option>
                        <option value="Driver License" {{ (!empty($user->id_proof_name) && $user->id_proof_name == 'Driver License') ? 'selected' : '' }}>Driver License</option>
                        <option value="Passport" {{ (!empty($user->id_proof_name) && $user->id_proof_name == 'Passport') ? 'selected' : '' }}>Passport</option>
                        <option value="SSN" {{ (!empty($user->id_proof_name) && $user->id_proof_name == 'SSN') ? 'selected' : '' }}>SSN</option>
                        <option value="State ID" {{ (!empty($user->id_proof_name) && $user->id_proof_name == 'State ID') ? 'selected' : '' }}>State ID</option>
                        <option value="Other" {{ (!empty($user->id_proof_name) && $user->id_proof_name == 'Other') ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            <div class="amazon-form-col">
                <div class="amazon-form-group">
                    <label for="id_proof_number">{{ __('lang_v1.id_proof_number') }}</label>
                    {!! Form::text('id_proof_number', !empty($user->id_proof_number) ? $user->id_proof_number : null, ['class' => 'amazon-form-control', 'id' => 'id_proof_number', 'placeholder' => 'Enter ID number']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Information - Side by Side Cards -->
<div class="amazon-cards-row">
    <!-- Permanent Address Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Permanent Address
        </div>
        <div class="amazon-card-body">
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="permanent_address">Street Address</label>
                        {!! Form::text('permanent_address', !empty($user->permanent_address) ? $user->permanent_address : null, ['class' => 'amazon-form-control', 'id' => 'permanent_address', 'placeholder' => 'Street address, P.O. box']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="permanent_city">City</label>
                        {!! Form::text('permanent_city', !empty($user->permanent_city) ? $user->permanent_city : null, ['class' => 'amazon-form-control', 'id' => 'permanent_city', 'placeholder' => 'City']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="permanent_state">State</label>
                        <select name="permanent_state" id="permanent_state" class="amazon-form-control">
                            <option value="">Select State</option>
                            @php
                                $us_states = ['AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming'];
                            @endphp
                            @foreach($us_states as $code => $name)
                                <option value="{{ $code }}" {{ (!empty($user->permanent_state) && $user->permanent_state == $code) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="permanent_zip">ZIP Code</label>
                        {!! Form::text('permanent_zip', !empty($user->permanent_zip) ? $user->permanent_zip : null, ['class' => 'amazon-form-control', 'id' => 'permanent_zip', 'placeholder' => 'ZIP Code', 'maxlength' => '10']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="permanent_country">Country</label>
                        <input type="text" class="amazon-form-control" id="permanent_country" value="United States" readonly style="background-color: #F7F8F8; cursor: not-allowed;">
                        <input type="hidden" name="permanent_country" value="US">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Address Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Current Address
            <label style="margin-left: auto; font-weight: 400; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                <input type="checkbox" id="same_as_permanent" style="width: 14px; height: 14px; cursor: pointer;">
                Same as Permanent
            </label>
        </div>
        <div class="amazon-card-body">
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="current_address">Street Address</label>
                        {!! Form::text('current_address', !empty($user->current_address) ? $user->current_address : null, ['class' => 'amazon-form-control', 'id' => 'current_address', 'placeholder' => 'Street address, P.O. box']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="current_city">City</label>
                        {!! Form::text('current_city', !empty($user->current_city) ? $user->current_city : null, ['class' => 'amazon-form-control', 'id' => 'current_city', 'placeholder' => 'City']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="current_state">State</label>
                        <select name="current_state" id="current_state" class="amazon-form-control">
                            <option value="">Select State</option>
                            @foreach($us_states as $code => $name)
                                <option value="{{ $code }}" {{ (!empty($user->current_state) && $user->current_state == $code) ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="current_zip">ZIP Code</label>
                        {!! Form::text('current_zip', !empty($user->current_zip) ? $user->current_zip : null, ['class' => 'amazon-form-control', 'id' => 'current_zip', 'placeholder' => 'ZIP Code', 'maxlength' => '10']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="current_country">Country</label>
                        <input type="text" class="amazon-form-control" id="current_country_display" value="United States" readonly style="background-color: #F7F8F8; cursor: not-allowed;">
                        <input type="hidden" name="current_country" value="US">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Social Media & Bank Details - Side by Side Cards -->
<div class="amazon-cards-row">
    <!-- Social Media Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
            </svg>
            Social Media
        </div>
        <div class="amazon-card-body">
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="fb_link" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#1877F2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            Facebook
                        </label>
                        {!! Form::text('fb_link', !empty($user->fb_link) ? $user->fb_link : null, ['class' => 'amazon-form-control', 'id' => 'fb_link', 'placeholder' => 'https://facebook.com/username']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="twitter_link" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#1DA1F2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                            Twitter
                        </label>
                        {!! Form::text('twitter_link', !empty($user->twitter_link) ? $user->twitter_link : null, ['class' => 'amazon-form-control', 'id' => 'twitter_link', 'placeholder' => 'https://twitter.com/username']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="social_media_1" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#0A66C2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            LinkedIn
                        </label>
                        {!! Form::text('social_media_1', !empty($user->social_media_1) ? $user->social_media_1 : null, ['class' => 'amazon-form-control', 'id' => 'social_media_1', 'placeholder' => 'https://linkedin.com/in/username']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="social_media_2" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            Other
                        </label>
                        {!! Form::text('social_media_2', !empty($user->social_media_2) ? $user->social_media_2 : null, ['class' => 'amazon-form-control', 'id' => 'social_media_2', 'placeholder' => 'Other social media URL']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Details Card -->
    <div class="amazon-card">
        <div class="amazon-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                <line x1="1" y1="10" x2="23" y2="10"></line>
            </svg>
            Bank Details
        </div>
        <div class="amazon-card-body">
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="account_holder_name">Account Holder Name</label>
                        {!! Form::text('bank_details[account_holder_name]', !empty($bank_details['account_holder_name']) ? $bank_details['account_holder_name'] : null, ['class' => 'amazon-form-control', 'id' => 'account_holder_name', 'placeholder' => 'Full name on account']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="account_number">Account Number</label>
                        {!! Form::text('bank_details[account_number]', !empty($bank_details['account_number']) ? $bank_details['account_number'] : null, ['class' => 'amazon-form-control', 'id' => 'account_number', 'placeholder' => 'Account number']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="bank_name">Bank Name</label>
                        {!! Form::text('bank_details[bank_name]', !empty($bank_details['bank_name']) ? $bank_details['bank_name'] : null, ['class' => 'amazon-form-control', 'id' => 'bank_name', 'placeholder' => 'Bank name']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="bank_code">Routing Number
                            <span class="amazon-tooltip" data-tip="{{ __('lang_v1.bank_code_help') }}">i</span>
                        </label>
                        {!! Form::text('bank_details[bank_code]', !empty($bank_details['bank_code']) ? $bank_details['bank_code'] : null, ['class' => 'amazon-form-control', 'id' => 'bank_code', 'placeholder' => '9-digit routing number', 'maxlength' => '9']) !!}
                    </div>
                </div>
            </div>
            <div class="amazon-form-row">
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="branch">Branch</label>
                        {!! Form::text('bank_details[branch]', !empty($bank_details['branch']) ? $bank_details['branch'] : null, ['class' => 'amazon-form-control', 'id' => 'branch', 'placeholder' => 'Branch name']) !!}
                    </div>
                </div>
                <div class="amazon-form-col">
                    <div class="amazon-form-group">
                        <label for="tax_payer_id">Tax Payer ID (SSN/EIN)
                            <span class="amazon-tooltip" data-tip="{{ __('lang_v1.tax_payer_id_help') }}">i</span>
                        </label>
                        {!! Form::text('bank_details[tax_payer_id]', !empty($bank_details['tax_payer_id']) ? $bank_details['tax_payer_id'] : null, ['class' => 'amazon-form-control', 'id' => 'tax_payer_id', 'placeholder' => 'XXX-XX-XXXX']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Same as Permanent Address checkbox functionality
    var sameAsPermanent = document.getElementById('same_as_permanent');
    if (sameAsPermanent) {
        sameAsPermanent.addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('current_address').value = document.getElementById('permanent_address').value;
                document.getElementById('current_city').value = document.getElementById('permanent_city').value;
                document.getElementById('current_state').value = document.getElementById('permanent_state').value;
                document.getElementById('current_zip').value = document.getElementById('permanent_zip').value;
            }
        });
    }

    // Mobile number formatting - only allow numbers
    var phoneFields = ['contact_number', 'alt_number', 'family_number'];
    phoneFields.forEach(function(fieldId) {
        var field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });

    // ZIP code formatting - only allow numbers and hyphen
    var zipFields = ['permanent_zip', 'current_zip'];
    zipFields.forEach(function(fieldId) {
        var field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9-]/g, '');
            });
        }
    });

    // Bank code (routing number) - only allow numbers
    var bankCode = document.getElementById('bank_code');
    if (bankCode) {
        bankCode.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>

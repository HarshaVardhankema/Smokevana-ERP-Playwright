<style>
    .invalid-feedback {
        display: none;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .is-invalid ~ .invalid-feedback {
        display: block;
    }
    .pac-container { z-index: 999999; }
</style>
<div class="modal-dialog merchant-application-modal modal-lg" role="document">
    <div class="modal-content">
        <!-- Amazon theme: dark header -->
        <div class="modal-header">
            <h4 class="modal-title">Merchant Application</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <style>
        /* Merchant Application – Amazon theme */
        .merchant-application-modal .modal-content {
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .merchant-application-modal .modal-header {
            background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 3px solid #ff9900;
        }
        .merchant-application-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
        .merchant-application-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; }
        .merchant-application-modal .modal-header .close:hover { color: #ff9900; opacity: 1; }
        .merchant-application-modal .modal-body {
            background: #37475a;
            padding: 1.25rem 1.5rem;
            max-height: min(85vh, 680px);
            overflow-y: auto;
        }
        .merchant-application-modal .step-indicators {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            padding: 0 4px;
        }
        .merchant-application-modal .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .merchant-application-modal .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 20px;
            right: -50%;
            width: 100%;
            height: 2px;
            background-color: #4a5d6e;
            z-index: 1;
        }
        .merchant-application-modal .step.active:not(:last-child):after,
        .merchant-application-modal .step:has(~ .step.active):not(:last-child):after {
            background-color: #ff9900;
        }
        .merchant-application-modal .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4a5d6e;
            color: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            position: relative;
            z-index: 2;
            font-weight: 600;
            font-size: 14px;
        }
        .merchant-application-modal .step.active .step-number,
        .merchant-application-modal .step:has(~ .step.active) .step-number {
            background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%);
            color: #fff;
        }
        .merchant-application-modal .step-title {
            font-size: 13px;
            color: rgba(255,255,255,0.65);
        }
        .merchant-application-modal .step.active .step-title {
            color: #ff9900;
            font-weight: 600;
        }
        .merchant-application-modal .step-content {
            padding: 20px 0;
            background: #fff;
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            border: 1px solid #D5D9D9;
        }
        .merchant-application-modal .step-content h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #232F3E;
            margin: 0 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #D5D9D9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .merchant-application-modal .step-content h5::before {
            content: '';
            width: 4px;
            height: 1.1em;
            background: #FF9900;
            border-radius: 2px;
        }
        .merchant-application-modal .step-content .form-group { margin-bottom: 0.75rem; }
        .merchant-application-modal .step-content label {
            color: #0F1111 !important;
            font-size: 0.8125rem;
            font-weight: 500;
        }
        .merchant-application-modal .step-content .form-control {
            background: #fff;
            border: 1px solid #D5D9D9;
            color: #0F1111;
            font-size: 0.8125rem;
            padding: 0.375rem 0.5rem;
            min-height: 2rem;
            box-sizing: border-box;
        }
        .merchant-application-modal .step-content .form-control:focus {
            border-color: #FF9900;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
        }
        .merchant-application-modal .step-content .radio label,
        .merchant-application-modal .step-content .radio-inline { color: #0F1111 !important; }
        .merchant-application-modal .step-content input[type="radio"] { accent-color: #FF9900; }
        .merchant-application-modal .is-invalid { border-color: #dc3545 !important; }
        .merchant-application-modal .modal-footer {
            background: #f0f2f2;
            border-top: 1px solid #D5D9D9;
            padding: 0.75rem 1.25rem;
        }
        .merchant-application-modal .modal-footer #nextBtn,
        .merchant-application-modal .modal-footer #submitBtn {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
            border-color: #C7511F !important;
            color: #fff !important;
            font-weight: 600;
            padding: 8px 24px;
            border-radius: 6px;
        }
        .merchant-application-modal .modal-footer #prevBtn {
            background: #fff !important;
            border: 1px solid #D5D9D9 !important;
            color: #0f1111 !important;
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 6px;
        }
        .merchant-application-modal .modal-footer #prevBtn:hover {
            background: #f7f8f8 !important;
            border-color: #a2a6a6 !important;
        }
        .merchant-application-modal #add_owner {
            background: #37475a !important;
            border-color: #4a5d6e !important;
            color: #fff !important;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
        }
        .merchant-application-modal #add_owner:hover {
            background: #4a5d6e !important;
            color: #fff !important;
        }
        </style>

        <form id="merchantApplicationForm" action="{{ route('merchant-applications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <!-- Step Indicators -->
                <div class="step-indicators mb-4">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-title">Business Info</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-title">Owner Info</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-title">Processing Info</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-number">4</span>
                        <span class="step-title">Documents</span>
                    </div>
                    <div class="step" data-step="5">
                        <span class="step-number">5</span>
                        <span class="step-title">Gateway option</span>
                    </div>
                </div>

                <!-- Step 1: Business Information -->
                <div class="step-content" id="step1">
                    <h5>Business Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Legal Business Name *</label>
                                <input type="text" class="form-control" name="legal_business_name" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>DBA Name (if applicable)</label>
                                <input type="text" class="form-control" name="dba_name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Business Type *</label>
                                <select class="form-control" name="business_type" required>
                                    <option value="">Select Business Type</option>
                                    <option value="sole_proprietorship">Sole Proprietorship</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="corporation">Corporation</option>
                                    <option value="llc">LLC</option>
                                </select>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Federal Tax ID *</label>
                                <input type="text" class="form-control" name="federal_tax_id" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Business Age *</label>
                                <input type="text" class="form-control" name="business_age" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Business Phone *</label>
                                <input type="tel" class="form-control" name="business_phone" pattern="[0-9]+" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Website</label>
                                <input type="url" class="form-control" name="website" pattern="https?://.+" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Legal Address *</label>
                                <input type="text" class="form-control" name="legal_address" id="legal_address" required autocomplete="off" role="presentation">
                                <input type="text" name="fake_legal_address" autocomplete="street-address" style="display:none;">
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" class="form-control" name="legal_city" id="legal_city" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" class="form-control" name="legal_state" id="legal_state" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ZIP Code *</label>
                                <input type="text" class="form-control" name="legal_zip" id="legal_zip" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Owner Information -->
                <div class="step-content" id="step2" style="display: none;">
                    <h5>Owner Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Legal Name *</label>
                                <input type="text" class="form-control" name="owner_legal_name" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ownership Percentage *</label>
                                <input type="number" class="form-control" name="ownership_percentage" min="0" max="100" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Job Title *</label>
                                <input type="text" class="form-control" name="job_title" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of Birth *</label>
                                <input type="date" class="form-control" name="date_of_birth" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" class="form-control" name="owner_email" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="tel" class="form-control" name="owner_phone" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Home Address *</label>
                                <input type="text" class="form-control" name="owner_address" id="owner_address" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" class="form-control" name="owner_city" id="owner_city" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" class="form-control" name="owner_state" id="owner_state" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ZIP Code *</label>
                                <input type="text" class="form-control" name="owner_zip" id="owner_zip" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Social Security Number *</label>
                                <input type="text" class="form-control" name="ssn" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div id="additional_owners">
                        <!-- Additional owners will be added here -->
                    </div>

                    <button type="button" class="btn btn-info" id="add_owner">
                        <i class="fa fa-plus"></i> Add Additional Owner
                    </button>
                </div>

                <!-- Step 3: Previous Processing Information -->
                <div class="step-content" id="step3" style="display: none;">
                    <h5>Previous Processing Information</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Have you had previous processing? *</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="has_previous_processing" value="1" required>
                                        Yes
                                    </label>
                                    <label class="ml-3">
                                        <input type="radio" name="has_previous_processing" value="0" required>
                                        No
                                    </label>
                                </div>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div id="previous_processing_details" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Processing Duration *</label>
                                    <input type="text" class="form-control" name="processing_duration" required>
                                    <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Previous Processor *</label>
                                    <input type="text" class="form-control" name="previous_processor" required>
                                    <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Average Ticket Amount *</label>
                                    <input type="number" class="form-control" name="average_ticket_amount" step="0.01" required>
                                    <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Monthly Volume *</label>
                                    <input type="number" class="form-control" name="monthly_volume" step="0.01" required>
                                    <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Documents -->
                <div class="step-content" id="step4" style="display: none;">
                    <h5>Required Documents</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Voided Check *</label>
                                <input type="file" class="form-control" name="voided_check" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Driver's License/ID *</label>
                                <input type="file" class="form-control" name="driver_license" required>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Processing Statements (if applicable)</label>
                                <input type="file" class="form-control" name="processing_statements">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Gateway option -->
                <div class="step-content" id="step5" style="display: none;">
                    <h5>Gateway option</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Gateway option</label>
                                <select class="form-control" name="gateway_option" required>
                                    <option value="">Select Gateway</option>
                                    <option value="nmi">NMI</option>
                                    <option value="authorize">Authorize.net</option>
                                    <option value="square">Square</option>
                                    <option value="razorpay">Razorpay</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="other">Other</option>
                                </select>
                                <span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Submit Application</button>
                </div>
        </form>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_address') }}&libraries=places"></script>
<script>
$(document).ready(function() {
    function initAutocompleteLegal() {
        var input = document.getElementById('legal_address');
        var options = {
            types: ['address'],
            componentRestrictions: { country: 'us' }
        };
        var autocomplete = new google.maps.places.Autocomplete(input, options);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) return;

            let streetNumber = getAddressComponent(place, 'street_number');
            let route = getAddressComponent(place, 'route');
            let streetAddress = `${streetNumber} ${route}`.trim();

            document.getElementById('legal_address').value = streetAddress;
            document.getElementById('legal_city').value = getAddressComponent(place, 'locality');
            document.getElementById('legal_state').value = getAddressComponent(place, 'administrative_area_level_1', 'short_name');
            document.getElementById('legal_zip').value = getAddressComponent(place, 'postal_code');
            document.getElementById('country_code').value = getAddressComponent(place, 'country', 'short_name');
        });
    }

    function initAutocompleteOwner() {
        var input = document.getElementById('owner_address');
        var options = {
            types: ['address'],
            componentRestrictions: { country: 'us' }
        };
        var autocomplete = new google.maps.places.Autocomplete(input, options);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) return;

            let streetNumber = getAddressComponent(place, 'street_number');
            let route = getAddressComponent(place, 'route');
            let streetAddress = `${streetNumber} ${route}`.trim();

            document.getElementById('owner_address').value = streetAddress;
            document.getElementById('owner_city').value = getAddressComponent(place, 'locality');
            document.getElementById('owner_state').value = getAddressComponent(place, 'administrative_area_level_1', 'short_name');
            document.getElementById('owner_zip').value = getAddressComponent(place, 'postal_code');
            document.getElementById('country_code').value = getAddressComponent(place, 'country', 'short_name');
        });
    }

    function getAddressComponent(place, type, format = 'long_name') {
        for (var i = 0; i < place.address_components.length; i++) {
            for (var j = 0; j < place.address_components[i].types.length; j++) {
                if (place.address_components[i].types[j] === type) {
                    return place.address_components[i][format];
                }
            }
        }
        return '';
    }

    // Add dummy field to mislead browser autofill
    $('<input type="text" name="fake_legal_address" autocomplete="street-address" style="display:none;">').appendTo('#merchantApplicationForm');

    // Prevent browser autocomplete on legal_address
    $('#legal_address')
        .attr('autocomplete', 'off')
        .attr('role', 'presentation')
        .on('focus', function() {
            if (!$(this).val()) {
                $(this).val(' '); // Temporary space to deter autofill
            }
        })
        .on('blur', function() {
            if ($(this).val() === ' ') {
                $(this).val(''); // Clear space if no user input
            }
        });

    // Validation function for required fields
    function validateInput(input) {
        const $input = $(input);
        const value = $input.val().trim();
        const type = $input.attr('type');
        const name = $input.attr('name');
        let isValid = true;
        let errorMessage = '';

        // Clear previous error message
        const $errorContainer = $input.next('.invalid-feedback');
        $errorContainer.hide().text('');

        // Basic check for empty required fields
        if ($input.prop('required') && !value) {
            isValid = false;
            errorMessage = `${$input.prev('label').text() || 'This field'} is required`;
        }

        // Specific validations based on input type
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        } else if (type === 'tel' && value) {
            const phoneRegex = /^[0-9]+$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Phone number must contain only numbers';
            }
        } else if (type === 'number' && value && name === 'ownership_percentage') {
            const num = parseFloat(value);
            if (num < 0 || num > 100) {
                isValid = false;
                errorMessage = 'Ownership percentage must be between 0 and 100';
            }
        } else if (type === 'file' && $input.prop('required') && !input.files.length) {
            isValid = false;
            errorMessage = `${$input.prev('label').text() || 'File'} is required`;
        } else if ($input.is('select') && $input.prop('required') && !value) {
            isValid = false;
            errorMessage = `${$input.prev('label').text() || 'Selection'} is required`;
        } else if ($input.is(':radio') && $input.prop('required') && !$input.is(':checked')) {
            isValid = false;
            errorMessage = `${$input.closest('.form-group').find('label').first().text() || 'Selection'} is required`;
        }

        // Apply or remove is-invalid class and show error message
        if (!isValid) {
            $input.addClass('is-invalid');
            if ($input.is(':radio')) {
                $input.closest('.form-group').find('.invalid-feedback').text(errorMessage).show();
            } else {
                $errorContainer.text(errorMessage).show();
            }
        } else {
            $input.removeClass('is-invalid');
            if ($input.is(':radio')) {
                $input.closest('.form-group').find('.invalid-feedback').hide().text('');
            } else {
                $errorContainer.hide().text('');
            }
        }

        return isValid;
    }

    // Validate phone fields specifically for Next button
    function validatePhoneFields() {
        let isValid = true;
        const phoneFields = $('input[type="tel"][name="business_phone"], input[type="tel"][name="owner_phone"]');
        phoneFields.each(function() {
            const $input = $(this);
            const value = $input.val().trim();
            const $errorContainer = $input.next('.invalid-feedback');
            $errorContainer.hide().text('');

            if ($input.prop('required') && !value) {
                isValid = false;
                $input.addClass('is-invalid');
                $errorContainer.text(`${$input.prev('label').text()} is required`).show();
            } else if (value && !/^[0-9]+$/.test(value)) {
                isValid = false;
                $input.addClass('is-invalid');
                $errorContainer.text('Phone number must contain only numbers').show();
            } else {
                $input.removeClass('is-invalid');
                $errorContainer.hide().text('');
            }
        });
        // if (!isValid) {
        //     toastr.error('Please correct the phone number fields.');
        // }
        return isValid;
    }

    // Attach blur event to all required inputs
    $('#merchantApplicationForm').find(':input[required]').on('blur', function() {
        validateInput(this);
    });

    // Handle radio buttons for has_previous_processing
    $('input[name="has_previous_processing"]').on('change', function() {
        const $details = $('#previous_processing_details');
        if ($(this).val() === '1') {
            $details.show();
            $details.find(':input').each(function() {
                $(this).prop('required', true);
            });
        } else {
            $details.hide();
            $details.find(':input').each(function() {
                $(this).prop('required', false).removeClass('is-invalid');
                $(this).next('.invalid-feedback').hide().text('');
            });
        }
        validateInput(this);
    });

    // // Intercept Next button click to validate phone fields and show toastr
    // $('#nextBtn').on('click', function(e) {
    //     if (!validatePhoneFields()) {
    //         e.preventDefault(); // Prevent step navigation
    //         // toastr.error('Please correct the phone number fields.');
    //         return false;
    //     }
    //     // Allow merchant_applications.js to handle step navigation
    // });

    // Initialize autocomplete
    document.addEventListener('DOMContentLoaded', function() {
        initAutocompleteLegal();
        initAutocompleteOwner();
    });

    $('#legal_address').on('focus', function() {
        initAutocompleteLegal();
    });
    $('#owner_address').on('focus', function() {
        initAutocompleteOwner();
    });

    // Handle dynamically added fields (e.g., additional owners)
    $(document).on('blur', '#additional_owners :input[required]', function() {
        validateInput(this);
    });
});
</script>
<script src="{{ asset('js/merchant_applications.js') }}"></script>

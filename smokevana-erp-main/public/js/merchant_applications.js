//Merchant Applications
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 5;
    // Update progress bar
    function updateProgress() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
        
        // Update step indicators
        $('.step').removeClass('active');
        $(`.step[data-step="${currentStep}"]`).addClass('active');
    }

    // Show/hide navigation buttons
    function updateButtons() {
        if (currentStep === 1) {
            $('#prevBtn').hide();
        } else {
            $('#prevBtn').show();
        }

        if (currentStep === totalSteps) {
            $('#nextBtn').hide();
            $('#submitBtn').show();
        } else {
            $('#nextBtn').show();
            $('#submitBtn').hide();
        }
    }

    // Handle next button click
    $('#nextBtn').click(function() {
        if (validateStep(currentStep)) {
            $(`#step${currentStep}`).hide();
            currentStep++;
            $(`#step${currentStep}`).show();
            updateProgress();
            updateButtons();
        }
    });

    // Handle previous button click
    $('#prevBtn').click(function() {
        $(`#step${currentStep}`).hide();
        currentStep--;
        $(`#step${currentStep}`).show();
        updateProgress();
        updateButtons();
    });

    // Validate each step
    function validateStep(step) {
        const currentStepElement = $(`#step${step}`);
        const fieldsToValidate = currentStepElement.find('input, select, textarea');
        let isValid = true;

        fieldsToValidate.each(function() {
            const field = $(this);
            const value = field.val() ? field.val().trim() : '';
            const type = field.attr('type');
            const name = field.attr('name');
            let errorMessage = '';
            let fieldValid = true;
            let $errorContainer = field.next('.invalid-feedback');
            if ($errorContainer.length === 0 && field.is(':radio')) {
                $errorContainer = field.closest('.form-group').find('.invalid-feedback');
            }
            if ($errorContainer.length === 0) {
                $errorContainer = $('<span class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem;"></span>');
                field.after($errorContainer);
            }
            $errorContainer.hide().text('');

            // Required check
            if (field.prop('required')) {
                if (type === 'file') {
                    if (!field[0].files.length) {
                        fieldValid = false;
                        errorMessage = `${field.prev('label').text() || 'File'} is required`;
                    }
                } else if (field.is(':radio')) {
                    const group = currentStepElement.find(`input[name='${field.attr('name')}']`);
                    if (group.filter(':checked').length === 0) {
                        fieldValid = false;
                        errorMessage = `${field.closest('.form-group').find('label').first().text() || 'Selection'} is required`;
                    }
                } else if (field.is('select')) {
                    if (!value) {
                        fieldValid = false;
                        errorMessage = `${field.prev('label').text() || 'Selection'} is required`;
                    }
                } else if (!value) {
                    fieldValid = false;
                    errorMessage = `${field.prev('label').text() || 'This field'} is required`;
                }
            }

            // Type-specific validation
            if (fieldValid && value) {
                if (type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        fieldValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                } else if (type === 'tel') {
                    const phoneRegex = /^[0-9]+$/;
                    if (!phoneRegex.test(value)) {
                        fieldValid = false;
                        errorMessage = 'Phone number must contain only numbers';
                    }
                } else if (type === 'number') {
                    const num = parseFloat(value);
                    const min = field.attr('min') !== undefined ? parseFloat(field.attr('min')) : undefined;
                    const max = field.attr('max') !== undefined ? parseFloat(field.attr('max')) : undefined;
                    if (isNaN(num)) {
                        fieldValid = false;
                        errorMessage = 'Please enter a valid number';
                    } else {
                        if (min !== undefined && num < min) {
                            fieldValid = false;
                            errorMessage = `Value must be at least ${min}`;
                        }
                        if (max !== undefined && num > max) {
                            fieldValid = false;
                            errorMessage = `Value must be at most ${max}`;
                        }
                        if ((name === 'ownership_percentage' || name?.includes('[percentage]')) && (num < 0 || num > 100)) {
                            fieldValid = false;
                            errorMessage = 'Ownership percentage must be between 0 and 100';
                        }
                    }
                } else if (field.attr('pattern')) {
                    const pattern = new RegExp(field.attr('pattern'));
                    if (!pattern.test(value)) {
                        fieldValid = false;
                        errorMessage = 'Invalid format';
                    }
                } else if (type === 'url' || name === 'website') {
                    // Website URL validation (only if not empty)
                    if (value) {
                        // Require http(s) protocol
                        const urlRegex = /^(https?:\/\/)[\w\-]+(\.[\w\-]+)+([\w\-.,@?^=%&:/~+#]*[\w\-@?^=%&/~+#])?$/i;
                        const isValidUrl = urlRegex.test(value);
                        console.log('Website validation:', value, 'Valid:', isValidUrl);
                        if (!isValidUrl) {
                            fieldValid = false;
                            errorMessage = 'Please enter a valid website URL (e.g., https://example.com)';
                        }
                    }
                }
            }

            // Apply or remove is-invalid class and show error message
            if (!fieldValid) {
                field.addClass('is-invalid');
                if (field.is(':radio')) {
                    field.closest('.form-group').find('.invalid-feedback').text(errorMessage).show();
                } else {
                    $errorContainer.text(errorMessage).show();
                }
                isValid = false;
            } else {
                field.removeClass('is-invalid');
                if (field.is(':radio')) {
                    field.closest('.form-group').find('.invalid-feedback').hide().text('');
                } else {
                    $errorContainer.hide().text('');
                }
            }
        });

        if (!isValid) {
            toastr.warning('Please fill in all required fields correctly.');
        }

        return isValid;
    }

    // Toggle previous processing details
    $('input[name="has_previous_processing"]').change(function() {
        if ($(this).val() === '1') {
            $('#previous_processing_details').show();
            $('#previous_processing_details input').prop('required', true);
        } else {
            $('#previous_processing_details').hide();
            $('#previous_processing_details input').prop('required', false);
        }
    });

    // Add additional owner
    $('#add_owner').click(function() {
        const ownerCount = $('.additional-owner').length;
        const ownerHtml = `
            <div class="additional-owner mb-3">
                <h5>Additional Owner ${ownerCount + 1}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" class="form-control" name="additional_owners[${ownerCount}][name]" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ownership Percentage *</label>
                            <input type="number" class="form-control" name="additional_owners[${ownerCount}][percentage]" min="0" max="100" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date of Birth *</label>
                            <input type="date" class="form-control" name="additional_owners[${ownerCount}][dob]" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Social Security Number *</label>
                            <input type="text" class="form-control" name="additional_owners[${ownerCount}][ssn]" required>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-owner">Remove Owner</button>
            </div>
        `;
        $('#additional_owners').append(ownerHtml);
    });

    // Remove additional owner
    $(document).on('click', '.remove-owner', function() {
        $(this).closest('.additional-owner').remove();
    });

    // Form submission
    $('#merchantApplicationForm').submit(function(e) {
        e.preventDefault();
        
        // Prevent browser's default validation
        if (!this.checkValidity()) {
            e.stopPropagation();
        }
        
        if (!validateStep(currentStep)) {
            return;
        }
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success('Application submitted successfully!');
                    // just close the modal
                    $('div.merchant_application_add_model').modal('hide');
                    // // reset the form
                    // $('#merchantApplicationForm')[0].reset();
                    // // reset the progress bar
                    // $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
                    // // reset the step indicators
                    // $('.step').removeClass('active');
                } else {
                    toastr.error('Error submitting application: ' + response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) { // Validation error
                    var errors = xhr.responseJSON.errors;
                    
                    // Find the first field with an error
                    var firstErrorField = Object.keys(errors)[0];
                    
                    // Map field names to step numbers
                    var fieldToStep = {
                        'legal_business_name': 1,
                        'dba_name': 1,
                        'business_type': 1,
                        'federal_tax_id': 1,
                        'business_age': 1,
                        'business_phone': 1,
                        'website': 1,
                        'legal_address': 1,
                        'legal_city': 1,
                        'legal_state': 1,
                        'legal_zip': 1,
                        'owner_legal_name': 2,
                        'ownership_percentage': 2,
                        'job_title': 2,
                        'date_of_birth': 2,
                        'owner_email': 2,
                        'owner_phone': 2,
                        'owner_address': 2,
                        'owner_city': 2,
                        'owner_state': 2,
                        'owner_zip': 2,
                        'ssn': 2,
                        'has_previous_processing': 3,
                        'processing_duration': 3,
                        'previous_processor': 3,
                        'average_ticket_amount': 3,
                        'monthly_volume': 3,
                        'voided_check': 4,
                        'driver_license': 4,
                        'processing_statements': 4,
                        'gateway_option': 5
                    };

                    // Get the step number for the first error
                    var stepNumber = fieldToStep[firstErrorField];
                    
                    if (stepNumber) {
                        // Navigate to the step with the error
                        $('.step-content').hide();
                        $('#step' + stepNumber).show();
                        
                        // Update progress bar and step indicators
                        var progress = ((stepNumber - 1) / 4) * 100;
                        $('.progress-bar').css('width', progress + '%');
                        
                        $('.step').removeClass('active');
                        $('.step[data-step="' + stepNumber + '"]').addClass('active');
                        
                        // Show/hide navigation buttons
                        $('#prevBtn').show();
                        if (stepNumber === 5) {
                            $('#nextBtn').hide();
                            $('#submitBtn').show();
                        } else {
                            $('#nextBtn').show();
                            $('#submitBtn').hide();
                        }
                        
                        // Add error class to the field
                        var errorField = $('#' + firstErrorField);
                        errorField.addClass('is-invalid');
                        
                        // Scroll to the error field
                        $('html, body').animate({
                            scrollTop: errorField.offset().top - 100
                        }, 500);
                        
                        // Show error message using toastr instead of alert
                        toastr.error(errors[firstErrorField][0]);
                        
                        // Focus the field after a short delay to ensure it's visible
                        setTimeout(function() {
                            errorField.focus();
                        }, 100);
                    }
                }
            }
        });
    });

    // Clear error styling and messages when field is modified
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
        var $errorContainer = $(this).next('.invalid-feedback');
        if ($errorContainer.length === 0 && $(this).is(':radio')) {
            $errorContainer = $(this).closest('.form-group').find('.invalid-feedback');
        }
        $errorContainer.hide().text('');
    });

    // Initialize the form
    updateProgress();
    updateButtons();
});
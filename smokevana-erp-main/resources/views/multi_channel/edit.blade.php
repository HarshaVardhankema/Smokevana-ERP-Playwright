@extends('layouts.app')

@section('title', 'Edit Multi Channel')

@section('css')
    @include('layouts.partials.amazon_admin_styles')
    <style>
        .multi-channel-edit-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
        .mc-edit-banner {
            background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
            border-radius: 0 0 10px 10px;
            padding: 22px 28px;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            position: relative;
            overflow: hidden;
        }
        .mc-edit-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
        .mc-edit-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
        .mc-edit-banner .banner-title i { color: #fff !important; }
        .mc-edit-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
        .mc-edit-banner .amazon-orange-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; font-weight: 600; padding: 10px 24px; border-radius: 6px; }
        .mc-edit-banner .amazon-orange-btn:hover { color: #fff !important; opacity: 0.95; }
        .multi-channel-edit-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .multi-channel-edit-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
        .multi-channel-edit-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
        .multi-channel-edit-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
        .multi-channel-edit-page .box-primary .box-title i { color: #fff !important; }
        .multi-channel-edit-page .box-primary .tw-flow-root { background: #f7f8f8 !important; padding: 1.25rem 1.5rem !important; }
        .multi-channel-edit-page .box-primary .tw-flow-root .form-group { margin-bottom: 0.75rem; }
        .multi-channel-edit-page .box-primary .tw-flow-root label { color: #0F1111 !important; }
        .multi-channel-edit-page .box-primary .tw-flow-root .form-control { background: #fff; border: 1px solid #D5D9D9; }
        .multi-channel-edit-page .box-primary .tw-flow-root .form-control:focus { border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2); }
        .multi-channel-edit-page .box-primary .tw-flow-root .help-block { color: #565959; font-size: 0.8125rem; }
        .multi-channel-edit-page .btn-primary { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
        .multi-channel-edit-page .btn-primary:hover { color: #fff !important; opacity: 0.95; }
        .alert { margin-bottom: 1rem; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .loading { opacity: 0.7; cursor: not-allowed !important; }
        button[type="submit"]:disabled { cursor: not-allowed; opacity: 0.7; }
        .input-error { color: #d9534f; font-size: 0.95em; margin-top: 2px; }
        .input-invalid, .select2-container--default .select2-selection--single.input-invalid, .select2-container--default .select2-selection--multiple.input-invalid { border: 1.5px solid #d9534f !important; box-shadow: 0 0 2px #d9534f; }
        .multi-channel-edit-page .meta-section { margin-bottom: 20px; border: 1px solid #D5D9D9; border-radius: 8px; padding: 15px; background: #fff; }
        .multi-channel-edit-page .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #ff9900; }
        .multi-channel-edit-page .section-header h5 { margin: 0; color: #232f3e; font-weight: bold; }
        .multi-channel-edit-page .meta-data-rows { margin-top: 10px; }
        .multi-channel-edit-page .meta-data-row { margin-bottom: 10px; padding: 10px; background: #f7f8f8; border-radius: 6px; border: 1px solid #D5D9D9; }
        .multi-channel-edit-page .section-title { display: flex; align-items: center; }
        .multi-channel-edit-page .section-actions { display: flex; align-items: center; }
        .multi-channel-edit-page .section-name { font-weight: bold; color: #0F1111; }
        .multi-channel-edit-page .add-section { margin-top: 15px; }
    </style>
@endsection

@section('content')
    {{-- Multi Channel Edit Form --}}
    {!! Form::open([
        'url' => action([\App\Http\Controllers\ECOM\MultichannelController::class, 'apiUpdate'], [$multichannel->id]),
        'method' => 'PUT',
        'id' => 'edit_multichannel_form',
    ]) !!}
    
    <!-- Amazon-style banner -->
    <section class="content-header">
        <div class="mc-edit-banner amazon-theme-banner">
            <div>
                <h1 class="banner-title"><i class="fas fa-edit"></i> Edit Multi Channel</h1>
                <p class="banner-subtitle">Update your multi-channel content with flexible settings</p>
            </div>
            <button type="submit" class="btn btn-primary amazon-orange-btn">Update</button>
        </div>
    </section>

    <!-- ================= Basic Information Section ================= -->
    <section class="content multi-channel-edit-page">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Basic Information', 'title_svg' => '<i class="fa fa-globe" aria-hidden="true"></i>'])
        <div class="row">
            <div class="col-md-3">
                {{-- Channel Type Input --}}
                {!! Form::label('type', 'Channel Type *') !!}
                {!! Form::select('type', [
                    'youtube' => 'YouTube',
                    'facebook' => 'Facebook',
                    'instagram' => 'Instagram',
                    'twitter' => 'Twitter',
                    'linkedin' => 'LinkedIn',
                    'tiktok' => 'TikTok',
                    'website' => 'Website',
                    'blog' => 'Blog',
                    'landing_page' => 'Landing Page',
                    'category_page' => 'Category Page',
                    'product_page' => 'Product Page',
                    'other' => 'Other'
                ], $multichannel->type, ['class' => 'form-control', 'required', 'placeholder' => 'Select Channel Type']) !!}
            </div>
            <div class="col-md-3">
                {{-- Status Dropdown --}}
                {!! Form::label('status', 'Status *') !!}
                {!! Form::select('status', [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'pending' => 'Pending',
                    'draft' => 'Draft'
                ], $multichannel->status, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-3">
                {{-- Visibility Toggle --}}
                {!! Form::label('visibility', 'Visibility *') !!}
                {!! Form::select('visibility', [
                    '1' => 'Public',
                    '0' => 'Private'
                ], $multichannel->visibility ? '1' : '0', ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="col-md-3">
                {{-- Title Input --}}
                {!! Form::label('title', 'Title *') !!}
                {!! Form::text('title', $multichannel->title, ['class' => 'form-control', 'required', 'placeholder' => 'Enter Channel Title']) !!}
            </div>
        </div>
        @endcomponent

        <!-- ================= URL Configuration Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'URL Configuration', 'title_svg' => '<i class="fa fa-link"></i>'])
        <div class="row">
            <div class="col-md-6">
                {{-- URL Input --}}
                {!! Form::label('url', 'Channel URL *') !!}
                {!! Form::text('url', $multichannel->url, ['class' => 'form-control', 'required', 'placeholder' => 'e.g. /exp.smokevana.com/home or https://example.com']) !!}
            </div>
            <div class="col-md-6">
                {{-- Thumbnail URL Input --}}
                {!! Form::label('thumbnail_url', 'Thumbnail URL') !!}
                {!! Form::text('thumbnail_url', $multichannel->thumbnail_url, ['class' => 'form-control', 'placeholder' => 'https://example.com/image.jpg']) !!}
                <small class="help-block">Optional: URL to thumbnail image</small>
            </div>
        </div>
        @endcomponent

        <!-- ================= Meta Information Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Meta Information', 'title_svg' => '<i class="fa fa-info-circle"></i>'])
        <div class="row">
            <div class="col-md-12">
                {{-- Short Meta Description --}}
                {!! Form::label('short_meta', 'Short Meta Description') !!}
                {!! Form::textarea('short_meta', $multichannel->short_meta, ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Enter short description or meta information']) !!}
                <small class="help-block">Optional: Brief description or meta information</small>
            </div>
        </div>
        @endcomponent

        <!-- ================= Content Description Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Content Description', 'title_svg' => '<i class="fa fa-file-text"></i>'])
        <div class="row">
            <div class="col-md-12">
                {{-- Description Textarea with TinyMCE --}}
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', null, ['class' => 'form-control description', 'rows' => '10', 'placeholder' => 'Enter detailed description of the channel content...']) !!}
                <small class="help-block">Optional: Detailed description of the channel content</small>
            </div>
        </div>
        @endcomponent

        <!-- ================= Additional Meta Data Section ================= -->
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Additional Meta Data', 'title_svg' => '<i class="fa fa-cogs"></i>'])
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Meta Data (Dynamic Sections)</label>
                    <div id="meta-data-container">
                        <!-- Sections will be loaded here dynamically -->
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary btn-sm add-section">
                            <i class="fa fa-plus"></i> Add New Section
                        </button>
                    </div>
                    <small class="help-block">Create dynamic sections and organize your meta data. Each section can contain multiple key-value pairs with options.</small>
                </div>
            </div>
        </div>
        @endcomponent
    </section>
    {!! Form::close() !!}
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        // --- Initialize TinyMCE for Description Field ---
        if ($('textarea.description').length > 0) {
            tinymce.init({
                selector: 'textarea.description',
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
            });
        }

        // --- Load existing meta data ---
        var existingMetaData = @json($multichannel->meta_data ? json_decode($multichannel->meta_data, true) : []);
        loadExistingMetaData(existingMetaData);

        // --- Dynamic Section Management ---
        var sectionCounter = 0;

        function generateSectionId() {
            return 'section_' + (++sectionCounter);
        }

        function loadExistingMetaData(metaData) {
            if (metaData && typeof metaData === 'object') {
                Object.keys(metaData).forEach(function(sectionName) {
                    if (Array.isArray(metaData[sectionName])) {
                        createSectionWithData(sectionName, metaData[sectionName]);
                    }
                });
            }
        }

        function createSectionWithData(sectionName, sectionData) {
            var sectionId = generateSectionId();
            var sectionHtml = `
                <div class="meta-section" data-section-id="${sectionId}">
                    <div class="section-header">
                        <div class="section-title">
                            <input type="text" class="form-control section-name" value="${sectionName}" placeholder="Section Name" style="width: 200px; display: inline-block;">
                            <button type="button" class="btn btn-warning btn-sm edit-section-name" style="margin-left: 10px;">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                        <div class="section-actions">
                            <button type="button" class="btn btn-success btn-sm add-meta-row" data-section="${sectionName}">
                                <i class="fa fa-plus"></i> Add Row
                            </button>
                            <button type="button" class="btn btn-danger btn-sm remove-section" style="margin-left: 5px;">
                                <i class="fa fa-trash"></i> Remove Section
                            </button>
                        </div>
                    </div>
                    <div class="meta-data-rows" data-section="${sectionName}">
                    </div>
                </div>
            `;
            
            $('#meta-data-container').append(sectionHtml);
            
            // Add existing data rows
            var $rowsContainer = $('#meta-data-container').find(`[data-section-id="${sectionId}"] .meta-data-rows`);
            sectionData.forEach(function(item) {
                var rowHtml = `
                    <div class="meta-data-row row">
                        <div class="col-md-2">
                            <input type="text" class="form-control meta-key" placeholder="Key (e.g., slider_slide_1)" name="meta_keys[]" value="${item.key || ''}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control meta-value" placeholder="Value" name="meta_values[]" value="${item.value || ''}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control meta-option1" placeholder="Option 1" name="meta_option1s[]" value="${item.option1 || ''}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control meta-option2" placeholder="Option 2" name="meta_option2s[]" value="${item.option2 || ''}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control meta-option3" placeholder="Option 3" name="meta_option3s[]" value="${item.option3 || ''}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-meta-row">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $rowsContainer.append(rowHtml);
            });
            
            updateMetaRowButtons();
        }

        function updateMetaRowButtons() {
            $('.meta-section').each(function() {
                var $rows = $(this).find('.meta-data-row');
                if ($rows.length > 1) {
                    $rows.find('.remove-meta-row').show();
                } else {
                    $rows.find('.remove-meta-row').hide();
                }
            });
        }

        // Add New Section
        $(document).on('click', '.add-section', function (e) {
            e.preventDefault();
            
            var sectionId = generateSectionId();
            var sectionName = prompt('Enter section name (e.g., hero_section, accordian_section):', 'new_section');
            
            if (sectionName && sectionName.trim()) {
                createSectionWithData(sectionName, []);
            }
        });

        // Edit Section Name
        $(document).on('click', '.edit-section-name', function() {
            var $sectionName = $(this).siblings('.section-name');
            var currentName = $sectionName.val();
            var newName = prompt('Enter new section name:', currentName);
            
            if (newName && newName.trim()) {
                $sectionName.val(newName);
                $(this).closest('.meta-section').find('.meta-data-rows').data('section', newName);
                $(this).closest('.meta-section').find('.add-meta-row').data('section', newName);
            }
        });

        // Remove Section
        $(document).on('click', '.remove-section', function() {
            if (confirm('Are you sure you want to remove this section and all its data?')) {
                $(this).closest('.meta-section').remove();
                updateMetaRowButtons();
            }
        });

        // Add Meta Data Row
        $(document).on('click', '.add-meta-row', function (e) {
            e.preventDefault();

            var $section = $(this).closest('.meta-section');
            var sectionId = $section.find('.meta-data-rows').data('section');

            var newRowHtml = `
                <div class="meta-data-row row">
                    <div class="col-md-2">
                        <input type="text" class="form-control meta-key" placeholder="Key (e.g., slider_slide_1)" name="meta_keys[]">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control meta-value" placeholder="Value" name="meta_values[]">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control meta-option1" placeholder="Option 1" name="meta_option1s[]">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control meta-option2" placeholder="Option 2" name="meta_option2s[]">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control meta-option3" placeholder="Option 3" name="meta_option3s[]">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-meta-row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            var $rowsContainer = $section.find('.meta-data-rows');
            $rowsContainer.append(newRowHtml);
            updateMetaRowButtons();
        });

        // Remove Meta Data Row
        $(document).on('click', '.remove-meta-row', function () {
            $(this).closest('.meta-data-row').remove();
            updateMetaRowButtons();
        });

        // --- VALIDATION LOGIC ---
        function showFieldError($input, message) {
            removeFieldError($input);
            $input.addClass('input-invalid');
            $input.focus();
            if ($input.next('.input-error').length === 0) {
                $input.after('<div class="input-error">' + message + '</div>');
            }
        }

        function removeFieldError($input) {
            $input.removeClass('input-invalid');
            $input.next('.input-error').remove();
        }

        $(document).on('input change', '.input-invalid, .form-control', function () {
            removeFieldError($(this));
        });

        // --- Form Validation ---
        function validateForm() {
            // Remove all previous errors
            $('.input-error').remove();
            $('.input-invalid').removeClass('input-invalid');

            // Validate Type
            var $type = $('select[name="type"]');
            var type = $type.val();
            if (!type) {
                showFieldError($type, 'Channel Type is required.');
                return false;
            }

            // Validate Status
            var $status = $('select[name="status"]');
            var status = $status.val();
            if (!status) {
                showFieldError($status, 'Status is required.');
                return false;
            }

            // Validate Visibility
            var $visibility = $('select[name="visibility"]');
            var visibility = $visibility.val();
            if (visibility === '') {
                showFieldError($visibility, 'Visibility is required.');
                return false;
            }

            // Validate Title
            var $title = $('input[name="title"]');
            var title = $title.val();
            if (!title || title.trim().length < 3) {
                showFieldError($title, 'Title is required and must be at least 3 characters.');
                return false;
            }

            // Validate URL
            var $url = $('input[name="url"]');
            var url = $url.val();
            if (!url || url.trim() === '') {
                showFieldError($url, 'Channel URL is required.');
                return false;
            }

            // Validate Thumbnail URL (if provided)
            var $thumbnailUrl = $('input[name="thumbnail_url"]');
            var thumbnailUrl = $thumbnailUrl.val();
            if (thumbnailUrl && thumbnailUrl.trim() !== '') {
                // Only validate thumbnail URL if it looks like a full URL (starts with http:// or https://)
                if (thumbnailUrl.trim().startsWith('http://') || thumbnailUrl.trim().startsWith('https://')) {
                    if (!isValidUrl(thumbnailUrl)) {
                        showFieldError($thumbnailUrl, 'Please enter a valid thumbnail URL.');
                        return false;
                    }
                }
            }

            // All validations passed
            return true;
        }

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // --- Form Submission Handler ---
        $('#edit_multichannel_form').on('submit', function (e) {
            e.preventDefault();
            if (!validateForm()) {
                return false;
            }

            let payload = createPayload();
            console.log('Payload:', payload);

            // Disable the update button and show loading state
            $('button[type="submit"]').prop('disabled', true).text('Updating...').addClass('loading');

            $.ajax({
                url: $(this).attr('action'),
                method: 'PUT',
                data: JSON.stringify(payload),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('button[type="submit"]').prop('disabled', false).text('Update').removeClass('loading');
                    swal({
                        title: "Success!",
                        text: "Multi Channel updated successfully!",
                        icon: "success",
                        button: "OK",
                    }).then((value) => {
                        window.location.href = '/multi-channel';
                    });
                },
                error: function (xhr) {
                    var errorMessage = 'Error updating multi channel. Please try again.';
                    
                    console.log('Status:', xhr.status);
                    console.log('Response Text:', xhr.responseText);

                    // Handle 422 validation errors
                    if (xhr.status === 422) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            console.log('Parsed Response:', response);

                            if (response.errors) {
                                var errorMessages = [];
                                for (var field in response.errors) {
                                    if (response.errors.hasOwnProperty(field)) {
                                        errorMessages.push(field + ': ' + response.errors[field].join(', '));
                                    }
                                }
                                errorMessage = 'Validation errors:\n' + errorMessages.join('\n');
                            } else if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            errorMessage = 'Server validation error. Please check the console for details.';
                        }
                    }

                    // Re-enable the update button after error
                    $('button[type="submit"]').prop('disabled', false).text('Update').removeClass('loading');
                    
                    swal({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        button: "OK",
                    });
                    console.error('Error:', xhr);
                }
            });
        });

        // --- Payload Construction ---
        function createPayload() {
            // Get TinyMCE content
            var description = '';
            if (tinymce.get('description')) {
                description = tinymce.get('description').getContent();
            }

            // Build meta data object with dynamic sectioned structure
            var metaData = {};

            $('.meta-section').each(function() {
                var sectionName = $(this).find('.section-name').val();
                var sectionId = $(this).find('.meta-data-rows').data('section');
                
                // Use section name from input, fallback to data attribute
                var finalSectionName = sectionName || sectionId;
                
                if (finalSectionName) {
                    metaData[finalSectionName] = [];

                    $(this).find('.meta-data-row').each(function() {
                        var key = $(this).find('.meta-key').val();
                        var value = $(this).find('.meta-value').val();
                        var option1 = $(this).find('.meta-option1').val();
                        var option2 = $(this).find('.meta-option2').val();
                        var option3 = $(this).find('.meta-option3').val();

                        if (key) {
                            metaData[finalSectionName].push({
                                key: key,
                                value: value || null,
                                option1: option1 || null,
                                option2: option2 || null,
                                option3: option3 || null
                            });
                        }
                    });
                }
            });

            // Add description to meta data if exists
            if (description) {
                metaData.description = description;
            }

            var payload = {
                type: $('select[name="type"]').val(),
                visibility: $('select[name="visibility"]').val() === '1',
                status: $('select[name="status"]').val(),
                title: $('input[name="title"]').val(),
                url: $('input[name="url"]').val(),
                thumbnail_url: $('input[name="thumbnail_url"]').val() || null,
                short_meta: $('textarea[name="short_meta"]').val() || null,
                meta_data: Object.keys(metaData).length > 0 ? metaData : null
            };

            return payload;
        }
    });
</script>
@endsection

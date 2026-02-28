@extends('layouts.app')
@section('title', 'Edit Complaint')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Edit Complaint</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            {!! Form::model($complaint, ['url' => action([\App\Http\Controllers\ComplaintController::class, 'update'], [$complaint->id]), 'method' => 'PUT', 'id' => 'complaint_form', 'files' => true, 'enctype' => 'multipart/form-data']) !!}
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('request_type', 'Request Type:*') !!}
                        {!! Form::text('request_type', null, ['class' => 'form-control', 'required', 'placeholder' => 'e.g., Product Issue, Delivery Problem']) !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', 'Status:*') !!}
                        {!! Form::select('status', [
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            'rejected' => 'Rejected'
                        ], null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('contact_id', 'Customer/Contact:') !!}
                        {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'placeholder' => 'Select Customer/Contact', 'style' => 'width:100%']) !!}
                        <small class="help-block">Optional - Select the customer related to this complaint</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('transaction_id', 'Transaction ID:') !!}
                        {!! Form::number('transaction_id', null, ['class' => 'form-control', 'placeholder' => 'Enter transaction ID']) !!}
                        <small class="help-block">Optional - Leave blank if not related to a transaction</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('variation_id', 'Products/Variations:') !!}
                        <select name="variation_id[]" id="variation_id" class="form-control select2" style="width:100%" multiple>
                            @if($complaint->variation_ids && is_array($complaint->variation_ids))
                                @php
                                    $selectedVariations = \App\Variation::with('product')
                                        ->whereIn('id', $complaint->variation_ids)
                                        ->get();
                                @endphp
                                @foreach($selectedVariations as $variation)
                                    <option value="{{ $variation->id }}" selected>
                                        {{ $variation->product->name }}{{ $variation->name ? ' - ' . $variation->name : '' }} (SKU: {{ $variation->sub_sku ?: 'N/A' }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <small class="help-block">Select transaction to load products, then select one or more products</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', 'Description:') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe the complaint in detail']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('admin_response', 'Admin Response:') !!}
                        {!! Form::textarea('admin_response', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Enter admin response to this complaint']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('existing_images', 'Existing Images:') !!}
                        @if($complaint->attachments && is_array($complaint->attachments) && count($complaint->attachments) > 0)
                            <div class="row" id="existing_images">
                                @foreach($complaint->attachments as $index => $attachment)
                                    <div class="col-md-3" style="margin-bottom: 10px;" id="image_{{ $index }}">
                                        <div class="thumbnail">
                                            <img src="{{ asset($attachment) }}" style="width: 100%; height: 150px; object-fit: cover;">
                                            <div class="caption">
                                                <button type="button" class="btn btn-danger btn-xs btn-block delete-image-btn" data-image="{{ $attachment }}" data-index="{{ $index }}">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No existing images</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('images', 'Upload New Images:') !!}
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple id="complaint_images">
                        <small class="help-block">Upload additional images (JPEG, PNG, JPG, GIF - Max 2MB each)</small>
                        
                        <!-- New Image Preview Container -->
                        <div id="image_preview" class="row" style="margin-top: 15px;"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-text-white tw-border-none pull-right">
                        @lang('messages.update')
                    </button>
                    <a href="{{ action([\App\Http\Controllers\ComplaintController::class, 'index']) }}" class="tw-dw-btn tw-dw-btn-error pull-right" style="margin-right: 10px;">
                        @lang('messages.cancel')
                    </a>
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        
        var imagesToDelete = [];
        
        // Store initial values
        var initialContactId = $('#contact_id').val();
        var initialTransactionId = $('#transaction_id').val();
        var initialVariationIds = $('#variation_id').val() || []; // Array of variation IDs
        
        // Enable/disable transaction dropdown based on initial contact
        if (!initialContactId) {
            $('#transaction_id').prop('disabled', true);
        }
        
        // Enable/disable variation dropdown based on initial transaction
        if (!initialTransactionId) {
            $('#variation_id').prop('disabled', true);
        }
        
        // Load transactions when contact is selected
        $('#contact_id').on('change', function() {
            var contact_id = $(this).val();
            var transaction_select = $('#transaction_id');
            var variation_select = $('#variation_id');
            
            // If contact changed, reset dependent dropdowns
            if (contact_id != initialContactId) {
                transaction_select.html('<option value="">Loading...</option>').prop('disabled', true).trigger('change');
                variation_select.html('<option value="">Select Transaction First</option>').prop('disabled', true).trigger('change');
            }
            
            if (contact_id) {
                $.ajax({
                    url: '/complaints/contact/' + contact_id + '/transactions',
                    type: 'GET',
                    success: function(data) {
                        var currentTransactionId = initialContactId == contact_id ? initialTransactionId : null;
                        transaction_select.html('<option value="">Select Transaction</option>');
                        
                        $.each(data, function(index, transaction) {
                            var selected = currentTransactionId == transaction.id ? ' selected' : '';
                            transaction_select.append('<option value="' + transaction.id + '"' + selected + '>' + transaction.text + '</option>');
                        });
                        
                        transaction_select.prop('disabled', false).trigger('change');
                    },
                    error: function() {
                        transaction_select.html('<option value="">Error loading transactions</option>');
                        toastr.error('Failed to load transactions');
                    }
                });
            } else {
                transaction_select.html('<option value="">Select Contact First</option>').prop('disabled', true);
                variation_select.html('<option value="">Select Transaction First</option>').prop('disabled', true);
            }
        });
        
        // Load variations when transaction is selected
        $('#transaction_id').on('change', function() {
            var transaction_id = $(this).val();
            var variation_select = $('#variation_id');
            
            // If transaction changed, reset variation dropdown
            if (transaction_id != initialTransactionId) {
                variation_select.html('<option value="">Loading...</option>').prop('disabled', true).trigger('change');
            }
            
            if (transaction_id) {
                $.ajax({
                    url: '/complaints/transaction/' + transaction_id + '/variations',
                    type: 'GET',
                    success: function(data) {
                        variation_select.html('');
                        
                        $.each(data, function(index, variation) {
                            var selected = initialTransactionId == transaction_id && initialVariationIds.includes(variation.id.toString()) ? ' selected' : '';
                            variation_select.append('<option value="' + variation.id + '"' + selected + '>' + variation.text + '</option>');
                        });
                        
                        variation_select.prop('disabled', false).trigger('change');
                    },
                    error: function() {
                        variation_select.html('<option value="">Error loading products</option>');
                        toastr.error('Failed to load products');
                    }
                });
            } else {
                variation_select.html('<option value="">Select Transaction First</option>').prop('disabled', true);
            }
        });

        // Delete existing image
        $(document).on('click', '.delete-image-btn', function() {
            var imagePath = $(this).data('image');
            var imageIndex = $(this).data('index');
            
            swal({
                title: 'Are you sure?',
                text: 'This image will be permanently deleted!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Add to deletion list
                    imagesToDelete.push(imagePath);
                    // Update hidden input
                    if ($('#delete_images_input').length === 0) {
                        $('#complaint_form').append('<input type="hidden" name="delete_images[]" id="delete_images_input">');
                    }
                    $('#complaint_form').append('<input type="hidden" name="delete_images[]" value="' + imagePath + '">');
                    
                    // Remove image from display
                    $('#image_' + imageIndex).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Show message if no images left
                        if ($('#existing_images .col-md-3').length === 0) {
                            $('#existing_images').html('<p class="text-muted">No existing images</p>');
                        }
                    });
                    
                    toastr.success('Image marked for deletion');
                }
            });
        });

        // New image preview functionality
        $('#complaint_images').on('change', function(e) {
            var files = e.target.files;
            var previewContainer = $('#image_preview');
            previewContainer.empty();
            
            if (files.length > 0) {
                $.each(files, function(index, file) {
                    if (file.type.match('image.*')) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var imageDiv = $('<div class="col-md-3" style="margin-bottom: 10px;">' +
                                '<div class="thumbnail">' +
                                '<img src="' + e.target.result + '" style="width: 100%; height: 150px; object-fit: cover;">' +
                                '<div class="caption text-center">' +
                                '<small>' + file.name + '</small>' +
                                '</div>' +
                                '</div>' +
                                '</div>');
                            previewContainer.append(imageDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    });
</script>
@endsection


<div class="modal-dialog amazon-form-modal taxonomy-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\TaxonomyController::class, 'update'], [$category->id]),'method' => 'POST', 'id' => 'category_edit_form', 'enctype' => 'multipart/form-data' ]) !!}
    {!! Form::hidden('_method', 'PUT') !!}
    
    @php
      $name_label = !empty($module_category_data['taxonomy_label']) ? $module_category_data['taxonomy_label'] : __( 'category.category_name' );
      $cat_code_enabled = isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code'] ? false : true;
      $cat_code_label = !empty($module_category_data['taxonomy_code_label']) ? $module_category_data['taxonomy_code_label'] : __( 'category.code' );
      $enable_sub_category = isset($module_category_data['enable_sub_taxonomy']) && !$module_category_data['enable_sub_taxonomy'] ? false : true;
      $category_code_help_text = !empty($module_category_data['taxonomy_code_help_text']) ? $module_category_data['taxonomy_code_help_text'] : __('lang_v1.category_code_help');
    @endphp

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Edit Category</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <!-- Card: Media Uploads -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-image"></i> @lang('lang_v1.media_uploads')</h5>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('cat_logo','Upload logo' . ':') !!}
                  {!! Form::file('cat_logo', ['accept' => 'image/*', 'class' => 'form-control amazon-file-input', 'id' => 'cat_logo_input']) !!}
                  <div class="amazon-file-preview" id="cat_logo_preview">
                    @if(!empty($category->cat_logo))
                      <img src="{{ asset('uploads/category_logos/' . $category->cat_logo) }}" alt="Current Logo" style="max-width: 80px; max-height: 60px; margin-top: 8px; border-radius: 4px; border: 1px solid #D5D9D9;">
                    @endif
                  </div>
                  <p class="help-block"><i> @lang('business.logo_help')</i> Previous logo (if exists) will be replaced.</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('cat_banner', 'Upload Banner' . ':') !!}
                  {!! Form::file('cat_banner', ['accept' => 'image/*', 'class' => 'form-control amazon-file-input', 'id' => 'cat_banner_input']) !!}
                  <div class="amazon-file-preview" id="cat_banner_preview">
                    @if(!empty($category->cat_banner))
                      <img src="{{ asset('uploads/category_banners/' . $category->cat_banner) }}" alt="Current Banner" style="max-width: 80px; max-height: 60px; margin-top: 8px; border-radius: 4px; border: 1px solid #D5D9D9;">
                    @endif
                  </div>
                  <p class="help-block"><i> @lang('business.logo_help')</i> Previous banner (if exists) will be replaced.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Basic Information -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-info-circle"></i> @lang('lang_v1.basic_information')</h5>
            @if($is_super_admin)
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::select('location_id', $business_locations, $category->location_id, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select'), 'required']); !!}
                  </div>
                </div>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name', $name_label . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', $category->name, ['class' => 'form-control', 'required', 'placeholder' => $name_label]); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('slug','Slug' . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-link"></i>
                    </span>
                    {!! Form::text('slug', old('slug', $category->slug), ['class' => 'form-control', 'placeholder' => 'auto-generated-slug']); !!}
                  </div>
                  <p class="help-block">Leave empty to auto-generate from name</p>
                </div>
              </div>
            </div>
            @if($cat_code_enabled)
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('short_code', $cat_code_label . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-hashtag"></i>
                    </span>
                    {!! Form::text('short_code', $category->short_code, ['class' => 'form-control', 'placeholder' => 'e.g., HSN001']); !!}
                  </div>
                  <p class="help-block">{!! $category_code_help_text !!}</p>
                </div>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
                  {!! Form::textarea('description', $category->description, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.description'), 'rows' => 3]); !!}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Category Settings -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-cog"></i> @lang('lang_v1.category_settings')</h5>
            @if(!empty($parent_categories) && $enable_sub_category)
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      {!! Form::checkbox('add_as_sub_cat', 1, !$is_parent, ['class' => 'toggler', 'data-toggle_id' => 'parent_cat_div']) !!} @lang( 'lang_v1.add_as_sub_txonomy' )
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group @if($is_parent) hide @endif" id="parent_cat_div">
                  {!! Form::label('parent_id', __( 'category.select_parent_category' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-sitemap"></i>
                    </span>
                    {!! Form::select('parent_id', $parent_categories, $selected_parent, ['class' => 'form-control select_category_id']); !!}
                  </div>
                </div>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('visibility', 'Category Visibilty' . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-eye"></i>
                    </span>
                    {!! Form::select('visibility', [
                        'public' => ('public'),
                        'coming soon' => ('coming soon'),
                        'protected' => ('protected')
                    ], old('visibility', $category->visibility), ['class' => 'form-control'] ); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('brand_ids', 'Select Brands' . ':') !!}
                  {!! Form::select('brand_ids[]', $brands->pluck('name', 'id'), $selected_brand_ids, [
                      'class' => 'form-control select2', 
                      'multiple' => true,
                      'id' => 'brand_ids',
                      'style' => 'width: 100%;',
                      'placeholder' => 'Select brands...'
                  ]); !!}
                  <p class="help-block" style="white-space: normal; word-wrap: break-word;"><i>Select brands that belong to this category (B2C functionality)</i></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white"><i class="fas fa-check"></i> Update Category</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<style>
  /* Additional Amazon styling for file inputs and hover effects */
  .amazon-form-modal .amazon-file-input {
    padding: 0.5rem;
    border-radius: 6px;
    border: 1px solid #D5D9D9;
    background: #ffffff;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .amazon-form-modal .amazon-file-input:hover {
    border-color: #FF9900;
    background: #FFF4E5;
  }
  
  .amazon-form-modal .amazon-file-input:focus {
    border-color: #FF9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
  }
  
  .amazon-form-modal .amazon-file-preview {
    margin-top: 0.5rem;
  }
  
  .amazon-form-modal .amazon-file-preview img {
    max-width: 80px;
    max-height: 60px;
    object-fit: contain;
    border-radius: 4px;
    border: 1px solid #D5D9D9;
    padding: 4px;
    background: #ffffff;
  }
  
  /* Enhanced hover effects for form controls */
  .amazon-form-modal .amazon-form-card .form-control:hover {
    border-color: #B8BDBD;
    background: #ffffff;
  }
  
  .amazon-form-modal .amazon-form-card .input-group-addon {
    transition: all 0.2s ease;
  }
  
  .amazon-form-modal .amazon-form-card .form-control:focus ~ .input-group-addon,
  .amazon-form-modal .amazon-form-card .form-control:focus + .input-group-addon {
    border-color: #FF9900;
    background: #FFF4E5;
  }
  
  /* Button hover effects */
  .amazon-form-modal .modal-footer .btn-primary:hover,
  .amazon-form-modal .modal-footer .tw-dw-btn-primary:hover {
    background: linear-gradient(to bottom, #E47911 0%, #D2691E 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 153, 0, 0.3);
  }
  
  .amazon-form-modal .modal-footer .btn-default:hover,
  .amazon-form-modal .modal-footer .tw-dw-btn-neutral:hover {
    background: rgba(255,255,255,0.15) !important;
    border-color: rgba(255,255,255,0.8) !important;
  }
  
  /* Checkbox styling */
  .amazon-form-modal .amazon-form-card .checkbox label {
    cursor: pointer;
    font-weight: 500;
    color: #0F1111;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .amazon-form-modal .amazon-form-card .checkbox input[type="checkbox"] {
    cursor: pointer;
    width: 18px;
    height: 18px;
    accent-color: #FF9900;
  }
  
  .amazon-form-modal .amazon-form-card .checkbox input[type="checkbox"]:hover {
    transform: scale(1.1);
  }
</style>

<script>
$(document).ready(function() {
  // File preview
  $('#cat_logo_input, #cat_banner_input').on('change', function() {
    var input = this;
    var previewId = $(this).attr('id').replace('_input', '_preview');
    var $preview = $('#' + previewId);
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) { 
        $preview.html('<img src="' + e.target.result + '" alt="" style="max-width: 80px; max-height: 60px; margin-top: 8px; border-radius: 4px; border: 1px solid #D5D9D9; padding: 4px; background: #ffffff;">'); 
      };
      reader.readAsDataURL(input.files[0]);
    }
  });

  // Initialize Select2 for brand dropdown
  var $modal = $('.category_modal');
  $('#brand_ids').select2({
    placeholder: 'Select brands...',
    allowClear: true,
    width: '100%',
    dropdownParent: $modal.length ? $modal : $('body')
  });

  // Location change handler
  $('.select_location_id').change(function() {
    var locationId = $(this).val();
    
    // Load categories for the selected location
    $.ajax({
      url: '/get-categories-for-location/' + locationId,
      type: 'GET',
      data: {is_perentcategory: true},
      success: function(response) {
        $('.select_category_id').empty();
        $('.select_category_id').append('<option value="">Select Category</option>');
        if (response && response.length) {
          response.forEach(function(category) {
            $('.select_category_id').append('<option value="' + category.id + '">' + category.name + '</option>');
          });
        }
      },
      error: function(xhr, status, error) {
        console.log('Error loading categories:', xhr.responseText);
      }
    });

    // Load brands for the selected location
    $.ajax({
      url: '/get-brands-for-location/' + locationId,
      type: 'GET',
      success: function(response) {
        $('#brand_ids').empty();
        $('#brand_ids').append('<option value="">Select brands...</option>');
        if (response && response.length) {
          response.forEach(function(brand) {
            $('#brand_ids').append('<option value="' + brand.id + '">' + brand.name + '</option>');
          });
          $('#brand_ids').trigger('change');
        }
      },
      error: function(xhr, status, error) {
        console.log('Error loading brands:', xhr.responseText);
      }
    });
  });
});
</script>

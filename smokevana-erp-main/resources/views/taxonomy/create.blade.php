<div class="modal-dialog amazon-form-modal taxonomy-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\TaxonomyController::class, 'store']), 'method' => 'post', 'id' => 'category_add_form', 'enctype' => 'multipart/form-data' ]) !!}
    
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'messages.add' )</h4>
    </div>

    <div class="modal-body">
      <input type="hidden" name="category_type" value="{{$category_type}}">
      @php
        $name_label = !empty($module_category_data['taxonomy_label']) ? $module_category_data['taxonomy_label'] : __( 'category.category_name' );
        $cat_code_enabled = isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code'] ? false : true;
        $cat_code_label = !empty($module_category_data['taxonomy_code_label']) ? $module_category_data['taxonomy_code_label'] : __( 'category.code' );
        $enable_sub_category = isset($module_category_data['enable_sub_taxonomy']) && !$module_category_data['enable_sub_taxonomy'] ? false : true;
        $category_code_help_text = !empty($module_category_data['taxonomy_code_help_text']) ? $module_category_data['taxonomy_code_help_text'] : __('lang_v1.category_code_help');
      @endphp

      <div class="row">
        <!-- Card: Media Uploads -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-image"></i> @lang('lang_v1.media_uploads')</h5>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('cat_logo','Upload logo' . ':') !!}
                  {!! Form::file('cat_logo', ['accept' => 'image/*', 'class' => 'form-control']) !!}
                  <p class="help-block"><i> @lang('business.logo_help')</i></p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('cat_banner', 'Upload Banner' . ':') !!}
                  {!! Form::file('cat_banner', ['accept' => 'image/*', 'class' => 'form-control']) !!}
                  <p class="help-block"><i> @lang('business.logo_help')</i></p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('category_banner', 'Upload Category Banner' . ':') !!}
                  {!! Form::file('category_banner', ['accept' => 'image/*', 'class' => 'form-control']) !!}
                  <p class="help-block"><i> @lang('business.logo_help')</i></p>
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
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select') ,'required']); !!}
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
                    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => $name_label]); !!}
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
                    {!! Form::text('slug', null, ['class' => 'form-control']); !!}
                  </div>
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
                    {!! Form::text('short_code', null, ['class' => 'form-control', 'placeholder' => $cat_code_label]); !!}
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
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.description'), 'rows' => 3]); !!}
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
                      {!! Form::checkbox('add_as_sub_cat', 1, false,[ 'class' => 'toggler', 'data-toggle_id' => 'parent_cat_div' ]); !!} @lang( 'lang_v1.add_as_sub_txonomy' )
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group hide" id="parent_cat_div">
                  {!! Form::label('parent_id', __( 'category.select_parent_category' ) . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-sitemap"></i>
                    </span>
                    {!! Form::select('parent_id', $parent_categories, null, ['class' => 'form-control select_category_id']); !!}
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
                    ], null,['class' => 'form-control'] ); !!}
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('brand_ids', 'Select Brands' . ':') !!}
                  {!! Form::select('brand_ids[]', $brands->pluck('name', 'id'), null, [
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
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
   $(document).ready(function() {
    // Initialize Select2 for brand dropdown (dropdownParent so it opens inside the modal)
    var $modal = $('.category_modal');
    $('#brand_ids').select2({
      placeholder: 'Select brands...',
      allowClear: true,
      width: '100%',
      dropdownParent: $modal.length ? $modal : $('body')
    });

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
          response.forEach(function(category) {
            $('.select_category_id').append('<option value="' + category.id + '">' + category.name + '</option>');
          });
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
          response.forEach(function(brand) {
            $('#brand_ids').append('<option value="' + brand.id + '">' + brand.name + '</option>');
          });
          $('#brand_ids').trigger('change');
        },
        error: function(xhr, status, error) {
          console.log('Error loading brands:', xhr.responseText);
        }
      });
    });
  });
</script>

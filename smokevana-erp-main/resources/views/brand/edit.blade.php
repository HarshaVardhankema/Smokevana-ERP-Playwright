<div class="modal-dialog amazon-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    @include('layouts.partials.amazon_form_styles')

    {!! Form::open(['url' => action([\App\Http\Controllers\BrandController::class, 'update'], [$brand->id]), 'method' => 'POST', 'id' => 'brand_edit_form', 'files' => true]) !!}
    {!! Form::hidden('_method', 'PUT') !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('brand.edit_brand')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <!-- Card: Brand Media -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-image"></i> Brand Media</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('brand_logo','Upload logo' . ':') !!}
                  {!! Form::file('brand_logo', ['accept' => 'image/*', 'class' => 'form-control amazon-file-input', 'id' => 'brand_logo_input']) !!}
                  <div class="amazon-file-preview" id="brand_logo_preview">
                    @if(!empty($brand->logo))
                      <img src="{{ url('uploads/img/' . $brand->logo) }}" alt="Current logo" style="max-width: 80px; max-height: 60px; margin-top: 8px; border-radius: 4px; border: 1px solid #D5D9D9; padding: 4px; background: #ffffff;">
                    @endif
                  </div>
                  <p class="help-block"><i>@lang('business.logo_help')</i> Max 2MB. Previous logo (if exists) will be replaced.</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('brand_banner', 'Upload Banner' . ':') !!}
                  {!! Form::file('brand_banner', ['accept' => 'image/*', 'class' => 'form-control amazon-file-input', 'id' => 'brand_banner_input']) !!}
                  <div class="amazon-file-preview" id="brand_banner_preview">
                    @if(!empty($brand->banner))
                      <img src="{{ url('uploads/img/' . $brand->banner) }}" alt="Current banner" style="max-width: 80px; max-height: 60px; margin-top: 8px; border-radius: 4px; border: 1px solid #D5D9D9; padding: 4px; background: #ffffff;">
                    @endif
                  </div>
                  <p class="help-block"><i>@lang('business.logo_help')</i> Max 2MB. Previous banner (if exists) will be replaced.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Brand Information -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-info-circle"></i> Brand Information</h5>
            @if(!empty($is_super_admin))
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-map-marker"></i>
                    </span>
                    {!! Form::select('location_id', $business_locations, $brand->location_id, ['class' => 'form-control select2 select_location_id', 'placeholder' => __('messages.please_select'), 'required']); !!}
                  </div>
                </div>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('name', __('brand.brand_name') . ':*') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-tag"></i>
                    </span>
                    {!! Form::text('name', $brand->name, ['class' => 'form-control', 'required', 'placeholder' => 'Enter brand name']); !!}
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
                    {!! Form::text('slug', $brand->slug, ['class' => 'form-control', 'placeholder' => 'Leave empty to auto-generate']); !!}
                  </div>
                  <p class="help-block">Leave empty to auto-generate from name</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('description', __('brand.short_description') . ':') !!}
                  {!! Form::textarea('description', $brand->description, ['class' => 'form-control', 'placeholder' => 'Enter a short brand description...', 'rows' => 3]); !!}
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  {!! Form::label('brand_url', 'Brand URL' . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-globe"></i>
                    </span>
                    {!! Form::url('brand_url', $brand->brand_url, ['class' => 'form-control', 'placeholder' => 'https://example.com']); !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Classification -->
        <div class="col-md-12">
          <div class="amazon-form-card">
            <h5 class="amazon-form-card-title"><i class="fa fa-tags"></i> Classification</h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('category', __('product.category') . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-folder"></i>
                    </span>
                    {!! Form::select('category', $brandCategory ?? [], $brand->category, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2 select_category_id']); !!}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label('visibility', 'Visibility' . ':') !!}
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-eye"></i>
                    </span>
                    {!! Form::select('visibility', [
                        'public' => __('public'),
                        'coming soon' => __('coming soon'),
                        'protected' => __('protected')
                    ], old('visibility', $brand->visibility), ['class' => 'form-control']); !!}
                  </div>
                </div>
              </div>
            </div>
            @if($is_repair_installed)
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      {!! Form::checkbox('use_for_repair', 1, $brand->use_for_repair, ['class' => 'input-icheck']) !!}
                      {{ __( 'repair::lang.use_for_repair' ) }}
                    </label>
                  </div>
                  @show_tooltip(__('repair::lang.use_for_repair_help_text'))
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white"><i class="fas fa-check"></i> Update Brand</button>
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
  $('#brand_logo_input, #brand_banner_input').on('change', function() {
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

  $('.select_location_id').change(function() {
    $.ajax({
      url: '/get-categories-for-location/' + $(this).val(),
      type: 'GET',
      data: { is_perentcategory: true },
      success: function(response) {
        $('.select_category_id').empty().append('<option value="">Select Category</option>');
        if (response && response.length) {
          response.forEach(function(category) {
            $('.select_category_id').append('<option value="' + category.id + '">' + category.name + '</option>');
          });
        }
      },
      error: function(xhr, status, error) { console.log(xhr.responseText); }
    });
  });
});
</script>

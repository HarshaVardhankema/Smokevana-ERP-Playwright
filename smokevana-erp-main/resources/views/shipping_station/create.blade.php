<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\ShippingStationController::class, 'store']), 'method' => 'post', 'id' => 'shipping_station_add_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('Add Shipping Station')</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __('Station Name') . ':*') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('Station Name')]); !!}
      </div>

      @if(!empty($users))
      <div class="form-group">
        {!! Form::label('user_id', __('Assigned User') . ':') !!}
        {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
        <p class="help-block"><i>@lang('Select a registered user to assign to this shipping station')</i></p>
      </div>
      @endif

      {{-- @if(!empty($business_locations))
      <div class="form-group">
        {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
      </div>
      @endif --}}

      <div class="form-group">
        {!! Form::label('station_code', __('Station Code') . ':') !!}
        {!! Form::text('station_code', null, ['class' => 'form-control', 'placeholder' => __('e.g., SS-001')]); !!}
        <p class="help-block"><i>@lang('Optional unique identifier for the station')</i></p>
      </div>

      <div class="form-group">
        {!! Form::label('description', __('Description') . ':') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Describe the shipping station...')]); !!}
      </div>

      {{-- <div class="form-group">
        {!! Form::label('printer_name', __('Label Printer Name') . ':') !!}
        {!! Form::text('printer_name', null, ['class' => 'form-control', 'placeholder' => __('e.g., Zebra ZP450')]); !!}
        <p class="help-block"><i>@lang('Name or identifier of the label printer at this station')</i></p>
      </div> --}}

      <div class="form-group">
        {!! Form::label('equipment_notes', __('Equipment Notes') . ':') !!}
        {!! Form::textarea('equipment_notes', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Notes about scales, packing materials, etc.')]); !!}
        <p class="help-block"><i>@lang('Additional notes about equipment and supplies at this station')</i></p>
      </div>

      <div class="form-group">
        <label>
          {!! Form::checkbox('is_active', 1, true, ['class' => 'input-icheck']) !!}
          @lang('Active')
        </label>
        <p class="help-block"><i>@lang('Uncheck to deactivate this shipping station')</i></p>
      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
  $(document).ready(function() {
    // Initialize Select2 for user dropdown if it exists
    if ($('#shipping_station_add_form select[name="user_id"]').length) {
      $('#shipping_station_add_form select[name="user_id"]').select2({
        dropdownParent: $('#shipping_station_add_form').closest('.modal'),
        width: '100%'
      });
    }

    $('#shipping_station_add_form').on('submit', function(e) {
      e.preventDefault();
      var form = $(this);
      
      // Ensure Select2 values are included in form data
      var formData = form.serializeArray();
      var userSelect = form.find('select[name="user_id"]');
      if (userSelect.length && userSelect.val()) {
        // Remove existing user_id if present and add the correct one
        formData = formData.filter(function(item) {
          return item.name !== 'user_id';
        });
        formData.push({name: 'user_id', value: userSelect.val()});
      }
      
      $.ajax({
        method: 'POST',
        url: form.attr('action'),
        data: $.param(formData),
        dataType: 'json',
        success: function(result) {
          if (result.success) {
            $('.shipping_station_modal').modal('hide');
            toastr.success(result.msg);
            shipping_stations_table.ajax.reload();
          } else {
            toastr.error(result.msg);
          }
        },
        error: function(xhr) {
          var errorMsg = '@lang("messages.something_went_wrong")';
          if (xhr.status == 422) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
              var errors = xhr.responseJSON.errors;
              $.each(errors, function(key, value) {
                toastr.error(value[0]);
              });
              return;
            } else if (xhr.responseJSON && xhr.responseJSON.msg) {
              errorMsg = xhr.responseJSON.msg;
            }
          } else if (xhr.responseJSON && xhr.responseJSON.msg) {
            errorMsg = xhr.responseJSON.msg;
          } else if (xhr.status === 403) {
            errorMsg = 'Unauthorized action.';
          }
          toastr.error(errorMsg);
        }
      });
    });
  });
</script>


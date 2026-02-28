<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('Shipping Station Details')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <table class="table table-bordered">
            <tr>
              <th style="width: 30%;">@lang('Station Name'):</th>
              <td>{{ $station->name }}</td>
            </tr>
            @if($station->station_code)
            <tr>
              <th>@lang('Station Code'):</th>
              <td>{{ $station->station_code }}</td>
            </tr>
            @endif
            @if($station->businessLocation)
            <tr>
              <th>@lang('Location'):</th>
              <td>{{ $station->businessLocation->name }}</td>
            </tr>
            @endif
            @if($station->printer_name)
            <tr>
              <th>@lang('Label Printer'):</th>
              <td>{{ $station->printer_name }}</td>
            </tr>
            @endif
            @if($station->user)
            <tr>
              <th>@lang('Assigned User'):</th>
              <td>{{ $station->user->first_name }} {{ $station->user->last_name }}</td>
            </tr>
            @endif
            @if($station->description)
            <tr>
              <th>@lang('Description'):</th>
              <td>{{ $station->description }}</td>
            </tr>
            @endif
            @if($station->equipment_notes)
            <tr>
              <th>@lang('Equipment Notes'):</th>
              <td>{{ $station->equipment_notes }}</td>
            </tr>
            @endif
            <tr>
              <th>@lang('Status'):</th>
              <td>
                @if($station->is_active)
                  <span class="label label-success">@lang('lang_v1.active')</span>
                @else
                  <span class="label label-danger">@lang('lang_v1.inactive')</span>
                @endif
              </td>
            </tr>
            @if($station->created_at)
            <tr>
              <th>@lang('Created At'):</th>
              <td>{{ @format_datetime($station->created_at) }}</td>
            </tr>
            @endif
          </table>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<div class="modal-dialog account-form-modal modal-xl" role="document" style="max-width: 1100px; width: 95%;">
  <div class="modal-content">
    <style>
      /* Account Form Modal - Amazon theme matching customer form */
      .account-form-modal { box-sizing: border-box; }
      .account-form-modal .modal-content,
      .account-form-modal .modal-body,
      .account-form-modal .account-create-card .form-control { box-sizing: border-box; }
      .account-form-modal .modal-content { border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.2); }
      .account-form-modal .modal-header {
        background: #37475a;
        color: #fff;
        padding: 1rem 1.25rem;
        border-bottom: none;
        flex-shrink: 0;
      }
      .account-form-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
      .account-form-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; margin-top: -0.25rem; }
      .account-form-modal .modal-body {
        background: #37475a;
        padding: 1rem 1.25rem;
        max-height: min(85vh, 720px);
        overflow-y: auto;
        overflow-x: hidden;
      }
      .account-form-modal .modal-footer {
        background: #37475a;
        border-top: 1px solid rgba(255,255,255,0.15);
        padding: 0.75rem 1.25rem;
        flex-shrink: 0;
      }

      /* Cards - white fields on Amazon background */
      .account-form-modal .account-create-card {
        background: #fff;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      }
      .account-form-modal .account-create-card-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #232F3E;
        margin: 0 0 0.75rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #D5D9D9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .account-form-modal .account-create-card-title i { color: #FF9900; }

      /* Form groups - consistent spacing */
      .account-form-modal .account-create-card .form-group {
        margin-bottom: 0.75rem;
      }
      .account-form-modal .account-create-card .form-group:last-child,
      .account-form-modal .account-create-card .row:last-child .form-group { margin-bottom: 0; }
      .account-form-modal .account-create-card label,
      .account-form-modal .account-create-card .control-label,
      .account-form-modal .account-create-card .help-block,
      .account-form-modal .account-create-card .text-muted {
        color: #0F1111 !important;
        font-size: 0.8125rem;
      }
      .account-form-modal .account-create-card .help-block { margin: 0.25rem 0 0; color: #565959 !important; font-size: 0.75rem; }
      .account-form-modal .account-create-card .form-control {
        background: #fff;
        border: 1px solid #D5D9D9;
        color: #0F1111;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-height: 2rem;
        max-width: 100%;
        box-sizing: border-box;
      }
      .account-form-modal .account-create-card .form-control:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
      }
      .account-form-modal .account-create-card .input-group-addon {
        background: #F7F8F8;
        color: #232F3E;
        border-color: #D5D9D9;
        font-size: 0.8125rem;
        padding: 0.375rem 0.5rem;
        min-width: 2.25rem;
      }
      .account-form-modal .account-create-card .input-group .form-control { border-left-color: #D5D9D9; }
      
      /* Select2 styling */
      .account-form-modal .account-create-card .select2-container--default .select2-selection--single {
        border: 1px solid #D5D9D9 !important;
        border-radius: 0 4px 4px 0 !important;
        height: 2rem;
      }
      .account-form-modal .account-create-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
        padding-left: 0.5rem;
        font-size: 0.8125rem;
      }
      .account-form-modal .account-create-card .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
      }
      .account-form-modal .account-create-card .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #FF9900 !important;
        color: #fff !important;
      }

      /* Table styling */
      .account-form-modal .account-create-card .table {
        margin-top: 0.5rem;
        margin-bottom: 0;
      }
      .account-form-modal .account-create-card .table th {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        color: #fff;
        border: none;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        font-size: 0.8125rem;
        position: relative;
      }
      .account-form-modal .account-create-card .table th::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
      }
      .account-form-modal .account-create-card .table td {
        border-color: #e5e7eb;
        padding: 0.5rem 0.75rem;
      }
      .account-form-modal .account-create-card .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9fafb;
      }

      /* Row gaps inside cards */
      .account-form-modal .account-create-card .row { margin-left: -0.375rem; margin-right: -0.375rem; }
      .account-form-modal .account-create-card .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

      /* Buttons - Amazon orange */
      .account-form-modal .modal-footer .btn-primary,
      .account-form-modal .modal-footer .btn-primary:hover,
      .account-form-modal .modal-footer .tw-dw-btn-primary,
      .account-form-modal .modal-footer .tw-dw-btn-primary:hover {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        font-weight: 500;
        padding: 0.375rem 1rem;
      }
      .account-form-modal .modal-footer .btn-default,
      .account-form-modal .modal-footer .tw-dw-btn-neutral {
        background: transparent !important;
        border: 1px solid rgba(255,255,255,0.6) !important;
        color: #fff !important;
      }
      .account-form-modal .modal-footer .btn-default:hover,
      .account-form-modal .modal-footer .tw-dw-btn-neutral:hover {
        background: rgba(255,255,255,0.1) !important;
        color: #fff !important;
      }

      /* Responsive: stack on narrow */
      @media (max-width: 768px) {
        .account-form-modal .modal-dialog { width: 100% !important; max-width: 100% !important; margin: 0.5rem; }
        .account-form-modal .account-create-card .row > [class*="col-"] { margin-bottom: 0.5rem; }
      }
    </style>

    {!! Form::open(['url' => action([\App\Http\Controllers\AccountController::class, 'store']), 'method' => 'post', 'id' => 'payment_account_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.add_account' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <!-- Card: Account Information -->
            <div class="col-md-12">
                <div class="account-create-card">
                    <h5 class="account-create-card-title"><i class="fa fa-wallet"></i> @lang('lang_v1.account_information')</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-tag"></i>
                                    </span>
                                    {!! Form::text('name', null, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-hashtag"></i>
                                    </span>
                                    {!! Form::text('account_number', null, ['class' => 'form-control', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </span>
                                    <select name="account_type_id" class="form-control select2">
                                        <option>@lang('messages.please_select')</option>
                                        @foreach($account_types as $account_type)
                                            <optgroup label="{{$account_type->name}}">
                                                <option value="{{$account_type->id}}">{{$account_type->name}}</option>
                                                @foreach($account_type->sub_types as $sub_type)
                                                    <option value="{{$sub_type->id}}">{{$sub_type->name}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('opening_balance', __( 'account.opening_balance' ) .":") !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-dollar-sign"></i>
                                    </span>
                                    {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number','placeholder' => __( 'account.opening_balance' ) ]); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Account Details -->
            <div class="col-md-12">
                <div class="account-create-card">
                    <h5 class="account-create-card-title"><i class="fa fa-info-circle"></i> @lang('lang_v1.account_details')</h5>
                    <div class="form-group">
                        <table class="table table-striped">
                            <tr>
                                <th>
                                    @lang('lang_v1.label')
                                </th>
                                <th>
                                    @lang('product.value')
                                </th>
                            </tr>
                            @for ($i = 0; $i < 6; $i++)
                                <tr>
                                    <td>
                                        {!! Form::text('account_details['.$i.'][label]', null, ['class' => 'form-control']); !!}
                                    </td>
                                    <td>
                                        {!! Form::text('account_details['.$i.'][value]', null, ['class' => 'form-control']); !!}      
                                    </td>
                                </tr>
                            @endfor
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card: Additional Information -->
            <div class="col-md-12">
                <div class="account-create-card">
                    <h5 class="account-create-card-title"><i class="fa fa-sticky-note"></i> @lang('brand.note')</h5>
                    <div class="form-group">
                        {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
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

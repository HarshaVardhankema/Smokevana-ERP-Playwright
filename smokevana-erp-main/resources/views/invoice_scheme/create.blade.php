<div class="modal-dialog invoice-scheme-form-modal" role="document">
    <div class="modal-content">
    <style>
        /* === Invoice Scheme Add Modal - Amazon theme === */
        .invoice-scheme-form-modal { box-sizing: border-box; }
        .invoice-scheme-form-modal .modal-content { border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.2); }
        .invoice-scheme-form-modal .modal-header {
            background: #37475a;
            color: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #FF9900;
            flex-shrink: 0;
        }
        .invoice-scheme-form-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
        .invoice-scheme-form-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; margin-top: -0.25rem; }
        .invoice-scheme-form-modal .modal-header .close:hover { color: #FF9900; }
        .invoice-scheme-form-modal .modal-body {
            background: #EAEDED;
            padding: 1rem 1.25rem;
        }
        .invoice-scheme-form-modal .modal-footer {
            background: #37475a;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding: 0.75rem 1.25rem;
        }

        /* Cards - white sections */
        .invoice-scheme-form-modal .invoice-scheme-card {
            background: #fff;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #D5D9D9;
        }
        .invoice-scheme-form-modal .invoice-scheme-card-title {
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
        .invoice-scheme-form-modal .invoice-scheme-card-title i { color: #FF9900; }

        /* Form controls */
        .invoice-scheme-form-modal .invoice-scheme-card .form-group { margin-bottom: 0.75rem; }
        .invoice-scheme-form-modal .invoice-scheme-card .form-group:last-child { margin-bottom: 0; }
        .invoice-scheme-form-modal .invoice-scheme-card label { color: #0F1111 !important; font-size: 0.8125rem; }
        .invoice-scheme-form-modal .invoice-scheme-card .form-control {
            background: #fff;
            border: 1px solid #D5D9D9;
            color: #0F1111;
            font-size: 0.8125rem;
            padding: 0.375rem 0.5rem;
        }
        .invoice-scheme-form-modal .invoice-scheme-card .form-control:focus {
            border-color: #FF9900;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
        }
        .invoice-scheme-form-modal .invoice-scheme-card .input-group-addon {
            background: #F7F8F8;
            color: #232F3E;
            border-color: #D5D9D9;
        }
        .invoice-scheme-form-modal .invoice-scheme-card input[type="checkbox"],
        .invoice-scheme-form-modal .invoice-scheme-card input[type="radio"] { accent-color: #FF9900; }

        /* Format option boxes - Amazon style */
        .invoice-scheme-form-modal .option-div-group .option-div {
            background: #fff !important;
            border: 2px solid #D5D9D9 !important;
            color: #0F1111 !important;
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .invoice-scheme-form-modal .option-div-group .option-div:hover {
            border-color: #FF9900 !important;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
        }
        .invoice-scheme-form-modal .option-div-group .option-div.active,
        .invoice-scheme-form-modal .option-div-group .option-div.active:hover {
            border-color: #FF9900 !important;
            background: #fff8e7 !important;
            box-shadow: 0 0 0 2px rgba(255,153,0,0.3);
        }
        .invoice-scheme-form-modal .option-div-group .icon { color: #FF9900 !important; }
        .invoice-scheme-form-modal .option-div-group .option-div h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
            color: #0F1111;
        }

        /* Preview box */
        .invoice-scheme-form-modal #preview_format {
            font-weight: 600;
            color: #232F3E;
            padding: 0.5rem 0;
        }

        /* Buttons - Amazon orange */
        .invoice-scheme-form-modal .modal-footer .btn-primary,
        .invoice-scheme-form-modal .modal-footer .tw-dw-btn-primary {
            background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
            border-color: #C7511F !important;
            color: #fff !important;
            font-weight: 500;
            padding: 0.375rem 1rem;
        }
        .invoice-scheme-form-modal .modal-footer .btn-default,
        .invoice-scheme-form-modal .modal-footer .tw-dw-btn-neutral {
            background: transparent !important;
            border: 1px solid rgba(255,255,255,0.6) !important;
            color: #fff !important;
        }
        .invoice-scheme-form-modal .modal-footer .btn-default:hover,
        .invoice-scheme-form-modal .modal-footer .tw-dw-btn-neutral:hover {
            background: rgba(255,255,255,0.1) !important;
            color: #fff !important;
        }
    </style>

        {!! Form::open(['url' => action([\App\Http\Controllers\InvoiceSchemeController::class, 'store']), 'method' => 'post', 'id' => 'invoice_scheme_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'invoice.add_invoice' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <!-- Card: Format Selection & Preview -->
                <div class="col-sm-12">
                    <div class="invoice-scheme-card">
                        <h5 class="invoice-scheme-card-title"><i class="fa fa-file-invoice"></i> @lang('invoice.invoice_scheme') - Format</h5>
                        <div class="row option-div-group">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="option-div">
                                        <h4>FORMAT: <br>XXXX <i class="fa fa-check-circle pull-right icon"></i></h4>
                                        {!! Form::radio('scheme_type', 'blank'); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="option-div">
                                        <h4>FORMAT: <br>{{ date('Y') }}{{config('constants.invoice_scheme_separator')}}XXXX <i class="fa fa-check-circle pull-right icon"></i></h4>
                                        {!! Form::radio('scheme_type', 'year'); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>@lang('invoice.preview'):</label>
                                    <div id="preview_format">@lang('invoice.not_selected')</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Basic Details -->
                <div class="col-sm-12">
                    <div class="invoice-scheme-card">
                        <h5 class="invoice-scheme-card-title"><i class="fa fa-info-circle"></i> Basic Details</h5>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'invoice.name' ) . ':*') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'invoice.name' ) ]); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {!! Form::label('invoice_number_type', __( 'invoice.number_type' ) . ':*') !!} @show_tooltip(__('invoice.number_type_tooltip'))
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-sort-numeric-down"></i></span>
                                        {!! Form::select('number_type', $number_types, null, ['class' => 'form-control select2', 'id' => 'invoice_number_type']); !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Format Settings (shown when applicable) -->
                <div id="invoice_format_settings" class="col-sm-12 hide">
                    <div class="invoice-scheme-card">
                        <h5 class="invoice-scheme-card-title"><i class="fa fa-cog"></i> Format Settings</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('prefix', __( 'invoice.prefix' ) . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-info"></i></span>
                                        {!! Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => '']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 sequential_field hide">
                                <div class="form-group">
                                    {!! Form::label('start_number', __( 'invoice.start_number' ) . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                                        {!! Form::number('start_number', 0, ['class' => 'form-control', 'required', 'min' => 0 ]); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('total_digits', __( 'invoice.total_digits' ) . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-list-ol"></i></span>
                                        {!! Form::select('total_digits', ['4' => '4', '5' => '5', '6' => '6', '7' => '7',
                                        '8' => '8', '9'=>'9', '10' => '10'], 4, ['class' => 'form-control', 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" style="margin-top: 1.75rem;">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('is_default', 1); !!} @lang('barcode.set_as_default')
                                        </label>
                                    </div>
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

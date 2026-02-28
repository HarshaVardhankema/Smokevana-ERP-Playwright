@extends('layouts.app')
@section('title', __('lang_v1.activity_log'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('lang_v1.activity_log')}}</h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">

            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('report.filters')</h4>
                        </div>
                        <div class="modal-body" style="padding: 0px; margin-top: 10px;">
                            {{-- @component('components.filters', ['title' => __('report.filters')]) --}}

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('al_users_filter', __( 'lang_v1.by' ) . ':') !!}
                                    {!! Form::select('al_users_filter', $users, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%', 'id' => 'al_users_filter', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('subject_type', __( 'lang_v1.subject_type' ) . ':') !!}
                                    {!! Form::select('subject_type', $transaction_types, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%', 'id' => 'subject_type', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('al_action_filter', __( 'messages.action' ) . ':') !!}
                                    {!! Form::select('al_action_filter', $actions, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%', 'id' => 'al_action_filter', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('al_table_filter', __( 'lang_v1.table' ) . ':') !!}
                                    {!! Form::text('al_table_filter', null, ['class' => 'form-control', 'id' => 'al_table_filter',
                                    'placeholder' => __('lang_v1.table')]); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('al_date_filter', __('report.date_range') . ':') !!}
                                    {!! Form::text('al_date_filter', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                                </div>
                            </div>

                            {{-- @endcomponent --}}
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-primary"
                                    id="applyFiltersBtn">@lang('messages.apply')</button>
                                --}}
                                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                    data-dismiss="modal">@lang('messages.close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div>
                <table class="table   table-bordered table-striped ajax_view hide-footer" id="activity_log_table" style="min-width: max-content;">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.subject_type')</th>
                            <th>@lang('messages.action')</th>
                            <th>@lang('lang_v1.by')</th>
                            <th>@lang('brand.note')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        $('#al_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#al_date_filter').val(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            activity_log_table.ajax.reload();
        });
        $('#al_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#al_date_filter').val('');
            activity_log_table.ajax.reload();
        });

        activity_log_table = $('#activity_log_table').DataTable({
            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:false,
            scrollX:true,
            scrollY: 600,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '{{action([\App\Http\Controllers\ReportController::class, 'activityLog'])}}',
                "data": function ( d ) {
                    var start_date = '';
                    var end_date = '';
                    if ($('#al_date_filter').val()) {
                        d.start_date = $('input#al_date_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('input#al_date_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }

                    d.user_id = $('#al_users_filter').val();
                    d.subject_type = $('#subject_type').val();
                    d.activity_action = $('#al_action_filter').val();
                    d.activity_table = $('#al_table_filter').val();
                }
            },
            columns: [
                { data: 'created_at', name: 'created_at'  },
                { data: 'subject_type', "orderable": false, "searchable": false},
                { data: 'description', name: 'description'},
                { data: 'created_by', name: 'created_by'},
                { data: 'note', name: 'note'}
            ],
            buttons: [
                    {
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function () {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                        customize: function (win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                ],
                
        });  

        $(document).on('change', '#al_users_filter, #subject_type, #al_action_filter', function(){
            activity_log_table.ajax.reload();
        });

        $(document).on('keyup', '#al_table_filter', function(){
            activity_log_table.ajax.reload();
        });
    });
</script>
@endsection
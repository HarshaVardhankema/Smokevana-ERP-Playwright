@extends('layouts.app')

@section('title', __('bookkeeping.import_accounts'))

@section('content')
<section class="content-header">
    <h1>@lang('bookkeeping.import_accounts')
        <small>@lang('bookkeeping.bulk_account_creation')</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-file-import"></i> @lang('bookkeeping.import_accounts')</h3>
                    <div class="box-tools">
                        <a href="{{ route('bookkeeping.accounts.export') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-export"></i> @lang('bookkeeping.export_accounts')
                        </a>
                        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> @lang('messages.back')
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <!-- Instructions -->
                    <div class="callout callout-info">
                        <h4><i class="fas fa-info-circle"></i> @lang('bookkeeping.import_instructions')</h4>
                        <ol>
                            <li>@lang('bookkeeping.download_template_first')</li>
                            <li>@lang('bookkeeping.fill_in_account_details')</li>
                            <li>@lang('bookkeeping.upload_completed_file')</li>
                            <li>@lang('bookkeeping.review_preview_and_confirm')</li>
                        </ol>
                    </div>

                    <div class="row">
                        <!-- Step 1: Download Template -->
                        <div class="col-md-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <span class="badge bg-white text-primary">1</span>
                                        @lang('bookkeeping.download_template')
                                    </h4>
                                </div>
                                <div class="panel-body text-center">
                                    <i class="fas fa-file-csv fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">@lang('bookkeeping.template_description')</p>
                                    <a href="{{ route('bookkeeping.accounts.import.template') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download"></i> @lang('bookkeeping.download_csv_template')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Upload File -->
                        <div class="col-md-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <span class="badge bg-white text-primary">2</span>
                                        @lang('bookkeeping.upload_file')
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <form id="uploadForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label>@lang('bookkeeping.select_file') (CSV, XLS, XLSX)</label>
                                            <input type="file" 
                                                   class="form-control" 
                                                   id="import_file" 
                                                   name="file" 
                                                   accept=".csv,.xls,.xlsx"
                                                   required>
                                            <p class="help-block">@lang('bookkeeping.max_file_size'): 5MB</p>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                                                @lang('bookkeeping.skip_duplicate_accounts')
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" id="update_existing" name="update_existing" value="1">
                                                @lang('bookkeeping.update_existing_accounts')
                                            </label>
                                        </div>
                                        <button type="button" class="btn btn-info btn-block" id="preview_btn">
                                            <i class="fas fa-eye"></i> @lang('bookkeeping.preview_import')
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Confirm Import -->
                        <div class="col-md-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <span class="badge bg-white text-primary">3</span>
                                        @lang('bookkeeping.confirm_import')
                                    </h4>
                                </div>
                                <div class="panel-body text-center">
                                    <i class="fas fa-check-circle fa-4x text-muted mb-3" id="confirm_icon"></i>
                                    <p class="text-muted">@lang('bookkeeping.review_and_confirm')</p>
                                    <button type="button" class="btn btn-success btn-lg" id="import_btn" disabled>
                                        <i class="fas fa-file-import"></i> @lang('bookkeeping.import_accounts')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div id="preview_section" style="display: none;">
                        <hr>
                        <h3><i class="fas fa-eye"></i> @lang('bookkeeping.import_preview')</h3>
                        
                        <!-- Statistics -->
                        <div class="row" id="preview_stats">
                            <div class="col-md-3">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('bookkeeping.total_rows')</span>
                                        <span class="info-box-number" id="stat_total">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('bookkeeping.valid_rows')</span>
                                        <span class="info-box-number" id="stat_valid">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fas fa-copy"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('bookkeeping.duplicate_rows')</span>
                                        <span class="info-box-number" id="stat_duplicates">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fas fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">@lang('bookkeeping.invalid_rows')</span>
                                        <span class="info-box-number" id="stat_invalid">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Tabs -->
                        <ul class="nav nav-tabs" id="previewTabs">
                            <li class="active"><a href="#tab_all" data-toggle="tab">@lang('lang_v1.all')</a></li>
                            <li><a href="#tab_valid" data-toggle="tab" class="text-success">@lang('bookkeeping.valid')</a></li>
                            <li><a href="#tab_duplicates" data-toggle="tab" class="text-warning">@lang('bookkeeping.duplicates')</a></li>
                            <li><a href="#tab_invalid" data-toggle="tab" class="text-danger">@lang('bookkeeping.invalid')</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_all">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped table-hover" id="all_preview_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('bookkeeping.account_code')</th>
                                                <th>@lang('bookkeeping.account_name')</th>
                                                <th>@lang('bookkeeping.account_type')</th>
                                                <th>@lang('bookkeeping.detail_type')</th>
                                                <th>@lang('bookkeeping.opening_balance')</th>
                                                <th>@lang('messages.status')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_valid">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped" id="valid_preview_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('bookkeeping.account_code')</th>
                                                <th>@lang('bookkeeping.account_name')</th>
                                                <th>@lang('bookkeeping.account_type')</th>
                                                <th>@lang('bookkeeping.detail_type')</th>
                                                <th>@lang('bookkeeping.opening_balance')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_duplicates">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped" id="duplicates_preview_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('bookkeeping.account_code')</th>
                                                <th>@lang('bookkeeping.account_name')</th>
                                                <th>@lang('bookkeeping.account_type')</th>
                                                <th>@lang('bookkeeping.reason')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_invalid">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-bordered table-striped" id="invalid_preview_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>@lang('bookkeeping.account_code')</th>
                                                <th>@lang('bookkeeping.account_name')</th>
                                                <th>@lang('bookkeeping.errors')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reference: Valid Account Types -->
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-book"></i> @lang('bookkeeping.valid_account_types_reference')</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        @foreach($accountTypes as $type => $label)
                        <div class="col-md-4">
                            <h4 class="text-primary">{{ $label }}</h4>
                            <p><code>{{ $type }}</code></p>
                            @if(isset($detailTypes[$type]))
                            <ul class="list-unstyled">
                                @foreach($detailTypes[$type] as $detailKey => $detailLabel)
                                <li><code>{{ $detailKey }}</code> - {{ $detailLabel }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var previewData = null;

    // Preview button
    $('#preview_btn').on('click', function() {
        var formData = new FormData($('#uploadForm')[0]);
        
        if (!$('#import_file').val()) {
            toastr.error('@lang("bookkeeping.please_select_file")');
            return;
        }

        $.ajax({
            url: "{{ route('bookkeeping.accounts.import.preview') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#preview_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> @lang("messages.loading")...');
            },
            success: function(response) {
                if (response.success) {
                    previewData = response.preview;
                    displayPreview(response.preview);
                    $('#preview_section').show();
                    $('#import_btn').prop('disabled', response.preview.valid_rows === 0);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || '@lang("bookkeeping.error_parsing_file")');
            },
            complete: function() {
                $('#preview_btn').prop('disabled', false).html('<i class="fas fa-eye"></i> @lang("bookkeeping.preview_import")');
            }
        });
    });

    // Import button
    $('#import_btn').on('click', function() {
        if (!previewData || previewData.valid_rows === 0) {
            toastr.error('@lang("bookkeeping.no_valid_rows_to_import")');
            return;
        }

        swal({
            title: '@lang("bookkeeping.confirm_import")',
            text: '@lang("bookkeeping.import_confirmation_message")',
            icon: 'warning',
            buttons: ['@lang("messages.cancel")', '@lang("bookkeeping.yes_import")'],
            dangerMode: true
        }).then(function(confirmed) {
            if (confirmed) {
                executeImport();
            }
        });
    });

    function displayPreview(preview) {
        // Update statistics
        $('#stat_total').text(preview.total_rows);
        $('#stat_valid').text(preview.valid_rows);
        $('#stat_duplicates').text(preview.duplicate_rows);
        $('#stat_invalid').text(preview.invalid_rows);

        // Clear tables
        $('#all_preview_table tbody, #valid_preview_table tbody, #duplicates_preview_table tbody, #invalid_preview_table tbody').empty();

        // Populate all data table
        preview.data.forEach(function(row, index) {
            var status = getRowStatus(index, preview.validation);
            var statusBadge = getStatusBadge(status);
            
            $('#all_preview_table tbody').append(
                '<tr class="' + status.class + '">' +
                '<td>' + (index + 2) + '</td>' +
                '<td>' + (row.account_code || '-') + '</td>' +
                '<td>' + (row.name || '-') + '</td>' +
                '<td>' + (row.account_type || '-') + '</td>' +
                '<td>' + (row.detail_type || '-') + '</td>' +
                '<td class="text-right">' + formatCurrency(row.opening_balance || 0) + '</td>' +
                '<td>' + statusBadge + '</td>' +
                '</tr>'
            );
        });

        // Valid rows
        preview.validation.valid.forEach(function(item) {
            $('#valid_preview_table tbody').append(
                '<tr>' +
                '<td>' + item.row + '</td>' +
                '<td>' + (item.data.account_code || '-') + '</td>' +
                '<td>' + item.data.name + '</td>' +
                '<td>' + item.data.account_type + '</td>' +
                '<td>' + (item.data.detail_type || '-') + '</td>' +
                '<td class="text-right">' + formatCurrency(item.data.opening_balance || 0) + '</td>' +
                '</tr>'
            );
        });

        // Duplicate rows
        preview.validation.duplicates.forEach(function(item) {
            $('#duplicates_preview_table tbody').append(
                '<tr class="warning">' +
                '<td>' + item.row + '</td>' +
                '<td>' + (item.data.account_code || '-') + '</td>' +
                '<td>' + item.data.name + '</td>' +
                '<td>' + (item.data.account_type || '-') + '</td>' +
                '<td>' + item.reason + '</td>' +
                '</tr>'
            );
        });

        // Invalid rows
        preview.validation.invalid.forEach(function(item) {
            $('#invalid_preview_table tbody').append(
                '<tr class="danger">' +
                '<td>' + item.row + '</td>' +
                '<td>' + (item.data.account_code || '-') + '</td>' +
                '<td>' + (item.data.name || '-') + '</td>' +
                '<td><ul class="list-unstyled text-danger">' + 
                    item.errors.map(function(e) { return '<li><i class="fas fa-times"></i> ' + e + '</li>'; }).join('') + 
                '</ul></td>' +
                '</tr>'
            );
        });
    }

    function getRowStatus(index, validation) {
        var rowNum = index + 2;
        
        for (var i = 0; i < validation.invalid.length; i++) {
            if (validation.invalid[i].row === rowNum) {
                return { status: 'invalid', class: 'danger' };
            }
        }
        
        for (var i = 0; i < validation.duplicates.length; i++) {
            if (validation.duplicates[i].row === rowNum) {
                return { status: 'duplicate', class: 'warning' };
            }
        }
        
        return { status: 'valid', class: 'success' };
    }

    function getStatusBadge(status) {
        switch (status.status) {
            case 'valid':
                return '<span class="label label-success"><i class="fas fa-check"></i> @lang("bookkeeping.valid")</span>';
            case 'duplicate':
                return '<span class="label label-warning"><i class="fas fa-copy"></i> @lang("bookkeeping.duplicate")</span>';
            case 'invalid':
                return '<span class="label label-danger"><i class="fas fa-times"></i> @lang("bookkeeping.invalid")</span>';
            default:
                return '';
        }
    }

    function executeImport() {
        var formData = new FormData($('#uploadForm')[0]);
        formData.append('skip_duplicates', $('#skip_duplicates').is(':checked') ? 1 : 0);
        formData.append('update_existing', $('#update_existing').is(':checked') ? 1 : 0);

        $.ajax({
            url: "{{ route('bookkeeping.accounts.import.execute') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#import_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> @lang("bookkeeping.importing")...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    showImportResults(response.results);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || '@lang("bookkeeping.error_importing")');
            },
            complete: function() {
                $('#import_btn').prop('disabled', false).html('<i class="fas fa-file-import"></i> @lang("bookkeeping.import_accounts")');
            }
        });
    }

    function showImportResults(results) {
        swal({
            title: '@lang("bookkeeping.import_complete")',
            text: '@lang("bookkeeping.created"): ' + results.created + ', @lang("bookkeeping.skipped"): ' + results.skipped + ', @lang("bookkeeping.failed"): ' + results.failed,
            icon: 'success',
            buttons: {
                ok: '@lang("messages.ok")',
                view: '@lang("bookkeeping.view_accounts")'
            }
        }).then(function(value) {
            if (value === 'view') {
                window.location.href = "{{ route('bookkeeping.accounts.index') }}";
            }
        });
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount || 0);
    }
});
</script>
@endsection





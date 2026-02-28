@extends('layouts.app')

@section('title', __('bookkeeping.merge_accounts'))

@section('content')
<section class="content-header">
    <h1>@lang('bookkeeping.merge_accounts')
        <small>@lang('bookkeeping.combine_accounts_into_one')</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-compress-arrows-alt"></i> @lang('bookkeeping.merge_accounts')</h3>
                    <div class="box-tools">
                        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> @lang('messages.back')
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="callout callout-warning">
                        <h4><i class="fas fa-exclamation-triangle"></i> @lang('bookkeeping.important_notice')</h4>
                        <ul>
                            <li>@lang('bookkeeping.merge_warning_1')</li>
                            <li>@lang('bookkeeping.merge_warning_2')</li>
                            <li>@lang('bookkeeping.merge_warning_3')</li>
                        </ul>
                    </div>

                    <div class="row">
                        <!-- Source Accounts Selection -->
                        <div class="col-md-5">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fas fa-list"></i> @lang('bookkeeping.source_accounts')
                                        <small>(@lang('bookkeeping.accounts_to_merge'))</small>
                                    </h4>
                                </div>
                                <div class="panel-body" style="max-height: 500px; overflow-y: auto;">
                                    @foreach($accountTypes as $type => $typeLabel)
                                        @if(isset($accounts[$type]) && $accounts[$type]->count() > 0)
                                            <div class="account-type-group">
                                                <h5 class="text-primary">
                                                    <strong>{{ $typeLabel }}</strong>
                                                </h5>
                                                @foreach($accounts[$type] as $account)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" 
                                                                   class="source-account-checkbox" 
                                                                   name="source_accounts[]" 
                                                                   value="{{ $account->id }}"
                                                                   data-type="{{ $type }}"
                                                                   data-code="{{ $account->account_code }}"
                                                                   data-name="{{ $account->name }}"
                                                                   data-balance="{{ $account->current_balance }}">
                                                            <span class="text-muted">{{ $account->account_code }}</span> - 
                                                            {{ $account->name }}
                                                            <span class="label label-default pull-right">
                                                                @format_currency($account->current_balance)
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <hr>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Merge Arrow -->
                        <div class="col-md-2 text-center" style="padding-top: 200px;">
                            <div class="merge-arrow">
                                <i class="fas fa-arrow-right fa-3x text-primary"></i>
                                <p class="text-muted mt-2">@lang('bookkeeping.will_merge_into')</p>
                            </div>
                        </div>

                        <!-- Target Account Selection -->
                        <div class="col-md-5">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fas fa-bullseye"></i> @lang('bookkeeping.target_account')
                                        <small>(@lang('bookkeeping.destination_account'))</small>
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>@lang('bookkeeping.filter_by_type')</label>
                                        <select class="form-control" id="target_account_type_filter">
                                            <option value="">@lang('lang_v1.all')</option>
                                            @foreach($accountTypes as $type => $typeLabel)
                                                <option value="{{ $type }}">{{ $typeLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('bookkeeping.select_target_account') <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="target_account_id" name="target_account_id" required style="width: 100%;">
                                            <option value="">@lang('messages.please_select')</option>
                                            @foreach($accountTypes as $type => $typeLabel)
                                                @if(isset($accounts[$type]) && $accounts[$type]->count() > 0)
                                                    <optgroup label="{{ $typeLabel }}" data-type="{{ $type }}">
                                                        @foreach($accounts[$type] as $account)
                                                            <option value="{{ $account->id }}" 
                                                                    data-type="{{ $type }}"
                                                                    data-balance="{{ $account->current_balance }}">
                                                                {{ $account->account_code }} - {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="target_account_info" class="well" style="display: none;">
                                        <h5><i class="fas fa-info-circle"></i> @lang('bookkeeping.target_account_details')</h5>
                                        <table class="table table-condensed">
                                            <tr>
                                                <td>@lang('bookkeeping.current_balance'):</td>
                                                <td id="target_current_balance" class="text-right"><strong>$0.00</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Merge Preview Section -->
                    <div id="merge_preview_section" class="row" style="display: none;">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fas fa-eye"></i> @lang('bookkeeping.merge_preview')
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div id="merge_preview_content">
                                        <!-- Preview content will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <button type="button" class="btn btn-info btn-lg" id="preview_merge_btn" disabled>
                                <i class="fas fa-eye"></i> @lang('bookkeeping.preview_merge')
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" id="execute_merge_btn" style="display: none;">
                                <i class="fas fa-compress-arrows-alt"></i> @lang('bookkeeping.execute_merge')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Confirmation Modal -->
<div class="modal fade" id="mergeConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> @lang('bookkeeping.confirm_merge')
                </h4>
            </div>
            <div class="modal-body">
                <div class="callout callout-danger">
                    <h4>@lang('bookkeeping.warning_irreversible')</h4>
                    <p>@lang('bookkeeping.merge_confirmation_message')</p>
                </div>
                <div id="merge_summary">
                    <!-- Summary will be loaded here -->
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="confirm_merge_checkbox" name="confirm" value="1">
                        @lang('bookkeeping.i_understand_and_confirm')
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times"></i> @lang('messages.cancel')
                </button>
                <button type="button" class="btn btn-danger" id="final_merge_btn" disabled>
                    <i class="fas fa-compress-arrows-alt"></i> @lang('bookkeeping.merge_accounts')
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var selectedSourceAccounts = [];
    var selectedTargetAccount = null;
    var mergePreviewData = null;

    // Initialize Select2
    $('#target_account_id').select2({
        placeholder: "@lang('messages.please_select')",
        allowClear: true
    });

    // Source account selection
    $('.source-account-checkbox').on('change', function() {
        updateSelectedSources();
        validateMergeSelection();
    });

    // Target account type filter
    $('#target_account_type_filter').on('change', function() {
        var selectedType = $(this).val();
        $('#target_account_id optgroup').each(function() {
            var groupType = $(this).data('type');
            if (selectedType === '' || groupType === selectedType) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#target_account_id').val('').trigger('change');
    });

    // Target account selection
    $('#target_account_id').on('change', function() {
        var $selected = $(this).find('option:selected');
        if ($selected.val()) {
            selectedTargetAccount = {
                id: $selected.val(),
                type: $selected.data('type'),
                balance: parseFloat($selected.data('balance')) || 0
            };
            $('#target_account_info').show();
            $('#target_current_balance strong').text(formatCurrency(selectedTargetAccount.balance));
        } else {
            selectedTargetAccount = null;
            $('#target_account_info').hide();
        }
        validateMergeSelection();
    });

    // Preview merge button
    $('#preview_merge_btn').on('click', function() {
        var sourceIds = selectedSourceAccounts.map(function(acc) { return acc.id; });
        
        $.ajax({
            url: "{{ route('bookkeeping.accounts.merge.preview') }}",
            type: 'POST',
            data: {
                source_account_ids: sourceIds,
                target_account_id: selectedTargetAccount.id,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#preview_merge_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            },
            success: function(response) {
                if (response.success) {
                    mergePreviewData = response.preview;
                    displayMergePreview(response.preview);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error generating preview');
            },
            complete: function() {
                $('#preview_merge_btn').prop('disabled', false).html('<i class="fas fa-eye"></i> @lang("bookkeeping.preview_merge")');
            }
        });
    });

    // Execute merge button
    $('#execute_merge_btn').on('click', function() {
        displayMergeSummary();
        $('#mergeConfirmModal').modal('show');
    });

    // Confirm checkbox
    $('#confirm_merge_checkbox').on('change', function() {
        $('#final_merge_btn').prop('disabled', !$(this).is(':checked'));
    });

    // Final merge button
    $('#final_merge_btn').on('click', function() {
        var sourceIds = selectedSourceAccounts.map(function(acc) { return acc.id; });
        
        $.ajax({
            url: "{{ route('bookkeeping.accounts.merge.execute') }}",
            type: 'POST',
            data: {
                source_account_ids: sourceIds,
                target_account_id: selectedTargetAccount.id,
                confirm: 1,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#final_merge_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Merging...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#mergeConfirmModal').modal('hide');
                    setTimeout(function() {
                        window.location.href = "{{ route('bookkeeping.accounts.index') }}";
                    }, 1500);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error merging accounts');
            },
            complete: function() {
                $('#final_merge_btn').prop('disabled', false).html('<i class="fas fa-compress-arrows-alt"></i> @lang("bookkeeping.merge_accounts")');
            }
        });
    });

    function updateSelectedSources() {
        selectedSourceAccounts = [];
        $('.source-account-checkbox:checked').each(function() {
            selectedSourceAccounts.push({
                id: $(this).val(),
                type: $(this).data('type'),
                code: $(this).data('code'),
                name: $(this).data('name'),
                balance: parseFloat($(this).data('balance')) || 0
            });
        });
    }

    function validateMergeSelection() {
        var isValid = selectedSourceAccounts.length > 0 && selectedTargetAccount;
        
        if (isValid) {
            // Check if all source accounts are same type as target
            var allSameType = selectedSourceAccounts.every(function(acc) {
                return acc.type === selectedTargetAccount.type;
            });
            
            // Check if target is not in source list
            var targetNotInSource = !selectedSourceAccounts.some(function(acc) {
                return acc.id === selectedTargetAccount.id;
            });
            
            isValid = allSameType && targetNotInSource;
            
            if (!allSameType) {
                toastr.warning('@lang("bookkeeping.accounts_must_be_same_type")');
            }
            if (!targetNotInSource) {
                toastr.warning('@lang("bookkeeping.target_cannot_be_source")');
            }
        }
        
        $('#preview_merge_btn').prop('disabled', !isValid);
        $('#merge_preview_section').hide();
        $('#execute_merge_btn').hide();
    }

    function displayMergePreview(preview) {
        var html = '<div class="row">';
        
        // Summary statistics
        html += '<div class="col-md-4">';
        html += '<div class="info-box bg-aqua">';
        html += '<span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>';
        html += '<div class="info-box-content">';
        html += '<span class="info-box-text">@lang("bookkeeping.transactions_to_transfer")</span>';
        html += '<span class="info-box-number">' + preview.total_transactions + '</span>';
        html += '</div></div></div>';
        
        html += '<div class="col-md-4">';
        html += '<div class="info-box bg-green">';
        html += '<span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>';
        html += '<div class="info-box-content">';
        html += '<span class="info-box-text">@lang("bookkeeping.balance_to_transfer")</span>';
        html += '<span class="info-box-number">' + formatCurrency(preview.total_balance_transfer) + '</span>';
        html += '</div></div></div>';
        
        html += '<div class="col-md-4">';
        html += '<div class="info-box bg-purple">';
        html += '<span class="info-box-icon"><i class="fas fa-calculator"></i></span>';
        html += '<div class="info-box-content">';
        html += '<span class="info-box-text">@lang("bookkeeping.new_target_balance")</span>';
        html += '<span class="info-box-number">' + formatCurrency(preview.new_target_balance) + '</span>';
        html += '</div></div></div>';
        html += '</div>';
        
        // Warnings
        if (preview.warnings.length > 0) {
            html += '<div class="callout callout-danger">';
            html += '<h4><i class="fas fa-exclamation-triangle"></i> @lang("bookkeeping.merge_warnings")</h4>';
            html += '<ul>';
            preview.warnings.forEach(function(warning) {
                html += '<li>' + warning + '</li>';
            });
            html += '</ul></div>';
        }
        
        // Source accounts table
        html += '<h4>@lang("bookkeeping.accounts_to_be_merged"):</h4>';
        html += '<table class="table table-bordered table-striped">';
        html += '<thead><tr><th>@lang("bookkeeping.account_code")</th><th>@lang("bookkeeping.account_name")</th><th>@lang("bookkeeping.transactions")</th><th>@lang("bookkeeping.balance")</th><th>@lang("messages.status")</th></tr></thead>';
        html += '<tbody>';
        preview.source_accounts.forEach(function(acc) {
            var statusBadge = acc.can_merge ? 
                '<span class="label label-success"><i class="fas fa-check"></i> OK</span>' : 
                '<span class="label label-danger"><i class="fas fa-times"></i> ' + acc.merge_reason + '</span>';
            html += '<tr>';
            html += '<td>' + acc.code + '</td>';
            html += '<td>' + acc.name + '</td>';
            html += '<td class="text-center">' + acc.transaction_count + '</td>';
            html += '<td class="text-right">' + formatCurrency(acc.current_balance) + '</td>';
            html += '<td>' + statusBadge + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
        
        $('#merge_preview_content').html(html);
        $('#merge_preview_section').show();
        
        if (preview.can_merge) {
            $('#execute_merge_btn').show();
        } else {
            $('#execute_merge_btn').hide();
        }
    }

    function displayMergeSummary() {
        if (!mergePreviewData) return;
        
        var html = '<table class="table table-bordered">';
        html += '<tr><th>@lang("bookkeeping.accounts_to_merge"):</th><td>' + mergePreviewData.source_accounts.length + '</td></tr>';
        html += '<tr><th>@lang("bookkeeping.target_account"):</th><td>' + mergePreviewData.target_account.code + ' - ' + mergePreviewData.target_account.name + '</td></tr>';
        html += '<tr><th>@lang("bookkeeping.transactions_affected"):</th><td>' + mergePreviewData.total_transactions + '</td></tr>';
        html += '<tr><th>@lang("bookkeeping.total_balance_transfer"):</th><td>' + formatCurrency(mergePreviewData.total_balance_transfer) + '</td></tr>';
        html += '<tr><th>@lang("bookkeeping.new_balance"):</th><td><strong>' + formatCurrency(mergePreviewData.new_target_balance) + '</strong></td></tr>';
        html += '</table>';
        
        $('#merge_summary').html(html);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }
});
</script>
@endsection





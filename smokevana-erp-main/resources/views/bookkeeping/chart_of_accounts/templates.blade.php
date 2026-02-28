@extends('layouts.app')

@section('title', __('bookkeeping.industry_templates'))

@section('content')
<section class="content-header">
    <h1>@lang('bookkeeping.industry_templates')
        <small>@lang('bookkeeping.predefined_chart_of_accounts')</small>
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-th-large"></i> @lang('bookkeeping.select_industry_template')</h3>
                    <div class="box-tools">
                        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> @lang('messages.back')
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    @if($existingAccountsCount > 0)
                    <div class="callout callout-warning">
                        <h4><i class="fas fa-exclamation-triangle"></i> @lang('bookkeeping.existing_accounts_notice')</h4>
                        <p>@lang('bookkeeping.you_have_existing_accounts', ['count' => $existingAccountsCount])</p>
                        <p>@lang('bookkeeping.template_will_add_new_accounts')</p>
                    </div>
                    @endif

                    <div class="row">
                        @foreach($templates as $key => $template)
                        <div class="col-md-3 col-sm-6">
                            <div class="panel panel-default template-card" data-template="{{ $key }}">
                                <div class="panel-body text-center">
                                    <div class="template-icon mb-3">
                                        <i class="fas {{ $template['icon'] }} fa-4x text-primary"></i>
                                    </div>
                                    <h4 class="template-title">{{ $template['name'] }}</h4>
                                    <p class="text-muted template-description">{{ $template['description'] }}</p>
                                    <button type="button" class="btn btn-info btn-block preview-template-btn" data-template="{{ $key }}">
                                        <i class="fas fa-eye"></i> @lang('bookkeeping.preview')
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Preview Section -->
    <div id="template_preview_section" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fas fa-eye"></i> 
                            <span id="preview_template_name">@lang('bookkeeping.template_preview')</span>
                        </h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" id="close_preview_btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Account List by Type -->
                                <div id="template_accounts_list">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="well">
                                    <h4><i class="fas fa-info-circle"></i> @lang('bookkeeping.template_info')</h4>
                                    <table class="table table-condensed">
                                        <tr>
                                            <td>@lang('bookkeeping.total_accounts'):</td>
                                            <td class="text-right"><strong id="preview_total_accounts">0</strong></td>
                                        </tr>
                                    </table>

                                    <hr>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="overwrite_existing" name="overwrite" value="1">
                                            @lang('bookkeeping.overwrite_unused_accounts')
                                        </label>
                                        <p class="help-block text-muted">
                                            <small>@lang('bookkeeping.overwrite_warning')</small>
                                        </p>
                                    </div>

                                    <button type="button" class="btn btn-success btn-lg btn-block" id="apply_template_btn">
                                        <i class="fas fa-check"></i> @lang('bookkeeping.apply_template')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Confirmation Modal -->
<div class="modal fade" id="applyTemplateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fas fa-th-large"></i> @lang('bookkeeping.confirm_apply_template')
                </h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-clipboard-list fa-5x text-primary mb-3"></i>
                    <h4 id="modal_template_name">Template Name</h4>
                    <p class="text-muted">@lang('bookkeeping.template_apply_message')</p>
                </div>
                
                <div class="callout callout-info">
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success"></i> @lang('bookkeeping.new_accounts_will_be_added')</li>
                        <li><i class="fas fa-check text-success"></i> @lang('bookkeeping.existing_accounts_preserved')</li>
                        <li><i class="fas fa-check text-success"></i> @lang('bookkeeping.no_data_will_be_lost')</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" id="confirm_apply_checkbox" name="confirm" value="1">
                        @lang('bookkeeping.i_understand_and_confirm')
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times"></i> @lang('messages.cancel')
                </button>
                <button type="button" class="btn btn-success" id="final_apply_btn" disabled>
                    <i class="fas fa-check"></i> @lang('bookkeeping.apply_template')
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.template-card {
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 280px;
}
.template-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}
.template-card.selected {
    border: 2px solid #3c8dbc;
    box-shadow: 0 4px 15px rgba(60,141,188,0.3);
}
.template-icon {
    padding: 20px 0;
}
.template-title {
    margin: 10px 0;
    font-weight: bold;
}
.template-description {
    font-size: 12px;
    min-height: 40px;
}
.account-type-section {
    margin-bottom: 20px;
}
.account-type-section h5 {
    background: #f5f5f5;
    padding: 8px 12px;
    margin-bottom: 10px;
    border-left: 3px solid #3c8dbc;
}
.account-list-item {
    padding: 5px 12px;
    border-bottom: 1px solid #eee;
}
.account-list-item:last-child {
    border-bottom: none;
}
.account-code {
    color: #999;
    font-family: monospace;
    margin-right: 10px;
}
</style>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var selectedTemplate = null;
    var templatePreviewData = null;

    // Template card click
    $('.template-card').on('click', function() {
        var templateKey = $(this).data('template');
        previewTemplate(templateKey);
    });

    // Preview button click
    $('.preview-template-btn').on('click', function(e) {
        e.stopPropagation();
        var templateKey = $(this).data('template');
        previewTemplate(templateKey);
    });

    // Close preview
    $('#close_preview_btn').on('click', function() {
        $('#template_preview_section').slideUp();
        $('.template-card').removeClass('selected');
        selectedTemplate = null;
    });

    // Apply template button
    $('#apply_template_btn').on('click', function() {
        if (!selectedTemplate || !templatePreviewData) {
            toastr.error('@lang("bookkeeping.please_select_template")');
            return;
        }
        
        $('#modal_template_name').text(templatePreviewData.template.name);
        $('#confirm_apply_checkbox').prop('checked', false);
        $('#final_apply_btn').prop('disabled', true);
        $('#applyTemplateModal').modal('show');
    });

    // Confirm checkbox
    $('#confirm_apply_checkbox').on('change', function() {
        $('#final_apply_btn').prop('disabled', !$(this).is(':checked'));
    });

    // Final apply button
    $('#final_apply_btn').on('click', function() {
        applyTemplate();
    });

    function previewTemplate(templateKey) {
        selectedTemplate = templateKey;
        $('.template-card').removeClass('selected');
        $('[data-template="' + templateKey + '"]').addClass('selected');

        $.ajax({
            url: "{{ url('bookkeeping/accounts/templates') }}/" + templateKey + "/preview",
            type: 'GET',
            beforeSend: function() {
                $('#template_preview_section').show();
                $('#template_accounts_list').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
            },
            success: function(response) {
                if (response.success) {
                    templatePreviewData = response;
                    displayTemplatePreview(response);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || '@lang("bookkeeping.error_loading_template")');
            }
        });
    }

    function displayTemplatePreview(data) {
        $('#preview_template_name').text(data.template.name);
        $('#preview_total_accounts').text(data.total_accounts);

        var html = '';
        
        $.each(data.accounts, function(type, group) {
            html += '<div class="account-type-section">';
            html += '<h5><i class="fas fa-folder"></i> ' + group.label + ' (' + group.accounts.length + ')</h5>';
            html += '<div class="account-list">';
            
            group.accounts.forEach(function(account) {
                html += '<div class="account-list-item">';
                html += '<span class="account-code">' + account.code + '</span>';
                html += '<span class="account-name">' + account.name + '</span>';
                html += '</div>';
            });
            
            html += '</div></div>';
        });

        $('#template_accounts_list').html(html);

        // Smooth scroll to preview
        $('html, body').animate({
            scrollTop: $('#template_preview_section').offset().top - 100
        }, 500);
    }

    function applyTemplate() {
        $.ajax({
            url: "{{ route('bookkeeping.accounts.templates.apply') }}",
            type: 'POST',
            data: {
                template_key: selectedTemplate,
                overwrite: $('#overwrite_existing').is(':checked') ? 1 : 0,
                confirm: 1,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#final_apply_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> @lang("bookkeeping.applying")...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    $('#applyTemplateModal').modal('hide');
                    
                    swal({
                        title: '@lang("bookkeeping.template_applied")',
                        text: response.msg,
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
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || '@lang("bookkeeping.error_applying_template")');
            },
            complete: function() {
                $('#final_apply_btn').prop('disabled', false).html('<i class="fas fa-check"></i> @lang("bookkeeping.apply_template")');
            }
        });
    }
});
</script>
@endsection





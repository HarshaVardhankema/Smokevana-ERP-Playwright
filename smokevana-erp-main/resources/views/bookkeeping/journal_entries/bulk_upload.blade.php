@extends('layouts.app')
@section('title', __('bookkeeping.bulk_journal_upload'))

@section('css')
<style>
/* Professional Purple Theme */
.bk-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 40px;
}

.bk-header-banner {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
}

.bk-header-banner h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.bk-header-banner .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.bk-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(124, 58, 237, 0.08);
    margin-bottom: 24px;
}

.bk-card-header {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 20px 24px;
    border-bottom: 1px solid rgba(124, 58, 237, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bk-card-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #4c1d95;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.bk-card-body {
    padding: 24px;
}

.bk-btn-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bk-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
}

.bk-btn-outline {
    background: #fff;
    color: #7c3aed !important;
    border: 2px solid #7c3aed;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bk-btn-outline:hover {
    background: #7c3aed;
    color: #fff !important;
}

.bk-btn-success {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* Upload Zone */
.upload-zone {
    border: 3px dashed #ddd6fe;
    border-radius: 16px;
    padding: 60px 40px;
    text-align: center;
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-zone:hover,
.upload-zone.dragover {
    border-color: #7c3aed;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
}

.upload-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.upload-icon i {
    font-size: 36px;
    color: #fff;
}

.upload-zone h4 {
    color: #4c1d95;
    font-size: 20px;
    margin-bottom: 8px;
}

.upload-zone p {
    color: #6b7280;
    margin-bottom: 0;
}

/* Instructions */
.instructions-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.instructions-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.instructions-list li:last-child {
    border-bottom: none;
}

.instruction-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.instruction-icon i {
    font-size: 14px;
    color: #7c3aed;
}

/* Preview Table */
.preview-container {
    display: none;
    margin-top: 24px;
}

.preview-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.stat-box {
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}

.stat-box.total {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
}

.stat-box.valid {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.stat-box.invalid {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
}

.stat-box.total .stat-number { color: #7c3aed; }
.stat-box.valid .stat-number { color: #059669; }
.stat-box.invalid .stat-number { color: #dc2626; }

.stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-top: 4px;
}

.bk-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.bk-table thead th {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    color: #6b21a8;
    border-bottom: 2px solid rgba(124, 58, 237, 0.15);
}

.bk-table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
}

.bk-table tbody tr.row-error {
    background: #fef2f2;
}

.bk-table tbody tr.row-valid {
    background: #f0fdf4;
}

.error-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    background: #fee2e2;
    color: #dc2626;
    margin: 2px;
}

.checkbox-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
    margin-top: 20px;
}

.checkbox-option input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #7c3aed;
}
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container-fluid" style="max-width: 1200px; margin: 0 auto; padding: 24px;">
        <!-- Header Banner -->
        <div class="bk-header-banner">
            <div>
                <h1><i class="fas fa-file-upload"></i> {{ __('bookkeeping.bulk_journal_upload') }}</h1>
                <p class="subtitle">{{ __('bookkeeping.bulk_upload_desc') }}</p>
            </div>
            <a href="{{ route('bookkeeping.journal.index') }}" class="bk-btn-outline" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); color: #fff !important;">
                <i class="fas fa-arrow-left"></i> {{ __('bookkeeping.back_to_journal') }}
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Upload Card -->
                <div class="bk-card">
                    <div class="bk-card-header">
                        <h3><i class="fas fa-cloud-upload-alt"></i> {{ __('bookkeeping.upload_file') }}</h3>
                        <a href="{{ route('bookkeeping.journal.bulk.template') }}" class="bk-btn-outline" style="padding: 8px 16px; font-size: 13px;">
                            <i class="fas fa-download"></i> {{ __('bookkeeping.download_template') }}
                        </a>
                    </div>
                    <div class="bk-card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="upload-zone" id="uploadZone">
                                <input type="file" name="file" id="fileInput" accept=".csv,.xlsx,.xls" style="display: none;">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h4>{{ __('bookkeeping.drag_drop_file') }}</h4>
                                <p>{{ __('bookkeeping.supported_formats') }}: CSV, XLSX, XLS (Max 5MB)</p>
                                <p style="margin-top: 12px;">
                                    <span style="color: #7c3aed; font-weight: 600; cursor: pointer;">{{ __('bookkeeping.browse_files') }}</span>
                                </p>
                            </div>
                            
                            <div id="fileInfo" style="display: none; margin-top: 16px; padding: 16px; background: #f0fdf4; border-radius: 12px;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <i class="fas fa-file-excel" style="font-size: 24px; color: #059669;"></i>
                                        <div>
                                            <div id="fileName" style="font-weight: 600; color: #1f2937;"></div>
                                            <div id="fileSize" style="font-size: 12px; color: #6b7280;"></div>
                                        </div>
                                    </div>
                                    <button type="button" id="removeFile" style="background: none; border: none; color: #dc2626; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Preview Container -->
                        <div class="preview-container" id="previewContainer">
                            <div class="preview-stats">
                                <div class="stat-box total">
                                    <div class="stat-number" id="totalRows">0</div>
                                    <div class="stat-label">{{ __('bookkeeping.total_rows') }}</div>
                                </div>
                                <div class="stat-box valid">
                                    <div class="stat-number" id="validRows">0</div>
                                    <div class="stat-label">{{ __('bookkeeping.valid_rows') }}</div>
                                </div>
                                <div class="stat-box invalid">
                                    <div class="stat-number" id="invalidRows">0</div>
                                    <div class="stat-label">{{ __('bookkeeping.invalid_rows') }}</div>
                                </div>
                            </div>

                            <div class="bk-card" style="margin-bottom: 0;">
                                <div class="bk-card-header">
                                    <h3><i class="fas fa-eye"></i> {{ __('bookkeeping.preview_data') }}</h3>
                                </div>
                                <div class="bk-card-body" style="padding: 0; max-height: 400px; overflow-y: auto;">
                                    <table class="bk-table" id="previewTable">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>{{ __('bookkeeping.date') }}</th>
                                                <th>{{ __('bookkeeping.type') }}</th>
                                                <th>{{ __('bookkeeping.memo') }}</th>
                                                <th>{{ __('bookkeeping.debit_account') }}</th>
                                                <th>{{ __('bookkeeping.credit_account') }}</th>
                                                <th>{{ __('bookkeeping.amount') }}</th>
                                                <th>{{ __('bookkeeping.status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="previewBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="checkbox-option">
                                <input type="checkbox" id="autoPost" name="auto_post">
                                <div>
                                    <strong>{{ __('bookkeeping.auto_post_entries') }}</strong>
                                    <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.auto_post_desc') }}</div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                                <button type="button" id="cancelUpload" class="bk-btn-outline">
                                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                                </button>
                                <button type="button" id="processUpload" class="bk-btn-success" disabled>
                                    <i class="fas fa-check"></i> {{ __('bookkeeping.import_entries') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Instructions Card -->
                <div class="bk-card">
                    <div class="bk-card-header">
                        <h3><i class="fas fa-info-circle"></i> {{ __('bookkeeping.instructions') }}</h3>
                    </div>
                    <div class="bk-card-body">
                        <ul class="instructions-list">
                            <li>
                                <div class="instruction-icon"><i class="fas fa-download"></i></div>
                                <div>
                                    <strong>{{ __('bookkeeping.step') }} 1</strong>
                                    <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.download_template_instruction') }}</div>
                                </div>
                            </li>
                            <li>
                                <div class="instruction-icon"><i class="fas fa-edit"></i></div>
                                <div>
                                    <strong>{{ __('bookkeeping.step') }} 2</strong>
                                    <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.fill_data_instruction') }}</div>
                                </div>
                            </li>
                            <li>
                                <div class="instruction-icon"><i class="fas fa-upload"></i></div>
                                <div>
                                    <strong>{{ __('bookkeeping.step') }} 3</strong>
                                    <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.upload_file_instruction') }}</div>
                                </div>
                            </li>
                            <li>
                                <div class="instruction-icon"><i class="fas fa-check"></i></div>
                                <div>
                                    <strong>{{ __('bookkeeping.step') }} 4</strong>
                                    <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.review_import_instruction') }}</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Required Columns Card -->
                <div class="bk-card">
                    <div class="bk-card-header">
                        <h3><i class="fas fa-columns"></i> {{ __('bookkeeping.required_columns') }}</h3>
                    </div>
                    <div class="bk-card-body" style="padding: 16px;">
                        <div style="font-size: 13px;">
                            <div style="padding: 8px 0; border-bottom: 1px solid #f3f4f6;"><strong>entry_date</strong> - YYYY-MM-DD</div>
                            <div style="padding: 8px 0; border-bottom: 1px solid #f3f4f6;"><strong>entry_type</strong> - standard, adjusting, etc.</div>
                            <div style="padding: 8px 0; border-bottom: 1px solid #f3f4f6;"><strong>memo</strong> - Entry description</div>
                            <div style="padding: 8px 0; border-bottom: 1px solid #f3f4f6;"><strong>debit_account_code</strong> - Account code</div>
                            <div style="padding: 8px 0; border-bottom: 1px solid #f3f4f6;"><strong>credit_account_code</strong> - Account code</div>
                            <div style="padding: 8px 0; border-bottom: 1px solid #f3f4f6;"><strong>amount</strong> - Numeric value</div>
                            <div style="padding: 8px 0;"><strong>line_description</strong> - Optional</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var uploadedFile = null;
    var previewData = null;
    
    // Upload zone click
    $('#uploadZone').on('click', function() {
        $('#fileInput').click();
    });
    
    // Drag and drop
    $('#uploadZone').on('dragover dragenter', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave', function() {
        $(this).removeClass('dragover');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#fileInput')[0].files = files;
            handleFile(files[0]);
        }
    });
    
    // File input change
    $('#fileInput').on('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });
    
    // Remove file
    $('#removeFile').on('click', function() {
        resetUpload();
    });
    
    // Cancel upload
    $('#cancelUpload').on('click', function() {
        resetUpload();
    });
    
    function handleFile(file) {
        var allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        var maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!file.name.match(/\.(csv|xlsx|xls)$/i)) {
            toastr.error('{{ __("bookkeeping.invalid_file_type") }}');
            return;
        }
        
        if (file.size > maxSize) {
            toastr.error('{{ __("bookkeeping.file_too_large") }}');
            return;
        }
        
        uploadedFile = file;
        $('#fileName').text(file.name);
        $('#fileSize').text(formatFileSize(file.size));
        $('#uploadZone').hide();
        $('#fileInfo').show();
        
        // Preview file
        previewFile();
    }
    
    function previewFile() {
        var formData = new FormData();
        formData.append('file', uploadedFile);
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: '{{ route("bookkeeping.journal.bulk.preview") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    previewData = response.preview;
                    renderPreview(previewData);
                    $('#previewContainer').show();
                    $('#processUpload').prop('disabled', previewData.valid_rows === 0);
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error previewing file');
            }
        });
    }
    
    function renderPreview(data) {
        $('#totalRows').text(data.total_rows);
        $('#validRows').text(data.valid_rows);
        $('#invalidRows').text(data.invalid_rows);
        
        var tbody = $('#previewBody');
        tbody.empty();
        
        // Show valid rows
        data.validation.valid.forEach(function(item) {
            var row = item.data;
            tbody.append(`
                <tr class="row-valid">
                    <td>${item.row}</td>
                    <td>${row.entry_date || '-'}</td>
                    <td>${row.entry_type || 'standard'}</td>
                    <td>${row.memo || '-'}</td>
                    <td>${row.debit_account_code || '-'}</td>
                    <td>${row.credit_account_code || '-'}</td>
                    <td>${parseFloat(row.amount || 0).toFixed(2)}</td>
                    <td><span style="color: #059669;"><i class="fas fa-check-circle"></i> Valid</span></td>
                </tr>
            `);
        });
        
        // Show invalid rows
        data.validation.invalid.forEach(function(item) {
            var row = item.data;
            var errors = item.errors.map(e => `<span class="error-badge">${e}</span>`).join(' ');
            tbody.append(`
                <tr class="row-error">
                    <td>${item.row}</td>
                    <td>${row.entry_date || '-'}</td>
                    <td>${row.entry_type || 'standard'}</td>
                    <td>${row.memo || '-'}</td>
                    <td>${row.debit_account_code || '-'}</td>
                    <td>${row.credit_account_code || '-'}</td>
                    <td>${parseFloat(row.amount || 0).toFixed(2)}</td>
                    <td>${errors}</td>
                </tr>
            `);
        });
    }
    
    function resetUpload() {
        uploadedFile = null;
        previewData = null;
        $('#fileInput').val('');
        $('#uploadZone').show();
        $('#fileInfo').hide();
        $('#previewContainer').hide();
        $('#autoPost').prop('checked', false);
    }
    
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }
    
    // Process upload
    $('#processUpload').on('click', function() {
        if (!uploadedFile || !previewData || previewData.valid_rows === 0) {
            return;
        }
        
        swal({
            title: '{{ __("bookkeeping.confirm_import") }}',
            text: '{{ __("bookkeeping.import_warning") }}'.replace(':count', previewData.valid_rows),
            icon: 'warning',
            buttons: ['{{ __("messages.cancel") }}', '{{ __("bookkeeping.import") }}'],
        }).then((willImport) => {
            if (willImport) {
                var formData = new FormData();
                formData.append('file', uploadedFile);
                formData.append('auto_post', $('#autoPost').is(':checked') ? 1 : 0);
                formData.append('_token', '{{ csrf_token() }}');
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ __("bookkeeping.importing") }}...');
                
                $.ajax({
                    url: '{{ route("bookkeeping.journal.bulk.process") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            setTimeout(function() {
                                window.location.href = '{{ route("bookkeeping.journal.index") }}';
                            }, 1500);
                        } else {
                            toastr.error(response.msg);
                            $('#processUpload').prop('disabled', false).html('<i class="fas fa-check"></i> {{ __("bookkeeping.import_entries") }}');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.msg || 'Error processing upload');
                        $('#processUpload').prop('disabled', false).html('<i class="fas fa-check"></i> {{ __("bookkeeping.import_entries") }}');
                    }
                });
            }
        });
    });
});
</script>
@endsection




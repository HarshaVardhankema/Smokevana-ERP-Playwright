@extends('layouts.app')
@section('title', __('bookkeeping.edit_template'))

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

.bk-form-group {
    margin-bottom: 20px;
}

.bk-form-label {
    display: block;
    font-weight: 600;
    color: #4c1d95;
    margin-bottom: 8px;
    font-size: 14px;
}

.bk-form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.bk-form-control:focus {
    border-color: #7c3aed;
    outline: none;
    box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
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
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    cursor: pointer;
}

/* Lines Table */
.lines-container {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.lines-header {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 16px 20px;
    display: grid;
    grid-template-columns: 1fr 120px 150px 1fr 60px;
    gap: 12px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    color: #6b21a8;
}

.line-row {
    padding: 16px 20px;
    display: grid;
    grid-template-columns: 1fr 120px 150px 1fr 60px;
    gap: 12px;
    align-items: center;
    border-bottom: 1px solid #f3f4f6;
    background: #fff;
}

.line-row:hover {
    background: #faf5ff;
}

.line-row select,
.line-row input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
}

.line-row select:focus,
.line-row input:focus {
    border-color: #7c3aed;
    outline: none;
}

.type-debit {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
}

.type-credit {
    background: #f0fdf4 !important;
    border-color: #86efac !important;
}

.remove-line-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    background: #fee2e2;
    color: #dc2626;
    cursor: pointer;
    transition: all 0.2s ease;
}

.remove-line-btn:hover {
    background: #dc2626;
    color: #fff;
}

.add-line-container {
    padding: 16px 20px;
    background: #f9fafb;
    border-top: 2px dashed #e5e7eb;
    display: flex;
    justify-content: center;
}
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container-fluid" style="max-width: 1200px; margin: 0 auto; padding: 24px;">
        <!-- Header Banner -->
        <div class="bk-header-banner">
            <div>
                <h1><i class="fas fa-edit"></i> {{ __('bookkeeping.edit_template') }}</h1>
                <p class="subtitle">{{ $template->name }}</p>
            </div>
            <a href="{{ route('bookkeeping.journal.templates.index') }}" class="bk-btn-outline" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); color: #fff !important;">
                <i class="fas fa-arrow-left"></i> {{ __('bookkeeping.back') }}
            </a>
        </div>

        <form id="templateForm">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="fas fa-info-circle"></i> {{ __('bookkeeping.template_info') }}</h3>
                </div>
                <div class="bk-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.template_name') }} *</label>
                                <input type="text" name="name" class="bk-form-control" required value="{{ $template->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.entry_type') }} *</label>
                                <select name="entry_type" class="bk-form-control" required>
                                    @foreach($entryTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $template->entry_type === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.description') }}</label>
                                <textarea name="description" class="bk-form-control" rows="3">{{ $template->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.status') }}</label>
                                <select name="is_active" class="bk-form-control">
                                    <option value="1" {{ $template->is_active ? 'selected' : '' }}>{{ __('bookkeeping.active') }}</option>
                                    <option value="0" {{ !$template->is_active ? 'selected' : '' }}>{{ __('bookkeeping.inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Lines -->
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="fas fa-list-alt"></i> {{ __('bookkeeping.template_lines') }}</h3>
                </div>
                <div class="bk-card-body" style="padding: 0;">
                    <div class="lines-container">
                        <div class="lines-header">
                            <div>{{ __('bookkeeping.account') }}</div>
                            <div>{{ __('bookkeeping.type') }}</div>
                            <div>{{ __('bookkeeping.default_amount') }}</div>
                            <div>{{ __('bookkeeping.description') }}</div>
                            <div></div>
                        </div>
                        
                        <div id="linesBody">
                            @foreach($template->lines as $index => $line)
                            <div class="line-row" data-index="{{ $index }}">
                                <div>
                                    <select name="lines[{{ $index }}][account_id]" class="account-select" required>
                                        <option value="">{{ __('bookkeeping.select_account') }}</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $line->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->account_code }} - {{ $account->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <select name="lines[{{ $index }}][type]" class="type-select {{ $line->type === 'debit' ? 'type-debit' : 'type-credit' }}" required>
                                        <option value="debit" {{ $line->type === 'debit' ? 'selected' : '' }}>{{ __('bookkeeping.debit') }}</option>
                                        <option value="credit" {{ $line->type === 'credit' ? 'selected' : '' }}>{{ __('bookkeeping.credit') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="number" name="lines[{{ $index }}][amount]" step="0.01" min="0" 
                                           value="{{ $line->amount }}" placeholder="0.00" class="amount-input">
                                </div>
                                <div>
                                    <input type="text" name="lines[{{ $index }}][description]" value="{{ $line->description }}"
                                           placeholder="{{ __('bookkeeping.line_description') }}">
                                </div>
                                <div>
                                    <button type="button" class="remove-line-btn" title="{{ __('bookkeeping.remove_line') }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="add-line-container">
                            <button type="button" id="addLineBtn" class="bk-btn-success">
                                <i class="fas fa-plus"></i> {{ __('bookkeeping.add_line') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('bookkeeping.journal.templates.index') }}" class="bk-btn-outline">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="bk-btn-primary">
                    <i class="fas fa-save"></i> {{ __('bookkeeping.update_template') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var lineIndex = {{ $template->lines->count() }};
    
    function updateTypeStyle(select) {
        var $select = $(select);
        $select.removeClass('type-debit type-credit');
        $select.addClass($select.val() === 'debit' ? 'type-debit' : 'type-credit');
    }
    
    $('#addLineBtn').on('click', function() {
        var newLine = `
            <div class="line-row" data-index="${lineIndex}">
                <div>
                    <select name="lines[${lineIndex}][account_id]" class="account-select" required>
                        <option value="">{{ __('bookkeeping.select_account') }}</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="lines[${lineIndex}][type]" class="type-select" required>
                        <option value="debit">{{ __('bookkeeping.debit') }}</option>
                        <option value="credit">{{ __('bookkeeping.credit') }}</option>
                    </select>
                </div>
                <div>
                    <input type="number" name="lines[${lineIndex}][amount]" step="0.01" min="0" placeholder="0.00">
                </div>
                <div>
                    <input type="text" name="lines[${lineIndex}][description]" placeholder="{{ __('bookkeeping.line_description') }}">
                </div>
                <div>
                    <button type="button" class="remove-line-btn"><i class="fas fa-times"></i></button>
                </div>
            </div>
        `;
        $('#linesBody').append(newLine);
        lineIndex++;
    });
    
    $(document).on('click', '.remove-line-btn', function() {
        if ($('.line-row').length > 2) {
            $(this).closest('.line-row').remove();
        } else {
            toastr.warning('{{ __("bookkeeping.min_two_lines") }}');
        }
    });
    
    $(document).on('change', '.type-select', function() {
        updateTypeStyle(this);
    });
    
    $('#templateForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("bookkeeping.journal.templates.update", $template->id) }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    window.location.href = '{{ route("bookkeeping.journal.templates.index") }}';
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error updating template');
            }
        });
    });
});
</script>
@endsection




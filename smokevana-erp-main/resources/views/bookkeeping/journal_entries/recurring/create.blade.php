@extends('layouts.app')
@section('title', __('bookkeeping.create_recurring'))

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

/* Toggle Switch */
.toggle-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 12px;
}

.toggle-switch {
    position: relative;
    width: 50px;
    height: 26px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 26px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #7c3aed;
}

input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

/* Frequency Grid */
.frequency-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.frequency-option {
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.frequency-option:hover {
    border-color: #a78bfa;
}

.frequency-option.selected {
    border-color: #7c3aed;
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}

.frequency-option input {
    display: none;
}

.frequency-option i {
    font-size: 24px;
    color: #7c3aed;
    margin-bottom: 8px;
    display: block;
}

.frequency-option .label {
    font-weight: 600;
    color: #4c1d95;
}

/* Lines Table for manual entry */
.lines-container {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    display: none;
}

.lines-header {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 16px 20px;
    display: grid;
    grid-template-columns: 1fr 120px 150px 60px;
    gap: 12px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    color: #6b21a8;
}

.line-row {
    padding: 16px 20px;
    display: grid;
    grid-template-columns: 1fr 120px 150px 60px;
    gap: 12px;
    align-items: center;
    border-bottom: 1px solid #f3f4f6;
}

.line-row select,
.line-row input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
}

.remove-line-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    background: #fee2e2;
    color: #dc2626;
    cursor: pointer;
}

.add-line-container {
    padding: 16px 20px;
    background: #f9fafb;
    display: flex;
    justify-content: center;
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

@media (max-width: 768px) {
    .frequency-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container-fluid" style="max-width: 1000px; margin: 0 auto; padding: 24px;">
        <!-- Header Banner -->
        <div class="bk-header-banner">
            <div>
                <h1><i class="fas fa-plus-circle"></i> {{ __('bookkeeping.create_recurring') }}</h1>
                <p class="subtitle">{{ __('bookkeeping.create_recurring_desc') }}</p>
            </div>
            <a href="{{ route('bookkeeping.journal.recurring.index') }}" class="bk-btn-outline" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); color: #fff !important;">
                <i class="fas fa-arrow-left"></i> {{ __('bookkeeping.back') }}
            </a>
        </div>

        <form id="recurringForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="fas fa-info-circle"></i> {{ __('bookkeeping.basic_info') }}</h3>
                </div>
                <div class="bk-card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.name') }} *</label>
                                <input type="text" name="name" class="bk-form-control" required 
                                       placeholder="{{ __('bookkeeping.recurring_name_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.fixed_amount') }}</label>
                                <input type="number" name="amount" class="bk-form-control" step="0.01" min="0" 
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="bk-form-group">
                        <label class="bk-form-label">{{ __('bookkeeping.description') }}</label>
                        <textarea name="description" class="bk-form-control" rows="2" 
                                  placeholder="{{ __('bookkeeping.recurring_description_placeholder') }}"></textarea>
                    </div>
                </div>
            </div>

            <!-- Source Selection -->
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="fas fa-file-alt"></i> {{ __('bookkeeping.entry_source') }}</h3>
                </div>
                <div class="bk-card-body">
                    <div class="bk-form-group">
                        <label class="bk-form-label">{{ __('bookkeeping.select_template') }}</label>
                        <select name="template_id" class="bk-form-control" id="templateSelect">
                            <option value="">{{ __('bookkeeping.select_template_optional') }}</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }} ({{ ucfirst($template->entry_type) }})</option>
                            @endforeach
                        </select>
                        <small style="color: #6b7280; margin-top: 4px; display: block;">
                            {{ __('bookkeeping.template_or_manual') }}
                        </small>
                    </div>

                    <!-- Manual Entry Lines (shown if no template selected) -->
                    <div class="lines-container" id="manualLines">
                        <div class="lines-header">
                            <div>{{ __('bookkeeping.account') }}</div>
                            <div>{{ __('bookkeeping.type') }}</div>
                            <div>{{ __('bookkeeping.amount') }}</div>
                            <div></div>
                        </div>
                        <div id="linesBody">
                            <div class="line-row" data-index="0">
                                <div>
                                    <select name="entry_data[lines][0][account_id]" required>
                                        <option value="">{{ __('bookkeeping.select_account') }}</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <select name="entry_data[lines][0][type]" required>
                                        <option value="debit">{{ __('bookkeeping.debit') }}</option>
                                        <option value="credit">{{ __('bookkeeping.credit') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="number" name="entry_data[lines][0][amount]" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div>
                                    <button type="button" class="remove-line-btn"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div class="line-row" data-index="1">
                                <div>
                                    <select name="entry_data[lines][1][account_id]" required>
                                        <option value="">{{ __('bookkeeping.select_account') }}</option>
                                        @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <select name="entry_data[lines][1][type]" required>
                                        <option value="debit">{{ __('bookkeeping.debit') }}</option>
                                        <option value="credit" selected>{{ __('bookkeeping.credit') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="number" name="entry_data[lines][1][amount]" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div>
                                    <button type="button" class="remove-line-btn"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="add-line-container">
                            <button type="button" id="addLineBtn" class="bk-btn-success">
                                <i class="fas fa-plus"></i> {{ __('bookkeeping.add_line') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="fas fa-calendar-alt"></i> {{ __('bookkeeping.schedule') }}</h3>
                </div>
                <div class="bk-card-body">
                    <div class="bk-form-group">
                        <label class="bk-form-label">{{ __('bookkeeping.frequency') }} *</label>
                        <div class="frequency-grid">
                            @foreach($frequencies as $key => $label)
                            <label class="frequency-option">
                                <input type="radio" name="frequency" value="{{ $key }}" {{ $key === 'monthly' ? 'checked' : '' }}>
                                <i class="fas fa-{{ $key === 'daily' ? 'sun' : ($key === 'weekly' ? 'calendar-week' : ($key === 'monthly' ? 'calendar-alt' : ($key === 'annually' ? 'calendar' : 'sync'))) }}"></i>
                                <span class="label">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.start_date') }} *</label>
                                <input type="date" name="start_date" class="bk-form-control" required 
                                       value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.end_date') }}</label>
                                <input type="date" name="end_date" class="bk-form-control">
                                <small style="color: #6b7280;">{{ __('bookkeeping.leave_empty_indefinite') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bk-form-group">
                                <label class="bk-form-label">{{ __('bookkeeping.max_occurrences') }}</label>
                                <input type="number" name="occurrences_limit" class="bk-form-control" min="1" 
                                       placeholder="{{ __('bookkeeping.unlimited') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="bk-card">
                <div class="bk-card-header">
                    <h3><i class="fas fa-cog"></i> {{ __('bookkeeping.options') }}</h3>
                </div>
                <div class="bk-card-body">
                    <div class="toggle-option">
                        <div>
                            <strong>{{ __('bookkeeping.auto_post') }}</strong>
                            <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.auto_post_recurring_desc') }}</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="auto_post" value="1">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-option">
                        <div>
                            <strong>{{ __('bookkeeping.notify_on_creation') }}</strong>
                            <div style="font-size: 13px; color: #6b7280;">{{ __('bookkeeping.notify_on_creation_desc') }}</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="notify_on_creation" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('bookkeeping.journal.recurring.index') }}" class="bk-btn-outline">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="bk-btn-primary">
                    <i class="fas fa-save"></i> {{ __('bookkeeping.create_recurring') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var lineIndex = 2;
    
    // Frequency selection
    $('.frequency-option').on('click', function() {
        $('.frequency-option').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input').prop('checked', true);
    });
    $('.frequency-option input:checked').closest('.frequency-option').addClass('selected');
    
    // Template selection toggle
    $('#templateSelect').on('change', function() {
        if ($(this).val()) {
            $('#manualLines').hide();
            $('#manualLines select, #manualLines input').prop('required', false);
        } else {
            $('#manualLines').show();
            $('#manualLines select[name$="[account_id]"]').prop('required', true);
        }
    });
    
    // Show manual lines initially if no template
    if (!$('#templateSelect').val()) {
        $('#manualLines').show();
    }
    
    // Add line
    $('#addLineBtn').on('click', function() {
        var newLine = `
            <div class="line-row" data-index="${lineIndex}">
                <div>
                    <select name="entry_data[lines][${lineIndex}][account_id]" required>
                        <option value="">{{ __('bookkeeping.select_account') }}</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="entry_data[lines][${lineIndex}][type]" required>
                        <option value="debit">{{ __('bookkeeping.debit') }}</option>
                        <option value="credit">{{ __('bookkeeping.credit') }}</option>
                    </select>
                </div>
                <div>
                    <input type="number" name="entry_data[lines][${lineIndex}][amount]" step="0.01" min="0" placeholder="0.00">
                </div>
                <div>
                    <button type="button" class="remove-line-btn"><i class="fas fa-times"></i></button>
                </div>
            </div>
        `;
        $('#linesBody').append(newLine);
        lineIndex++;
    });
    
    // Remove line
    $(document).on('click', '.remove-line-btn', function() {
        if ($('.line-row').length > 2) {
            $(this).closest('.line-row').remove();
        } else {
            toastr.warning('{{ __("bookkeeping.min_two_lines") }}');
        }
    });
    
    // Form submission
    $('#recurringForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("bookkeeping.journal.recurring.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    window.location.href = '{{ route("bookkeeping.journal.recurring.index") }}';
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.msg || 'Error creating recurring entry');
            }
        });
    });
});
</script>
@endsection




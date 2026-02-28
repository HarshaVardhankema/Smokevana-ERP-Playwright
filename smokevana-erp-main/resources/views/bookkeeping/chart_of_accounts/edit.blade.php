@extends('layouts.app')

@section('title', __('bookkeeping.edit_account'))

@section('css')
<style>
/* Edit Account Form - Professional Purple Theme */
.coa-form-page {
    background: linear-gradient(135deg, #f8f9fe 0%, #eef1f8 100%);
    min-height: 100vh;
    padding-bottom: 40px;
}

.coa-form-header {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 30px rgba(124, 58, 237, 0.25);
}

.coa-form-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #fff !important;
}

.coa-form-header h1 i {
    font-size: 28px;
    color: #fff !important;
}

.coa-form-header .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.coa-form-header .btn-light {
    background: rgba(255,255,255,0.95);
    color: #7c3aed;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
}

.coa-form-header .btn-light:hover {
    background: #fff;
}

.coa-form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.08);
    border: 1px solid rgba(139, 92, 246, 0.06);
    overflow: hidden;
}

.coa-form-card-header {
    background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    padding: 20px 24px;
    border-bottom: 1px solid #ede9fe;
}

.coa-form-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #5b21b6;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.coa-form-card-title i {
    color: #8b5cf6;
}

.coa-form-card-body {
    padding: 24px;
}

.coa-form-group {
    margin-bottom: 20px;
}

.coa-form-group label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
    margin-bottom: 8px;
    display: block;
}

.coa-form-group label .required {
    color: #ef4444;
}

.coa-form-group .form-control,
.coa-form-group .form-select {
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.coa-form-group .form-control:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
}

.coa-form-group .help-block {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

.coa-form-group .has-error .form-control {
    border-color: #ef4444;
}

.coa-form-group .has-error .help-block {
    color: #ef4444;
}

.coa-form-actions {
    padding: 20px 24px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.coa-form-actions .btn {
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    font-size: 14px;
}

.coa-form-actions .btn-primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border: none;
}

.coa-form-actions .btn-primary:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.35);
}

.coa-current-balance {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
}

.coa-current-balance-label {
    font-size: 12px;
    color: #065f46;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.coa-current-balance-value {
    font-size: 28px;
    font-weight: 700;
    color: #059669;
    font-family: 'SF Mono', Monaco, monospace;
    margin-top: 4px;
}

.coa-account-type-preview {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.coa-type-badge {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.coa-type-badge.asset { background: #d1fae5; color: #065f46; }
.coa-type-badge.liability { background: #fee2e2; color: #991b1b; }
.coa-type-badge.equity { background: #dbeafe; color: #1e40af; }
.coa-type-badge.income { background: #ede9fe; color: #5b21b6; }
.coa-type-badge.expense { background: #fef3c7; color: #92400e; }
.coa-type-badge.cogs { background: #e0e7ff; color: #3730a3; }
</style>
@endsection

@section('content')
<section class="content coa-form-page">
    
    <!-- Header -->
    <div class="coa-form-header">
        <div>
            <h1><i class="fas fa-edit"></i> @lang('bookkeeping.edit_account')</h1>
            <p class="subtitle">{{ $account->account_code }} - {{ $account->name }}</p>
        </div>
        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> @lang('messages.back')
        </a>
    </div>

    <form action="{{ route('bookkeeping.accounts.update', $account->id) }}" method="POST" id="edit_account_form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="coa-form-card">
                    <div class="coa-form-card-header">
                        <h3 class="coa-form-card-title">
                            <i class="fas fa-info-circle"></i>
                            @lang('bookkeeping.account_details')
                        </h3>
                    </div>
                    <div class="coa-form-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('account_code') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.account_code') <span class="required">*</span></label>
                                    <input type="text" name="account_code" class="form-control" 
                                           value="{{ old('account_code', $account->account_code) }}" 
                                           placeholder="e.g., 1000" required>
                                    @if($errors->has('account_code'))
                                        <span class="help-block">{{ $errors->first('account_code') }}</span>
                                    @else
                                        <span class="help-block">@lang('bookkeeping.unique_identifier_for_account')</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.account_name') <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name', $account->name) }}" 
                                           placeholder="e.g., Cash on Hand" required>
                                    @if($errors->has('name'))
                                        <span class="help-block">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('account_type') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.account_type') <span class="required">*</span></label>
                                    <select name="account_type" class="form-control" id="account_type" required {{ $account->is_system_account ? 'disabled' : '' }}>
                                        <option value="">@lang('messages.please_select')</option>
                                        @foreach($accountTypes as $type => $label)
                                            <option value="{{ $type }}" {{ old('account_type', $account->account_type) == $type ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($account->is_system_account)
                                        <input type="hidden" name="account_type" value="{{ $account->account_type }}">
                                    @endif
                                    @if($errors->has('account_type'))
                                        <span class="help-block">{{ $errors->first('account_type') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coa-form-group {{ $errors->has('detail_type') ? 'has-error' : '' }}">
                                    <label>@lang('bookkeeping.detail_type')</label>
                                    <select name="detail_type" class="form-control" id="detail_type" {{ $account->is_system_account ? 'disabled' : '' }}>
                                        <option value="">@lang('messages.please_select')</option>
                                        @if(isset($detailTypes[$account->account_type]))
                                            @foreach($detailTypes[$account->account_type] as $key => $label)
                                                <option value="{{ $key }}" {{ old('detail_type', $account->detail_type) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($account->is_system_account)
                                        <input type="hidden" name="detail_type" value="{{ $account->detail_type }}">
                                    @endif
                                    @if($errors->has('detail_type'))
                                        <span class="help-block">{{ $errors->first('detail_type') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="coa-form-group">
                                    <label>@lang('bookkeeping.parent_account')</label>
                                    <select name="parent_id" class="form-control" id="parent_id">
                                        <option value="">@lang('bookkeeping.no_parent_top_level')</option>
                                        @foreach($parentAccounts as $name => $id)
                                            <option value="{{ $id }}" {{ old('parent_id', $account->parent_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">@lang('bookkeeping.select_parent_for_sub_account')</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="coa-form-group">
                                    <label>@lang('messages.status')</label>
                                    <select name="is_active" class="form-control" {{ $account->is_system_account ? 'disabled' : '' }}>
                                        <option value="1" {{ old('is_active', $account->is_active) == 1 ? 'selected' : '' }}>
                                            @lang('business.is_active')
                                        </option>
                                        <option value="0" {{ old('is_active', $account->is_active) == 0 ? 'selected' : '' }}>
                                            @lang('lang_v1.inactive')
                                        </option>
                                    </select>
                                    @if($account->is_system_account)
                                        <input type="hidden" name="is_active" value="{{ $account->is_active }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="coa-form-group">
                            <label>@lang('bookkeeping.description')</label>
                            <textarea name="description" class="form-control" rows="3" 
                                      placeholder="@lang('bookkeeping.optional_description')">{{ old('description', $account->description) }}</textarea>
                        </div>
                    </div>
                    <div class="coa-form-actions">
                        <a href="{{ route('bookkeeping.accounts.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> @lang('messages.cancel')
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('messages.update')
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Current Balance Card -->
                <div class="coa-form-card" style="margin-bottom: 24px;">
                    <div class="coa-form-card-body">
                        <div class="coa-current-balance">
                            <div class="coa-current-balance-label">@lang('bookkeeping.current_balance')</div>
                            <div class="coa-current-balance-value">@format_currency($account->current_balance ?? 0)</div>
                        </div>
                    </div>
                </div>

                <!-- Account Info Card -->
                <div class="coa-form-card">
                    <div class="coa-form-card-header">
                        <h3 class="coa-form-card-title">
                            <i class="fas fa-chart-bar"></i>
                            @lang('bookkeeping.account_info')
                        </h3>
                    </div>
                    <div class="coa-form-card-body">
                        <table class="table table-condensed" style="margin-bottom: 0;">
                            <tr>
                                <td class="text-muted">@lang('bookkeeping.created_at'):</td>
                                <td class="text-right">{{ $account->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">@lang('bookkeeping.opening_balance'):</td>
                                <td class="text-right">@format_currency($account->opening_balance ?? 0)</td>
                            </tr>
                            @if($account->is_system_account)
                            <tr>
                                <td colspan="2">
                                    <div class="alert alert-info" style="margin: 10px 0 0; padding: 10px; font-size: 12px;">
                                        <i class="fas fa-lock"></i> @lang('bookkeeping.system_account_notice')
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>
                        
                        <div style="margin-top: 16px;">
                            <a href="{{ route('bookkeeping.accounts.ledger', $account->id) }}" class="btn btn-info btn-block">
                                <i class="fas fa-book"></i> @lang('bookkeeping.view_ledger')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var detailTypes = @json($detailTypes);

    // Update detail types when account type changes
    $('#account_type').on('change', function() {
        var accountType = $(this).val();
        var $detailType = $('#detail_type');
        
        $detailType.empty();
        $detailType.append('<option value="">@lang("messages.please_select")</option>');
        
        if (accountType && detailTypes[accountType]) {
            $.each(detailTypes[accountType], function(key, label) {
                $detailType.append('<option value="' + key + '">' + label + '</option>');
            });
        }
    });

    // Form validation
    $('#edit_account_form').on('submit', function(e) {
        var isValid = true;
        
        if (!$('input[name="account_code"]').val().trim()) {
            toastr.error('@lang("bookkeeping.account_code_required")');
            isValid = false;
        }
        
        if (!$('input[name="name"]').val().trim()) {
            toastr.error('@lang("bookkeeping.account_name_required")');
            isValid = false;
        }
        
        if (!$('#account_type').val()) {
            toastr.error('@lang("bookkeeping.account_type_required")');
            isValid = false;
        }
        
        return isValid;
    });
});
</script>
@endsection





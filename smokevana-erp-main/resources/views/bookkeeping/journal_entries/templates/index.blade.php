@extends('layouts.app')
@section('title', __('bookkeeping.journal_templates'))

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
    position: relative;
    overflow: hidden;
}

.bk-header-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
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

.bk-header-banner h1 i {
    font-size: 28px;
    color: #fff !important;
}

.bk-header-banner .subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
}

.bk-btn-primary {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: #fff !important;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bk-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
    color: #fff !important;
}

.bk-btn-outline {
    background: #fff;
    color: #7c3aed !important;
    border: 2px solid #7c3aed;
    border-radius: 10px;
    padding: 8px 18px;
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

.bk-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(124, 58, 237, 0.08);
    margin-bottom: 24px;
    overflow: hidden;
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

.bk-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.bk-table thead th {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b21a8;
    border-bottom: 2px solid rgba(124, 58, 237, 0.15);
}

.bk-table tbody tr {
    transition: all 0.2s ease;
}

.bk-table tbody tr:hover {
    background: #faf5ff;
}

.bk-table tbody td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.bk-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.bk-badge-purple {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #7c3aed;
}

.bk-badge-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}

.bk-badge-gray {
    background: #f3f4f6;
    color: #6b7280;
}

.bk-action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.bk-action-btn-edit {
    background: #ede9fe;
    color: #7c3aed;
}

.bk-action-btn-edit:hover {
    background: #7c3aed;
    color: #fff;
}

.bk-action-btn-use {
    background: #d1fae5;
    color: #059669;
}

.bk-action-btn-use:hover {
    background: #059669;
    color: #fff;
}

.bk-action-btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.bk-action-btn-delete:hover {
    background: #dc2626;
    color: #fff;
}

.bk-empty-state {
    text-align: center;
    padding: 60px 20px;
}

.bk-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.bk-empty-icon i {
    font-size: 36px;
    color: #7c3aed;
}

.template-lines-preview {
    font-size: 12px;
    color: #6b7280;
}

.template-lines-preview .line-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-right: 12px;
}

.template-lines-preview .debit {
    color: #dc2626;
}

.template-lines-preview .credit {
    color: #059669;
}
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 24px;">
        <!-- Header Banner -->
        <div class="bk-header-banner">
            <div class="bk-header-content">
                <h1><i class="fas fa-file-invoice"></i> {{ __('bookkeeping.journal_templates') }}</h1>
                <p class="subtitle">{{ __('bookkeeping.journal_templates_desc') }}</p>
            </div>
            <div style="display: flex; gap: 12px; position: relative; z-index: 2;">
                <a href="{{ route('bookkeeping.journal.index') }}" class="bk-btn-outline" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); color: #fff !important;">
                    <i class="fas fa-arrow-left"></i> {{ __('bookkeeping.back_to_journal') }}
                </a>
                <a href="{{ route('bookkeeping.journal.templates.create') }}" class="bk-btn-primary" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-plus"></i> {{ __('bookkeeping.create_template') }}
                </a>
            </div>
        </div>

        <!-- Templates Card -->
        <div class="bk-card">
            <div class="bk-card-header">
                <h3><i class="fas fa-layer-group"></i> {{ __('bookkeeping.all_templates') }}</h3>
                <span class="bk-badge bk-badge-purple">
                    <i class="fas fa-file-alt"></i> {{ $templates->count() }} {{ __('bookkeeping.templates') }}
                </span>
            </div>
            <div class="bk-card-body" style="padding: 0;">
                @if($templates->count() > 0)
                <table class="bk-table">
                    <thead>
                        <tr>
                            <th>{{ __('bookkeeping.template_name') }}</th>
                            <th>{{ __('bookkeeping.entry_type') }}</th>
                            <th>{{ __('bookkeeping.lines_preview') }}</th>
                            <th>{{ __('bookkeeping.usage_count') }}</th>
                            <th>{{ __('bookkeeping.status') }}</th>
                            <th style="width: 150px;">{{ __('bookkeeping.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #1f2937;">{{ $template->name }}</div>
                                @if($template->description)
                                <div style="font-size: 12px; color: #6b7280;">{{ Str::limit($template->description, 50) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="bk-badge bk-badge-purple">
                                    {{ $entryTypes[$template->entry_type] ?? ucfirst($template->entry_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="template-lines-preview">
                                    @foreach($template->lines->take(3) as $line)
                                    <span class="line-item {{ $line->type }}">
                                        <i class="fas fa-{{ $line->type === 'debit' ? 'arrow-right' : 'arrow-left' }}"></i>
                                        {{ $line->account ? Str::limit($line->account->name, 15) : 'N/A' }}
                                    </span>
                                    @endforeach
                                    @if($template->lines->count() > 3)
                                    <span style="color: #9ca3af;">+{{ $template->lines->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #7c3aed;">{{ $template->usage_count }}</span>
                                <span style="color: #9ca3af; font-size: 12px;">times</span>
                            </td>
                            <td>
                                @if($template->is_active)
                                <span class="bk-badge bk-badge-green">
                                    <i class="fas fa-check-circle"></i> {{ __('bookkeeping.active') }}
                                </span>
                                @else
                                <span class="bk-badge bk-badge-gray">
                                    <i class="fas fa-pause-circle"></i> {{ __('bookkeeping.inactive') }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('bookkeeping.journal.create') }}?template={{ $template->id }}" 
                                       class="bk-action-btn bk-action-btn-use" title="{{ __('bookkeeping.use_template') }}">
                                        <i class="fas fa-play"></i>
                                    </a>
                                    @if(!$template->is_system)
                                    <a href="{{ route('bookkeeping.journal.templates.edit', $template->id) }}" 
                                       class="bk-action-btn bk-action-btn-edit" title="{{ __('bookkeeping.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="bk-action-btn bk-action-btn-delete delete-template" 
                                            data-id="{{ $template->id }}" data-name="{{ $template->name }}" title="{{ __('bookkeeping.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="bk-empty-state">
                    <div class="bk-empty-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h4 style="color: #4c1d95; margin-bottom: 8px;">{{ __('bookkeeping.no_templates_yet') }}</h4>
                    <p style="color: #6b7280; margin-bottom: 20px;">{{ __('bookkeeping.no_templates_desc') }}</p>
                    <a href="{{ route('bookkeeping.journal.templates.create') }}" class="bk-btn-primary">
                        <i class="fas fa-plus"></i> {{ __('bookkeeping.create_first_template') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Delete template
    $('.delete-template').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        
        swal({
            title: '{{ __("bookkeeping.confirm_delete") }}',
            text: '{{ __("bookkeeping.delete_template_warning") }}'.replace(':name', name),
            icon: 'warning',
            buttons: ['{{ __("messages.cancel") }}', '{{ __("messages.delete") }}'],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ url("bookkeeping/journal-templates") }}/' + id,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            location.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.msg || 'Error deleting template');
                    }
                });
            }
        });
    });
});
</script>
@endsection




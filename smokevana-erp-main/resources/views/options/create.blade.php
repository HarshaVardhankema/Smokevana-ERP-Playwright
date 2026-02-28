@extends('layouts.app')
@section('title', 'Create Option')
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* Create Option – match “Create New Offer” style: dark header bar + section cards with dark header / light body */
.options-form-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }

/* Top header bar: dark blue-grey, icon + title + subtitle left; orange Create button right */
.options-form-page .amazon-options-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 10px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.options-form-page .amazon-options-banner .banner-inner {
    display: flex;
    align-items: center;
    gap: 18px;
}
.options-form-page .amazon-options-banner .banner-icon {
    width: 52px; height: 52px; min-width: 52px;
    border-radius: 10px; background: rgba(255,255,255,0.1);
    color: #fff; font-size: 24px;
    display: flex; align-items: center; justify-content: center;
}
.options-form-page .amazon-options-banner .banner-text { display: flex; flex-direction: column; gap: 6px; }
.options-form-page .amazon-options-banner .banner-title {
    font-size: 24px; font-weight: 700; margin: 0; color: #fff;
}
.options-form-page .amazon-options-banner .banner-subtitle {
    font-size: 13px; color: rgba(255,255,255,0.78); margin: 0;
}
.options-form-page .amazon-options-banner .banner-action .btn-option-save {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important; color: #fff !important;
    font-weight: 600; padding: 10px 24px; border-radius: 6px;
}
.options-form-page .amazon-options-banner .banner-action .btn-option-save:hover { color: #fff !important; opacity: 0.95; }

/* Section card: dark header (rounded top) + light beige body (rounded bottom) */
.options-form-page .option-section-card {
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #D5D9D9;
}
.options-form-page .option-section-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 600;
}
.options-form-page .option-section-header i { color: #fff; font-size: 18px; }
.options-form-page .option-section-body {
    background: #f7f8f8;
    padding: 1.25rem 1.5rem;
}
.options-form-page .option-section-body .form-group { margin-bottom: 0.75rem; }
.options-form-page .option-section-body label { color: #0F1111 !important; font-size: 0.8125rem; }
.options-form-page .option-section-body .form-control {
    background: #fff; border: 1px solid #D5D9D9; color: #0F1111;
    font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-height: 2rem;
    box-sizing: border-box;
}
.options-form-page .option-section-body .form-control:focus {
    border-color: #FF9900; outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.options-form-page .option-section-body textarea.form-control { min-height: 120px; }
.options-form-page .option-section-body input[type="radio"] { accent-color: #FF9900; }
.options-form-page .option-section-body .radio-inline { color: #0F1111 !important; margin-right: 1rem; }
.options-form-page .option-section-body .row { margin-left: -0.375rem; margin-right: -0.375rem; }
.options-form-page .option-section-body .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

.options-form-page .option-form-actions {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.options-form-page .btn-option-save {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important; color: #fff !important;
    font-weight: 600; padding: 10px 24px; border-radius: 6px;
}
.options-form-page .btn-option-save:hover { color: #fff !important; opacity: 0.95; }
.options-form-page .btn-option-cancel {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    color: #0f1111 !important; padding: 10px 20px; border-radius: 6px;
}
.options-form-page .btn-option-cancel:hover { background: #f0f2f2 !important; }
.options-form-page .text-danger { color: #c45500 !important; }
.options-form-page .box-primary {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
</style>
@endsection

@section('content')
<div class="options-form-page">
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
        {!! Form::open(['route' => 'options.store', 'method' => 'POST', 'id' => 'option_create_form']) !!}

        <!-- Top header bar: title left, Create button right (match Create New Offer) -->
        <div class="amazon-options-banner amazon-theme-banner">
            <div class="banner-inner">
                <div class="banner-icon"><i class="fa fa-cog" aria-hidden="true"></i></div>
                <div class="banner-text">
                    <h1 class="banner-title">Create Option</h1>
                    <p class="banner-subtitle">Add a new system option (type, key, value and usage).</p>
                </div>
            </div>
            <div class="banner-action">
                <button type="submit" class="btn btn-option-save">
                    <i class="fa fa-save"></i> Create
                </button>
            </div>
        </div>

        <div class="option-section-card">
            <div class="option-section-header"><i class="fa fa-tag"></i> Type & Key</div>
            <div class="option-section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('type', 'Type:') !!}
                        {!! Form::text('type', null, ['class' => 'form-control', 'placeholder' => 'Enter type']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('key', 'Key:') !!}
                        {!! Form::text('key', null, ['class' => 'form-control', 'placeholder' => 'Enter key']) !!}
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="option-section-card">
            <div class="option-section-header"><i class="fa fa-edit"></i> Value Input Type & Value</div>
            <div class="option-section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('value_input_type', 'Value Input Type:') !!}
                        <div>
                            <label class="radio-inline">
                                <input type="radio" name="value_input_type" value="simple" checked> Simple Input
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="value_input_type" value="editor"> Text Editor
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('value', 'Value:') !!}
                        <div id="simple_input_wrapper">
                            {!! Form::textarea('value', null, ['class' => 'form-control', 'id' => 'option_value_simple', 'placeholder' => 'Enter value', 'rows' => 8]) !!}
                        </div>
                        <div id="editor_wrapper" style="display:none;">
                            {!! Form::textarea('value_editor', null, ['class' => 'form-control', 'id' => 'option_value_editor', 'placeholder' => 'Enter value', 'rows' => 8]) !!}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="option-section-card">
            <div class="option-section-header"><i class="fa fa-window-maximize"></i> Modal & Use For</div>
            <div class="option-section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('modal_type', 'Modal Type:') !!}
                        {!! Form::text('modal_type', null, ['class' => 'form-control', 'placeholder' => 'Enter modal type']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('modal_id', 'Modal ID:') !!}
                        {!! Form::number('modal_id', null, ['class' => 'form-control', 'placeholder' => 'Enter modal ID']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('use_for', 'Use For:' . ' <span class="text-danger">*</span>', [], false) !!}
                        {!! Form::select('use_for', ['frontend' => 'Frontend', 'backend' => 'Backend'], 'backend', ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']) !!}
                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="option-section-card">
            <div class="option-section-header"><i class="fa fa-paper-plane"></i> @lang('messages.action')</div>
            <div class="option-section-body">
                <div class="option-form-actions">
                    <button type="submit" class="btn btn-option-save">
                        <i class="fa fa-save"></i> @lang('messages.save')
                    </button>
                    <a href="{{ route('options.index') }}" class="btn btn-option-cancel">
                        <i class="fa fa-times"></i> @lang('messages.cancel')
                    </a>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
        @endcomponent
    </section>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        
        var editorInitialized = false;
        
        // Handle input type change
        $('input[name="value_input_type"]').change(function() {
            var selectedType = $(this).val();
            
            if (selectedType === 'simple') {
                // Show simple input wrapper
                $('#simple_input_wrapper').show();
                $('#editor_wrapper').hide();
                
                // Destroy TinyMCE if exists
                if (editorInitialized && tinymce.get('option_value_editor')) {
                    tinymce.get('option_value_editor').remove();
                    editorInitialized = false;
                }
            } else {
                // Show editor wrapper
                $('#simple_input_wrapper').hide();
                $('#editor_wrapper').show();
                
                // Initialize TinyMCE only once
                if (!editorInitialized) {
                    setTimeout(function() {
                        tinymce.init({
                            selector: 'textarea#option_value_editor',
                            height: 250
                        });
                        editorInitialized = true;
                    }, 100);
                }
            }
        });
        
        // Before form submit, copy content to the main value field
        $('#option_create_form').submit(function(e) {
            var selectedType = $('input[name="value_input_type"]:checked').val();
            
            if (selectedType === 'simple') {
                // Use simple input
                $('#option_value_simple').attr('name', 'value');
                $('#option_value_editor').removeAttr('name');
            } else {
                // Use editor - sync content first
                if (editorInitialized && tinymce.get('option_value_editor')) {
                    tinymce.get('option_value_editor').save();
                }
                $('#option_value_editor').attr('name', 'value');
                $('#option_value_simple').removeAttr('name');
            }
        });
    });
</script>
@endsection

@extends('layouts.app')
@section('title', 'Edit Option')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Edit Option</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        {!! Form::model($option, ['route' => ['options.update', $option->id], 'method' => 'PUT', 'id' => 'option_edit_form']) !!}
        
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

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('value_input_type', 'Value Input Type:') !!}
                    <div>
                        @php
                            $hasHtml = $option->value && preg_match('/<[^>]+>/', $option->value);
                        @endphp
                        <label class="radio-inline">
                            <input type="radio" name="value_input_type" value="simple" {{ !$hasHtml ? 'checked' : '' }}> Simple Input
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="value_input_type" value="editor" {{ $hasHtml ? 'checked' : '' }}> Text Editor
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('value', 'Value:') !!}
                    <div id="simple_input_wrapper" style="{{ $hasHtml ? 'display:none;' : '' }}">
                        {!! Form::textarea('value', null, ['class' => 'form-control', 'id' => 'option_value_simple', 'placeholder' => 'Enter value', 'rows' => 8]) !!}
                    </div>
                    <div id="editor_wrapper" style="{{ !$hasHtml ? 'display:none;' : '' }}">
                        {!! Form::textarea('value_editor', $option->value, ['class' => 'form-control', 'id' => 'option_value_editor', 'placeholder' => 'Enter value', 'rows' => 8]) !!}
                    </div>
                </div>
            </div>
        </div>

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
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('use_for', 'Use For:' . ' <span class="text-danger">*</span>', [], false) !!}
                    {!! Form::select('use_for', ['frontend' => 'Frontend', 'backend' => 'Backend'], null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']) !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> @lang('messages.update')
                </button>
                <a href="{{ route('options.index') }}" class="btn btn-default">
                    <i class="fa fa-times"></i> @lang('messages.cancel')
                </a>
            </div>
        </div>

        {!! Form::close() !!}
    @endcomponent
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        
        var editorInitialized = false;
        
        // Initialize TinyMCE if editor is selected on load
        if ($('input[name="value_input_type"]:checked').val() === 'editor') {
            setTimeout(function() {
                tinymce.init({
                    selector: 'textarea#option_value_editor',
                    height: 250
                });
                editorInitialized = true;
            }, 100);
        }
        
        // Handle input type change
        $('input[name="value_input_type"]').change(function() {
            var selectedType = $(this).val();
            
            if (selectedType === 'simple') {
                // Copy content from editor to simple input before switching
                if (editorInitialized && tinymce.get('option_value_editor')) {
                    var content = tinymce.get('option_value_editor').getContent();
                    // Strip HTML tags for simple input
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = content;
                    $('#option_value_simple').val(tempDiv.textContent || tempDiv.innerText || "");
                    
                    // Destroy TinyMCE
                    tinymce.get('option_value_editor').remove();
                    editorInitialized = false;
                }
                
                // Show simple input wrapper
                $('#simple_input_wrapper').show();
                $('#editor_wrapper').hide();
            } else {
                // Copy content from simple to editor
                var simpleContent = $('#option_value_simple').val();
                $('#option_value_editor').val(simpleContent);
                
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
        $('#option_edit_form').submit(function(e) {
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


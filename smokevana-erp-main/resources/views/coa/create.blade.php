@extends('layouts.app')

@section('title', 'Create COA')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .coa-create-page { background: #EAEDED; min-height: calc(100vh - 120px); padding: 20px 0; padding-bottom: 2rem; }
    .coa-create-page .page-header-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        border-radius: 10px;
        padding: 24px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .coa-create-page .page-header-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .coa-create-page .page-header-card h1 {
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }
    .coa-create-page .page-header-card .icon-box {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 10px;
        display: flex;
        width: 52px;
        height: 52px;
        min-width: 52px;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ff9900;
    }
    .coa-create-page .page-header-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.78);
        margin: 4px 0 0 0;
    }
    .coa-create-page .content-card {
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #D5D9D9;
    }
    .coa-create-page .content-card .box {
        background: transparent !important;
        border: none !important;
    }
    .coa-create-page .content-card .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .coa-create-page .content-card .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .coa-create-page .content-card .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
    }
    .coa-create-page .content-card .box-body {
        background: #f7f8f8 !important;
        padding: 1rem 1.25rem;
    }
    .coa-lists-table thead th {
        background: #e8e9e9 !important;
        color: #232F3E !important;
        border-bottom: 2px solid #D5D9D9;
        padding: 10px 12px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .coa-lists-table tbody td {
        padding: 8px 10px;
        background: #fff;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="coa-create-page">
    <div class="container-fluid">
        <div class="page-header-card">
            <div class="page-header-title">
                <div class="icon-box"><i class="fas fa-certificate"></i></div>
                <div>
                    <h1>Create COA</h1>
                    <p class="page-header-subtitle">Add a category with one or more COA list items linked to Google Drive</p>
                </div>
            </div>
            <div>
                <a href="{{ route('coa.index') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="content-card">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Create COA')])

            @if(session('status'))
                <div class="alert alert-{{ session('status.success') ? 'success' : 'danger' }}">
                    {{ session('status.msg') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin-bottom: 0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! Form::open(['url' => route('coa.store'), 'method' => 'post', 'id' => 'coa_form']) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_name', 'Category:') !!}
                        {!! Form::text('category_name', old('category_name'), [
                            'class' => 'form-control',
                            'id' => 'category_name_input',
                            'placeholder' => 'Enter category name',
                            'required'
                        ]) !!}
                        <input type="hidden" name="category_id" id="category_id_input" value="">
                        <small class="text-muted" style="margin-top: 5px; display: block;">
                            Enter the category name manually
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('location_id', 'Location:') !!}
                        {!! Form::select('location_id', $locations, old('location_id'), [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => 'Select location',
                            'style' => 'width:100%',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('brand_id', 'Brand (optional):') !!}
                        {!! Form::select('brand_id', $brands, old('brand_id'), [
                            'class' => 'form-control select2',
                            'placeholder' => 'Select brand (optional)',
                            'style' => 'width:100%',
                        ]) !!}
                    </div>
                </div>
            </div>

            <hr>

            <h4 style="margin-top: 0; margin-bottom: 10px;">COA Lists</h4>
            <p class="text-muted" style="margin-bottom: 10px;">
                Add one or more list items under this category. Each list can have a descriptive name and a Google Drive link to the certificate or image.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered coa-lists-table" id="coa_lists_table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">List Name</th>
                            <th style="width: 60%;">Google Drive Link</th>
                            <th style="width: 10%;">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="list-row">
                            <td>
                                <input type="text" name="lists[0][name]" class="form-control" placeholder="e.g. Flavours" required>
                            </td>
                            <td>
                                <input type="text" name="lists[0][link]" class="form-control" placeholder="https://drive.google.com/..." required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-danger remove-list-row" disabled>
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-default" id="add_list_row">
                <i class="fa fa-plus"></i> Add Another List
            </button>

            <div class="clearfix" style="margin-top: 20px;"></div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save COA
            </button>

            {!! Form::close() !!}

            @endcomponent
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        // Initialize Select2 for location and brand dropdowns
        $('.select2').select2();

        $('#add_list_row').on('click', function () {
            var $tbody = $('#coa_lists_table tbody');
            var index = $tbody.find('.list-row').length;

            var rowHtml = '' +
                '<tr class="list-row">' +
                    '<td>' +
                        '<input type="text" name="lists[' + index + '][name]" class="form-control" placeholder="e.g. flavours" required>' +
                    '</td>' +
                    '<td>' +
                        '<input type="text" name="lists[' + index + '][link]" class="form-control" placeholder="https://drive.google.com/..." required>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-xs btn-danger remove-list-row">' +
                            '<i class="fa fa-trash"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';

            $tbody.append(rowHtml);
            $tbody.find('.remove-list-row:disabled').prop('disabled', false);
        });

        $(document).on('click', '.remove-list-row', function () {
            var $tbody = $('#coa_lists_table tbody');

            if ($tbody.find('.list-row').length <= 1) {
                return;
            }

            $(this).closest('tr').remove();
        });

        // Form submission validation
        $('#coa_form').on('submit', function(e) {
            var categoryName = $('#category_name_input').val();
            if (!categoryName || !categoryName.trim()) {
                e.preventDefault();
                alert('Please enter a category name.');
                return false;
            }
            // Ensure category_id is empty for new categories
            $('#category_id_input').val('');
        });
    });
</script>
@endsection


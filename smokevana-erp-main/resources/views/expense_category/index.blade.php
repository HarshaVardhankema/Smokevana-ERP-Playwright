@extends('layouts.app')
@section('title', __('expense.expense_categories'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* Expense Categories Page - Amazon Theme */
    .expense-categories-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }

    /* Top header banner */
    .expense-categories-page .content-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .expense-categories-page .content-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .ec-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff; }
    .ec-header-banner .banner-title i { color: #ff9900; }
    .ec-header-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.88); margin: 4px 0 0 0; }

    /* Section card */
    .expense-categories-page .box-primary {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        background: #fff;
    }
    .expense-categories-page .box-primary .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .expense-categories-page .box-primary .box-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: #ff9900;
    }
    .expense-categories-page .box-primary .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 0 1 auto;
    }
    .expense-categories-page .box-primary .box-title i { color: #ff9900 !important; }
    .expense-categories-page .box-primary .box-tools {
        margin-left: auto;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .expense-categories-page .box-primary .tw-flow-root,
    .expense-categories-page .box-primary .table-responsive {
        background: #f7f8f8 !important;
        padding: 1rem 1.25rem !important;
    }

    /* Add button */
    .expense-categories-page .amazon-orange-add {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 2px solid #C7511F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
    }
    .expense-categories-page .amazon-orange-add:hover { color: #fff !important; opacity: 0.95; }

    /* Table header row */
    .expense-categories-page #expense_category_table thead tr {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        position: relative;
    }
    .expense-categories-page #expense_category_table thead tr::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .expense-categories-page #expense_category_table thead th {
        background: transparent !important;
        color: #fff !important;
        font-weight: 600;
        border-color: rgba(255,255,255,0.2) !important;
        padding: 12px 14px;
        position: relative;
        z-index: 2;
        text-align: left;
    }

    /* Table body */
    .expense-categories-page #expense_category_table tbody td {
        border-color: #D5D9D9;
        padding: 10px 14px;
        vertical-align: middle;
        text-align: left;
    }

    /* ACTION column: header right-aligned, data right-aligned, buttons to far right */
    .expense-categories-page #expense_category_table thead th:last-child {
        text-align: right !important;
        padding-right: 20px;
    }
    .expense-categories-page #expense_category_table tbody td:last-child {
        text-align: right !important;
        white-space: nowrap;
        padding-right: 20px;
    }
    .expense-categories-page .expense-category-action-btns {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .expense-categories-page .expense-category-action-btns .tw-dw-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-height: 32px;
        padding: 6px 12px;
    }
    .expense-categories-page .expense-category-action-btns .btn-modal {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
    }
    .expense-categories-page .expense-category-action-btns .delete_expense_category {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
    }
    .expense-categories-page .expense-category-action-btns .delete_expense_category:hover {
        background: #c82333 !important;
    }

    /* DataTables controls */
    .expense-categories-page .dataTables_wrapper .dataTables_filter input,
    .expense-categories-page .dataTables_wrapper .dataTables_length select {
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 4px 8px;
    }
    .expense-categories-page .dataTables_wrapper .dataTables_filter input:focus,
    .expense-categories-page .dataTables_wrapper .dataTables_length select:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .expense-categories-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #ff9900 !important;
        border-color: #ff9900 !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="expense-categories-page">
<!-- Amazon-style banner -->
<section class="content-header">
    <div class="ec-header-banner">
        <h1 class="banner-title"><i class="fas fa-folder-open"></i> @lang('expense.expense_categories')</h1>
        <p class="banner-subtitle">@lang('expense.manage_your_expense_categories')</p>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'expense.all_your_expense_categories' ), 'icon' => '<i class="fas fa-list"></i>'])
        @slot('tool')
            <div class="box-tools">
                <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white btn-modal amazon-orange-add"
                    data-href="{{action([\App\Http\Controllers\ExpenseCategoryController::class, 'create'])}}"
                    data-container=".expense_category_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="expense_category_table" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40%">@lang( 'expense.category_name' )</th>
                        <th style="width:30%">@lang( 'expense.category_code' )</th>
                        <th style="width:3%">@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade expense_category_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
</div>

<script>
$(document).ready(function() {
    // Wait for app.js to initialize the table, then destroy and reinitialize with correct settings
    var checkTable = setInterval(function() {
        if ($.fn.DataTable.isDataTable('#expense_category_table')) {
            clearInterval(checkTable);
            // Destroy existing DataTable
            $('#expense_category_table').DataTable().destroy();
            // Remove any leftover DataTables DOM
            $('#expense_category_table').empty();
            // Rebuild the thead
            $('#expense_category_table').append(
                '<thead><tr>' +
                '<th style="width:40%;text-align:left">@lang("expense.category_name")</th>' +
                '<th style="width:30%;text-align:left">@lang("expense.category_code")</th>' +
                '<th style="width:50%;text-align:right">@lang("messages.action")</th>' +
                '</tr></thead>'
            );
            // Reinitialize with correct config
            window.expense_cat_table = $('#expense_category_table').DataTable({
                processing: true,
                language: { processing: '<div id="main_loader"><span class="loader"></span></div>' },
                serverSide: true,
                autoWidth: false,
                ajax: '/expense-categories',
                columnDefs: [
                    { targets: 0, width: '40%', className: 'dt-left' },
                    { targets: 1, width: '30%', className: 'dt-left' },
                    { targets: 2, width: '50%', orderable: false, searchable: false, className: 'dt-right' },
                ],
            });
        }
    }, 100);
});
</script>
@endsection

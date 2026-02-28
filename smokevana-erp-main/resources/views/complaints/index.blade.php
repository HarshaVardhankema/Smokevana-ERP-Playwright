@extends('layouts.app')
@section('title', 'Complaints')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    /* Complaints Page - Amazon Theme */
    .complaints-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    
    /* Header Banner */
    .complaints-page .content-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .complaints-page .content-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .complaints-page .content-header h1 {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #fff !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .complaints-page .content-header h1 small {
        display: block;
        font-size: 13px !important;
        color: rgba(255,255,255,0.88) !important;
        margin-top: 4px;
        font-weight: 500 !important;
    }
    
    /* Box/Card Styling */
    .complaints-page .box-primary {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #D5D9D9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        background: #fff;
    }
    .complaints-page .box-primary .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .complaints-page .box-primary .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .complaints-page .box-primary .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
    }
    .complaints-page .box-primary .box-body,
    .complaints-page .box-primary .table-responsive {
        background: #f7f8f8 !important;
        padding: 1rem 1.25rem !important;
    }
    
    /* Add Button - Amazon Orange */
    .complaints-page .box-tools a {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 2px solid #C7511F !important;
        color: #fff !important;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        position: relative;
        overflow: hidden;
    }
    .complaints-page .box-tools a::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .complaints-page .box-tools a:hover {
        color: #fff !important;
        opacity: 0.95;
        border-color: #E47911 !important;
    }
    .complaints-page .box-tools a svg {
        width: 18px;
        height: 18px;
        margin-right: 6px;
    }
    
    /* Table Header - Dark with Orange Top Line */
    .complaints-page #complaints_table thead tr {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        position: relative;
    }
    .complaints-page #complaints_table thead tr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #ff9900;
        z-index: 1;
    }
    .complaints-page #complaints_table thead th {
        background: transparent !important;
        color: #fff !important;
        font-weight: 600;
        border-color: rgba(255,255,255,0.2) !important;
        padding: 12px 8px;
        position: relative;
        z-index: 2;
    }
    .complaints-page #complaints_table thead th.sorting::after,
    .complaints-page #complaints_table thead th.sorting_asc::after,
    .complaints-page #complaints_table thead th.sorting_desc::after {
        color: #ff9900 !important;
    }
    
    /* Table Body */
    .complaints-page #complaints_table tbody tr {
        background: #fff;
    }
    .complaints-page #complaints_table tbody td {
        border-color: #D5D9D9;
        padding: 10px 8px;
    }
    
    /* Status Badges */
    .complaints-page .badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .complaints-page .badge-warning,
    .complaints-page .badge[style*="background: #FF9900"],
    .complaints-page .badge[style*="background-color: #FF9900"] {
        background: #FF9900 !important;
        color: #fff !important;
    }
    .complaints-page .badge-success {
        background: #28a745 !important;
        color: #fff !important;
    }
    
    /* Action Buttons */
    .complaints-page .btn-view {
        background: #17a2b8 !important;
        color: #fff !important;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .complaints-page .btn-edit {
        background: #FF9900 !important;
        color: #fff !important;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    .complaints-page .btn-delete {
        background: #dc3545 !important;
        color: #fff !important;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
    }
    
    /* DataTables Controls */
    .complaints-page .dataTables_wrapper .dataTables_filter input,
    .complaints-page .dataTables_wrapper .dataTables_length select {
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 4px 8px;
    }
    .complaints-page .dataTables_wrapper .dataTables_filter input:focus,
    .complaints-page .dataTables_wrapper .dataTables_length select:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
    }
    .complaints-page .dt-buttons .btn,
    .complaints-page .dt-buttons button {
        background: #232f3e !important;
        border: 1px solid #37475a !important;
        color: #fff !important;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.8125rem;
    }
    .complaints-page .dt-buttons .btn:hover,
    .complaints-page .dt-buttons button:hover {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
    }
    .complaints-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #ff9900 !important;
        border-color: #ff9900 !important;
        color: #fff !important;
    }
    .complaints-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: #ff9900;
        color: #232f3e;
    }
</style>
@endsection

@section('content')
<div class="complaints-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Complaints
        <small>Manage customer complaints</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @if(session('status'))
        @if(session('status')['success'])
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('status')['msg'] }}
            </div>
        @else
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('status')['msg'] }}
            </div>
        @endif
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Complaints'])
        @can('complaint.create')
            @slot('tool')
                <div class="box-tools">
                    <a href="{{ action([\App\Http\Controllers\ComplaintController::class, 'create']) }}" 
                       class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
        @endcan
        
        @can('complaint.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view hide-footer" id="complaints_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Request Type</th>
                            <th>Customer/Contact</th>
                            <th>Invoice No</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

</section>
<!-- /.content -->
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var complaints_table = $('#complaints_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ action([\App\Http\Controllers\ComplaintController::class, 'index']) }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'request_type', name: 'request_type' },
                { data: 'contact_id', name: 'contact_id' },
                { data: 'transaction_id', name: 'transaction_id' },
                { data: 'products', name: 'products', orderable: false },
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status' },
                { data: 'created_by', name: 'created_by' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        // Delete complaint
        $(document).on('click', 'button.delete_complaint_button', function() {
            swal({
                title: LANG.sure,
                text: 'Are you sure you want to delete this complaint?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: { "_token": "{{ csrf_token() }}" },
                        success: function(result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                complaints_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection

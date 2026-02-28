@extends('layouts.app')
@section('title', 'Options')
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.options-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.options-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.options-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.options-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.options-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.options-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.options-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.options-page .box-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.options-page .box-title { color: #fff !important; font-weight: 600; }
.options-page .box-tools { margin: 0; }
.options-page .box-tools .tw-dw-btn,
.options-page .box-tools .btn-primary,
.options-page .box-tools a[href*="options.create"] {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
}
.options-page #options_table thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.options-page #options_table tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.options-page #options_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.options-page #options_table tbody tr:hover td { background: #fff8e7 !important; }
.options-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.options-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.options-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important; color: #0f1111 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page options-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-sliders page-header-icon"></i>
        Options
        <small>Manage your system options</small>
    </h1>
</section>

{{-- @if(session('status'))
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
@endif --}}

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Options'])
        @slot('tool')
            <div class="box-tools">
                <a href="{{ route('options.create') }}" class="tw-dw-btn">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        @endslot

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="options_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Key</th>
                        {{-- <th>Value</th> --}}
                        <th>Modal Type</th>
                        <th>Modal ID</th>
                        <th>Use For</th>
                        <th>Created At</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var options_table = $('#options_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('options.index') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'type', name: 'type' },
                { data: 'key', name: 'key' },
                // { data: 'value', name: 'value' },
                { data: 'modal_type', name: 'modal_type' },
                { data: 'modal_id', name: 'modal_id' },
                { data: 'use_for', name: 'use_for' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.delete_option_button', function(e) {
            e.preventDefault();
            var delete_url = $(this).data('href');
            
            swal({
                title: 'Are you sure?',
                text: "You want to delete this option?",
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: 'Cancel',
                        value: null,
                        visible: true,
                        className: 'btn btn-default',
                        closeModal: true,
                    },
                    confirm: {
                        text: 'Delete',
                        value: true,
                        visible: true,
                        className: 'btn btn-danger',
                        closeModal: true
                    }
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: 'DELETE',
                        url: delete_url,
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                options_table.ajax.reload();
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


@extends('layouts.app')
@section('title', 'Tickets')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Tickets
        <small>Manage Tickets</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Tickets'])
        @slot('tool')
            <div class="box-tools">
                <button id="dynamic_button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right tw-mb-2 btn-modal"
                data-href="{{action([\App\Http\Controllers\TicketController::class, 'create'])}}" 
                data-container=".ticket_modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg> Add
            </button>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tickets_table">
                <thead>
                    <tr>
                        <th>Reference No</th>
                        <th>Lead Name</th>
                        <th>Assigned To</th>
                        <th>Issue Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade ticket_modal" tabindex="-1" role="dialog" 
         aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        //Tickets table
        var tickets_table = $('#tickets_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\TicketController::class, 'index']) }}",
                data: function(d) {
                }
            },
            columnDefs: [
                {
                    targets: [1, 2, 3, 4, 5, 6],
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                }
            ],
            columns: [
                { data: 'reference_no', name: 'reference_no' },
                { data: 'lead_name', name: 'lead_name' },
                { data: 'assigned_to', name: 'assigned_to' },
                { data: 'issue_type_badge', name: 'issue_type' },
                { data: 'issue_priority_badge', name: 'issue_priority' },
                { data: 'status_badge', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action' }
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#tickets_table'));
            }
        });

        $(document).on('click', 'a.delete_ticket', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: "Are you sure you want to delete this ticket?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                tickets_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        // Handle modal buttons
        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            var container = $(this).data('container');
            
            $.ajax({
                url: href,
                dataType: 'html',
                success: function(result) {
                    $(container).html(result).modal('show');
                }
            });
        });
    });
</script>
@endsection


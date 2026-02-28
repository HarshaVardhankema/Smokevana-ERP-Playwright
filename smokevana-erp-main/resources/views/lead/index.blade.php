@extends('layouts.app')
@section('title', __('lang_v1.leads'))
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('lang_v1.leads')
        <small>@lang('contact.manage_your_contact', ['contacts' => __('lang_v1.leads')])</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_leads')])
        @slot('tool')
            <div class="box-tools">
                <button id="dynamic_button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right tw-mb-2 btn-modal"
                data-href="{{action([\App\Http\Controllers\LeadController::class, 'create'])}}" 
                data-container=".lead_modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg> @lang('messages.add')
            </button>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="leads_table">
                <thead>
                    <tr>
                        <th>Reference No</th>
                        <th>@lang('lang_v1.store_name')</th>
                        <th>@lang('lang_v1.address')</th>
                        <th>Created By</th>
                        <th>Visited By</th>
                        <th>Status</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade lead_modal" tabindex="-1" role="dialog" 
         aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        //Leads table
        var leads_table = $('#leads_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\LeadController::class, 'index']) }}",
                data: function(d) {
                }
            },
            columnDefs: [
                {
                    targets: 2,
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 3,
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 5,
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                }
            ],
            columns: [
                { data: 'reference_no', name: 'reference_no' },
                { data: 'store_name', name: 'store_name' },
                { data: 'full_address', name: 'full_address' },
                { data: 'created_by', name: 'created_by' },
                { data: 'visited_by', name: 'visited_by' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action' }
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#leads_table'));
            }
        });

        $(document).on('click', 'a.delete_lead', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_lead,
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
                                leads_table.ajax.reload();
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
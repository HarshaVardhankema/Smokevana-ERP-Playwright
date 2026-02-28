@extends('layouts.app')
@section('title', 'Business Identifications')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Business Identifications
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">Manage customer business identifications</small>
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

    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Business Identifications'])
        @can('business_identification.create')
            @slot('tool')
                <div class="box-tools">
                    <a href="{{ action([\App\Http\Controllers\BusinessIdentificationController::class, 'create']) }}" 
                       class="tw-dw-btn tw-dw-btn-sm tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full">
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
        
        @can('business_identification.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view hide-footer" id="business_identifications_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Legal Business Name</th>
                            <th>Customer/Contact</th>
                            <th>FEIN/Tax ID</th>
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

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var identifications_table = $('#business_identifications_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ action([\App\Http\Controllers\BusinessIdentificationController::class, 'index']) }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'legal_business_name', name: 'legal_business_name' },
                { data: 'contact_id', name: 'contact_id' },
                { data: 'fein_tax_id', name: 'fein_tax_id' },
                { data: 'status', name: 'status' },
                { data: 'created_by', name: 'created_by' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });

        // Delete identification
        $(document).on('click', 'button.delete_identification_button', function() {
            swal({
                title: LANG.sure,
                text: 'Are you sure you want to delete this business identification?',
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
                                identifications_table.ajax.reload();
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


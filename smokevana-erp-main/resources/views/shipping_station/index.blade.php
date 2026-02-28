@extends('layouts.app')
@section('title', __('Shipping Stations'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('Shipping Stations')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('Manage your shipping stations')</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('All Shipping Stations')])
            @can('brand.create')
                @slot('tool')
                    <div class="box-tools">
                        <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                            data-href="{{action([\App\Http\Controllers\ShippingStationController::class, 'create']) }}"
                            data-container=".shipping_station_modal">
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
            
            @can('brand.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped ajax_view hide-footer" id="shipping_stations_table">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Station Code')</th>
                                {{-- <th>@lang('Location')</th> --}}
                                <th>@lang('Assigned User')</th>
                                <th>@lang('Active')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade shipping_station_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable
            shipping_stations_table = $('#shipping_stations_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{action([\App\Http\Controllers\ShippingStationController::class, 'index'])}}",
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'station_code', name: 'station_code' },
                    // { data: 'location_name', name: 'location_name' },
                    { data: 'assigned_user', name: 'assigned_user' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#shipping_stations_table'));
                },
            });

            $(document).on('click', '.delete_shipping_station_button', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed) => {
                    if (confirmed) {
                        $.ajax({
                            method: 'DELETE',
                            url: url,
                            dataType: 'json',
                            success: function(result) {
                                if (result.success) {
                                    toastr.success(result.msg);
                                    shipping_stations_table.ajax.reload();
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


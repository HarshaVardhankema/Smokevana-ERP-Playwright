@extends('layouts.app')
@section('title', __('tax_rate.tax_rates'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.tax-rates-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.tax-rates-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.tax-rates-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.tax-rates-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.tax-rates-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.tax-rates-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.tax-rates-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.tax-rates-page .box-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.tax-rates-page .box-title { color: #fff !important; font-weight: 600; }
.tax-rates-page .box-tools { margin: 0; }
.tax-rates-page #dynamic_button {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
}
.tax-rates-page #tax_rates_table thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.tax-rates-page #tax_rates_table tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.tax-rates-page #tax_rates_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.tax-rates-page #tax_rates_table tbody tr:hover td { background: #fff8e7 !important; }
.tax-rates-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.tax-rates-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.tax-rates-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important; color: #0f1111 !important;
}
.tax-rates-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: #ff9900; background: #fff8e7 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page tax-rates-page">
    <section class="content-header">
        <h1>
            <i class="fa fa-percent page-header-icon"></i>
            @lang('tax_rate.tax_rates')
            <small>@lang('tax_rate.manage_your_tax_rates')</small>
        </h1>
    </section>

    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('report.filters')</h4>
                </div>
                <div class="modal-body">
                    @php
                        $user = auth()->user();
                        $is_admin = $user->user_type === 'admin' || $user->can('admin');
                        
                        // Check if user is B2C based on their location's is_b2c field
                        $is_b2c = false;
                        $user_location_id = null;
                        
                        // Get user's location ID using the same method as controller
                        $business_id = request()->session()->get('user.business_id');
                        $is_super_admin = $user->can('access_all_locations') || $user->can('admin');
                        
                        if ($is_super_admin) {
                            // For super admin, we'll assume they can see all filters
                            $is_b2c = true; // Allow filters for admin
                        } else {
                            // For regular users, get their permitted locations
                            $permitted_locations = $user->permitted_locations($business_id);
                            
                            if ($permitted_locations == 'all') {
                                // User has access to all locations, get first available location
                                $default_location = \App\BusinessLocation::where('business_id', $business_id)
                                    ->where('is_active', 1)
                                    ->first();
                                $user_location_id = $default_location ? $default_location->id : null;
                            } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
                                // User has specific location permissions, use the first one
                                $user_location_id = $permitted_locations[0];
                            }
                            
                            // Check if the location is B2C
                            if ($user_location_id) {
                                $is_b2c = \App\BusinessLocation::where('id', $user_location_id)->value('is_b2c') == 1;
                            }
                        }
                        
                        $is_b2b = !$is_b2c && !$is_admin;
                        
                    @endphp
                    <div class="row">
                        @if ($is_admin)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('location_filter', __('Location') . ':') !!}
                                {!! Form::select(
                                    'location_filter',
                                    $business_locations,
                                    null,
                                    [
                                        'class' => 'form-control select2',
                                        'style' => 'width:100%',
                                        'placeholder' => __('lang_v1.all'),
                                       
                                    ],
                                ) !!}
                            </div>
                        </div>
                        @endif
                        @if ($is_admin || $is_b2c )
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('brand_filter', __('Brand Name') . ':') !!}
                                {!! Form::select('brand_filter', $brands ?? [], null, 
                                    [
                                        'class' => 'form-control select2',
                                        'style' => 'width:100%',
                                        'id' => 'brand_filter',
                                        'placeholder' => __('lang_v1.all'),
                                      
                                    ],
                                ) !!}
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('tax_type_filter', __('Tax Type') . ':') !!}
                                {!! Form::select('tax_type_filter', $tax_types ?? [], null, 
                                    [
                                        'class' => 'form-control select2',
                                        'style' => 'width:100%',
                                        'id' => 'tax_type_filter',
                                        'placeholder' => __('lang_v1.all'),
                                    ],
                                ) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-info" id="applyFilters">Apply Filters</button>
                    <button type="button" class="btn btn-default" id="clearFilters">Clear</button> --}}
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>



    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('tax_rate.all_your_tax_rates')])
            @can('tax_rate.create')
                @slot('tool')
                    <div class="box-tools">
                        <button id="dynamic_button" class="tw-dw-btn btn-modal" data-href="{{ action([\App\Http\Controllers\TaxRateController::class, 'create']) }}" data-container=".tax_rate_modal">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </button>
                    </div>
                @endslot
            @endcan

            @can('tax_rate.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tax_rates_table">
                        <thead>
                            <tr>
                                <th>@lang('tax_rate.location_tax_type')</th>
                                <th>@lang('tax_rate.state_code')</th>
                                <th>@lang('tax_rate.tax_type')</th>
                                <th>@lang('tax_rate.value')</th>
                                <th>@lang('Location')</th>
                                <th>@lang('Brand Name')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent
    </section>
    @include('locationtaxtype.index')
</div>
    <div class="modal fade tax_rate_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
           console.log('tax_rates_table is ready');
            if (typeof tax_rates_table !== 'undefined') {
                console.log('tax_rates_table is ready');
                
                // Change event for standard selects
                $(document).on('change', '#location_filter, #brand_filter, #tax_type_filter', function() {
                    //console.log('Filter changed:', $(this).attr('id'), $(this).val());
                    tax_rates_table.ajax.reload();
                });

                // For Select2 dropdowns
                $(document).on('select2:select', '#location_filter, #brand_filter, #tax_type_filter', function() {
                    //console.log('Select2 changed:', $(this).attr('id'), $(this).val());
                    tax_rates_table.ajax.reload();
                });
            } else {
                console.warn('tax_rates_table is not defined');
            }
        });
    </script>
@endsection

@section('scripts')
    <script type="text/javascript">
$(document).on('change', '#location_filter, #brand_filter, #tax_type_filter', function() {
    console.log('Filter changed: ', $(this).attr('id'), $(this).val());
     tax_rates_table.ajax.reload();
 });
 
 // Alternative event binding for select2
 $(document).on('select2:select', '#location_filter, #brand_filter, #tax_type_filter', function() {
    console.log('Select2 changed: ', $(this).attr('id'), $(this).val());
     tax_rates_table.ajax.reload();
 });
</script>
@endsection


{{-- @extends('layouts.app')
@section('title', __('tax_rate.tax_rates'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('tax_rate.tax_rates')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('tax_rate.manage_your_tax_rates')</small>
        </h1>
    </section>

    <!-- Main content -->

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('tax_rate.all_your_tax_rates')])
            @can('tax_rate.create')
                @slot('tool')
                    <div class="box-tools">
                        <button
                            class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                            data-href="{{ action([\App\Http\Controllers\TaxRateController::class, 'create']) }}"
                            data-container=".tax_rate_modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </button>
                    </div>
                @endslot
            @endcan
            @can('tax_rate.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tax_rates_table">
                        <thead>
                            <tr>
                                <th>state_name</th>
                                <th>@lang('tax_rate.state_code')</th>
                                <th>@lang('tax_rate.tax_type')</th>
                                <th>@lang('tax_rate.value')</th>
                                <th>@lang('tax_rate.location_tax_type')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>

                </div>
            @endcan
        @endcomponent

        @component('components.widget', ['class' => 'box-primary'])
            @slot('title')
                @lang('tax_rate.tax_groups') ( @lang('lang_v1.combination_of_taxes') ) @show_tooltip(__('tooltip.tax_groups'))
            @endslot
            @can('tax_rate.create')
                @slot('tool')
                    <div class="box-tools">
                        <button
                            class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal pull-right"
                            data-href="{{ action([\App\Http\Controllers\GroupTaxController::class, 'create']) }}"
                            data-container=".tax_group_modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </button>
                    </div>
                @endslot
            @endcan
            @can('tax_rate.view')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tax_groups_table">
                        <thead>
                            <tr>
                                <th>@lang('tax_rate.name')</th>
                                <th>@lang('tax_rate.rate')</th>
                                <th>@lang('tax_rate.sub_taxes')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade tax_rate_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade tax_group_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#tax_rates_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,

                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: false,
                "ajax": {
                    "url": '{{ action('App\Http\Controllers\TaxRateController@index') }}',
                    type: 'GET',
                    "data": function(d) {
                        d = __datatable_ajax_callback(d);
                    }
                },
                columns: [{
                        data: 'state_name',
                        name: 'state_name'
                    },
                    {
                        data: 'state_code',
                        name: 'state_code'
                    },
                    {
                        data: 'tax_type',
                        name: 'tax_type'
                    },
                    {
                        data: 'value',
                        name: 'value'
                    },
                    {
                        data: 'location_tax_type',
                        name: 'location_tax_type'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
        });
    </script>

@endsection --}}

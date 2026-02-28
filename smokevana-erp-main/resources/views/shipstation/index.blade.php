@extends('layouts.app')
@section('title', "ShipStationAPI Configuration")
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.shipstation-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.shipstation-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.shipstation-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.shipstation-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.shipstation-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.shipstation-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.shipstation-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.shipstation-page .box-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.shipstation-page .box-title { color: #fff !important; font-weight: 600; }
.shipstation-page .box-tools { margin: 0; }
.shipstation-page #dynamic_button {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
}
.shipstation-page #shipstationlist thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.shipstation-page #shipstationlist tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.shipstation-page #shipstationlist tbody tr:nth-child(even) td { background: #f9fafb !important; }
.shipstation-page #shipstationlist tbody tr:hover td { background: #fff8e7 !important; }
.shipstation-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.shipstation-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.shipstation-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important; color: #0f1111 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page shipstation-page">
    <section class="content-header">
        <h1>
            <i class="fa fa-truck page-header-icon"></i>
            ShipStationAPI Configuration
            <small>manage your ShipStationAPI here</small>
        </h1>
    </section>

    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' =>'ShipStations'])
            {{-- @can('tax_rate.create') --}}
                @slot('tool')
                    <div class="box-tools">
                        <button id="dynamic_button" class="btn-modal" data-href="{{ action([\App\Http\Controllers\ShipStationController::class, 'create']) }}" data-container=".shipstation_add_model">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </button>
                    </div>
                @endslot
            {{-- @endcan  --}}

            {{-- {{-- @can('tax_rate.view') --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="shipstationlist">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Key</th>
                                <th>Priority</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            {{-- @endcan --}}
        @endcomponent
    </section>

    <div class="modal fade shipstation_add_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</div>
@endsection

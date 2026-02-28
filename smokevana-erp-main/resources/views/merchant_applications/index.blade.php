@extends('layouts.app')
@section('title', 'Merchant Applications')
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.merchant-applications-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.merchant-applications-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.merchant-applications-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.merchant-applications-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.merchant-applications-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.merchant-applications-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.merchant-applications-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.merchant-applications-page .box-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.merchant-applications-page .box-title { color: #fff !important; font-weight: 600; }
.merchant-applications-page .box-tools { margin: 0; }
.merchant-applications-page #dynamic_button {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
}
.merchant-applications-page #merchant_application_list thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.merchant-applications-page #merchant_application_list tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.merchant-applications-page #merchant_application_list tbody tr:nth-child(even) td { background: #f9fafb !important; }
.merchant-applications-page #merchant_application_list tbody tr:hover td { background: #fff8e7 !important; }
.merchant-applications-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.merchant-applications-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.merchant-applications-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important; color: #0f1111 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page merchant-applications-page">
    <section class="content-header">
        <h1>
            <i class="fa fa-credit-card page-header-icon"></i>
            Merchant Applications
            <small>manage your transactions with payment gateways</small>
        </h1>
    </section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' =>'Merchant Applications'])
                @slot('tool')
                    <div class="box-tools">
                        <button id="dynamic_button" class="btn-modal" data-href="{{ action([\App\Http\Controllers\MerchantApplicationController::class, 'create']) }}" data-container=".merchant_application_add_model">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </button>
                    </div>
                @endslot

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="merchant_application_list">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Business Name</th>
                                <th>Owner Name</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach($applications as $application)
                            <tr>
                                <td>{{ $application->id }}</td>
                                    <td>{{ $application->legal_business_name }}</td>
                                    <td>{{ $application->owner_legal_name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $application->status === 'approved' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('merchant-applications.show', $application->id) }}" class="btn btn-sm btn-info">View</a>
                                        @if($application->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal{{ $application->id }}">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal{{ $application->id }}">
                                            Reject
                                        </button>
                                        @endif
                                    </td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            @endcomponent
</section>
<div class="modal fade merchant_application_add_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</div>
@endsection
@section('javascript')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_api_key') }}&libraries=places"></script>
<script>
      $(document).ready(function () {
        var merchant_application_list = $('#merchant_application_list').DataTable({
                    processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
                    serverSide: true,
                    fixedHeader:false,
                    ajax: '/merchant-applications',
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'legal_business_name', name: 'legal_business_name' },
                        { data: 'owner_legal_name', name: 'owner_legal_name' },
                        { data: 'status', name: 'status' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ]
                });
      });
</script>
@endsection

 
@extends('layouts.app')
@section('title', __('lang_v1.sales_commission_agents'))

@section('css')
<style>
    .amazon-sca-container {
        padding: 15px 20px 25px;
        background: #EAEDED;
        min-height: calc(100vh - 60px);
    }

    .content-header {
        display: none !important;
    }

    .amazon-sca-header-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-top: 4px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
        color: #f9fafb;
    }

    .amazon-sca-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .amazon-sca-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }

    .amazon-sca-header-title i {
        font-size: 22px;
        color: #ffffff;
    }

    .amazon-sca-header-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }

    .amazon-card {
        background: #fff;
        border: 1px solid #d5d9d9;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .amazon-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid #e7e7e7;
        flex-wrap: wrap;
        gap: 12px;
        background: #fafafa;
        border-radius: 8px 8px 0 0;
    }

    .amazon-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .amazon-card-title h2 {
        font-size: 16px;
        font-weight: 700;
        color: #0F1111;
        margin: 0;
    }

    .amazon-agent-count {
        background: #F0F2F2;
        color: #565959;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 12px;
        border: 1px solid #d5d9d9;
    }

    .amazon-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%);
        border: 1px solid #C7511F;
        border-radius: 20px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        white-space: nowrap;
    }

    .amazon-add-btn:hover {
        opacity: 0.92;
        color: #fff;
        text-decoration: none;
    }

    .amazon-add-btn svg {
        width: 16px;
        height: 16px;
    }

    .amazon-card-body {
        padding: 0;
    }

    .amazon-card-body .table-responsive {
        margin: 0;
        border: none;
    }

    /* Amazon-style table */
    .amazon-card-body table.table {
        margin-bottom: 0;
        border: none;
    }

    .amazon-card-body table.table thead th {
        background: #F7F8F8;
        color: #0F1111;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 12px 16px;
        border-bottom: 2px solid #e7e7e7;
        border-top: none;
        white-space: nowrap;
    }

    .amazon-card-body table.table tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: #0F1111;
        border-bottom: 1px solid #E6E6E6;
        vertical-align: middle;
    }

    .amazon-card-body table.table tbody tr:hover {
        background: #F7FAFA;
    }

    .amazon-card-body table.table tbody tr:last-child td {
        border-bottom: none;
    }

    /* DataTable controls */
    .amazon-card-body .dataTables_wrapper {
        padding: 0;
    }

    .amazon-card-body .dataTables_wrapper .dataTables_length,
    .amazon-card-body .dataTables_wrapper .dataTables_filter {
        padding: 12px 16px 0;
    }

    .amazon-card-body .dataTables_wrapper .dataTables_info,
    .amazon-card-body .dataTables_wrapper .dataTables_paginate {
        padding: 12px 16px;
    }

    .amazon-card-body .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #D5D9D9;
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 13px;
        transition: border-color 0.2s;
    }

    .amazon-card-body .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #FF9900;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255,153,0,0.15);
    }

    .amazon-card-body .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: #fff !important;
        border-radius: 4px;
    }

    .amazon-card-body .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #F7F8F8 !important;
        border-color: #D5D9D9 !important;
        color: #0F1111 !important;
    }

    /* Status badge */
    .amazon-status-active {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #E3F5E1;
        color: #067D62;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .amazon-status-inactive {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #FFEBE5;
        color: #B12704;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Action buttons */
    .amazon-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.1s ease-in-out;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid;
    }

    .amazon-action-btn.edit {
        background: #E7F4FF;
        color: #0066C0;
        border-color: #0066C0;
    }

    .amazon-action-btn.edit:hover {
        background: #0066C0;
        color: #fff;
    }

    .amazon-action-btn.delete {
        background: #FFEBE5;
        color: #B12704;
        border-color: #B12704;
    }

    .amazon-action-btn.delete:hover {
        background: #B12704;
        color: #fff;
    }

    /* Hide default box styling from widget component */
    .amazon-sca-container .box {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        margin-bottom: 0 !important;
    }

    .amazon-sca-container .box-header,
    .amazon-sca-container .box .box-header {
        display: none !important;
    }

    .amazon-sca-container .box-body {
        padding: 0 !important;
    }

    @media (max-width: 768px) {
        .amazon-sca-container {
            padding: 10px 12px 20px;
        }

        .amazon-sca-header-banner {
            padding: 16px 18px;
            flex-direction: column;
            align-items: flex-start;
        }

        .amazon-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media print {
        .amazon-sca-header-banner {
            background: #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border-radius: 0;
            box-shadow: none;
            padding: 16px 24px;
            margin: 0;
        }

        .amazon-sca-header-title,
        .amazon-sca-header-title i {
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
        }

        .amazon-sca-header-subtitle {
            color: #fff !important;
            -webkit-print-color-adjust: exact !important;
        }
    }
</style>
@endsection

@section('content')

<div class="amazon-sca-container">
    <div class="amazon-sca-header-banner amazon-theme-banner">
        <div class="amazon-sca-header-content">
            <h1 class="amazon-sca-header-title">
                <i class="fas fa-user-tie"></i>
                @lang('lang_v1.sales_commission_agents')
            </h1>
            <p class="amazon-sca-header-subtitle">
                Manage sales representatives and their commission rates, access locations, and payout settings.
            </p>
        </div>
    </div>

    @can('user.view')
    <div class="amazon-card">
        <div class="amazon-card-header">
            <div class="amazon-card-title">
                <h2>@lang('lang_v1.sales_commission_agents')</h2>
                <span class="amazon-agent-count" id="total_agents">0 agents</span>
            </div>
            @can('user.create')
            <a class="amazon-add-btn btn-modal"
                data-href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'create'])}}"
                data-container=".commission_agent_modal">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                @lang('messages.add')
            </a>
            @endcan
        </div>

        <div class="amazon-card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped nowrap" id="sales_commission_agent_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('user.name')</th>
                            <th>@lang('business.email')</th>
                            <th>@lang('lang_v1.contact_no')</th>
                            <th>@lang('business.address')</th>
                            <th>@lang('lang_v1.cmmsn_percent')</th>
                            <th>@lang('role.access_locations')</th>
                            <th>Status</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @endcan

    <div class="modal fade commission_agent_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>
</div>

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        var table = $('#sales_commission_agent_table');
        if ($.fn.DataTable.isDataTable(table)) {
            table.on('draw.dt', function() {
                var info = table.DataTable().page.info();
                $('#total_agents').text(info.recordsTotal + ' agent' + (info.recordsTotal !== 1 ? 's' : ''));
            });
        } else {
            var observer = new MutationObserver(function() {
                if ($.fn.DataTable.isDataTable(table)) {
                    observer.disconnect();
                    table.DataTable().on('draw.dt', function() {
                        var info = table.DataTable().page.info();
                        $('#total_agents').text(info.recordsTotal + ' agent' + (info.recordsTotal !== 1 ? 's' : ''));
                    });
                    var info = table.DataTable().page.info();
                    if (info) {
                        $('#total_agents').text(info.recordsTotal + ' agent' + (info.recordsTotal !== 1 ? 's' : ''));
                    }
                }
            });
            observer.observe(document.getElementById('sales_commission_agent_table'), { childList: true, subtree: true });
        }
    });
</script>
@endsection

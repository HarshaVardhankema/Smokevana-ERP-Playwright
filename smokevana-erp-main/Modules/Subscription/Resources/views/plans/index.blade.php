@extends('layouts.app')

@section('title', __('subscription::lang.subscription_plans'))

@section('content')
<style>
    .plans-page {
        background: #f3f3f3;
        min-height: calc(100vh - 120px);
        padding: 20px 0;
    }
    
    .page-header-card {
        background: linear-gradient(90deg, #232f3e 0%, #37475a 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(17, 153, 142, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header-card h1 {
        color: #fff;
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .page-header-card h1 .icon-box {
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 10px;
        display: flex;
    }
    
    .btn-add-plan {
        background: linear-gradient(180deg, #FFD814 0%, #FF9900 100%);
        color: #0F1111;
        border: 1px solid #FFA500;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .btn-add-plan:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(255, 153, 0, 0.4);
        color: #0F1111;
        background: linear-gradient(180deg, #FFE033 0%, #FFB020 100%);
    }
    
    .plans-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .plans-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 24px;
    }
    
    .plans-card .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .plans-card .card-header h3 i {
        color: #00a8e1;
    }
    
    .plans-table {
        margin: 0;
    }
    
    .plans-table thead th {
        background: #f8f9fa;
        border: none;
        padding: 14px 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c757d;
    }
    
    .plans-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid #f5f5f5;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .plans-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .plan-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .plan-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .plan-icon.prime {
        background: linear-gradient(135deg, #00a8e1, #0077a3);
        color: #fff;
    }
    
    .plan-icon.standard {
        background: linear-gradient(135deg, #485769, #232f3e);
        color: #fff;
    }
    
    .plan-details h6 {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0 0 4px 0;
    }
    
    .plan-details span {
        font-size: 12px;
        color: #6c757d;
    }
    
    .price-cell {
        font-size: 18px;
        font-weight: 700;
        color: #111;
    }
    
    .price-cell small {
        font-size: 12px;
        color: #6c757d;
        font-weight: 400;
    }
    
    .badge-status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #e2e3e5; color: #383d41; }
    .badge-prime { background: linear-gradient(135deg, #00a8e1, #0077a3); color: #fff; margin-left: 6px; }
    .badge-featured { background: #cce5ff; color: #004085; margin-left: 6px; }
    
    .subscribers-cell {
        text-align: center;
    }
    
    .subscribers-cell .count {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
    }
    
    .subscribers-cell .max {
        font-size: 12px;
        color: #6c757d;
    }
    
    .action-btns .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
        transition: all 0.2s ease;
    }
    
    .action-btns .btn-view {
        background: #e3f2fd;
        color: #ffae00;
        border: none;
    }
    
    .action-btns .btn-view:hover {
        background: #ffae00;
        color: #fff;
    }
    
    .action-btns .btn-edit {
        background: #e8f5e9;
        color: #388e3c;
        border: none;
    }
    
    .action-btns .btn-edit:hover {
        background: #388e3c;
        color: #fff;
    }
    
    .action-btns .btn-delete {
        background: #ffebee;
        color: #d32f2f;
        border: none;
    }
    
    .action-btns .btn-delete:hover {
        background: #d32f2f;
        color: #fff;
    }
    
    /* Empty state */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }
    
    .empty-state i {
        font-size: 60px;
        color: #e0e0e0;
        margin-bottom: 20px;
    }
    
    .empty-state h4 {
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #999;
        margin-bottom: 20px;
    }
</style>

<div class="plans-page">
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header-card">
            <h1>
                <div class="icon-box">
                    <i class="fas fa-tags"></i>
                </div>
                Subscription Plans
            </h1>
            @can('subscription.create')
            <a href="{{ route('subscription.plans.create') }}" class="btn btn-add-plan">
                <i class="fas fa-plus"></i> Create New Plan
            </a>
            @endcan
        </div>

        {{-- Plans Table Card --}}
        <div class="plans-card">
            <div class="card-header">
                <h3><i class="fas fa-layer-group"></i> All Plans</h3>
            </div>
            <div class="card-body p-0">
                <table class="table plans-table" id="plans_table" width="100%">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Price</th>
                            <th>Billing</th>
                            <th>Customer Group</th>
                            <th>Subscribers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    var plans_table = $('#plans_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('subscription.plans.data') }}",
        columns: [
            { 
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    var iconClass = row.is_prime ? 'prime' : 'standard';
                    var icon = row.is_prime ? 'fa-crown' : 'fa-tag';
                    return '<div class="plan-name-cell">' +
                           '<div class="plan-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>' +
                           '<div class="plan-details">' +
                           '<h6>' + data + '</h6>' +
                           '<span>' + (row.description ? row.description.substring(0, 40) + '...' : 'No description') + '</span>' +
                           '</div></div>';
                }
            },
            { 
                data: 'price_formatted', 
                name: 'price',
                render: function(data, type, row) {
                    return '<div class="price-cell">' + data + '<br><small>/' + row.billing_cycle + '</small></div>';
                }
            },
            { data: 'billing_info', name: 'billing_type' },
            { data: 'customer_group_name', name: 'customer_group_id' },
            { 
                data: 'subscribers_count', 
                name: 'current_subscribers',
                render: function(data, type, row) {
                    var parts = data.split(' / ');
                    var max = parts[1] ? '<span class="max">/ ' + parts[1] + '</span>' : '<span class="max">unlimited</span>';
                    return '<div class="subscribers-cell"><span class="count">' + parts[0] + '</span> ' + max + '</div>';
                }
            },
            { 
                data: 'status_badge', 
                name: 'is_active',
                render: function(data, type, row) {
                    var html = row.is_active 
                        ? '<span class="badge-status badge-active">Active</span>' 
                        : '<span class="badge-status badge-inactive">Inactive</span>';
                    if (row.is_prime) {
                        html += '<span class="badge-status badge-prime"><i class="fas fa-crown"></i> Prime</span>';
                    }
                    if (row.is_featured) {
                        html += '<span class="badge-status badge-featured">Featured</span>';
                    }
                    return html;
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var html = '<div class="action-btns">';
                    html += '<a href="{{ url("subscription/plans") }}/' + row.id + '" class="btn btn-view" title="View"><i class="fas fa-eye"></i></a>';
                    html += '<a href="{{ url("subscription/plans") }}/' + row.id + '/edit" class="btn btn-edit" title="Edit"><i class="fas fa-edit"></i></a>';
                    if (row.current_subscribers == 0) {
                        html += '<button type="button" class="btn btn-delete delete-plan" data-id="' + row.id + '" title="Delete"><i class="fas fa-trash"></i></button>';
                    }
                    html += '</div>';
                    return html;
                }
            }
        ],
        order: [[0, 'asc']],
        pageLength: 10,
        language: {
            emptyTable: '<div class="empty-state"><i class="fas fa-tags"></i><h4>No Plans Yet</h4><p>Create your first subscription plan to get started.</p></div>',
            processing: '<div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div>'
        }
    });

    // Delete plan
    $(document).on('click', '.delete-plan', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Delete Plan?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d32f2f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('subscription/plans') }}/" + id,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            plans_table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'Error deleting plan');
                    }
                });
            }
        });
    });
});
</script>
@endsection

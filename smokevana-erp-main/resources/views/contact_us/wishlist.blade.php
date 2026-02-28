@extends('layouts.app')

@section('title', 'Wishlist')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* ========== Wishlist Page – Amazon theme ========== */
.amazon-wishlist-page { background: #EAEDED; min-height: calc(100vh - 120px); padding: 20px 0; padding-bottom: 2rem; }
.amazon-wishlist-page .page-header-card {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e;
    border-radius: 10px;
    padding: 24px 32px !important;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.amazon-wishlist-page .page-header-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    opacity: 0.9;
}
.amazon-wishlist-page .page-header-card h1 {
    color: #fff !important; font-size: 1.5rem !important; font-weight: 700; margin: 0;
    display: flex; align-items: center; gap: 14px;
}
.amazon-wishlist-page .page-header-card h1 .icon-box {
    background: rgba(255,255,255,0.15); border-radius: 10px; padding: 10px; display: flex;
    color: #FF9900;
}
.amazon-wishlist-page .page-header-subtitle { font-size: 13px; color: #b8c4ce !important; margin: 4px 0 0 0; }

/* Content card – Amazon style */
.amazon-wishlist-page .content-card {
    background: #fff !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important;
    box-shadow: 0 2px 5px rgba(15,17,17,0.08) !important;
    overflow: hidden; margin-bottom: 24px;
}
.amazon-wishlist-page .content-card .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important;
    padding: 14px 20px !important;
    border-bottom: 2px solid #ff9900 !important;
}
.amazon-wishlist-page .content-card .box-title { color: #fff !important; font-weight: 600 !important; }
.amazon-wishlist-page .content-card .box-title i { color: #FF9900 !important; }

/* Table */
.amazon-wishlist-page #wishlist_table thead th {
    background: #232f3e !important;
    color: #fff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
    font-weight: 600;
    font-size: 13px;
}
.amazon-wishlist-page #wishlist_table tbody td {
    padding: 12px 14px;
    color: #0f1111;
    border-color: #e5e7eb;
    font-size: 13px;
}
.amazon-wishlist-page #wishlist_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.amazon-wishlist-page #wishlist_table tbody tr:hover td { background: #fff8e7 !important; }

/* DataTables buttons – Amazon orange */
.amazon-wishlist-page .dt-buttons .btn,
.amazon-wishlist-page .dt-buttons button {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    color: #0f1111 !important;
    border: 1px solid #a88734 !important;
    border-radius: 8px;
    font-weight: 600;
}
.amazon-wishlist-page .dt-buttons .btn:hover,
.amazon-wishlist-page .dt-buttons button:hover {
    opacity: 0.95;
    color: #0f1111 !important;
}

/* DataTables search / length / pagination */
.amazon-wishlist-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
    padding: 8px 12px;
}
.amazon-wishlist-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.amazon-wishlist-page .dataTables_wrapper .dataTables_length select {
    border: 1px solid #D5D9D9;
    border-radius: 6px;
}
.amazon-wishlist-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important;
    color: #0f1111 !important;
}
.amazon-wishlist-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: #ff9900;
    background: #fff8e7 !important;
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page amazon-wishlist-page">
    <div class="container-fluid">
        <div class="page-header-card amazon-theme-banner">
            <h1>
                <div class="icon-box"><i class="fas fa-heart"></i></div>
                Wishlist
            </h1>
            <p class="page-header-subtitle">Manage customer wishlist items</p>
        </div>

        <div class="content-card">
            <div class="box-body" style="padding: 1.25rem 1.5rem;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="wishlist_table">
                        <thead>
                            <tr>
                                {{-- <th>{{ __('Action') }}</th> --}}
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Product Image') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Created At') }}</th>
                                
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
    $(document).ready(function() {
        console.log('yyyyyy');
        $('#wishlist_table').DataTable({
    processing: true,
    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
    serverSide: true,
    ajax: "{{ action([\App\Http\Controllers\ECOM\WishlistsController::class, 'wishlist']) }}",
    columns: [
        { data: 'product_name', name: 'product_name' },
        {
            data: 'product_image',
            name: 'product_image',
            render: function(data, type, row) {
                return `<img src="${data}" alt="Product Image" width="50" height="50" />`;
            }
        },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'created_at', name: 'created_at' }
    ]
}); 
});
 </script>
@endsection


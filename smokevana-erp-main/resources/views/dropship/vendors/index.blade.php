@extends('layouts.app')
@section('title', 'Dropship Vendors')

@section('css')
<style>
    .dv-header-banner { background:#37475a;border-radius:6px;padding:22px 28px;margin-bottom:16px;box-shadow:0 3px 10px rgba(15,17,17,0.4); }
    .dv-header-banner .banner-title { display:flex;align-items:center;gap:10px;font-size:22px;font-weight:700;margin:0;color:#fff; }
    .dv-header-banner .banner-title i { color:#fff!important; }
    .dv-header-banner .banner-subtitle { font-size:13px;color:rgba(249,250,251,0.88);margin:4px 0 0 0; }
    .amazon-orange-add { background:linear-gradient(to bottom,#FF9900 0%,#E47911 100%)!important;border-color:#C7511F!important;color:white!important; }
    .amazon-orange-add:hover { color:white!important;opacity:0.95; }
</style>
@endsection

@section('content')
<section class="content-header">
    <div class="dv-header-banner">
        <h1 class="banner-title"><i class="fas fa-users"></i> Dropship Vendors</h1>
        <p class="banner-subtitle">Manage dropship vendors, product mappings, and portal access.</p>
    </div>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
            <div class="box-tools">
                <a href="{{ route('dropship.vendors.create') }}" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm amazon-orange-add">
                    <i class="fas fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        @endslot

        <!-- Filters -->
        <div class="row tw-mb-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Filter by Vendor Type:</label>
                    <select id="vendor_type_filter" class="form-control select2">
                        <option value="">All Vendor Types</option>
                        @foreach($vendorTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-9">
                <div class="alert alert-info tw-mt-6">
                    <i class="fas fa-info-circle"></i>
                    <strong>Vendor Types:</strong>
                    <span class="badge bg-secondary">ERP</span> Internal vendor |
                    <span class="badge bg-purple">WooCommerce</span> Auto-synced from WooCommerce (read-only) |
                    <span class="badge bg-info">Dropship</span> ERP portal fulfillment
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="vendors-table">
                <thead>
                    <tr>
                        <th>Vendor Name</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Products</th>
                        <th>Pending Orders</th>
                        <th>Markup %</th>
                        <th>Status</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('.select2').select2();

    var vendors_table = $('#vendors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("dropship.vendors.index") }}',
            data: function(d) {
                d.vendor_type = $('#vendor_type_filter').val();
            }
        },
        columns: [
            { data: 'display_name', name: 'name' },
            { data: 'vendor_type_badge', name: 'vendor_type' },
            { data: 'email', name: 'email' },
            { data: 'products_count', name: 'products_count', searchable: false },
            { data: 'pending_orders', name: 'pending_orders', searchable: false },
            { data: 'default_markup_percentage', name: 'default_markup_percentage' },
            { data: 'status_badge', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Filter by vendor type
    $('#vendor_type_filter').on('change', function() {
        vendors_table.ajax.reload();
    });

    $(document).on('click', '.delete-vendor', function(e) {
        e.preventDefault();
        var url = $(this).data('href');
        
        swal({
            title: LANG.sure,
            text: 'This will delete the vendor and all product mappings.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            vendors_table.ajax.reload();
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












       
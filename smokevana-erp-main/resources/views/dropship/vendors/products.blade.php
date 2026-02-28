@extends('layouts.app')
@section('title', 'Vendor Products - ' . $vendor->display_name)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-boxes"></i> Products: {{ $vendor->display_name }}
    </h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-lg-8">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Mapped Products'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Vendor Cost</th>
                                <th>Markup</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Primary</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>

        <div class="col-lg-4">
            @component('components.widget', ['class' => 'box-success', 'title' => 'Add Product Mapping'])
                <form id="add-mapping-form">
                    <div class="form-group">
                        {!! Form::label('product_id', 'Select Product *') !!}
                        {!! Form::select('product_id', $availableProducts, null, ['class' => 'form-control select2', 'id' => 'product_id', 'placeholder' => 'Search product...', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('vendor_cost_price', 'Vendor Cost Price') !!}
                        {!! Form::number('vendor_cost_price', null, ['class' => 'form-control', 'id' => 'vendor_cost_price', 'step' => '0.01', 'min' => '0']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('vendor_markup_percentage', 'Markup Percentage (%)') !!}
                        {!! Form::number('vendor_markup_percentage', $vendor->default_markup_percentage, ['class' => 'form-control', 'id' => 'vendor_markup_percentage', 'step' => '0.01', 'min' => '0']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('vendor_sku', 'Vendor SKU') !!}
                        {!! Form::text('vendor_sku', null, ['class' => 'form-control', 'id' => 'vendor_sku']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('lead_time_days', 'Lead Time (Days)') !!}
                        {!! Form::number('lead_time_days', 0, ['class' => 'form-control', 'id' => 'lead_time_days', 'min' => '0']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('min_order_qty', 'Min Order Qty') !!}
                        {!! Form::number('min_order_qty', 1, ['class' => 'form-control', 'id' => 'min_order_qty', 'min' => '1']) !!}
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_primary_vendor', 1, false, ['id' => 'is_primary_vendor']) !!}
                                Set as primary vendor
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-w-full">
                        <i class="fas fa-plus"></i> Add Product Mapping
                    </button>
                </form>
            @endcomponent

            @component('components.widget', ['class' => 'box-info', 'title' => 'Vendor Summary'])
                <div class="tw-space-y-2">
                    <div class="tw-flex tw-justify-between">
                        <span>Total Products:</span>
                        <span class="tw-font-semibold">{{ $vendor->products()->count() }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Active Products:</span>
                        <span class="tw-font-semibold text-success">{{ $vendor->activeProducts()->count() }}</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Default Markup:</span>
                        <span class="tw-font-semibold">{{ $vendor->default_markup_percentage }}%</span>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<!-- Edit Mapping Modal -->
<div class="modal fade" id="edit-mapping-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Product Mapping</h4>
            </div>
            <form id="edit-mapping-form">
                <div class="modal-body">
                    <input type="hidden" id="edit_product_id">
                    <div class="form-group">
                        {!! Form::label('edit_vendor_cost_price', 'Vendor Cost Price') !!}
                        {!! Form::number('edit_vendor_cost_price', null, ['class' => 'form-control', 'id' => 'edit_vendor_cost_price', 'step' => '0.01', 'min' => '0']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('edit_vendor_markup_percentage', 'Markup Percentage (%)') !!}
                        {!! Form::number('edit_vendor_markup_percentage', null, ['class' => 'form-control', 'id' => 'edit_vendor_markup_percentage', 'step' => '0.01', 'min' => '0']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('edit_vendor_sku', 'Vendor SKU') !!}
                        {!! Form::text('edit_vendor_sku', null, ['class' => 'form-control', 'id' => 'edit_vendor_sku']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('edit_lead_time_days', 'Lead Time (Days)') !!}
                        {!! Form::number('edit_lead_time_days', null, ['class' => 'form-control', 'id' => 'edit_lead_time_days', 'min' => '0']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('edit_status', 'Status') !!}
                        {!! Form::select('edit_status', [
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'out_of_stock' => 'Out of Stock'
                        ], null, ['class' => 'form-control', 'id' => 'edit_status']) !!}
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('edit_is_primary_vendor', 1, false, ['id' => 'edit_is_primary_vendor']) !!}
                                Set as primary vendor
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tw-dw-btn tw-dw-btn-ghost" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="tw-dw-btn tw-dw-btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('.select2').select2();
    var vendorId = {{ $vendor->id }};

    var productsTable = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("dropship.vendors.products", $vendor->id) }}',
        columns: [
            { data: 'name', name: 'products.name' },
            { data: 'sku', name: 'products.sku' },
            { data: 'vendor_cost', name: 'vendor_cost', orderable: false },
            { data: 'markup', name: 'markup', orderable: false },
            { data: 'selling_price', name: 'selling_price', orderable: false },
            { data: 'status', name: 'status' },
            { data: 'is_primary', name: 'is_primary' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Add mapping
    $('#add-mapping-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("dropship.vendors.add-product", $vendor->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: $('#product_id').val(),
                vendor_cost_price: $('#vendor_cost_price').val(),
                vendor_markup_percentage: $('#vendor_markup_percentage').val(),
                vendor_sku: $('#vendor_sku').val(),
                lead_time_days: $('#lead_time_days').val(),
                min_order_qty: $('#min_order_qty').val(),
                is_primary_vendor: $('#is_primary_vendor').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    productsTable.ajax.reload();
                    $('#add-mapping-form')[0].reset();
                    $('#product_id').val('').trigger('change');
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('Failed to add mapping');
            }
        });
    });

    // Edit mapping
    $(document).on('click', '.edit-mapping', function() {
        var productId = $(this).data('product-id');
        $('#edit_product_id').val(productId);
        $('#edit-mapping-modal').modal('show');
    });

    $('#edit-mapping-form').on('submit', function(e) {
        e.preventDefault();
        var productId = $('#edit_product_id').val();
        
        $.ajax({
            url: '{{ url("dropship/vendors") }}/' + vendorId + '/products/' + productId,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                vendor_cost_price: $('#edit_vendor_cost_price').val(),
                vendor_markup_percentage: $('#edit_vendor_markup_percentage').val(),
                vendor_sku: $('#edit_vendor_sku').val(),
                lead_time_days: $('#edit_lead_time_days').val(),
                status: $('#edit_status').val(),
                is_primary_vendor: $('#edit_is_primary_vendor').is(':checked') ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.msg);
                    productsTable.ajax.reload();
                    $('#edit-mapping-modal').modal('hide');
                } else {
                    toastr.error(response.msg);
                }
            },
            error: function() {
                toastr.error('Failed to update mapping');
            }
        });
    });

    // Remove mapping
    $(document).on('click', '.remove-mapping', function() {
        var productId = $(this).data('product-id');
        
        swal({
            title: 'Remove Mapping?',
            text: 'This product will be unmapped from this vendor.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ url("dropship/vendors") }}/' + vendorId + '/products/' + productId,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            productsTable.ajax.reload();
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        });
    });
});
</script>
@endsection













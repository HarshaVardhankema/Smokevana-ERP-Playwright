<div class="modal-dialog modal-xl no-print" id='sell_pick_verify_data'>
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sell_pick_verify_data_title">Sell Pick Verify Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Ordered Quantity</th>
                                    <th>Picked Quantity</th>
                                    <th>Shorted Quantity</th>
                                    <th>Verified Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pickingOrders->sell_lines as $sellLine)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if (!empty($sellLine->variations->media) || !empty($sellLine->product->image_url))
                                            <img width="50" height="50"
                                                src="{{ $sellLine->variations->media[0]->display_url ?? ($sellLine->product->image_url ?? '') }}"
                                                alt="Product Image" class="tw-w-16 tw-h-16 tw-object-cover">
                                            @else
                                                <img width="50" height="50"
                                                    src="{{ asset('images/default-product.png') }}" alt="Product Image"
                                                    class="tw-w-16 tw-h-16 tw-object-cover">
                                            @endif
                                        </td>
                                        <td>
                                            <b
                                                data-sub-sku="{{ $sellLine->variations->sub_sku ?? '' }}">{{ $sellLine->variations->sub_sku ?? '' }}</b>
                                            <b
                                                data-barcode="{{ $sellLine->variations->var_barcode_no ?? '' }}">{{ $sellLine->variations->var_barcode_no ?? '' }}</b>
                                            {{ $sellLine->product->name }}
                                            @if (!empty($sellLine->variations->name) && $sellLine->variations->name !== 'DUMMY')
                                                ( <b style="font-size: 12px;"><i>{{ $sellLine->variations->name }} </i></b>)
                                            @endif
                                        </td>
                                        <td>{{ $sellLine->ordered_quantity }}</td>
                                        <td>{{ $sellLine->picked_quantity }}</td>
                                        <td>{{ $sellLine->shorted_picked_qty }}</td>
                                        <td>{{ $sellLine->verified_qty }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
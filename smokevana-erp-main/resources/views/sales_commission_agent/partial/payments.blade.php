<div class="row">
    <div class="col-md-12">
        <!-- Commission Agent Payments Table -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-money-bill-alt"></i> Commission Agent Payments
                </h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="commission_payments_table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference No</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($commission_payouts) && $commission_payouts->count() > 0)
                                @foreach($commission_payouts as $payout)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payout->transaction_date)->format('M d, Y') }}</td>
                                        <td>{{ $payout->ref_no }}</td>
                                        <td>${{ number_format($payout->final_total, 2) }}</td>
                                        <td>
                                            @if($payout->payment_lines && $payout->payment_lines->count() > 0)
                                                {{ ucfirst(str_replace('_', ' ', $payout->payment_lines->first()->method)) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($payout->payment_status == 'paid')
                                                <span class="label label-success">{{ ucfirst($payout->payment_status) }}</span>
                                            @else
                                                <span class="label label-warning">{{ ucfirst($payout->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $payout->additional_notes ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-xs view_payment_modal" data-id="{{ $payout->id }}">
                                                    <i class="fa fa-eye"></i> View
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No commission payments found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize DataTable
            if ($('#commission_payments_table').length) {
                $('#commission_payments_table').DataTable({
                    "pageLength": 25,
                    "order": [[0, "desc"]]
                });
            }


            console.log('commission_payments_table');
        });

        $(document).on('click', '.view_payment_modal', function () {
            var url = $(this).data('href');
            var container = $('.view_modal');
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'html',
                success: function (result) {
                    $(container)
                        .html(result)
                        .modal('show');
                    __currency_convert_recursively(container);
                },
            });
        });

    </script>
@endsection --}}
@extends('layouts.app')
@section('title', __('lang_v1.credit_lines'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.credit-line-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.credit-line-header-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    border-radius: 0 0 10px 10px;
    padding: 22px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.credit-line-header-banner.amazon-theme-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #ff9900;
    z-index: 1;
}
.credit-line-header-banner .banner-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: #fff !important;
}
.credit-line-header-banner .banner-title i { color: #fff !important; }
.credit-line-header-banner .banner-subtitle {
    font-size: 13px;
    color: rgba(255,255,255,0.9) !important;
    margin: 4px 0 0 0;
}
.credit-line-header-banner .banner-actions .btn-primary {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 2px solid #C7511F !important;
    color: #fff !important;
    font-weight: 600;
}
.credit-line-header-banner .banner-actions .btn-primary:hover {
    color: #fff !important;
    opacity: 0.95;
    border-color: #E47911 !important;
}
.credit-line-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
.credit-line-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
.credit-line-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
.credit-line-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
</style>
@endsection

@section('content')
<!-- Amazon-style banner -->
<section class="content-header no-print">
    <div class="credit-line-header-banner amazon-theme-banner">
        <div>
            <h1 class="banner-title"><i class="fas fa-credit-card"></i> Credit Line Management</h1>
            <p class="banner-subtitle">View and manage credit applications and approved limits</p>
        </div>
        <div class="banner-actions">
            <a href="{{ route('credit-lines.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Create Credit Application
            </a>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content credit-line-page">
    @component('components.widget', ['class' => 'box-primary', 'title' =>"Credit Lines"])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="credit_lines_table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Requested Credit Amount</th>
                        <th>Approved Credit Limit</th>
                        <th>Average Revenue Per Month</th>
                        <th>Status</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade credit_line_modal" tabindex="-1" role="dialog" 
         aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        //Credit Lines table
        var credit_lines_table = $('#credit_lines_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\CreditLineController::class, 'index']) }}",
                data: function(d) {
                }
            },
            columnDefs: [
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                }
            ],
            columns: [
                { data: 'customer_name', name: 'customer_name' },
                { data: 'requested_credit_amount', name: 'requested_credit_amount' },
                { data: 'approved_credit_limit', name: 'approved_credit_limit' },
                { data: 'average_revenue_per_month', name: 'average_revenue_per_month' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action' }
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#credit_lines_table'));
            }
        });

        $(document).on('click', 'a.delete_credit_line', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_credit_line,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                credit_lines_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        // Handle approve button click
        $(document).on('click', 'a.approve_credit_line_button', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            // Navigate to the approval form page
            window.location.href = href;
        });

        // Handle view button click
        $(document).on('click', 'a.view_credit_line_button', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            // Navigate to the view details page
            window.location.href = href;
        });

        // Handle reject button click
        $(document).on('click', 'a.reject_credit_line_button', function(e) {
            e.preventDefault();
            swal({
                title: 'Are you sure?',
                text: 'Are you sure you want to reject this credit application?',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willReject) => {
                if (willReject) {
                    var href = $(this).attr('href');
                    $.ajax({
                        url: href,
                        method: 'GET',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                credit_lines_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        });

        // Handle modal buttons
        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            var container = $(this).data('container');
            
            $.ajax({
                url: href,
                dataType: 'html',
                success: function(result) {
                    $(container).html(result).modal('show');
                }
            });
        });
    });
</script>
@endsection

@section('css')
<style>
.label-success {
    background-color: #5cb85c !important;
    color: white !important;
}

.label-default {
    background-color: #777 !important;
    color: white !important;
}

.table th:nth-child(3) {
    text-align: center;
}

.table td:nth-child(3) {
    text-align: center;
}
</style>
@endsection

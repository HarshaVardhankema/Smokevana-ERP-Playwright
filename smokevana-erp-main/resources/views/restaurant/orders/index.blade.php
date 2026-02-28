@extends('layouts.restaurant')
@section('title', __( 'restaurant.orders' ))

@section('css')
<style>
/* Amazon Theme - All Orders Page */
.amazon-orders-page {
    background: #EAEDED;
    min-height: 100vh;
    padding: 20px 24px;
}

/* Banner */
.amazon-orders-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    padding: 0;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
    overflow: hidden;
}

.amazon-orders-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
    width: 100%;
}

.amazon-orders-banner__content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-orders-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-orders-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-orders-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

.amazon-orders-banner__actions .btn-refresh-amz {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: 1px solid #e47911 !important;
    border-radius: 6px !important;
    padding: 8px 18px !important;
    font-weight: 600;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(255, 153, 0, 0.35);
    transition: all 0.2s ease;
    cursor: pointer;
}

.amazon-orders-banner__actions .btn-refresh-amz:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.45);
    transform: translateY(-1px);
}

.amazon-orders-banner__actions .btn-refresh-amz svg {
    width: 18px;
    height: 18px;
}

/* Card Sections */
.amazon-orders-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    border: 1px solid #d5d9d9;
    overflow: hidden;
    margin-bottom: 16px;
}

.amazon-orders-card__header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 3px solid #ff9900;
}

.amazon-orders-card__header i {
    color: #ff9900;
    font-size: 16px;
}

.amazon-orders-card__header h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.amazon-orders-card__body {
    padding: 16px 20px;
}

.amazon-orders-card__body .overlay {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 0 0 10px 10px;
}

/* Service Staff Filter Card */
.amazon-filter-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    border: 1px solid #d5d9d9;
    padding: 16px 20px;
    margin-bottom: 16px;
}

.amazon-filter-card .input-group-addon {
    background: #232f3e;
    color: #ff9900;
    border: 1px solid #37475a;
    border-radius: 6px 0 0 6px;
}

.amazon-filter-card .form-control {
    border: 1px solid #d5d9d9;
    border-radius: 0 6px 6px 0;
    height: 38px;
    font-size: 13px;
}

.amazon-filter-card .form-control:focus {
    border-color: #ff9900;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.15);
}

.amazon-filter-card .select2-container--default .select2-selection--single {
    border: 1px solid #d5d9d9;
    border-radius: 0 6px 6px 0;
    height: 38px;
}

.amazon-filter-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    font-size: 13px;
}

/* Order Card Items */
.amazon-orders-card__body .order_div .small-box {
    background: #ffffff !important;
    border: 1px solid #d5d9d9;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(15, 17, 17, 0.08);
    margin-bottom: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.amazon-orders-card__body .order_div .small-box:hover {
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.15);
    transform: translateY(-2px);
    border-color: #ff9900;
}

.amazon-orders-card__body .small-box .inner {
    padding: 14px 16px 10px;
}

.amazon-orders-card__body .small-box .inner h4 {
    color: #0f1111;
    font-weight: 700;
    font-size: 16px;
    margin: 0 0 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid #ff9900;
}

.amazon-orders-card__body .small-box .inner .table th {
    color: #565959;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    padding: 5px 4px;
    border: none;
}

.amazon-orders-card__body .small-box .inner .table td {
    color: #0f1111;
    font-size: 13px;
    font-weight: 500;
    padding: 5px 4px;
    border: none;
}

.amazon-orders-card__body .small-box .label {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 4px;
}

.amazon-orders-card__body .small-box .mark_as_served_btn,
.amazon-orders-card__body .small-box .mark_as_cooked_btn {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    font-size: 13px;
    padding: 10px;
    border-radius: 0;
    transition: all 0.2s ease;
}

.amazon-orders-card__body .small-box .mark_as_served_btn:hover,
.amazon-orders-card__body .small-box .mark_as_cooked_btn:hover {
    background: #e47911 !important;
}

.amazon-orders-card__body .small-box .btn-modal.bg-info {
    background: #232f3e !important;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    font-size: 12px;
    padding: 8px;
    border-radius: 0 0 8px 8px;
    transition: all 0.2s ease;
}

.amazon-orders-card__body .small-box .btn-modal.bg-info:hover {
    background: #37475a !important;
}

/* Line Orders Table */
.amazon-orders-card__body .table-responsive {
    border-radius: 6px;
    overflow: hidden;
}

.amazon-orders-card__body table.table thead th {
    background: #232f3e !important;
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 10px 12px;
    border: none;
}

.amazon-orders-card__body table.table tbody td {
    font-size: 13px;
    color: #0f1111;
    padding: 10px 12px;
    border-bottom: 1px solid #e7e7e7;
    vertical-align: middle;
}

.amazon-orders-card__body table.table tbody tr:hover td {
    background: #fef8f0;
}

/* Mark as served links in line orders */
.amazon-orders-card__body a.mark_line_order_as_served {
    color: #ff9900 !important;
    font-weight: 600;
    transition: color 0.2s ease;
}

.amazon-orders-card__body a.mark_line_order_as_served:hover {
    color: #e47911 !important;
}

/* Print button */
.amazon-orders-card__body .print_line_order {
    color: #232f3e !important;
    transition: color 0.2s ease;
}

.amazon-orders-card__body .print_line_order:hover {
    color: #ff9900 !important;
}

/* Empty state */
.amazon-orders-card__body h4.text-center,
.amazon-orders-card__body .col-md-12 h4 {
    color: #565959;
    font-size: 15px;
    font-weight: 500;
    padding: 30px 0;
}
</style>
@endsection

@section('content')

<!-- Main content -->
<div class="amazon-orders-page">
    
    <!-- Amazon Banner -->
    <div class="amazon-orders-banner">
        <div class="amazon-orders-banner__stripe"></div>
        <div class="amazon-orders-banner__content">
            <div>
                <h1 class="amazon-orders-banner__title">
                    <i class="fas fa-clipboard-list"></i>
                    @lang('restaurant.all_orders')
                    @show_tooltip(__('lang_v1.tooltip_serviceorder'))
                </h1>
                <p class="amazon-orders-banner__subtitle">View and manage all service orders</p>
            </div>
            <div class="amazon-orders-banner__actions">
                <button type="button" class="btn-refresh-amz" id="refresh_orders">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                    @lang('restaurant.refresh')
                </button>
            </div>
        </div>
    </div>

    @if(!$is_service_staff)
    <!-- Service Staff Filter -->
    <div class="amazon-filter-card">
        <div class="col-sm-6" style="padding: 0;">
            {!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\OrderController::class, 'index']), 'method' => 'get', 'id' => 'select_service_staff_form' ]) !!}
            <div class="form-group" style="margin-bottom: 0;">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-user-secret"></i>
                    </span>
                    {!! Form::select('service_staff', $service_staff, request()->service_staff, ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_service_staff'), 'id' => 'service_staff_id']); !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="clearfix"></div>
    </div>
    @endif

    <!-- Line Orders Card -->
    <div class="amazon-orders-card">
        <div class="amazon-orders-card__header">
            <i class="fas fa-list-alt"></i>
            <h4>@lang('lang_v1.line_orders')</h4>
        </div>
        <div class="amazon-orders-card__body">
            <input type="hidden" id="orders_for" value="waiter">
            <div class="row" id="line_orders_div">
                @include('restaurant.partials.line_orders', array('orders_for' => 'waiter'))   
            </div>
            <div class="overlay hide">
                <i class="fas fa-sync fa-spin"></i>
            </div>
        </div>
    </div>

    <!-- All Your Orders Card -->
    <div class="amazon-orders-card">
        <div class="amazon-orders-card__header">
            <i class="fas fa-shopping-bag"></i>
            <h4>@lang('restaurant.all_your_orders')</h4>
        </div>
        <div class="amazon-orders-card__body">
            <input type="hidden" id="orders_for" value="waiter">
            <div class="row" id="orders_div">
                @include('restaurant.partials.show_orders', array('orders_for' => 'waiter'))   
            </div>
            <div class="overlay hide">
                <i class="fas fa-sync fa-spin"></i>
            </div>
        </div>
    </div>

</div>
<!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
        $('select#service_staff_id').change( function(){
            $('form#select_service_staff_form').submit();
        });
        $(document).ready(function(){
            $(document).on('click', 'a.mark_as_served_btn', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "info",
                  buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var _this = $(this);
                        var href = _this.data('href');
                        $.ajax({
                            method: "GET",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    refresh_orders();
                                    toastr.success(result.msg);
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', 'a.mark_line_order_as_served', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "info",
                  buttons: true,
                }).then((sure) => {
                    if (sure) {
                        var _this = $(this);
                        var href = _this.attr('href');
                        $.ajax({
                            method: "GET",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    refresh_orders();
                                    toastr.success(result.msg);
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $('.print_line_order').click( function(){
                let data = {
                    'line_id' : $(this).data('id'),
                    'service_staff_id' : $("#service_staff_id").val()
                };
                $.ajax({
                    method: "GET",
                    url: '/modules/print-line-order',
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if (result.success == 1 && result.html_content != '') {
                            $('#receipt_section').html(result.html_content);
                            __print_receipt('receipt_section');
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });
        });
    </script>
@endsection
@extends('layouts.restaurant')
@section('title', __('restaurant.kitchen'))

@section('css')
<style>
/* Amazon Theme - Kitchen Orders Page */
.amazon-kitchen-container {
    background: #EAEDED;
    min-height: 100vh;
    padding: 20px 24px;
}

/* Banner */
.amazon-kitchen-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 8px;
    padding: 0;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.3);
    overflow: hidden;
}

.amazon-kitchen-banner__stripe {
    height: 4px;
    background: linear-gradient(90deg, #ff9900, #ffad33, #ff9900);
    width: 100%;
}

.amazon-kitchen-banner__content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.amazon-kitchen-banner__title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
}

.amazon-kitchen-banner__title i {
    color: #ff9900;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(255, 153, 0, 0.4));
}

.amazon-kitchen-banner__subtitle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0 36px;
}

.amazon-kitchen-banner__actions .btn-refresh {
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
}

.amazon-kitchen-banner__actions .btn-refresh:hover {
    background: #e47911 !important;
    box-shadow: 0 4px 10px rgba(255, 153, 0, 0.45);
    transform: translateY(-1px);
}

.amazon-kitchen-banner__actions .btn-refresh svg {
    width: 18px;
    height: 18px;
}

/* Orders Card */
.amazon-kitchen-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(15, 17, 17, 0.08);
    border: 1px solid #d5d9d9;
    overflow: hidden;
    padding: 20px;
}

.amazon-kitchen-card .overlay {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
}

/* Order Cards Grid */
#orders_div {
    margin: 0 -8px;
}

#orders_div .order_div {
    padding: 8px;
}

/* Individual Order Box */
#orders_div .small-box {
    background: #ffffff !important;
    border: 1px solid #d5d9d9;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(15, 17, 17, 0.08);
    margin-bottom: 0;
    overflow: hidden;
    transition: all 0.2s ease;
}

#orders_div .small-box:hover {
    box-shadow: 0 4px 12px rgba(15, 17, 17, 0.15);
    transform: translateY(-2px);
    border-color: #ff9900;
}

#orders_div .small-box .inner {
    padding: 14px 16px 10px;
}

#orders_div .small-box .inner h4 {
    color: #0f1111;
    font-weight: 700;
    font-size: 16px;
    margin: 0 0 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid #ff9900;
}

#orders_div .small-box .inner .table {
    margin: 0;
}

#orders_div .small-box .inner .table th {
    color: #565959;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    padding: 5px 4px;
    border: none;
    white-space: nowrap;
}

#orders_div .small-box .inner .table td {
    color: #0f1111;
    font-size: 13px;
    font-weight: 500;
    padding: 5px 4px;
    border: none;
}

/* Status Labels */
#orders_div .small-box .label {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 4px;
}

/* Mark as Cooked Button */
#orders_div .small-box .mark_as_cooked_btn {
    background: #ff9900 !important;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    font-size: 13px;
    padding: 10px;
    border-radius: 0;
    transition: all 0.2s ease;
}

#orders_div .small-box .mark_as_cooked_btn:hover {
    background: #e47911 !important;
}

/* Order Details Button */
#orders_div .small-box .btn-modal.bg-info {
    background: #232f3e !important;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    font-size: 12px;
    padding: 8px;
    border-radius: 0 0 8px 8px;
    transition: all 0.2s ease;
}

#orders_div .small-box .btn-modal.bg-info:hover {
    background: #37475a !important;
}

/* Gray footer for non-kitchen */
#orders_div .small-box .small-box-footer.bg-gray {
    background: #f0f2f2 !important;
    height: 38px;
}

/* Empty State */
#orders_div > .col-md-12 > h4 {
    color: #565959;
    font-size: 16px;
    font-weight: 500;
    padding: 40px 0;
}
</style>
@endsection

@section('content')
    <!-- Main content -->
    <div class="amazon-kitchen-container">
        <!-- Amazon Banner -->
        <div class="amazon-kitchen-banner">
            <div class="amazon-kitchen-banner__stripe"></div>
            <div class="amazon-kitchen-banner__content">
                <div>
                    <h1 class="amazon-kitchen-banner__title">
                        <i class="fas fa-utensils"></i>
                        @lang('restaurant.all_orders') - @lang('restaurant.kitchen')
                        @show_tooltip(__('lang_v1.tooltip_kitchen'))
                    </h1>
                    <p class="amazon-kitchen-banner__subtitle">Manage and track kitchen orders in real-time</p>
                </div>
                <div class="amazon-kitchen-banner__actions">
                    <button type="button" class="btn-refresh" id="refresh_orders">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                        @lang('restaurant.refresh')
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="amazon-kitchen-card">
            <input type="hidden" id="orders_for" value="kitchen">
            <div class="row" id="orders_div">
                @include('restaurant.partials.show_orders', ['orders_for' => 'kitchen'])
            </div>
            <div class="overlay hide">
                <i class="fas fa-sync fa-spin"></i>
            </div>
        </div>
    </div>
    <!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', 'a.mark_as_cooked_btn', function(e) {
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
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    _this.closest('.order_div').remove();
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

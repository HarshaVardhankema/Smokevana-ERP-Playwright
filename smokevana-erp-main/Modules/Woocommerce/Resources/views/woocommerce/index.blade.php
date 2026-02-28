@extends('layouts.app')
@section('title', __('woocommerce::lang.woocommerce'))

@section('content')
    @include('woocommerce::layouts.nav')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('woocommerce::lang.woocommerce')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @php
            $is_superadmin = auth()->user()->can('superadmin');
        @endphp
        <div class="row">
            @if (!empty($alerts['connection_failed']))
                <div class="col-sm-12">
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <ul>
                            <li>{{ $alerts['connection_failed'] }}</li>
                        </ul>
                    </div>
                </div>
            @endif
            <div class="col-sm-6">
                @if ($is_superadmin || auth()->user()->can('woocommerce.syc_categories'))
                    <div class="col-sm-12">
                        <div
                            class="tw-transition-all lg:tw-col-span-1 tw-mb-4 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw-translate-y-0.5 tw-ring-gray-200">
                            <div class="tw-p-4 sm:tw-p-5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                        <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                            <h3
                                                class="tw-text-base tw-font-medium tw-tracking-tight tw-text-gray-900 tw-truncate tw-whitespace-nowrap sm:tw-text-lg lg:tw-text-xl">
                                                @lang('woocommerce::lang.sync_product_categories'):
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="tw-flow-root tw-mt-5 tw-border-gray-200">
                                    <div class="tw-mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                        <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                            @if (!empty($alerts['not_synced_cat']) || !empty($alerts['updated_cat']))
                                                <div class="col-sm-12">
                                                    <div class="alert alert-warning alert-dismissible">
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-hidden="true">×</button>
                                                        <ul>
                                                            @if (!empty($alerts['not_synced_cat']))
                                                                <li>{{ $alerts['not_synced_cat'] }}</li>
                                                            @endif
                                                            @if (!empty($alerts['updated_cat']))
                                                                <li>{{ $alerts['updated_cat'] }}</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-sm-6">
                                                {{-- upload from ERP --}}
                                                <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm tw-dw-btn-wide"
                                                    id="sync_product_categories"> <i class="fa fa-upload"></i>
                                                    @lang('woocommerce::lang.sync')</button>
                                                <span class="last_sync_cat"></span>

                                                <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm tw-dw-btn-wide"
                                                    id="sync_product_categories_from_woo"> <i class="fa fa-download"></i>
                                                    Sync from WooCommerce</button>
                                                <span class="last_sync_cat_from_woo"></span>
                                            </div>
                                            <div class="col-sm-12">
                                                <br>
                                                <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error" id="reset_categories">
                                                    <i class="fa fa-undo"></i> @lang('woocommerce::lang.reset_synced_cat')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- brand sync --}}
                            <div class="tw-transition-all lg:tw-col-span-1 tw-mb-4 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw-translate-y-0.5 tw-ring-gray-200">
                                <div class="tw-p-4 sm:tw-p-5">
                                    <div class="tw-flex tw-items-center tw-gap-2.5">
                                        <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                            <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                                <h3
                                                    class="tw-text-base tw-font-medium tw-tracking-tight tw-text-gray-900 tw-truncate tw-whitespace-nowrap sm:tw-text-lg lg:tw-text-xl">
                                                    Sync Product Brands
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tw-flow-root tw-mt-5 tw-border-gray-200">
                                        <div class="tw-mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                            <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                                <div class="col-sm-6">
                                                    <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm tw-dw-btn-wide"
                                                        id="sync_product_brands"> <i class="fa fa-upload"></i>
                                                        @lang('woocommerce::lang.sync')</button>
                                                    <span class="last_sync_brand"></span>

                                                    <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm tw-dw-btn-wide"
                                                        id="sync_product_brands_from_woo"> <i class="fa fa-download"></i>
                                                        Sync</button>
                                                    <span class="last_sync_brand_from_woo"></span>
                                                </div>
                                                <div class="col-sm-12">
                                                    <br>
                                                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error" id="reset_brands">
                                                        <i class="fa fa-undo"></i> Reset Synced Brands</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                @endif
                @if ($is_superadmin || auth()->user()->can('woocommerce.map_tax_rates'))
                    <div class="col-sm-12">
                        <div
                            class="tw-transition-all lg:tw-col-span-1 tw-mb-4 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw-translate-y-0.5 tw-ring-gray-200">
                            <div class="tw-p-4 sm:tw-p-5">
                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                    <div class="tw-flex tw-items-center tw-flex-1 tw-min-w-0 tw-gap-1">
                                        <div class="tw-w-full sm:tw-w-1/2 md:tw-w-1/2">
                                            <h3
                                                class="tw-text-base tw-font-medium tw-tracking-tight tw-text-gray-900 tw-truncate tw-whitespace-nowrap sm:tw-text-lg lg:tw-text-xl">
                                                @lang('woocommerce::lang.map_tax_rates'):
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="tw-flow-root tw-mt-5 tw-border-gray-200">
                                    <div class="tw-mx-4 tw--my-2 tw-overflow-x-auto sm:tw--mx-5">
                                        <div class="tw-inline-block tw-min-w-full tw-py-2 tw-align-middle sm:tw-px-5">
                                            <div class="">
                                                {!! Form::open([
                                                    'action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@mapTaxRates',
                                                    'method' => 'post',
                                                ]) !!}
                                                <div class="col-xs-12">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang('woocommerce::lang.pos_tax_rate')</th>
                                                                <th>@lang('woocommerce::lang.equivalent_woocommerce_tax_rate')</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if (!empty($tax_rates))
                                                                @foreach ($tax_rates as $tax_rate)
                                                                    <tr>
                                                                        <td>{{ $tax_rate->name }}:</td>
                                                                        <td>{!! Form::select('taxes[' . $tax_rate->id . ']', $woocommerce_tax_rates, $tax_rate->woocommerce_tax_rate_id, [
                                                                            'class' => 'form-control',
                                                                        ]) !!}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <button type="submit"
                                                                        class="tw-dw-btn tw-dw-btn-error tw-text-white pull-right">
                                                                        @lang('messages.save')
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($is_superadmin || auth()->user()->can('woocommerce.sync_products'))
                    <div class="col-sm-12">
                        @component('components.widget', [
                            'class' => '',
                            'title' => __('woocommerce::lang.sync_products') . ':',
                        ])
                            <div class="">
                                @if (!empty($alerts['not_synced_product']) || !empty($alerts['not_updated_product']))
                                    <div class="col-sm-12">
                                        <div class="alert alert-warning alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <ul>
                                                @if (!empty($alerts['not_synced_product']))
                                                    <li>{{ $alerts['not_synced_product'] }}</li>
                                                @endif
                                                @if (!empty($alerts['not_updated_product']))
                                                    <li>{{ $alerts['not_updated_product'] }}</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-6">
                                    <div style="display: inline-flex; width: 100%;">
                                        <button type="button" class="tw-dw-btn tw-dw-btn-warning tw-text-white tw-dw-btn-sm sync_products"
                                            data-sync-type="new"> <i class="fa fa-upload"></i> @lang('woocommerce::lang.sync_only_new')</button>
                                        &nbsp;@show_tooltip(__('woocommerce::lang.sync_new_help'))
                                    </div>
                                    <span class="last_sync_new_products"></span>
                                </div>
                                <div class="col-sm-6">
                                    <div style="display: inline-flex; width: 100%;">
                                        <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm sync_products"
                                            data-sync-type="all"> <i class="fa fa-upload"></i> @lang('woocommerce::lang.sync_all')</button>
                                        &nbsp;@show_tooltip(__('woocommerce::lang.sync_all_help'))
                                    </div>
                                    <span class="last_sync_all_products"></span>
                                </div>
                                <div class="col-sm-12">
                                    <br>
                                    <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error" id="reset_products"> <i
                                            class="fa fa-undo"></i> @lang('woocommerce::lang.reset_synced_products')</button>
                                </div>
                            </div>
                        @endcomponent
                    </div>
                @endif
                @if ($is_superadmin || auth()->user()->can('woocommerce.sync_products'))
                    <div class="col-sm-12">
                        @component('components.widget', [
                            'class' => '',
                            'title' => __('Sync Products from WooCommerce to ERP') . ':',
                        ])
                            <div class="">
                                <div class="col-sm-6">
                                    <div style="display: inline-flex; width: 100%;">
                                        <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm sync_products_from_woo"
                                            data-sync-type="all"> <i class="fa fa-download"></i> @lang('Sync All from WooCommerce')</button>
                                        &nbsp;@show_tooltip(__('Sync all products from WooCommerce to ERP system'))
                                    </div>
                                    <span class="last_sync_from_woo_all"></span>
                                </div>
                                <div class="col-sm-6">
                                    <div style="display: inline-flex; width: 100%;">
                                        <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm sync_products_from_woo"
                                            data-sync-type="updated"> <i class="fa fa-download"></i> @lang('Sync Updated from WooCommerce')</button>
                                        &nbsp;@show_tooltip(__('Sync only updated products from WooCommerce to ERP system'))
                                    </div>
                                    <span class="last_sync_from_woo_updated"></span>
                                </div>
                                <div class="col-sm-12">
                                    <br>
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle"></i> 
                                        This syncs products from WooCommerce to ERP using optimized WordPress plugin endpoints.
                                    </small>
                                </div>
                            </div>
                        @endcomponent
                    </div>
                @endif
                @if ($is_superadmin || auth()->user()->can('woocommerce.sync_orders'))
                    <div class="col-sm-12">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('woocommerce::lang.sync_orders') . ':'])
                            <div class="col-sm-6">
                                <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm tw-dw-btn-wide" id="sync_orders"> <i
                                        class="fa fa-download"></i> WooCommerce to ERP</button>
                                <span class="last_sync_orders"></span>
                            </div>
                        @endcomponent
                    </div>
                @endif
                @if ($is_superadmin || auth()->user()->can('woocommerce.sync_products'))
                    <div class="col-sm-12">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Sync Quantities') . ':'])
                            <div class="col-sm-6">
                                <div style="display: inline-flex; width: 100%;">
                                    <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm tw-dw-btn-wide" id="sync_product_quantities"> <i
                                            class="fa fa-upload"></i> @lang('ERP to WooCommerce')</button>
                                    &nbsp;@show_tooltip(__('Sync product quantities from ERP to WooCommerce'))
                                </div>
                                <span class="last_sync_product_quantities"></span>
                            </div>
                            <div class="col-sm-6">
                                <div style="display: inline-flex; width: 100%;">
                                    <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm tw-dw-btn-wide" id="sync_product_quantities_from_woo"> <i
                                            class="fa fa-download"></i> @lang('WooCommerce to ERP')</button>
                                    &nbsp;@show_tooltip(__('Sync product quantities from WooCommerce to ERP using super-fast optimized endpoint'))
                                </div>
                                <span class="last_sync_product_quantities_from_woo"></span>
                            </div>
                            <div class="col-sm-12">
                                <br>
                                <small class="text-muted">
                                    <i class="fa fa-rocket"></i> 
                                    <strong>Super-fast sync:</strong> Uses optimized WordPress plugin endpoint for lightning-fast quantity synchronization. 
                                    Retrieves only essential data (SKU, stock, prices) using direct SQL queries for maximum performance.
                                </small>
                            </div>
                        @endcomponent
                    </div>
                @endif
                @if ($is_superadmin || auth()->user()->can('woocommerce.sync_products'))
                <div class="col-sm-12">
                    @component('components.widget', ['class' => 'box-primary', 'title' => __('Sync Customers') . ':'])
                        <div class="col-sm-6">
                            <button type="button" class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm tw-dw-btn-wide" id="sync_customers"> <i 
                                class="fa fa-upload"></i> ERP to WooCommerce</button>
                            <span class="last_sync_customers"></span>
                        </div>

                        {{-- Sync Customers from WooCommerce to ERP --}}
                        <div class="col-sm-6">
                            <button type="button" class="tw-dw-btn tw-dw-btn-info tw-text-white tw-dw-btn-sm tw-dw-btn-wide" id="sync_customers_from_woo"> <i 
                                class="fa fa-download"></i>WooCommerce to ERP</button>
                            <span class="last_sync_customers_from_woo"></span>
                        </div>
                    @endcomponent
                </div>
                @endif
            </div>
        </div>

    </section>
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            syncing_text = '<i class="fa fa-refresh fa-spin"></i> ' + "{{ __('woocommerce::lang.syncing') }}...";
            update_sync_date();

            //Sync Product Categories
            $('#sync_product_categories').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn_html = $(this).html();
                $(this).html(syncing_text);
                $(this).attr('disabled', true);
                $.ajax({
                    url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCategories']) }}",
                    dataType: "json",
                    timeout: 0,
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            update_sync_date();
                        } else {
                            toastr.error(result.msg);
                        }
                        $('#sync_product_categories').html(btn_html);
                        $('#sync_product_categories').removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                });
            });

            //Sync Products
            $('.sync_products').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                sync_products(btn, btn_html);
            });

            //Sync Products from WooCommerce to ERP
            $('.sync_products_from_woo').on('click', function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                sync_products_from_woo(btn, btn_html);
            });

            //Sync Orders
            $('#sync_orders').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                $.ajax({
                    url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncOrders']) }}",
                    dataType: "json",
                    timeout: 0,
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            update_sync_date();
                        } else {
                            toastr.error(result.msg);
                        }
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                });
            });

            //Sync Product Quantities
            $('#sync_product_quantities').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                $.ajax({
                    url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductQuantities']) }}",
                    dataType: "json",
                    timeout: 0,
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            update_sync_date();
                        } else {
                            toastr.error(result.msg);
                        }
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                });
            });

            //Sync Product Quantities from WooCommerce to ERP
            $('#sync_product_quantities_from_woo').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                sync_product_quantities_from_woo(btn, btn_html);
            });

            //Sync Customers
            $('#sync_customers').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                $.ajax({
                    url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCustomers']) }}",
                    dataType: "json",
                    timeout: 0,
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            update_sync_date();
                        } else {
                            toastr.error(result.msg);
                        }
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                });
            });

            //Sync Customers from WooCommerce to ERP
            $('#sync_customers_from_woo').click(function() {
                $(window).bind('beforeunload', function() {
                    return true;
                });
                var btn = $(this);
                var btn_html = btn.html();
                btn.html(syncing_text);
                btn.attr('disabled', true);

                sync_customers_from_woo(btn, btn_html);
            });

        function update_sync_date() {
            $.ajax({
                url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'getSyncLog']) }}",
                dataType: "json",
                timeout: 0,
                success: function(data) {
                    if (data.categories) {
                        $('span.last_sync_cat').html('<small>{{ __('woocommerce::lang.last_synced') }}: ' +
                            data.categories + '</small>');
                    }
                    if (data.new_products) {
                        $('span.last_sync_new_products').html(
                            '<small>{{ __('woocommerce::lang.last_synced') }}: ' + data.new_products +
                            '</small>');
                    }
                    if (data.all_products) {
                        $('span.last_sync_all_products').html(
                            '<small>{{ __('woocommerce::lang.last_synced') }}: ' + data.all_products +
                            '</small>');
                    }
                    if (data.orders) {
                        $('span.last_sync_orders').html('<small>{{ __('woocommerce::lang.last_synced') }}: ' +
                            data.orders + '</small>');
                    }
                    if (data.from_woo_all) {
                        $('span.last_sync_from_woo_all').html('<small>{{ __('woocommerce::lang.last_synced') }}: ' +
                            data.from_woo_all + '</small>');
                    }
                    if (data.from_woo_updated) {
                        $('span.last_sync_from_woo_updated').html('<small>{{ __('woocommerce::lang.last_synced') }}: ' +
                            data.from_woo_updated + '</small>');
                    }

                }
            });
        }

        //Reset Synced Categories
        $(document).on('click', 'button#reset_categories', function() {
            var checkbox = document.createElement("div");
            checkbox.setAttribute('class', 'checkbox');
            checkbox.innerHTML =
                '<label><input type="checkbox" id="yes_reset_cat"> {{ __('woocommerce::lang.yes_reset') }}</label>';
            swal({
                title: LANG.sure,
                text: "{{ __('woocommerce::lang.confirm_reset_cat') }}",
                icon: "warning",
                content: checkbox,
                buttons: true,
                dangerMode: true,
            }).then((confirm) => {
                if (confirm) {
                    if ($('#yes_reset_cat').is(":checked")) {
                        $(window).bind('beforeunload', function() {
                            return true;
                        });
                        var btn = $(this);
                        btn.attr('disabled', true);
                        $.ajax({
                            url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'resetCategories']) }}",
                            dataType: "json",
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                } else {
                                    toastr.error(result.msg);
                                }
                                btn.removeAttr('disabled');
                                $(window).unbind('beforeunload');
                                location.reload();
                            }
                        });
                    }
                }
            });
        });

        //Reset Synced products
        $(document).on('click', 'button#reset_products', function() {
            var checkbox = document.createElement("div");
            checkbox.setAttribute('class', 'checkbox');
            checkbox.innerHTML =
                '<label><input type="checkbox" id="yes_reset_product"> {{ __('woocommerce::lang.yes_reset') }}</label>';
            swal({
                title: LANG.sure,
                text: "{{ __('woocommerce::lang.confirm_reset_product') }}",
                icon: "warning",
                content: checkbox,
                buttons: true,
                dangerMode: true,
            }).then((confirm) => {
                if (confirm) {
                    if ($('#yes_reset_product').is(":checked")) {
                        $(window).bind('beforeunload', function() {
                            return true;
                        });
                        var btn = $(this);
                        btn.attr('disabled', true);
                        $.ajax({
                            url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'resetProducts']) }}",
                            dataType: "json",
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                } else {
                                    toastr.error(result.msg);
                                }
                                btn.removeAttr('disabled');
                                $(window).unbind('beforeunload');
                                location.reload();
                            }
                        });
                    }
                }
            });
        });

        function sync_products(btn, btn_html, offset = 0) {
            var type = btn.data('sync-type');
            $.ajax({
                url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProducts']) }}?type=" +
                    type + "&offset=" + offset,
                dataType: "json",
                timeout: 0,
                success: function(result) {
                    if (result.success) {
                        if (result.total_products > 0) {
                            offset++;
                            sync_products(btn, btn_html, offset)
                        } else {
                            update_sync_date();
                            btn.html(btn_html);
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                        }
                        toastr.success(result.msg);

                    } else {
                        toastr.error(result.msg);
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                }
            });
        }

        function sync_products_from_woo(btn, btn_html, offset = 0) {
            var type = btn.data('sync-type');
            $.ajax({
                url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductFromWooToErp']) }}?sync_type=" +
                    type + "&offset=" + offset + "&limit=100",
                dataType: "json",
                timeout: 0,
                success: function(result) {
                    if (result.success) {
                        if (result.has_more && result.total_products > 0) {
                            // Continue with next chunk
                            sync_products_from_woo(btn, btn_html, result.next_offset);
                        } else {
                            // Sync completed
                            update_sync_date();
                            btn.html(btn_html);
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                        }
                        
                        // Show progress message
                        var progress_msg = 'Processed: ' + result.total_products + ' products';
                        if (result.created_products && result.created_products.length > 0) {
                            progress_msg += ' (Created: ' + result.created_products.length + ')';
                        }
                        if (result.updated_products && result.updated_products.length > 0) {
                            progress_msg += ' (Updated: ' + result.updated_products.length + ')';
                        }
                        if (result.skipped_products && result.skipped_products.length > 0) {
                            progress_msg += ' (Skipped: ' + result.skipped_products.length + ')';
                        }
                        
                        toastr.success(progress_msg);

                    } else {
                        toastr.error(result.msg || 'Sync failed');
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Sync failed: ' + error);
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            });
        }

        function sync_customers_from_woo(btn, btn_html, offset = 0) {
            $.ajax({
                url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncCustomersFromWooToErp']) }}",
                data: {
                    offset: offset,
                    limit: 100,
                    chunked: true
                },
                dataType: "json",
                timeout: 0,
                success: function(result) {
                    if (result.success) {
                        var data = result.data;
                        
                        if (data.has_more && data.total_customers > 0) {
                            // Continue with next chunk
                            sync_customers_from_woo(btn, btn_html, data.next_offset);
                        } else {
                            // Sync completed
                            update_sync_date();
                            btn.html(btn_html);
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                        }
                        
                        // Show progress message
                        var progress_msg = '👥 Customer sync: Processed ' + data.total_customers + ' customers';
                        if (data.created_customers && data.created_customers.length > 0) {
                            progress_msg += ' (Created: ' + data.created_customers.length + ')';
                        }
                        if (data.updated_customers && data.updated_customers.length > 0) {
                            progress_msg += ' (Updated: ' + data.updated_customers.length + ')';
                        }
                        if (data.skipped_customers && data.skipped_customers.length > 0) {
                            progress_msg += ' (Skipped: ' + data.skipped_customers.length + ')';
                        }
                        
                        toastr.success(progress_msg);

                    } else {
                        toastr.error(result.msg || 'Customer sync failed');
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Customer sync failed: ' + error);
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            });
        }

        function sync_product_quantities_from_woo(btn, btn_html, offset = 0) {
            $.ajax({
                url: "{{ action([\Modules\Woocommerce\Http\Controllers\WoocommerceController::class, 'syncProductQuantitiesFromWooToErp']) }}",
                data: {
                    chunk_size: 100, // Increased chunk size for super-fast sync
                    offset: offset
                },
                dataType: "json",
                timeout: 0,
                success: function(result) {
                    if (result.success) {
                        var data = result.data;
                        
                        if (data.has_more && data.total_processed > 0) {
                            // Continue with next chunk
                            sync_product_quantities_from_woo(btn, btn_html, data.next_offset);
                        } else {
                            // Sync completed
                            update_sync_date();
                            btn.html(btn_html);
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                        }
                        
                        // Show progress message with performance metrics
                        var progress_msg = '🚀 Super-fast sync: Processed ' + data.total_processed + ' products';
                        if (data.updated > 0) {
                            progress_msg += ' (Updated: ' + data.updated + ')';
                        }
                        if (data.skipped > 0) {
                            progress_msg += ' (Skipped: ' + data.skipped + ')';
                        }
                        
                        // Add performance metrics if available
                        if (data.performance) {
                            var perf = data.performance;
                            if (perf.query_time) {
                                progress_msg += ' | Query: ' + (perf.query_time * 1000).toFixed(2) + 'ms';
                            }
                            if (perf.memory_usage) {
                                var memory_mb = (perf.memory_usage / 1024 / 1024).toFixed(2);
                                progress_msg += ' | Memory: ' + memory_mb + 'MB';
                            }
                        }
                        
                        toastr.success(progress_msg);

                    } else {
                        toastr.error(result.msg || 'Super-fast quantity sync failed');
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Super-fast quantity sync failed: ' + error);
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            });
        }

    });
    
    $(document).ready(function() {
        // Test connection functionality
        window.testConnection = function() {
            var btn = $('#test_connection_btn');
            var original_text = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> Testing...');
            btn.prop('disabled', true);

            $.ajax({
                url: '/woocommerce/test-connection',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Connection successful!');
                    } else {
                        toastr.error(response.message || 'Connection failed');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error testing connection');
                },
                complete: function() {
                    btn.html(original_text);
                    btn.prop('disabled', false);
                }
            });
        };

        // Sync categories from WooCommerce to ERP (chunked)
        $(document).on('click', '#sync_product_categories_from_woo', function() {
            $(window).bind('beforeunload', function() {
                return true;
            });
            sync_categories_from_woo(0);
        });

        // Sync brands from WooCommerce to ERP
        $(document).on('click', '#sync_product_brands_from_woo', function() {
            var btn = $(this);
            var original_text = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
            btn.prop('disabled', true);

            $.ajax({
                url: '/woocommerce/sync-brands-from-woo',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        update_sync_date('brands_from_woo');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error syncing brands from WooCommerce');
                },
                complete: function() {
                    btn.html(original_text);
                    btn.prop('disabled', false);
                }
            });
        });

        // Sync products from WooCommerce to ERP (chunked)
        window.sync_products_from_woo = function(sync_type = 'all', offset = 0) {
            var btn = $('#sync_products_from_woo');
            var original_text = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
            btn.prop('disabled', true);

            $.ajax({
                url: '/woocommerce/sync-product-from-woo-to-erp',
                type: 'GET',
                data: {
                    sync_type: sync_type,
                    offset: offset,
                    limit: 100
                },
                success: function(response) {
                    if (response.success) {
                        var message = response.msg;
                        if (response.has_more) {
                            message += ' (Processing more...)';
                            // Continue with next chunk
                            setTimeout(function() {
                                sync_products_from_woo(sync_type, response.next_offset);
                            }, 1000);
                        } else {
                            message += ' (Completed)';
                            update_sync_date('products_from_woo');
                            // Re-enable button when all chunks are complete
                            btn.html(original_text);
                            btn.prop('disabled', false);
                        }
                        toastr.success(message);
                    } else {
                        toastr.error(response.message || 'Error syncing products');
                        // Re-enable button on error
                        btn.html(original_text);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error syncing products from WooCommerce');
                    // Re-enable button on error
                    btn.html(original_text);
                    btn.prop('disabled', false);
                }
            });
        };

        // Sync product quantities from WooCommerce to ERP (chunked)
        window.sync_product_quantities_from_woo = function(offset = 0) {
            var btn = $('#sync_product_quantities_from_woo');
            var original_text = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
            btn.prop('disabled', true);

            $.ajax({
                url: '/woocommerce/sync-product-quantities-from-woo-to-erp',
                type: 'GET',
                data: {
                    offset: offset,
                    chunk_size: 50
                },
                success: function(response) {
                    if (response.success) {
                        var message = response.message;
                        if (response.has_more) {
                            message += ' (Processing more...)';
                            // Continue with next chunk
                            setTimeout(function() {
                                sync_product_quantities_from_woo(response.next_offset);
                            }, 1000);
                        } else {
                            message += ' (Completed)';
                            update_sync_date('quantities_from_woo');
                            // Re-enable button when all chunks are complete
                            btn.html(original_text);
                            btn.prop('disabled', false);
                        }
                        toastr.success(message);
                    } else {
                        toastr.error(response.message || 'Error syncing quantities');
                        // Re-enable button on error
                        btn.html(original_text);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error syncing quantities from WooCommerce');
                    // Re-enable button on error
                    btn.html(original_text);
                    btn.prop('disabled', false);
                }
            });
        };

        // Sync categories from WooCommerce to ERP (chunked)
        window.sync_categories_from_woo = function(offset = 0) {
            var btn = $('#sync_product_categories_from_woo');
            var original_text = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
            btn.prop('disabled', true);

            $.ajax({
                url: '/woocommerce/sync-categories-from-woo',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    offset: offset,
                    limit: 500
                },
                success: function(response) {
                    if (response.success) {
                        var message = response.message;
                        if (response.has_more && response.total_categories > 0) {
                            message += ' (Processing more...)';
                            // Continue with next chunk
                            setTimeout(function() {
                                sync_categories_from_woo(response.next_offset);
                            }, 1000);
                        } else {
                            message += ' (Completed)';
                            update_sync_date('cat_from_woo');
                            // Re-enable button only when completed
                            btn.html(original_text);
                            btn.prop('disabled', false);
                        }
                        
                        // Show detailed progress
                        if (response.total_categories) {
                            message = '📁 Category sync: Processed ' + response.total_categories + ' categories';
                            if (response.created_categories && response.created_categories.length > 0) {
                                message += ' (Created: ' + response.created_categories.length + ')';
                            }
                            if (response.updated_categories && response.updated_categories.length > 0) {
                                message += ' (Updated: ' + response.updated_categories.length + ')';
                            }
                            if (response.skipped_categories && response.skipped_categories.length > 0) {
                                message += ' (Skipped: ' + response.skipped_categories.length + ')';
                            }
                        }
                        
                        toastr.success(message);
                    } else {
                        toastr.error(response.message || 'Error syncing categories');
                        // Re-enable button on error
                        btn.html(original_text);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error syncing categories from WooCommerce');
                    // Re-enable button on error
                    btn.html(original_text);
                    btn.prop('disabled', false);
                }
            });
        };

        // Update sync date
        function update_sync_date(sync_type) {
            var now = new Date();
            var timeString = now.toLocaleTimeString();
            $('.last_sync_' + sync_type).text('Last sync: ' + timeString);
        }
    });

    </script>
@endsection

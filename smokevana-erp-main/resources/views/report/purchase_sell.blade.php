@extends('layouts.app')
@section('title', __( 'report.purchase_sell' ))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'report.purchase_sell' )
        <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang( 'report.purchase_sell_msg' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="print_section"><h2>{{session()->get('business.name')}} - @lang( 'report.purchase_sell' )</h2></div>
    <div class="row no-print">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="purchase_sell_location_filter">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="purchase_sell_date_filter">
                    <span>
                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Purchase & Sale Graph Section -->
    <div class="row no-print" id="purchase_sell_chart_container" style="margin-bottom: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box box-primary', 'icon' => '<i class="fa fa-chart-line"></i> ', 'title' => __('Purchase & Sale Trend')])
                <div class="chart-container" style="position: relative; height: 400px; width: 100%;">
                    <canvas id="purchase_sell_chart"></canvas>
                </div>
            @endcomponent
        </div>
    </div>
    
    <br>
    <div class="row">
        <div class="col-xs-6">
            @component('components.widget', ['class' => 'box box-primary', 'icon' => '<i class="fa fa-shopping-cart"></i> ', 'title' => __('purchase.purchases')])
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('report.total_purchase') }}:</th>
                        <td>
                            <span class="total_purchase">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('report.purchase_inc_tax') }}:</th>
                        <td>
                             <span class="purchase_inc_tax">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('lang_v1.total_purchase_return_inc_tax') }}:</th>
                        <td>
                             <span class="purchase_return_inc_tax">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('report.purchase_due') }}: @show_tooltip(__('tooltip.purchase_due'))</th>
                        <td>
                             <span class="purchase_due">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </table>
            @endcomponent
        </div>

        <div class="col-xs-6">
            @component('components.widget', ['class' => 'box box-primary', 'icon' => '<i class="fa fa-dollar-sign"></i> ', 'title' => __('sale.sells')])
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('report.total_sell') }}:</th>
                        <td>
                            <span class="total_sell">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('report.sell_inc_tax') }}:</th>
                        <td>
                             <span class="sell_inc_tax">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('lang_v1.total_sell_return_inc_tax') }}:</th>
                        <td>
                             <span class="total_sell_return">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('report.sell_due') }}: @show_tooltip(__('tooltip.sell_due'))</th>
                        <td>
                            <span class="sell_due">
                                <i class="fas fa-sync fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </table>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            @component('components.widget', ['class' => 'box box-primary', 'icon' => '<i class="fa fa-calculator"></i> '])
                @slot('title')
                    {{ __('lang_v1.overall') }} 
                    ((@lang('business.sale') - @lang('lang_v1.sell_return')) - (@lang('lang_v1.purchase') - @lang('lang_v1.purchase_return')) ) 
                    @show_tooltip(__('tooltip.over_all_sell_purchase'))
                @endslot
                <h3 class="text-muted">
                    {{ __('report.sell_minus_purchase') }}: 
                    <span class="sell_minus_purchase">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                </h3>

                <h3 class="text-muted">
                    {{ __('report.difference_due') }}: 
                    <span class="difference_due">
                        <i class="fas fa-sync fa-spin fa-fw"></i>
                    </span>
                </h3>
            @endcomponent
        </div>
    </div>
    <div class="row no-print">
        <div class="col-sm-12">
            <button class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right" aria-label="Print"
                onclick="window.print();">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                </svg> @lang('messages.print')
            </button>
        </div>
    </div>
	

</section>
<!-- /.content -->
</div>
@stop
@section('javascript')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    var purchaseSellChart = null;
    
    function loadPurchaseSellChart() {
        var start_date = $('#purchase_sell_date_filter').data('daterangepicker') 
            ? $('#purchase_sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD')
            : null;
        var end_date = $('#purchase_sell_date_filter').data('daterangepicker')
            ? $('#purchase_sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            : null;
        var location_id = $('#purchase_sell_location_filter').val();
        
        $.ajax({
            url: '/reports/purchase-sell-chart-data',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date,
                location_id: location_id
            },
            success: function(response) {
                if (response.success && response.labels && response.purchase && response.sale) {
                    var ctx = document.getElementById('purchase_sell_chart').getContext('2d');
                    
                    // Destroy existing chart if it exists
                    if (purchaseSellChart) {
                        purchaseSellChart.destroy();
                    }
                    
                    // Create new chart
                    purchaseSellChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [
                                {
                                    label: '@lang("purchase.purchases")',
                                    data: response.purchase,
                                    borderColor: 'rgb(255, 99, 132)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                },
                                {
                                    label: '@lang("sale.sells")',
                                    data: response.sale,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: '@lang("Purchase & Sale Trend")',
                                    font: {
                                        size: 16,
                                        weight: 'bold'
                                    }
                                },
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            var label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += __currency_trans_from_en(context.parsed.y);
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return __currency_trans_from_en(value);
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.1)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            interaction: {
                                mode: 'nearest',
                                axis: 'x',
                                intersect: false
                            }
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading chart data:', error);
            }
        });
    }
    
    $(document).ready(function() {
        // Load chart on page load
        setTimeout(function() {
            loadPurchaseSellChart();
        }, 500);
        
        // Reload chart when filters change
        $('#purchase_sell_location_filter').on('change', function() {
            loadPurchaseSellChart();
        });
        
        // Hook into date filter change
        $('#purchase_sell_date_filter').on('apply.daterangepicker', function(ev, picker) {
            loadPurchaseSellChart();
        });
    });
</script>

@endsection

@extends('layouts.app')
@section('title', __('report.expense_report'))
@section('css')
@include('report.partials.amazon_report_styles')
@endsection

@section('content')
<div class="report-amazon-page">
<!-- Amazon banner -->
<div class="sr-banner amazon-theme-banner">
    <div class="banner-inner">
        <div class="banner-icon"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></div>
        <div class="banner-text">
            <h1 class="banner-title">{{ __('report.expense_report') }}</h1>
            <p class="banner-subtitle">@lang('report.expense_report_by_category')</p>
        </div>
    </div>
    <div class="banner-actions">
        <button type="button" class="btn btn-sr-filters" data-toggle="modal" data-target="#filterModal">
            <i class="fa fa-filter"></i> @lang('report.filters')
        </button>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">

            <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('report.filters')</h4>
                        </div>
                        <div class="modal-body" style="padding: 0px; margin-top: 10px;">

                            {{-- @component('components.filters', ['title' => __('report.filters')]) --}}
                            {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class,
                            'getExpenseReport']), 'method' => 'get' ]) !!}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('category_id', __('category.category').':') !!}
                                    {!! Form::select('category', $categories, null, ['placeholder' =>
                                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id'
                                    => 'category_id']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null , ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'trending_product_date_range', 'readonly']); !!}
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit"
                                    class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white pull-right">@lang('report.apply_filters')</button>
                            </div>
                            {!! Form::close() !!}
                            {{-- @endcomponent --}}
                            <div class="modal-footer">
                                {{-- <button type="button" class="btn btn-primary"
                                    id="applyFiltersBtn">@lang('messages.apply')</button>
                                --}}
                                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                    data-dismiss="modal">@lang('messages.close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('report.total_expense') . ' - ' . __('report.summary')])
            {!! $chart->container() !!}
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('expense.expense_categories')])
            <table class="table nowrap" id="expense_report_table">
                <thead>
                    <tr>
                        <th>@lang( 'expense.expense_categories' )</th>
                        <th>@lang( 'report.total_expense' )</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total_expense = 0;
                    @endphp
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{$expense['category'] ?? __('report.others')}}</td>
                        <td><span class="display_currency"
                                data-currency_symbol="true">{{$expense['total_expense']}}</span></td>
                    </tr>
                    @php
                    $total_expense += $expense['total_expense'];
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>@lang('sale.total')</td>
                        <td><span class="display_currency" data-currency_symbol="true">{{$total_expense}}</span></td>
                    </tr>
                </tfoot>
            </table>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
{!! $chart->script() !!}
@endsection
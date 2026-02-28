@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .expense-list-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .expense-header-banner {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 0 0 10px 10px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .expense-header-banner.amazon-theme-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .expense-header-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .expense-header-banner .banner-title i { color: #fff !important; }
    .expense-header-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
    .expense-list-page #dynamic_button,
    .expense-list-page .amazon-orange-add,
    .expense-list-page .box-tools .tw-dw-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border: 2px solid #C7511F !important; color: #fff !important; }
    .expense-list-page #dynamic_button:hover,
    .expense-list-page .amazon-orange-add:hover,
    .expense-list-page .box-tools .tw-dw-btn:hover { color: #fff !important; opacity: 0.95; border-color: #E47911 !important; }
    /* Section card: dark header + orange line + light body */
    .expense-list-page .box-primary { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .expense-list-page .box-primary .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .expense-list-page .box-primary .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .expense-list-page .box-primary .box-title { color: #fff !important; font-weight: 600; }
    .expense-list-page .box-primary .tw-flow-root,
    .expense-list-page .box-primary .table-responsive { background: #f7f8f8 !important; padding: 1rem 1.25rem !important; }
    .expense-list-page .dt-buttons .btn,
    .expense-list-page .dt-buttons button,
    .expense-list-page .dt-buttons .dt-button { background: #232f3e !important; border: 1px solid #37475a !important; color: #fff !important; }
    .expense-list-page .dt-buttons .btn:hover,
    .expense-list-page .dt-buttons button:hover,
    .expense-list-page .dt-buttons .dt-button:hover { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; }
    .expense-list-page .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #ff9900 !important; border-color: #ff9900 !important; color: #fff !important; }
    .expense-list-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover { border-color: #ff9900; color: #232f3e; }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div class="expense-header-banner amazon-theme-banner">
        <h1 class="banner-title"><i class="fas fa-money-bill-alt"></i> @lang('expense.expenses')</h1>
        <p class="banner-subtitle">@lang('expense.all_expenses')</p>
    </div>
</section>

<!-- Main content -->
<section class="content expense-list-page">
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
                            @if(auth()->user()->can('all_expense.access'))
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control
                                    select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('expense_for', __('expense.expense_for').':') !!}
                                    {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2',
                                    'style' => 'width:100%']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_contact_filter', __('contact.contact') . ':') !!}
                                    {!! Form::select('expense_contact_filter', $contacts, null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_category_id',__('expense.expense_category').':') !!}
                                    {!! Form::select('expense_category_id', $categories, null, ['placeholder' =>
                                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id'
                                    => 'expense_category_id']); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_sub_category_id_filter',__('product.sub_category').':') !!}
                                    {!! Form::select('expense_sub_category_id_filter', $sub_categories, null,
                                    ['placeholder' =>
                                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id'
                                    => 'expense_sub_category_id_filter']); !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null, ['placeholder' =>
                                    __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' =>
                                    'expense_date_range', 'readonly']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('expense_payment_status', __('purchase.payment_status') . ':') !!}
                                    {!! Form::select('expense_payment_status', ['paid' => __('lang_v1.paid'), 'due' =>
                                    __('lang_v1.due'), 'partial' => __('lang_v1.partial')], null, ['class' =>
                                    'form-control select2', 'style' => 'width:100%', 'placeholder' =>
                                    __('lang_v1.all')]); !!}
                                </div>
                            </div>
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
    {{-- @endcomponent --}}


    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('expense.all_expenses')])
            @can('expense.add')
            @slot('tool')
            <div class="box-tools">
                {{-- <a class="btn btn-block btn-primary"
                    href="{{action([\App\Http\Controllers\ExpenseController::class, 'create'])}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a> --}}
                <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right amazon-orange-add"
                    href="{{action([\App\Http\Controllers\ExpenseController::class, 'create'])}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg> @lang('messages.add')
                </a>
            </div>
            @endslot
            @endcan
            <div class="table-responsive">
                <table class="table   table-bordered table-striped ajax_view hide-footer" id="expense_table" style="min-width: max-content;">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('lang_v1.recur_details')</th>
                            <th>@lang('expense.expense_category')</th>
                            <th>@lang('product.sub_category')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('product.tax')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('purchase.payment_due')
                            <th>@lang('expense.expense_for')</th>
                            <th>@lang('contact.contact')</th>
                            <th>@lang('expense.expense_note')</th>
                            <th>@lang('lang_v1.added_by')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="2"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td></td>
                            <td colspan="3"></td>
                            <td class="footer_payment_status_count"></td>
                            <td></td>
                            <td class="footer_expense_total text-left"></td>
                            <td class="footer_total_due text-left"></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>@endsection
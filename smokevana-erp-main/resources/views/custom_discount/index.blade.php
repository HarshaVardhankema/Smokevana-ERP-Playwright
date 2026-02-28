@extends('layouts.app')
@section('title', __('Discounts'))

@section('content')

    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Discount Rules
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">Discount Rules</small>
        </h1>
    </section>
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('tax_rate.all_your_tax_rates')])
            @can('tax_rate.create')
                @slot('tool')
                    <div class="box-tools">
                        <a id="dynamic_button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right tw-m-2"
                                        href="{{ action([\App\Http\Controllers\CustomDiscountController::class, 'create']) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg> @lang('messages.add')
                                    </a>
                    </div>
                @endslot
            @endcan
        @endcomponent
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="custom_discount_table">
                <thead>
                    <tr>
                        <th>Discount ID</th>
                        <th>Name</th>
                        <th>Priority</th>
                        <th>Life</th>
                        <th>Action Button</th>
                    </tr>
                </thead>
            </table>
        </div>
    </section>

    <div class="modal fade custom_discount_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

@endsection

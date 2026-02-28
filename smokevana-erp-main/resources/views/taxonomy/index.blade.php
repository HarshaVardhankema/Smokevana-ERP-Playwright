@extends('layouts.app')
@php
    $heading = !empty($module_category_data['heading']) ? $module_category_data['heading'] : __('category.categories');
    $navbar = !empty($module_category_data['navbar']) ? $module_category_data['navbar'] : null;
@endphp
@section('title', $heading)

@section('css')
<style>
    .taxonomy-header-banner { background:#37475a;border-radius:6px;padding:22px 28px;margin-bottom:16px;box-shadow:0 3px 10px rgba(15,17,17,0.4); }
    .taxonomy-header-banner .banner-title { display:flex;align-items:center;gap:10px;font-size:22px;font-weight:700;margin:0;color:#fff; }
    .taxonomy-header-banner .banner-title i,.taxonomy-header-banner .banner-title [data-toggle="tooltip"] { color:#fff!important; }
    .taxonomy-header-banner .banner-subtitle { font-size:13px;color:rgba(249,250,251,0.88);margin:4px 0 0 0; }
    .amazon-orange-add { background:linear-gradient(to bottom,#FF9900 0%,#E47911 100%)!important;border-color:#C7511F!important;color:white!important; }
    .amazon-orange-add:hover { color:white!important;opacity:0.95; }
</style>
@endsection

@section('content')
    @if (!empty($navbar))
        @include($navbar)
    @endif
    <!-- Amazon-style banner -->
    <section class="content-header">
        <div class="taxonomy-header-banner amazon-theme-banner">
            <h1 class="banner-title">
                <i class="fas fa-folder"></i> {{ $heading }}
                @if (isset($module_category_data['heading_tooltip']))
                    @show_tooltip($module_category_data['heading_tooltip'])
                @endif
            </h1>
            <p class="banner-subtitle">{{ $module_category_data['sub_heading'] ?? __('category.manage_your_categories') }}</p>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        @php
            $cat_code_enabled =
                isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code']
                    ? false
                    : true;
        @endphp
        <input type="hidden" id="category_type" value="{{ request()->get('type') }}">
        @php
            $can_add = true;
            if (request()->get('type') == 'product' && !auth()->user()->can('category.create')) {
                $can_add = false;
            }
        @endphp
        @component('components.widget', ['class' => 'box-solid', 'can_add' => $can_add])
            @if ($can_add)
                @slot('tool')
                    <div class="box-tools">
                        {{-- <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action([\App\Http\Controllers\TaxonomyController::class, 'create'])}}?type={{request()->get('type')}}" 
                    data-container=".category_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button> --}}
                        <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal amazon-orange-add"
                            data-href="{{action([\App\Http\Controllers\TaxonomyController::class, 'create'])}}?type={{request()->get('type')}}" 
                            data-container=".category_modal">
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
            @endif
            <style>
                /* Hide sorting icons for the first column */
                #category_table th:nth-child(1).sorting:after, 
                #category_table th:nth-child(1).sorting_asc:after, 
                #category_table th:nth-child(1).sorting_desc:after {
                    display: none !important;
                }
                            </style>
            <div >
                <table style="min-width: max-content;" class="table   table-bordered table-striped ajax_view hide-footer"  id="category_table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>
                                @if (!empty($module_category_data['taxonomy_label']))
                                    {{ $module_category_data['taxonomy_label'] }}
                                @else
                                    @lang('category.category')
                                @endif
                            </th>
                            @if ($cat_code_enabled)
                                <th>{{ $module_category_data['taxonomy_code_label'] ?? __('category.code') }}</th>
                            @endif
                            <th>Slug</th>
                            <th>Visibility</th>
                            <th>@lang('lang_v1.description')</th>
                            <th>Parent Category</th>
                            <th>Location</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@stop
@section('javascript')
    @includeIf('taxonomy.taxonomies_js')
@endsection

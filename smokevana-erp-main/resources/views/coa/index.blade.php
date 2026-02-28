@extends('layouts.app')

@section('title', 'COA List')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .coa-page { background: #EAEDED; min-height: calc(100vh - 120px); padding: 20px 0; padding-bottom: 2rem; }
    .coa-page .page-header-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
        border-radius: 10px;
        padding: 24px 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    .coa-page .page-header-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .coa-page .page-header-card h1 {
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }
    .coa-page .page-header-card .icon-box {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 10px;
        display: flex;
        width: 52px;
        height: 52px;
        min-width: 52px;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ff9900;
    }
    .coa-page .page-header-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.78);
        margin: 4px 0 0 0;
    }
    .coa-page .content-card {
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #D5D9D9;
    }
    .coa-page .content-card .box {
        background: transparent !important;
        border: none !important;
    }
    .coa-page .content-card .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .coa-page .content-card .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .coa-page .content-card .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
    }
    .coa-page .content-card .box-body {
        background: #f7f8f8 !important;
        padding: 1rem 1.25rem;
    }
    .coa-page table thead th {
        background: #e8e9e9 !important;
        color: #232F3E !important;
        border-bottom: 2px solid #D5D9D9;
        padding: 10px 12px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .coa-page table tbody td {
        padding: 10px 12px;
        color: #0F1111;
        background: #fff;
        vertical-align: top;
    }
    .coa-page table tbody tr:hover td {
        background: #f7f8f8 !important;
    }
    .coa-list-links ul {
        margin: 0;
        padding-left: 18px;
    }
    .coa-list-links li {
        margin-bottom: 4px;
    }
    .coa-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #D5D9D9;
        margin-bottom: 20px;
        background: #f7f8f8;
        padding: 0;
    }
    .coa-tab {
        padding: 12px 24px;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #232F3E;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }
    .coa-tab:hover {
        background: #e8e9e9;
        color: #C7511F;
    }
    .coa-tab.active {
        background: #fff;
        color: #C7511F;
        border-bottom-color: #ff9900;
    }
    .coa-tab-content {
        display: none;
    }
    .coa-tab-content.active {
        display: block;
    }
    .category-header-row {
        border-top: 2px solid #D5D9D9;
    }
    .category-header-row + tr {
        border-top: none;
    }
</style>
@endsection

@section('content')
<div class="coa-page">
    <div class="container-fluid">
        <div class="page-header-card">
            <div class="page-header-title">
                <div class="icon-box"><i class="fas fa-certificate"></i></div>
                <div>
                    <h1>COA (Certificate of Analysis)</h1>
                    <p class="page-header-subtitle">Manage category-wise COA links for your B2B and B2C locations</p>
                </div>
            </div>
            <div>
                <a href="{{ route('coa.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Create COA
                </a>
            </div>
        </div>

        <div class="content-card">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('COA List')])
            
            <!-- Tabs Navigation -->
            <div class="coa-tabs">
                <button type="button" class="coa-tab {{ $activeTab == 'category' ? 'active' : '' }}" data-tab="category">
                    <i class="fa fa-folder"></i> Category
                </button>
                <button type="button" class="coa-tab {{ $activeTab == 'lists' ? 'active' : '' }}" data-tab="lists">
                    <i class="fa fa-list"></i> Lists
                </button>
            </div>

            <div class="row" style="margin-bottom: 12px;">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('search', 'Search Category:') !!}
                        {!! Form::text('search', $search ?? '', [
                            'class' => 'form-control',
                            'id' => 'coa_filter_search',
                            'placeholder' => 'Type category name...',
                            'style' => 'width:100%',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id', 'Location:') !!}
                        {!! Form::select('location_id', $locations, $location_id, [
                            'class' => 'form-control select2',
                            'id' => 'coa_filter_location_id',
                            'placeholder' => 'All locations',
                            'style' => 'width:100%',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('brand_id', 'Brand (optional):') !!}
                        {!! Form::select('brand_id', $brands, $brand_id, [
                            'class' => 'form-control select2',
                            'id' => 'coa_filter_brand_id',
                            'placeholder' => 'All brands',
                            'style' => 'width:100%',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3" style="margin-top: 25px;">
                    <button type="button" class="btn btn-primary" id="coa_apply_filters">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-default" id="coa_reset_filters">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Category Tab Content -->
            <div class="coa-tab-content {{ $activeTab == 'category' ? 'active' : '' }}" id="category-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 80px;">S.No.</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Brand</th>
                                <th>Created At</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $index => $category)
                                @php
                                    $locationName = optional($category->location)->name ?? '—';
                                    if ($locationName !== '—' && $category->location) {
                                        $name = $category->location->name;
                                        $hasB2B = stripos($name, 'B2B') !== false;
                                        $hasB2C = stripos($name, 'B2C') !== false;
                                        if (!$hasB2B && !$hasB2C) {
                                            $suffix = (!empty($category->location->is_b2c) && $category->location->is_b2c == 1) ? ' B2C' : ' B2B';
                                            $locationName = $name . $suffix;
                                        }
                                    }
                                @endphp
                                <tr>
                                    {{-- Sequential number: 1, 2, 3... based on creation order --}}
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $locationName }}</td>
                                    <td>
                                        @if($category->brand)
                                            {{ $category->brand->name }} <small class="text-muted">(ID: {{ $category->brand->id }})</small>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ optional($category->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('coa.show', $category->id) }}" class="btn btn-xs btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            {!! Form::open([
                                                'url' => route('coa.destroy', $category->id),
                                                'method' => 'DELETE',
                                                'style' => 'display:inline-block',
                                                'onsubmit' => "return confirm('Are you sure you want to delete this COA category?');",
                                            ]) !!}
                                                <button type="submit" class="btn btn-xs btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No COA categories found. Create a new COA using the "Create COA" button.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Lists Tab Content -->
            <div class="coa-tab-content {{ $activeTab == 'lists' ? 'active' : '' }}" id="lists-tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>List Name</th>
                                <th>Location</th>
                                <th>Brand</th>
                                <th>Google Drive Link</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedLists = $allLists->groupBy(function($list) {
                                    return $list->category->id;
                                });
                            @endphp
                            @forelse($groupedLists as $categoryId => $lists)
                                @php
                                    $category = $lists->first()->category;
                                @endphp
                                <tr class="category-header-row" style="background-color: #f0f0f0;">
                                    <td colspan="5" style="font-weight: bold; font-size: 14px; padding: 12px;">
                                        <i class="fa fa-folder"></i> {{ $category->name }}
                                    </td>
                                </tr>
                                @foreach($lists as $list)
                                    @php
                                        $locationName = optional($list->category->location)->name ?? '—';
                                        if ($locationName !== '—' && $list->category->location) {
                                            $name = $list->category->location->name;
                                            $hasB2B = stripos($name, 'B2B') !== false;
                                            $hasB2C = stripos($name, 'B2C') !== false;
                                            if (!$hasB2B && !$hasB2C) {
                                                $suffix = (!empty($list->category->location->is_b2c) && $list->category->location->is_b2c == 1) ? ' B2C' : ' B2B';
                                                $locationName = $name . $suffix;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td style="padding-left: 30px;">{{ $list->name }}</td>
                                        <td>{{ $locationName }}</td>
                                        <td>{{ optional($list->category->brand)->name ?? '—' }}</td>
                                        <td>
                                            <a href="{{ $list->link }}" target="_blank" rel="noopener noreferrer" class="text-primary" title="{{ $list->link }}">
                                                {{ strlen($list->link) > 50 ? substr($list->link, 0, 50) . '...' : $list->link }}
                                            </a>
                                        </td>
                                        <td>{{ optional($list->created_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No list items found. Create a new COA with lists using the "Create COA" button.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).ready(function () {
        $('.select2').select2();

        // Tab switching
        $('.coa-tab').on('click', function () {
            var tabName = $(this).data('tab');
            
            // Update active tab
            $('.coa-tab').removeClass('active');
            $(this).addClass('active');
            
            // Update active content
            $('.coa-tab-content').removeClass('active');
            $('#' + tabName + '-tab').addClass('active');
            
            // Update URL without reload
            var params = new URLSearchParams(window.location.search);
            params.set('tab', tabName);
            window.history.pushState({}, '', '{{ route('coa.index') }}?' + params.toString());
        });

        $('#coa_apply_filters').on('click', function () {
            var params = {};
            var search = $('#coa_filter_search').val();
            var locationId = $('#coa_filter_location_id').val();
            var brandId = $('#coa_filter_brand_id').val();
            var activeTab = $('.coa-tab.active').data('tab') || 'category';

            if (search && search.trim()) {
                params.search = search.trim();
            }
            if (locationId) {
                params.location_id = locationId;
            }
            if (brandId) {
                params.brand_id = brandId;
            }
            params.tab = activeTab;

            var query = $.param(params);
            var baseUrl = '{{ route('coa.index') }}';
            window.location.href = query ? (baseUrl + '?' + query) : baseUrl;
        });

        $('#coa_reset_filters').on('click', function () {
            $('#coa_filter_search').val('');
            $('#coa_filter_location_id').val(null).trigger('change');
            $('#coa_filter_brand_id').val(null).trigger('change');
        });

        // Allow Enter key to trigger search
        $('#coa_filter_search').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $('#coa_apply_filters').click();
            }
        });
    });
</script>
@endsection


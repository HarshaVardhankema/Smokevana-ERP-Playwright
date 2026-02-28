@extends('layouts.app')

@section('title', 'View COA')

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .coa-show-page { background: #EAEDED; min-height: calc(100vh - 120px); padding: 20px 0; padding-bottom: 2rem; }
    .coa-show-page .page-header-card {
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
    .coa-show-page .page-header-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .coa-show-page .page-header-card h1 {
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }
    .coa-show-page .page-header-card .icon-box {
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
    .coa-show-page .content-card {
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: 1px solid #D5D9D9;
    }
    .coa-show-page .content-card .box {
        background: transparent !important;
        border: none !important;
    }
    .coa-show-page .content-card .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #fff !important;
        border: none !important;
        padding: 14px 20px !important;
        position: relative;
    }
    .coa-show-page .content-card .box-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #ff9900;
    }
    .coa-show-page .content-card .box-title {
        color: #fff !important;
        font-weight: 600;
        font-size: 1rem;
    }
    .coa-show-page .content-card .box-body {
        background: #f7f8f8 !important;
        padding: 1rem 1.25rem;
    }
</style>
@endsection

@section('content')
<div class="coa-show-page">
    <div class="container-fluid">
        <div class="page-header-card">
            <div class="page-header-title">
                <div class="icon-box"><i class="fas fa-certificate"></i></div>
                <div>
                    <h1>COA Details</h1>
                    <p class="page-header-subtitle">Review the category and its associated COA lists</p>
                </div>
            </div>
            <div>
                <a href="{{ route('coa.index') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="content-card">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('COA Details')])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Category ID</label>
                        <p class="form-control-static"><strong>{{ $category->id }}</strong></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Category</label>
                        <p class="form-control-static">{{ $category->name }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Location</label>
                        <p class="form-control-static">{{ optional($category->location)->name ?? '—' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Brand</label>
                        <p class="form-control-static">{{ optional($category->brand)->name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <hr>

            <h4>Lists</h4>
            @if($category->lists->isEmpty())
                <p class="text-muted">No lists have been added under this category.</p>
            @else
                <ul>
                    @foreach($category->lists as $list)
                        <li style="margin-bottom: 6px;">
                            <strong>{{ $list->name }}</strong>:
                            <a href="{{ $list->link }}" target="_blank" rel="noopener noreferrer">
                                {{ $list->link }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @endcomponent
        </div>
    </div>
</div>
@endsection


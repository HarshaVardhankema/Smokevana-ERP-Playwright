@extends('layouts.app')
@section('title', __('barcode.add_barcode_setting'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
/* === Barcode form pages – Amazon theme (card layout) === */
.barcode-form-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.barcode-form-page .amazon-barcode-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 10px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.barcode-form-page .amazon-barcode-banner .banner-inner {
    display: flex; align-items: center; gap: 18px;
}
.barcode-form-page .amazon-barcode-banner .banner-action .btn-barcode-save {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important; color: #fff !important;
    font-weight: 600; padding: 10px 24px; border-radius: 6px;
}
.barcode-form-page .amazon-barcode-banner .banner-action .btn-barcode-save:hover { color: #fff !important; opacity: 0.95; }
.barcode-form-page .amazon-barcode-banner .banner-icon {
    width: 52px; height: 52px; min-width: 52px;
    border-radius: 10px; background: rgba(255,255,255,0.1);
    color: #fff; font-size: 24px;
    display: flex; align-items: center; justify-content: center;
}
.barcode-form-page .amazon-barcode-banner .banner-text { display: flex; flex-direction: column; gap: 6px; }
.barcode-form-page .amazon-barcode-banner .banner-title {
    font-size: 24px; font-weight: 700; margin: 0; color: #fff;
}
.barcode-form-page .amazon-barcode-banner .banner-subtitle {
    font-size: 13px; color: rgba(255,255,255,0.78); margin: 0;
}

.barcode-form-page .barcode-section-card {
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #D5D9D9;
}
.barcode-form-page .barcode-section-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #fff;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 600;
    position: relative;
}
.barcode-form-page .barcode-section-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: #ff9900;
}
.barcode-form-page .barcode-section-header i { color: #fff; font-size: 18px; }
.barcode-form-page .barcode-section-body {
    background: #f7f8f8;
    padding: 1.25rem 1.5rem;
}
.barcode-form-page .barcode-section-body .form-group { margin-bottom: 0.75rem; }
.barcode-form-page .barcode-section-body .form-group:last-child,
.barcode-form-page .barcode-section-body .row:last-child .form-group { margin-bottom: 0; }
.barcode-form-page .barcode-section-body label { color: #0F1111 !important; font-size: 0.8125rem; }
.barcode-form-page .barcode-section-body .form-control {
    background: #fff; border: 1px solid #D5D9D9; color: #0F1111;
    font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-height: 2rem;
    box-sizing: border-box;
}
.barcode-form-page .barcode-section-body .form-control:focus {
    border-color: #FF9900; outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.barcode-form-page .barcode-section-body .input-group-addon {
    background: #F7F8F8; border: 1px solid #D5D9D9; color: #232F3E;
    font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-width: 2.25rem;
}
.barcode-form-page .barcode-section-body .input-group .form-control { border-left-color: #D5D9D9; }
.barcode-form-page .barcode-section-body input[type="checkbox"] { accent-color: #FF9900; }
.barcode-form-page .barcode-section-body .checkbox label { color: #0F1111 !important; }
.barcode-form-page .barcode-section-body .row { margin-left: -0.375rem; margin-right: -0.375rem; }
.barcode-form-page .barcode-section-body .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

.barcode-form-page .barcode-form-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.barcode-form-page .btn-barcode-save {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important; color: #fff !important;
    font-weight: 600; padding: 10px 24px; border-radius: 6px;
}
.barcode-form-page .btn-barcode-save:hover { color: #fff !important; opacity: 0.95; }
.barcode-form-page .btn-barcode-cancel {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    color: #0f1111 !important; padding: 10px 20px; border-radius: 6px;
}
.barcode-form-page .btn-barcode-cancel:hover { background: #f7f8f8 !important; }
</style>
@endsection

@section('content')
<div class="barcode-form-page">
    <!-- Amazon banner -->
    <div class="amazon-barcode-banner amazon-theme-banner">
        <div class="banner-inner">
            <div class="banner-icon"><i class="fa fa-barcode" aria-hidden="true"></i></div>
            <div class="banner-text">
                <h1 class="banner-title">@lang('barcode.add_barcode_setting')</h1>
                <p class="banner-subtitle">@lang('barcode.manage_your_barcodes')</p>
            </div>
        </div>
        <div class="banner-action">
            <button type="submit" form="add_barcode_settings_form" class="btn btn-barcode-save">@lang('messages.save')</button>
        </div>
    </div>

    <section class="content">
        {!! Form::open(['url' => action([\App\Http\Controllers\BarcodeController::class, 'store']), 'method' => 'post', 'id' => 'add_barcode_settings_form']) !!}

        <!-- Card: Name & description -->
        <div class="barcode-section-card">
            <div class="barcode-section-header"><i class="fa fa-tag"></i> @lang('barcode.setting_name') & @lang('barcode.setting_description')</div>
            <div class="barcode-section-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('name', __('barcode.setting_name') . ':*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __('barcode.setting_name')]); !!}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('description', __('barcode.setting_description')) !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('barcode.setting_description'), 'rows' => 3]); !!}
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_continuous', 1, false, ['id' => 'is_continuous']); !!}
                                @lang('barcode.is_continuous')
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Card: Margins -->
        <div class="barcode-section-card">
            <div class="barcode-section-header"><i class="fa fa-arrows-alt"></i> @lang('barcode.top_margin') & @lang('barcode.left_margin')</div>
            <div class="barcode-section-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('top_margin', __('barcode.top_margin') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></span>
                            {!! Form::number('top_margin', 0, ['class' => 'form-control', 'placeholder' => __('barcode.top_margin'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('left_margin', __('barcode.left_margin') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></span>
                            {!! Form::number('left_margin', 0, ['class' => 'form-control', 'placeholder' => __('barcode.left_margin'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Card: Sticker dimensions -->
        <div class="barcode-section-card">
            <div class="barcode-section-header"><i class="fa fa-square"></i> @lang('barcode.width') & @lang('barcode.height')</div>
            <div class="barcode-section-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('width', __('barcode.width') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                            {!! Form::number('width', null, ['class' => 'form-control', 'placeholder' => __('barcode.width'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('height', __('barcode.height') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-text-height" aria-hidden="true"></i></span>
                            {!! Form::number('height', null, ['class' => 'form-control', 'placeholder' => __('barcode.height'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Card: Paper dimensions -->
        <div class="barcode-section-card">
            <div class="barcode-section-header"><i class="fa fa-file-alt"></i> @lang('barcode.paper_width') & @lang('barcode.paper_height')</div>
            <div class="barcode-section-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('paper_width', __('barcode.paper_width') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-text-width" aria-hidden="true"></i></span>
                            {!! Form::number('paper_width', null, ['class' => 'form-control', 'placeholder' => __('barcode.paper_width'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 paper_height_div">
                    <div class="form-group">
                        {!! Form::label('paper_height', __('barcode.paper_height') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-text-height" aria-hidden="true"></i></span>
                            {!! Form::number('paper_height', null, ['class' => 'form-control', 'placeholder' => __('barcode.paper_height'), 'min' => 0.1, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Card: Layout (rows, columns, per sheet) -->
        <div class="barcode-section-card">
            <div class="barcode-section-header"><i class="fa fa-th"></i> @lang('barcode.stickers_in_one_row') & @lang('barcode.stickers_in_one_sheet')</div>
            <div class="barcode-section-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('stickers_in_one_row', __('barcode.stickers_in_one_row') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></span>
                            {!! Form::number('stickers_in_one_row', null, ['class' => 'form-control', 'placeholder' => __('barcode.stickers_in_one_row'), 'min' => 1, 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('row_distance', __('barcode.row_distance') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-resize-vertical" aria-hidden="true"></span></span>
                            {!! Form::number('row_distance', 0, ['class' => 'form-control', 'placeholder' => __('barcode.row_distance'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('col_distance', __('barcode.col_distance') . ' (' . __('barcode.in_in') . '):*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-resize-horizontal" aria-hidden="true"></span></span>
                            {!! Form::number('col_distance', 0, ['class' => 'form-control', 'placeholder' => __('barcode.col_distance'), 'min' => 0, 'step' => 0.00001, 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 stickers_per_sheet_div">
                    <div class="form-group">
                        {!! Form::label('stickers_in_one_sheet', __('barcode.stickers_in_one_sheet') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-th" aria-hidden="true"></i></span>
                            {!! Form::number('stickers_in_one_sheet', null, ['class' => 'form-control', 'placeholder' => __('barcode.stickers_in_one_sheet'), 'min' => 1, 'required']); !!}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Card: Default & actions -->
        <div class="barcode-section-card">
            <div class="barcode-section-header"><i class="fa fa-cog"></i> @lang('messages.action')</div>
            <div class="barcode-section-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('is_default', 1); !!}
                                @lang('barcode.set_as_default')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="barcode-form-actions">
                        <button type="submit" class="btn btn-barcode-save">@lang('messages.save')</button>
                        <a href="{{ action([\App\Http\Controllers\BarcodeController::class, 'index']) }}" class="btn btn-barcode-cancel">@lang('messages.cancel')</a>
                    </div>
                </div>
            </div>
            </div>
        </div>

        {!! Form::close() !!}
    </section>
</div>
@endsection

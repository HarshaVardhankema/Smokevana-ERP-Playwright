@extends('layouts.app')
@section('title', __('lang_v1.product_stock_history'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .stock-history-page {
        background: #EAEDED;
        min-height: 100vh;
        padding: 16px 20px 40px;
    }
    @media (max-width: 768px) {
        .stock-history-page {
            padding: 10px 12px 30px;
        }
    }

    .stock-history-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-top: 4px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .stock-history-banner-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #ffffff;
    }

    .stock-history-banner-title i {
        font-size: 22px;
        color: #fef3c7;
    }

    .stock-history-banner-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 0;
    }

    .stock-history-content {
        background: #EAEDED;
        padding: 0;
    }

    .stock-history-content .amazon-card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(15, 17, 17, 0.1);
        border: 1px solid #D5D9D9;
        overflow: hidden;
        background-color: #ffffff;
        margin-bottom: 20px;
    }

    .stock-history-content .amazon-card .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        background-color: #37475a !important;
        color: #fff !important;
        padding: 16px 24px !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        border: none !important;
        border-bottom: none !important;
        min-height: 52px;
        position: relative;
        border-left: 4px solid #FF9900 !important;
    }

    .stock-history-content .amazon-card .box-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #FF9900, #E47911);
    }

    .stock-history-content .amazon-card .box-header .box-title {
        font-size: 17px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stock-history-content .amazon-card .box-header .box-title i {
        color: #FF9900 !important;
    }

    .stock-history-content .amazon-card .tw-flow-root {
        padding: 24px 28px !important;
        background: linear-gradient(180deg, #fff 0%, #F7F8F8 100%) !important;
        border-top: 1px solid rgba(213, 217, 217, 0.5) !important;
    }

    @media (max-width: 768px) {
        .stock-history-content .amazon-card .tw-flow-root {
            padding: 18px 20px !important;
        }
    }

    .stock-history-content .form-group label {
        font-weight: 600;
        font-size: 14px;
        color: #0F1111;
        margin-bottom: 8px;
    }

    .stock-history-content .form-control,
    .stock-history-content .select2-container--default .select2-selection--single {
        background-color: #F7F8F8;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: #0F1111;
        transition: all 0.2s ease;
    }

    .stock-history-content .form-control:hover,
    .stock-history-content .select2-container--default .select2-selection--single:hover {
        background-color: #fff;
        border-color: #B8BDBD;
    }

    .stock-history-content .form-control:focus,
    .stock-history-content .select2-container--default.select2-container--focus .select2-selection--single {
        background-color: #fff;
        border-color: #0066C0;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15);
    }

    /* Stock details styling */
    #product_stock_history .table {
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
    }

    #product_stock_history .table thead th {
        background: #F7F8F8;
        color: #0F1111;
        font-weight: 600;
        font-size: 13px;
        padding: 12px 16px;
        border-bottom: 2px solid #D5D9D9;
    }

    #product_stock_history .table tbody td {
        padding: 12px 16px;
        color: #0F1111;
        border-bottom: 1px solid #E5E7EB;
    }

    #product_stock_history .table tbody tr:hover {
        background-color: #F7F8F8;
    }

    #product_stock_history strong {
        color: #0F1111;
        font-weight: 600;
        font-size: 14px;
    }
    /* Make summary card headers (QUANTITIES IN/OUT, TOTALS) white */
    #product_stock_history .stock-details-card strong {
        color: #ffffff !important;
    }

    #product_stock_history .table-condensed th,
    #product_stock_history .table-condensed td {
        padding: 8px 12px;
        font-size: 13px;
    }

    #product_stock_history .text-success {
        color: #0F8644 !important;
        font-weight: 600;
    }

    #product_stock_history .text-danger {
        color: #C40000 !important;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stock-history-banner {
            padding: 18px 20px;
            margin-bottom: 12px;
        }
        
        .stock-history-banner-title {
            font-size: 20px;
        }
        
        .stock-history-banner-subtitle {
            font-size: 12px;
        }
    }
</style>
@endsection

@section('content')

<div class="stock-history-page">
    <!-- Amazon-style Banner Header -->
    <div class="stock-history-banner">
        <div class="amazon-banner-content">
            <h1 class="stock-history-banner-title">
                <i class="fas fa-history"></i>
                @lang('lang_v1.product_stock_history')
            </h1>
            <p class="stock-history-banner-subtitle">View detailed stock movement history for your products</p>
        </div>
    </div>

    <!-- Main content -->
    <section class="content stock-history-content">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', [
                    'class' => 'box-primary amazon-card',
                    'title' => $product->name,
                    'title_svg' => '<i class="fas fa-box" style="margin-right:6px;"></i>'
                ])
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_id',  __('sale.product') . ':') !!}
                                {!! Form::select('product_id', [$product->id=>$product->name . ' - ' . $product->sku], $product->id, ['class' => 'form-control', 'style' => 'width:100%']); !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations, request()->input('location_id', null), ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                            </div>
                        </div>
                        @if($product->type == 'variable')
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="variation_id">@lang('product.variations'):</label>
                                    <select class="select2 form-control" name="variation_id" id="variation_id">
                                        @foreach($product->variations as $variation)
                                            <option value="{{$variation->id}}"
                                            @if(request()->input('variation_id', null) == $variation->id)
                                                selected
                                            @endif
                                            >{{$variation->product_variation->name}} - {{$variation->name}} ({{$variation->sub_sku}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="variation_id" name="variation_id" value="{{$product->variations->first()->id}}">
                        @endif
                    </div>
                @endcomponent
                @component('components.widget', [
                    'class' => 'box-primary amazon-card',
                    'title' => 'Stock History Details',
                    'title_svg' => '<i class="fas fa-list-alt" style="margin-right:6px;"></i>'
                ])
                    <div id="product_stock_history" style="display: none;"></div>
                @endcomponent
            </div>
        </div>
    </section>
</div>
@endsection

@section('javascript')
   <script type="text/javascript">
        $(document).ready( function(){
            load_stock_history($('#variation_id').val(), $('#location_id').val());

            $('#product_id').select2({
                ajax: {
                    url: '/products/list-no-variation',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        };
                    },
                },
                minimumInputLength: 1,
                escapeMarkup: function(m) {
                    return m;
                },
            }).on('select2:select', function (e) {
                var data = e.params.data;
                window.location.href = "{{url('/')}}/products/stock-history/" + data.id
            });
        });

       function load_stock_history(variation_id, location_id) {
            $('#product_stock_history').fadeOut();
            $.ajax({
                url: '/products/stock-history/' + variation_id + "?location_id=" + location_id,
                dataType: 'html',
                success: function(result) {
                    $('#product_stock_history')
                        .html(result)
                        .fadeIn();

                    __currency_convert_recursively($('#product_stock_history'));

                    $('#stock_history_table').DataTable({
                        searching: false,
                        fixedHeader:false,
                        ordering: false
                    });
                },
            });
       }

       $(document).on('change', '#variation_id, #location_id', function(){
            load_stock_history($('#variation_id').val(), $('#location_id').val());
       });
   </script>
@endsection
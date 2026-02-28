@extends('layouts.app')
@section('title', __('barcode.barcodes'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.barcode-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.barcode-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.barcode-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.barcode-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.barcode-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.barcode-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.barcode-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.barcode-page .box-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.barcode-page .box-title { color: #fff !important; font-weight: 600; }
.barcode-page .box-tools { margin: 0; }
.barcode-page #dynamic_button {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
}
.barcode-page #barcode_table thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.barcode-page #barcode_table tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.barcode-page #barcode_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.barcode-page #barcode_table tbody tr:hover td { background: #fff8e7 !important; }
.barcode-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.barcode-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page barcode-page">
<section class="content-header">
    <h1>
        <i class="fa fa-barcode page-header-icon"></i>
        @lang('barcode.barcodes')
        <small>@lang('barcode.manage_your_barcodes')</small>
    </h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('barcode.all_your_barcode')])
        @slot('tool')
            <div class="box-tools">
                <a id="dynamic_button" class="tw-dw-btn pull-right"
                    href="{{ action([\App\Http\Controllers\BarcodeController::class, 'create']) }}">
                    <i class="fa fa-plus"></i> @lang('barcode.add_new_setting')
                </a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="barcode_table">
                <thead>
                    <tr>
                        <th>@lang('barcode.setting_name')</th>
                        <th>@lang('barcode.setting_description')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
</div>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        var barcode_table = $('#barcode_table').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:false,
            buttons:[],
            ajax: '/barcodes',
            bPaginate: false,
            columnDefs: [ {
                "targets": 2,
                "orderable": false,
                "searchable": false
            } ]
        });
        $(document).on('click', 'button.delete_barcode_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_barcode,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success === true){
                                toastr.success(result.msg);
                                barcode_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click', 'button.set_default', function(){
            var href = $(this).data('href');
            var data = $(this).serialize();

            $.ajax({
                method: "get",
                url: href,
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success === true){
                        toastr.success(result.msg);
                        barcode_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    });
</script>
@endsection
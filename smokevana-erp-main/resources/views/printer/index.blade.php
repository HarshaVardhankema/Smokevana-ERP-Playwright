@extends('layouts.app')
@section('title', __('printer.printers'))
@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
.printer-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.printer-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e; border-radius: 10px; padding: 24px 32px !important;
    margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative; overflow: hidden;
}
.printer-page .content-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911); opacity: 0.9;
}
.printer-page .content-header h1 {
    display: flex; align-items: center; gap: 12px;
    font-size: 1.5rem !important; color: #fff !important; margin: 0 !important;
}
.printer-page .content-header h1 .page-header-icon { color: #ffffff !important; }
.printer-page .content-header h1 small {
    display: block; font-size: 13px !important; font-weight: 500 !important;
    color: #b8c4ce !important; margin-top: 4px;
}
.printer-page .box-primary {
    background: #fff !important; border: 1px solid #D5D9D9 !important;
    border-radius: 10px !important; box-shadow: 0 2px 5px rgba(15,17,17,0.08);
}
.printer-page .box-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border-bottom: 2px solid #ff9900 !important;
    padding: 14px 20px !important; border-radius: 10px 10px 0 0;
}
.printer-page .box-title { color: #fff !important; font-weight: 600; }
.printer-page .box-tools { margin: 0; }
.printer-page #dynamic_button {
    background: linear-gradient(to bottom, #ffd97d 0%, #ff9900 5%, #e47911 100%) !important;
    border: 1px solid #a88734 !important; color: #0f1111 !important;
    font-weight: 600; border-radius: 8px; padding: 8px 18px; margin: 0 !important;
}
.printer-page #printer_table thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important; padding: 12px 14px !important;
}
.printer-page #printer_table tbody td {
    padding: 12px 14px; color: #0f1111; border-color: #e5e7eb;
}
.printer-page #printer_table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.printer-page #printer_table tbody tr:hover td { background: #fff8e7 !important; }
.printer-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.printer-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ff9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
</style>
@endsection

@section('content')
<div class="admin-amazon-page printer-page">
<section class="content-header">
    <h1>
        <i class="fa fa-print page-header-icon"></i>
        @lang('printer.printers')
        <small>@lang('printer.manage_your_printers')</small>
    </h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('printer.all_your_printer')])
        @slot('tool')
            <div class="box-tools">
                <a id="dynamic_button" class="tw-dw-btn pull-right"
                    href="{{ action([\App\Http\Controllers\PrinterController::class, 'create']) }}">
                    <i class="fa fa-plus"></i> @lang('printer.add_printer')
                </a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="printer_table">
                <thead>
                    <tr>
                        <th>@lang('printer.name')</th>
                        <th>@lang('printer.connection_type')</th>
                        <th>@lang('printer.capability_profile')</th>
                        <th>@lang('printer.character_per_line')</th>
                        <th>@lang('printer.ip_address')</th>
                        <th>@lang('printer.port')</th>
                        <th>@lang('printer.path')</th>
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
        var printer_table = $('#printer_table').DataTable({
            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:false,
            buttons:[],
            ajax: '/printers',
            bPaginate: false,
            columnDefs: [ {
                "targets": 2,
                "orderable": false,
                "searchable": false
            } ]
        });
        $(document).on('click', 'button.delete_printer_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_printer,
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
                                printer_table.ajax.reload();
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
                        printer_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    });
</script>
@endsection
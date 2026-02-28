@extends('layouts.app')

@section('title', 'Multi Channel')

@section('css')
<style>
    .amazon-multichannel-page { background: #f3f3f3; min-height: calc(100vh - 120px); padding: 20px 0; }
    .amazon-multichannel-page .page-header-card {
        background: linear-gradient(90deg, #232f3e 0%, #37475a 100%);
        border-radius: 16px;
        padding: 24px 30px;
        margin-bottom: 24px;
        box-shadow: 0 10px 40px rgba(15,17,17,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .amazon-multichannel-page .page-header-card h1 {
        color: #fff; font-size: 26px; font-weight: 700; margin: 0;
        display: flex; align-items: center; gap: 14px;
    }
    .amazon-multichannel-page .page-header-card h1 .icon-box {
        background: rgba(0,0,0,0.25); border-radius: 12px; padding: 10px; display: flex;
    }
    .amazon-multichannel-page .btn-add {
        background: linear-gradient(180deg,#FFD814 0%,#FCD200 100%) !important;
        color: #131921 !important; border: 1px solid #FCD200;
        padding: 12px 24px; border-radius: 12px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .amazon-multichannel-page .btn-add:hover {
        background: linear-gradient(180deg,#F7CA00 0%,#F2C200 100%) !important;
        color: #131921 !important; border-color: #F2C200;
    }
    .amazon-multichannel-page .content-card {
        background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden; margin-bottom: 24px;
    }
    .amazon-multichannel-page .content-card .box-header { padding: 20px 24px; border-bottom: 2px solid #f0f0f0; background: #fff; }
    .amazon-multichannel-page .content-card .box-title { color: #1a1a2e; font-weight: 600; font-size: 18px; }
    .amazon-multichannel-page .content-card .box-title i { color: #FF9900; }
    .amazon-multichannel-page #multi_channel_table thead th {
        background: #f8f9fa !important; color: #1a1a2e !important;
        border-bottom: 2px solid #e5e7eb; padding: 14px 16px;
        font-weight: 600; font-size: 12px; text-transform: uppercase;
    }
    .amazon-multichannel-page #multi_channel_table tbody td { padding: 14px 16px; color: #131921; }
    .amazon-multichannel-page #multi_channel_table tbody tr:hover { background: #f9fafb !important; }
    .amazon-multichannel-page .dt-buttons .btn,
    .amazon-multichannel-page .dt-buttons button {
        background: linear-gradient(180deg,#FFD814 0%,#FCD200 100%) !important;
        color: #131921 !important; border-color: #FCD200 !important;
        border-radius: 10px; font-weight: 600;
    }
    .amazon-multichannel-page .dt-buttons .btn:hover,
    .amazon-multichannel-page .dt-buttons button:hover {
        background: linear-gradient(180deg,#F7CA00 0%,#F2C200 100%) !important;
        color: #131921 !important; border-color: #F2C200 !important;
    }
    .amazon-multichannel-page .dataTables_filter input { border-radius: 10px; border: 1px solid #e0e0e0; }
    .amazon-multichannel-page .dataTables_length select { border-radius: 10px; border: 1px solid #e0e0e0; }
    .amazon-multichannel-page .edit_multichannel_button { background: #FF9900 !important; color: #fff !important; border-color: #e88b00 !important; }
    .amazon-multichannel-page .edit_multichannel_button:hover { background: #e88b00 !important; color: #fff !important; }
    .amazon-multichannel-page .view_multichannel_button { background: #fff !important; color: #37475a !important; border: 1px solid #37475a !important; }
    .amazon-multichannel-page .view_multichannel_button:hover { background: #f0f0f0 !important; }
</style>
@endsection

@section('content')
<div class="amazon-multichannel-page">
    <div class="container-fluid">
        <div class="page-header-card">
            <h1>
                <div class="icon-box"><i class="fas fa-layer-group"></i></div>
                Multi Channel
            </h1>
            <a href="{{ action([\App\Http\Controllers\ECOM\MultichannelController::class, 'create']) }}" class="btn-add">
                <i class="fas fa-plus"></i> @lang('messages.add')
            </a>
        </div>

        <div class="content-card">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Channels'])
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="multi_channel_table">
                    <thead>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Visibility') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('URL') }}</th>
                            <th>{{ __('Thumbnail') }}</th>
                            <th>{{ __('Short Meta') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent
        </div>
    </div>
</div>
@endsection
@section('javascript')
    <script type="text/javascript">
    $(document).ready(function() {
        $('#multi_channel_table').DataTable({
            processing: true,
            language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            ajax: "{{ action([\App\Http\Controllers\ECOM\MultichannelController::class, 'multichannel']) }}",
            columns: [
                { data: 'type', name: 'type' },
                { data: 'visibility', name: 'visibility' },
                { data: 'status', name: 'status' },
                { data: 'title', name: 'title' },
                { data: 'url', name: 'url' },
                { data: 'thumbnail', name: 'thumbnail' },
                { data: 'short_meta', name: 'short_meta' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Handle edit button clicks
        $(document).on('click', '.edit_multichannel_button', function() {
            var url = $(this).data('href');
            window.location.href = url;
        });

        // Handle view button clicks
        $(document).on('click', '.view_multichannel_button', function() {
            var url = $(this).data('href');
            window.open(url, '_blank');
        });
    });
 </script>
@endsection


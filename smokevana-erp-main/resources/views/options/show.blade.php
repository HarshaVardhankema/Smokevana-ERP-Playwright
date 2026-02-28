@extends('layouts.app')
@section('title', 'View Option')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Option Details</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#details_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-info-circle"></i> Details</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="details_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">ID:</th>
                                            <td>{{ $option->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td>{{ $option->type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Key:</th>
                                            <td>{{ $option->key ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Value:</th>
                                            <td>
                                                <div style="max-height: 400px; overflow-y: auto; background: #f5f5f5; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
                                                    {!! $option->value ?? 'N/A' !!}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Modal Type:</th>
                                            <td>{{ $option->modal_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Modal ID:</th>
                                            <td>{{ $option->modal_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Use For:</th>
                                            <td>
                                                <span class="label label-{{ $option->use_for == 'frontend' ? 'success' : 'info' }}">
                                                    {{ ucfirst($option->use_for) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By:</th>
                                            <td>{{ $option->creator?->user_full_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated By:</th>
                                            <td>{{ $option->updater?->user_full_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At:</th>
                                            <td>{{ \Carbon\Carbon::parse($option->created_at)->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At:</th>
                                            <td>{{ \Carbon\Carbon::parse($option->updated_at)->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('options.edit', $option->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> @lang('messages.edit')
                </a>
                <a href="{{ route('options.index') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    @endcomponent
</section>

@endsection


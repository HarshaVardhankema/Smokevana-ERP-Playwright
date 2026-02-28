@extends('layouts.app')
@section('title', 'View Complaint')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Complaint Details</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID:</strong>
                        <p>{{ $complaint->id }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Request Type:</strong>
                        <p>{{ ucfirst(str_replace('_', ' ', $complaint->request_type)) }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'in_progress' => 'info',
                                    'resolved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $color = $statusColors[$complaint->status] ?? 'default';
                            @endphp
                            <span class="label label-{{ $color }}">{{ ucfirst($complaint->status) }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Created By:</strong>
                        <p>{{ $complaint->creator ? $complaint->creator->username : 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Customer/Contact:</strong>
                        <p>{{ $complaint->contact ? $complaint->contact->name : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Invoice No:</strong>
                        <p>
                            @if($complaint->transaction)
                                {{ $complaint->transaction->invoice_no }} 
                                <span class="label label-default">{{ ucfirst($complaint->transaction->type) }}</span>
                                <br><small class="text-muted">Date: {{ date('d M Y', strtotime($complaint->transaction->transaction_date)) }}</small>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Products/Variations:</strong>
                        @if($complaint->variation_ids && is_array($complaint->variation_ids) && count($complaint->variation_ids) > 0)
                            @php
                                $variations = \App\Variation::with('product')
                                    ->whereIn('id', $complaint->variation_ids)
                                    ->get();
                            @endphp
                            <div style="margin-top: 10px;">
                                @foreach($variations as $variation)
                                    <div class="alert alert-info" style="margin-bottom: 5px;">
                                        <strong>{{ $variation->product->name }}</strong>
                                        @if($variation->name)
                                            - {{ $variation->name }}
                                        @endif
                                        <br><small class="text-muted">SKU: {{ $variation->sub_sku ?: 'N/A' }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>N/A</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <strong>Description:</strong>
                        <p>{{ $complaint->description ?: 'No description provided' }}</p>
                    </div>
                </div>

                @if($complaint->admin_response)
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Admin Response:</strong>
                            <p class="alert alert-info">{{ $complaint->admin_response }}</p>
                        </div>
                    </div>
                @endif

                @if($complaint->attachments && is_array($complaint->attachments) && count($complaint->attachments) > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Attached Images:</strong>
                            <div class="row" style="margin-top: 10px;">
                                @foreach($complaint->attachments as $attachment)
                                    @if($attachment)
                                        <div class="col-md-3" style="margin-bottom: 10px;">
                                            <div class="thumbnail">
                                                <a href="{{ asset($attachment) }}" target="_blank" data-lightbox="complaint-images">
                                                    <img src="{{ asset($attachment) }}" style="width: 100%; height: 150px; object-fit: cover;" alt="Complaint Image">
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <strong>Created At:</strong>
                        <p>{{ $complaint->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Last Updated:</strong>
                        <p>{{ $complaint->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ action([\App\Http\Controllers\ComplaintController::class, 'index']) }}" class="tw-dw-btn tw-dw-btn-primary">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        @can('complaint.update')
                            <a href="{{ action([\App\Http\Controllers\ComplaintController::class, 'edit'], [$complaint->id]) }}" class="tw-dw-btn tw-dw-btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</section>

@endsection


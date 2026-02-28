@extends('layouts.app')
@section('title', 'Vendor Details - ' . $vendor->display_name)

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-user"></i> {{ $vendor->display_name }}
        {!! $vendor->status_badge !!}
    </h1>
</section>

<section class="content">
    <div class="row">
        <!-- Vendor Info -->
        <div class="col-lg-4">
            @component('components.widget', ['class' => 'box-primary', 'title' => 'Vendor Information'])
                <div class="tw-space-y-3">
                    @if($vendor->company_name)
                    <div>
                        <strong>Company:</strong><br>{{ $vendor->company_name }}
                    </div>
                    @endif
                    @if($vendor->email)
                    <div>
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a>
                    </div>
                    @endif
                    @if($vendor->phone)
                    <div>
                        <strong>Phone:</strong><br>
                        <a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a>
                    </div>
                    @endif
                    @if($vendor->address)
                    <div>
                        <strong>Address:</strong><br>{{ $vendor->address }}
                    </div>
                    @endif
                    @if($vendor->wp_term_id)
                    <div>
                        <strong>WooCommerce Term ID:</strong><br>{{ $vendor->wp_term_id }}
                    </div>
                    @endif
                </div>
                <hr>
                <div class="tw-flex tw-gap-2">
                    <a href="{{ route('dropship.vendors.edit', $vendor->id) }}" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('dropship.vendors.products', $vendor->id) }}" class="tw-dw-btn tw-dw-btn-sm tw-dw-btn-info">
                        <i class="fas fa-boxes"></i> Products
                    </a>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-info', 'title' => 'Pricing & Commission'])
                <div class="tw-space-y-2">
                    <div class="tw-flex tw-justify-between">
                        <span>Margin:</span>
                        <span class="tw-font-semibold">{{ $vendor->margin_percentage }}%</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Default Markup:</span>
                        <span class="tw-font-semibold">{{ $vendor->default_markup_percentage }}%</span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Commission:</span>
                        <span class="tw-font-semibold">
                            {{ $vendor->commission_value }}{{ $vendor->commission_type === 'percentage' ? '%' : '' }}
                        </span>
                    </div>
                    <div class="tw-flex tw-justify-between">
                        <span>Payment Terms:</span>
                        <span class="tw-font-semibold">{{ ucfirst($vendor->payment_terms) }}</span>
                    </div>
                </div>
            @endcomponent
        </div>

        <!-- Performance Metrics -->
        <div class="col-lg-8">
            @component('components.widget', ['class' => 'box-success', 'title' => 'Performance Metrics'])
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="tw-text-center tw-p-4 tw-rounded tw-bg-blue-50">
                            <div class="tw-text-3xl tw-font-bold tw-text-blue-600">{{ $performance['total_orders'] }}</div>
                            <div class="tw-text-sm tw-text-gray-600">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="tw-text-center tw-p-4 tw-rounded tw-bg-green-50">
                            <div class="tw-text-3xl tw-font-bold tw-text-green-600">{{ $performance['completed_orders'] }}</div>
                            <div class="tw-text-sm tw-text-gray-600">Completed</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="tw-text-center tw-p-4 tw-rounded tw-bg-yellow-50">
                            <div class="tw-text-3xl tw-font-bold tw-text-yellow-600">{{ $performance['pending_orders'] }}</div>
                            <div class="tw-text-sm tw-text-gray-600">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="tw-text-center tw-p-4 tw-rounded tw-bg-purple-50">
                            <div class="tw-text-3xl tw-font-bold tw-text-purple-600">{{ $performance['completion_rate'] }}%</div>
                            <div class="tw-text-sm tw-text-gray-600">Completion Rate</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <span>Avg. Fulfillment Time:</span>
                            <span class="tw-font-semibold">{{ $performance['avg_fulfillment_hours'] }} hours</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <span>Total Revenue:</span>
                            <span class="tw-font-semibold tw-text-green-600">@format_currency($performance['total_revenue'])</span>
                        </div>
                    </div>
                </div>
            @endcomponent

            @component('components.widget', ['class' => 'box-warning', 'title' => 'Recent Orders'])
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Tracking</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('dropship.orders.show', $order->id) }}">
                                        {{ $order->transaction->invoice_no ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{!! $order->status_badge !!}</td>
                                <td>{{ $order->tracking_number ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No orders yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentOrders->count() > 0)
                <div class="tw-mt-3">
                    <a href="{{ route('dropship.vendors.orders', $vendor->id) }}" class="tw-dw-btn tw-dw-btn-ghost tw-dw-btn-sm">
                        View All Orders <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                @endif
            @endcomponent
        </div>
    </div>
</section>
@endsection













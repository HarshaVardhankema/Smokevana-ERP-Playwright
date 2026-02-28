@extends('layouts.app')
@section('title', 'New Screens Dashboard')

@section('content')
<div class="tw-p-6 tw-bg-gray-100 tw-min-h-screen tw-overflow-x-hidden">
    <div class="tw-flex tw-flex-row tw-gap-4 tw-w-full">
        <!-- Gross Merchandise Value (GMV) Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Gradient Border Top -->
            <div class="tw-absolute tw-top-0 tw-left-0 tw-right-0 tw-h-1 tw-bg-gradient-to-r tw-from-green-400 tw-to-yellow-400"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div class="tw-p-4" style="border-top: 3px solid #FFD700;">
                <!-- Icon Circle -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-4">
                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-green-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-dollar-sign tw-text-green-600 tw-text-xl"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Gross Merchandise Value (GMV)
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-green-600 tw-mb-2 tw-break-words">
                    $347,820.00
                </div>
                
                <!-- Change Indicator -->
                <div class="tw-flex tw-items-center tw-gap-1 tw-mb-2">
                    <i class="fas fa-arrow-up tw-text-green-600 tw-text-sm"></i>
                    <span class="tw-text-sm tw-font-medium tw-text-green-600">+18.4% vs last month</span>
                </div>
                
                <!-- Bottom Text -->
                <p class="tw-text-xs tw-text-gray-600 tw-mt-2">This Month</p>
            </div>
        </div>

        <!-- Platform Revenue Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Gradient Border Top -->
            <div class="tw-absolute tw-top-0 tw-left-0 tw-right-0 tw-h-1 tw-bg-gradient-to-r tw-from-purple-400 tw-to-orange-400"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div class="tw-p-4" style="border-top: 3px solid blue;">
                <!-- Icon Circle -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-4">
                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-purple-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-tag tw-text-purple-600 tw-text-xl"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Platform Revenue
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-purple-600 tw-mb-2 tw-break-words">
                    $48,295.00
                </div>
                
                <!-- Change Indicator -->
                <div class="tw-flex tw-items-center tw-gap-1 tw-mb-2">
                    <i class="fas fa-arrow-up tw-text-green-600 tw-text-sm"></i>
                    <span class="tw-text-sm tw-font-medium tw-text-green-600">+22.1% vs last month</span>
                </div>
                
                <!-- Bottom Text -->
                <p class="tw-text-xs tw-text-purple-600 tw-mt-2">Avg Take Rate: 13.9%</p>
            </div>
        </div>

        <!-- Active Sellers Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Gradient Border Top -->
            <div class="tw-absolute tw-top-0 tw-left-0 tw-right-0 tw-h-1 tw-bg-gradient-to-r tw-from-blue-400 tw-to-orange-400"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div class="tw-p-4" style="border-top: 3px solid purple;">
                <!-- Icon Circle -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-4">
                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-blue-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-user-tie tw-text-blue-600 tw-text-xl"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Active Sellers
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-2 tw-break-words">
                    1,247
                </div>
                
                <!-- Change Indicator -->
                <div class="tw-flex tw-items-center tw-gap-1 tw-mb-2">
                    <i class="fas fa-arrow-up tw-text-blue-600 tw-text-sm"></i>
                    <span class="tw-text-sm tw-font-medium tw-text-blue-600">+34 this month</span>
                </div>
            </div>
        </div>

        <!-- Active Buyers Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Gradient Border Top -->
            <div class="tw-absolute tw-top-0 tw-left-0 tw-right-0 tw-h-1 tw-bg-gradient-to-r tw-from-teal-400 tw-to-orange-400"></div>
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div class="tw-p-4" style="border-top: 3px solid teal;">
                <!-- Icon Circle -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-4">
                    <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-teal-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-shopping-bag tw-text-teal-600 tw-text-xl"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Active Buyers
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-mb-2 tw-break-words">
                    8,430
                </div>
                
                <!-- Change Indicator -->
                <div class="tw-flex tw-items-center tw-gap-1 tw-mb-2">
                    <i class="fas fa-arrow-up tw-text-blue-600 tw-text-sm"></i>
                    <span class="tw-text-sm tw-font-medium tw-text-blue-600">+412 this month</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second Row: 6 Cards -->
    <div class="tw-flex tw-flex-row tw-gap-3 tw-w-full tw-mt-6">
        <!-- Orders Today Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Left Border -->
            <div class="tw-absolute tw-top-0 tw-bottom-0 tw-left-0 tw-w-1.5 tw-bg-yellow-500 tw-rounded-l-lg"></div>
            <div class="tw-p-4" style="border-left: 3px solid #FFD700;">
                <!-- Icon -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-3" >
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-yellow-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-shopping-bag tw-text-yellow-600 tw-text-lg"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Orders Today
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-break-words">
                    287
                </div>
            </div>
        </div>

        <!-- GMV Today Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Left Border -->
            <div class="tw-absolute tw-top-0 tw-bottom-0 tw-left-0 tw-w-1 tw-bg-green-500 tw-rounded-l-lg"></div>
            <div class="tw-p-4" style="border-left: 3px solid green;">
                <!-- Icon -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-green-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-chart-line tw-text-green-600 tw-text-lg"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    GMV Today
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-break-words">
                    $24,560
                </div>
            </div>
        </div>

        <!-- Fees Today Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Left Border -->
            <div class="tw-absolute tw-top-0 tw-bottom-0 tw-left-0 tw-w-1 tw-bg-purple-500 tw-rounded-l-lg"></div>
            <div class="tw-p-4" style="border-left: 3px solid purple;">
                <!-- Icon -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-purple-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-coins tw-text-purple-600 tw-text-lg"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Fees Today
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-break-words">
                    $3,410
                </div>
            </div>
        </div>

        <!-- Avg Order Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Left Border -->
            <div class="tw-absolute tw-top-0 tw-bottom-0 tw-left-0 tw-w-1 tw-bg-blue-500 tw-rounded-l-lg"></div>
            <div class="tw-p-4" style="border-left: 3px solid blue;">
                <!-- Icon -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-blue-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-calculator tw-text-blue-600 tw-text-lg"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Avg Order
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-gray-900 tw-break-words">
                    $485
                </div>
            </div>
        </div>

        <!-- Alerts Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Left Border -->
            <div class="tw-absolute tw-top-0 tw-bottom-0 tw-left-0 tw-w-1 tw-bg-red-500 tw-rounded-l-lg"></div>
            <div class="tw-p-4" style="border-left: 3px solid red;">
                <!-- Icon -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-red-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-exclamation-triangle tw-text-red-600 tw-text-lg"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Alerts
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-red-600 tw-break-words">
                    7
                </div>
            </div>
        </div>

        <!-- Disputes Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0">
            <!-- Left Border -->
            <div class="tw-absolute tw-top-0 tw-bottom-0 tw-left-0 tw-w-1.5 tw-bg-yellow-500 tw-rounded-l-lg"></div>
            <div class="tw-p-4" style="border-left: 3px solid #f8e18b;" >
                <!-- Icon -->
                <div class="tw-flex tw-items-start tw-justify-between tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-yellow-100 tw-flex tw-items-center tw-justify-center">
                        <i class="fas fa-balance-scale tw-text-yellow-600 tw-text-lg"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 class="tw-text-xs tw-font-semibold tw-text-gray-700 tw-mb-2 tw-uppercase tw-tracking-wide tw-leading-tight">
                    Disputes
                </h3>
                
                <!-- Main Value -->
                <div class="tw-text-2xl tw-font-bold tw-text-yellow-600 tw-break-words">
                    4
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row: 2 Chart Cards -->
    <div class="tw-flex tw-flex-row tw-gap-6 tw-w-full tw-mt-6">
        <!-- GMV & Platform Revenue Chart Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0" style="flex: 1 1 60%;">
            <div class="tw-p-6">
                <!-- Header -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <div>
                        <h2 class="tw-text-lg tw-font-bold tw-text-gray-900 tw-mb-1">GMV & Platform Revenue</h2>
                        <p class="tw-text-sm tw-text-gray-600">Last 12 Months</p>
                    </div>
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <div class="tw-flex tw-items-center tw-gap-2 tw-bg-gray-100 tw-rounded-lg tw-p-1">
                            <button class="tw-px-4 tw-py-1.5 tw-text-sm tw-font-medium tw-bg-white tw-text-gray-700 tw-rounded tw-shadow-sm tw-transition-colors" id="gmv-monthly-btn">
                                Monthly
                            </button>
                            <button class="tw-px-4 tw-py-1.5 tw-text-sm tw-font-medium tw-text-gray-600 tw-rounded tw-transition-colors hover:tw-text-gray-900" id="gmv-weekly-btn">
                                Weekly
                            </button>
                        </div>
                        <a href="#" class="tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-font-medium">Download Report</a>
                    </div>
                </div>
                
                <!-- Chart Container -->
                <div class="tw-relative" style="height: 400px;">
                    <canvas id="gmvRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown Chart Card -->
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden tw-relative tw-flex-1 tw-min-w-0" style="flex: 1 1 40%;">
            <div class="tw-p-6">
                <!-- Header -->
                <div class="tw-mb-4">
                    <h2 class="tw-text-lg tw-font-bold tw-text-gray-900 tw-mb-1">Revenue Breakdown</h2>
                    <p class="tw-text-sm tw-text-gray-600">Revenue by Stream — This Month</p>
                </div>
                
                <!-- Chart Container -->
                <div class="tw-relative tw-h-80 tw-flex tw-items-center tw-justify-center">
                    <canvas id="revenueBreakdownChart"></canvas>
                    <!-- Center Text Overlay -->
                    <div class="tw-absolute tw-inset-0 tw-flex tw-items-center tw-justify-center tw-pointer-events-none">
                        <div class="tw-text-center">
                            <div class="tw-text-3xl tw-font-bold tw-text-gray-900">$48,295</div>
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div style="margin-top: 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #f97316; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Referral Fees</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">$21,733 (45%)</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #3b82f6; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">FBS Fees</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">$10,625 (22%)</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #10b981; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Subscriptions</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">$6,761 (14%)</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #a855f7; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Advertising</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">$5,795 (12%)</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #6b7280; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Other</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">$3,381 (7%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fourth Row: Platform Activity and Admin Actions -->
    <div class="tw-flex tw-flex-row tw-gap-6 tw-w-full tw-mt-6">
        <!-- Left Column: Platform Activity -->
        <div class="tw-flex-1" style="flex: 1 1 60%;">
            <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden">
                <div class="tw-p-6">
                    <!-- Header -->
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                        <h2 class="tw-text-lg tw-font-bold tw-text-gray-900">Platform Activity</h2>
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; animation: pulse 2s infinite;"></div>
                            <span style="color: #10b981; font-size: 13px; font-weight: 500;">Live</span>
                        </div>
                    </div>
                    
                    <!-- Tabs -->
                    <div class="tw-flex tw-gap-2 tw-mb-4 tw-border-b tw-border-gray-200">
                        <button class="tab-btn active" data-tab="all" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; border-bottom: 2px solid #111827; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;">All</button>
                        <button class="tab-btn" data-tab="orders" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;">Orders</button>
                        <button class="tab-btn" data-tab="users" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;">Users</button>
                        <button class="tab-btn" data-tab="compliance" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;">Compliance</button>
                        <button class="tab-btn" data-tab="disputes" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;">Disputes</button>
                        <button class="tab-btn" data-tab="payouts" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; background: none; border-top: none; border-left: none; border-right: none; cursor: pointer;">Payouts</button>
                    </div>
                    
                    <!-- Activity List -->
                    <div class="activity-list" style="max-height: 500px; overflow-y: auto;">
                        <!-- All Activities -->
                        <div class="tab-content active" data-content="all">
                            <div class="tw-space-y-4">
                                <!-- New Order -->
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #10b981; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">New order #SMK-2026-0287</span>
                                            <span style="color: #6b7280; font-size: 12px;">2 min ago</span>
                                        </div>
                                        <div style="color: #374151; font-size: 13px;">$1,240.00</div>
                                        <div style="color: #6b7280; font-size: 12px; margin-top: 2px;">Platform fee: $148.80 (12%)</div>
                                    </div>
                                </div>
                                
                                <!-- New Seller Registered -->
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb; margin-top: 12px;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #3b82f6; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">New seller registered: Green Leaf Distributors (CA)</span>
                                            <span style="color: #6b7280; font-size: 12px;">5 min ago</span>
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span style="background-color: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Pending Review</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- License Expiration Alert -->
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb; margin-top: 12px;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #f97316; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">License expiration alert: Seller VapeCo</span>
                                            <span style="color: #6b7280; font-size: 12px;">8 min ago</span>
                                        </div>
                                        <div style="color: #374151; font-size: 13px;">NY wholesale license expires in <span style="color: #ef4444; font-weight: 600;">7 days</span></div>
                                    </div>
                                </div>
                                
                                <!-- Dispute Opened -->
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb; margin-top: 12px;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #ef4444; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">Dispute opened: Order #SMK-2026-0251</span>
                                            <span style="color: #6b7280; font-size: 12px;">12 min ago</span>
                                        </div>
                                        <div style="color: #374151; font-size: 13px;">Quality complaint - $465.00</div>
                                    </div>
                                </div>
                                
                                <!-- Seller Payout Processed -->
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb; margin-top: 12px;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #a855f7; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">Seller payout processed: $12,340 to Curevana LLC</span>
                                            <span style="color: #6b7280; font-size: 12px;">15 min ago</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other tab contents (initially hidden) -->
                        <div class="tab-content" data-content="orders" style="display: none;">
                            <div class="tw-space-y-4">
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #10b981; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">New order #SMK-2026-0287</span>
                                            <span style="color: #6b7280; font-size: 12px;">2 min ago</span>
                                        </div>
                                        <div style="color: #374151; font-size: 13px;">$1,240.00</div>
                                        <div style="color: #6b7280; font-size: 12px; margin-top: 2px;">Platform fee: $148.80 (12%)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-content" data-content="users" style="display: none;">
                            <div class="tw-space-y-4">
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #3b82f6; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">New seller registered: Green Leaf Distributors (CA)</span>
                                            <span style="color: #6b7280; font-size: 12px;">5 min ago</span>
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span style="background-color: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Pending Review</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-content" data-content="compliance" style="display: none;">
                            <div class="tw-space-y-4">
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #f97316; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">License expiration alert: Seller VapeCo</span>
                                            <span style="color: #6b7280; font-size: 12px;">8 min ago</span>
                                        </div>
                                        <div style="color: #374151; font-size: 13px;">NY wholesale license expires in <span style="color: #ef4444; font-weight: 600;">7 days</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-content" data-content="disputes" style="display: none;">
                            <div class="tw-space-y-4">
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #ef4444; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">Dispute opened: Order #SMK-2026-0251</span>
                                            <span style="color: #6b7280; font-size: 12px;">12 min ago</span>
                                        </div>
                                        <div style="color: #374151; font-size: 13px;">Quality complaint - $465.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-content" data-content="payouts" style="display: none;">
                            <div class="tw-space-y-4">
                                <div class="tw-flex tw-items-start tw-gap-3" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: #a855f7; margin-top: 4px; flex-shrink: 0;"></div>
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-items-center tw-justify-between tw-mb-1">
                                            <span style="color: #111827; font-size: 14px; font-weight: 500;">Seller payout processed: $12,340 to Curevana LLC</span>
                                            <span style="color: #6b7280; font-size: 12px;">15 min ago</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column: Pending Admin Actions and Platform Health Gauges -->
        <div class="tw-flex-1" style="flex: 1 1 40%; display: flex; flex-direction: column; gap: 24px;">
            <!-- Top Row: Pending Admin Actions -->
            <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden">
                <div class="tw-p-6">
                    <h2 class="tw-text-lg tw-font-bold tw-text-gray-900 tw-mb-4">Pending Admin Actions</h2>
                    <div class="tw-space-y-3">
                        <!-- Seller Applications -->
                        <div class="tw-flex tw-items-center tw-justify-between" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <i class="fas fa-shield-alt" style="color: #3b82f6; font-size: 18px;"></i>
                                <span style="color: #374151; font-size: 14px;">12 seller applications pending review</span>
                            </div>
                            <button style="background-color: #3b82f6; color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">Review</button>
                        </div>
                        
                        <!-- Products in Moderation -->
                        <div class="tw-flex tw-items-center tw-justify-between" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <i class="fas fa-flag" style="color: #f97316; font-size: 18px;"></i>
                                <span style="color: #374151; font-size: 14px;">7 products in moderation queue</span>
                            </div>
                            <button style="background-color: #f97316; color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">Moderate</button>
                        </div>
                        
                        <!-- Open Disputes -->
                        <div class="tw-flex tw-items-center tw-justify-between" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <i class="fas fa-gavel" style="color: #ef4444; font-size: 18px;"></i>
                                <span style="color: #374151; font-size: 14px;">4 open disputes awaiting resolution</span>
                            </div>
                            <button style="background-color: #ef4444; color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">Resolve</button>
                        </div>
                        
                        <!-- Compliance Escalations -->
                        <div class="tw-flex tw-items-center tw-justify-between" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 18px;"></i>
                                <span style="color: #374151; font-size: 14px;">3 compliance escalations</span>
                            </div>
                            <button style="background-color: #f59e0b; color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">View</button>
                        </div>
                        
                        <!-- Seller Payouts on Hold -->
                        <div class="tw-flex tw-items-center tw-justify-between" style="padding: 12px; border-radius: 8px; background-color: #f9fafb;">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <i class="fas fa-clock" style="color: #6b7280; font-size: 18px;"></i>
                                <span style="color: #374151; font-size: 14px;">2 seller payouts on hold</span>
                            </div>
                            <button style="background-color: #6b7280; color: white; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">Process</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Row: Platform Health Gauges -->
            <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden">
                <div class="tw-p-6">
                    <h2 class="tw-text-lg tw-font-bold tw-text-gray-900 tw-mb-4">Platform Health Gauges</h2>
                    <div style="display: flex; flex-direction: row; gap: 16px; justify-content: space-between; align-items: center; width: 100%;">
                        <!-- Seller Compliance -->
                        <div style="flex: 1; text-align: center; min-width: 0;">
                            <div class="gauge-container" style="position: relative; width: 90px; height: 90px; margin: 0 auto;">
                                <canvas id="gauge1" width="90" height="90" style="transform: rotate(-90deg); width: 90px; height: 90px;"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                    <div style="font-size: 16px; font-weight: 700; color: #10b981;">94%</div>
                                </div>
                            </div>
                            <div style="margin-top: 6px; color: #374151; font-size: 12px; font-weight: 500;">Seller Compliance</div>
                        </div>
                        
                        <!-- Order Fulfillment -->
                        <div style="flex: 1; text-align: center; min-width: 0;">
                            <div class="gauge-container" style="position: relative; width: 90px; height: 90px; margin: 0 auto;">
                                <canvas id="gauge2" width="90" height="90" style="transform: rotate(-90deg); width: 90px; height: 90px;"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                    <div style="font-size: 16px; font-weight: 700; color: #10b981;">97%</div>
                                </div>
                            </div>
                            <div style="margin-top: 6px; color: #374151; font-size: 12px; font-weight: 500;">Order Fulfillment</div>
                        </div>
                        
                        <!-- Buyer NPS -->
                        <div style="flex: 1; text-align: center; min-width: 0;">
                            <div class="gauge-container" style="position: relative; width: 90px; height: 90px; margin: 0 auto;">
                                <canvas id="gauge3" width="90" height="90" style="transform: rotate(-90deg); width: 90px; height: 90px;"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                    <div style="font-size: 16px; font-weight: 700; color: #f97316;">72</div>
                                </div>
                            </div>
                            <div style="margin-top: 6px; color: #374151; font-size: 12px; font-weight: 500;">Buyer NPS</div>
                        </div>
                        
                        <!-- System Uptime -->
                        <div style="flex: 1; text-align: center; min-width: 0;">
                            <div class="gauge-container" style="position: relative; width: 90px; height: 90px; margin: 0 auto;">
                                <canvas id="gauge4" width="90" height="90" style="transform: rotate(-90deg); width: 90px; height: 90px;"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                    <div style="font-size: 16px; font-weight: 700; color: #10b981;">99.9%</div>
                                </div>
                            </div>
                            <div style="margin-top: 6px; color: #374151; font-size: 12px; font-weight: 500;">System Uptime</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fifth Row: Top Sellers This Month -->
    <div class="tw-mt-6">
        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-overflow-hidden">
            <div class="tw-p-6">
                <!-- Header -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h2 class="tw-text-lg tw-font-bold tw-text-gray-900">Top Sellers This Month</h2>
                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                        View All Sellers
                        <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                    </a>
                </div>
                
                <!-- Table -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                        <thead style="background-color: #ffffff !important;">
                            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Rank</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Seller Name</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">State</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">GMV</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Orders</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Platform Fees</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Compliance</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 14px; font-weight: 600; color: #111827;">1</span>
                                        <i class="fas fa-trophy" style="color: #f59e0b; font-size: 16px;"></i>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">CloudNine Hemp Co.</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">CA</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$52,480.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">187</td>
                                <td style="padding: 16px 12px; text-align: right;">
                                    <a href="#" style="color: #a855f7; font-size: 14px; font-weight: 500; text-decoration: none;">$7,347.20</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="compliance-gauge" data-percentage="95" data-color="#10b981" style="width: 40px; height: 40px; position: relative;">
                                            <canvas width="40" height="40" style="transform: rotate(-90deg); width: 40px; height: 40px;"></canvas>
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 10px; font-weight: 600; color: #10b981;">95%</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="display: flex; gap: 2px;">
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 14px;"></i>
                                        </div>
                                        <span style="color: #374151; font-size: 14px; font-weight: 500;">4.9</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 2 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <span style="font-size: 14px; font-weight: 600; color: #111827;">2</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">VapeWorks Distribution</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">TX</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$48,920.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">156</td>
                                <td style="padding: 16px 12px; text-align: right;">
                                    <a href="#" style="color: #a855f7; font-size: 14px; font-weight: 500; text-decoration: none;">$6,848.80</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="compliance-gauge" data-percentage="90" data-color="#10b981" style="width: 40px; height: 40px; position: relative;">
                                            <canvas width="40" height="40" style="transform: rotate(-90deg); width: 40px; height: 40px;"></canvas>
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 10px; font-weight: 600; color: #10b981;">90%</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="display: flex; gap: 2px;">
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 14px;"></i>
                                        </div>
                                        <span style="color: #374151; font-size: 14px; font-weight: 500;">4.8</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 3 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <span style="font-size: 14px; font-weight: 600; color: #111827;">3</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">Premium Tobacco Supply</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">FL</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$41,350.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">98</td>
                                <td style="padding: 16px 12px; text-align: right;">
                                    <a href="#" style="color: #a855f7; font-size: 14px; font-weight: 500; text-decoration: none;">$5,362.50</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="compliance-gauge" data-percentage="94" data-color="#10b981" style="width: 40px; height: 40px; position: relative;">
                                            <canvas width="40" height="40" style="transform: rotate(-90deg); width: 40px; height: 40px;"></canvas>
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 10px; font-weight: 600; color: #10b981;">94%</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="display: flex; gap: 2px;">
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 14px;"></i>
                                        </div>
                                        <span style="color: #374151; font-size: 14px; font-weight: 500;">4.7</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 4 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <span style="font-size: 14px; font-weight: 600; color: #111827;">4</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">Green Leaf Distributors</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">CO</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$38,760.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">142</td>
                                <td style="padding: 16px 12px; text-align: right;">
                                    <a href="#" style="color: #a855f7; font-size: 14px; font-weight: 500; text-decoration: none;">$5,426.40</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="compliance-gauge" data-percentage="93" data-color="#10b981" style="width: 40px; height: 40px; position: relative;">
                                            <canvas width="40" height="40" style="transform: rotate(-90deg); width: 40px; height: 40px;"></canvas>
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 10px; font-weight: 600; color: #10b981;">93%</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="display: flex; gap: 2px;">
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 14px;"></i>
                                        </div>
                                        <span style="color: #374151; font-size: 14px; font-weight: 500;">4.9</span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 5 -->
                            <tr>
                                <td style="padding: 16px 12px;">
                                    <span style="font-size: 14px; font-weight: 600; color: #111827;">5</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">Curevana Wholesale</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">NY</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$35,280.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">124</td>
                                <td style="padding: 16px 12px; text-align: right;">
                                    <a href="#" style="color: #a855f7; font-size: 14px; font-weight: 500; text-decoration: none;">$4,939.20</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 8px;">
                                        <div class="compliance-gauge" data-percentage="80" data-color="#f97316" style="width: 40px; height: 40px; position: relative;">
                                            <canvas width="40" height="40" style="transform: rotate(-90deg); width: 40px; height: 40px;"></canvas>
                                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 10px; font-weight: 600; color: #f97316;">80%</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="display: flex; gap: 2px;">
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 14px;"></i>
                                            <i class="far fa-star" style="color: #d1d5db; font-size: 14px;"></i>
                                        </div>
                                        <span style="color: #374151; font-size: 14px; font-weight: 500;">4.2</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom tooltip styling for Revenue Breakdown chart */
    #revenueBreakdownChart + * {
        pointer-events: none;
    }
    
    /* Chart.js tooltip customization */
    .chartjs-tooltip {
        background-color: #4B5563 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    
    .chartjs-tooltip-title {
        color: #FFFFFF !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        margin-bottom: 6px !important;
    }
    
    .chartjs-tooltip-body {
        color: #FFFFFF !important;
        font-size: 13px !important;
    }
    
    .chartjs-tooltip-color {
        border-radius: 2px !important;
        width: 12px !important;
        height: 12px !important;
    }
</style>
@endsection

@section('javascript')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script type="text/javascript">
    // GMV & Platform Revenue Chart (Combined Bar and Line)
    const gmvCtx = document.getElementById('gmvRevenueChart').getContext('2d');
    const gmvRevenueChart = new Chart(gmvCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'GMV',
                    type: 'bar',
                    data: [280000, 290000, 300000, 310000, 320000, 330000, 340000, 350000, 360000, 350000, 5000, 3000],
                    backgroundColor: 'rgba(236, 171, 57, 0.8)',
                    borderColor: '#ecab39',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Platform Revenue',
                    type: 'line',
                    data: [25000, 26000, 28000, 30000, 32000, 34000, 36000, 38000, 40000, 45000, 500, 300],
                    backgroundColor: 'rgba(153, 102, 255, 0.1)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                if (context.datasetIndex === 0) {
                                    label += '$' + context.parsed.y.toLocaleString();
                                } else {
                                    label += '$' + context.parsed.y.toLocaleString();
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    max: 300000,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000) + 'k';
                        },
                        stepSize: 100000
                    },
                    title: {
                        display: false
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    max: 50000,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000) + 'k';
                        },
                        stepSize: 10000
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: false
                    }
                }
            }
        }
    });

    // Revenue Breakdown Donut Chart
    const revenueCtx = document.getElementById('revenueBreakdownChart').getContext('2d');
    const revenueBreakdownChart = new Chart(revenueCtx, {
        type: 'doughnut',
        data: {
            labels: ['Referral Fees', 'FBS Fees', 'Subscriptions', 'Advertising', 'Other'],
            datasets: [{
                data: [21733, 10625, 6761, 5795, 3381],
                backgroundColor: [
                    '#f97316',  // Orange - Referral Fees
                    '#3b82f6',  // Blue - FBS Fees
                    '#10b981',  // Green/Teal - Subscriptions
                    '#a855f7',  // Purple - Advertising
                    '#6b7280'   // Gray - Other
                ],
                borderColor: [
                    '#f97316',
                    '#3b82f6',
                    '#10b981',
                    '#a855f7',
                    '#6b7280'
                ],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: '#4B5563',
                    titleColor: '#FFFFFF',
                    bodyColor: '#FFFFFF',
                    borderColor: '#4B5563',
                    borderWidth: 0,
                    cornerRadius: 8,
                    padding: {
                        top: 10,
                        right: 12,
                        bottom: 10,
                        left: 12
                    },
                    titleFont: {
                        size: 13,
                        weight: '600',
                        family: 'Arial, sans-serif'
                    },
                    bodyFont: {
                        size: 13,
                        weight: '400',
                        family: 'Arial, sans-serif'
                    },
                    displayColors: true,
                    boxPadding: 6,
                    usePointStyle: false,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(0);
                            return '$' + value.toLocaleString() + ' (' + percentage + '%)';
                        },
                        labelColor: function(context) {
                            return {
                                borderColor: context.dataset.backgroundColor[context.dataIndex],
                                backgroundColor: context.dataset.backgroundColor[context.dataIndex],
                                borderWidth: 0,
                                borderRadius: 2,
                                width: 12,
                                height: 12
                            };
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'point'
            },
            onHover: function(event, activeElements) {
                // Adjust tooltip position after it's rendered to avoid center overlap
                if (activeElements.length > 0) {
                    setTimeout(function() {
                        const tooltip = document.querySelector('.chartjs-tooltip');
                        if (tooltip) {
                            const chart = revenueBreakdownChart;
                            const chartArea = chart.chartArea;
                            const centerX = chartArea.left + (chartArea.right - chartArea.left) / 2;
                            const centerY = chartArea.top + (chartArea.bottom - chartArea.top) / 2;
                            
                            const rect = chart.canvas.getBoundingClientRect();
                            const tooltipRect = tooltip.getBoundingClientRect();
                            const tooltipCenterX = tooltipRect.left + tooltipRect.width / 2 - rect.left;
                            const tooltipCenterY = tooltipRect.top + tooltipRect.height / 2 - rect.top;
                            
                            const distanceFromCenter = Math.sqrt(
                                Math.pow(tooltipCenterX - centerX, 2) + 
                                Math.pow(tooltipCenterY - centerY, 2)
                            );
                            
                            // If tooltip is too close to center, move it outward
                            if (distanceFromCenter < 80) {
                                const angle = Math.atan2(tooltipCenterY - centerY, tooltipCenterX - centerX);
                                const offsetDistance = 100;
                                const newX = centerX + Math.cos(angle) * offsetDistance;
                                const newY = centerY + Math.sin(angle) * offsetDistance;
                                
                                tooltip.style.left = (rect.left + newX - tooltipRect.width / 2) + 'px';
                                tooltip.style.top = (rect.top + newY - tooltipRect.height / 2) + 'px';
                            }
                        }
                    }, 10);
                }
            }
        }
    });

    // Toggle buttons functionality
    document.getElementById('gmv-monthly-btn').addEventListener('click', function() {
        this.classList.add('tw-bg-white', 'tw-text-gray-700', 'tw-shadow-sm');
        this.classList.remove('tw-text-gray-600');
        document.getElementById('gmv-weekly-btn').classList.remove('tw-bg-white', 'tw-text-gray-700', 'tw-shadow-sm');
        document.getElementById('gmv-weekly-btn').classList.add('tw-text-gray-600');
    });

    document.getElementById('gmv-weekly-btn').addEventListener('click', function() {
        this.classList.add('tw-bg-white', 'tw-text-gray-700', 'tw-shadow-sm');
        this.classList.remove('tw-text-gray-600');
        document.getElementById('gmv-monthly-btn').classList.remove('tw-bg-white', 'tw-text-gray-700', 'tw-shadow-sm');
        document.getElementById('gmv-monthly-btn').classList.add('tw-text-gray-600');
    });

    // Tab functionality for Platform Activity
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Update active tab button
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.style.color = '#6b7280';
                btn.style.borderBottom = '2px solid transparent';
            });
            this.style.color = '#111827';
            this.style.borderBottom = '2px solid #111827';
            
            // Show/hide tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            document.querySelector(`.tab-content[data-content="${tabName}"]`).style.display = 'block';
        });
    });

    // Draw circular progress gauges
    function drawGauge(canvasId, percentage, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 35;
        const lineWidth = 8;
        
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw background circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = lineWidth;
        ctx.stroke();
        
        // Draw progress arc
        const startAngle = -Math.PI / 2;
        const endAngle = startAngle + (2 * Math.PI * percentage / 100);
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.strokeStyle = color;
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();
    }

    // Initialize gauges
    setTimeout(function() {
        drawGauge('gauge1', 94, '#10b981');
        drawGauge('gauge2', 97, '#10b981');
        drawGauge('gauge3', 72, '#f97316'); // Buyer NPS is out of 100, so 72%
        drawGauge('gauge4', 99.9, '#10b981');
        
        // Draw compliance gauges in the table
        document.querySelectorAll('.compliance-gauge').forEach(function(gauge) {
            const percentage = parseInt(gauge.getAttribute('data-percentage'));
            const color = gauge.getAttribute('data-color');
            const canvas = gauge.querySelector('canvas');
            if (canvas) {
                drawComplianceGauge(canvas, percentage, color);
            }
        });
    }, 100);
    
    // Function to draw compliance gauge (smaller version for table)
    function drawComplianceGauge(canvas, percentage, color) {
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 15;
        const lineWidth = 4;
        
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw background circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = lineWidth;
        ctx.stroke();
        
        // Draw progress arc
        const startAngle = -Math.PI / 2;
        const endAngle = startAngle + (2 * Math.PI * percentage / 100);
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.strokeStyle = color;
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();
    }
</script>

<style>
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>
@endsection
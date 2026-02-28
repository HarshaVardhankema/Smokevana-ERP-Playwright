@extends('layouts.app')
@section('title', 'Seller Applications')

@section('css')
    <style>
        .app-filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            transition: all 0.2s ease;
        }

        .app-filter-tab.active {
            background: #f97316;
            color: white;
            border-color: #f97316;
        }

        .app-filter-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px !important;
        }
    </style>
@endsection

@section('content')
    <div
        style="padding: 24px; background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', system-ui, -apple-system, sans-serif;">

        <!-- Section 1: Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                    <h1 style="font-size: 26px; font-weight: 800; color: #1e293b; margin: 0;">Seller Applications</h1>
                    <span
                        style="background: #fef3c7; color: #d97706; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-circle" style="font-size: 8px;"></i> 12 Pending
                    </span>
                </div>
                <!-- Breadcrumb -->
                <div
                    style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #94a3b8; font-weight: 600;">
                    <span>Admin</span>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <a href="{{ route('new-screens.sellers') }}" style="color: #94a3b8; text-decoration: none;">Sellers</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span style="color: #475569;">Pending Applications</span>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <!-- Search Bar -->
                <div style="position: relative;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" placeholder="Search sellers, buyers, orders, produc..."
                        style="padding: 10px 16px 10px 42px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; width: 280px; background: white; color: #64748b;">
                </div>
                <!-- Status indicator -->
                <div
                    style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; color: #475569; font-weight: 600; white-space: nowrap;">
                    <span
                        style="width: 8px; height: 8px; background: #16a34a; border-radius: 50%; display: inline-block;"></span>
                    All Systems Operational
                </div>
                <!-- Notification Bell -->
                <div
                    style="position: relative; cursor: pointer; background: white; padding: 10px 12px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <i class="far fa-bell" style="font-size: 18px; color: #475569;"></i>
                    <span
                        style="position: absolute; top: 8px; right: 8px; background: #ef4444; color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 9px; display: flex; align-items: center; justify-content: center; font-weight: 800;">3</span>
                </div>
                <!-- Avatar -->
                <div
                    style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 14px; cursor: pointer;">
                    SA
                </div>
            </div>
        </div>

        <!-- Section 1: Stat Cards -->
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 32px;">
            <!-- Pending Review -->
            <div
                style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div
                    style="background: #fffbeb; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #f59e0b;">
                    <i class="fas fa-clock" style="font-size: 18px;"></i>
                </div>
                <div style="font-size: 36px; font-weight: 800; color: #f59e0b; line-height: 1;">12</div>
                <div
                    style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 8px;">
                    Pending Review</div>
            </div>

            <!-- In Review -->
            <div
                style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div
                    style="background: #eff6ff; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #3b82f6;">
                    <i class="fas fa-eye" style="font-size: 18px;"></i>
                </div>
                <div style="font-size: 36px; font-weight: 800; color: #3b82f6; line-height: 1;">8</div>
                <div
                    style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 8px;">
                    In Review</div>
            </div>

            <!-- Awaiting Info -->
            <div
                style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div
                    style="background: #fff7ed; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #f97316;">
                    <i class="fas fa-file-alt" style="font-size: 18px;"></i>
                </div>
                <div style="font-size: 36px; font-weight: 800; color: #f97316; line-height: 1;">5</div>
                <div
                    style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 8px;">
                    Awaiting Info</div>
            </div>

            <!-- Approved (30D) -->
            <div
                style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div
                    style="background: #f0fdf4; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #16a34a;">
                    <i class="fas fa-check-circle" style="font-size: 18px;"></i>
                </div>
                <div style="font-size: 36px; font-weight: 800; color: #16a34a; line-height: 1;">143</div>
                <div
                    style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 8px;">
                    Approved (30D)</div>
            </div>

            <!-- Rejected (30D) -->
            <div
                style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div
                    style="background: #fef2f2; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #ef4444;">
                    <i class="fas fa-times-circle" style="font-size: 18px;"></i>
                </div>
                <div style="font-size: 36px; font-weight: 800; color: #ef4444; line-height: 1;">18</div>
                <div
                    style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 8px;">
                    Rejected (30D)</div>
            </div>
        </div>

        <!-- Section 1: Filters & Search -->
        <div
            style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Filters & Search</h3>
                <a href="#" style="font-size: 14px; font-weight: 700; color: #3b82f6; text-decoration: none;">Clear All</a>
            </div>

            <!-- Filter Tabs -->
            <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="app-filter-tab active">All 25</button>
                <button class="app-filter-tab">New 12</button>
                <button class="app-filter-tab">In Review 8</button>
                <button class="app-filter-tab">Awaiting Info 5</button>
                <button class="app-filter-tab">Approved 0</button>
                <button class="app-filter-tab">Rejected 0</button>
            </div>

            <!-- Search and Dropdowns -->
            <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr; gap: 16px; align-items: center;">
                <!-- Search Input -->
                <div style="position: relative;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" placeholder="Application # / Business"
                        style="width: 100%; padding: 11px 16px 11px 42px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f8fafc; color: #475569;">
                </div>
                <!-- Business Type -->
                <select class="app-filter-select"
                    style="width: 100%; padding: 11px 40px 11px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f8fafc; color: #475569; font-weight: 600;">
                    <option>Business Type: All</option>
                    <option>Distributor</option>
                    <option>Brand</option>
                    <option>Manufacturer</option>
                    <option>Wholesaler</option>
                </select>
                <!-- State -->
                <select class="app-filter-select"
                    style="width: 100%; padding: 11px 40px 11px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f8fafc; color: #475569; font-weight: 600;">
                    <option>State: All</option>
                    <option>California</option>
                    <option>New York</option>
                    <option>Colorado</option>
                    <option>Florida</option>
                </select>
                <!-- Risk Score -->
                <select class="app-filter-select"
                    style="width: 100%; padding: 11px 40px 11px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f8fafc; color: #475569; font-weight: 600;">
                    <option>Risk Score: All</option>
                    <option>Low Risk</option>
                    <option>Medium Risk</option>
                    <option>High Risk</option>
                </select>
                <!-- Sort -->
                <select class="app-filter-select"
                    style="width: 100%; padding: 11px 40px 11px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f8fafc; color: #475569; font-weight: 600;">
                    <option>Sort: Oldest First</option>
                    <option>Sort: Newest First</option>
                    <option>Sort: Risk Score</option>
                    <option>Sort: Business Name</option>
                </select>
            </div>
        </div>

        @php
            $applications = [
                ['id' => '#APP-0234', 'name' => 'Pacific Smoke Distributors LLC', 'ein' => '94-2847562', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'CA, NV, OR', 'date' => 'Oct 18, 2024', 'age' => '11 days', 'age_color' => '#ef4444', 'assignee' => 'Unassigned', 'status' => 'New', 'status_color' => '#94a3b8', 'status_bg' => '#f1f5f9', 'risk' => 'High', 'risk_color' => '#ef4444', 'bg' => '#fff1f2'],
                ['id' => '#APP-0231', 'name' => 'GreenLeaf Wholesale Inc', 'ein' => '87-3456789', 'type' => 'Wholesaler', 'type_color' => '#8b5cf6', 'type_bg' => '#f5f3ff', 'states' => 'CO, NV', 'date' => 'Oct 15, 2024', 'age' => '14 days', 'age_color' => '#ef4444', 'assignee' => 'Sarah Johnson', 'status' => 'In Review', 'status_color' => '#3b82f6', 'status_bg' => '#eff6ff', 'risk' => 'High', 'risk_color' => '#ef4444', 'bg' => '#fff1f2'],
                ['id' => '#APP-0228', 'name' => 'HighTide Supply Co', 'ein' => '45-7890123', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'CA', 'date' => 'Oct 22, 2024', 'age' => '7 days', 'age_color' => '#f59e0b', 'assignee' => 'Michael Chen', 'status' => 'Awaiting Info', 'status_color' => '#d97706', 'status_bg' => '#fef3c7', 'risk' => 'Medium', 'risk_color' => '#f59e0b', 'bg' => '#fffbeb'],
                ['id' => '#APP-0225', 'name' => 'VaporWave Distribution', 'ein' => '23-5678901', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'NV, OR, WA', 'date' => 'Oct 20, 2024', 'age' => '9 days', 'age_color' => '#f59e0b', 'assignee' => 'Emily Rodriguez', 'status' => 'In Review', 'status_color' => '#3b82f6', 'status_bg' => '#eff6ff', 'risk' => 'Medium', 'risk_color' => '#f59e0b', 'bg' => '#fffbeb'],
                ['id' => '#APP-0222', 'name' => 'SmokeCloud Enterprises', 'ein' => '67-8901234', 'type' => 'Wholesaler', 'type_color' => '#8b5cf6', 'type_bg' => '#f5f3ff', 'states' => 'CA, CO', 'date' => 'Oct 24, 2024', 'age' => '5 days', 'age_color' => '#1e293b', 'assignee' => 'Unassigned', 'status' => 'New', 'status_color' => '#94a3b8', 'status_bg' => '#f1f5f9', 'risk' => 'Low', 'risk_color' => '#10b981', 'bg' => 'white'],
                ['id' => '#APP-0220', 'name' => 'Premier Tobacco Solutions', 'ein' => '12-3456789', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'CA', 'date' => 'Oct 25, 2024', 'age' => '4 days', 'age_color' => '#1e293b', 'assignee' => 'Sarah Johnson', 'status' => 'In Review', 'status_color' => '#3b82f6', 'status_bg' => '#eff6ff', 'risk' => 'Low', 'risk_color' => '#10b981', 'bg' => 'white'],
                ['id' => '#APP-0218', 'name' => 'Rocky Mountain Hemp Co', 'ein' => '89-0123456', 'type' => 'Wholesaler', 'type_color' => '#8b5cf6', 'type_bg' => '#f5f3ff', 'states' => 'CO', 'date' => 'Oct 26, 2024', 'age' => '3 days', 'age_color' => '#1e293b', 'assignee' => 'Michael Chen', 'status' => 'Awaiting Info', 'status_color' => '#d97706', 'status_bg' => '#fef3c7', 'risk' => 'Low', 'risk_color' => '#10b981', 'bg' => 'white'],
                ['id' => '#APP-0215', 'name' => 'WestCoast Vape Supply', 'ein' => '34-5678902', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'CA, OR', 'date' => 'Oct 27, 2024', 'age' => '2 days', 'age_color' => '#1e293b', 'assignee' => 'Unassigned', 'status' => 'New', 'status_color' => '#94a3b8', 'status_bg' => '#f1f5f9', 'risk' => 'Medium', 'risk_color' => '#f59e0b', 'bg' => 'white'],
                ['id' => '#APP-0213', 'name' => 'Golden State Tobacco LLC', 'ein' => '56-7890123', 'type' => 'Wholesaler', 'type_color' => '#8b5cf6', 'type_bg' => '#f5f3ff', 'states' => 'CA', 'date' => 'Oct 28, 2024', 'age' => '1 day', 'age_color' => '#1e293b', 'assignee' => 'Emily Rodriguez', 'status' => 'In Review', 'status_color' => '#3b82f6', 'status_bg' => '#eff6ff', 'risk' => 'Low', 'risk_color' => '#10b981', 'bg' => 'white'],
                ['id' => '#APP-0211', 'name' => 'Northwest Hemp Distributors', 'ein' => '78-9012345', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'OR, WA', 'date' => 'Oct 28, 2024', 'age' => '1 day', 'age_color' => '#1e293b', 'assignee' => 'Unassigned', 'status' => 'New', 'status_color' => '#94a3b8', 'status_bg' => '#f1f5f9', 'risk' => 'Low', 'risk_color' => '#10b981', 'bg' => 'white'],
                ['id' => '#APP-0209', 'name' => 'Desert Sky Wholesale', 'ein' => '90-1234567', 'type' => 'Wholesaler', 'type_color' => '#8b5cf6', 'type_bg' => '#f5f3ff', 'states' => 'NV', 'date' => 'Oct 29, 2024', 'age' => '< 1 day', 'age_color' => '#1e293b', 'assignee' => 'Unassigned', 'status' => 'New', 'status_color' => '#94a3b8', 'status_bg' => '#f1f5f9', 'risk' => 'Medium', 'risk_color' => '#f59e0b', 'bg' => 'white'],
                ['id' => '#APP-0207', 'name' => 'Mile High Vapor Supply', 'ein' => '01-2345678', 'type' => 'Distributor', 'type_color' => '#3b82f6', 'type_bg' => '#eff6ff', 'states' => 'CO', 'date' => 'Oct 29, 2024', 'age' => '< 1 day', 'age_color' => '#1e293b', 'assignee' => 'Unassigned', 'status' => 'New', 'status_color' => '#94a3b8', 'status_bg' => '#f1f5f9', 'risk' => 'Low', 'risk_color' => '#10b981', 'bg' => 'white'],
            ];
        @endphp

        <!-- Priority Alerts Section -->
        

        <!-- Section 3: Applications Table -->
        <div
            style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 28px;">

            <!-- Table Header -->
            <div
                style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div
                    style="font-size: 16px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 12px;">
                    All Applications
                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">Showing <strong
                            style="color: #1e293b;">1-12</strong> of <strong style="color: #1e293b;">25</strong></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 700; color: #475569; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #f59e0b; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-filter"></i> Advanced Filters
                    </button>
                </div>
            </div>

            <!-- Table Content -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr
                            style="background: #1e293b; border-bottom: 1px solid #e2e8f0; font-size: 12px; font-weight: 800; color: #ffffff; text-transform: uppercase;">
                            <th style="padding: 16px 24px; white-space: nowrap;">APPLICATION # <i class="fas fa-sort"
                                    style="margin-left: 4px; color: #cbd5e1;"></i></th>
                            <th style="padding: 16px 24px;">BUSINESS NAME</th>
                            <th style="padding: 16px 24px;">TYPE</th>
                            <th style="padding: 16px 24px; text-align: center;">STATES</th>
                            <th style="padding: 16px 24px; white-space: nowrap;">SUBMITTED <i class="fas fa-sort"
                                    style="margin-left: 4px; color: #cbd5e1;"></i></th>
                            <th style="padding: 16px 24px; white-space: nowrap;">AGE (DAYS) <i class="fas fa-sort"
                                    style="margin-left: 4px; color: #cbd5e1;"></i></th>
                            <th style="padding: 16px 24px;">ASSIGNED TO</th>
                            <th style="padding: 16px 24px; text-align: center;">STATUS</th>
                            <th style="padding: 16px 24px; white-space: nowrap;">RISK SCORE <i class="fas fa-sort"
                                    style="margin-left: 4px; color: #cbd5e1;"></i></th>
                            <th style="padding: 16px 24px;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px; color: #475569;">
                        @foreach($applications as $app)
                            <tr
                                style="border-bottom: 1px solid #e2e8f0; background: {{ $loop->iteration % 2 == 0 ? '#f8fafc' : 'white' }}; transition: background 0.2s ease;">
                                <td style="padding: 16px 24px; color: #0284c7; font-weight: 700; white-space: nowrap;">
                                    {{ $app['id'] }}
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="font-weight: 800; color: #1e293b; margin-bottom: 4px;">{{ $app['name'] }}</div>
                                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">EIN: {{ $app['ein'] }}</div>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <span
                                        style="background: {{ $app['type_bg'] }}; color: {{ $app['type_color'] }}; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">{{ $app['type'] }}</span>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <div style="width: 80px; font-weight: 600; text-align: center; color: #475569;">
                                        {{ $app['states'] }}
                                    </div>
                                </td>
                                <td style="padding: 16px 24px; color: #475569; font-weight: 600;">{{ $app['date'] }}</td>
                                <td style="padding: 16px 24px; color: {{ $app['age_color'] }}; font-weight: 800;">
                                    {{ $app['age'] }}</td>
                                <td style="padding: 16px 24px;">
                                    <select
                                        style="padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; font-weight: 600; color: #475569; outline: none; background: white; white-space: nowrap;">
                                        <option {{ $app['assignee'] == 'Unassigned' ? 'selected' : '' }}>Unassigned</option>
                                        <option {{ $app['assignee'] == 'Sarah Johnson' ? 'selected' : '' }}>Sarah Johnson</option>
                                        <option {{ $app['assignee'] == 'Michael Chen' ? 'selected' : '' }}>Michael Chen</option>
                                        <option {{ $app['assignee'] == 'Emily Rodriguez' ? 'selected' : '' }}>Emily Rodriguez
                                        </option>
                                    </select>
                                </td>
                                <td style="padding: 16px 24px; text-align: center;">
                                    <span
                                        style="background: {{ $app['status_bg'] }}; color: {{ $app['status_color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; white-space: nowrap; max-width: 90px; text-align: center; line-height: 1.2;">
                                        @if(strpos($app['status'], ' ') !== false)
                                            {!! str_replace(' ', '<br>', $app['status']) !!}
                                        @else
                                            {{ $app['status'] }}
                                        @endif
                                    </span>
                                </td>
                                <td style="padding: 16px 24px; white-space: nowrap;">
                                    <div
                                        style="display: flex; align-items: center; gap: 6px; color: {{ $app['risk_color'] }}; font-weight: 800;">
                                        <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $app['risk'] }}
                                    </div>
                                </td>
                                <td style="padding: 16px 24px;">
                                    <button
                                        style="background: #f59e0b; color: white; border: none; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.2s;">Review</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Pagination -->
            <div
                style="padding: 20px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 14px; color: #64748b; font-weight: 600;">
                    Showing <strong style="color: #1e293b;">1-12</strong> of <strong style="color: #1e293b;">25</strong>
                    applications
                </div>
                <div style="display: flex; gap: 8px;">
                    <button
                        style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; color: #cbd5e1; cursor: not-allowed;"><i
                            class="fas fa-chevron-left"></i></button>
                    <button
                        style="padding: 6px 14px; border: none; border-radius: 6px; background: #f59e0b; color: white; font-weight: 700;">1</button>
                    <button
                        style="padding: 6px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #475569; font-weight: 700; cursor: pointer;">2</button>
                    <button
                        style="padding: 6px 14px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #475569; font-weight: 700; cursor: pointer;">3</button>
                    <button
                        style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; color: #475569; cursor: pointer;"><i
                            class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 28px;">
            <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 800; color: #1e293b;">
                    <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i> Priority Alerts & Aging Applications
                </div>
                <div style="background: #fef2f2; color: #dc2626; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800;">
                    4 Critical
                </div>
            </div>

            <div style="display: flex; flex-direction: column;">
                <!-- Row 1 -->
                <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: relative;">
                    <div style="position: absolute; left: 0; top: 20px; bottom: 20px; width: 4px; background-color: #dc2626; border-top-right-radius: 4px; border-bottom-right-radius: 4px;"></div>
                    <div style="padding-left: 12px;">
                        <div style="color: #0284c7; font-weight: 700; font-size: 15px; margin-bottom: 8px;">
                            #APP-0231 <span style="color: #64748b;">-</span> <span style="color: #0284c7;">GreenLeaf Wholesale Inc</span>
                        </div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <span style="background: #fef2f2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">14 DAYS OLD</span>
                            <span style="background: #fef2f2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">HIGH RISK</span>
                        </div>
                        <div style="font-size: 14px; color: #475569; margin-bottom: 6px;">
                            <strong style="color: #1e293b;">Alert:</strong> Application exceeds 10-day SLA. License verification failed for CO state.
                        </div>
                        <div style="font-size: 14px; color: #475569;">
                            <strong style="color: #1e293b;">Assigned to:</strong> Sarah Johnson
                        </div>
                    </div>
                    <div>
                        <button style="background: #dc2626; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">Escalate</button>
                    </div>
                </div>

                <!-- Row 2 -->
                <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <div style="padding-left: 12px;">
                        <div style="color: #0284c7; font-weight: 700; font-size: 15px; margin-bottom: 8px;">
                            #APP-0234 <span style="color: #64748b;">-</span> <span style="color: #0284c7;">Pacific Smoke Distributors LLC</span>
                        </div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <span style="background: #fef2f2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">11 DAYS OLD</span>
                            <span style="background: #fef2f2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">HIGH RISK</span>
                        </div>
                        <div style="font-size: 14px; color: #475569; margin-bottom: 6px;">
                            <strong style="color: #1e293b;">Alert:</strong> Application exceeds 10-day SLA. EIN verification pending. Unassigned.
                        </div>
                        <div style="font-size: 14px; color: #475569;">
                            <strong style="color: #1e293b;">Assigned to:</strong> Unassigned
                        </div>
                    </div>
                    <div>
                        <button style="background: #dc2626; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">Escalate</button>
                    </div>
                </div>

                <!-- Row 3 -->
                <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <div style="padding-left: 12px;">
                        <div style="color: #0284c7; font-weight: 700; font-size: 15px; margin-bottom: 8px;">
                            #APP-0225 <span style="color: #64748b;">-</span> <span style="color: #0284c7;">VaporWave Distribution</span>
                        </div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">9 DAYS OLD</span>
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">MEDIUM RISK</span>
                        </div>
                        <div style="font-size: 14px; color: #475569; margin-bottom: 6px;">
                            <strong style="color: #1e293b;">Alert:</strong> Approaching 10-day SLA. License documents pending verification for WA state.
                        </div>
                        <div style="font-size: 14px; color: #475569;">
                            <strong style="color: #1e293b;">Assigned to:</strong> Emily Rodriguez
                        </div>
                    </div>
                    <div>
                        <button style="background: #d97706; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">Review</button>
                    </div>
                </div>

                <!-- Row 4 -->
                <div style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="padding-left: 12px;">
                        <div style="color: #0284c7; font-weight: 700; font-size: 15px; margin-bottom: 8px;">
                            #APP-0228 <span style="color: #64748b;">-</span> <span style="color: #0284c7;">HighTide Supply Co</span>
                        </div>
                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">AWAITING INFO</span>
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">7 DAYS OLD</span>
                        </div>
                        <div style="font-size: 14px; color: #475569; margin-bottom: 6px;">
                            <strong style="color: #1e293b;">Alert:</strong> Waiting on applicant to resubmit COA documents. Last contact: 3 days ago.
                        </div>
                        <div style="font-size: 14px; color: #475569;">
                            <strong style="color: #1e293b;">Assigned to:</strong> Michael Chen
                        </div>
                    </div>
                    <div>
                        <button style="background: #d97706; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer;">Follow Up</button>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- Section 4: Dashboard Cards -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
            <!-- Reviewer Workload -->
            <div style="background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Reviewer Workload</h3>
                    <i class="fas fa-users" style="color: #f59e0b;"></i>
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="https://i.pravatar.cc/150?u=sarah" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px;">Sarah Johnson</div>
                                <div style="font-size: 12px; color: #64748b;">Senior Reviewer</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; font-size: 16px; color: #1e293b;">8</div>
                            <div style="font-size: 12px; color: #059669;">Active</div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="https://i.pravatar.cc/150?u=michael" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px;">Michael Chen</div>
                                <div style="font-size: 12px; color: #64748b;">Reviewer</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; font-size: 16px; color: #1e293b;">6</div>
                            <div style="font-size: 12px; color: #64748b;">Active</div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="https://i.pravatar.cc/150?u=emily" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px;">Emily Rodriguez</div>
                                <div style="font-size: 12px; color: #64748b;">Reviewer</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; font-size: 16px; color: #1e293b;">5</div>
                            <div style="font-size: 12px; color: #64748b;">Active</div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px;">Unassigned</div>
                                <div style="font-size: 12px; color: #64748b;">No Reviewer</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 800; font-size: 16px; color: #dc2626;">6</div>
                            <div style="font-size: 12px; color: #64748b;">Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Rate (30d) -->
            <div style="background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Approval Rate (30d)</h3>
                    <i class="fas fa-chart-pie" style="color: #f59e0b;"></i>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <div style="position: relative; width: 140px; height: 140px; margin-bottom: 24px; display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e2e8f0" stroke-width="4"></path>
                            <!-- Approved slice -->
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#059669" stroke-width="4" stroke-dasharray="88.8, 100" style="transition: stroke-dasharray 1s ease 0s;"></path>
                        </svg>
                        <div style="position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <span style="font-size: 24px; font-weight: 800; color: #059669; line-height: 1;">88.8%</span>
                            <span style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.05em;">APPROVED</span>
                        </div>
                    </div>
                    
                    <div style="width: 100%; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #475569; font-weight: 600;">
                                <div style="width: 10px; height: 10px; border-radius: 50%; background: #059669;"></div> Approved
                            </div>
                            <strong style="color: #1e293b; font-size: 14px;">143</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #475569; font-weight: 600;">
                                <div style="width: 10px; height: 10px; border-radius: 50%; background: #dc2626;"></div> Rejected
                            </div>
                            <strong style="color: #1e293b; font-size: 14px;">18</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avg Review Time -->
            <div style="background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Avg Review Time</h3>
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                </div>
                <div style="text-align: center; margin-bottom: 32px;">
                    <div style="font-size: 42px; font-weight: 800; color: #f59e0b; line-height: 1; margin-bottom: 4px;">3.2</div>
                    <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">DAYS</div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                        <span style="color: #64748b; font-weight: 600;">New &rarr; In Review</span>
                        <strong style="color: #1e293b;">1.2 days</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                        <span style="color: #64748b; font-weight: 600;">In Review &rarr; Decision</span>
                        <strong style="color: #1e293b;">2.0 days</strong>
                    </div>
                    <div style="height: 1px; background: #e2e8f0; margin: 4px 0;"></div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                        <span style="color: #64748b; font-weight: 600;">SLA Target</span>
                        <strong style="color: #059669;">&lt; 10 days</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Rejection Reasons -->
        <div style="background: white; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Top Rejection Reasons (Last 30 Days)</h3>
                <a href="#" style="font-size: 13px; font-weight: 700; color: #0284c7; text-decoration: none;">View Full Report</a>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <!-- Invalid License -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="font-weight: 700; color: #475569;">Invalid License</span>
                        <strong style="color: #1e293b;">8 (44.4%)</strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 44.4%; height: 100%; background: #dc2626; border-radius: 4px;"></div>
                    </div>
                </div>
                
                <!-- Incomplete Documentation -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="font-weight: 700; color: #475569;">Incomplete Documentation</span>
                        <strong style="color: #1e293b;">5 (27.8%)</strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 27.8%; height: 100%; background: #dc2626; border-radius: 4px;"></div>
                    </div>
                </div>

                <!-- Failed Verification -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="font-weight: 700; color: #475569;">Failed Verification</span>
                        <strong style="color: #1e293b;">3 (16.7%)</strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 16.7%; height: 100%; background: #dc2626; border-radius: 4px;"></div>
                    </div>
                </div>

                <!-- Other -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="font-weight: 700; color: #475569;">Other</span>
                        <strong style="color: #1e293b;">2 (11.1%)</strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 11.1%; height: 100%; background: #dc2626; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 800; color: #1e293b;">
                    <i class="fas fa-history" style="color: #f59e0b;"></i> Recent Activity Feed
                </div>
                <a href="#" style="font-size: 13px; font-weight: 700; color: #0284c7; text-decoration: none;">View All</a>
            </div>

            <div style="padding: 24px;">
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <!-- Activity 1 -->
                    <div style="display: flex; gap: 16px;">
                        <div style="margin-top: 6px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 14px; color: #475569;">
                            <div><strong style="color: #1e293b;">Sarah Johnson</strong> approved application</div>
                            <div style="color: #0284c7; font-weight: 700;">#APP-0213</div>
                            <div>- Golden State Tobacco LLC</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">2 minutes ago</div>
                        </div>
                    </div>

                    <!-- Activity 2 -->
                    <div style="display: flex; gap: 16px;">
                        <div style="margin-top: 6px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 14px; color: #475569;">
                            <div><strong style="color: #1e293b;">Michael Chen</strong> assigned</div>
                            <div style="color: #0284c7; font-weight: 700;">#APP-0228</div>
                            <div>to himself</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">15 minutes ago</div>
                        </div>
                    </div>

                    <!-- Activity 3 -->
                    <div style="display: flex; gap: 16px;">
                        <div style="margin-top: 6px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 14px; color: #475569;">
                            <div><strong style="color: #1e293b;">Emily Rodriguez</strong> requested additional info for</div>
                            <div style="color: #0284c7; font-weight: 700;">#APP-0218</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">42 minutes ago</div>
                        </div>
                    </div>

                    <!-- Activity 4 -->
                    <div style="display: flex; gap: 16px;">
                        <div style="margin-top: 6px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #dc2626;"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 14px; color: #475569;">
                            <div><strong style="color: #1e293b;">Sarah Johnson</strong> rejected application</div>
                            <div style="color: #0284c7; font-weight: 700;">#APP-0196</div>
                            <div>- Reason: Invalid License</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">1 hour ago</div>
                        </div>
                    </div>

                    <!-- Activity 5 -->
                    <div style="display: flex; gap: 16px;">
                        <div style="margin-top: 6px;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #8b5cf6;"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; font-size: 14px; color: #475569;">
                            <div><strong style="color: #1e293b;">New application received:</strong></div>
                            <div style="color: #0284c7; font-weight: 700;">#APP-0207</div>
                            <div>- Mile High Vapor Supply</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">2 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
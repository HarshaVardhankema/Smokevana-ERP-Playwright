@extends('layouts.app')
@section('title', 'Sellers')

@section('css')
    <style>
        .seller-row:hover {
            background-color: #ffffff !important;
        }

        .filter-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px !important;
        }

        .compliance-circle {
            position: relative;
            width: 32px;
            height: 32px;
        }

        .compliance-circle svg {
            transform: rotate(-90deg);
        }

        .sellers-table thead th {
            background-color: #f5f5f5 !important;
            color: #1f2937 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 12px 16px !important;
        }
    </style>
@endsection

@section('content')
    <div
        style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0;">Sellers</h1>
            <div style="display: flex; gap: 12px;">
                <button
                    style="padding: 10px 20px; background-color: #f3a847; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-plus"></i> Add New Seller
                </button>
            </div>
        </div>

        <!-- Section 1: Top Metrics Cards -->
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 24px;">
            <!-- Verified & Active -->
            <div
                style="background-color: #ffffff; border-radius: 8px; border-left: 4px solid #10b981; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 800; color: #1a202c;">1,089</div>
                <div
                    style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">
                    Verified & Active</div>
            </div>

            <!-- Pending Review -->
            <a href="{{ route('new-screens.pending-applications') }}" style="text-decoration: none;">
                <div
                    style="background-color: #ffffff; border-radius: 8px; border-left: 4px solid #f59e0b; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); cursor: pointer;">
                    <div style="font-size: 28px; font-weight: 800; color: #1a202c;">12</div>
                    <div
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">
                        Pending Review</div>
                </div>
            </a>

            <!-- Probationary -->
            <div
                style="background-color: #ffffff; border-radius: 8px; border-left: 4px solid #f97316; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 800; color: #1a202c;">34</div>
                <div
                    style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">
                    Probationary</div>
            </div>

            <!-- Suspended -->
            <div
                style="background-color: #ffffff; border-radius: 8px; border-left: 4px solid #ef4444; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 800; color: #1a202c;">8</div>
                <div
                    style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">
                    Suspended</div>
            </div>

            <!-- New This Month -->
            <div
                style="background-color: #ffffff; border-radius: 8px; border-left: 4px solid #3b82f6; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: 800; color: #1a202c;">34</div>
                <div
                    style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px;">
                    New This Month</div>
            </div>
        </div>

        <!-- Section 2: Filters Bar -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px 8px 0 0; padding: 20px; border-bottom: none;">
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                <!-- Search -->
                <div style="position: relative; flex-grow: 1; min-width: 240px;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" placeholder="Search seller name, EIN, email..."
                        style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937; outline: none;">
                </div>

                <!-- Status Select -->
                <select class="filter-select"
                    style="padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #4b5563; min-width: 120px; outline: none; background-color: white;">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Pending</option>
                    <option>Probationary</option>
                    <option>Suspended</option>
                </select>

                <!-- States Select -->
                <select class="filter-select"
                    style="padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #4b5563; min-width: 110px; outline: none; background-color: white;">
                    <option>All States</option>
                </select>

                <!-- Categories Select -->
                <select class="filter-select"
                    style="padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #4b5563; min-width: 140px; outline: none; background-color: white;">
                    <option>All Categories</option>
                </select>

                <!-- Tiers Select -->
                <select class="filter-select"
                    style="padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #4b5563; min-width: 110px; outline: none; background-color: white;">
                    <option>All Tiers</option>
                </select>

                <!-- Date Joined Button -->
                <button
                    style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #4b5563; background-color: white; cursor: pointer;">
                    <i class="far fa-calendar"></i>
                    Date Joined
                </button>

                <!-- Export Button -->
                <button
                    style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #4b5563; background-color: white; cursor: pointer;">
                    <i class="fas fa-download"></i>
                    Export
                    <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
                </button>
            </div>
        </div>

        <!-- Section 3: Sellers Table -->
        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px; overflow-x: auto;">
            <table class="sellers-table" style="width: 100%; border-collapse: collapse; min-width: 1000px;">
                <thead>
                    <tr style="text-align: left;">
                        <th style="background-color: #f5f5f5 !important; padding: 12px 20px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; width: 40px;">
                            <input type="checkbox"
                                style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #cbd5e1;">
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                            Seller Name <i class="fas fa-sort" style="margin-left: 4px; font-size: 10px; color: #64748b;"></i>
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                            Business Type
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                            State(s)
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                            Joined Date <i class="fas fa-sort" style="margin-left: 4px; font-size: 10px; color: #64748b;"></i>
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                            Status
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                            Seller Plan
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            Products <i class="fas fa-sort" style="margin-left: 4px; font-size: 10px; color: #64748b;"></i>
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; text-align: right;">
                            Total GMV <i class="fas fa-sort" style="margin-left: 4px; font-size: 10px; color: #64748b;"></i>
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 12px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; text-align: right;">
                            Platform Fees <i class="fas fa-sort" style="margin-left: 4px; font-size: 10px; color: #64748b;"></i>
                        </th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 20px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            Compliance <i class="fas fa-sort" style="margin-left: 4px; font-size: 10px; color: #64748b;"></i>
                        </th>
                    </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                    @php
                        $sellers = [
                            ['name' => 'GreenLeaf Distributors', 'type' => 'Distributor', 'type_color' => '#eff6ff', 'type_text' => '#3b82f6', 'states' => ['CA', 'NV'], 'joined' => 'Jan 15, 2024', 'status' => 'Active', 'status_color' => '#f0fdf4', 'status_text' => '#16a34a', 'plan' => 'Professional', 'products' => '247', 'gmv' => '$284,950', 'fees' => '$34,194', 'compliance' => 96, 'comp_color' => '#10b981'],
                            ['name' => 'CloudNine Brands', 'type' => 'Brand', 'type_color' => '#f5f3ff', 'type_text' => '#8b5cf6', 'states' => ['NY', 'NJ', 'PA'], 'joined' => 'Mar 22, 2024', 'status' => 'Active', 'status_color' => '#f0fdf4', 'status_text' => '#16a34a', 'plan' => 'Premium', 'products' => '189', 'gmv' => '$412,680', 'fees' => '$45,395', 'compliance' => 94, 'comp_color' => '#10b981'],
                            ['name' => 'West Coast Wholesale', 'type' => 'Wholesaler', 'type_color' => '#ecfdf5', 'type_text' => '#10b981', 'states' => ['CA', 'OR', 'WA'], 'joined' => 'Feb 08, 2024', 'status' => 'Pending Review', 'status_color' => '#fffbeb', 'status_text' => '#d97706', 'plan' => 'Professional', 'products' => '156', 'gmv' => '$198,340', 'fees' => '$23,801', 'compliance' => 82, 'comp_color' => '#f59e0b'],
                            ['name' => 'PureGreen Manufacturing', 'type' => 'Manufacturer', 'type_color' => '#f0fdf4', 'type_text' => '#16a34a', 'states' => ['CO'], 'joined' => 'Apr 12, 2024', 'status' => 'Active', 'status_color' => '#f0fdf4', 'status_text' => '#16a34a', 'plan' => 'Enterprise', 'products' => '512', 'gmv' => '$876,420', 'fees' => '$96,406', 'compliance' => 98, 'comp_color' => '#10b981'],
                            ['name' => 'SunnyDaze Supply Co.', 'type' => 'Distributor', 'type_color' => '#eff6ff', 'type_text' => '#3b82f6', 'states' => ['FL', 'GA'], 'joined' => 'May 03, 2024', 'status' => 'Probationary', 'status_color' => '#fff7ed', 'status_text' => '#f97316', 'plan' => 'Basic', 'products' => '89', 'gmv' => '$124,560', 'fees' => '$14,947', 'compliance' => 68, 'comp_color' => '#f43f5e'],
                            ['name' => 'HighTide Brands Inc.', 'type' => 'Brand', 'type_color' => '#f5f3ff', 'type_text' => '#8b5cf6', 'states' => ['MI', 'IL'], 'joined' => 'Jun 18, 2024', 'status' => 'Active', 'status_color' => '#f0fdf4', 'status_text' => '#16a34a', 'plan' => 'Premium', 'products' => '324', 'gmv' => '$534,290', 'fees' => '$58,772', 'compliance' => 91, 'comp_color' => '#10b981'],
                            ['name' => 'EcoLeaf Organics', 'type' => 'Manufacturer', 'type_color' => '#f0fdf4', 'type_text' => '#16a34a', 'states' => ['VT', 'MA'], 'joined' => 'Jul 25, 2024', 'status' => 'Suspended', 'status_color' => '#fef2f2', 'status_text' => '#ef4444', 'plan' => 'Professional', 'products' => '167', 'gmv' => '$0', 'fees' => '$0', 'compliance' => 52, 'comp_color' => '#f43f5e'],
                            ['name' => 'Mountain Peak Extracts', 'type' => 'Wholesaler', 'type_color' => '#ecfdf5', 'type_text' => '#10b981', 'states' => ['AZ', 'NM'], 'joined' => 'Aug 09, 2024', 'status' => 'Active', 'status_color' => '#f0fdf4', 'status_text' => '#16a34a', 'plan' => 'Professional', 'products' => '203', 'gmv' => '$346,780', 'fees' => '$41,614', 'compliance' => 88, 'comp_color' => '#f59e0b'],
                        ];
                    @endphp

                    @foreach($sellers as $seller)
                        <tr class="seller-row" style="background-color: #ffffff !important; border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 16px 20px;">
                                <input type="checkbox"
                                    style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #d1d5db;">
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-chevron-right" style="color: #94a3b8; font-size: 10px;"></i>
                                    <a href="{{ route('new-screens.seller-detail') }}" style="text-decoration: none;">
                                        <span style="font-weight: 700; color: #0369a1; cursor: pointer;">{{ $seller['name'] }}</span>
                                    </a>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span
                                    style="background-color: {{ $seller['type_color'] }}; color: {{ $seller['type_text'] }}; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                    {{ $seller['type'] }}
                                </span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; gap: 4px;">
                                    @foreach($seller['states'] as $state)
                                        <span
                                            style="background-color: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 700;">{{ $state }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td style="padding: 16px 12px; color: #475569; font-size: 13px;">{{ $seller['joined'] }}</td>
                            <td style="padding: 16px 12px;">
                                <span
                                    style="background-color: {{ $seller['status_color'] }}; color: {{ $seller['status_text'] }}; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                    {{ $seller['status'] }}
                                </span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span
                                    style="background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;">
                                    {{ $seller['plan'] }}
                                </span>
                            </td>
                            <td
                                style="padding: 16px 12px; text-align: center; color: #1e293b; font-weight: 600; font-size: 13px;">
                                {{ $seller['products'] }}</td>
                            <td
                                style="padding: 16px 12px; text-align: right; color: #1e293b; font-weight: 800; font-size: 13px;">
                                {{ $seller['gmv'] }}</td>
                            <td
                                style="padding: 16px 12px; text-align: right; color: #6366f1; font-weight: 800; font-size: 13px;">
                                {{ $seller['fees'] }}</td>
                            <td style="padding: 16px 20px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    <span
                                        style="font-weight: 800; color: {{ $seller['comp_color'] }}; font-size: 14px;">{{ $seller['compliance'] }}</span>
                                    <div class="compliance-circle">
                                        <svg width="32" height="32">
                                            <circle cx="16" cy="16" r="14" fill="transparent" stroke="#f1f5f9" stroke-width="3">
                                            </circle>
                                            <circle cx="16" cy="16" r="14" fill="transparent"
                                                stroke="{{ $seller['comp_color'] }}" stroke-width="3" stroke-dasharray="87.96"
                                                stroke-dashoffset="{{ 87.96 * (1 - $seller['compliance'] / 100) }}"
                                                stroke-linecap="round"></circle>
                                        </svg>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Container -->
            <div
                style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background-color: #ffffff;">
                <div style="font-size: 13px; color: #64748b;">
                    Showing 1-25 of 1,247
                </div>
                <div style="display: flex; gap: 8px;">
                    <button
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 6px; color: #94a3b8; background: white; cursor: not-allowed;">
                        <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                    </button>
                    <button
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 6px; background-color: #f3a847; color: white; border: none; font-weight: 800; font-size: 13px;">1</button>
                    <button
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 6px; background-color: white; color: #4b5563; font-weight: 600; font-size: 13px;"
                        onmouseover="this.style.backgroundColor='#f9fafb'"
                        onmouseout="this.style.backgroundColor='white'">2</button>
                    <button
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 6px; background-color: white; color: #4b5563; font-weight: 600; font-size: 13px;"
                        onmouseover="this.style.backgroundColor='#f9fafb'"
                        onmouseout="this.style.backgroundColor='white'">3</button>
                    <button
                        style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 6px; color: #64748b; background: white; cursor: pointer;">
                        <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
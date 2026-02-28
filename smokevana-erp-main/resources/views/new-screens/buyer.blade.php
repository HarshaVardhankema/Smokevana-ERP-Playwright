@extends('layouts.app')
@section('title', 'Buyer')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Metric Cards Row -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap;">
        <!-- Total Buyers Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <div style="padding: 16px;">
                <!-- Icon -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users" style="color: #3b82f6; font-size: 24px;"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    TOTAL BUYERS
                </h3>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    8,430
                </div>
            </div>
        </div>

        <!-- Prime Members Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <div style="padding: 16px;">
                <!-- Icon -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-crown" style="color: #3b82f6; font-size: 24px;"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    PRIME MEMBERS
                </h3>
                
                <!-- Main Value with Percentage -->
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 8px;">
                    <div style="font-size: 30px; font-weight: 700; color: #111827; word-break: break-word;">
                        3,120
                    </div>
                    <span style="font-size: 16px; font-weight: 500; color: #3b82f6;">(37%)</span>
                </div>
                
                <!-- PRIME Badge -->
                <div style="display: inline-block; padding: 4px 8px; background-color: #3b82f6; color: #ffffff; border-radius: 4px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    PRIME
                </div>
            </div>
        </div>

        <!-- Active This Month Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <div style="padding: 16px;">
                <!-- Icon -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="color: #10b981; font-size: 24px;"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    ACTIVE THIS MONTH
                </h3>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #10b981; margin-bottom: 8px; word-break: break-word;">
                    5,670
                </div>
            </div>
        </div>

        <!-- New This Month Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <div style="padding: 16px;">
                <!-- Icon -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #ccfbf1; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-plus" style="color: #14b8a6; font-size: 24px;"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    NEW THIS MONTH
                </h3>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #14b8a6; margin-bottom: 8px; word-break: break-word;">
                    412
                </div>
            </div>
        </div>

        <!-- At-Risk (60+ Days) Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <div style="padding: 16px;">
                <!-- Icon -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #fed7aa; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle" style="color: #f97316; font-size: 24px;"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    AT-RISK (60+ DAYS)
                </h3>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #f97316; margin-bottom: 8px; word-break: break-word;">
                    234
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Section -->
    <div style="margin-top: 24px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Filters & Search</h2>
        </div>
        
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Search Bar -->
            <div style="position: relative; flex: 1; min-width: 250px;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;"></i>
                <input type="text" id="search-input" placeholder="Search buyers..." style="width: 100%; padding: 10px 12px 10px 36px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #111827;">
            </div>
            
            <!-- Dropdown Filters -->
            <select id="status-filter" style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #111827; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option value="all">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            
            <select id="prime-tier-filter" style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #111827; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option value="all">All Prime Tiers</option>
                <option value="Prime Elite">Prime Elite</option>
                <option value="Prime Pro">Prime Pro</option>
                <option value="Prime Max">Prime Max</option>
                <option value="Prime Lite">Prime Lite</option>
                <option value="Non-Prime">Non-Prime</option>
            </select>
            
            <select id="state-filter" style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #111827; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option value="all">All States</option>
                <option value="CA">CA</option>
                <option value="NV">NV</option>
                <option value="CO">CO</option>
                <option value="OR">OR</option>
            </select>
            
            <select id="store-type-filter" style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #111827; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option value="all">All Store Types</option>
                <option value="Dispensary">Dispensary</option>
                <option value="Smoke Shop">Smoke Shop</option>
                <option value="Vape Shop">Vape Shop</option>
                <option value="Convenience">Convenience</option>
                <option value="Gas Station">Gas Station</option>
            </select>
            
            <select id="last-order-filter" style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #111827; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option value="all">Last Order: All</option>
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
            
            <!-- Advanced Filters Button -->
            <button style="padding: 10px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <span>Advanced Filters</span>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>

    <!-- Buyers Table Section -->
    <div style="margin-top: 24px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
        <div style="padding: 24px;">
            <!-- Table Header -->
            <div style="margin-bottom: 16px;">
                <h2 id="table-header" style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">All Buyers Showing 1-25 of 8,430</h2>
            </div>
            
            <!-- Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Buyer / Store</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Store Type</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">State</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Joined</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Status</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Prime Tier</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Orders</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Lifetime Spend</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Platform Fees</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Avg Order</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Last Order</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Complianc</th>
                        </tr>
                    </thead>
                    <tbody id="buyer-table-body">
                        <!-- Row 1 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: block; margin-bottom: 4px;">Sunset Dispensary</a>
                                <span style="color: #6b7280; font-size: 12px;">contact@sunsetdispensary.com</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #e9d5ff; color: #7c3aed;">Dispensary</span>
                            </td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">CA</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 15, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Active</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #1e3a8a; color: #ffffff;">Prime Elite</span>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">342</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$89,450</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$10,734</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">$261</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Oct 28, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Valid</span>
                            </td>
                        </tr>
                        
                        <!-- Row 2 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: block; margin-bottom: 4px;">Green Valley Retail</a>
                                <span style="color: #6b7280; font-size: 12px;">info@greenvalleyretail.com</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #dbeafe; color: #1e40af;">Smoke Shop</span>
                            </td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">NV</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 20, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Active</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #3b82f6; color: #ffffff;">Prime Pro</span>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">289</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$72,340</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$8,681</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">$250</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Oct 25, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Valid</span>
                            </td>
                        </tr>
                        
                        <!-- Row 3 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: block; margin-bottom: 4px;">Vape Central</a>
                                <span style="color: #6b7280; font-size: 12px;">sales@vapecentral.com</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #d1fae5; color: #166534;">Vape Shop</span>
                            </td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">CO</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Mar 10, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Active</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #60a5fa; color: #ffffff;">Prime Max</span>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">156</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$45,230</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$5,428</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">$290</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Oct 22, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Valid</span>
                            </td>
                        </tr>
                        
                        <!-- Row 4 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: block; margin-bottom: 4px;">Quick Stop Convenience</a>
                                <span style="color: #6b7280; font-size: 12px;">orders@quickstop.com</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #fed7aa; color: #c2410c;">Convenience</span>
                            </td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">OR</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Apr 5, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fed7aa; color: #ea580c;">Inactive</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #bfdbfe; color: #1e40af;">Prime Lite</span>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">89</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$18,920</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$2,270</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">$213</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Sep 15, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fed7aa; color: #ea580c;">Expiring</span>
                            </td>
                        </tr>
                        
                        <!-- Row 5 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: block; margin-bottom: 4px;">Rocky Mountain Dispensary</a>
                                <span style="color: #6b7280; font-size: 12px;">info@rockymountaindisp.com</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #e9d5ff; color: #7c3aed;">Dispensary</span>
                            </td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">CO</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">May 12, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fed7aa; color: #ea580c;">Inactive</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #e5e7eb; color: #374151;">Non-Prime</span>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">28</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$8,450</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$1,183</td>
                            <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">$302</td>
                            <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Aug 15, 2024</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Valid</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                <div id="pagination-info" style="font-size: 14px; color: #6b7280;">
                    Showing <span style="font-weight: 600; color: #111827;">1-10</span> of <span style="font-weight: 600; color: #111827;">8,430</span> buyers
                </div>
                <div id="pagination-controls" style="display: flex; align-items: center; gap: 8px;">
                    <!-- Pagination buttons will be dynamically generated -->
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row: Buyer Distribution by Store Type & Prime Membership Distribution -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; margin-top: 24px; flex-wrap: wrap;">
        <!-- Buyer Distribution by Store Type Chart -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 400px;">
            <div style="padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 24px 0;">Buyer Distribution by Store Type</h2>
                
                <!-- Chart Container -->
                <div style="position: relative; height: 300px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="storeTypeDistributionChart"></canvas>
                </div>
                
                <!-- Legend -->
                <div style="margin-top: 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #3b82f6; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Smoke Shop</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">33.7%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #14b8a6; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Vape Shop</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">25.3%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #a855f7; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Dispensary</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">23.6%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #f97316; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Convenience Store</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">10.5%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Gas Station</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">6.79%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prime Membership Distribution Chart -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 400px;">
            <div style="padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 24px 0;">Prime Membership Distribution</h2>
                
                <!-- Chart Container -->
                <div style="position: relative; height: 300px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="primeMembershipChart"></canvas>
                </div>
                
                <!-- Legend -->
                <div style="margin-top: 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #e5e7eb; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Non-Prime</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">63%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #bfdbfe; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Prime Lite</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">14.8%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #3b82f6; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Prime Pro</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">11.7%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #1e3a8a; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Prime Elite</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">7.39%</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #1e40af; flex-shrink: 0;"></div>
                            <span style="color: #374151; font-size: 14px;">Prime Max</span>
                        </div>
                        <span style="color: #111827; font-weight: 600; font-size: 14px;">3.14%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Three Cards Row: Top Buyers, Buyers by State, At-Risk Buyers -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; margin-top: 24px; flex-wrap: wrap;">
        <!-- Top Buyers by Lifetime Spend Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 24px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                    <i class="fas fa-trophy" style="color: #f59e0b; font-size: 18px;"></i>
                    <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Top Buyers by Lifetime Spend</h2>
                </div>
                
                <!-- Top Buyers List -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Rank 1 -->
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 8px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f59e0b; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 14px; font-weight: 700; flex-shrink: 0;">1</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">CloudNine Shop</div>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Prime Max</div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <span style="font-size: 14px; font-weight: 600; color: #10b981;">$124,890</span>
                                <span style="font-size: 12px; color: #6b7280;">•</span>
                                <span style="font-size: 14px; font-weight: 500; color: #3b82f6;">$14,987 fees</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rank 2 -->
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 8px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #9ca3af; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 14px; font-weight: 700; flex-shrink: 0;">2</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Sunset Dispensary</div>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Prime Elite</div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <span style="font-size: 14px; font-weight: 600; color: #10b981;">$89,450</span>
                                <span style="font-size: 12px; color: #6b7280;">•</span>
                                <span style="font-size: 14px; font-weight: 500; color: #3b82f6;">$10,734 fees</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rank 3 -->
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 8px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #fb923c; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 14px; font-weight: 700; flex-shrink: 0;">3</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Green Valley Retail</div>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Prime Pro</div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <span style="font-size: 14px; font-weight: 600; color: #10b981;">$72,340</span>
                                <span style="font-size: 12px; color: #6b7280;">•</span>
                                <span style="font-size: 14px; font-weight: 500; color: #3b82f6;">$8,681 fees</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rank 4 -->
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 8px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #9ca3af; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 14px; font-weight: 700; flex-shrink: 0;">4</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Pacific Coast Wholesale</div>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Prime Lite</div>
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <span style="font-size: 14px; font-weight: 600; color: #10b981;">$56,780</span>
                                <span style="font-size: 12px; color: #6b7280;">•</span>
                                <span style="font-size: 14px; font-weight: 500; color: #3b82f6;">$6,814 fees</span>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </div>

        <!-- Buyers by State Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 24px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                    <i class="fas fa-map-marker-alt" style="color: #f97316; font-size: 18px;"></i>
                    <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Buyers by State</h2>
                </div>
                
                <!-- State Bars -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- California -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">California</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">3,456 buyers</span>
                        </div>
                        <div style="width: 100%; height: 12px; background-color: #e5e7eb; border-radius: 6px; overflow: hidden;">
                            <div style="width: 100%; height: 100%; background-color: #3b82f6; border-radius: 6px;"></div>
                        </div>
                    </div>
                    
                    <!-- Nevada -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Nevada</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">2,134 buyers</span>
                        </div>
                        <div style="width: 100%; height: 12px; background-color: #e5e7eb; border-radius: 6px; overflow: hidden;">
                            <div style="width: 62%; height: 100%; background-color: #14b8a6; border-radius: 6px;"></div>
                        </div>
                    </div>
                    
                    <!-- Colorado -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Colorado</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">1,567 buyers</span>
                        </div>
                        <div style="width: 100%; height: 12px; background-color: #e5e7eb; border-radius: 6px; overflow: hidden;">
                            <div style="width: 45%; height: 100%; background-color: #10b981; border-radius: 6px;"></div>
                        </div>
                    </div>
                    
                    <!-- Oregon -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Oregon</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">892 buyers</span>
                        </div>
                        <div style="width: 100%; height: 12px; background-color: #e5e7eb; border-radius: 6px; overflow: hidden;">
                            <div style="width: 26%; height: 100%; background-color: #a855f7; border-radius: 6px;"></div>
                        </div>
                    </div>
                    
                    <!-- Washington -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Washington</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">381 buyers</span>
                        </div>
                        <div style="width: 100%; height: 12px; background-color: #e5e7eb; border-radius: 6px; overflow: hidden;">
                            <div style="width: 11%; height: 100%; background-color: #f97316; border-radius: 6px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- At-Risk Buyers Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 24px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f97316; font-size: 18px;"></i>
                    <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">At-Risk Buyers (60+ Days)</h2>
                </div>
                
                <!-- At-Risk Buyers List -->
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
                    <!-- Rocky Mountain Dispensary -->
                    <div style="padding: 16px; background-color: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Rocky Mountain Dispensary</div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Last order: Aug 15, 2024</div>
                        <div style="font-size: 12px; color: #6b7280;">Lost fees: ~$450/mo</div>
                    </div>
                    
                    <!-- Desert Smoke Shop -->
                    <div style="padding: 16px; background-color: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Desert Smoke Shop</div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Last order: Aug 21, 2024</div>
                        <div style="font-size: 12px; color: #6b7280;">Lost fees: ~$320/mo</div>
                    </div>
                    
                    <!-- Valley Vape Lounge -->
                    <div style="padding: 16px; background-color: #fef3c7; border-radius: 8px; border: 1px solid #fde68a;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Valley Vape Lounge</div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Last order: Aug 27, 2024</div>
                        <div style="font-size: 12px; color: #6b7280;">Lost fees: ~$280/mo</div>
                    </div>
                </div>
                
                <!-- Re-Engagement Campaign Button -->
                <button style="width: 100%; padding: 12px 16px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #f97316; border: 1px solid #f97316; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fas fa-envelope" style="font-size: 14px;"></i>
                    <span>Send Re-Engagement Campaign</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Buyer Growth & Activity Trends Chart Row -->
    <div style="margin-top: 24px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
        <div style="padding: 24px;">
            <!-- Header -->
            <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 24px 0;">Buyer Growth & Activity Trends (Last 12 Months)</h2>
            
            <!-- Chart Container -->
            <div style="position: relative; height: 400px;">
                <canvas id="buyerGrowthChart"></canvas>
            </div>
            
            <!-- Legend -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 32px; margin-top: 24px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 3px; background-color: #14b8a6; border-radius: 2px;"></div>
                    <span style="font-size: 14px; color: #374151;">New Buyers</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 3px; background-color: #3b82f6; border-radius: 2px;"></div>
                    <span style="font-size: 14px; color: #374151;">Active Buyers</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 3px; background-color: #f97316; border-radius: 2px;"></div>
                    <span style="font-size: 14px; color: #374151;">At-Risk Buyers</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
    // Helper function to get initials from name
    function getInitials(name) {
        const words = name.split(' ');
        if (words.length >= 2) {
            return (words[0][0] + words[1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }

    // Helper function to generate avatar color based on name
    function getAvatarColor(name) {
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        const index = name.charCodeAt(0) % colors.length;
        return colors[index];
    }

    // Dummy buyer data
    const allBuyers = [
        { name: 'Sunset Dispensary', email: 'contact@sunsetdispensary.com', storeType: 'Dispensary', state: 'CA', joined: 'Jan 15, 2024', status: 'Active', primeTier: 'Prime Elite', orders: 342, lifetimeSpend: 89450, platformFees: 10734, avgOrder: 261, lastOrder: 'Oct 28, 2024', compliance: 'Valid' },
        { name: 'Green Valley Retail', email: 'info@greenvalleyretail.com', storeType: 'Smoke Shop', state: 'NV', joined: 'Feb 20, 2024', status: 'Active', primeTier: 'Prime Pro', orders: 289, lifetimeSpend: 72340, platformFees: 8681, avgOrder: 250, lastOrder: 'Oct 25, 2024', compliance: 'Valid' },
        { name: 'Vape Central', email: 'sales@vapecentral.com', storeType: 'Vape Shop', state: 'CO', joined: 'Mar 10, 2024', status: 'Active', primeTier: 'Prime Max', orders: 156, lifetimeSpend: 45230, platformFees: 5428, avgOrder: 290, lastOrder: 'Oct 22, 2024', compliance: 'Valid' },
        { name: 'Quick Stop Convenience', email: 'orders@quickstop.com', storeType: 'Convenience', state: 'OR', joined: 'Apr 5, 2024', status: 'Inactive', primeTier: 'Prime Lite', orders: 89, lifetimeSpend: 18920, platformFees: 2270, avgOrder: 213, lastOrder: 'Sep 15, 2024', compliance: 'Expiring' },
        { name: 'Rocky Mountain Dispensary', email: 'info@rockymountaindisp.com', storeType: 'Dispensary', state: 'CO', joined: 'May 12, 2024', status: 'Inactive', primeTier: 'Non-Prime', orders: 28, lifetimeSpend: 8450, platformFees: 1183, avgOrder: 302, lastOrder: 'Aug 15, 2024', compliance: 'Valid' },
        { name: 'Pacific Coast Vapes', email: 'hello@pacificvapes.com', storeType: 'Vape Shop', state: 'CA', joined: 'Jan 8, 2024', status: 'Active', primeTier: 'Prime Elite', orders: 412, lifetimeSpend: 125680, platformFees: 15082, avgOrder: 305, lastOrder: 'Oct 30, 2024', compliance: 'Valid' },
        { name: 'Mountain View Dispensary', email: 'contact@mountainview.com', storeType: 'Dispensary', state: 'CO', joined: 'Feb 14, 2024', status: 'Active', primeTier: 'Prime Pro', orders: 298, lifetimeSpend: 98760, platformFees: 11851, avgOrder: 331, lastOrder: 'Oct 27, 2024', compliance: 'Valid' },
        { name: 'Desert Smoke Shop', email: 'info@desertsmoke.com', storeType: 'Smoke Shop', state: 'NV', joined: 'Mar 22, 2024', status: 'Active', primeTier: 'Prime Max', orders: 187, lifetimeSpend: 52340, platformFees: 6281, avgOrder: 280, lastOrder: 'Oct 24, 2024', compliance: 'Valid' },
        { name: 'City Gas Station', email: 'orders@citygas.com', storeType: 'Gas Station', state: 'CA', joined: 'Apr 18, 2024', status: 'Inactive', primeTier: 'Non-Prime', orders: 45, lifetimeSpend: 12340, platformFees: 1728, avgOrder: 274, lastOrder: 'Sep 8, 2024', compliance: 'Expiring' },
        { name: 'Highway Convenience', email: 'sales@highwayconv.com', storeType: 'Convenience', state: 'OR', joined: 'May 5, 2024', status: 'Active', primeTier: 'Prime Lite', orders: 124, lifetimeSpend: 28960, platformFees: 3475, avgOrder: 234, lastOrder: 'Oct 20, 2024', compliance: 'Valid' },
        { name: 'Golden State Dispensary', email: 'info@goldenstate.com', storeType: 'Dispensary', state: 'CA', joined: 'Jan 25, 2024', status: 'Active', primeTier: 'Prime Elite', orders: 356, lifetimeSpend: 112450, platformFees: 13494, avgOrder: 316, lastOrder: 'Oct 29, 2024', compliance: 'Valid' },
        { name: 'Silver State Vapes', email: 'contact@silverstate.com', storeType: 'Vape Shop', state: 'NV', joined: 'Feb 28, 2024', status: 'Active', primeTier: 'Prime Pro', orders: 267, lifetimeSpend: 78450, platformFees: 9414, avgOrder: 294, lastOrder: 'Oct 26, 2024', compliance: 'Valid' },
        { name: 'Rocky Top Smoke', email: 'hello@rockytop.com', storeType: 'Smoke Shop', state: 'CO', joined: 'Mar 15, 2024', status: 'Inactive', primeTier: 'Prime Max', orders: 98, lifetimeSpend: 23450, platformFees: 2814, avgOrder: 239, lastOrder: 'Aug 20, 2024', compliance: 'Valid' },
        { name: 'Coastal Convenience', email: 'orders@coastal.com', storeType: 'Convenience', state: 'CA', joined: 'Apr 10, 2024', status: 'Active', primeTier: 'Prime Lite', orders: 156, lifetimeSpend: 34560, platformFees: 4147, avgOrder: 222, lastOrder: 'Oct 23, 2024', compliance: 'Valid' },
        { name: 'Express Gas Mart', email: 'info@expressgas.com', storeType: 'Gas Station', state: 'OR', joined: 'May 20, 2024', status: 'Inactive', primeTier: 'Non-Prime', orders: 32, lifetimeSpend: 8760, platformFees: 1226, avgOrder: 274, lastOrder: 'Sep 5, 2024', compliance: 'Expiring' },
        { name: 'Sunrise Dispensary', email: 'contact@sunrise.com', storeType: 'Dispensary', state: 'CA', joined: 'Jan 30, 2024', status: 'Active', primeTier: 'Prime Elite', orders: 389, lifetimeSpend: 134560, platformFees: 16147, avgOrder: 346, lastOrder: 'Oct 31, 2024', compliance: 'Valid' },
        { name: 'Nevada Vape Co', email: 'sales@nevadavape.com', storeType: 'Vape Shop', state: 'NV', joined: 'Feb 12, 2024', status: 'Active', primeTier: 'Prime Pro', orders: 245, lifetimeSpend: 67890, platformFees: 8147, avgOrder: 277, lastOrder: 'Oct 28, 2024', compliance: 'Valid' },
        { name: 'Mountain Smoke', email: 'info@mountainsmoke.com', storeType: 'Smoke Shop', state: 'CO', joined: 'Mar 28, 2024', status: 'Active', primeTier: 'Prime Max', orders: 178, lifetimeSpend: 49870, platformFees: 5984, avgOrder: 280, lastOrder: 'Oct 25, 2024', compliance: 'Valid' },
        { name: 'Quick Mart', email: 'orders@quickmart.com', storeType: 'Convenience', state: 'OR', joined: 'Apr 22, 2024', status: 'Inactive', primeTier: 'Prime Lite', orders: 67, lifetimeSpend: 14560, platformFees: 1747, avgOrder: 217, lastOrder: 'Aug 10, 2024', compliance: 'Expiring' },
        { name: 'Pacific Gas Stop', email: 'hello@pacificgas.com', storeType: 'Gas Station', state: 'CA', joined: 'May 8, 2024', status: 'Active', primeTier: 'Non-Prime', orders: 89, lifetimeSpend: 21340, platformFees: 2988, avgOrder: 240, lastOrder: 'Oct 21, 2024', compliance: 'Valid' },
        { name: 'Elite Dispensary', email: 'contact@elite.com', storeType: 'Dispensary', state: 'CA', joined: 'Jan 18, 2024', status: 'Active', primeTier: 'Prime Elite', orders: 421, lifetimeSpend: 145680, platformFees: 17482, avgOrder: 346, lastOrder: 'Oct 30, 2024', compliance: 'Valid' },
        { name: 'Desert Vape', email: 'info@desertvape.com', storeType: 'Vape Shop', state: 'NV', joined: 'Feb 25, 2024', status: 'Active', primeTier: 'Prime Pro', orders: 234, lifetimeSpend: 65430, platformFees: 7852, avgOrder: 280, lastOrder: 'Oct 27, 2024', compliance: 'Valid' },
        { name: 'High Country Smoke', email: 'sales@highcountry.com', storeType: 'Smoke Shop', state: 'CO', joined: 'Mar 5, 2024', status: 'Inactive', primeTier: 'Prime Max', orders: 112, lifetimeSpend: 31240, platformFees: 3749, avgOrder: 279, lastOrder: 'Sep 12, 2024', compliance: 'Valid' },
        { name: 'Corner Store', email: 'orders@cornerstore.com', storeType: 'Convenience', state: 'OR', joined: 'Apr 15, 2024', status: 'Active', primeTier: 'Prime Lite', orders: 145, lifetimeSpend: 32340, platformFees: 3881, avgOrder: 223, lastOrder: 'Oct 24, 2024', compliance: 'Valid' },
        { name: 'Route 66 Gas', email: 'info@route66.com', storeType: 'Gas Station', state: 'CA', joined: 'May 15, 2024', status: 'Inactive', primeTier: 'Non-Prime', orders: 54, lifetimeSpend: 14560, platformFees: 2038, avgOrder: 270, lastOrder: 'Aug 25, 2024', compliance: 'Expiring' }
    ];

    // Store Type badge styles
    const storeTypeStyles = {
        'Dispensary': { bg: '#e9d5ff', color: '#7c3aed' },
        'Smoke Shop': { bg: '#dbeafe', color: '#1e40af' },
        'Vape Shop': { bg: '#d1fae5', color: '#166534' },
        'Convenience': { bg: '#fed7aa', color: '#c2410c' },
        'Gas Station': { bg: '#fee2e2', color: '#991b1b' }
    };

    // Prime Tier badge styles
    const primeTierStyles = {
        'Prime Elite': { bg: '#1e3a8a', color: '#ffffff' },
        'Prime Pro': { bg: '#3b82f6', color: '#ffffff' },
        'Prime Max': { bg: '#60a5fa', color: '#ffffff' },
        'Prime Lite': { bg: '#bfdbfe', color: '#1e40af' },
        'Non-Prime': { bg: '#e5e7eb', color: '#374151' }
    };

    // Status badge styles
    const statusStyles = {
        'Active': { bg: '#dcfce7', color: '#16a34a' },
        'Inactive': { bg: '#fed7aa', color: '#ea580c' }
    };

    // Compliance badge styles
    const complianceStyles = {
        'Valid': { bg: '#dcfce7', color: '#16a34a' },
        'Expiring': { bg: '#fed7aa', color: '#ea580c' }
    };

    let currentPage = 1;
    const itemsPerPage = 10;

    // Filter and paginate buyers
    function filterAndPaginateBuyers() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const primeTierFilter = document.getElementById('prime-tier-filter').value;
        const stateFilter = document.getElementById('state-filter').value;
        const storeTypeFilter = document.getElementById('store-type-filter').value;
        const lastOrderFilter = document.getElementById('last-order-filter').value;

        // Filter buyers
        let filteredBuyers = allBuyers.filter(buyer => {
            // Search filter
            if (searchTerm && !buyer.name.toLowerCase().includes(searchTerm) && !buyer.email.toLowerCase().includes(searchTerm)) {
                return false;
            }

            // Status filter
            if (statusFilter !== 'all' && buyer.status !== statusFilter) {
                return false;
            }

            // Prime Tier filter
            if (primeTierFilter !== 'all' && buyer.primeTier !== primeTierFilter) {
                return false;
            }

            // State filter
            if (stateFilter !== 'all' && buyer.state !== stateFilter) {
                return false;
            }

            // Store Type filter
            if (storeTypeFilter !== 'all' && buyer.storeType !== storeTypeFilter) {
                return false;
            }

            // Last Order filter (simplified - just checking if last order exists)
            if (lastOrderFilter !== 'all') {
                // For demo purposes, we'll just check if lastOrder exists
                // In real implementation, you'd parse dates and check ranges
            }

            return true;
        });

        // Update total count
        const totalCount = filteredBuyers.length;
        const totalPages = Math.ceil(totalCount / itemsPerPage);

        // Reset to page 1 if current page is out of bounds
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = 1;
        }

        // Get paginated buyers
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedBuyers = filteredBuyers.slice(startIndex, endIndex);

        // Render table
        renderTable(paginatedBuyers);

        // Update header
        const start = totalCount > 0 ? startIndex + 1 : 0;
        const end = Math.min(endIndex, totalCount);
        document.getElementById('table-header').textContent = `All Buyers Showing ${start}-${end} of ${totalCount.toLocaleString()}`;

        // Update pagination info
        document.getElementById('pagination-info').innerHTML = `Showing <span style="font-weight: 600; color: #111827;">${start}-${end}</span> of <span style="font-weight: 600; color: #111827;">${totalCount.toLocaleString()}</span> buyers`;

        // Render pagination
        renderPagination(totalPages);
    }

    // Render table rows
    function renderTable(buyers) {
        const tbody = document.getElementById('buyer-table-body');
        tbody.innerHTML = '';

        buyers.forEach(buyer => {
            const storeTypeStyle = storeTypeStyles[buyer.storeType];
            const primeTierStyle = primeTierStyles[buyer.primeTier];
            const statusStyle = statusStyles[buyer.status];
            const complianceStyle = complianceStyles[buyer.compliance];

            const initials = getInitials(buyer.name);
            const avatarColor = getAvatarColor(buyer.name);
            
            const row = `
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 16px 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: ${avatarColor}; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 14px; font-weight: 600; flex-shrink: 0;">
                                ${initials}
                            </div>
                            <div style="flex: 1;">
                                <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: block; margin-bottom: 4px;">${buyer.name}</a>
                                <span style="color: #6b7280; font-size: 12px;">${buyer.email}</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 12px;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: ${storeTypeStyle.bg}; color: ${storeTypeStyle.color};">${buyer.storeType}</span>
                    </td>
                    <td style="padding: 16px 12px; color: #374151; font-size: 14px;">${buyer.state}</td>
                    <td style="padding: 16px 12px; color: #374151; font-size: 14px;">${buyer.joined}</td>
                    <td style="padding: 16px 12px; text-align: center;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: ${statusStyle.bg}; color: ${statusStyle.color};">${buyer.status}</span>
                    </td>
                    <td style="padding: 16px 12px;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: ${primeTierStyle.bg}; color: ${primeTierStyle.color};">${buyer.primeTier}</span>
                    </td>
                    <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">${buyer.orders.toLocaleString()}</td>
                    <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$${buyer.lifetimeSpend.toLocaleString()}</td>
                    <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$${buyer.platformFees.toLocaleString()}</td>
                    <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px;">$${buyer.avgOrder}</td>
                    <td style="padding: 16px 12px; color: #374151; font-size: 14px;">${buyer.lastOrder}</td>
                    <td style="padding: 16px 12px; text-align: center;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: ${complianceStyle.bg}; color: ${complianceStyle.color};">${buyer.compliance}</span>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    // Render pagination controls
    function renderPagination(totalPages) {
        const paginationControls = document.getElementById('pagination-controls');
        paginationControls.innerHTML = '';

        if (totalPages === 0) {
            return;
        }

        // Previous button
        const prevButton = document.createElement('button');
        prevButton.innerHTML = '<i class="fas fa-chevron-left" style="font-size: 12px;"></i>';
        prevButton.style.cssText = 'padding: 8px 12px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;';
        prevButton.disabled = currentPage === 1;
        if (prevButton.disabled) {
            prevButton.style.opacity = '0.5';
            prevButton.style.cursor = 'not-allowed';
        }
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                filterAndPaginateBuyers();
            }
        });
        paginationControls.appendChild(prevButton);

        // Page number buttons
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            const firstButton = document.createElement('button');
            firstButton.textContent = '1';
            firstButton.style.cssText = 'padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;';
            firstButton.addEventListener('click', () => {
                currentPage = 1;
                filterAndPaginateBuyers();
            });
            paginationControls.appendChild(firstButton);

            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.style.cssText = 'padding: 8px 4px; font-size: 14px; color: #6b7280;';
                paginationControls.appendChild(ellipsis);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            if (i === currentPage) {
                pageButton.style.cssText = 'padding: 8px 12px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #ecab39; border: 1px solid #ecab39; border-radius: 6px; cursor: pointer;';
            } else {
                pageButton.style.cssText = 'padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;';
            }
            pageButton.addEventListener('click', () => {
                currentPage = i;
                filterAndPaginateBuyers();
            });
            paginationControls.appendChild(pageButton);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.style.cssText = 'padding: 8px 4px; font-size: 14px; color: #6b7280;';
                paginationControls.appendChild(ellipsis);
            }

            const lastButton = document.createElement('button');
            lastButton.textContent = totalPages;
            lastButton.style.cssText = 'padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;';
            lastButton.addEventListener('click', () => {
                currentPage = totalPages;
                filterAndPaginateBuyers();
            });
            paginationControls.appendChild(lastButton);
        }

        // Next button
        const nextButton = document.createElement('button');
        nextButton.innerHTML = '<i class="fas fa-chevron-right" style="font-size: 12px;"></i>';
        nextButton.style.cssText = 'padding: 8px 12px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;';
        nextButton.disabled = currentPage === totalPages;
        if (nextButton.disabled) {
            nextButton.style.opacity = '0.5';
            nextButton.style.cursor = 'not-allowed';
        }
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                filterAndPaginateBuyers();
            }
        });
        paginationControls.appendChild(nextButton);
    }

    // Event listeners
    document.getElementById('search-input').addEventListener('input', () => {
        currentPage = 1;
        filterAndPaginateBuyers();
    });

    document.getElementById('status-filter').addEventListener('change', () => {
        currentPage = 1;
        filterAndPaginateBuyers();
    });

    document.getElementById('prime-tier-filter').addEventListener('change', () => {
        currentPage = 1;
        filterAndPaginateBuyers();
    });

    document.getElementById('state-filter').addEventListener('change', () => {
        currentPage = 1;
        filterAndPaginateBuyers();
    });

    document.getElementById('store-type-filter').addEventListener('change', () => {
        currentPage = 1;
        filterAndPaginateBuyers();
    });

    document.getElementById('last-order-filter').addEventListener('change', () => {
        currentPage = 1;
        filterAndPaginateBuyers();
    });

    // Initial load
    filterAndPaginateBuyers();
</script>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script type="text/javascript">
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Buyer Distribution by Store Type Chart
        const storeTypeCtx = document.getElementById('storeTypeDistributionChart').getContext('2d');
        const storeTypeChart = new Chart(storeTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Smoke Shop', 'Vape Shop', 'Dispensary', 'Convenience Store', 'Gas Station'],
                datasets: [{
                    data: [33.7, 25.3, 23.6, 10.5, 6.79],
                    backgroundColor: [
                        '#3b82f6', // Smoke Shop - Blue
                        '#14b8a6', // Vape Shop - Teal
                        '#a855f7', // Dispensary - Purple
                        '#f97316', // Convenience Store - Orange
                        '#ef4444'  // Gas Station - Red
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });

        // Prime Membership Distribution Chart
        const primeMembershipCtx = document.getElementById('primeMembershipChart').getContext('2d');
        const primeMembershipChart = new Chart(primeMembershipCtx, {
            type: 'doughnut',
            data: {
                labels: ['Non-Prime', 'Prime Lite', 'Prime Pro', 'Prime Elite', 'Prime Max'],
                datasets: [{
                    data: [63, 14.8, 11.7, 7.39, 3.14],
                    backgroundColor: [
                        '#e5e7eb', // Non-Prime - Light Gray
                        '#bfdbfe', // Prime Lite - Light Blue
                        '#3b82f6', // Prime Pro - Medium Blue
                        '#1e3a8a', // Prime Elite - Dark Blue
                        '#1e40af'  // Prime Max - Darker Blue
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });

        // Buyer Growth & Activity Trends Line Chart
        const buyerGrowthCtx = document.getElementById('buyerGrowthChart').getContext('2d');
        const buyerGrowthChart = new Chart(buyerGrowthCtx, {
            type: 'line',
            data: {
                labels: ['Nov 23', 'Dec 23', 'Jan 24', 'Feb 24', 'Mar 24', 'Apr 24', 'May 24', 'Jun 24', 'Jul 24', 'Aug 24', 'Sep 24'],
                datasets: [
                    {
                        label: 'New Buyers',
                        data: [250, 280, 450, 380, 320, 350, 300, 280, 250, 220, 200],
                        borderColor: '#14b8a6',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#14b8a6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Active Buyers',
                        data: [4200, 4400, 4600, 5000, 5200, 5400, 5600, 5700, 5800, 5700, 5600],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'At-Risk Buyers',
                        data: [120, 130, 140, 150, 140, 130, 120, 110, 100, 110, 120],
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 6000,
                        ticks: {
                            stepSize: 1000,
                            font: {
                                size: 12
                            },
                            color: '#6b7280',
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        grid: {
                            color: '#e5e7eb',
                            drawBorder: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    });
</script>
@endsection

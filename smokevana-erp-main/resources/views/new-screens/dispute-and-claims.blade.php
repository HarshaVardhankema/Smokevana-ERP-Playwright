@extends('layouts.app')
@section('title', 'Dispute & Claims')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Header Section -->
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="flex: 1; min-width: 300px;">
            <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">Dispute Resolution Center</h1>
            <p style="font-size: 14px; color: #6b7280; margin: 0;">Manage buyer-seller disputes with evidence review and resolution tools.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Export Report Button -->
            <button style="padding: 10px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-download" style="font-size: 14px;"></i>
                <span>Export Report</span>
            </button>
            <!-- Advanced Filters Button -->
            <button style="padding: 10px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: 1px solid #a855f7; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; gap: 8px;">
                <span>Advanced Filters</span>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>

    <!-- Metric Cards Row -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap;">
        <!-- Urgent Disputes Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative; flex: 1; min-width: 200px; border: 2px solid #ef4444;">
            <div style="padding: 16px;">
                <!-- Icon and Label -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #fee2e2; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 24px;"></i>
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: #ef4444; text-transform: uppercase; letter-spacing: 0.5px;">Urgent</span>
                </div>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    4
                </div>
                
                <!-- Description -->
                <div style="font-size: 14px; color: #374151; margin-bottom: 8px;">
                    Open Disputes
                </div>
                
                <!-- Trend -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-arrow-up" style="color: #ef4444; font-size: 12px;"></i>
                    <span style="font-size: 12px; font-weight: 500; color: #ef4444;">2 new today</span>
                </div>
            </div>
        </div>

        <!-- Active/Investigating Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative; flex: 1; min-width: 200px; border: 2px solid #fcf5c8;">
            <div style="padding: 16px;">
                <!-- Icon and Label -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #fef3c7; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-search" style="color: #f97316; font-size: 24px;"></i>
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: #f97316; text-transform: uppercase; letter-spacing: 0.5px;">Active</span>
                </div>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    8
                </div>
                
                <!-- Description -->
                <div style="font-size: 14px; color: #374151; margin-bottom: 8px;">
                    Investigating
                </div>
                
                <!-- Trend -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-clock" style="color: #6b7280; font-size: 12px;"></i>
                    <span style="font-size: 12px; font-weight: 500; color: #374151;">Avg 2.1 days</span>
                </div>
            </div>
        </div>

        <!-- Success/Resolved This Month Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative; flex: 1; min-width: 200px; border: 2px solid #10b981;">
            <div style="padding: 16px;">
                <!-- Icon and Label -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 24px;"></i>
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: #10b981; text-transform: uppercase; letter-spacing: 0.5px;">Success</span>
                </div>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    34
                </div>
                
                <!-- Description -->
                <div style="font-size: 14px; color: #374151; margin-bottom: 8px;">
                    Resolved This Month
                </div>
                
                <!-- Trend -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-arrow-down" style="color: #10b981; font-size: 12px;"></i>
                    <span style="font-size: 12px; font-weight: 500; color: #10b981;">↓ 12% from last month</span>
                </div>
            </div>
        </div>

        <!-- Performance/Avg Resolution Time Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative; flex: 1; min-width: 200px; border: 2px solid #3b82f6;">
            <div style="padding: 16px;">
                <!-- Icon and Label -->
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock" style="color: #3b82f6; font-size: 24px;"></i>
                    </div>
                    <span style="font-size: 12px; font-weight: 600; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.5px;">Performance</span>
                </div>
                
                <!-- Main Value -->
                <div style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    3.2
                </div>
                
                <!-- Description -->
                <div style="font-size: 14px; color: #374151; margin-bottom: 8px;">
                    Avg Resolution Time (days)
                </div>
                
                <!-- Trend -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-arrow-down" style="color: #3b82f6; font-size: 12px;"></i>
                    <span style="font-size: 12px; font-weight: 500; color: #3b82f6;">↓ 0.8 days faster</span>
                </div>
            </div>
        </div>
    </div>

    <!-- now create new row of table as per the ss -->
    
    <!-- Disputes Table Section -->
    <div style="margin-top: 24px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <!-- Status Tabs -->
        <div style="display: flex; align-items: center; gap: 8px; padding: 16px; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap;">
            <button class="status-tab active" data-status="open" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span>Open</span>
                <span style="background-color: #ef4444; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">4</span>
            </button>
            <button class="status-tab" data-status="investigating" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span>Investigating</span>
                <span style="background-color: #fbbf24; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">8</span>
            </button>
            <button class="status-tab" data-status="awaiting-seller" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span>Awaiting Seller Response</span>
                <span style="background-color: #6b7280; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">6</span>
            </button>
            <button class="status-tab" data-status="awaiting-buyer" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span>Awaiting Buyer Response</span>
                <span style="background-color: #6b7280; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">2</span>
            </button>
            <button class="status-tab" data-status="resolved" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span>Resolved</span>
                <span style="background-color: #10b981; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">34</span>
            </button>
            <button class="status-tab" data-status="escalated" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                <span>Escalated</span>
                <span style="background-color: #a855f7; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">1</span>
            </button>
        </div>

        <!-- Search and Filter Bar -->
        <div style="display: flex; align-items: center; gap: 12px; padding: 16px; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap;">
            <!-- Search Input -->
            <div style="position: relative; flex: 1; min-width: 200px;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;"></i>
                <input type="text" placeholder="Search disputes..." style="width: 100%; padding: 10px 12px 10px 36px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#d1d5db';">
            </div>
            
            <!-- All Types Dropdown -->
            <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option>All Types</option>
                <option>Quality Issue</option>
                <option>Non-delivery</option>
                <option>Wrong Item</option>
                <option>Compliance</option>
            </select>
            
            <!-- All Priority Dropdown -->
            <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option>All Priority</option>
                <option>High</option>
                <option>Medium</option>
                <option>Low</option>
            </select>
            
            <!-- Sort Dropdown -->
            <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                <option>Sort by: Age (Oldest First)</option>
                <option>Sort by: Age (Newest First)</option>
                <option>Sort by: Amount (Highest First)</option>
                <option>Sort by: Amount (Lowest First)</option>
            </select>
        </div>

        <!-- Disputes Table -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                <thead style="background-color: #ffffff !important;">
                    <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; width: 40px;">
                            <input type="checkbox" style="cursor: pointer;">
                        </th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">DISPUTE #</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">TYPE</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">BUYER</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">SELLER</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ORDER #</th>
                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ORDER AMOUNT</th>
                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">DISPUTED AMOUNT</th>
                        <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PLATFORM FEE AT STAKE</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">STATUS</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PRIORITY</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">AGE</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ASSIGNED ADMIN</th>
                        <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr class="dispute-row" data-status="investigating" style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 16px 12px;">
                            <input type="checkbox" style="cursor: pointer;">
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">DSP-2847</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Quality Issue</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">Sarah Johnson</span>
                                <span style="font-size: 12px; color: #6b7280;">Prime Member</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">TechGear Store</span>
                                <span style="font-size: 12px; color: #6b7280;">Business</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; color: #3b82f6; font-weight: 500;">ORD-8921</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">$124.99</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #ef4444; font-weight: 500;">$124.99</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #a855f7; font-weight: 500;">$18.75</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Investigating</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">High</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 12px; color: #ef4444; font-weight: 500; white-space: nowrap;">7 days</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">Y</div>
                                <span style="font-size: 14px; color: #111827;">You</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px; text-align: center;">
                            <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#9333ea';" onmouseout="this.style.backgroundColor='#a855f7';">Open</button>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr class="dispute-row" data-status="awaiting-seller" style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 16px 12px;">
                            <input type="checkbox" style="cursor: pointer;">
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">DSP-2846</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f97316; background-color: #fed7aa; border-radius: 25px;">Non-delivery</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">Michael Chen</span>
                                <span style="font-size: 12px; color: #6b7280;">Standard</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">Fashion Hub</span>
                                <span style="font-size: 12px; color: #6b7280;">Individual</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; color: #3b82f6; font-weight: 500;">ORD-8920</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">$89.50</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #ef4444; font-weight: 500;">$89.50</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #a855f7; font-weight: 500;">$13.43</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Awaiting Seller</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">High</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 12px; color: #ef4444; font-weight: 500; white-space: nowrap;">6 days</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">ES</div>
                                <span style="font-size: 14px; color: #111827;">Emma S.</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px; text-align: center;">
                            <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#9333ea';" onmouseout="this.style.backgroundColor='#a855f7';">Open</button>
                        </td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr class="dispute-row" data-status="investigating" style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 16px 12px;">
                            <input type="checkbox" style="cursor: pointer;">
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">DSP-2845</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Wrong Item</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">Emily Davis</span>
                                <span style="font-size: 12px; color: #6b7280;">Prime Member</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">HomeGoods Plus</span>
                                <span style="font-size: 12px; color: #6b7280;">Business</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; color: #3b82f6; font-weight: 500;">ORD-8919</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">$156.00</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #ef4444; font-weight: 500;">$156.00</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #a855f7; font-weight: 500;">$23.40</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Investigating</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Medium</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 12px; color: #374151; font-weight: 500; white-space: nowrap;">4 days</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">JK</div>
                                <span style="font-size: 14px; color: #111827;">James K.</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px; text-align: center;">
                            <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#9333ea';" onmouseout="this.style.backgroundColor='#a855f7';">Open</button>
                        </td>
                    </tr>
                    
                    <!-- Row 4 -->
                    <tr class="dispute-row" data-status="awaiting-buyer" style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 16px 12px;">
                            <input type="checkbox" style="cursor: pointer;">
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">DSP-2844</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #a855f7; background-color: #e9d5ff; border-radius: 25px;">Compliance</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">Robert Martinez</span>
                                <span style="font-size: 12px; color: #6b7280;">Standard</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span style="font-size: 14px; color: #111827; font-weight: 500;">ElectroMart</span>
                                <span style="font-size: 12px; color: #6b7280;">Business</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 14px; color: #3b82f6; font-weight: 500;">ORD-8918</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">$299.99</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #ef4444; font-weight: 500;">$299.99</span>
                        </td>
                        <td style="padding: 16px 12px; text-align: right;">
                            <span style="font-size: 14px; color: #a855f7; font-weight: 500;">$45.00</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Awaiting Buyer</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Low</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span style="font-size: 12px; color: #374151; font-weight: 500; white-space: nowrap;">3 days</span>
                        </td>
                        <td style="padding: 16px 12px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">Y</div>
                                <span style="font-size: 14px; color: #111827;">You</span>
                            </div>
                        </td>
                        <td style="padding: 16px 12px; text-align: center;">
                            <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#9333ea';" onmouseout="this.style.backgroundColor='#a855f7';">Open</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px;">
            <div class="pagination-info" style="font-size: 14px; color: #374151;">
                Showing <span style="font-weight: 600; color: #111827;">1-4</span> of <span style="font-weight: 600; color: #111827;">4</span> disputes
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 4px;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">
                    <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                </button>
                <button style="padding: 8px 12px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #a855f7; border: 1px solid #a855f7; border-radius: 6px; cursor: pointer;">1</button>
                <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 4px;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">
                    <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
                </button>
            </div>
        </div>
    </div>

 
    
    <!-- Three Cards Row -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap; margin-top: 24px;">
        <!-- Dispute Types Distribution Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-pie" style="color: #a855f7; font-size: 20px;"></i>
                    </div>
                    <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Dispute Types Distribution</h3>
                </div>
                
                <!-- Horizontal Bar Chart -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Quality Issues -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Quality Issues</span>
                            <span style="font-size: 14px; color: #111827; font-weight: 600;">35%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div style="width: 35%; height: 100%; background-color: #ef4444; border-radius: 4px;"></div>
                        </div>
                    </div>
                    
                    <!-- Non-delivery -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Non-delivery</span>
                            <span style="font-size: 14px; color: #111827; font-weight: 600;">28%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div style="width: 28%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                        </div>
                    </div>
                    
                    <!-- Wrong Item -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Wrong Item</span>
                            <span style="font-size: 14px; color: #111827; font-weight: 600;">22%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div style="width: 22%; height: 100%; background-color: #3b82f6; border-radius: 4px;"></div>
                        </div>
                    </div>
                    
                    <!-- Compliance -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Compliance</span>
                            <span style="font-size: 14px; color: #111827; font-weight: 600;">10%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div style="width: 10%; height: 100%; background-color: #a855f7; border-radius: 4px;"></div>
                        </div>
                    </div>
                    
                    <!-- Billing -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Billing</span>
                            <span style="font-size: 14px; color: #111827; font-weight: 600;">5%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                            <div style="width: 5%; height: 100%; background-color: #ec4899; border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Outcomes Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trophy" style="color: #a855f7; font-size: 20px;"></i>
                    </div>
                    <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Resolution Outcomes</h3>
                </div>
                
                <!-- Outcomes List -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Buyer Favored -->
                    <div>
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-check-circle" style="color: #10b981; font-size: 18px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 600;">Buyer Favored</span>
                                    <span style="font-size: 14px; color: #111827; font-weight: 600;">58%</span>
                                </div>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">Full refunds issued</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seller Favored -->
                    <div>
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-store" style="color: #3b82f6; font-size: 18px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 600;">Seller Favored</span>
                                    <span style="font-size: 14px; color: #111827; font-weight: 600;">23%</span>
                                </div>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">Claims rejected</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Split Decision -->
                    <div>
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background-color: #fed7aa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-handshake" style="color: #f97316; font-size: 18px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 600;">Split Decision</span>
                                    <span style="font-size: 14px; color: #111827; font-weight: 600;">19%</span>
                                </div>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">Partial resolutions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Impact Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dollar-sign" style="color: #10b981; font-size: 20px;"></i>
                    </div>
                    <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Financial Impact</h3>
                </div>
                
                <!-- Financial Metrics -->
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <!-- Total Disputed Amount -->
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 8px;">$8,456.32</div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="color: #ef4444; font-size: 12px;"></i>
                            <span style="font-size: 12px; font-weight: 500; color: #ef4444;">12% from last month</span>
                        </div>
                        <p style="font-size: 12px; color: #6b7280; margin: 8px 0 0 0;">Total Disputed Amount</p>
                    </div>
                    
                    <!-- Platform Fees at Risk -->
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #a855f7; margin-bottom: 8px;">$1,268.45</div>
                        <p style="font-size: 12px; color: #6b7280; margin: 0;">15% of disputed amounts</p>
                        <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">Platform Fees at Risk</p>
                    </div>
                    
                    <!-- Refunded This Month -->
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #10b981; margin-bottom: 8px;">$4,892.10</div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-down" style="color: #10b981; font-size: 12px;"></i>
                            <span style="font-size: 12px; font-weight: 500; color: #10b981;">8% from last month</span>
                        </div>
                        <p style="font-size: 12px; color: #6b7280; margin: 8px 0 0 0;">Refunded This Month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    
    <!-- Admin Team Performance and Recent Activity Log Row -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap; margin-top: 24px;">
        <!-- Admin Team Performance Section -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 2; min-width: 600px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users" style="color: #a855f7; font-size: 20px;"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Admin Team Performance</h3>
                </div>
                
                <!-- Performance Table -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff;">ADMIN</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff;">ACTIVE CASES</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff;">RESOLVED THIS MONTH</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff;">AVG RESOLUTION TIME</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff;">BUYER SATISFACTION</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1: You -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">Y</div>
                                        <div>
                                            <div style="font-size: 14px; color: #111827; font-weight: 600;">You</div>
                                            <div style="font-size: 12px; color: #6b7280;">Senior Moderator</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">2</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">12</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">2.8 days</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 92%; height: 100%; background-color: #10b981; border-radius: 4px;"></div>
                                        </div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 600; min-width: 35px;">92%</span>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Active</span>
                                </td>
                            </tr>
                            
                            <!-- Row 2: Emma S. -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">ES</div>
                                        <div>
                                            <div style="font-size: 14px; color: #111827; font-weight: 600;">Emma S.</div>
                                            <div style="font-size: 12px; color: #6b7280;">Moderator</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">1</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">9</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">3.4 days</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 88%; height: 100%; background-color: #10b981; border-radius: 4px;"></div>
                                        </div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 600; min-width: 35px;">88%</span>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Active</span>
                                </td>
                            </tr>
                            
                            <!-- Row 3: James K. -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">JK</div>
                                        <div>
                                            <div style="font-size: 14px; color: #111827; font-weight: 600;">James K.</div>
                                            <div style="font-size: 12px; color: #6b7280;">Moderator</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">1</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">8</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">3.1 days</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 85%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                                        </div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 600; min-width: 35px;">85%</span>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Active</span>
                                </td>
                            </tr>
                            
                            <!-- Row 4: Sarah T. -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #6b7280; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">ST</div>
                                        <div>
                                            <div style="font-size: 14px; color: #111827; font-weight: 600;">Sarah T.</div>
                                            <div style="font-size: 12px; color: #6b7280;">Junior Moderator</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">0</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">5</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="font-size: 14px; color: #111827; font-weight: 500;">4.2 days</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="flex: 1; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                            <div style="width: 79%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                                        </div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 600; min-width: 35px;">79%</span>
                                    </div>
                                </td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Away</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity Log Section -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 400px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="color: #a855f7; font-size: 20px;"></i>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Recent Activity Log</h3>
                    </div>
                    <a href="#" style="font-size: 14px; font-weight: 500; color: #a855f7; text-decoration: none;">View All</a>
                </div>
                
                <!-- Activity List -->
                <div style="display: flex; flex-direction: column; gap: 0;">
                    <!-- Activity 1 -->
                    <div style="padding: 16px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #d1fae5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-check-circle" style="color: #10b981; font-size: 16px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; color: #111827; font-weight: 600; margin-bottom: 4px;">Dispute DSP-2841 Resolved</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Full refund issued to buyer. Platform fee retained.</div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: #6b7280;">Resolved by:</span>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">Y</div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 500;">You</span>
                                    </div>
                                    <span style="font-size: 12px; color: #6b7280;">2 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity 2 -->
                    <div style="padding: 16px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #fed7aa; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-comment" style="color: #f97316; font-size: 16px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; color: #111827; font-weight: 600; margin-bottom: 4px;">New Message in DSP-2847</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Seller responded with shipping insurance documentation.</div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: #6b7280;">From:</span>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">TS</div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 500;">TechGear Store</span>
                                    </div>
                                    <span style="font-size: 12px; color: #6b7280;">3 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity 3 -->
                    <div style="padding: 16px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #fee2e2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 16px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; color: #111827; font-weight: 600; margin-bottom: 4px;">New High Priority Dispute</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">DSP-2846 created for non-delivery claim worth $89.50</div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: #6b7280;">Assigned to:</span>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">ES</div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 500;">Emma S.</span>
                                    </div>
                                    <span style="font-size: 12px; color: #6b7280;">5 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity 4 -->
                    <div style="padding: 16px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-file-upload" style="color: #3b82f6; font-size: 16px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; color: #111827; font-weight: 600; margin-bottom: 4px;">Evidence Uploaded to DSP-2845</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Buyer uploaded 3 photos showing incorrect item received.</div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: #6b7280;">From:</span>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">ED</div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 500;">Emily Davis</span>
                                    </div>
                                    <span style="font-size: 12px; color: #6b7280;">6 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activity 5 -->
                    <div style="padding: 16px 0;">
                        <div style="display: flex; align-items: start; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-arrow-up" style="color: #a855f7; font-size: 16px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; color: #111827; font-weight: 600; margin-bottom: 4px;">Dispute DSP-2839 Escalated</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Escalated to senior management due to policy violation concerns.</div>
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-size: 12px; color: #6b7280;">Escalated by:</span>
                                        <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">JK</div>
                                        <span style="font-size: 12px; color: #111827; font-weight: 500;">James K.</span>
                                    </div>
                                    <span style="font-size: 12px; color: #6b7280;">8 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- create new ui layout as per the ss -->
    
    <!-- Automation Rules Section -->
    <div style="margin-top: 24px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-cog" style="color: #a855f7; font-size: 20px;"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Automation Rules</h3>
                </div>
                <button style="padding: 10px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#9333ea';" onmouseout="this.style.backgroundColor='#a855f7';">
                    <i class="fas fa-plus" style="font-size: 12px;"></i>
                    <span>Create Rule</span>
                </button>
            </div>
            
            <!-- Rules Grid (2x2) -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <!-- Rule 1: Auto-Escalate Old Disputes -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; position: relative; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: start; justify-content: space-between;">
                        <div style="display: flex; align-items: start; gap: 12px; flex: 1;">
                            <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-clock" style="color: #10b981; font-size: 24px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 4px;">Auto-Escalate Old Disputes</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Active</div>
                                <p style="font-size: 14px; color: #374151; margin: 0 0 8px 0;">Automatically escalate disputes that remain unresolved for more than 10 days.</p>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Triggered 3 times this month.</div>
                                <a href="#" style="font-size: 14px; color: #3b82f6; text-decoration: none; font-weight: 500;">Edit</a>
                            </div>
                        </div>
                        <!-- Toggle Switch -->
                        <label style="position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; margin-left: 12px;">
                            <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; border-radius: 24px; transition: 0.3s;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #ffffff; border-radius: 50%; transition: 0.3s; transform: translateX(20px);"></span>
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Rule 2: Smart Assignment -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; position: relative; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: start; justify-content: space-between;">
                        <div style="display: flex; align-items: start; gap: 12px; flex: 1;">
                            <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-users" style="color: #3b82f6; font-size: 24px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 4px;">Smart Assignment</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Active</div>
                                <p style="font-size: 14px; color: #374151; margin: 0 0 8px 0;">Assign new disputes to admins based on current workload and expertise.</p>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Triggered 12 times this month.</div>
                                <a href="#" style="font-size: 14px; color: #3b82f6; text-decoration: none; font-weight: 500;">Edit</a>
                            </div>
                        </div>
                        <!-- Toggle Switch -->
                        <label style="position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; margin-left: 12px;">
                            <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; border-radius: 24px; transition: 0.3s;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #ffffff; border-radius: 50%; transition: 0.3s; transform: translateX(20px);"></span>
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Rule 3: Response Reminder -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; position: relative; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: start; justify-content: space-between;">
                        <div style="display: flex; align-items: start; gap: 12px; flex: 1;">
                            <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #fef3c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-bell" style="color: #f59e0b; font-size: 24px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 4px;">Response Reminder</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Active</div>
                                <p style="font-size: 14px; color: #374151; margin: 0 0 8px 0;">Send reminders to sellers who haven't responded within 48 hours.</p>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Triggered 8 times this month.</div>
                                <a href="#" style="font-size: 14px; color: #3b82f6; text-decoration: none; font-weight: 500;">Edit</a>
                            </div>
                        </div>
                        <!-- Toggle Switch -->
                        <label style="position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; margin-left: 12px;">
                            <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; border-radius: 24px; transition: 0.3s;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #ffffff; border-radius: 50%; transition: 0.3s; transform: translateX(20px);"></span>
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Rule 4: High-Value Alert -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; position: relative; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: start; justify-content: space-between;">
                        <div style="display: flex; align-items: start; gap: 12px; flex: 1;">
                            <div style="width: 48px; height: 48px; border-radius: 8px; background-color: #fee2e2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-flag" style="color: #ef4444; font-size: 24px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 4px;">High-Value Alert</div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Active</div>
                                <p style="font-size: 14px; color: #374151; margin: 0 0 8px 0;">Flag disputes over $500 as high priority and notify senior admins.</p>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Triggered 2 times this month.</div>
                                <a href="#" style="font-size: 14px; color: #3b82f6; text-decoration: none; font-weight: 500;">Edit</a>
                            </div>
                        </div>
                        <!-- Toggle Switch -->
                        <label style="position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; margin-left: 12px;">
                            <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #10b981; border-radius: 24px; transition: 0.3s;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #ffffff; border-radius: 50%; transition: 0.3s; transform: translateX(20px);"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Status Tab Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const statusTabs = document.querySelectorAll('.status-tab');
        const disputeRows = document.querySelectorAll('.dispute-row');
        
        function updatePagination() {
            const totalVisible = Array.from(disputeRows).filter(row => {
                const style = window.getComputedStyle(row);
                return style.display !== 'none';
            }).length;
            
            const paginationInfo = document.querySelector('.pagination-info');
            if (paginationInfo && totalVisible > 0) {
                paginationInfo.innerHTML = `Showing <span style="font-weight: 600; color: #111827;">1-${totalVisible}</span> of <span style="font-weight: 600; color: #111827;">${totalVisible}</span> disputes`;
            } else if (paginationInfo) {
                paginationInfo.innerHTML = `Showing <span style="font-weight: 600; color: #111827;">0</span> of <span style="font-weight: 600; color: #111827;">0</span> disputes`;
            }
        }
        
        statusTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                statusTabs.forEach(t => {
                    t.style.backgroundColor = '#ffffff';
                    t.style.color = '#374151';
                    t.style.border = '1px solid #d1d5db';
                });
                
                // Add active class to clicked tab
                this.style.backgroundColor = '#a855f7';
                this.style.color = '#ffffff';
                this.style.border = 'none';
                
                // Filter table rows based on status
                const status = this.getAttribute('data-status');
                
                disputeRows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    
                    if (status === 'open') {
                        // Show all non-resolved, non-escalated disputes
                        if (rowStatus !== 'resolved' && rowStatus !== 'escalated') {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Show only matching status
                        if (rowStatus === status) {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
                
                // Update pagination
                updatePagination();
            });
        });
        
        // Initialize: Show all rows for "Open" tab (default active)
        disputeRows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if (rowStatus !== 'resolved' && rowStatus !== 'escalated') {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Initialize pagination
        updatePagination();
    });
</script>
@endsection

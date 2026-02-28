@extends('layouts.app')
@section('title', 'Platform Revenue')

@section('content')
<div id="financial-report-content" style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Top Control Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <!-- Date Range Buttons -->
        <div style="display: flex; align-items: center; gap: 8px;">
            <button id="date-today" class="date-range-btn" data-range="today" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;">
                Today
            </button>
            <button id="date-7d" class="date-range-btn" data-range="7d" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;">
                7D
            </button>
            <button id="date-30d" class="date-range-btn active" data-range="30d" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #ecab39; border: 1px solid #ecab39; border-radius: 8px; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                30D
            </button>
            <button id="date-90d" class="date-range-btn" data-range="90d" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;">
                90D
            </button>
            <button id="date-ytd" class="date-range-btn" data-range="ytd" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;">
                YTD
            </button>
            <button id="date-custom" class="date-range-btn" data-range="custom" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; gap: 4px;">
                <span>Custom</span>
                <i class="fas fa-calendar" style="font-size: 12px;"></i>
            </button>
        </div>
        
        <!-- Export Button -->
        <div style="position: relative;">
            <button id="export-report-btn" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #ecab39; border: 1px solid #ecab39; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-download" style="font-size: 14px;"></i>
                <span>Export Financial Report</span>
                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>

    <!-- Metric Cards Row -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap;">
        <!-- Gross Merchandise Value Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <!-- Gradient Border Top -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div style="padding: 16px;">
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    GROSS MERCHANDISE VALUE
                </h3>
                
                <!-- Main Value -->
                <div id="gmv-value" style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    $347,820
                </div>
                
                <!-- Change Indicator -->
                <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 8px;">
                    <i class="fas fa-arrow-up" style="color: #16a34a; font-size: 14px;"></i>
                    <span id="gmv-change" style="font-size: 14px; font-weight: 500; color: #16a34a;">+18.4%</span>
                </div>
                
                <!-- Description -->
                <p style="font-size: 12px; color: #4b5563; margin-top: 8px;">Total value of all orders on platform</p>
            </div>
        </div>

        <!-- Total Platform Revenue Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <!-- Gradient Border Top -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div style="padding: 16px;">
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    TOTAL PLATFORM REVENUE
                </h3>
                
                <!-- Main Value -->
                <div id="platform-revenue-value" style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    $48,295
                </div>
                
                <!-- Change Indicator -->
                <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 8px;">
                    <i class="fas fa-arrow-up" style="color: #16a34a; font-size: 14px;"></i>
                    <span id="platform-revenue-change" style="font-size: 14px; font-weight: 500; color: #16a34a;">+22.1%</span>
                </div>
                
                <!-- Description -->
                <p style="font-size: 12px; color: #4b5563; margin-top: 8px;">All fees + subscriptions + ads</p>
            </div>
        </div>

        <!-- Referral Fee Income Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <!-- Gradient Border Top -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div style="padding: 16px;">
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    REFERRAL FEE INCOME
                </h3>
                
                <!-- Main Value -->
                <div id="referral-fee-value" style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    $31,130
                </div>
                
                <!-- Description -->
                <p id="referral-fee-desc" style="font-size: 12px; color: #4b5563; margin-top: 8px;">Commission on orders (avg 8.9%)</p>
            </div>
        </div>

        <!-- FBS Fee Income Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <!-- Gradient Border Top -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div style="padding: 16px;">
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    FBS FEE INCOME
                </h3>
                
                <!-- Main Value -->
                <div id="fbs-fee-value" style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    $7,840
                </div>
                
                <!-- Description -->
                <p style="font-size: 12px; color: #4b5563; margin-top: 8px;">Fulfillment fees collected</p>
            </div>
        </div>

        <!-- AD Revenue Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; position: relative; flex: 1; min-width: 200px;">
            <!-- Gradient Border Top -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #e5a238, #a855f7);"></div>
            <div style="padding: 16px;">
                <!-- Title -->
                <h3 style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                    AD REVENUE
                </h3>
                
                <!-- Main Value -->
                <div id="ad-revenue-value" style="font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; word-break: break-word;">
                    $5,220
                </div>
                
                <!-- Description -->
                <p style="font-size: 12px; color: #4b5563; margin-top: 8px;">Sponsored products + brands + display</p>
            </div>
        </div>
    </div>

    <!-- Platform Revenue Over Time Chart -->
    <div style="margin-top: 24px;">
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; padding: 24px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Platform Revenue Over Time</h2>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <!-- Time Granularity Buttons -->
                    <div style="display: flex; align-items: center; gap: 4px; background-color: #f3f4f6; border-radius: 8px; padding: 4px;">
                        <button id="daily-btn" style="padding: 6px 12px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: transparent; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            Daily
                        </button>
                        <button id="weekly-btn" style="padding: 6px 12px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: transparent; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            Weekly
                        </button>
                        <button id="monthly-btn" style="padding: 6px 12px; font-size: 14px; font-weight: 500; color: #111827; background-color: #ffffff; border: none; border-radius: 6px; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                            Monthly
                        </button>
                    </div>
                    <!-- Show Prior Period Checkbox -->
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" style="width: 16px; height: 16px; cursor: pointer;">
                        <span style="font-size: 14px; color: #374151;">Show prior period</span>
                    </label>
                </div>
            </div>
            
            <!-- Chart Container -->
            <div style="position: relative; height: 400px;">
                <canvas id="platformRevenueChart"></canvas>
            </div>
        </div>
    </div>

 

    <!-- Three Cards Row: Take Rate Analysis, Revenue by Seller Tier, Fee Collection Status -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; margin-top: 24px; flex-wrap: wrap;">
        <!-- Take Rate Analysis Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 24px 0;">Take Rate Analysis</h2>
                
                <!-- Chart Container -->
                <div style="position: relative; height: 200px; margin-bottom: 24px;">
                    <canvas id="takeRateChart"></canvas>
                </div>
                
                <!-- Current Take Rate and Target -->
                <div style="display: flex; align-items: baseline; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Current Take Rate</div>
                        <div style="font-size: 32px; font-weight: 700; color: #f97316;">13.9%</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Target</div>
                        <div style="font-size: 18px; font-weight: 600; color: #16a34a;">15%</div>
                    </div>
                </div>
                
                <!-- Category Breakdown -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-size: 14px; color: #374151;">Pre-Rolls</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: #374151;">28%</span>
                            <span style="font-size: 14px; color: #6b7280;">$12,400</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-size: 14px; color: #374151;">Vapes</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: #374151;">22%</span>
                            <span style="font-size: 14px; color: #6b7280;">$9,700</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-size: 14px; color: #374151;">Flower</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: #374151;">18%</span>
                            <span style="font-size: 14px; color: #6b7280;">$7,900</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-size: 14px; color: #374151;">Edibles</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: #374151;">15%</span>
                            <span style="font-size: 14px; color: #6b7280;">$6,600</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-size: 14px; color: #374151;">Accessories</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: #374151;">10%</span>
                            <span style="font-size: 14px; color: #6b7280;">$4,400</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                        <span style="font-size: 14px; color: #374151;">Other</span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 14px; color: #374151;">7%</span>
                            <span style="font-size: 14px; color: #6b7280;">$3,100</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Seller Tier Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 24px 0;">Revenue by Seller Tier</h2>
                
                <!-- Chart Container -->
                <div style="position: relative; height: 200px; margin-bottom: 24px;">
                    <canvas id="sellerTierChart"></canvas>
                </div>
                
                <!-- Average Revenue per Seller -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-size: 14px; color: #374151;">Avg. Revenue per Enterprise Seller</span>
                        <span style="font-size: 14px; color: #6b7280;">$560</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                        <span style="font-size: 14px; color: #374151;">Avg. Revenue per Professional Seller</span>
                        <span style="font-size: 14px; color: #6b7280;">$83</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Collection Status Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 24px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 24px 0;">Fee Collection Status</h2>
                
                <!-- Donut Chart Container -->
                <div style="position: relative; height: 200px; margin-bottom: 24px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="feeCollectionChart"></canvas>
                </div>
                
                <!-- Fee Status Breakdown -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #16a34a;"></div>
                            <span style="font-size: 14px; color: #374151;">Collected</span>
                        </div>
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">$1,045.35</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #fbbf24;"></div>
                            <span style="font-size: 14px; color: #374151;">Pending</span>
                        </div>
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">$245.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444;"></div>
                            <span style="font-size: 14px; color: #374151;">Failed</span>
                        </div>
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">$95.20</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                        <span style="font-size: 14px; color: #374151; font-weight: 600;">Total Fees</span>
                        <span style="font-size: 16px; color: #111827; font-weight: 700;">$1,385.55</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Platform Transactions Table -->
    <div style="margin-top: 24px;">
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
            <div style="padding: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Platform Transactions</h2>
                    <!-- Filter Tabs -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button class="filter-tab active" data-filter="all" style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #ecab39; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            All
                        </button>
                        <button class="filter-tab" data-filter="Referral Fee" style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            Referral Fee
                        </button>
                        <button class="filter-tab" data-filter="FBS Fee" style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            FBS Fee
                        </button>
                        <button class="filter-tab" data-filter="Subscription" style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            Subscription
                        </button>
                        <button class="filter-tab" data-filter="Advertising" style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            Advertising
                        </button>
                    </div>
                </div>
                
                <!-- Table -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                        <thead style="background-color: #ffffff !important;">
                            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Transaction ID</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Date</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Seller</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Type</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Amount</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Fee</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="transaction-row" data-type="Referral Fee" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001234</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-15</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">CloudNine Hemp Co.</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #f9e5be; color: #92400e;">Referral Fee</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$1,240.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$148.80</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Collected</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="FBS Fee" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001235</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-15</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">VapeWorks Distribution</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #bedaf9; color: #1e40af;">FBS Fee</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$890.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$89.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fef3c7; color: #d97706;">Pending</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="Subscription" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001236</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-14</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Premium Tobacco Supply</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #dfefdf; color: #166534;">Subscription</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$2,500.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$250.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Collected</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="Advertising" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001237</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-14</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Green Leaf Distributors</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #d2cbf6; color: #6b21a8;">Advertising</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$1,850.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$185.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fee2e2; color: #ef4444;">Failed</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="Referral Fee" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001238</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-13</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Curevana Wholesale</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #f9e5be; color: #92400e;">Referral Fee</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$2,100.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$252.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Collected</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="FBS Fee" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001239</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-13</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">VapeCo LLC</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #bedaf9; color: #1e40af;">FBS Fee</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$650.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$65.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fef3c7; color: #d97706;">Pending</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="Subscription" style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001240</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-12</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Smoke Haven</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #dfefdf; color: #166534;">Subscription</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$1,200.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$120.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Collected</span>
                                </td>
                            </tr>
                            <tr class="transaction-row" data-type="Advertising">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">TXN-2026-001241</a>
                                </td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">2026-01-12</td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Green Distributors</a>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 25px; font-size: 12px; font-weight: 500; background-color: #d2cbf6; color: #6b21a8;">Advertising</span>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$980.00</td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 500;">$98.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fee2e2; color: #ef4444;">Failed</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 14px; color: #6b7280;">
                        Showing <span style="font-weight: 600; color: #111827;">1</span> to <span style="font-weight: 600; color: #111827;">7</span> of <span style="font-weight: 600; color: #111827;">127</span> results
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" disabled>
                            Previous
                        </button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #ecab39; border: 1px solid #ecab39; border-radius: 6px; cursor: pointer;">1</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">2</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">3</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seller Payouts This Month Table -->
    <div style="margin-top: 24px;">
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
            <div style="padding: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Seller Payouts This Month</h2>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #efbc59; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                        Process All Pending
                    </button>
                </div>
                
                <!-- Table -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                        <thead style="background-color: #ffffff !important;">
                            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Seller Name</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Account Balance</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Last Payout Date</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Last Payout</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Next Scheduled</th>
                                <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Amount Due</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Status</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1: CloudNine Hemp Co. -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">CloudNine Hemp Co.</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$18,450.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 28, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$15,240.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 11, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$18,450.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 2: VapeWorks Distribution -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">VapeWorks Distribution</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$14,320.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 30, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$12,680.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 13, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$14,320.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 3: Premium Tobacco Supply -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Premium Tobacco Supply</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$11,850.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 25, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$10,450.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 08, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$11,850.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dbeafe; color: #2563eb;">Processing</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #f97316; font-size: 14px; text-decoration: none; margin-right: 16px;">Hold</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 4: Green Leaf Distributors -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Green Leaf Distributors</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$9,240.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 01, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$8,240.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 15, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$9,240.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 5: Curevana Wholesale (On Hold - Yellow Background) -->
                            <tr style="border-bottom: 1px solid #f3f4f6; background-color: #fef9c3;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Curevana Wholesale</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$7,680.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 20, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$6,840.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 05, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$7,680.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fed7aa; color: #ea580c;">On Hold</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 6: Elite Vape Wholesale -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Elite Vape Wholesale</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$6,420.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 28, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$5,890.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 11, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$6,420.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 7: Smoke Solutions Inc -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Smoke Solutions Inc</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$5,120.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 02, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$4,680.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 16, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$5,120.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 8: Herbal Wellness Co -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Herbal Wellness Co</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$4,850.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 26, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$4,120.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 09, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$4,850.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 9: Delta Distributors -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">Delta Distributors</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$3,940.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 31, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$3,420.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 14, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$3,940.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #dcfce7; color: #16a34a;">Scheduled</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                            
                            <!-- Row 10: VapeCo LLC (On Hold - Yellow Background) -->
                            <tr style="border-bottom: 1px solid #f3f4f6; background-color: #fef9c3;">
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none;">VapeCo LLC</a>
                                </td>
                                <td style="padding: 16px 12px; text-align: right; color: #111827; font-size: 14px; font-weight: 700;">$2,840.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Jan 18, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #374151; font-size: 14px;">$2,120.00</td>
                                <td style="padding: 16px 12px; color: #374151; font-size: 14px;">Feb 01, 2026</td>
                                <td style="padding: 16px 12px; text-align: right; color: #16a34a; font-size: 14px; font-weight: 700;">$2,840.00</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; background-color: #fed7aa; color: #ea580c;">On Hold</span>
                                </td>
                                <td style="padding: 16px 12px;">
                                    <a href="#" style="color: #3b82f6; font-size: 14px; text-decoration: none; margin-right: 16px;">Process Now</a>
                                    <a href="#" style="color: #6b7280; font-size: 14px; text-decoration: none;">View History</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- View All Payouts Link -->
                <div style="text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                    <a href="#" style="color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        View All Payouts
                        <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- html2canvas Library for Export -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script type="text/javascript">
    // Data for different time granularities
    const chartData = {
        monthly: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            referralFees: [31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 0, 0],
            fbsFees: [7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 0, 0],
            subscriptions: [3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 0, 0],
            advertising: [5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 0, 0],
            totalRevenue: [47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 0, 0]
        },
        weekly: {
            labels: ['W1', 'W2', 'W3', 'W4', 'W5', 'W6', 'W7', 'W8', 'W9', 'W10', 'W11', 'W12'],
            referralFees: [7750, 7750, 7750, 7750, 7750, 7750, 7750, 7750, 7750, 7750, 0, 0],
            fbsFees: [1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 0, 0],
            subscriptions: [875, 875, 875, 875, 875, 875, 875, 875, 875, 875, 0, 0],
            advertising: [1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 0, 0],
            totalRevenue: [11875, 11875, 11875, 11875, 11875, 11875, 11875, 11875, 11875, 11875, 0, 0]
        },
        daily: {
            labels: ['D1', 'D2', 'D3', 'D4', 'D5', 'D6', 'D7', 'D8', 'D9', 'D10', 'D11', 'D12'],
            referralFees: [1107, 1107, 1107, 1107, 1107, 1107, 1107, 1107, 1107, 1107, 0, 0],
            fbsFees: [279, 279, 279, 279, 279, 279, 279, 279, 279, 279, 0, 0],
            subscriptions: [125, 125, 125, 125, 125, 125, 125, 125, 125, 125, 0, 0],
            advertising: [186, 186, 186, 186, 186, 186, 186, 186, 186, 186, 0, 0],
            totalRevenue: [1696, 1696, 1696, 1696, 1696, 1696, 1696, 1696, 1696, 1696, 0, 0]
        }
    };

    // Platform Revenue Over Time Chart (Stacked Area with Total Revenue Line)
    const ctx = document.getElementById('platformRevenueChart').getContext('2d');
    let platformRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.monthly.labels,
            datasets: [
                {
                    label: 'Referral Fees',
                    data: chartData.monthly.referralFees,
                    backgroundColor: 'rgba(249, 229, 190, 0.5)',
                    borderColor: 'rgba(249, 229, 190, 0.8)',
                    borderWidth: 0,
                    fill: true,
                    stack: 'Stack 0',
                    order: 4
                },
                {
                    label: 'FBS Fees',
                    data: chartData.monthly.fbsFees,
                    backgroundColor: 'rgba(190, 218, 249, 0.5)',
                    borderColor: 'rgba(190, 218, 249, 0.8)',
                    borderWidth: 0,
                    fill: true,
                    stack: 'Stack 0',
                    order: 3
                },
                {
                    label: 'Subscriptions',
                    data: chartData.monthly.subscriptions,
                    backgroundColor: 'rgba(223, 239, 223, 0.5)',
                    borderColor: 'rgba(223, 239, 223, 0.8)',
                    borderWidth: 0,
                    fill: true,
                    stack: 'Stack 0',
                    order: 2
                },
                {
                    label: 'Advertising',
                    data: chartData.monthly.advertising,
                    backgroundColor: 'rgba(210, 203, 246, 0.5)',
                    borderColor: 'rgba(210, 203, 246, 0.8)',
                    borderWidth: 0,
                    fill: true,
                    stack: 'Stack 0',
                    order: 1
                },
                {
                    label: 'Total Revenue',
                    data: chartData.monthly.totalRevenue,
                    backgroundColor: 'transparent',
                    borderColor: '#111827',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    order: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        },
                        generateLabels: function(chart) {
                            const original = Chart.defaults.plugins.legend.labels.generateLabels;
                            const labels = original.call(this, chart);
                            
                            // Customize legend items
                            labels.forEach(label => {
                                if (label.text === 'Total Revenue') {
                                    label.borderDash = [5, 5];
                                    label.borderColor = '#111827';
                                    label.fillStyle = 'transparent';
                                }
                            });
                            
                            return labels;
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
                                label += '$' + context.parsed.y.toLocaleString();
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
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
                    stacked: true,
                    beginAtZero: true,
                    max: 40000,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000) + 'k';
                        },
                        stepSize: 10000,
                        font: {
                            size: 12
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#e5e7eb'
                    }
                }
            }
        }
    });

    // Function to update chart based on selected time granularity
    function updateChart(granularity) {
        const data = chartData[granularity];
        
        platformRevenueChart.data.labels = data.labels;
        platformRevenueChart.data.datasets[0].data = data.referralFees;
        platformRevenueChart.data.datasets[1].data = data.fbsFees;
        platformRevenueChart.data.datasets[2].data = data.subscriptions;
        platformRevenueChart.data.datasets[3].data = data.advertising;
        platformRevenueChart.data.datasets[4].data = data.totalRevenue;
        
        platformRevenueChart.update();
    }

    // Function to update button styles
    function updateButtonStyles(activeBtn) {
        const buttons = ['daily-btn', 'weekly-btn', 'monthly-btn'];
        buttons.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btnId === activeBtn) {
                btn.style.color = '#111827';
                btn.style.backgroundColor = '#ffffff';
                btn.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
            } else {
                btn.style.color = '#6b7280';
                btn.style.backgroundColor = 'transparent';
                btn.style.boxShadow = 'none';
            }
        });
    }

    // Event listeners for time granularity buttons
    document.getElementById('daily-btn').addEventListener('click', function() {
        updateChart('daily');
        updateButtonStyles('daily-btn');
    });

    document.getElementById('weekly-btn').addEventListener('click', function() {
        updateChart('weekly');
        updateButtonStyles('weekly-btn');
    });

    document.getElementById('monthly-btn').addEventListener('click', function() {
        updateChart('monthly');
        updateButtonStyles('monthly-btn');
    });

    // Take Rate Analysis Chart
    const takeRateCtx = document.getElementById('takeRateChart').getContext('2d');
    const takeRateChart = new Chart(takeRateCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            datasets: [
                {
                    label: 'Current Take Rate',
                    data: [12.5, 13.0, 12.8, 13.2, 13.5, 13.8, 14.0, 13.9, 13.7, 13.9],
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Target',
                    data: [15, 15, 15, 15, 15, 15, 15, 15, 15, 15],
                    borderColor: '#16a34a',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    pointRadius: 0
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
                    intersect: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        color: '#6b7280'
                    }
                },
                y: {
                    min: 10,
                    max: 20,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        },
                        stepSize: 2,
                        font: {
                            size: 11
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#e5e7eb'
                    }
                }
            }
        }
    });

    // Revenue by Seller Tier Chart (Horizontal Bar)
    const sellerTierCtx = document.getElementById('sellerTierChart').getContext('2d');
    const sellerTierChart = new Chart(sellerTierCtx, {
        type: 'bar',
        data: {
            labels: ['Enterprise (50)', 'Professional (180)', 'Standard (420)'],
            datasets: [{
                label: 'Revenue',
                data: [28000, 15000, 5000],
                backgroundColor: [
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)'
                ],
                borderColor: [
                    '#f97316',
                    '#3b82f6',
                    '#10b981'
                ],
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.x.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 30000,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000) + 'k';
                        },
                        font: {
                            size: 11
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#e5e7eb'
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        color: '#6b7280'
                    }
                }
            }
        }
    });

    // Fee Collection Status Donut Chart
    const feeCollectionCtx = document.getElementById('feeCollectionChart').getContext('2d');
    const feeCollectionChart = new Chart(feeCollectionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Collected', 'Pending', 'Failed'],
            datasets: [{
                data: [78, 17, 5],
                backgroundColor: [
                    '#16a34a',
                    '#fbbf24',
                    '#ef4444'
                ],
                borderColor: '#ffffff',
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
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });

    // Table Filter Functionality
    document.querySelectorAll('.filter-tab').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button styles
            document.querySelectorAll('.filter-tab').forEach(btn => {
                btn.style.color = '#6b7280';
                btn.style.backgroundColor = '#f3f4f6';
            });
            this.style.color = '#111827';
            this.style.backgroundColor = '#ecab39';
            
            // Filter table rows
            const rows = document.querySelectorAll('.transaction-row');
            rows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-type') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Date Range Data for all sections
    const dateRangeData = {
        today: {
            gmv: 12450,
            gmvChange: '+5.2%',
            platformRevenue: 1680,
            platformRevenueChange: '+8.1%',
            referralFee: 1107,
            referralFeeDesc: 'Commission on orders (avg 8.9%)',
            fbsFee: 279,
            adRevenue: 186,
            chartData: {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    referralFees: [1107, 1107, 1107, 1107, 1107, 1107, 1107, 1107, 1107, 1107, 0, 0],
                    fbsFees: [279, 279, 279, 279, 279, 279, 279, 279, 279, 279, 0, 0],
                    subscriptions: [125, 125, 125, 125, 125, 125, 125, 125, 125, 125, 0, 0],
                    advertising: [186, 186, 186, 186, 186, 186, 186, 186, 186, 186, 0, 0],
                    totalRevenue: [1696, 1696, 1696, 1696, 1696, 1696, 1696, 1696, 1696, 1696, 0, 0]
                }
            }
        },
        '7d': {
            gmv: 87560,
            gmvChange: '+12.3%',
            platformRevenue: 12180,
            platformRevenueChange: '+15.4%',
            referralFee: 7750,
            referralFeeDesc: 'Commission on orders (avg 8.9%)',
            fbsFee: 1950,
            adRevenue: 1300,
            chartData: {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    referralFees: [7750, 7750, 7750, 7750, 7750, 7750, 7750, 7750, 7750, 7750, 0, 0],
                    fbsFees: [1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 1950, 0, 0],
                    subscriptions: [875, 875, 875, 875, 875, 875, 875, 875, 875, 875, 0, 0],
                    advertising: [1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 0, 0],
                    totalRevenue: [11875, 11875, 11875, 11875, 11875, 11875, 11875, 11875, 11875, 11875, 0, 0]
                }
            }
        },
        '30d': {
            gmv: 347820,
            gmvChange: '+18.4%',
            platformRevenue: 48295,
            platformRevenueChange: '+22.1%',
            referralFee: 31130,
            referralFeeDesc: 'Commission on orders (avg 8.9%)',
            fbsFee: 7840,
            adRevenue: 5220,
            chartData: {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    referralFees: [31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 0, 0],
                    fbsFees: [7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 0, 0],
                    subscriptions: [3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 0, 0],
                    advertising: [5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 0, 0],
                    totalRevenue: [47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 0, 0]
                }
            }
        },
        '90d': {
            gmv: 1043460,
            gmvChange: '+25.7%',
            platformRevenue: 144885,
            platformRevenueChange: '+28.3%',
            referralFee: 93390,
            referralFeeDesc: 'Commission on orders (avg 8.9%)',
            fbsFee: 23520,
            adRevenue: 15660,
            chartData: {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    referralFees: [93000, 93000, 93000, 93000, 93000, 93000, 93000, 93000, 93000, 93000, 0, 0],
                    fbsFees: [23400, 23400, 23400, 23400, 23400, 23400, 23400, 23400, 23400, 23400, 0, 0],
                    subscriptions: [10500, 10500, 10500, 10500, 10500, 10500, 10500, 10500, 10500, 10500, 0, 0],
                    advertising: [15600, 15600, 15600, 15600, 15600, 15600, 15600, 15600, 15600, 15600, 0, 0],
                    totalRevenue: [142500, 142500, 142500, 142500, 142500, 142500, 142500, 142500, 142500, 142500, 0, 0]
                }
            }
        },
        ytd: {
            gmv: 4173840,
            gmvChange: '+32.1%',
            platformRevenue: 579540,
            platformRevenueChange: '+35.8%',
            referralFee: 373560,
            referralFeeDesc: 'Commission on orders (avg 8.9%)',
            fbsFee: 94080,
            adRevenue: 62640,
            chartData: {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    referralFees: [372000, 372000, 372000, 372000, 372000, 372000, 372000, 372000, 372000, 372000, 0, 0],
                    fbsFees: [93600, 93600, 93600, 93600, 93600, 93600, 93600, 93600, 93600, 93600, 0, 0],
                    subscriptions: [42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 0, 0],
                    advertising: [62400, 62400, 62400, 62400, 62400, 62400, 62400, 62400, 62400, 62400, 0, 0],
                    totalRevenue: [570000, 570000, 570000, 570000, 570000, 570000, 570000, 570000, 570000, 570000, 0, 0]
                }
            }
        },
        custom: {
            gmv: 347820,
            gmvChange: '+18.4%',
            platformRevenue: 48295,
            platformRevenueChange: '+22.1%',
            referralFee: 31130,
            referralFeeDesc: 'Commission on orders (avg 8.9%)',
            fbsFee: 7840,
            adRevenue: 5220,
            chartData: {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    referralFees: [31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 31000, 0, 0],
                    fbsFees: [7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 7800, 0, 0],
                    subscriptions: [3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 3500, 0, 0],
                    advertising: [5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 5200, 0, 0],
                    totalRevenue: [47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 47500, 0, 0]
                }
            }
        }
    };

    // Function to format currency
    function formatCurrency(value) {
        return '$' + value.toLocaleString();
    }

    // Function to update all data based on date range
    function updateDateRangeData(range) {
        const data = dateRangeData[range];
        if (!data) return;

        // Update metric cards
        document.getElementById('gmv-value').textContent = formatCurrency(data.gmv);
        document.getElementById('gmv-change').textContent = data.gmvChange;
        document.getElementById('platform-revenue-value').textContent = formatCurrency(data.platformRevenue);
        document.getElementById('platform-revenue-change').textContent = data.platformRevenueChange;
        document.getElementById('referral-fee-value').textContent = formatCurrency(data.referralFee);
        document.getElementById('referral-fee-desc').textContent = data.referralFeeDesc;
        document.getElementById('fbs-fee-value').textContent = formatCurrency(data.fbsFee);
        document.getElementById('ad-revenue-value').textContent = formatCurrency(data.adRevenue);

        // Update chart data
        if (platformRevenueChart && data.chartData.monthly) {
            const chartData = data.chartData.monthly;
            platformRevenueChart.data.labels = chartData.labels;
            platformRevenueChart.data.datasets[0].data = chartData.referralFees;
            platformRevenueChart.data.datasets[1].data = chartData.fbsFees;
            platformRevenueChart.data.datasets[2].data = chartData.subscriptions;
            platformRevenueChart.data.datasets[3].data = chartData.advertising;
            platformRevenueChart.data.datasets[4].data = chartData.totalRevenue;
            platformRevenueChart.update();
        }
    }

    // Function to update date range button styles
    function updateDateRangeButtonStyles(activeBtn) {
        document.querySelectorAll('.date-range-btn').forEach(btn => {
            const range = btn.getAttribute('data-range');
            if (btn === activeBtn) {
                btn.style.color = '#ffffff';
                btn.style.backgroundColor = '#ecab39';
                btn.style.borderColor = '#ecab39';
                btn.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
            } else {
                btn.style.color = '#374151';
                btn.style.backgroundColor = '#ffffff';
                btn.style.borderColor = '#d1d5db';
                btn.style.boxShadow = 'none';
            }
        });
    }

    // Event listeners for date range buttons
    document.querySelectorAll('.date-range-btn').forEach(button => {
        button.addEventListener('click', function() {
            const range = this.getAttribute('data-range');
            updateDateRangeButtonStyles(this);
            updateDateRangeData(range);
        });
    });

    // Export Financial Report as Image
    document.getElementById('export-report-btn').addEventListener('click', function() {
        // Show loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 14px;"></i> <span>Exporting...</span>';
        this.disabled = true;

        // Get the element to export
        const element = document.getElementById('financial-report-content');
        
        // Configure html2canvas options
        const options = {
            backgroundColor: '#f3f4f6',
            scale: 2, // Higher quality
            useCORS: true,
            logging: false,
            width: element.scrollWidth,
            height: element.scrollHeight,
            windowWidth: element.scrollWidth,
            windowHeight: element.scrollHeight
        };

        // Convert to canvas and then to image
        html2canvas(element, options).then(function(canvas) {
            // Create download link
            const link = document.createElement('a');
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, -5);
            link.download = 'financial-report-' + timestamp + '.png';
            link.href = canvas.toDataURL('image/png');
            
            // Trigger download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Reset button state
            document.getElementById('export-report-btn').innerHTML = originalText;
            document.getElementById('export-report-btn').disabled = false;
        }).catch(function(error) {
            console.error('Export failed:', error);
            alert('Failed to export report. Please try again.');
            
            // Reset button state
            document.getElementById('export-report-btn').innerHTML = originalText;
            document.getElementById('export-report-btn').disabled = false;
        });
    });
</script>
@endsection

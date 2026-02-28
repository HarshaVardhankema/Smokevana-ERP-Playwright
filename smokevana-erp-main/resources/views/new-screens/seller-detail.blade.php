@extends('layouts.app')
@section('title', 'Seller Detail - GreenLeaf Distributors')

@section('css')
    <style>
        .nav-tabs {
            display: flex;
            gap: 32px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .nav-tab {
            padding: 12px 4px;
            font-weight: 700;
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
            position: relative;
        }

        .nav-tab.active {
            color: #1e293b;
        }

        .nav-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #f3a847;
        }

        .orders-table thead th {
            background-color: #f5f5f5 !important;
            color: #1f2937 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 12px 16px !important;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .orders-table tbody tr {
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endsection

@section('content')
    <div
        style="padding: 24px; background-color: #f8fafc; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

        <!-- Global Header (Top Line Matching Second Image) -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding: 0 0 16px 0; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <h1 style="font-size: 18px; font-weight: 800; color: #000; margin: 0;">GreenLeaf Distributors</h1>
                <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #64748b;">
                    <a href="#" style="text-decoration: none; color: #0369a1;">Admin</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px; opacity: 0.5;"></i>
                    <a href="{{ route('new-screens.sellers') }}" style="text-decoration: none; color: #0369a1;">Sellers</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px; opacity: 0.5;"></i>
                    <span style="color: #64748b;">GreenLeaf Distributors</span>
                </div>
                <div style="display: flex; gap: 8px; margin-left: 10px;">
                    <span
                        style="background-color: #f0fdf4; color: #16a34a; padding: 2px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Active</span>
                    <span
                        style="background-color: #f1f5f9; color: #475569; padding: 2px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">Professional</span>
                </div>
                <!-- Compliance Score Mini -->
                <div style="display: flex; align-items: center; gap: 8px; margin-left:10px;">
                    <span style="font-weight: 800; color: #10b981; font-size: 15px;">96</span>
                    <div style="position: relative; width: 28px; height: 28px;">
                        <svg width="28" height="28" style="transform: rotate(-90deg);">
                            <circle cx="14" cy="14" r="12" fill="transparent" stroke="#f1f5f9" stroke-width="3"></circle>
                            <circle cx="14" cy="14" r="12" fill="transparent" stroke="#10b981" stroke-width="3"
                                stroke-dasharray="75.4" stroke-dashoffset="3" stroke-linecap="round"></circle>
                        </svg>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="position: relative;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" placeholder="Search seller"
                        style="padding: 8px 12px 8px 36px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none; width: 150px; background-color: #fff;">
                </div>
                <div
                    style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; font-weight: 600;">
                    <div style="width: 8px; height: 8px; background-color: #10b981; border-radius: 50%;"></div>
                    All Systems Operational
                </div>
                <div style="position: relative; color: #64748b; cursor: pointer;">
                    <i class="far fa-bell" style="font-size: 20px;"></i>
                    <span
                        style="position: absolute; top: -4px; right: -4px; background-color: #ef4444; color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 9px; display: flex; align-items: center; justify-content: center; font-weight: 800;">3</span>
                </div>
                <div
                    style="width: 34px; height: 34px; border-radius: 50%; overflow: hidden; border: 1px solid #e2e8f0; cursor: pointer;">
                    <img src="https://media.phantasm.site/wp-content/uploads/2026/01/Asset-4@2x.png"
                        style="width: 100%; height: 100%; object-fit: cover;"
                        onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=random'">
                </div>
            </div>
        </div>

        <!-- Action Buttons Row (White/Light Theme Matching Second Image) -->
        <div style="display: flex; gap: 12px; margin-bottom: 24px;">
            <button
                style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; color: #1f2937; font-weight: 700; font-size: 13.5px; cursor: pointer; transition: all 0.2s;">
                <i class="far fa-envelope" style="font-size: 14px; color: #64748b;"></i> Send Message
            </button>
            <button
                style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; color: #1f2937; font-weight: 700; font-size: 13.5px; cursor: pointer; transition: all 0.2s;">
                <i class="fas fa-sliders-h" style="font-size: 13px; color: #64748b;"></i> Adjust Fee Rates
            </button>
            <button
                style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background-color: #ffffff; border: 1px solid #ef4444; border-radius: 8px; color: #ef4444; font-weight: 700; font-size: 13.5px; cursor: pointer; transition: all 0.2s;">
                <i class="fas fa-ban" style="font-size: 14px; color: #ef4444;"></i> Suspend Seller
            </button>
            <button
                style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; color: #1f2937; font-weight: 700; font-size: 13.5px; cursor: pointer; transition: all 0.2s;">
                <i class="far fa-eye" style="font-size: 14px; color: #64748b;"></i> View as Seller
            </button>
            <button
                style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; color: #1f2937; font-weight: 700; font-size: 13.5px; cursor: pointer; transition: all 0.2s;">
                <i class="far fa-sticky-note" style="font-size: 14px; color: #64748b;"></i> Admin Notes
                <span
                    style="background-color: #f3a847; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: 2px; font-weight: 800;">4</span>
            </button>
        </div>

        <!-- Section 1: Business Info Grid (Matching Visual Style) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Column 1: Business Info -->
            <div
                style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2
                    style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 24px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    Business Info</h2>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Business Name</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;">GreenLeaf Distributors LLC</div>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        DBA</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;">GreenLeaf Distribution</div>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        EIN</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;">**-***4782</div>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Business Type</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;">Distributor</div>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Primary Contact</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;">Michael Chen</div>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Email</div>
                    <a href="mailto:michael@greenleaf.com"
                        style="font-size: 15px; font-weight: 700; color: #0284c7; text-decoration: none;">michael@greenleaf.com</a>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Phone</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;">(415) 555-0198</div>
                </div>

                <div style="margin-bottom: 18px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        Address</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.5;">1847 Market
                        Street<br>San Francisco, CA 94103</div>
                </div>

                <div style="display: flex; gap: 32px;">
                    <div>
                        <div
                            style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                            Joined Date</div>
                        <div style="font-size: 15px; font-weight: 700; color: #1e293b;">January 15, 2024</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                            Account Age</div>
                        <div style="font-size: 15px; font-weight: 700; color: #1e293b;">287 days</div>
                    </div>
                </div>
            </div>

            <!-- Column 2: Platform Performance -->
            <div
                style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2
                    style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 24px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    Platform Performance</h2>

                <div style="margin-bottom: 32px;">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                        Total Lifetime GMV</div>
                    <div style="font-size: 36px; font-weight: 800; color: #10b981; letter-spacing: -0.02em;">$284,950</div>
                </div>

                <div style="margin-bottom: 40px;">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                        Total Platform Fees Generated</div>
                    <div
                        style="font-size: 36px; font-weight: 800; color: #6366f1; letter-spacing: -0.02em; display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-tag" style="font-size: 24px; opacity: 0.8;"></i>
                        $34,194
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                            Active Product Listings</div>
                        <div style="font-size: 24px; font-weight: 800; color: #1e293b;">247</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                            Avg Order Value</div>
                        <div style="font-size: 24px; font-weight: 800; color: #1e293b;">$1,847</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                            Rating</div>
                        <div
                            style="font-size: 24px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-star" style="color: #f3a847; font-size: 20px;"></i> 4.8
                            <span style="font-weight: 600; color: #94a3b8; font-size: 14px; margin-left: 2px;">(342
                                reviews)</span>
                        </div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                            Buy Box Win Rate</div>
                        <div style="font-size: 24px; font-weight: 800; color: #1e293b;">73%</div>
                    </div>
                </div>

                <div style="margin-top: 32px;">
                    <div
                        style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">
                        Total Reviews</div>
                    <div style="font-size: 24px; font-weight: 800; color: #1e293b;">342</div>
                </div>
            </div>

            <!-- Column 3: Compliance Summary -->
            <div
                style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: center;">
                <h2
                    style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 24px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; text-align: left;">
                    Compliance Summary</h2>

                <div style="position: relative; width: 100px; height: 100px; margin: 0 auto 16px;">
                    <svg width="100" height="100" style="transform: rotate(-90deg);">
                        <circle cx="50" cy="50" r="45" fill="transparent" stroke="#f1f5f9" stroke-width="8"></circle>
                        <circle cx="50" cy="50" r="45" fill="transparent" stroke="#10b981" stroke-width="8"
                            stroke-dasharray="282.7" stroke-dashoffset="11" stroke-linecap="round"></circle>
                    </svg>
                    <div
                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 32px; font-weight: 800; color: #1e293b;">
                        96</div>
                </div>
                <div
                    style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 12px;">
                    Overall Compliance Score</div>
                <div style="font-size: 16px; font-weight: 700; color: #10b981; margin-top: 4px;">Excellent</div>

                <div style="text-align: left; margin-top: 40px;">
                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 16px;">
                        Licenses</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-weight: 700; color: #4b5563; font-size: 14px;">Active</span>
                        <span style="font-weight: 800; color: #10b981; font-size: 14px;">4</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 24px;">
                        <span style="font-weight: 700; color: #4b5563; font-size: 14px;">Expiring Soon</span>
                        <span style="font-weight: 800; color: #f59e0b; font-size: 14px;">1</span>
                    </div>

                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 16px;">
                        COAs</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-weight: 700; color: #4b5563; font-size: 14px;">Valid</span>
                        <span style="font-weight: 800; color: #10b981; font-size: 14px;">247</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 24px;">
                        <span style="font-weight: 700; color: #4b5563; font-size: 14px;">Expired</span>
                        <span style="font-weight: 800; color: #ef4444; font-size: 14px;">0</span>
                    </div>

                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 12px;">
                        PACT ACT Status</div>
                    <div style="margin-bottom: 24px;">
                        <span
                            style="background-color: #f0fdf4; color: #16a34a; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800;">Compliant</span>
                    </div>

                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">
                        Last Audit Date</div>
                    <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 24px;">September 15, 2024
                    </div>

                    <div
                        style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">
                        Probation Status</div>
                    <div style="font-size: 14px; font-weight: 700; color: #10b981;">None</div>
                </div>
            </div>
        </div>

        <!-- Section 2: Platform Revenue Analytics -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; border-left: 6px solid #6366f1;">
            <div style="padding-left: 12px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <i class="fas fa-chart-line" style="color: #6366f1; font-size: 20px;"></i>
                    <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Platform Revenue from
                        GreenLeaf Distributors</h2>
                </div>

                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px; margin-bottom: 32px;">
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                            Total Fees (Life)</div>
                        <div style="font-size: 26px; font-weight: 800; color: #10b981;">$34,194</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                            This Month</div>
                        <div style="font-size: 26px; font-weight: 800; color: #10b981;">$4,230</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                            Avg Monthly</div>
                        <div style="font-size: 26px; font-weight: 800; color: #1e293b;">$3,685</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                            Fee Rate</div>
                        <div style="font-size: 26px; font-weight: 800; color: #6366f1;">Custom 11%</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                            Outstanding</div>
                        <div style="font-size: 26px; font-weight: 800; color: #10b981;">$0.00</div>
                    </div>
                    <div>
                        <div
                            style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                            Last Payment</div>
                        <div style="font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 8px;">Oct 15, 2024</div>
                    </div>
                </div>

                <!-- Mocked Chart SVG -->
                <div style="height: 120px; position: relative; margin-top: 20px;">
                    <svg width="100%" height="100%" viewBox="0 0 1000 100" preserveAspectRatio="none">
                        <path
                            d="M0,80 L80,78 L160,75 L240,70 L320,65 L400,60 L480,55 L560,50 L640,55 L720,60 L800,20 L880,100 L1000,100"
                            fill="rgba(99, 102, 241, 0.05)" stroke="none"></path>
                        <path
                            d="M0,80 L80,78 L160,75 L240,70 L320,65 L400,60 L480,55 L560,50 L640,55 L720,60 L800,20 L880,100"
                            fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round"></path>
                    </svg>
                    <div
                        style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px; color: #94a3b8; font-weight: 700;">
                        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Tabs & Table -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="padding: 24px;">
                <div class="nav-tabs">
                    <div class="nav-tab active">Orders</div>
                    <div class="nav-tab">Products</div>
                    <div class="nav-tab">Compliance</div>
                    <div class="nav-tab">Financials</div>
                    <div class="nav-tab">Activity Log</div>
                    <div class="nav-tab">Admin Notes <span
                            style="background-color: #f3a847; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: 4px;">4</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div style="display: flex; gap: 24px; align-items: center;">
                        <span style="font-size: 14px; color: #475569;"><span style="font-weight: 800; color: #1e293b;">Total
                                Orders:</span> 154</span>
                        <span style="font-size: 14px; color: #475569;"><span style="font-weight: 800; color: #1e293b;">Total
                                GMV:</span> $284,950</span>
                        <span style="font-size: 14px; color: #475569;"><span style="font-weight: 800; color: #6366f1;">Total
                                Fees:</span> $34,194</span>
                    </div>
                    <button
                        style="padding: 10px 18px; background-color: white; color: #1e293b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download"></i> Export Orders
                    </button>
                </div>

                <table class="orders-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Buyer</th>
                            <th style="text-align: center;">Items</th>
                            <th style="text-align: right;">GMV</th>
                            <th style="text-align: right;">Platform Fee</th>
                            <th style="text-align: center;">Payment Status</th>
                            <th style="text-align: center;">Fulfillment</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $orders = [
                                ['id' => '#ORD-8472', 'date' => 'Oct 28, 2024', 'buyer' => 'Sunset Dispensary', 'items' => 12, 'gmv' => '$2,450', 'fee' => '$269.50 (11%)', 'pay' => 'Paid', 'status' => 'Shipped', 'pay_bg' => '#f0fdf4', 'pay_text' => '#16a34a', 'stat_bg' => '#eff6ff', 'stat_text' => '#3b82f6'],
                                ['id' => '#ORD-8461', 'date' => 'Oct 27, 2024', 'buyer' => 'Green Valley Retail', 'items' => 8, 'gmv' => '$1,890', 'fee' => '$207.90 (11%)', 'pay' => 'Paid', 'status' => 'Delivered', 'pay_bg' => '#f0fdf4', 'pay_text' => '#16a34a', 'stat_bg' => '#f0fdf4', 'stat_text' => '#16a34a'],
                                ['id' => '#ORD-8449', 'date' => 'Oct 26, 2024', 'buyer' => 'CloudNine Shop', 'items' => 15, 'gmv' => '$3,240', 'fee' => '$356.40 (11%)', 'pay' => 'Paid', 'status' => 'Processing', 'pay_bg' => '#f0fdf4', 'pay_text' => '#16a34a', 'stat_bg' => '#fffbeb', 'stat_text' => '#d97706'],
                                ['id' => '#ORD-8432', 'date' => 'Oct 25, 2024', 'buyer' => 'Pacific Coast Wholesale', 'items' => 24, 'gmv' => '$5,680', 'fee' => '$624.80 (11%)', 'pay' => 'Paid', 'status' => 'Delivered', 'pay_bg' => '#f0fdf4', 'pay_text' => '#16a34a', 'stat_bg' => '#f0fdf4', 'stat_text' => '#16a34a'],
                                ['id' => '#ORD-8418', 'date' => 'Oct 24, 2024', 'buyer' => 'HighTide Brands', 'items' => 6, 'gmv' => '$1,120', 'fee' => '$123.20 (11%)', 'pay' => 'Paid', 'status' => 'Shipped', 'pay_bg' => '#f0fdf4', 'pay_text' => '#16a34a', 'stat_bg' => '#eff6ff', 'stat_text' => '#3b82f6'],
                            ];
                        @endphp
                        @foreach($orders as $order)
                            <tr>
                                <td style="padding: 16px 16px; color: #0284c7; font-weight: 700; font-size: 13px;">
                                    {{ $order['id'] }}</td>
                                <td style="padding: 16px 16px; color: #64748b; font-size: 13px;">{{ $order['date'] }}</td>
                                <td style="padding: 16px 16px; color: #1e293b; font-weight: 600; font-size: 13px;">
                                    {{ $order['buyer'] }}</td>
                                <td
                                    style="padding: 16px 16px; text-align: center; color: #1e293b; font-weight: 600; font-size: 13px;">
                                    {{ $order['items'] }}</td>
                                <td
                                    style="padding: 16px 16px; text-align: right; color: #1e293b; font-weight: 800; font-size: 13px;">
                                    {{ $order['gmv'] }}</td>
                                <td
                                    style="padding: 16px 16px; text-align: right; color: #6366f1; font-weight: 800; font-size: 13px;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                        <i class="fas fa-tag" style="font-size: 11px;"></i> {{ $order['fee'] }}
                                    </div>
                                </td>
                                <td style="padding: 16px 16px; text-align: center;">
                                    <span
                                        style="background-color: {{ $order['pay_bg'] }}; color: {{ $order['pay_text'] }}; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">{{ $order['pay'] }}</span>
                                </td>
                                <td style="padding: 16px 16px; text-align: center;">
                                    <span
                                        style="background-color: {{ $order['stat_bg'] }}; color: {{ $order['stat_text'] }}; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">{{ $order['status'] }}</span>
                                </td>
                                <td style="padding: 16px 16px; text-align: center; color: #94a3b8;">
                                    <i class="fas fa-ellipsis-v" style="cursor: pointer;"></i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div
                    style="padding: 16px 0; display: flex; justify-content: space-between; align-items: center; background-color: #ffffff; border-top: 1px solid #f1f5f9;">
                    <div style="font-size: 13px; color: #64748b;">Showing 1-5 of 154</div>
                    <div style="display: flex; gap: 8px;">
                        <button
                            style="width: 32px; height: 32px; border: 1px solid #e2e8f0; background: white; border-radius: 6px; color: #94a3b8; cursor: pointer;"><i
                                class="fas fa-chevron-left"></i></button>
                        <button
                            style="width: 32px; height: 32px; border: none; background: #f3a847; color: white; border-radius: 6px; font-weight: 700; cursor: pointer;">1</button>
                        <button
                            style="width: 32px; height: 32px; border: 1px solid #e2e8f0; background: white; border-radius: 6px; color: #64748b; cursor: pointer;">2</button>
                        <button
                            style="width: 32px; height: 32px; border: 1px solid #e2e8f0; background: white; border-radius: 6px; color: #64748b; cursor: pointer;">3</button>
                        <button
                            style="width: 32px; height: 32px; border: 1px solid #e2e8f0; background: white; border-radius: 6px; color: #94a3b8; cursor: pointer;"><i
                                class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
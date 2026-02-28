@extends('layouts.app')
@section('title', 'Advertising Platform Overview')

@section('content')
    <div
        style="padding: 24px; background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', system-ui, -apple-system, sans-serif;">

        <!-- Section 1: Header & Summary Cards -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Advertising Platform</h1>
                <p style="font-size: 14px; color: #64748b; margin: 0;">Manage ad campaigns, revenue, and platform settings
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="position: relative;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" placeholder="Search campaigns, advertisers..."
                        style="padding: 10px 16px 10px 40px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; width: 280px; background-color: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                </div>
                <div
                    style="position: relative; color: #64748b; cursor: pointer; background: white; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <i class="far fa-bell" style="font-size: 18px;"></i>
                    <span
                        style="position: absolute; top: 8px; right: 8px; background-color: #ef4444; border: 2px solid white; border-radius: 50%; width: 10px; height: 10px;"></span>
                </div>
                <button
                    style="background-color: #8b5cf6; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.2);">
                    <i class="fas fa-plus"></i> New Campaign
                </button>
                <a href="{{ route('new-screens.platform-settings') }}"
                    style="color: #64748b; cursor: pointer; background: white; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-cog" style="font-size: 18px;"></i>
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 32px;">
            <!-- Card 1 -->
            <div
                style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #a855f7;"></div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="background: #f3e8ff; color: #a855f7; padding: 10px; border-radius: 12px;"><i
                            class="fas fa-dollar-sign"></i></div>
                    <div
                        style="background: #f0fdf4; color: #16a34a; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 20px;">
                        +12.5%</div>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">$5,220</div>
                <div style="font-size: 13px; color: #64748b; font-weight: 600;">Ad Revenue (This Month)</div>
                <div
                    style="border-top: 1px solid #f1f5f9; margin-top: 16px; padding-top: 12px; font-size: 12px; color: #94a3b8;">
                    vs. Last Month: <span style="font-weight: 700; color: #64748b;">$4,640</span>
                </div>
            </div>

            <!-- Card 2 -->
            <div
                style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #3b82f6;"></div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="background: #eff6ff; color: #3b82f6; padding: 10px; border-radius: 12px;"><i
                            class="fas fa-bullseye"></i></div>
                    <div
                        style="background: #f0fdf4; color: #16a34a; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 20px;">
                        +8</div>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">187</div>
                <div style="font-size: 13px; color: #64748b; font-weight: 600;">Active Campaigns</div>
                <div
                    style="border-top: 1px solid #f1f5f9; margin-top: 16px; padding-top: 12px; font-size: 12px; color: #94a3b8;">
                    Paused: 23 | Ended: 145
                </div>
            </div>

            <!-- Card 3 -->
            <div
                style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #06b6d4;"></div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="background: #ecfeff; color: #06b6d4; padding: 10px; border-radius: 12px;"><i
                            class="fas fa-eye"></i></div>
                    <div
                        style="background: #f0fdf4; color: #16a34a; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 20px;">
                        +18.2%</div>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">2.4M</div>
                <div style="font-size: 13px; color: #64748b; font-weight: 600;">Total Impressions</div>
                <div
                    style="border-top: 1px solid #f1f5f9; margin-top: 16px; padding-top: 12px; font-size: 12px; color: #94a3b8;">
                    Daily Avg: 80K
                </div>
            </div>

            <!-- Card 4 -->
            <div
                style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #10b981;"></div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="background: #f0fdf4; color: #10b981; padding: 10px; border-radius: 12px;"><i
                            class="fas fa-mouse-pointer"></i></div>
                    <div
                        style="background: #f0fdf4; color: #16a34a; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 20px;">
                        +0.3%</div>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">2.1%</div>
                <div style="font-size: 13px; color: #64748b; font-weight: 600;">Platform Avg CTR</div>
                <div
                    style="border-top: 1px solid #f1f5f9; margin-top: 16px; padding-top: 12px; font-size: 12px; color: #94a3b8;">
                    Industry Avg: 1.8%
                </div>
            </div>

            <!-- Card 5 -->
            <div
                style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #f59e0b;"></div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="background: #fffbeb; color: #f59e0b; padding: 10px; border-radius: 12px;"><i
                            class="fas fa-chart-line"></i></div>
                    <div
                        style="background: #fef2f2; color: #ef4444; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 20px;">
                        -1.2%</div>
                </div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">19.8%</div>
                <div style="font-size: 13px; color: #64748b; font-weight: 600;">Advertiser ACOS Avg</div>
                <div
                    style="border-top: 1px solid #f1f5f9; margin-top: 16px; padding-top: 12px; font-size: 12px; color: #94a3b8;">
                    Target: < 20% </div>
            </div>
        </div>

       

        <!-- Section 2: Advertising Revenue Trend -->
            <div
                style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
                    <div>
                        <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Advertising
                            Revenue Trend</h2>
                        <p style="font-size: 14px; color: #64748b; margin: 0;">Daily ad revenue with advertiser count
                            overlay</p>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="far fa-calendar-alt"></i> Last 30 Days
                        </button>
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>

                <!-- Chart Mockup -->
                <div style="height: 300px; position: relative;">
                    <div
                        style="height: 240px; display: flex; align-items: flex-end; justify-content: space-between; gap: 8px; padding-right: 40px; border-bottom: 2px solid #f1f5f9;">
                        @php
                            $chartData = [160, 210, 175, 155, 205, 145, 225, 145, 145, 225, 180, 215, 165, 205, 155, 215, 175, 170, 195, 155, 200, 220, 170, 135, 125, 175, 135, 140, 180, 160];
                            $max = max($chartData);
                        @endphp
                        @foreach($chartData as $val)
                            <div
                                style="flex: 1; height: {{ ($val / $max) * 100 }}%; background: #6366f1; border-radius: 4px 4px 0 0;">
                            </div>
                        @endforeach

                        <!-- Right Y Axis -->
                        <div
                            style="position: absolute; right: 0; top: 0; bottom: 0; width: 40px; display: flex; flex-direction: column; justify-content: space-between; font-size: 11px; color: #94a3b8; font-weight: 700; text-align: right;">
                            <span>140</span><span>135</span><span>130</span><span>125</span><span>120</span>
                        </div>

                        <!-- SVG Line Overlay (Mocked) -->
                        <svg style="position: absolute; left: 0; top: 0; width: calc(100% - 40px); height: 240px;"
                            preserveAspectRatio="none">
                            <path
                                d="M0,180 L40,150 L80,165 L120,170 L160,130 L200,180 L240,155 L280,185 L320,150 L360,190 L400,165 L440,155 L480,175 L520,140 L560,185 L600,195 L640,140 L680,165 L720,175 L760,195 L800,165 L840,185 L880,160 L920,180 L960,160"
                                fill="none" stroke="#94a3b8" stroke-width="2" stroke-dasharray="4,4"></path>
                        </svg>
                    </div>

                    <div
                        style="display: flex; justify-content: space-between; margin-top: 12px; font-size: 11px; color: #94a3b8; font-weight: 700; padding-right: 40px;">
                        <span>Jan 13</span><span>Jan 15</span><span>Jan 17</span><span>Jan 19</span><span>Jan
                            21</span><span>Jan 23</span><span>Jan 25</span><span>Jan 27</span><span>Jan 29</span><span>Jan
                            31</span><span>Feb 2</span><span>Feb 4</span><span>Feb 6</span><span>Feb 8</span><span>Feb
                            11</span>
                    </div>
                </div>

                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                    <div style="display: flex; gap: 24px;">
                        <div
                            style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; font-weight: 600;">
                            <div style="width: 12px; height: 12px; background: #6366f1; border-radius: 3px;"></div> Daily
                            Revenue
                        </div>
                        <div
                            style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; font-weight: 600;">
                            <i class="fas fa-chart-line" style="color: #94a3b8;"></i> Advertiser Count
                        </div>
                    </div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b;">
                        Monthly Total: <span style="color: #8b5cf6;">$5,220</span>
                    </div>
                </div>
            </div>

        

            <!-- Section 3: Revenue by Ad Type & Top Advertisers -->
            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 24px; margin-bottom: 32px;">
                <!-- Left: Revenue by Ad Type -->
                <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Revenue by Ad Type</h2>
                        <a href="#" style="font-size: 13px; font-weight: 700; color: #8b5cf6; text-decoration: none;">View
                            Details</a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <!-- Type 1 -->
                        <div>
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #fff7ed; color: #f97316; padding: 8px; border-radius: 10px;"><i
                                            class="fas fa-box"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Sponsored Products
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">124 campaigns</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 14px; font-weight: 800; color: #1e293b;">$3,120</div>
                                    <div style="font-size: 12px; color: #16a34a; font-weight: 700;">59.8%</div>
                                </div>
                            </div>
                            <div
                                style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                <div style="width: 59.8%; height: 100%; background: #f97316;"></div>
                            </div>
                        </div>

                        <!-- Type 2 -->
                        <div>
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #eff6ff; color: #3b82f6; padding: 8px; border-radius: 10px;"><i
                                            class="fas fa-store"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Sponsored Brands
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">38 campaigns</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 14px; font-weight: 800; color: #1e293b;">$1,340</div>
                                    <div style="font-size: 12px; color: #16a34a; font-weight: 700;">25.7%</div>
                                </div>
                            </div>
                            <div
                                style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                <div style="width: 25.7%; height: 100%; background: #3b82f6;"></div>
                            </div>
                        </div>

                        <!-- Type 3 -->
                        <div>
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #f5f3ff; color: #8b5cf6; padding: 8px; border-radius: 10px;"><i
                                            class="fas fa-ad"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Sponsored Display
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">19 campaigns</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 14px; font-weight: 800; color: #1e293b;">$540</div>
                                    <div style="font-size: 12px; color: #16a34a; font-weight: 700;">10.3%</div>
                                </div>
                            </div>
                            <div
                                style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                <div style="width: 10.3%; height: 100%; background: #8b5cf6;"></div>
                            </div>
                        </div>

                        <!-- Type 4 -->
                        <div>
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: #f0fdf4; color: #10b981; padding: 8px; border-radius: 10px;"><i
                                            class="fas fa-layer-group"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Category Sponsorship
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">6 campaigns</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 14px; font-weight: 800; color: #1e293b;">$220</div>
                                    <div style="font-size: 12px; color: #16a34a; font-weight: 700;">4.2%</div>
                                </div>
                            </div>
                            <div
                                style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                <div style="width: 4.2%; height: 100%; background: #10b981;"></div>
                            </div>
                        </div>
                    </div>

                    <div
                        style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 16px; font-weight: 700; color: #1e293b;">Total Revenue</span>
                        <span style="font-size: 24px; font-weight: 800; color: #8b5cf6;">$5,220</span>
                    </div>
                </div>

                <!-- Right: Top Advertisers -->
                <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Top Advertisers (Spenders)
                        </h2>
                        <a href="#" style="font-size: 13px; font-weight: 700; color: #8b5cf6; text-decoration: none;">View
                            All</a>
                    </div>

                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #f1f5f9; text-align: left;">
                                <th
                                    style="background-color: #f5f5f5 !important; padding: 12px 10px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                    Rank</th>
                                <th
                                    style="background-color: #f5f5f5 !important; padding: 12px 10px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                    Seller Name</th>
                                <th
                                    style="background-color: #f5f5f5 !important; padding: 12px 10px; text-align: right; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                    Ad Spend</th>
                                <th
                                    style="background-color: #f5f5f5 !important; padding: 12px 10px; text-align: right; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                    Revenue Gen.</th>
                                <th
                                    style="background-color: #f5f5f5 !important; padding: 12px 10px; text-align: right; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                    ROAS</th>
                                <th
                                    style="background-color: #f5f5f5 !important; padding: 12px 10px; text-align: right; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                    Platform Rev.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $advertisers = [
                                    ['rank' => 1, 'name' => 'TechGear Pro', 'id' => '#87234', 'spend' => '$1,240', 'rev' => '$8,920', 'roas' => '7.19x', 'plat' => '$1,240', 'bg' => '#f59e0b'],
                                    ['rank' => 2, 'name' => 'Fashion Hub', 'id' => '#65891', 'spend' => '$980', 'rev' => '$6,440', 'roas' => '6.57x', 'plat' => '$980', 'bg' => '#94a3b8'],
                                    ['rank' => 3, 'name' => 'Home Essentials', 'id' => '#54732', 'spend' => '$765', 'rev' => '$4,890', 'roas' => '6.39x', 'plat' => '$765', 'bg' => '#f97316'],
                                    ['rank' => 4, 'name' => 'Beauty Bliss', 'id' => '#43298', 'spend' => '$620', 'rev' => '$3,720', 'roas' => '6.00x', 'plat' => '$620', 'bg' => '#cbd5e1'],
                                    ['rank' => 5, 'name' => 'Sports Zone', 'id' => '#39847', 'spend' => '$515', 'rev' => '$2,880', 'roas' => '5.59x', 'plat' => '$515', 'bg' => '#cbd5e1'],
                                ];
                            @endphp
                            @foreach($advertisers as $adv)
                                <tr style="border-bottom: 1px solid #f8fafc; background-color: #ffffff !important;">
                                    <td style="padding: 16px 10px;">
                                        <div
                                            style="width: 28px; height: 28px; background: {{ $adv['bg'] }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">
                                            {{ $adv['rank'] }}</div>
                                    </td>
                                    <td style="padding: 16px 10px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden;">
                                                <img src="https://i.pravatar.cc/100?u={{ $adv['id'] }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div>
                                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">
                                                    {{ $adv['name'] }}</div>
                                                <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">ID:
                                                    {{ $adv['id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        style="padding: 16px 10px; text-align: right; font-size: 14px; font-weight: 800; color: #1e293b;">
                                        {{ $adv['spend'] }}</td>
                                    <td
                                        style="padding: 16px 10px; text-align: right; font-size: 14px; font-weight: 700; color: #64748b;">
                                        {{ $adv['rev'] }}</td>
                                    <td
                                        style="padding: 16px 10px; text-align: right; font-size: 13px; font-weight: 700; color: #16a34a;">
                                        {{ $adv['roas'] }}</td>
                                    <td
                                        style="padding: 16px 10px; text-align: right; font-size: 15px; font-weight: 800; color: #8b5cf6;">
                                        {{ $adv['plat'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 4: Campaigns Pending Review -->
            <div
                style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 32px; overflow: hidden;">
                <div
                    style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="background: #fffbeb; color: #f59e0b; padding: 10px; border-radius: 10px;"><i
                                class="fas fa-flag"></i></div>
                        <div>
                            <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Campaigns Pending
                                Review</h2>
                            <p style="font-size: 13px; color: #64748b; margin: 0;">Sponsored Brands requiring creative
                                approval</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-sort-amount-down"></i> Sort by Date
                        </button>
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left;">
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Campaign Name</th>
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Seller</th>
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Type</th>
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Creative Preview</th>
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Submitted</th>
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Status</th>
                            <th
                                style="background-color: #f5f5f5 !important; padding: 16px 24px; text-align: right; font-size: 11px; font-weight: 700; color: #000000 !important; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pending = [
                                ['name' => 'Summer Collection Launch', 'id' => '#CAM-89234', 'seller' => 'Fashion Hub', 'sid' => '#65891', 'type' => 'Sponsored Brand', 'time' => '2 hours ago', 'date' => 'Dec 18, 2024 14:30', 'bg' => '#fdf2f2'],
                                ['name' => 'Holiday Gift Guide 2024', 'id' => '#CAM-89156', 'seller' => 'Home Essentials', 'sid' => '#54732', 'type' => 'Sponsored Brand', 'time' => '5 hours ago', 'date' => 'Dec 18, 2024 11:15', 'bg' => '#f0fdf4'],
                                ['name' => 'New Year Fitness Campaign', 'id' => '#CAM-89087', 'seller' => 'Sports Zone', 'sid' => '#39847', 'type' => 'Sponsored Brand', 'time' => '8 hours ago', 'date' => 'Dec 18, 2024 08:45', 'bg' => '#eff6ff'],
                                ['name' => 'Premium Skincare Line', 'id' => '#CAM-88923', 'seller' => 'Beauty Bliss', 'sid' => '#43298', 'type' => 'Sponsored Brand', 'time' => '12 hours ago', 'date' => 'Dec 18, 2024 04:20', 'bg' => '#fdf2f8'],
                            ];
                        @endphp
                        @foreach($pending as $item)
                            <tr style="border-bottom: 1px solid #f1f5f9; background-color: #ffffff !important;">
                                <td style="padding: 24px;">
                                    <div style="font-size: 15px; font-weight: 800; color: #1e293b;">{{ $item['name'] }}</div>
                                    <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">ID: {{ $item['id'] }}</div>
                                </td>
                                <td style="padding: 24px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden;">
                                            <img src="https://i.pravatar.cc/100?u={{ $item['sid'] }}"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;">
                                                {{ $item['seller'] }}</div>
                                            <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">{{ $item['sid'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 24px;">
                                    <span
                                        style="display: flex; align-items: center; gap: 6px; background-color: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; width: max-content;">
                                        <i class="fas fa-store" style="font-size: 10px;"></i> {{ $item['type'] }}
                                    </span>
                                </td>
                                <td style="padding: 24px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 48px; height: 48px; background: {{ $item['bg'] }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="far fa-image" style="font-size: 20px; opacity: 0.5; color: #000;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 12px; font-weight: 700; color: #1e293b;">1200x628px</div>
                                            <div style="font-size: 11px; color: #94a3b8;">Banner Ad</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 24px;">
                                    <div style="font-size: 13px; font-weight: 700; color: #1e293b;">{{ $item['time'] }}</div>
                                    <div style="font-size: 11px; color: #94a3b8;">{{ $item['date'] }}</div>
                                </td>
                                <td style="padding: 24px;">
                                    <span
                                        style="display: flex; align-items: center; gap: 6px; background-color: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; width: max-content;">
                                        <div style="width: 6px; height: 6px; background: #d97706; border-radius: 50%;"></div>
                                        Pending
                                    </span>
                                </td>
                                <td style="padding: 24px; text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                        <button
                                            style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 800; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button
                                            style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 800; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <a href="#"
                                            style="font-size: 12px; font-weight: 700; color: #8b5cf6; text-decoration: none; margin-left: 8px;">Preview
                                            Full</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div
                    style="padding: 20px 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: white;">
                    <div style="font-size: 14px; color: #64748b; font-weight: 600;">Showing <span
                            style="color: #1e293b; font-weight: 800;">4</span> of 8 pending campaigns</div>
                    <div style="display: flex; gap: 12px;">
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #94a3b8; cursor: not-allowed;">Previous</button>
                        <button
                            style="padding: 8px 16px; background: #8b5cf6; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">Next</button>
                    </div>
                </div>
            </div>

          

            <!-- Section 6: Platform Ad Settings -->
            <div
                style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="background: #f5f3ff; color: #8b5cf6; padding: 10px; border-radius: 10px;"><i
                                class="fas fa-sliders-h"></i></div>
                        <div>
                            <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Platform Ad Settings
                            </h2>
                            <p style="font-size: 13px; color: #64748b; margin: 0;">Configure advertising parameters and
                                compliance rules</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #64748b; cursor: pointer;">Reset
                            to Defaults</button>
                        <button
                            style="padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 13px; font-weight: 700; color: #8b5cf6; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-history"></i> View History
                        </button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px;">
                    <!-- Left Side: Bid Settings -->
                    <div>
                        <h3 style="font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 20px;">Minimum CPC Bids
                            (per ad type)</h3>
                        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                            @php
                                $bidSettings = [
                                    ['name' => 'Sponsored Products', 'hint' => 'Cost per click minimum', 'val' => '0.25', 'bg' => '#fff7ed', 'icon' => 'fas fa-box', 'color' => '#f97316'],
                                    ['name' => 'Sponsored Brands', 'hint' => 'Cost per click minimum', 'val' => '0.50', 'bg' => '#eff6ff', 'icon' => 'fas fa-store', 'color' => '#3b82f6'],
                                    ['name' => 'Sponsored Display', 'hint' => 'Cost per click minimum', 'val' => '0.35', 'bg' => '#f5f3ff', 'icon' => 'fas fa-ad', 'color' => '#8b5cf6'],
                                    ['name' => 'Category Sponsorship', 'hint' => 'Cost per click minimum', 'val' => '1.00', 'bg' => '#f0fdf4', 'icon' => 'fas fa-layer-group', 'color' => '#10b981'],
                                ];
                            @endphp
                            @foreach($bidSettings as $set)
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid {{ $set['bg'] == '#fff' ? '#e2e8f0' : $set['bg'] }}; padding: 16px; border-radius: 12px; background: {{ $set['bg'] }}10;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div
                                            style="background: {{ $set['bg'] }}; color: {{ $set['color'] }}; padding: 10px; border-radius: 10px;">
                                            <i class="{{ $set['icon'] }}"></i></div>
                                        <div>
                                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;">{{ $set['name'] }}
                                            </div>
                                            <div style="font-size: 11px; color: #94a3b8;">{{ $set['hint'] }}</div>
                                        </div>
                                    </div>
                                    <div
                                        style="display: flex; align-items: center; gap: 12px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px 12px;">
                                        <span style="font-size: 14px; font-weight: 700; color: #94a3b8;">$</span>
                                        <input type="text" value="{{ $set['val'] }}"
                                            style="border: none; width: 40px; text-align: right; font-size: 14px; font-weight: 800; color: #1e293b; outline: none;">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <h3 style="font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 20px;">Ad Density
                            Controls</h3>
                        <div
                            style="background: #f8fafc; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; gap: 24px; border: 1px solid #f1f5f9;">
                            <div>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Maximum Ad Density
                                            per Page</div>
                                        <div style="font-size: 11px; color: #94a3b8;">Percentage of page content that can be
                                            ads</div>
                                    </div>
                                    <div
                                        style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; gap: 4px;">
                                        <input type="text" value="30"
                                            style="border: none; width: 30px; text-align: center; font-size: 14px; font-weight: 800; color: #1e293b; outline: none;">
                                        <span style="font-size: 14px; font-weight: 700; color: #94a3b8;">%</span>
                                    </div>
                                </div>
                                <div
                                    style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                    <div style="width: 30%; height: 100%; background: #8b5cf6;"></div>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Ad-to-Organic Ratio in
                                        Search Results</div>
                                    <div style="font-size: 11px; color: #94a3b8;">Ratio of sponsored to organic listings
                                    </div>
                                    <div style="font-size: 11px; color: #94a3b8; font-style: italic; margin-top: 4px;">1
                                        sponsored result for every 4 organic results</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div
                                        style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; font-size: 14px; font-weight: 800; color: #1e293b;">
                                        1</div>
                                    <span style="font-weight: 800; color: #94a3b8;">:</span>
                                    <div
                                        style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; font-size: 14px; font-weight: 800; color: #1e293b;">
                                        4</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Toggles & Guides -->
                    <div>
                        <h3 style="font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 20px;">Compliance &
                            Restrictions</h3>
                        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                            <!-- Toggle 1 -->
                            <div
                                style="background: #f0fdf4; border: 1px solid #dcfce7; padding: 20px; border-radius: 16px;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div
                                            style="background: #dcfce7; color: #16a34a; padding: 10px; border-radius: 12px;">
                                            <i class="fas fa-shield-alt"></i></div>
                                        <div>
                                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">Auto-Suppress
                                                Restricted Jurisdictions</div>
                                            <div style="font-size: 12px; color: #64748b;">Automatically block ads in
                                                restricted regions</div>
                                        </div>
                                    </div>
                                    <div
                                        style="width: 44px; height: 24px; background: #10b981; border-radius: 20px; position: relative; cursor: pointer;">
                                        <div
                                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                                        </div>
                                    </div>
                                </div>
                                <div
                                    style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #16a34a; margin-left: 56px;">
                                    <i class="fas fa-check-circle"></i> Currently enabled - 12 jurisdictions blocked
                                </div>
                            </div>

                            <!-- Toggle 2 -->
                            <div
                                style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div
                                            style="background: #eff6ff; color: #3b82f6; padding: 10px; border-radius: 12px;">
                                            <i class="fas fa-user-shield"></i></div>
                                        <div>
                                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">Age-Restricted
                                                Content Filter</div>
                                            <div style="font-size: 12px; color: #64748b;">Require age verification for
                                                certain products</div>
                                        </div>
                                    </div>
                                    <div
                                        style="width: 44px; height: 24px; background: #3b82f6; border-radius: 20px; position: relative; cursor: pointer;">
                                        <div
                                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 3 -->
                            <div
                                style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 16px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div
                                            style="background: #fffbeb; color: #f59e0b; padding: 10px; border-radius: 12px;">
                                            <i class="fas fa-eye"></i></div>
                                        <div>
                                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">Manual Review
                                                Required</div>
                                            <div style="font-size: 12px; color: #64748b;">All new campaigns need admin
                                                approval</div>
                                        </div>
                                    </div>
                                    <div
                                        style="width: 44px; height: 24px; background: #e2e8f0; border-radius: 20px; position: relative; cursor: pointer;">
                                        <div
                                            style="position: absolute; left: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 style="font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 20px;">Resources &
                            Guidelines</h3>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <a href="#"
                                style="text-decoration: none; background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 12px; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="background: #fbcfe8; color: #db2777; padding: 8px; border-radius: 8px;"><i
                                            class="fas fa-book"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Ad Creative
                                            Guidelines</div>
                                        <div style="font-size: 11px; color: #64748b;">Best practices and requirements</div>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right" style="color: #db2777; font-size: 14px;"></i>
                            </a>

                            <a href="#"
                                style="text-decoration: none; background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="background: #dbeafe; color: #2563eb; padding: 8px; border-radius: 8px;"><i
                                            class="fas fa-file-alt"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Compliance
                                            Documentation</div>
                                        <div style="font-size: 11px; color: #64748b;">Legal requirements by region</div>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right" style="color: #2563eb; font-size: 14px;"></i>
                            </a>

                            <a href="#"
                                style="text-decoration: none; background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="background: #dcfce7; color: #16a34a; padding: 8px; border-radius: 8px;"><i
                                            class="fas fa-graduation-cap"></i></div>
                                    <div>
                                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Advertiser Training
                                            Center</div>
                                        <div style="font-size: 11px; color: #64748b;">Tutorials and certification courses
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-right" style="color: #16a34a; font-size: 14px;"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div
                    style="margin-top: 48px; border-top: 1px solid #f1f5f9; padding-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                    <div
                        style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; font-weight: 600;">
                        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
                        Last updated: Dec 15, 2024 at 3:42 PM by Admin User
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button
                            style="padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; font-size: 14px; font-weight: 700; color: #64748b; cursor: pointer;">Cancel</button>
                        <button
                            style="padding: 12px 24px; background: #f97316; color: white; border: none; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.2);">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </div>
            </div>
                <!-- Section: Performance & Growth Metrics -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
                <!-- Campaign Performance -->
                <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Campaign Performance</h3>
                        <i class="fas fa-chart-pie" style="color: #8b5cf6; font-size: 18px;"></i>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Active Campaigns</span>
                            <span style="font-size: 16px; font-weight: 800; color: #1e293b;">187</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Avg. Campaign Duration</span>
                            <span style="font-size: 14px; font-weight: 800; color: #1e293b;">18 days</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Completion Rate</span>
                            <span style="font-size: 14px; font-weight: 800; color: #16a34a;">87.3%</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <div
                                style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 10px;">
                                Campaign Status Distribution</div>
                            <div style="display: flex; height: 8px; border-radius: 4px; overflow: hidden; background: #f1f5f9;">
                                <div style="width: 70%; background: #16a34a;"></div>
                                <div style="width: 20%; background: #f59e0b;"></div>
                                <div style="width: 10%; background: #ef4444;"></div>
                            </div>
                            <div
                                style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 11px; color: #94a3b8; font-weight: 700;">
                                <span>Active (187)</span>
                                <span>Paused (23)</span>
                                <span>Ended (145)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertiser Growth -->
                <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Advertiser Growth</h3>
                        <i class="fas fa-users" style="color: #3b82f6; font-size: 18px;"></i>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Total Advertisers</span>
                            <span style="font-size: 16px; font-weight: 800; color: #1e293b;">142</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">New This Month</span>
                            <span style="font-size: 14px; font-weight: 800; color: #16a34a;">+18</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Avg. Spend per Advertiser</span>
                            <span style="font-size: 14px; font-weight: 800; color: #1e293b;">$36.76</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <div
                                style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 10px;">
                                Monthly Growth Trend</div>
                            <div style="height: 40px; display: flex; align-items: flex-end; gap: 4px;">
                                <div style="flex: 1; height: 12px; background: #dbeafe; border-radius: 2px;"></div>
                                <div style="flex: 1; height: 18px; background: #dbeafe; border-radius: 2px;"></div>
                                <div style="flex: 1; height: 14px; background: #dbeafe; border-radius: 2px;"></div>
                                <div style="flex: 1; height: 24px; background: #dbeafe; border-radius: 2px;"></div>
                                <div style="flex: 1; height: 20px; background: #dbeafe; border-radius: 2px;"></div>
                                <div style="flex: 1; height: 32px; background: #3b82f6; border-radius: 2px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Health -->
                <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0;">Platform Health</h3>
                        <i class="fas fa-heartbeat" style="color: #ef4444; font-size: 18px;"></i>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Ad Approval Rate</span>
                            <span style="font-size: 16px; font-weight: 800; color: #16a34a;">94.2%</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Avg. Review Time</span>
                            <span style="font-size: 14px; font-weight: 800; color: #1e293b;">2.3 hrs</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; color: #64748b; font-weight: 600;">Violation Rate</span>
                            <span style="font-size: 14px; font-weight: 800; color: #ef4444;">1.8%</span>
                        </div>
                        <div
                            style="margin-top: 8px; background: #f0fdf4; border-radius: 12px; padding: 12px; display: flex; align-items: center; gap: 10px; border: 1px solid #dcfce7;">
                            <div style="width: 8px; height: 8px; background: #16a34a; border-radius: 50%;"></div>
                            <span style="font-size: 13px; font-weight: 700; color: #16a34a;">All Systems Operational</span>
                        </div>
                    </div>
                </div>
            </div>
              <!-- Section 5: Recent Platform Activity -->
            <div
                style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 32px; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">Recent Platform Activity</h2>
                    <a href="#" style="font-size: 14px; font-weight: 700; color: #8b5cf6; text-decoration: none;">View All
                        Activity</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Activity 1 -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div
                            style="width: 36px; height: 36px; background: #f0fdf4; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-check" style="font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Campaign Approved</div>
                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">2 min ago</div>
                            </div>
                            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">"Holiday Gift Guide 2024" by Home
                                Essentials has been approved and is now live</div>
                        </div>
                    </div>

                    <!-- Activity 2 -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div
                            style="width: 36px; height: 36px; background: #eff6ff; color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-dollar-sign" style="font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">New Advertiser Registered
                                </div>
                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">15 min ago</div>
                            </div>
                            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">Premium Electronics Store has
                                joined the platform and set up their first campaign</div>
                        </div>
                    </div>

                    <!-- Activity 3 -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div
                            style="width: 36px; height: 36px; background: #fffbeb; color: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-flag" style="font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Campaign Flagged for Review
                                </div>
                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">1 hour ago</div>
                            </div>
                            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">"Summer Collection Launch"
                                requires manual review due to creative content guidelines</div>
                        </div>
                    </div>

                    <!-- Activity 4 -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div
                            style="width: 36px; height: 36px; background: #f5f3ff; color: #8b5cf6; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-sliders-h" style="font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Platform Settings Updated
                                </div>
                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">3 hours ago</div>
                            </div>
                            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">Minimum CPC bid for Sponsored
                                Products adjusted from $0.20 to $0.25</div>
                        </div>
                    </div>

                    <!-- Activity 5 -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div
                            style="width: 36px; height: 36px; background: #fef2f2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-times" style="font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Campaign Rejected</div>
                                <div style="font-size: 12px; color: #94a3b8; font-weight: 600;">5 hours ago</div>
                            </div>
                            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">"Clearance Sale" by Fashion Hub
                                rejected due to prohibited discount claims</div>
                        </div>
                    </div>
                </div>
            </div>
             <!-- Section: Quick Actions Banner -->
        <div
            style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border-radius: 16px; padding: 24px; margin-bottom: 32px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">
                <div>
                    <h2 style="font-size: 20px; font-weight: 800; margin: 0 0 4px 0;">Quick Actions</h2>
                    <p style="font-size: 14px; opacity: 0.9; margin: 0;">Streamline your advertising management workflow</p>
                </div>
                <div
                    style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bolt" style="font-size: 20px;"></i>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 24px;">
                <button
                    style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: white; padding: 16px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; font-size: 14px;">
                    <i class="fas fa-plus"></i> Create Campaign
                </button>
                <button
                    style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: white; padding: 16px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; font-size: 14px;">
                    <i class="fas fa-eye"></i> Review Queue
                </button>
                <button
                    style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: white; padding: 16px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; font-size: 14px;">
                    <i class="fas fa-chart-line"></i> View Reports
                </button>
                <a href="{{ route('new-screens.platform-settings') }}"
                    style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: white; padding: 16px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; font-size: 14px; text-decoration: none;">
                    <i class="fas fa-cog"></i> Manage Settings
                </a>
            </div>
        </div>
        </div>
    </div>
@endsection
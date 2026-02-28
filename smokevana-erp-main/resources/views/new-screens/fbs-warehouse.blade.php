@extends('layouts.app')
@section('title', 'FBS Warehouse')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Network Overview Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Network Overview</h2>
                <div style="font-size: 12px; color: #6b7280;">Last updated: 2 minutes ago</div>
            </div>

            <!-- Metric Cards -->
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; flex-wrap: wrap;">
                <!-- Total Warehouses Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; flex: 1; min-width: 180px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-warehouse" style="color: #3b82f6; font-size: 20px;"></i>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Warehouses</div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">4</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; font-weight: 500; color: #10b981;">100% operational</span>
                    </div>
                </div>

                <!-- Units in Storage Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; flex: 1; min-width: 180px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-boxes" style="color: #10b981; font-size: 20px;"></i>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Units in Storage</div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">124,500</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; font-weight: 500; color: #10b981;">12.3% vs last week</span>
                    </div>
                </div>

                <!-- Orders Fulfilled Today Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; flex: 1; min-width: 180px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-truck" style="color: #10b981; font-size: 20px;"></i>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Orders Fulfilled Today</div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">287</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; font-weight: 500; color: #10b981;">8.5% vs yesterday</span>
                    </div>
                </div>

                <!-- FBS Revenue (Month) Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; flex: 1; min-width: 180px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-dollar-sign" style="color: #a855f7; font-size: 20px;"></i>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">FBS Revenue (Month)</div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">$7,840</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; font-weight: 500; color: #10b981;">15.2% vs last month</span>
                    </div>
                </div>

                <!-- Avg Ship Time Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; flex: 1; min-width: 180px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock" style="color: #10b981; font-size: 20px;"></i>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Avg Ship Time</div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">1.8 days</div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <i class="fas fa-plus" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; font-weight: 500; color: #10b981;">0.2 days improved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouse Facilities Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">Warehouse Facilities</h2>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <select style="padding: 8px 32px 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                        <option>Filter</option>
                        <option>All Warehouses</option>
                        <option>Operational</option>
                        <option>Under Maintenance</option>
                    </select>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plus" style="font-size: 12px;"></i>
                        <span>Add Warehouse</span>
                    </button>
                </div>
            </div>

            <!-- Warehouse Cards Grid -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <!-- FBS Los Angeles Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; border-left: 4px solid #3b82f6;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">FBS Los Angeles</h3>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">3450 E Spring St, Long Beach, CA 90806</div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                <span style="font-size: 12px; color: #10b981; font-weight: 500;">Operational</span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Units Stored</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">42,340</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Orders Today</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">98</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Avg Ship Time</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">1.5d</div>
                        </div>
                    </div>

                    <!-- Secondary Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Capacity Used</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">84%</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Staff on Shift</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">24</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Compliance</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">12</div>
                        </div>
                    </div>

                    <!-- Daily Order Volume Chart -->
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Daily Order Volume (Last 7 Days)</div>
                        <div style="display: flex; align-items: end; justify-content: space-between; gap: 4px; height: 80px;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 60%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Mon</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 65%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Tue</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 55%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Wed</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 75%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Thu</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 65%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Fri</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 75%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sat</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #3b82f6; border-radius: 4px 4px 0 0; height: 80%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sun</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 8px;">
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #3b82f6; border: none; border-radius: 6px; cursor: pointer;">View Dashboard</button>
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Inventory</button>
                    </div>
                </div>

                <!-- FBS New York Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; border-left: 4px solid #10b981;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">FBS New York</h3>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">850 3rd Ave, Brooklyn, NY 11232</div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                <span style="font-size: 12px; color: #10b981; font-weight: 500;">Operational</span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Units Stored</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">38,920</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Orders Today</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">87</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Avg Ship Time</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">1.6d</div>
                        </div>
                    </div>

                    <!-- Secondary Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Capacity Used</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">73%</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Staff on Shift</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">21</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Compliance</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">10</div>
                        </div>
                    </div>

                    <!-- Daily Order Volume Chart -->
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Daily Order Volume (Last 7 Days)</div>
                        <div style="display: flex; align-items: end; justify-content: space-between; gap: 4px; height: 80px;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 50%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Mon</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 55%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Tue</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 45%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Wed</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 60%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Thu</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 55%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Fri</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 60%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sat</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #10b981; border-radius: 4px 4px 0 0; height: 65%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sun</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 8px;">
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">View Dashboard</button>
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #10b981; background-color: #ffffff; border: 1px solid #10b981; border-radius: 6px; cursor: pointer;">View Inventory</button>
                    </div>
                </div>

                <!-- FBS Dallas Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; border-left: 4px solid #f97316;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">FBS Dallas</h3>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">2550 Westport Pkwy, Fort Worth, TX 76177</div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                <span style="font-size: 12px; color: #10b981; font-weight: 500;">Operational</span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Units Stored</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">28,150</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Orders Today</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">64</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Avg Ship Time</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">2.1d</div>
                        </div>
                    </div>

                    <!-- Secondary Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Capacity Used</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">68%</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Staff on Shift</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">18</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Compliance</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">8</div>
                        </div>
                    </div>

                    <!-- Daily Order Volume Chart -->
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Daily Order Volume (Last 7 Days)</div>
                        <div style="display: flex; align-items: end; justify-content: space-between; gap: 4px; height: 80px;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 45%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Mon</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 50%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Tue</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 40%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Wed</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 55%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Thu</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 50%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Fri</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 55%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sat</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #f97316; border-radius: 4px 4px 0 0; height: 60%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sun</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 8px;">
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">View Dashboard</button>
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #f97316; background-color: #ffffff; border: 1px solid #f97316; border-radius: 6px; cursor: pointer;">View Inventory</button>
                    </div>
                </div>

                <!-- FBS Seattle Card -->
                <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; border-left: 4px solid #14b8a6;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">FBS Seattle</h3>
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">19800 28th Ave S, SeaTac, WA 98188</div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                <span style="font-size: 12px; color: #10b981; font-weight: 500;">Operational</span>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Units Stored</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">15,090</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Orders Today</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">38</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Avg Ship Time</div>
                            <div style="font-size: 20px; font-weight: 700; color: #111827;">2.3d</div>
                        </div>
                    </div>

                    <!-- Secondary Metrics -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Capacity Used</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">61%</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Staff on Shift</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">12</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Compliance</div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">6</div>
                        </div>
                    </div>

                    <!-- Daily Order Volume Chart -->
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">Daily Order Volume (Last 7 Days)</div>
                        <div style="display: flex; align-items: end; justify-content: space-between; gap: 4px; height: 80px;">
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 25%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Mon</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 30%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Tue</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 20%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Wed</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 35%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Thu</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 30%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Fri</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 35%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sat</span>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <div style="width: 100%; background-color: #14b8a6; border-radius: 4px 4px 0 0; height: 40%; min-height:50px;"></div>
                                <span style="font-size: 10px; color: #6b7280;">Sun</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 8px;">
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #14b8a6; border: none; border-radius: 6px; cursor: pointer;">View Dashboard</button>
                        <button style="flex: 1; padding: 10px 16px; font-size: 14px; font-weight: 500; color: #14b8a6; background-color: #ffffff; border: 1px solid #14b8a6; border-radius: 6px; cursor: pointer;">View Inventory</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- create new ui layout as per the ss -->
    
    <!-- FBS Fee Revenue Breakdown Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">FBS Fee Revenue Breakdown</h2>
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Detailed revenue analysis by fee category.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download" style="font-size: 12px;"></i>
                        <span>Export</span>
                    </button>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer;">View Details</button>
                </div>
            </div>

            <!-- Revenue Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">FEE TYPE</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">THIS MONTH</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">LAST MONTH</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">CHANGE %</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">YTD TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1: Fulfillment Fees -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Fulfillment Fees</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$4,320</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #374151;">$3,850</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                                    <span style="font-size: 14px; font-weight: 500; color: #10b981;">12.2%</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$48,640</td>
                        </tr>
                        
                        <!-- Row 2: Storage Fees -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Storage Fees</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$2,100</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #374151;">$1,920</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                                    <span style="font-size: 14px; font-weight: 500; color: #10b981;">9.4%</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$23,520</td>
                        </tr>
                        
                        <!-- Row 3: Inbound Receiving -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Inbound Receiving</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$840</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #374151;">$750</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                                    <span style="font-size: 14px; font-weight: 500; color: #10b981;">12.0%</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$9,480</td>
                        </tr>
                        
                        <!-- Row 4: Returns Processing -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Returns Processing</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$420</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #374151;">$380</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                                    <span style="font-size: 14px; font-weight: 500; color: #10b981;">10.5%</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$4,740</td>
                        </tr>
                        
                        <!-- Row 5: Long-term Storage -->
                        <tr style="border-bottom: 2px solid #e5e7eb;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Long-term Storage</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$160</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #374151;">$140</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                                    <span style="font-size: 14px; font-weight: 500; color: #10b981;">14.3%</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$1,800</td>
                        </tr>
                        
                        <!-- Total Row -->
                        <tr style="background-color: #f9fafb;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 700;">Total</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 700;">$7,840</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 700;">$7,040</td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                                    <span style="font-size: 14px; font-weight: 700; color: #10b981;">11.4%</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 700;">$88,180</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FBS Performance Metrics Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">FBS Performance Metrics</h2>
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Real-time operational excellence indicators.</p>
                </div>
                <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-download" style="font-size: 12px;"></i>
                    <span>Download Report</span>
                </button>
            </div>

            <!-- Performance Metrics Grid -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                <!-- Pick Accuracy -->
                <div style="background-color: #eefdf4; border: 1px solid #10b981; border-radius: 8px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Pick Accuracy</div>
                        <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">99.4%</div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                            <span style="font-size: 12px; color: #10b981;">0.2% vs last week</span>
                        </div>
                    </div>
                    <div style="position: relative; width: 120px; height: 120px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: visible;">
                        <canvas id="pickAccuracyGauge" width="100" height="100" style="transform: rotate(-90deg);"></canvas>
                    </div>
                </div>

                <!-- Pack Accuracy -->
                <div style="background-color: #eefdf4; border: 1px solid #10b981; border-radius: 8px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Pack Accuracy</div>
                        <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">99.7%</div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                            <span style="font-size: 12px; color: #10b981;">0.1% vs last week</span>
                        </div>
                    </div>
                    <div style="position: relative; width: 120px; height: 120px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: visible;">
                        <canvas id="packAccuracyGauge" width="100" height="100" style="transform: rotate(-90deg);"></canvas>
                    </div>
                </div>

                <!-- Ship-on-Time -->
                <div style="background-color: #eefdf4; border: 1px solid #10b981; border-radius: 8px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Ship-on-Time</div>
                        <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">97.2%</div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                            <span style="font-size: 12px; color: #10b981;">1.8% vs last week</span>
                        </div>
                    </div>
                    <div style="position: relative; width: 120px; height: 120px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: visible;">
                        <canvas id="shipOnTimeGauge" width="100" height="100" style="transform: rotate(-90deg);"></canvas>
                    </div>
                </div>

                <!-- Compliance Pass -->
                <div style="background-color: #eefdf4; border: 1px solid #10b981; border-radius: 8px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Compliance Pass</div>
                        <div style="font-size: 32px; font-weight: 700; color: #10b981; margin-bottom: 8px;">98.8%</div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                            <span style="font-size: 12px; color: #10b981;">0.4% vs last week</span>
                        </div>
                    </div>
                    <div style="position: relative; width: 120px; height: 120px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: visible;">
                        <canvas id="compliancePassGauge" width="100" height="100" style="transform: rotate(-90deg);"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inbound Shipment Queue Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Inbound Shipment Queue</h2>
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Pending seller shipments to FBS warehouses.</p>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 200px;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;"></i>
                    <input type="text" placeholder="Search shipments..." style="width: 100%; padding: 10px 12px 10px 36px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#d1d5db';">
                </div>
                <button style="padding: 10px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-filter" style="font-size: 12px;"></i>
                    <span>Filter</span>
                </button>
            </div>

            <!-- Shipment Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">SELLER NAME</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">SHIPMENT ID</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">DESTINATION</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">EXPECTED UNITS</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">STATUS</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ETA</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1: TechMart LLC -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">TM</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">TechMart LLC</div>
                                        <div style="font-size: 12px; color: #6b7280;">ID: SLR-10234</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">SHIP-2024-8847</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-warehouse" style="color: #3b82f6; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #111827;">FBS Los Angeles</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">1,250</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">In Transit</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; color: #111827; font-weight: 500;">Jan 15, 2024</div>
                                <div style="font-size: 12px; color: #6b7280;">2 days remaining</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">Accept</button>
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">Flag</button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Row 2: HomeGoods Plus -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">HG</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">HomeGoods Plus</div>
                                        <div style="font-size: 12px; color: #6b7280;">ID: SLR-10189</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">SHIP-2024-0846</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-warehouse" style="color: #10b981; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #111827;">FBS New York</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">890</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Arrived</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; color: #111827; font-weight: 500;">Jan 13, 2024</div>
                                <div style="font-size: 12px; color: #10b981;">On time</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">Accept</button>
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">Flag</button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Row 3: Fashion Street Co -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #f97316; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">FS</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Fashion Street Co</div>
                                        <div style="font-size: 12px; color: #6b7280;">ID: SLR-10456</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">SHIP-2024-0845</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-warehouse" style="color: #f97316; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #111827;">FBS Dallas</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">2,340</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Receiving</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; color: #111827; font-weight: 500;">Jan 13, 2024</div>
                                <div style="font-size: 12px; color: #f59e0b;">In progress</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">Accept</button>
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">Flag</button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Row 4: Beauty Essentials -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">BE</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Beauty Essentials</div>
                                        <div style="font-size: 12px; color: #6b7280;">ID: SLR-10782</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">SHIP-2024-0844</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-warehouse" style="color: #3b82f6; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #111827;">FBS Los Angeles</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">670</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Scheduled</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; color: #111827; font-weight: 500;">Jan 18, 2024</div>
                                <div style="font-size: 12px; color: #6b7280;">5 days remaining</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">Accept</button>
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">Flag</button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Row 5: Outdoor Gear Pro -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">OG</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Outdoor Gear Pro</div>
                                        <div style="font-size: 12px; color: #6b7280;">ID: SLR-10923</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">SHIP-2024-0843</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-warehouse" style="color: #14b8a6; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #111827;">FBS Seattle</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">1,580</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">In Transit</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; color: #111827; font-weight: 500;">Jan 16, 2024</div>
                                <div style="font-size: 12px; color: #6b7280;">3 days remaining</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">Accept</button>
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">Flag</button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Row 6: Kids & Baby Store -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background-color: #ec4899; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">KB</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Kids & Baby Store</div>
                                        <div style="font-size: 12px; color: #6b7280;">ID: SLR-10567</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">SHIP-2024-0842</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-warehouse" style="color: #10b981; font-size: 16px;"></i>
                                    <span style="font-size: 14px; color: #111827;">FBS New York</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">3,120</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Scheduled</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; color: #111827; font-weight: 500;">Jan 20, 2024</div>
                                <div style="font-size: 12px; color: #6b7280;">7 days remaining</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer;">Accept</button>
                                    <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">Flag</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 14px; color: #374151;">
                    Showing <span style="font-weight: 600; color: #111827;">6</span> of <span style="font-weight: 600; color: #111827;">24</span> shipments
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">Previous</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #a855f7; border: 1px solid #a855f7; border-radius: 6px; cursor: pointer;">1</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">2</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">3</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- create new ui layout as per the ss -->
    
    <!-- Staff Distribution Section -->
    <div style="background-color: #ffffff; margin-top:25px; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Staff Distribution</h2>
                <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer;">Manage Staff</button>
            </div>

            <!-- Warehouse Cards Grid -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                <!-- FBS Los Angeles -->
                <div style="background-color: #dbeafe; border-radius: 8px; padding: 20px; position: relative;">
                    <div style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">LA</div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <i class="fas fa-warehouse" style="color: #3b82f6; font-size: 24px;"></i>
                        <div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">FBS Los Angeles</div>
                        </div>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">24</div>
                    <div style="font-size: 12px; color: #6b7280;">Active staff members</div>
                </div>

                <!-- FBS New York -->
                <div style="background-color: #d1fae5; border-radius: 8px; padding: 20px; position: relative;">
                    <div style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">NY</div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <i class="fas fa-warehouse" style="color: #10b981; font-size: 24px;"></i>
                        <div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">FBS New York</div>
                        </div>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">21</div>
                    <div style="font-size: 12px; color: #6b7280;">Active staff members</div>
                </div>

                <!-- FBS Dallas -->
                <div style="background-color: #fed7aa; border-radius: 8px; padding: 20px; position: relative;">
                    <div style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background-color: #f97316; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">TX</div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <i class="fas fa-warehouse" style="color: #f97316; font-size: 24px;"></i>
                        <div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">FBS Dallas</div>
                        </div>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">18</div>
                    <div style="font-size: 12px; color: #6b7280;">Active staff members</div>
                </div>

                <!-- FBS Seattle -->
                <div style="background-color: #d1fae5; border-radius: 8px; padding: 20px; position: relative;">
                    <div style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background-color: #14b8a6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">WA</div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <i class="fas fa-warehouse" style="color: #14b8a6; font-size: 24px;"></i>
                        <div>
                            <div style="font-size: 16px; font-weight: 600; color: #111827;">FBS Seattle</div>
                        </div>
                    </div>
                    <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">12</div>
                    <div style="font-size: 12px; color: #6b7280;">Active staff members</div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts & Notifications Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">System Alerts & Notifications</h2>
                <a href="#" style="font-size: 14px; color: #a855f7; font-weight: 500; text-decoration: none;">View All</a>
            </div>

            <!-- Notification Cards -->
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <!-- High Capacity Alert -->
                <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 16px; display: flex; align-items: start; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f59e0b; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 18px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">High Capacity Alert - FBS Los Angeles</div>
                        <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Warehouse capacity at 84%. Consider redistributing inventory or expanding storage.</div>
                        <div style="font-size: 12px; color: #6b7280;">2 hours ago</div>
                    </div>
                    <button style="background: none; border: none; color: #6b7280; cursor: pointer; padding: 4px; flex-shrink: 0;" onclick="this.closest('div[style*=\"background-color: #fef3c7\"]').style.display='none';">
                        <i class="fas fa-times" style="font-size: 14px;"></i>
                    </button>
                </div>

                <!-- Compliance Check Completed -->
                <div style="background-color: #d1fae5; border-left: 4px solid #10b981; border-radius: 6px; padding: 16px; display: flex; align-items: start; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-check-circle" style="font-size: 18px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Compliance Check Completed - All Facilities</div>
                        <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Monthly compliance audit passed with 98.8% success rate across all warehouses.</div>
                        <div style="font-size: 12px; color: #6b7280;">5 hours ago</div>
                    </div>
                    <button style="background: none; border: none; color: #6b7280; cursor: pointer; padding: 4px; flex-shrink: 0;" onclick="this.closest('div[style*=\"background-color: #d1fae5\"]').style.display='none';">
                        <i class="fas fa-times" style="font-size: 14px;"></i>
                    </button>
                </div>

                <!-- New Shipment Arrived -->
                <div style="background-color: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 6px; padding: 16px; display: flex; align-items: start; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-info-circle" style="font-size: 18px;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">New Shipment Arrived - FBS New York</div>
                        <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Shipment SHIP-2024-0846 from HomeGoods Plus has arrived and is ready for receiving.</div>
                        <div style="font-size: 12px; color: #6b7280;">8 hours ago</div>
                    </div>
                    <button style="background: none; border: none; color: #6b7280; cursor: pointer; padding: 4px; flex-shrink: 0;" onclick="this.closest('div[style*=\"background-color: #dbeafe\"]').style.display='none';">
                        <i class="fas fa-times" style="font-size: 14px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #ffffff; margin: 0 0 20px 0;">Quick Actions</h2>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <!-- Add Warehouse -->
            <button style="padding: 16px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 12px rgba(0,0,0,0.15)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">
                <i class="fas fa-plus" style="color: #ffffff; font-size: 24px;"></i>
                <span style="font-size: 14px; font-weight: 500; color: #ffffff;">Add Warehouse</span>
            </button>

            <!-- Export Report -->
            <button style="padding: 16px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 12px rgba(0,0,0,0.15)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">
                <i class="fas fa-file-export" style="color: #ffffff; font-size: 24px;"></i>
                <span style="font-size: 14px; font-weight: 500; color: #ffffff;">Export Report</span>
            </button>

            <!-- Add Staff -->
            <button style="padding: 16px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 12px rgba(0,0,0,0.15)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">
                <i class="fas fa-user-plus" style="color: #ffffff; font-size: 24px;"></i>
                <span style="font-size: 14px; font-weight: 500; color: #ffffff;">Add Staff</span>
            </button>

            <!-- Settings -->
            <button style="padding: 16px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 12px rgba(0,0,0,0.15)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';">
                <i class="fas fa-cog" style="color: #ffffff; font-size: 24px;"></i>
                <span style="font-size: 14px; font-weight: 500; color: #ffffff;">Settings</span>
            </button>
        </div>
    </div>
</div>

<script>
    // Draw circular progress gauges for Performance Metrics
    function drawGauge(canvasId, percentage, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 42;
        const lineWidth = 10;
        
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

    // Initialize gauges when page loads
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            drawGauge('pickAccuracyGauge', 99.4, '#10b981');
            drawGauge('packAccuracyGauge', 99.7, '#10b981');
            drawGauge('shipOnTimeGauge', 97.2, '#10b981');
            drawGauge('compliancePassGauge', 98.8, '#10b981');
        }, 100);
    });
</script>
@endsection


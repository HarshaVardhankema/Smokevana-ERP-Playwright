@extends('layouts.app')
@section('title', 'Compliance Center')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Top Row - KPI Cards with Circular Gauges -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <!-- Platform Compliance Score -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                <canvas id="platformComplianceChart" width="120" height="120" style="max-width: 120px; max-height: 120px; margin-bottom: 12px;"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; margin-top: -12px;">
                    <div style="font-size: 24px; font-weight: 700; color: #10b981;">94%</div>
                </div>
                <div style="text-align: center; margin-top: 12px;">
                    <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Platform Compliance Score</div>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; color: #10b981; font-weight: 500;">+1.2% this month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seller Compliance Avg -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                <canvas id="sellerComplianceChart" width="120" height="120" style="max-width: 120px; max-height: 120px; margin-bottom: 12px;"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; margin-top: -12px;">
                    <div style="font-size: 24px; font-weight: 700; color: #10b981;">91%</div>
                </div>
                <div style="text-align: center; margin-top: 12px;">
                    <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Seller Compliance Avg</div>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                        <i class="fas fa-arrow-up" style="color: #10b981; font-size: 12px;"></i>
                        <span style="font-size: 12px; color: #10b981; font-weight: 500;">+0.8% this month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Coverage -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                <canvas id="licenseCoverageChart" width="120" height="120" style="max-width: 120px; max-height: 120px; margin-bottom: 12px;"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; margin-top: -12px;">
                    <div style="font-size: 24px; font-weight: 700; color: #10b981;">97%</div>
                </div>
                <div style="text-align: center; margin-top: 12px;">
                    <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">License Coverage</div>
                    <div style="font-size: 12px; color: #6b7280;">% sellers with valid licenses</div>
                </div>
            </div>
        </div>

        <!-- COA Coverage -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                <canvas id="coaCoverageChart" width="120" height="120" style="max-width: 120px; max-height: 120px; margin-bottom: 12px;"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; margin-top: -12px;">
                    <div style="font-size: 24px; font-weight: 700; color: #f97316;">89%</div>
                </div>
                <div style="text-align: center; margin-top: 12px;">
                    <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">COA Coverage</div>
                    <div style="font-size: 12px; color: #6b7280;">% products with valid COAs</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Alerts Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-bell" style="color: #6b7280; font-size: 20px;"></i>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Compliance Alerts</h2>
                    <span style="background-color: #ef4444; color: #ffffff; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">14 Active Alerts</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <select style="padding: 8px 32px 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                        <option>Priority: All</option>
                        <option>Priority: Critical</option>
                        <option>Priority: High</option>
                        <option>Priority: Medium</option>
                    </select>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download" style="font-size: 12px;"></i>
                        <span>Export</span>
                    </button>
                </div>
            </div>

            <!-- Alerts List -->
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Alert 1 - CRITICAL -->
                <div style="border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #ef4444; border-radius: 25px;">CRITICAL</span>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 12px 0;">Seller XYZ - CA wholesale license expired 5 days ago</h3>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-circle" style="color: #ef4444; font-size: 6px;"></i>
                                    <span>12 products still active on marketplace despite expired license</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-briefcase" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>Pacific Smoke Distributors</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-clock" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>5 days open</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 200px;">
                            <select style="padding: 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none;">
                                <option>Assigned: Auto</option>
                            </select>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Resolve</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Escalate</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Suspend Seller</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #374151; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Override</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert 2 - HIGH -->
                <div style="border-left: 4px solid #f97316; background-color: #fff7ed; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #f97316; border-radius: 25px;">HIGH</span>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 12px 0;">3 products from Seller ABC missing COA documentation</h3>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-circle" style="color: #f97316; font-size: 6px;"></i>
                                    <span>Required for Hemp/CBD category compliance</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-briefcase" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>GreenLeaf Wholesale</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-clock" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>3 days open</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 200px;">
                            <select style="padding: 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none;">
                                <option>Assigned: Sarah J.</option>
                            </select>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Resolve</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Escalate</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Suspend Seller</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #374151; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Override</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert 3 - HIGH -->
                <div style="border-left: 4px solid #f97316; background-color: #fff7ed; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #f97316; border-radius: 25px;">HIGH</span>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 12px 0;">Seller VapeCo PACT Act monthly report overdue (January 2026)</h3>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-circle" style="color: #f97316; font-size: 6px;"></i>
                                    <span>Federal reporting requirement for vape products</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-briefcase" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>VaporWave Distribution</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-clock" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>12 days open</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 200px;">
                            <select style="padding: 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none;">
                                <option>Assigned: Michael C.</option>
                            </select>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Resolve</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Escalate</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Suspend Seller</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #374151; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Override</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert 4 - MEDIUM -->
                <div style="border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #f59e0b; border-radius: 25px;">MEDIUM</span>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 12px 0;">14 COAs expiring within 30 days across 8 sellers</h3>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-circle" style="color: #f59e0b; font-size: 6px;"></i>
                                    <span>Proactive renewal reminders required</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-users" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>Multiple sellers affected</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-clock" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>2 days open</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 200px;">
                            <select style="padding: 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none;">
                                <option>Assigned: Emily R.</option>
                            </select>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Resolve</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Escalate</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Suspend Seller</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #374151; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Override</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert 5 - MEDIUM -->
                <div style="border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; color: #ffffff; background-color: #f59e0b; border-radius: 25px;">MEDIUM</span>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 12px 0;">NY state license renewal pending for 2 sellers</h3>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-circle" style="color: #f59e0b; font-size: 6px;"></i>
                                    <span>Applications submitted, awaiting state approval</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-users" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>2 sellers affected</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #374151;">
                                    <i class="fas fa-clock" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>7 days open</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; min-width: 200px;">
                            <select style="padding: 8px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none;">
                                <option>Assigned: Sarah J.</option>
                            </select>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #10b981; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Resolve</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Escalate</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Suspend Seller</button>
                                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #374151; border: none; border-radius: 6px; cursor: pointer; flex: 1; min-width: 80px;">Override</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 14px; color: #374151;">
                    Showing <span style="font-weight: 600; color: #111827;">5</span> of <span style="font-weight: 600; color: #111827;">14</span> alerts
                </div>
                <a href="#" style="font-size: 14px; font-weight: 500; color: #3b82f6; text-decoration: none;">View All Alerts</a>
            </div>
        </div>
    </div>

    <!-- create new ui layout as per the ss -->
    
    <!-- Top Row - License Expiration Timeline and Regulatory Updates -->
    <div style="display: grid; margin-top:25px; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <!-- License Expiration Timeline -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">License Expiration Timeline</h3>
            <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px 0;">Next 90 days</p>
            
            <!-- Timeline -->
            <div style="position: relative; height: 40px; margin-bottom: 30px; background-color: #f3f4f6; border-radius: 4px; padding: 8px;">
                <div style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 4px; height: 20px; background-color: #3b82f6; border-radius: 2px;"></div>
                <div style="position: absolute; left: 33.33%; top: 50%; transform: translateY(-50%); width: 4px; height: 20px; background-color: #f59e0b; border-radius: 2px;"></div>
                <div style="position: absolute; left: 66.66%; top: 50%; transform: translateY(-50%); width: 4px; height: 20px; background-color: #f59e0b; border-radius: 2px;"></div>
                <div style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 4px; height: 20px; background-color: #ef4444; border-radius: 2px;"></div>
                <div style="position: absolute; left: 0; top: -20px; font-size: 12px; color: #6b7280;">Today</div>
                <div style="position: absolute; left: 33.33%; top: -20px; font-size: 12px; color: #6b7280; transform: translateX(-50%);">30 days</div>
                <div style="position: absolute; left: 66.66%; top: -20px; font-size: 12px; color: #6b7280; transform: translateX(-50%);">60 days</div>
                <div style="position: absolute; right: 0; top: -20px; font-size: 12px; color: #6b7280;">90 days</div>
            </div>

            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0;">Licenses Expiring Within 60 Days</h4>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <!-- License 1 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Pacific Smoke Distributors</div>
                        <div style="font-size: 12px; color: #6b7280;">CA Wholesale License • Expires in <span style="color: #ef4444; font-weight: 600;">3 days</span> (Feb 12, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
                <!-- License 2 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">GreenLeaf Wholesale</div>
                        <div style="font-size: 12px; color: #6b7280;">NY Hemp License • Expires in <span style="color: #f59e0b; font-weight: 600;">12 days</span> (Feb 21, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
                <!-- License 3 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">VaporWave Distribution</div>
                        <div style="font-size: 12px; color: #6b7280;">FL Vape Retailer License • Expires in <span style="color: #f59e0b; font-weight: 600;">18 days</span> (Feb 27, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
                <!-- License 4 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">HighTide Supply Co</div>
                        <div style="font-size: 12px; color: #6b7280;">TX Tobacco Permit • Expires in <span style="color: #f59e0b; font-weight: 600;">28 days</span> (Mar 09, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
                <!-- License 5 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Premier Tobacco Wholesale</div>
                        <div style="font-size: 12px; color: #6b7280;">CO Tobacco License • Expires in <span style="color: #f59e0b; font-weight: 600;">35 days</span> (Mar 16, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
                <!-- License 6 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Rocky Mountain Hemp Co</div>
                        <div style="font-size: 12px; color: #6b7280;">CA Hemp Manufacturer • Expires in <span style="color: #f59e0b; font-weight: 600;">45 days</span> (Mar 26, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
                <!-- License 7 -->
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Golden State Tobacco</div>
                        <div style="font-size: 12px; color: #6b7280;">MA Tobacco Distributor • Expires in <span style="color: #f59e0b; font-weight: 600;">58 days</span> (Apr 08, 2026)</div>
                    </div>
                    <button style="padding: 6px 16px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">Contact Seller</button>
                </div>
            </div>
        </div>

        <!-- Regulatory Updates Feed -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Regulatory Updates Feed</h3>
            <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px 0;">Recent changes by state</p>
            
            <div style="display: flex; flex-direction: column; gap: 16px; max-height: 600px; overflow-y: auto;">
                <!-- Update 1 -->
                <div style="border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: #6b7280;">Feb 05, 2026</span>
                        <span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: #ef4444; border-radius: 12px;">HIGH IMPACT</span>
                        <span style="font-size: 12px; color: #6b7280;">Massachusetts</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">New hemp THC potency limits effective March 1.</h4>
                    <p style="font-size: 13px; color: #374151; margin: 0 0 12px 0;">Maximum THC concentration reduced from 0.3% to 0.2% for hemp products. Affects 23 products from 8 sellers currently listed.</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Official Source</button>
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #8b5cf6; border: none; border-radius: 6px; cursor: pointer;">Update Rules Engine</button>
                    </div>
                </div>
                <!-- Update 2 -->
                <div style="border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: #6b7280;">Feb 03, 2026</span>
                        <span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: #ef4444; border-radius: 12px;">HIGH IMPACT</span>
                        <span style="font-size: 12px; color: #6b7280;">California</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">New packaging requirements for vape products.</h4>
                    <p style="font-size: 13px; color: #374151; margin: 0 0 12px 0;">Child-resistant packaging now mandatory for all disposable vape devices. Implementation deadline: April 1, 2026. Affects 156 products.</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Official Source</button>
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #8b5cf6; border: none; border-radius: 6px; cursor: pointer;">Update Rules Engine</button>
                    </div>
                </div>
                <!-- Update 3 -->
                <div style="border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: #6b7280;">Jan 26, 2026</span>
                        <span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: #f59e0b; border-radius: 12px;">MEDIUM IMPACT</span>
                        <span style="font-size: 12px; color: #6b7280;">New York</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">Updated COA testing requirements for CBD products.</h4>
                    <p style="font-size: 13px; color: #374151; margin: 0 0 12px 0;">Third-party lab testing now required to include heavy metals screening. Existing COAs must be updated by May 1, 2026.</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Official Source</button>
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #8b5cf6; border: none; border-radius: 6px; cursor: pointer;">Update Rules Engine</button>
                    </div>
                </div>
                <!-- Update 4 -->
                <div style="border-left: 4px solid #f59e0b; background-color: #fffbeb; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: #6b7280;">Jan 25, 2026</span>
                        <span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: #f59e0b; border-radius: 12px;">MEDIUM IMPACT</span>
                        <span style="font-size: 12px; color: #6b7280;">Florida</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">New age verification requirements at delivery.</h4>
                    <p style="font-size: 13px; color: #374151; margin: 0 0 12px 0;">Enhanced ID scanning required for all tobacco and vape deliveries. Carriers must implement by March 15, 2026.</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Official Source</button>
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #8b5cf6; border: none; border-radius: 6px; cursor: pointer;">Update Rules Engine</button>
                    </div>
                </div>
                <!-- Update 5 -->
                <div style="border-left: 4px solid #10b981; background-color: #f0fdf4; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: #6b7280;">Jan 22, 2026</span>
                        <span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: #10b981; border-radius: 12px;">LOW IMPACT</span>
                        <span style="font-size: 12px; color: #6b7280;">Texas</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">Annual license fee adjustment.</h4>
                    <p style="font-size: 13px; color: #374151; margin: 0 0 12px 0;">Tobacco distributor license fees increased by 5% for 2026 renewal period. No operational changes required.</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Official Source</button>
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">No Action Required</button>
                    </div>
                </div>
                <!-- Update 6 -->
                <div style="border-left: 4px solid #10b981; background-color: #f0fdf4; border-radius: 6px; padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: #6b7280;">Jan 18, 2026</span>
                        <span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 600; color: #ffffff; background-color: #10b981; border-radius: 12px;">LOW IMPACT</span>
                        <span style="font-size: 12px; color: #6b7280;">Colorado</span>
                    </div>
                    <h4 style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">Updated reporting form templates.</h4>
                    <p style="font-size: 13px; color: #374151; margin: 0 0 12px 0;">New monthly sales report template available. Optional adoption, previous format still accepted through June 2026.</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Official Source</button>
                        <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">No Action Required</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Row - Compliance Audit Tools and Recent Audits -->
    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
        <!-- Compliance Audit Tools -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Compliance Audit Tools</h3>
            <p style="font-size: 14px; color: #6b7280; margin: 0 0 20px 0;">Schedule and run platform-wide compliance scans</p>
            
            <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                <button style="padding: 10px 20px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Schedule Audit</button>
                <button style="padding: 10px 20px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #8b5cf6; border: none; border-radius: 6px; cursor: pointer;">Run Platform-Wide Scan</button>
            </div>

            <!-- Active Scan -->
            <div style="background-color: #eff6ff; border: 1px solid #3b82f6; border-radius: 6px; padding: 16px;">
                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 8px;">Scan In Progress: Platform-Wide License Verification</div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 12px;">Started 12 minutes ago by Sarah Johnson</div>
                <div style="margin-bottom: 8px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                        <span style="font-size: 12px; color: #6b7280;">Progress: 87% complete</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #e5e7eb; border-radius: 4px; overflow: hidden;">
                        <div style="width: 87%; height: 100%; background-color: #3b82f6; border-radius: 4px;"></div>
                    </div>
                </div>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 12px;">Est. completion: 6 minutes</div>
                <button style="padding: 6px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #ffffff; border: 1px solid #3b82f6; border-radius: 6px; cursor: pointer;">View Details</button>
            </div>
        </div>

        <!-- Recent Audits -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Recent Audits</h3>
                <a href="#" style="font-size: 14px; font-weight: 500; color: #3b82f6; text-decoration: none;">View All Audits</a>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 10px; text-align: left; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">AUDIT ID</th>
                            <th style="padding: 10px; text-align: left; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">DATE</th>
                            <th style="padding: 10px; text-align: left; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">TYPE</th>
                            <th style="padding: 10px; text-align: left; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">SCOPE</th>
                            <th style="padding: 10px; text-align: center; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ISSUES</th>
                            <th style="padding: 10px; text-align: center; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">RESOLVED</th>
                            <th style="padding: 10px; text-align: center; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">STATUS</th>
                            <th style="padding: 10px; text-align: center; font-size: 11px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">REPORT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 12px; font-size: 13px; color: #111827;">AUD-2026-0147</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Feb 08, 2026</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Scheduled</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Platform-wide</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #ef4444; font-weight: 600;">23</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #10b981; font-weight: 600;">18</td>
                            <td style="padding: 12px; text-align: center;"><span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 12px;">Complete</span></td>
                            <td style="padding: 12px; text-align: center;"><a href="#" style="color: #3b82f6; text-decoration: none;"><i class="fas fa-download"></i> Download</a></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 12px; font-size: 13px; color: #111827;">AUD-2026-0146</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Feb 07, 2026</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Ad-hoc</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Seller: Pacific Smoke</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #ef4444; font-weight: 600;">5</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #10b981; font-weight: 600;">5</td>
                            <td style="padding: 12px; text-align: center;"><span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 12px;">Complete</span></td>
                            <td style="padding: 12px; text-align: center;"><a href="#" style="color: #3b82f6; text-decoration: none;"><i class="fas fa-download"></i> Download</a></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 12px; font-size: 13px; color: #111827;">AUD-2026-0145</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Feb 05, 2026</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">System</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">COA Validation</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #ef4444; font-weight: 600;">12</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #10b981; font-weight: 600;">8</td>
                            <td style="padding: 12px; text-align: center;"><span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 12px;">Complete</span></td>
                            <td style="padding: 12px; text-align: center;"><a href="#" style="color: #3b82f6; text-decoration: none;"><i class="fas fa-download"></i> Download</a></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 12px; font-size: 13px; color: #111827;">AUD-2026-0144</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Feb 03, 2026</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Scheduled</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Platform-wide</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #ef4444; font-weight: 600;">31</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #10b981; font-weight: 600;">28</td>
                            <td style="padding: 12px; text-align: center;"><span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 12px;">Complete</span></td>
                            <td style="padding: 12px; text-align: center;"><a href="#" style="color: #3b82f6; text-decoration: none;"><i class="fas fa-download"></i> Download</a></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 12px; font-size: 13px; color: #111827;">AUD-2026-0143</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Feb 01, 2026</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Ad-hoc</td>
                            <td style="padding: 12px; font-size: 13px; color: #374151;">Seller: VaporWave</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #ef4444; font-weight: 600;">7</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px; color: #10b981; font-weight: 600;">7</td>
                            <td style="padding: 12px; text-align: center;"><span style="display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 12px;">Complete</span></td>
                            <td style="padding: 12px; text-align: center;"><a href="#" style="color: #3b82f6; text-decoration: none;"><i class="fas fa-download"></i> Download</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Row - Compliance by Category, Top Compliant Sellers, Quick Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
        <!-- Compliance by Category -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Compliance by Category</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Hemp/CBD -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Hemp/CBD</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">92%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 92%; height: 100%; background-color: #10b981; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- Vape Products -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Vape Products</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">87%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 87%; height: 100%; background-color: #3b82f6; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- THC Products -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">THC Products</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">85%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 85%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- Tobacco -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Tobacco</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">98%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 98%; height: 100%; background-color: #10b981; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- Accessories -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Accessories</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">98%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 98%; height: 100%; background-color: #10b981; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Compliant Sellers -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Top Compliant Sellers</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Seller 1 -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 16px; font-weight: 700; color: #f59e0b;">1.</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Golden State Tobacco</span>
                        </div>
                        <div style="font-size: 12px; color: #6b7280; margin-left: 24px;">248 products</div>
                    </div>
                    <span style="font-size: 16px; font-weight: 700; color: #10b981;">100%</span>
                </div>
                <!-- Seller 2 -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 16px; font-weight: 700; color: #6b7280;">2.</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Premier Tobacco</span>
                        </div>
                        <div style="font-size: 12px; color: #6b7280; margin-left: 24px;">192 products</div>
                    </div>
                    <span style="font-size: 16px; font-weight: 700; color: #10b981;">99%</span>
                </div>
                <!-- Seller 3 -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 16px; font-weight: 700; color: #6b7280;">3.</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Northwest Hemp</span>
                        </div>
                        <div style="font-size: 12px; color: #6b7280; margin-left: 24px;">156 products</div>
                    </div>
                    <span style="font-size: 16px; font-weight: 700; color: #10b981;">98%</span>
                </div>
                <!-- Seller 4 -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 16px; font-weight: 700; color: #6b7280;">4.</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">HighTide Supply</span>
                        </div>
                        <div style="font-size: 12px; color: #6b7280; margin-left: 24px;">134 products</div>
                    </div>
                    <span style="font-size: 16px; font-weight: 700; color: #10b981;">97%</span>
                </div>
                <!-- Seller 5 -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 16px; font-weight: 700; color: #6b7280;">5.</span>
                            <span style="font-size: 14px; font-weight: 600; color: #111827;">Rocky Mountain Hemp</span>
                        </div>
                        <div style="font-size: 12px; color: #6b7280; margin-left: 24px;">118 products</div>
                    </div>
                    <span style="font-size: 16px; font-weight: 700; color: #10b981;">96%</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Quick Actions</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px; font-size: 14px; font-weight: 500; color: #111827; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; text-align: left;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-export" style="color: #6b7280;"></i>
                        <span>Export Compliance Report</span>
                    </span>
                    <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                </button>
                <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px; font-size: 14px; font-weight: 500; color: #111827; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; text-align: left;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope" style="color: #6b7280;"></i>
                        <span>Send Bulk Compliance Notice</span>
                    </span>
                    <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                </button>
                <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px; font-size: 14px; font-weight: 500; color: #111827; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; text-align: left;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-cog" style="color: #6b7280;"></i>
                        <span>Update Compliance Rules</span>
                    </span>
                    <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                </button>
                <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px; font-size: 14px; font-weight: 500; color: #111827; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; text-align: left;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-user-check" style="color: #6b7280;"></i>
                        <span>Review Seller Applications</span>
                    </span>
                    <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                </button>
                <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px; font-size: 14px; font-weight: 500; color: #111827; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; text-align: left;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-book" style="color: #6b7280;"></i>
                        <span>View Compliance Documentation</span>
                    </span>
                    <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to draw circular gauge
    function drawGauge(canvasId, percentage, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 50;
        const lineWidth = 12;

        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw background circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.strokeStyle = '#f3f4f6';
        ctx.lineWidth = lineWidth;
        ctx.stroke();

        // Draw progress arc
        const startAngle = -Math.PI / 2;
        const endAngle = startAngle + (2 * Math.PI * (percentage / 100));
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.strokeStyle = color;
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Draw percentage text (optional - we're using HTML overlay instead)
    }

    // Draw all gauges
    drawGauge('platformComplianceChart', 94, '#10b981');
    drawGauge('sellerComplianceChart', 91, '#10b981');
    drawGauge('licenseCoverageChart', 97, '#10b981');
    drawGauge('coaCoverageChart', 89, '#f97316');
});
</script>
@endsection

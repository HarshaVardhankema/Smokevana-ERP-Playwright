@extends('layouts.app')
@section('title', 'Reports & Business Intelligence')

@section('content')
<div
    style="padding: 24px; background-color: #ffffff; min-height: 100vh; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #1e293b;">

    <!-- Header Section -->
    <div
        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; padding: 0 8px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Reports & Business
                Intelligence</h1>
            <p style="font-size: 15px; color: #64748b; margin: 0; font-weight: 500;">
                Comprehensive analytics, pre-built reports, and custom report builder with scheduled exports
            </p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button
                style="padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; font-size: 14px; font-weight: 600; color: #475569; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <i class="fas fa-download" style="color: #64748b;"></i> Export All
            </button>
            <button
                style="padding: 10px 20px; background: #f97316; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2);">
                <i class="fas fa-plus"></i> New Custom Report
            </button>
        </div>
    </div>

    <!-- Four Top Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 40px; padding: 0 8px;">

        <!-- Card 1: Reports Generated -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div
                    style="background: #fff7ed; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #f97316;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div
                    style="background: #dcfce7; color: #16a34a; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 12px;">
                    +12%
                </div>
            </div>
            <div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px; line-height: 1;">
                    1,247</div>
                <div style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 4px;">Reports Generated
                </div>
                <div style="font-size: 13px; color: #94a3b8; font-weight: 500;">This month</div>
            </div>
        </div>

        <!-- Card 2: Scheduled Reports -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div
                    style="background: #f5f3ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                    <i class="fas fa-clock"></i>
                </div>
                <div
                    style="background: #eff6ff; color: #2563eb; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 12px;">
                    Active
                </div>
            </div>
            <div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px; line-height: 1;">34
                </div>
                <div style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 4px;">Scheduled Reports
                </div>
                <div style="font-size: 13px; color: #94a3b8; font-weight: 500;">Running automatically</div>
            </div>
        </div>

        <!-- Card 3: Report Subscribers -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div
                    style="background: #eff6ff; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <i class="fas fa-users"></i>
                </div>
                <div
                    style="background: #fff7ed; color: #ea580c; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 12px;">
                    +5
                </div>
            </div>
            <div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px; line-height: 1;">89
                </div>
                <div style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 4px;">Report Subscribers
                </div>
                <div style="font-size: 13px; color: #94a3b8; font-weight: 500;">Receiving automated reports</div>
            </div>
        </div>

        <!-- Card 4: Custom Reports -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div
                    style="background: #dcfce7; width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #22c55e;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div
                    style="background: #f1f5f9; color: #64748b; font-size: 12px; font-weight: 700; padding: 4px 8px; border-radius: 12px;">
                    Custom
                </div>
            </div>
            <div>
                <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 4px; line-height: 1;">156
                </div>
                <div style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 4px;">Custom Reports</div>
                <div style="font-size: 13px; color: #94a3b8; font-weight: 500;">Created by users</div>
            </div>
        </div>

    </div>

    <!-- Section Title: Pre-Built Report Categories -->
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 0 8px;">
        <h2 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">Pre-Built Report Categories</h2>
        <div style="display: flex; gap: 16px; color: #64748b; font-size: 14px; font-weight: 600;">
            <span style="display: flex; align-items: center; gap: 6px; cursor: pointer;"><i class="fas fa-filter"></i>
                Filter</span>
            <span style="display: flex; align-items: center; gap: 6px; cursor: pointer;"><i class="fas fa-sort"></i>
                Sort</span>
        </div>
    </div>

    <!-- Grid of 6 Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; padding: 0 8px;">

        <!-- Platform Revenue Report -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-top: 4px solid #f97316; border-radius: 8px; padding: 32px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center;">
            <div
                style="background: #fff7ed; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #f97316; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0;">Platform Revenue Report
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; font-weight: 500;">
                GMV, fees, take rates, revenue streams breakdown. Exportable to Excel and PDF formats.
            </p>
            <a href="#"
                style="font-size: 14px; font-weight: 700; color: #ea580c; text-decoration: none; margin-top: auto; display: flex; align-items: center; gap: 6px;">
                Generate Report &rarr;
            </a>
        </div>

        <!-- Fee Collection Report -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-top: 4px solid #a855f7; border-radius: 8px; padding: 32px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center;">
            <div
                style="background: #faf5ff; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #a855f7; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-tag"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0;">Fee Collection Report
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; font-weight: 500;">
                All platform fees by category, seller, period. Overdue tracking and payment status.
            </p>
            <a href="#"
                style="font-size: 14px; font-weight: 700; color: #ea580c; text-decoration: none; margin-top: auto; display: flex; align-items: center; gap: 6px;">
                Generate Report &rarr;
            </a>
        </div>

        <!-- Seller Performance Report -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6; border-radius: 8px; padding: 32px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center;">
            <div
                style="background: #eff6ff; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-users"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0;">Seller Performance Report
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; font-weight: 500;">
                Rankings, compliance scores, GMV contribution, defect rates, and performance metrics.
            </p>
            <a href="#"
                style="font-size: 14px; font-weight: 700; color: #ea580c; text-decoration: none; margin-top: auto; display: flex; align-items: center; gap: 6px;">
                Generate Report &rarr;
            </a>
        </div>

        <!-- Buyer Analytics Report -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-top: 4px solid #10b981; border-radius: 8px; padding: 32px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center;">
            <div
                style="background: #ecfdf5; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0;">Buyer Analytics Report
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; font-weight: 500;">
                Purchasing patterns, retention, Prime conversion, LTV analysis, and behavior insights.
            </p>
            <a href="#"
                style="font-size: 14px; font-weight: 700; color: #ea580c; text-decoration: none; margin-top: auto; display: flex; align-items: center; gap: 6px;">
                Generate Report &rarr;
            </a>
        </div>

        <!-- Compliance Report -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-top: 4px solid #ef4444; border-radius: 8px; padding: 32px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center;">
            <div
                style="background: #fef2f2; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0;">Compliance Report</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; font-weight: 500;">
                License status, COA coverage, PACT Act compliance, violations, and regulatory tracking.
            </p>
            <a href="#"
                style="font-size: 14px; font-weight: 700; color: #ea580c; text-decoration: none; margin-top: auto; display: flex; align-items: center; gap: 6px;">
                Generate Report &rarr;
            </a>
        </div>

        <!-- FBS Operations Report -->
        <div
            style="background: white; border: 1px solid #e2e8f0; border-top: 4px solid #14b8a6; border-radius: 8px; padding: 32px 24px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; align-items: center;">
            <div
                style="background: #f0fdfa; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #14b8a6; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-truck"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #1e293b; margin: 0 0 12px 0;">FBS Operations Report
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 24px 0; line-height: 1.5; font-weight: 500;">
                Warehouse utilization, fulfillment speed, storage costs, returns, and logistics efficiency.
            </p>
            <a href="#"
                style="font-size: 14px; font-weight: 700; color: #ea580c; text-decoration: none; margin-top: auto; display: flex; align-items: center; gap: 6px;">
                Generate Report &rarr;
            </a>
        </div>

    </div>

    <!-- Custom Report Builder Section -->
    <div
        style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-top: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Custom Report Builder
                </h2>
                <p style="font-size: 14px; color: #64748b; margin: 0; font-weight: 500;">
                    Build tailored reports with your preferred metrics, groupings, and filters.
                </p>
            </div>
            <a href="#"
                style="display: flex; align-items: center; gap: 6px; color: #f97316; text-decoration: none; font-size: 14px; font-weight: 600;">
                <i class="fas fa-info-circle"></i> Help Guide
            </a>
        </div>

        <!-- Section 1: Select Metrics -->
        <div style="margin-bottom: 40px; padding-left: 24px; border-left: 4px solid #f97316; display: flex; gap: 16px;">

            <div style="flex: 1; margin-top: 20px; padding-top: 0;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0;"><span
                        style="background: #f97316; color: white; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 700;">1</span>
                    Select Metrics</h3>

                <!-- Revenue Metrics -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 12px;">Revenue Metrics
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="metric-tag selected" data-metric="gmv"
                            style="padding: 8px 16px; background: #f97316; color: white; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            GMV <i class="fas fa-times" style="font-size: 11px;"></i>
                        </span>
                        <span class="metric-tag selected" data-metric="platform-fees"
                            style="padding: 8px 16px; background: #f97316; color: white; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            Platform Fees <i class="fas fa-times" style="font-size: 11px;"></i>
                        </span>
                        <span class="metric-tag" data-metric="referral-fees"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Referral Fees
                        </span>
                        <span class="metric-tag" data-metric="fbs-fees"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            FBS Fees
                        </span>
                        <span class="metric-tag selected" data-metric="ad-revenue"
                            style="padding: 8px 16px; background: #f97316; color: white; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            Ad Revenue <i class="fas fa-times" style="font-size: 11px;"></i>
                        </span>
                    </div>
                </div>

                <!-- Order Metrics -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 12px;">Order Metrics
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="metric-tag selected" data-metric="order-count"
                            style="padding: 8px 16px; background: #f97316; color: white; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            Order Count <i class="fas fa-times" style="font-size: 11px;"></i>
                        </span>
                        <span class="metric-tag" data-metric="aov"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            AOV
                        </span>
                        <span class="metric-tag" data-metric="status-distribution"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Status Distribution
                        </span>
                        <span class="metric-tag" data-metric="cancellation-rate"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Cancellation Rate
                        </span>
                    </div>
                </div>

                <!-- Seller Metrics -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 12px;">Seller Metrics
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="metric-tag" data-metric="active-sellers"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Active Sellers
                        </span>
                        <span class="metric-tag" data-metric="new-sellers"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            New Sellers
                        </span>
                        <span class="metric-tag" data-metric="suspended"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Suspended
                        </span>
                        <span class="metric-tag selected" data-metric="by-tier"
                            style="padding: 8px 16px; background: #f97316; color: white; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            By Tier <i class="fas fa-times" style="font-size: 11px;"></i>
                        </span>
                    </div>
                </div>

                <!-- Buyer Metrics -->
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 12px;">Buyer Metrics
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="metric-tag" data-metric="active-buyers"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Active Buyers
                        </span>
                        <span class="metric-tag" data-metric="prime-percent"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Prime %
                        </span>
                        <span class="metric-tag" data-metric="retention-rate"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Retention Rate
                        </span>
                        <span class="metric-tag" data-metric="ltv"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            LTV
                        </span>
                    </div>
                </div>

                <!-- Compliance Metrics -->
                <div>
                    <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 12px;">Compliance
                        Metrics</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="metric-tag" data-metric="compliance-score"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Compliance Score
                        </span>
                        <span class="metric-tag" data-metric="active-alerts"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            Active Alerts
                        </span>
                        <span class="metric-tag" data-metric="license-status"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            License Status
                        </span>
                        <span class="metric-tag" data-metric="coa-coverage"
                            style="padding: 8px 16px; background: #fff7ed; color: #ea580c; border: 1px solid #ea580c; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">
                            COA Coverage
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Group By -->
        <div style="margin-bottom: 40px; padding-left: 24px; border-left: 4px solid #a855f7; display: flex; gap: 16px;">
            
            <div style="flex: 1; margin-top: 20px; padding-top: 0;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0;"> <span style="background: #a855f7; color: white; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 700;">2</span>  Group By</h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="radio" name="groupBy" value="date" checked
                            style="width: 18px; height: 18px; cursor: pointer; accent-color: #f97316;">
                        <span style="font-size: 14px; font-weight: 600; color: #1e293b;">Date</span>
                        <select
                            style="margin-left: 12px; padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer;">
                            <option value="monthly" selected>Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="daily">Daily</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </label>
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="radio" name="groupBy" value="seller"
                            style="width: 18px; height: 18px; cursor: pointer; accent-color: #f97316;">
                        <span style="font-size: 14px; font-weight: 600; color: #1e293b;">Seller</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="radio" name="groupBy" value="buyer"
                            style="width: 18px; height: 18px; cursor: pointer; accent-color: #f97316;">
                        <span style="font-size: 14px; font-weight: 600; color: #1e293b;">Buyer</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="radio" name="groupBy" value="product-category"
                            style="width: 18px; height: 18px; cursor: pointer; accent-color: #f97316;">
                        <span style="font-size: 14px; font-weight: 600; color: #1e293b;">Product Category</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 12px; cursor: pointer;">
                        <input type="radio" name="groupBy" value="state-region"
                            style="width: 18px; height: 18px; cursor: pointer; accent-color: #f97316;">
                        <span style="font-size: 14px; font-weight: 600; color: #1e293b;">State / Region</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 3: Apply Filters -->
        <div style="margin-bottom: 40px;   padding-left: 24px; border-left: 4px solid #3b82f6; display: flex; gap: 16px;">
           
            <div style="flex: 1; margin-top: 20px; padding-top: 0;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0;"> <span style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 20px; font-size: 12px; font-weight: 700;">3</span> Apply Filters</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <!-- Date Range -->
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Date
                            Range</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="flex: 1; position: relative;">
                                <input type="date" id="startDate"
                                    style="width: 100%; padding: 10px 12px; padding-left: 38px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer;">
                                <i class="fas fa-calendar" id="startDateIcon" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #f97316; cursor: pointer; font-size: 16px;"></i>
                            </div>
                            <span style="color: #94a3b8; font-size: 13px; font-weight: 500;">to</span>
                            <div style="flex: 1; position: relative;">
                                <input type="date" id="endDate"
                                    style="width: 100%; padding: 10px 12px; padding-left: 38px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer;">
                                <i class="fas fa-calendar" id="endDateIcon" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #f97316; cursor: pointer; font-size: 16px;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- State / Region -->
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">State
                            / Region</label>
                        <select
                            style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer;">
                            <option value="all" selected>All States</option>
                            <option value="ca">California</option>
                            <option value="ny">New York</option>
                            <option value="tx">Texas</option>
                        </select>
                    </div>

                    <!-- Product Category -->
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Product
                            Category</label>
                        <select
                            style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer;">
                            <option value="all" selected>All Categories</option>
                            <option value="category1">Category 1</option>
                            <option value="category2">Category 2</option>
                        </select>
                    </div>

                    <!-- Seller Tier -->
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Seller
                            Tier</label>
                        <select
                            style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer;">
                            <option value="all" selected>All Tiers</option>
                            <option value="tier1">Tier 1</option>
                            <option value="tier2">Tier 2</option>
                            <option value="tier3">Tier 3</option>
                        </select>
                    </div>
                </div>
            </div>



        </div>
        <!-- Footer Actions -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding-top: 24px; border-top: 1px solid #e2e8f0;">
            <a href="#" id="resetAll"
                style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer;">Reset
                All</a>
            <div style="display: flex; gap: 12px;">
                <button
                    style="padding: 10px 20px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <i class="fas fa-clock"></i> Schedule Report
                </button>
                <button
                    style="padding: 10px 20px; background: #f97316; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2);">
                    <i class="fas fa-play"></i> Generate Report
                </button>
            </div>
        </div>

        <script>
        // Make calendar icons clickable to open date picker
        document.getElementById('startDateIcon')?.addEventListener('click', function() {
            document.getElementById('startDate').click();
        });

        document.getElementById('endDateIcon')?.addEventListener('click', function() {
            document.getElementById('endDate').click();
        });

        // Metric tag toggle functionality
        document.querySelectorAll('.metric-tag').forEach(tag => {
            tag.addEventListener('click', function() {
                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    this.style.background = 'white';
                    this.style.color = '#475569';
                    this.style.border = '1px solid #e2e8f0';
                    const icon = this.querySelector('i');
                    if (icon) icon.remove();
                } else {
                    this.classList.add('selected');
                    this.style.background = '#f97316';
                    this.style.color = 'white';
                    this.style.border = 'none';
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-times';
                    icon.style.fontSize = '11px';
                    this.appendChild(icon);
                }
            });
        });

        // Reset All functionality
        document.getElementById('resetAll')?.addEventListener('click', function(e) {
            e.preventDefault();
            // Reset metrics
            document.querySelectorAll('.metric-tag').forEach(tag => {
                tag.classList.remove('selected');
                tag.style.background = 'white';
                tag.style.color = '#475569';
                tag.style.border = '1px solid #e2e8f0';
                const icon = tag.querySelector('i');
                if (icon) icon.remove();
            });
            // Reset group by
            document.querySelector('input[name="groupBy"][value="date"]').checked = true;
            // Reset filters
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.querySelectorAll('select').forEach(select => {
                if (select.querySelector('option[value="all"]')) {
                    select.value = 'all';
                } else if (select.querySelector('option[value="monthly"]')) {
                    select.value = 'monthly';
                }
            });
        });
        </script>

    </div>

    <!-- Scheduled Reports Section -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-top: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Scheduled Reports</h2>
                <p style="font-size: 14px; color: #64748b; margin: 0; font-weight: 500;">
                    Automated reports sent to specified recipients on a recurring basis
                </p>
            </div>
            <button
                style="padding: 10px 20px; background: #f97316; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2);">
                <i class="fas fa-plus"></i> New Schedule
            </button>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead style="background-color: #fff !important;">
                    <tr style="background-color: #1e293b !important; background: #1e293b !important; border-bottom: 2px solid #0f172a;">
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">REPORT NAME</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">TYPE</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">FREQUENCY</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">RECIPIENTS</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">LAST RUN</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">NEXT RUN</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">STATUS</th>
                        <th style="padding: 14px 16px; text-align: center; font-weight: 700; color: #ffffff !important; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; background-color: #1e293b !important;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr style="background: #ffffff; border-bottom: 1px solid #e2e8f0; hover:background: #f8fafc;">
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Weekly Revenue Summary</div>
                            <div style="font-size: 12px; color: #64748b;">GMV + Platform Fees</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <span style="display: inline-block; background: #fff7ed; color: #f97316; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">Revenue</span>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Weekly</div>
                            <div style="font-size: 12px; color: #64748b;">Every Monday</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline; margin-bottom: 2px;">finance@company.com</div>
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline;">ceo@company.com</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Jan 15, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">9:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Jan 22, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">9:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" checked style="width: 40px; height: 24px; cursor: pointer; accent-color: #10b981;">
                            </label>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-edit"></i></button>
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-play"></i></button>
                                <button style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Monthly Seller Performance</div>
                            <div style="font-size: 12px; color: #64748b;">Top 100 sellers by GMV</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <span style="display: inline-block; background: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">Seller</span>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Monthly</div>
                            <div style="font-size: 12px; color: #64748b;">1st of month</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline;">ops@company.com</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Jan 1, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">8:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Feb 1, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">8:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" checked style="width: 40px; height: 24px; cursor: pointer; accent-color: #10b981;">
                            </label>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-edit"></i></button>
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-play"></i></button>
                                <button style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr style="background: #ffffff; border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Daily Compliance Alert</div>
                            <div style="font-size: 12px; color: #64748b;">License expiry warnings</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <span style="display: inline-block; background: #fef2f2; color: #ef4444; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">Compliance</span>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Daily</div>
                            <div style="font-size: 12px; color: #64748b;">Every day 7 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline; margin-bottom: 2px;">compliance@company.com</div>
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline;">legal@company.com</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Jan 17, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">7:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Jan 18, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">7:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" checked style="width: 40px; height: 24px; cursor: pointer; accent-color: #10b981;">
                            </label>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-edit"></i></button>
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-play"></i></button>
                                <button style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Quarterly FBS Analytics</div>
                            <div style="font-size: 12px; color: #64748b;">Warehouse efficiency metrics</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <span style="display: inline-block; background: #ecfdf5; color: #10b981; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">Operations</span>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Quarterly</div>
                            <div style="font-size: 12px; color: #64748b;">End of quarter</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline;">logistics@company.com</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Dec 31, 2023</div>
                            <div style="font-size: 12px; color: #64748b;">10:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Mar 31, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">10:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" style="width: 40px; height: 24px; cursor: pointer; accent-color: #10b981;">
                            </label>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-edit"></i></button>
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-play"></i></button>
                                <button style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr style="background: #ffffff;">
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Bi-Weekly Buyer Insights</div>
                            <div style="font-size: 12px; color: #64748b;">Retention & LTV analysis</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <span style="display: inline-block; background: #ecfdf5; color: #10b981; padding: 4px 12px; border-radius: 12px; font-weight: 600; font-size: 12px;">Buyer</span>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Bi-Weekly</div>
                            <div style="font-size: 12px; color: #64748b;">1st & 15th</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline; margin-bottom: 2px;">marketing@company.com</div>
                            <div style="color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline;">growth@company.com</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Jan 15, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">11:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle;">
                            <div style="color: #1e293b; font-weight: 600;">Feb 1, 2024</div>
                            <div style="font-size: 12px; color: #64748b;">11:00 AM</div>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <label style="display: inline-flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" checked style="width: 40px; height: 24px; cursor: pointer; accent-color: #10b981;">
                            </label>
                        </td>
                        <td style="padding: 16px; vertical-align: middle; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-edit"></i></button>
                                <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-play"></i></button>
                                <button style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px 8px; transition: color 0.2s;"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Downloads Section -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-top: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Recent Downloads</h2>
                <p style="font-size: 14px; color: #64748b; margin: 0; font-weight: 500;">
                    Recently generated reports available for download (expires after 30 days)
                </p>
            </div>
            <button style="padding: 8px 16px; background: none; border: none; color: #f97316; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: underline;">
                <i class="fas fa-times" style="margin-right: 4px; font-size: 12px;"></i> Clear All
            </button>
        </div>

        <!-- Downloads List -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            
            <!-- Download Item 1 -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div style="width: 40px; height: 40px; background: #d1fae5; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 20px;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Platform_Revenue_Report_Q4_2023.xlsx</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #64748b;">
                            <span>Generated: Jan 19, 2024 at 3:45 PM</span>
                            <span>•</span>
                            <span>Size: 2.4 MB</span>
                            <span>•</span>
                            <span style="color: #ef4444; font-weight: 600;">Expires in 28 days</span>
                        </div>
                    </div>
                </div>
                <button style="padding: 8px 16px; background: #f97316; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2); margin-left: 16px; white-space: nowrap;">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

            <!-- Download Item 2 -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div style="width: 40px; height: 40px; background: #fee2e2; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 20px;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Seller_Performance_Top_100_January.pdf</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #64748b;">
                            <span>Generated: Jan 18, 2024 at 11:20 AM</span>
                            <span>•</span>
                            <span>Size: 1.8 MB</span>
                            <span>•</span>
                            <span style="color: #ef4444; font-weight: 600;">Expires in 27 days</span>
                        </div>
                    </div>
                </div>
                <button style="padding: 8px 16px; background: #f97316; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2); margin-left: 16px; white-space: nowrap;">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

            <!-- Download Item 3 -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px;">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Compliance_Report_All_Sellers_Jan_2024.csv</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #64748b;">
                            <span>Generated: Jan 15, 2024 at 9:30 AM</span>
                            <span>•</span>
                            <span>Size: 856 KB</span>
                            <span>•</span>
                            <span style="color: #ef4444; font-weight: 600;">Expires in 26 days</span>
                        </div>
                    </div>
                </div>
                <button style="padding: 8px 16px; background: #f97316; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2); margin-left: 16px; white-space: nowrap;">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

            <!-- Download Item 4 -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div style="width: 40px; height: 40px; background: #d1fae5; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 20px;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">FBS_Operations_Warehouse_Utilization_Dec.xlsx</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #64748b;">
                            <span>Generated: Jan 14, 2024 at 2:15 PM</span>
                            <span>•</span>
                            <span>Size: 3.1 MB</span>
                            <span>•</span>
                            <span style="color: #ef4444; font-weight: 600;">Expires in 25 days</span>
                        </div>
                    </div>
                </div>
                <button style="padding: 8px 16px; background: #f97316; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2); margin-left: 16px; white-space: nowrap;">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

            <!-- Download Item 5 -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div style="width: 40px; height: 40px; background: #fee2e2; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 20px;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Buyer_Analytics_Retention_LTV_Analysis.pdf</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #64748b;">
                            <span>Generated: Jan 13, 2024 at 4:50 PM</span>
                            <span>•</span>
                            <span>Size: 2.2 MB</span>
                            <span>•</span>
                            <span style="color: #ef4444; font-weight: 600;">Expires in 24 days</span>
                        </div>
                    </div>
                </div>
                <button style="padding: 8px 16px; background: #f97316; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2); margin-left: 16px; white-space: nowrap;">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

            <!-- Download Item 6 -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                    <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px;">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Fee_Collection_Report_All_Categories_Q4.csv</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #64748b;">
                            <span>Generated: Jan 12, 2024 at 10:05 AM</span>
                            <span>•</span>
                            <span>Size: 1.5 MB</span>
                            <span>•</span>
                            <span style="color: #ef4444; font-weight: 600;">Expires in 23 days</span>
                        </div>
                    </div>
                </div>
                <button style="padding: 8px 16px; background: #f97316; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2); margin-left: 16px; white-space: nowrap;">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>

        </div>

        <!-- View All Downloads Link -->
        <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
            <a href="#" style="color: #1e293b; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                View All Downloads <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
            </a>
        </div>

    </div>



    <!-- Report Templates Library Section -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-top: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Report Templates Library</h2>
                <p style="font-size: 14px; color: #64748b; margin: 0; font-weight: 500;">
                    Pre-configured templates to quickly generate common reports
                </p>
            </div>
            <button style="padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <i class="fas fa-plus" style="color: #f97316;"></i> Create Template
            </button>
        </div>

        <!-- Templates Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            
            <!-- Template 1 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #f5f3ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #8b5cf6; font-size: 24px;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <button style="background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 16px;"><i class="fas fa-star"></i></button>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Monthly Financial Summary</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0; line-height: 1.4;">Complete financial overview including GMV, fees, and net revenue</p>
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 16px;">Used 47 times</div>
                <a href="#" style="color: #f97316; text-decoration: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    Use Template <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                </a>
            </div>

            <!-- Template 2 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 24px;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <button style="background: none; border: none; color: #f97316; cursor: pointer; font-size: 16px;"><i class="fas fa-star"></i></button>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Top Performers Analysis</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0; line-height: 1.4;">Rankings of top sellers ranked by various metrics</p>
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 16px;">Used 89 times</div>
                <a href="#" style="color: #f97316; text-decoration: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    Use Template <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                </a>
            </div>

            <!-- Template 3 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #fef2f2; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 24px;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <button style="background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 16px;"><i class="fas fa-star"></i></button>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Compliance Violations</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0; line-height: 1.4;">All compliance issues, warnings, and violation tracking</p>
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 16px;">Used 34 times</div>
                <a href="#" style="color: #f97316; text-decoration: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    Use Template <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                </a>
            </div>

            <!-- Template 4 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #ecfdf5; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 24px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <button style="background: none; border: none; color: #f97316; cursor: pointer; font-size: 16px;"><i class="fas fa-star"></i></button>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Growth Trends Report</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0; line-height: 1.4;">Month-over-month and year-over-year growth analysis</p>
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 16px;">Used 92 times</div>
                <a href="#" style="color: #f97316; text-decoration: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    Use Template <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                </a>
            </div>

            <!-- Template 5 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 24px;">
                        <i class="fas fa-box"></i>
                    </div>
                    <button style="background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 16px;"><i class="fas fa-star"></i></button>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Inventory & Fulfillment</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0; line-height: 1.4;">FBS warehouse metrics and inventory turnover rates</p>
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 16px;">Used 28 times</div>
                <a href="#" style="color: #f97316; text-decoration: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    Use Template <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                </a>
            </div>

            <!-- Template 6 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; cursor: pointer; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 24px;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <button style="background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 16px;"><i class="fas fa-star"></i></button>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">Order Analytics Deep Dive</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 16px 0; line-height: 1.4;">Comprehensive order data including cancellations and returns</p>
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 16px;">Used 51 times</div>
                <a href="#" style="color: #f97316; text-decoration: none; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                    Use Template <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                </a>
            </div>

        </div>

    </div>

    <!-- Export Settings & Preferences Section -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-top: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="margin-bottom: 32px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Export Settings & Preferences</h2>
            <p style="font-size: 14px; color: #64748b; margin: 0; font-weight: 500;">
                Configure default export formats and delivery options
            </p>
        </div>

        <!-- Two Column Layout -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
            
            <!-- Left Column: Default Export Format -->
            <div>
                <h3 style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; text-transform: uppercase; letter-spacing: 0.5px;">Default Export Format</h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Excel Option -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="radio" name="exportFormat" value="excel" checked style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Excel (.xlsx)</div>
                            <div style="font-size: 12px; color: #64748b;">Best for data analysis and pivot tables</div>
                        </div>
                    </label>

                    <!-- PDF Option -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="radio" name="exportFormat" value="pdf" style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">PDF (.pdf)</div>
                            <div style="font-size: 12px; color: #64748b;">Best for sharing and presentations</div>
                        </div>
                    </label>

                    <!-- CSV Option -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="radio" name="exportFormat" value="csv" style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">CSV (.csv)</div>
                            <div style="font-size: 12px; color: #64748b;">Best for importing to other systems</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Right Column: Delivery Options -->
            <div>
                <h3 style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; text-transform: uppercase; letter-spacing: 0.5px;">Delivery Options</h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Email Notification -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Email notification on completion</div>
                            <input type="email" placeholder="admin@company.com" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #1e293b; background: #f8fafc;">
                        </div>
                    </label>

                    <!-- Auto-upload -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">Auto-upload to cloud storage</div>
                            <select style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: #f8fafc; cursor: pointer;">
                                <option>Select storage provider</option>
                                <option>Google Drive</option>
                                <option>Dropbox</option>
                                <option>AWS S3</option>
                            </select>
                        </div>
                    </label>

                    <!-- Compress Files -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Compress large files</div>
                            <div style="font-size: 12px; color: #64748b;">Automatically zip files larger than 10 MB</div>
                        </div>
                    </label>

                    <!-- Data Dictionary -->
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; padding: 12px; border-radius: 8px; transition: background 0.2s;">
                        <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px; accent-color: #f97316;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">Include data dictionary</div>
                            <div style="font-size: 12px; color: #64748b;">Add metadata and column descriptions</div>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        <!-- Data Retention Policy -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Data Retention Policy</div>
                    <div style="font-size: 13px; color: #64748b;">Generated reports are automatically deleted after the specified period</div>
                </div>
                <select style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #475569; background: white; cursor: pointer; font-weight: 600; min-width: 120px;">
                    <option>30 days</option>
                    <option>60 days</option>
                    <option>90 days</option>
                    <option>6 months</option>
                    <option>1 year</option>
                </select>
            </div>
        </div>

        <!-- Footer Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
            <button style="padding: 10px 20px; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                Reset to Defaults
            </button>
            <button style="padding: 10px 20px; background: #f97316; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 1px 2px rgba(249, 115, 22, 0.2);">
                Save Preferences
            </button>
        </div>

    </div>


    <!-- now create new ui layout as per the ss below this line  -->

    <!-- API Integration & Webhooks Section -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; margin-top: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">API Integration & Webhooks</h2>
                <p style="font-size: 14px; color: #64748b; margin: 0; font-weight: 500;">
                    Programmatically access reports and configure webhooks for automated workflows
                </p>
            </div>
            <button style="padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <i class="fas fa-book" style="color: #1e293b;"></i> API Documentation
            </button>
        </div>

        <!-- Two Column Layout: API Keys & Webhooks -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px;">
            
            <!-- Left Column: API Keys -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 20px;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">API Keys</h3>
                        <p style="font-size: 12px; color: #64748b; margin: 0;">Manage authentication tokens</p>
                    </div>
                </div>

                <!-- API Key Items -->
                <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px;">
                    <!-- Production Key -->
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Production Key</div>
                            <div style="font-family: monospace; font-size: 13px; color: #1e293b; font-weight: 500; letter-spacing: 1px;">sk_live••••••••••••••a2f</div>
                        </div>
                        <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 8px;" title="Copy to clipboard">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Generate New Key Button -->
                <button style="width: 100%; padding: 12px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;">
                    <i class="fas fa-plus" style="color: #f97316;"></i> Generate New Key
                </button>
            </div>

            <!-- Right Column: Webhooks -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; background: #f5f3ff; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #8b5cf6; font-size: 20px;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">Webhooks</h3>
                        <p style="font-size: 12px; color: #64748b; margin: 0;">Receive real-time notifications</p>
                    </div>
                </div>

                <!-- Webhook Items -->
                <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px;">
                    <!-- Report Completed Webhook -->
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="flex: 1;">
                            <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Report Completed</div>
                            <div style="font-size: 13px; color: #475569; word-break: break-all;">https://api.company.com/webhooks/reports</div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <span style="background: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap;">Active</span>
                            <button style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 14px; padding: 4px 8px;">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Webhook Button -->
                <button style="width: 100%; padding: 12px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;">
                    <i class="fas fa-plus" style="color: #f97316;"></i> Add Webhook
                </button>
            </div>

        </div>

        <!-- API Rate Limits Info Box -->
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; display: flex; align-items: flex-start; gap: 16px;">
            <div style="width: 32px; height: 32px; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 16px; flex-shrink: 0;">
                <i class="fas fa-circle-info"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: #3b82f6; margin-bottom: 4px;">API Rate Limits</div>
                <div style="font-size: 13px; color: #1e40af;">Current plan allows 1,000 API calls per hour. Contact support to increase limits.</div>
            </div>
        </div>

    </div>

    @endsection
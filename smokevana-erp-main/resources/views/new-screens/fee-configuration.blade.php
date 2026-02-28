@extends('layouts.app')
@section('title', 'Fee Configuration')

@section('css')
    <style>
        #referral-fee-table tbody tr:hover,
        #fulfillment-fee-table tbody tr:hover {
            background-color: #fff9c4 !important;
            /* Noticeable yellow hover */
            transition: background-color 0.1s ease-in-out;
        }
    </style>
@endsection

@section('content')
    <div
        style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

        <!-- Top Action Bar Section (Exactly as per Image) -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div style="font-size: 14.5px; color: #616161; margin-top: 12px; font-weight: 500;">
                Configure commission rates, fees, and revenue rules
            </div>

            <div style="display: flex; gap: 12px;">
                <!-- Revert to Saved Button -->
                <button
                    style="display: flex; align-items: center; gap: 8px; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 20px; color: #1f2937; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                    onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='#ffffff'">
                    <i class="fas fa-rotate-left" style="font-size: 14px;"></i>
                    Revert to Saved
                </button>

                <!-- Save All Changes Button -->
                <button
                    style="display: flex; align-items: center; gap: 8px; background-color: #f3a847; border: none; border-radius: 8px; padding: 11px 24px; color: #ffffff; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                    onmouseover="this.style.backgroundColor='#e29636'; this.style.transform='translateY(-1px)'"
                    onmouseout="this.style.backgroundColor='#f3a847'; this.style.transform='translateY(0)'">
                    <i class="fas fa-check" style="font-size: 14px;"></i>
                    Save All Changes
                </button>
            </div>
        </div>

        <!-- Info Box Section -->
        <div
            style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 16px 20px; display: flex; align-items: center; gap: 12px; margin-bottom: 32px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <i class="fas fa-circle-info" style="color: #2563eb; font-size: 18px;"></i>
            <span style="color: #1e40af; font-size: 14px; font-weight: 500; font-family: inherit; line-height: 1.5;">
                Changes to fee configuration take effect immediately for new orders. Existing orders retain their original
                fee rates.
            </span>
        </div>

        <!-- Section 2: Referral Fee Rates (Exactly as per Image) -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 24px;">
                <div style="margin-top: 4px;">
                    <i class="fas fa-tag" style="color: #7c3aed; font-size: 20px; transform: rotate(-45deg);"></i>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0;">Referral Fee Rates
                        (Commission)</h2>
                    <p style="font-size: 14px; color: #64748b; margin: 4px 0 0 0;">Percentage commission charged on each
                        order, deducted from seller proceeds.</p>
                </div>
            </div>

            <table id="referral-fee-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="text-align: left;">
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Category</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Current Rate (%)</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            New Rate (%)</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Min Fee ($)</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Max Fee ($)</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Effective Date</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Last Changed</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Pre-Rolls -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Pre-Rolls</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">12%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="12"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$1.00
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- Vape Cartridges -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">Vape
                            Cartridges</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">14%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="14"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$1.00
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- Flower (Highlighted Row) -->
                    <tr style="background-color: #fffbeb !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; font-weight: 700; color: #1e293b;">
                            Flower</td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; color: #334155;">10%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7;">
                            <input type="text" value="11"
                                style="width: 60px; padding: 6px 12px; border: 2px solid #f3a847; border-radius: 6px; text-align: center; color: #334155; font-weight: 600; background-color: #ffffff;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; color: #334155; font-weight: 600;">$1.00
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #fef3c7; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- Edibles -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Edibles</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">15%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="15"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$1.50
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- CBD Products -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">CBD
                            Products</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">12%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="12"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$1.00
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- Accessories -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Accessories</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">8%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="8"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$0.50
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $200.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- Hemp THC -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">Hemp
                            THC</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">13%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="13"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$1.00
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                    <!-- Concentrates -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Concentrates</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155;">14%</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                            <input type="text" value="14"
                                style="width: 60px; padding: 6px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">$1.50
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">
                            $500.00</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Jan 01, 2026</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Dec 15, 2025</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #94a3b8;">
                            <span style="color: #0284c7; cursor: pointer; font-weight: 600;">Edit</span> | History
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Footer Actions -->
            <div style="margin-top: 24px;">
                <div
                    style="color: #0d9488; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 14px; margin-bottom: 16px;">
                    <i class="fas fa-plus"></i> Add Category
                </div>
                <div
                    style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-chevron-right" style="font-size: 12px;"></i> View Rate Change History
                </div>
            </div>
        </div>



        <div style="margin-top: 32px;"></div>

        <!-- Section 3: FBS Fulfillment Fees (Exactly as per Image) -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 24px;">
                <div style="margin-top: 4px;">
                    <i class="fas fa-truck" style="color: #4f46e5; font-size: 20px;"></i>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0;">FBS Fulfillment Fees</h2>
                    <p style="font-size: 14px; color: #64748b; margin: 4px 0 0 0;">Fees charged for Fulfillment by Smokevana
                        services</p>
                </div>
            </div>

            <table id="fulfillment-fee-table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="text-align: left;">
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Fee Type</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            Standard Rate</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0; text-align: center;">
                            Prime Rate</th>
                        <th
                            style="background-color: #f5f5f5 !important; padding: 12px 16px; color: #1f2937 !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; border-bottom: 1px solid #e2e8f0;">
                            Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Small Item -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">Small
                            Item Fulfillment</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$3.50/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$3.00/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">&lt;1 lb, &lt;12"
                            longest side</td>
                    </tr>
                    <!-- Medium Item -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Medium Item Fulfillment</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$5.00/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$4.25/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">1-5 lbs</td>
                    </tr>
                    <!-- Large Item -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">Large
                            Item Fulfillment</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$8.00/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$7.00/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">5-20 lbs</td>
                    </tr>
                    <!-- Monthly Storage -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Monthly Storage (per cu ft)</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$0.75"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$0.75"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">Standard months</td>
                    </tr>
                    <!-- Peak Storage -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">Peak
                            Storage (Oct-Dec)</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$2.40"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$2.40"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">Seasonal surge</td>
                    </tr>
                    <!-- Long-term Storage -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Long-term Storage (>180 days)</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$6.90/cu ft"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$6.90/cu ft"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">Semi-annual</td>
                    </tr>
                    <!-- Inbound Receiving -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Inbound Receiving</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$0.25/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$0.25/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">Includes compliance
                            check</td>
                    </tr>
                    <!-- Removal/Disposal -->
                    <tr style="background-color: #ffffff !important;">
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                            Removal/Disposal</td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$0.50/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <input type="text" value="$0.50/unit"
                                style="width: 100px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #334155; font-weight: 600;">
                        </td>
                        <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #94a3b8;">Return or destroy
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 32px;"></div>

        <!-- Section 4: Seller Subscription Plans (Exactly as per Image) -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 32px;">
                <div style="margin-top: 2px;">
                    <i class="fas fa-crown" style="color: #6366f1; font-size: 20px;"></i>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0;">Seller Subscription Plans</h2>
                    <p style="font-size: 14px; color: #64748b; margin: 4px 0 0 0;">Configure subscription tiers and pricing
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; padding: 0 40px;">
                <!-- Basic Plan -->
                <div
                    style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px 24px; display: flex; flex-direction: column; min-height: 480px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <span style="font-weight: 700; font-size: 16px; color: #1a202c;">Basic</span>
                        <span style="color: #0284c7; font-size: 13px; font-weight: 600; cursor: pointer;">Edit Plan</span>
                    </div>

                    <div
                        style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px 16px; text-align: center; margin-bottom: 32px;">
                        <span style="font-size: 26px; font-weight: 800; color: #1a202c;">Free</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0; flex-grow: 1;">
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Basic marketplace access
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Up to 50 listings
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Standard support
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #9ca3af;">
                            <i class="fas fa-times" style="color: #94a3b8; font-size: 12px;"></i> No analytics
                        </li>
                    </ul>

                    <div style="margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <div style="color: #64748b; font-size: 12px; font-weight: 500;">Active Subscribers</div>
                        <div style="font-size: 26px; font-weight: 700; color: #1a202c; margin-top: 6px;">847</div>
                    </div>
                </div>

                <!-- Professional Plan (Highlighted) -->
                <div
                    style="border: 2px solid #f59e0b; background-color: #fff7ed; border-radius: 12px; padding: 32px 24px; display: flex; flex-direction: column; min-height: 480px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <span style="font-weight: 700; font-size: 16px; color: #1a202c;">Professional</span>
                        <span style="color: #0284c7; font-size: 13px; font-weight: 600; cursor: pointer;">Edit Plan</span>
                    </div>

                    <div
                        style="background-color: #ffffff; border: 1px solid #fed7aa; border-radius: 8px; padding: 24px 16px; text-align: center; margin-bottom: 32px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <span style="font-size: 26px; font-weight: 800; color: #1a202c;">$39.99/mo</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0; flex-grow: 1;">
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Full marketplace access
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Unlimited listings
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Basic analytics
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Priority support
                        </li>
                    </ul>

                    <div style="margin-top: 32px; border-top: 1px solid #ffedd5; padding-top: 20px;">
                        <div style="color: #64748b; font-size: 12px; font-weight: 500;">Active Subscribers</div>
                        <div style="font-size: 26px; font-weight: 700; color: #1a202c; margin-top: 6px;">247</div>
                    </div>
                </div>

                <!-- Premium Plan -->
                <div
                    style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px 24px; display: flex; flex-direction: column; min-height: 480px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <span style="font-weight: 700; font-size: 16px; color: #1a202c;">Premium</span>
                        <span style="color: #0284c7; font-size: 13px; font-weight: 600; cursor: pointer;">Edit Plan</span>
                    </div>

                    <div
                        style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px 16px; text-align: center; margin-bottom: 32px;">
                        <span style="font-size: 26px; font-weight: 800; color: #1a202c;">$99.99/mo</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0; flex-grow: 1;">
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Advanced analytics
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Priority support
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Lower rates (-1%)
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Featured placement
                        </li>
                    </ul>

                    <div style="margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <div style="color: #64748b; font-size: 12px; font-weight: 500;">Active Subscribers</div>
                        <div style="font-size: 26px; font-weight: 700; color: #1a202c; margin-top: 6px;">128</div>
                    </div>
                </div>

                <!-- Enterprise Plan (Highlighted Purple) -->
                <div
                    style="border: 2px solid #8b5cf6; background-color: #f5f3ff; border-radius: 12px; padding: 32px 24px; display: flex; flex-direction: column; min-height: 480px; box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <span style="font-weight: 700; font-size: 16px; color: #1a202c;">Enterprise</span>
                        <span style="color: #0284c7; font-size: 13px; font-weight: 600; cursor: pointer;">Edit Plan</span>
                    </div>

                    <div
                        style="background-color: #ffffff; border: 1px solid #ddd6fe; border-radius: 8px; padding: 24px 16px; text-align: center; margin-bottom: 32px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <span style="font-size: 26px; font-weight: 800; color: #1a202c;">$299.99/mo</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0; flex-grow: 1;">
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Dedicated account manager
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Custom rate negotiation
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> API access
                        </li>
                        <li
                            style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 14px; color: #4b5563;">
                            <i class="fas fa-check" style="color: #10b981; font-size: 12px;"></i> Bulk management tools
                        </li>
                    </ul>

                    <div style="margin-top: 32px; border-top: 1px solid #ede9fe; padding-top: 20px;">
                        <div style="color: #64748b; font-size: 12px; font-weight: 500;">Active Subscribers</div>
                        <div style="font-size: 26px; font-weight: 700; color: #1a202c; margin-top: 6px;">58</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 32px;"></div>

        <!-- Section 5: Advertising Fee Rates (Exactly as per Image) -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 32px;">
                <div style="margin-top: 4px;">
                    <i class="fas fa-bullhorn" style="color: #6366f1; font-size: 20px;"></i>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0;">Advertising Fee Rates</h2>
                    <p style="font-size: 14px; color: #64748b; margin: 4px 0 0 0;">Configure minimum bids and platform
                        margins
                        for advertising</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <!-- Left Column: Minimum CPC Bids -->
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: #1a202c; margin-bottom: 16px;">Minimum CPC Bids by
                        Ad Type</h3>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- Sponsored Products -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; background-color: #f9fafb; padding: 16px 20px; border-radius: 8px; border: 1px solid #f1f5f9;">
                            <div>
                                <div style="font-weight: 700; font-size: 14px; color: #1a202c;">Sponsored Products</div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Per-click cost</div>
                            </div>
                            <input type="text" value="$0.10"
                                style="width: 80px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #1a202c; font-weight: 700; background-color: #ffffff;">
                        </div>

                        <!-- Sponsored Brands -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; background-color: #f9fafb; padding: 16px 20px; border-radius: 8px; border: 1px solid #f1f5f9;">
                            <div>
                                <div style="font-weight: 700; font-size: 14px; color: #1a202c;">Sponsored Brands</div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Per-click cost</div>
                            </div>
                            <input type="text" value="$0.25"
                                style="width: 80px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #1a202c; font-weight: 700; background-color: #ffffff;">
                        </div>

                        <!-- Sponsored Display -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; background-color: #f9fafb; padding: 16px 20px; border-radius: 8px; border: 1px solid #f1f5f9;">
                            <div>
                                <div style="font-weight: 700; font-size: 14px; color: #1a202c;">Sponsored Display</div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Per-click cost</div>
                            </div>
                            <input type="text" value="$0.15"
                                style="width: 80px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #1a202c; font-weight: 700; background-color: #ffffff;">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Margin Configuration -->
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: #1a202c; margin-bottom: 16px;">Platform Margin
                        Configuration</h3>

                    <div
                        style="display: flex; justify-content: space-between; align-items: center; background-color: #f9fafb; padding: 16px 20px; border-radius: 8px; border: 1px solid #f1f5f9; margin-bottom: 16px;">
                        <div>
                            <div style="font-weight: 700; font-size: 14px; color: #1a202c;">Platform Margin on Ad Spend
                            </div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Percentage kept by platform</div>
                        </div>
                        <input type="text" value="15%"
                            style="width: 80px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; color: #1a202c; font-weight: 700; background-color: #ffffff;">
                    </div>

                    <!-- Revenue Calculation Info -->
                    <div
                        style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 16px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-circle-info" style="color: #2563eb; font-size: 16px; margin-top: 2px;"></i>
                        <div>
                            <div style="color: #1e40af; font-size: 14px; font-weight: 700; margin-bottom: 4px;">Revenue
                                Calculation</div>
                            <div style="color: #2563eb; font-size: 13px; line-height: 1.4;">
                                Platform keeps 15% of all ad spend. Example: Seller spends $1,000 on ads → Platform earns
                                $150
                            </div>
                        </div>
                    </div>

                    <!-- Performance Summary -->
                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                        <div
                            style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">
                            Current Month Performance</div>

                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <span style="font-size: 14px; color: #475569; font-weight: 500;">Total Ad Spend</span>
                            <span style="font-size: 18px; font-weight: 700; color: #1e293b;">$34,800</span>
                        </div>

                        <div
                            style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                            <span style="font-size: 14px; color: #475569; font-weight: 500;">Platform Revenue (15%)</span>
                            <span style="font-size: 18px; font-weight: 700; color: #6366f1;">$5,220</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 32px;"></div>

        <!-- Section 6: Revenue Impact Analysis (Exactly as per Image) -->
        <div
            style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0;">Revenue Impact Analysis</h2>
                    <p style="font-size: 14px; color: #64748b; margin: 4px 0 0 0;">Preview how proposed changes will affect
                        platform revenue</p>
                </div>
                <button
                    style="display: flex; align-items: center; gap: 8px; background-color: #6366f1; border: none; border-radius: 8px; padding: 10px 20px; color: #ffffff; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#4f46e5'" onmouseout="this.style.backgroundColor='#6366f1'">
                    <i class="fas fa-calculator"></i>
                    Calculate Impact
                </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
                <!-- Card 1: Current Monthly Revenue -->
                <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Current Monthly Revenue</div>
                    <div style="font-size: 32px; font-weight: 800; color: #1a202c; margin-bottom: 4px;">$48,295</div>
                    <div style="font-size: 13px; color: #94a3b8;">Based on last 30 days</div>
                </div>

                <!-- Card 2: Projected Monthly Revenue (Highlighted Green) -->
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 24px;">
                    <div style="font-size: 11px; font-weight: 700; color: #15803d; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Projected Monthly Revenue</div>
                    <div style="font-size: 32px; font-weight: 800; color: #16a34a; margin-bottom: 4px;">$49,780</div>
                    <div style="font-size: 13px; color: #16a34a; font-weight: 700;">
                        <i class="fas fa-arrow-up"></i> +$1,485 (+3.1%)
                    </div>
                </div>

                <!-- Card 3: Annual Impact (Highlighted Purple) -->
                <div style="background-color: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 12px; padding: 24px;">
                    <div style="font-size: 11px; font-weight: 700; color: #6d28d9; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Annual Impact</div>
                    <div style="font-size: 32px; font-weight: 800; color: #7c3aed; margin-bottom: 4px;">$17,820</div>
                    <div style="font-size: 13px; color: #7c3aed; font-weight: 600;">Additional yearly revenue</div>
                </div>
            </div>

            <!-- Impact Summary (Yellow Box) -->
            <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 24px; display: flex; align-items: flex-start; gap: 16px;">
                <div style="margin-top: 4px;">
                    <i class="fas fa-triangle-exclamation" style="color: #d97706; font-size: 20px;"></i>
                </div>
                <div>
                    <h4 style="font-size: 15px; font-weight: 700; color: #92400e; margin: 0 0 12px 0;">Impact Summary</h4>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <li style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px; color: #b45309; margin-bottom: 8px;">
                            <span style="font-weight: 900;">•</span> 
                            Flower category rate increase (10% → 11%) affects 420 active sellers
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px; color: #b45309; margin-bottom: 8px;">
                            <span style="font-weight: 900;">•</span> 
                            Estimated additional monthly revenue from Flower: $1,485
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px; color: #b45309; margin-bottom: 8px;">
                            <span style="font-weight: 900;">•</span> 
                            No negative impact expected on seller retention
                        </li>
                        <li style="display: flex; align-items: flex-start; gap: 8px; font-size: 14px; color: #b45309;">
                            <span style="font-weight: 900;">•</span> 
                            Changes align with industry standard rates (8-15%)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
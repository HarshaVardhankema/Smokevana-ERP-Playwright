@extends('layouts.app')
@section('title', 'Geofence Rule Engine')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Header Stats Pills -->
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <div style="padding: 8px 16px; background-color: #ffffff; border-radius: 20px; font-size: 14px; font-weight: 500; color: #111827; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            38 States Active
        </div>
        <div style="padding: 8px 16px; background-color: #ffffff; border-radius: 20px; font-size: 14px; font-weight: 500; color: #111827; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            847 Total Rules
        </div>
        <div style="padding: 8px 16px; background-color: #ffffff; border-radius: 20px; font-size: 14px; font-weight: 500; color: #111827; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            Last Update: Feb 01, 2026
        </div>
    </div>

    <!-- Main Content: Map and Rules Table -->
    <div style="display: flex; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap;">
        <!-- Left Section: United States Jurisdiction Map -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 500px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">United States Jurisdiction Map</h2>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button style="width: 32px; height: 32px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #374151;">+</button>
                        <button style="width: 32px; height: 32px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #374151;">-</button>
                        <button style="width: 32px; height: 32px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #374151;">
                            <i class="fas fa-redo" style="font-size: 12px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Map Container (Placeholder) -->
                <div style="width: 100%; height: 400px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; position: relative; margin-bottom: 20px; display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center; color: #6b7280;">
                        <i class="fas fa-map" style="font-size: 48px; margin-bottom: 8px; opacity: 0.5;"></i>
                        <div style="font-size: 14px;">United States Map</div>
                        <!-- State markers would be here -->
                        <div style="position: absolute; top: 20px; left: 20px; padding: 4px 8px; background-color: #ffffff; border: 2px solid #f97316; border-radius: 4px; font-size: 12px; font-weight: 600; color: #111827;">CA</div>
                        <div style="position: absolute; top: 40px; left: 200px; padding: 4px 8px; background-color: #ffffff; border: 2px solid #f97316; border-radius: 4px; font-size: 12px; font-weight: 600; color: #111827;">NY</div>
                        <div style="position: absolute; top: 120px; left: 300px; padding: 4px 8px; background-color: #ffffff; border: 2px solid #f97316; border-radius: 4px; font-size: 12px; font-weight: 600; color: #111827;">TX</div>
                    </div>
                </div>

                <!-- Regulation Complexity Legend -->
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 12px;">Regulation Complexity Legend</h3>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 24px; height: 24px; border-radius: 4px; background-color: #d1fae5; border: 1px solid #10b981;"></div>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Permissive</span>
                            <span style="font-size: 12px; color: #6b7280; margin-left: auto;">Most products allowed.</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 24px; height: 24px; border-radius: 4px; background-color: #fef3c7; border: 1px solid #f59e0b;"></div>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Moderate</span>
                            <span style="font-size: 12px; color: #6b7280; margin-left: auto;">Some restrictions.</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 24px; height: 24px; border-radius: 4px; background-color: #fee2e2; border: 1px solid #ef4444;"></div>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Restrictive</span>
                            <span style="font-size: 12px; color: #6b7280; margin-left: auto;">Significant bans.</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 24px; height: 24px; border-radius: 4px; background-color: #f3f4f6; border: 1px solid #9ca3af;"></div>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">No Presence</span>
                            <span style="font-size: 12px; color: #6b7280; margin-left: auto;">Not active yet.</span>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <div style="text-align: center;">
                        <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">38</div>
                        <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">ACTIVE STATES</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">847</div>
                        <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL RULES</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 4px;">94%</div>
                        <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">COMPLIANCE RATE</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section: California Rules Table -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 600px;">
            <div style="padding: 20px;">
                <!-- Header -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-flag" style="color: #f97316; font-size: 20px;"></i>
                        <div>
                            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">California</h2>
                            <div style="font-size: 12px; color: #6b7280;">Last updated: Jan 28, 2026 by Sarah Johnson</div>
                        </div>
                    </div>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; cursor: pointer;">Edit Rules</button>
                </div>

                <!-- Summary Cards -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px;">
                    <div style="padding: 16px; background-color: #dbeafe; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: #1e40af; margin-bottom: 4px;">23</div>
                        <div style="font-size: 12px; color: #1e40af; font-weight: 500;">TOTAL RULES</div>
                    </div>
                    <div style="padding: 16px; background-color: #d1fae5; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: #065f46; margin-bottom: 4px;">450</div>
                        <div style="font-size: 12px; color: #065f46; font-weight: 500;">PRODUCTS ALLOWED</div>
                    </div>
                    <div style="padding: 16px; background-color: #fef3c7; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: #92400e; margin-bottom: 4px;">34</div>
                        <div style="font-size: 12px; color: #92400e; font-weight: 500;">RESTRICTED</div>
                    </div>
                    <div style="padding: 16px; background-color: #fee2e2; border-radius: 8px; text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">12</div>
                        <div style="font-size: 12px; color: #991b1b; font-weight: 500;">BANNED</div>
                    </div>
                </div>

                <!-- Tabs and Add Rule Button -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 2px solid #e5e7eb;">
                    <div style="display: flex; gap: 0;">
                        <button class="tab-button active" data-tab="rules-table" style="padding: 12px 24px; font-size: 14px; font-weight: 600; color: #111827; background-color: transparent; border: none; border-bottom: 3px solid #f59e0b; cursor: pointer; margin-bottom: -2px;">Rules Table</button>
                        <button class="tab-button" data-tab="change-log" style="padding: 12px 24px; font-size: 14px; font-weight: 500; color: #6b7280; background-color: transparent; border: none; cursor: pointer; margin-bottom: -2px;">Change Log</button>
                    </div>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #a855f7; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-plus" style="font-size: 12px;"></i>
                        <span>Add Rule</span>
                    </button>
                </div>

                <!-- Rules Table -->
                <div id="rules-table-content" class="tab-content" style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                        <thead style="background-color: #ffffff !important;">
                            <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PRODUCT CLASSIFICATION</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">STATUS</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">CONDITIONS</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">EFFECTIVE DATE</th>
                                <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">LEGAL SOURCE</th>
                                <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Tobacco Products</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px;">
                                        Allowed
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Retail license required, T21 age verification</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Jan 2020</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">State Code § XX</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 2 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Flavored Nicotine E-Liquid</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #991b1b; background-color: #fee2e2; border-radius: 25px;">
                                        Banned
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">All flavors including menthol</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Jun 2024</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">SB 793</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 3 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Hemp-Derived THC</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px;">
                                        Allowed
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Max 0.3% delta-9, COA mandatory</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Mar 2023</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">State Hemp Act</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 4 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">CBD Products</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px;">
                                        Allowed
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">No restrictions</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Feb 2022</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Farm Bill compliance</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 5 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Disposable Vapes</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #92400e; background-color: #fef3c7; border-radius: 25px;">
                                        Restricted
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Child-resistant packaging required</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Apr 2026</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">AB 2571</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 6 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Kratom Products</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px;">
                                        Allowed
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Age 21+ verification required</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Jan 2023</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">SB 57</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 7 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Nicotine Pouches</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px;">
                                        Allowed
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Standard tobacco regulations apply</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Jan 2020</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">State Code § XX</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                            
                            <!-- Row 8 -->
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Rolling Papers & Accessories</td>
                                <td style="padding: 16px 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px;">
                                        Allowed
                                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                                    </span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">No restrictions</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">-</td>
                                <td style="padding: 16px 12px; font-size: 14px; color: #374151;">No specific legislation</td>
                                <td style="padding: 16px 12px; text-align: center;">
                                    <i class="fas fa-pencil-alt" style="color: #6b7280; font-size: 14px; cursor: pointer;"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px;">
                    <div style="font-size: 14px; color: #374151;">
                        Showing <span style="font-weight: 600; color: #111827;">8</span> of <span style="font-weight: 600; color: #111827;">23</span> rules
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">Previous</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #f97316; border: 1px solid #f97316; border-radius: 6px; cursor: pointer;">1</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">2</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">3</button>
                        <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">Next</button>
                    </div>
                </div>

                <!-- Change Log Content -->
                <div id="change-log-content" class="tab-content" style="display: none;">
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        <i class="fas fa-history" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                        <div style="font-size: 16px; font-weight: 500;">View detailed change log below</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- create new ui layout as per the ss -->
    </div>

    <!-- Change Log Section -->
    <div id="change-log-section" style="margin-top: 24px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; display: none;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">California Rules Change Log</h2>
                <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-download" style="font-size: 12px;"></i>
                    <span>Export Log</span>
                </button>
            </div>

            <!-- Change Log Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">DATE & TIME</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ADMIN</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">RULE CHANGED</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PREVIOUS VALUE</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">NEW VALUE</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">REASON</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">
                                <div style="font-weight: 500; color: #111827;">Jan 28, 2026</div>
                                <div style="font-size: 12px; color: #6b7280;">2:34 PM</div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">SJ</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Sarah Johnson</div>
                                        <div style="font-size: 12px; color: #6b7280;">Super Admin</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Disposable Vapes (Status & Conditions)</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px; width: fit-content;">Allowed</span>
                                    <span style="font-size: 12px; color: #374151;">No restrictions</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #92400e; background-color: #fef3c7; border-radius: 25px; width: fit-content;">Restricted</span>
                                    <span style="font-size: 12px; color: #374151;">Child-resistant packaging required</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">New AB 2571 legislation effective April 2026</td>
                        </tr>
                        
                        <!-- Row 2 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">
                                <div style="font-weight: 500; color: #111827;">Jan 15, 2026</div>
                                <div style="font-size: 12px; color: #6b7280;">10:22 AM</div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">MC</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Michael Chen</div>
                                        <div style="font-size: 12px; color: #6b7280;">Compliance Manager</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Hemp-Derived THC (Conditions updated)</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Max 0.3% delta-9</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Max 0.3% delta-9, COA mandatory</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Added COA requirement for enhanced compliance tracking</td>
                        </tr>
                        
                        <!-- Row 3 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">
                                <div style="font-weight: 500; color: #111827;">Dec 20, 2025</div>
                                <div style="font-size: 12px; color: #6b7280;">4:15 PM</div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #a855f7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">SJ</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Sarah Johnson</div>
                                        <div style="font-size: 12px; color: #6b7280;">Super Admin</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Kratom Products (New rule added)</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #6b7280; font-style: italic;">-</td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #065f46; background-color: #d1fae5; border-radius: 25px; width: fit-content;">Allowed</span>
                                    <span style="font-size: 12px; color: #374151;">Age 21+ verification required</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">SB 57 signed into law, effective Jan 2023</td>
                        </tr>
                        
                        <!-- Row 4 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">
                                <div style="font-weight: 500; color: #111827;">Nov 08, 2025</div>
                                <div style="font-size: 12px; color: #6b7280;">11:45 AM</div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">ER</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">Emily Rodriguez</div>
                                        <div style="font-size: 12px; color: #6b7280;">Compliance Analyst</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">CBD Products (Legal source updated)</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">2018 Farm Bill</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Farm Bill compliance</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Updated reference for clarity</td>
                        </tr>
                        
                        <!-- Row 5 -->
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">
                                <div style="font-weight: 500; color: #111827;">Oct 12, 2025</div>
                                <div style="font-size: 12px; color: #6b7280;">3:20 PM</div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f97316; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">JA</div>
                                    <div>
                                        <div style="font-size: 14px; color: #111827; font-weight: 500;">James Anderson</div>
                                        <div style="font-size: 12px; color: #6b7280;">Super Admin</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827; font-weight: 500;">Nicotine Pouches (Legal source added)</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #6b7280; font-style: italic;">-</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">State Code § XX</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Added legal reference for documentation</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 14px; color: #374151;">
                    Showing <span style="font-weight: 600; color: #111827;">5</span> of <span style="font-weight: 600; color: #111827;">47</span> changes
                </div>
                <a href="#" style="font-size: 14px; color: #a855f7; font-weight: 500; text-decoration: none;">View Full History</a>
            </div>
        </div>
    </div>

    <!-- Three Cards Row -->
    <div id="change-log-cards" style="display: none; flex-direction: row; gap: 16px; width: 100%; flex-wrap: wrap; margin-top: 24px;">
        <!-- Rule Status Breakdown Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 20px; position: relative;">
                <div style="position: absolute; top: 20px; right: 20px;">
                    <i class="fas fa-chart-pie" style="color: #a855f7; font-size: 20px; opacity: 0.3;"></i>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Rule Status Breakdown</h3>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #10b981; flex-shrink: 0;"></div>
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Allowed</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600; margin-left: auto;">450 products</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #f59e0b; flex-shrink: 0;"></div>
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Restricted</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600; margin-left: auto;">34 products</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444; flex-shrink: 0;"></div>
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Banned</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600; margin-left: auto;">12 products</span>
                    </div>
                </div>
                <div style="padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">COMPLIANCE RATE</div>
                    <div style="font-size: 32px; font-weight: 700; color: #10b981;">91.5%</div>
                </div>
            </div>
        </div>

        <!-- Active Sellers in CA Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 20px; position: relative;">
                <div style="position: absolute; top: 20px; right: 20px;">
                    <i class="fas fa-shopping-bag" style="color: #3b82f6; font-size: 20px; opacity: 0.3;"></i>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">Active Sellers in CA</h3>
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 20px;">Sellers currently operating in California</div>
                <div style="font-size: 48px; font-weight: 700; color: #111827; margin-bottom: 20px;">127</div>
                <div style="display: flex; flex-direction: column; gap: 8px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #111827;">Fully Compliant</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">116</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #111827;">Minor Issues</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">8</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 14px; color: #111827;">Under Review</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">3</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; flex: 1; min-width: 300px;">
            <div style="padding: 20px; position: relative;">
                <div style="position: absolute; top: 20px; right: 20px;">
                    <i class="fas fa-bolt" style="color: #f59e0b; font-size: 20px; opacity: 0.3;"></i>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s; text-align: left;" onmouseover="this.style.backgroundColor='#f3f4f6';" onmouseout="this.style.backgroundColor='#f9fafb';">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-file-alt" style="color: #3b82f6; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Copy Rules to Another State</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                    </button>
                    <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s; text-align: left;" onmouseover="this.style.backgroundColor='#f3f4f6';" onmouseout="this.style.backgroundColor='#f9fafb';">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-download" style="color: #10b981; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Export Rules Template</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                    </button>
                    <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s; text-align: left;" onmouseover="this.style.backgroundColor='#f3f4f6';" onmouseout="this.style.backgroundColor='#f9fafb';">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-bell" style="color: #f97316; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">Notify Affected Sellers</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                    </button>
                    <button style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s; text-align: left;" onmouseover="this.style.backgroundColor='#f3f4f6';" onmouseout="this.style.backgroundColor='#f9fafb';">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-clock" style="color: #a855f7; font-size: 16px;"></i>
                            <span style="font-size: 14px; color: #111827; font-weight: 500;">View Full Change History</span>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #6b7280; font-size: 12px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Remove active class from all tabs
                tabButtons.forEach(btn => {
                    btn.style.fontWeight = '500';
                    btn.style.color = '#6b7280';
                    btn.style.borderBottom = 'none';
                });
                
                // Add active class to clicked tab
                this.style.fontWeight = '600';
                this.style.color = '#111827';
                this.style.borderBottom = '3px solid #f59e0b';
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
                
                // Show selected tab content
                const targetContent = document.getElementById(targetTab + '-content');
                const changeLogSection = document.getElementById('change-log-section');
                const changeLogCards = document.getElementById('change-log-cards');
                
                if (targetTab === 'rules-table') {
                    // Show rules table, hide change log section
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }
                    if (changeLogSection) {
                        changeLogSection.style.display = 'none';
                    }
                    if (changeLogCards) {
                        changeLogCards.style.display = 'none';
                    }
                } else if (targetTab === 'change-log') {
                    // Hide rules table, show change log section
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }
                    if (changeLogSection) {
                        changeLogSection.style.display = 'block';
                    }
                    if (changeLogCards) {
                        changeLogCards.style.display = 'flex';
                    }
                }
            });
        });
    });
</script>
@endsection

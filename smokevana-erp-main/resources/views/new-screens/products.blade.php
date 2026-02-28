@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Statistics Bar -->
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <button class="status-tab" data-status="pending-review" style="padding: 8px 16px; font-size: 14px; font-weight: 600; color: #111827; background-color: #fed7aa; border: 1px solid #f97316; border-radius: 25px; cursor: pointer;">
            Pending Review <span style="background-color: #f97316; color: #ffffff; padding: 2px 8px; border-radius: 12px; margin-left: 6px; font-size: 12px;">47</span>
        </button>
        <button class="status-tab" data-status="approved-today" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 25px; cursor: pointer;">
            Approved Today <span style="color: #111827; font-weight: 600; margin-left: 6px;">23</span>
        </button>
        <button class="status-tab" data-status="rejected" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 25px; cursor: pointer;">
            Rejected <span style="color: #111827; font-weight: 600; margin-left: 6px;">8</span>
        </button>
        <button class="status-tab" data-status="flagged" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 25px; cursor: pointer;">
            Flagged <span style="color: #111827; font-weight: 600; margin-left: 6px;">5</span>
        </button>
        <button class="status-tab" data-status="reported-by-buyers" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 25px; cursor: pointer;">
            Reported by Buyers <span style="color: #111827; font-weight: 600; margin-left: 6px;">3</span>
        </button>
    </div>

    <!-- Filters & Search Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header with Clear All -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Filters & Search</h2>
                <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Clear All</button>
            </div>

            <!-- Search and Filters Row -->
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 12px; flex-wrap: wrap;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;"></i>
                    <input type="text" placeholder="Product name or SKU" style="width: 100%; padding: 10px 12px 10px 36px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#d1d5db';">
                </div>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Category: All</option>
                </select>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Seller: All</option>
                </select>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Compliance Issue: All</option>
                </select>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Date: Oldest First</option>
                    <option>Date: Newest First</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Moderation Queue Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="padding: 20px;">
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">Moderation Queue</h2>
                    <div style="font-size: 14px; color: #374151;">
                        Showing <span id="showing-range" style="font-weight: 600; color: #111827;">1-10</span> of <span id="total-count" style="font-weight: 600; color: #111827;">47</span> products
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                        <span>Advanced Filters</span>
                    </button>
                    <button style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download" style="font-size: 12px;"></i>
                        <span>Export</span>
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">IMAGE</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PRODUCT NAME</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">SELLER</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">CATEGORY</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">SUBMITTED</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">COMPLIANCE</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">RISK LEVEL</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        <!-- Row 1 - Pending Review -->
                        <tr class="product-row" data-status="pending-review" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #9ca3af; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Premium Glass Water Pipe - 12" Blue</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: WP-BLU-12-001</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Pacific Smoke</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #a855f7; background-color: #e9d5ff; border-radius: 25px;">Accessories</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 18, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Issues Found (3)</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></div>
                                    <span style="font-size: 14px; color: #111827;">Critical</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 2 - Pending Review -->
                        <tr class="product-row" data-status="pending-review" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #10b981; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Full Spectrum CBD Oil 1000mg</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: CBD-FS-1000</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">GreenLeaf Wholesale</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Hemp/CBD</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 19, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Issues Found (2)</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></div>
                                    <span style="font-size: 14px; color: #111827;">High</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 3 - Pending Review -->
                        <tr class="product-row" data-status="pending-review" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #dbeafe; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #3b82f6; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Disposable Vape Pen - Mint Ice 5000 Puffs</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: DVP-MINT-5K</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">VaporWave Dist</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Vape</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 20, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Pending (1)</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #f59e0b;"></div>
                                    <span style="font-size: 14px; color: #111827;">Medium</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 4 - Pending Review -->
                        <tr class="product-row" data-status="pending-review" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #fef3c7; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #f59e0b; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Premium Cedar Cigar Humidor - 50 Count</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: HUM-CED-50</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Premier Tobacco</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #a855f7; background-color: #e9d5ff; border-radius: 25px;">Accessories</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 21, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Issues Found (1)</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #f59e0b;"></div>
                                    <span style="font-size: 14px; color: #111827;">Medium</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 5 - Approved Today -->
                        <tr class="product-row" data-status="approved-today" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #9ca3af; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Organic Hemp Rolling Papers King Size (50 Pack)</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: RP-ORG-KS-50</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">HighTide Supply</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #a855f7; background-color: #e9d5ff; border-radius: 25px;">Accessories</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 22, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">All Passed</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                    <span style="font-size: 14px; color: #111827;">Low</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 6 - Approved Today -->
                        <tr class="product-row" data-status="approved-today" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #1f2937; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #ffffff; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Advanced Vape Mod Kit 200W - Black</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: VM-ADV-200-BK</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">WestCoast Vape</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Vape</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 23, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">All Passed</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                    <span style="font-size: 14px; color: #111827;">Low</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 7 - Pending Review -->
                        <tr class="product-row" data-status="pending-review" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background: linear-gradient(135deg, #f59e0b 0%, #ec4899 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #ffffff; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Delta-9 THC Gummies 10mg - Mixed Fruit (20ct)</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: THC-GUM-10-MF-20</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Rocky Mountain Hemp</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f97316; background-color: #fed7aa; border-radius: 25px;">THC Products</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 24, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Pending (2)</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #f59e0b;"></div>
                                    <span style="font-size: 14px; color: #111827;">Medium</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 8 - Approved Today -->
                        <tr class="product-row" data-status="approved-today" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #6b7280; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Stainless Steel Cigar Cutter - Double Blade</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: CC-SS-DBL</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Golden State Tobacco</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #a855f7; background-color: #e9d5ff; border-radius: 25px;">Accessories</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 25, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">All Passed</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                    <span style="font-size: 14px; color: #111827;">Low</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 9 - Approved Today -->
                        <tr class="product-row" data-status="approved-today" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #d1fae5; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #10b981; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">CBD Vape Cartridge 500mg - Natural Hemp</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: CBD-CART-500-NH</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Northwest Hemp</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Hemp/CBD</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 26, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">All Passed</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                    <span style="font-size: 14px; color: #111827;">Low</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 10 - Approved Today -->
                        <tr class="product-row" data-status="approved-today" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #6b7280; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Portable Herb Vaporizer - Silver Edition</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: HV-PORT-SIL</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Desert Sky Wholesale</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Vape</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 27, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">All Passed</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></div>
                                    <span style="font-size: 14px; color: #111827;">Low</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 11 - Rejected -->
                        <tr class="product-row" data-status="rejected" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #fee2e2; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #ef4444; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Prohibited Product Example</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: PROD-REJ-001</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Test Seller</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Rejected</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 15, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Rejected</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></div>
                                    <span style="font-size: 14px; color: #111827;">Critical</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 12 - Flagged -->
                        <tr class="product-row" data-status="flagged" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #fef3c7; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #f59e0b; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Suspicious Product Listing</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: PROD-FLG-001</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Flagged Seller</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Flagged</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 16, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Flagged</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #f59e0b;"></div>
                                    <span style="font-size: 14px; color: #111827;">High</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                        
                        <!-- Row 13 - Reported by Buyers -->
                        <tr class="product-row" data-status="reported-by-buyers" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px;">
                                <div style="width: 60px; height: 60px; border-radius: 6px; background-color: #fee2e2; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <i class="fas fa-image" style="color: #ef4444; font-size: 24px;"></i>
                                </div>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px;">Buyer Reported Product</div>
                                <div style="font-size: 12px; color: #6b7280;">SKU: PROD-RPT-001</div>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Reported Seller</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Reported</span>
                            </td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 17, 2024</td>
                            <td style="padding: 16px 12px;">
                                <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Reported</span>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></div>
                                    <span style="font-size: 14px; color: #111827;">High</span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center;">
                                <button style="padding: 6px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #f97316; border: none; border-radius: 6px; cursor: pointer;">Review</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 14px; color: #374151;">
                    Showing <span id="pagination-range" style="font-weight: 600; color: #111827;">1-10</span> of <span id="pagination-total" style="font-weight: 600; color: #111827;">47</span> products
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">&lt;</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #f97316; border: 1px solid #f97316; border-radius: 6px; cursor: pointer;">1</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">2</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">3</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">4</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">5</button>
                    <button style="padding: 8px 12px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb';" onmouseout="this.style.backgroundColor='#ffffff';">&gt;</button>
                </div>
            </div>
        </div>
    </div>

    <!-- create new ui layout as per the ss -->
    
    <!-- Top Row - Key Performance Indicators -->
    <div style="display: grid;  margin-top:25px; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <!-- Pending Review Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px; position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #fee2e2; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 20px;"></i>
                </div>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">47</div>
            <div style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">PENDING REVIEW</div>
        </div>

        <!-- Approved Today Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px; position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-check-circle" style="color: #10b981; font-size: 20px;"></i>
                </div>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">23</div>
            <div style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">APPROVED TODAY</div>
        </div>

        <!-- Rejected Today Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px; position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #fee2e2; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-times-circle" style="color: #ef4444; font-size: 20px;"></i>
                </div>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">8</div>
            <div style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">REJECTED TODAY</div>
        </div>

        <!-- Avg Review Time Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px; position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #fef3c7; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clock" style="color: #f59e0b; font-size: 20px;"></i>
                </div>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">2.3</div>
            <div style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">AVG REVIEW TIME (HRS)</div>
        </div>
    </div>

    <!-- Middle Row - Detailed Metrics -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <!-- Top Compliance Issues Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 16px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Top Compliance Issues</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Image Quality -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Image Quality</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">18</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 100%; height: 100%; background-color: #ef4444; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- Missing COA -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Missing COA</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">12</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 66.67%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- Prohibited Claims -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Prohibited Claims</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">9</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 50%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                    </div>
                </div>
                <!-- Category Mismatch -->
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 14px; color: #111827; font-weight: 500;">Category Mismatch</span>
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">8</span>
                    </div>
                    <div style="width: 100%; height: 8px; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="width: 44.44%; height: 100%; background-color: #f97316; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Rate (7d) Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <i class="fas fa-clock" style="color: #f59e0b; font-size: 16px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Approval Rate (7d)</h3>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 20px; position: relative;">
                <canvas id="approvalRateChart" width="200" height="200" style="max-width: 200px; max-height: 200px;"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                    <div style="font-size: 24px; font-weight: 700; color: #10b981;">85%</div>
                    <div style="font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase;">APPROVED</div>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #10b981;"></div>
                    <span style="font-size: 14px; color: #111827;">Approved (127)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444;"></div>
                    <span style="font-size: 14px; color: #111827;">Rejected (22)</span>
                </div>
            </div>
        </div>

        <!-- Moderator Activity Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <i class="fas fa-user" style="color: #f59e0b; font-size: 16px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Moderator Activity</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Sarah Johnson -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 600; font-size: 14px;">SJ</div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 2px;">Sarah Johnson</div>
                        <div style="font-size: 12px; color: #6b7280;">Senior Moderator</div>
                    </div>
                    <div style="font-size: 14px; font-weight: 600; color: #111827;">34 <span style="font-weight: 400; color: #6b7280;">Today</span></div>
                </div>
                <!-- Michael Chen -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 600; font-size: 14px;">MC</div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 2px;">Michael Chen</div>
                        <div style="font-size: 12px; color: #6b7280;">Moderator</div>
                    </div>
                    <div style="font-size: 14px; font-weight: 600; color: #111827;">28 <span style="font-weight: 400; color: #6b7280;">Today</span></div>
                </div>
                <!-- Emily Rodriguez -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 600; font-size: 14px;">ER</div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 2px;">Emily Rodriguez</div>
                        <div style="font-size: 12px; color: #6b7280;">Moderator</div>
                    </div>
                    <div style="font-size: 14px; font-weight: 600; color: #111827;">21 <span style="font-weight: 400; color: #6b7280;">Today</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row - Recent Activity Log -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-sync-alt" style="color: #f97316; font-size: 16px;"></i>
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Recent Moderation Decisions</h3>
            </div>
            <a href="#" style="font-size: 14px; font-weight: 500; color: #3b82f6; text-decoration: none;">View All</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Activity Item 1 -->
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; margin-top: 6px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div style="font-size: 14px; color: #111827;">
                        <span style="font-weight: 600;">Sarah Johnson</span> approved <span style="font-weight: 600;">CBD Vape Cartridge 500mg</span> by <span style="font-weight: 600;">Northwest Hemp</span>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">3 minutes ago</div>
                </div>
            </div>
            <!-- Activity Item 2 -->
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444; margin-top: 6px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div style="font-size: 14px; color: #111827;">
                        <span style="font-weight: 600;">Michael Chen</span> rejected <span style="font-weight: 600;">Budget Glass Pipe</span> by <span style="font-weight: 600;">Discount Smoke Shop</span> - Reason: Poor image quality
                    </div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">12 minutes ago</div>
                </div>
            </div>
            <!-- Activity Item 3 -->
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #f97316; margin-top: 6px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div style="font-size: 14px; color: #111827;">
                        <span style="font-weight: 600;">Emily Rodriguez</span> requested changes for <span style="font-weight: 600;">Premium Vape Mod</span> by <span style="font-weight: 600;">VaporWave Dist</span> - Missing PMTA documentation
                    </div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">18 minutes ago</div>
                </div>
            </div>
            <!-- Activity Item 4 -->
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; margin-top: 6px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div style="font-size: 14px; color: #111827;">
                        <span style="font-weight: 600;">Sarah Johnson</span> approved <span style="font-weight: 600;">Organic Rolling Papers</span> by <span style="font-weight: 600;">HighTide Supply</span>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">25 minutes ago</div>
                </div>
            </div>
            <!-- Activity Item 5 -->
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; margin-top: 6px; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div style="font-size: 14px; color: #111827;">
                        <span style="font-weight: 600;">Michael Chen</span> approved <span style="font-weight: 600;">Cedar Cigar Humidor</span> by <span style="font-weight: 600;">Premier Tobacco</span>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">32 minutes ago</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusTabs = document.querySelectorAll('.status-tab');
    const productRows = document.querySelectorAll('.product-row');
    const showingRange = document.getElementById('showing-range');
    const totalCount = document.getElementById('total-count');
    const paginationRange = document.getElementById('pagination-range');
    const paginationTotal = document.getElementById('pagination-total');

    // Status counts
    const statusCounts = {
        'pending-review': 47,
        'approved-today': 23,
        'rejected': 8,
        'flagged': 5,
        'reported-by-buyers': 3
    };

    // Filter function
    function filterTable(status) {
        let visibleCount = 0;
        
        productRows.forEach((row) => {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counts
        const count = status === 'all' ? productRows.length : statusCounts[status] || 0;
        const start = visibleCount > 0 ? 1 : 0;
        const end = visibleCount;

        showingRange.textContent = visibleCount > 0 ? `${start}-${end}` : '0-0';
        totalCount.textContent = count;
        paginationRange.textContent = visibleCount > 0 ? `${start}-${end}` : '0-0';
        paginationTotal.textContent = count;
    }

    // Tab click handlers
    statusTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.getAttribute('data-status');

            // Update active tab styling
            statusTabs.forEach(t => {
                if (t === this) {
                    // Active state
                    t.style.fontWeight = '600';
                    t.style.color = '#111827';
                    t.style.backgroundColor = '#fed7aa';
                    t.style.borderColor = '#f97316';
                    // Update span styling for active tab
                    const span = t.querySelector('span');
                    if (span) {
                        span.style.backgroundColor = '#f97316';
                        span.style.color = '#ffffff';
                    }
                } else {
                    // Inactive state
                    t.style.fontWeight = '500';
                    t.style.color = '#374151';
                    t.style.backgroundColor = '#ffffff';
                    t.style.borderColor = '#d1d5db';
                    // Update span styling for inactive tabs
                    const span = t.querySelector('span');
                    if (span) {
                        span.style.backgroundColor = 'transparent';
                        span.style.color = '#111827';
                    }
                }
            });

            // Filter table
            filterTable(status);
        });
    });

    // Initialize with pending-review (default active)
    filterTable('pending-review');

    // Draw Approval Rate Donut Chart
    const canvas = document.getElementById('approvalRateChart');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = 70;
        const lineWidth = 20;

        // Draw background circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
        ctx.strokeStyle = '#f3f4f6';
        ctx.lineWidth = lineWidth;
        ctx.stroke();

        // Draw approved arc (85%)
        const approvedPercentage = 0.85;
        const startAngle = -Math.PI / 2;
        const endAngle = startAngle + (2 * Math.PI * approvedPercentage);
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.strokeStyle = '#10b981';
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Draw rejected arc (15%)
        const rejectedPercentage = 0.15;
        const rejectedStartAngle = endAngle;
        const rejectedEndAngle = rejectedStartAngle + (2 * Math.PI * rejectedPercentage);
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, rejectedStartAngle, rejectedEndAngle);
        ctx.strokeStyle = '#ef4444';
        ctx.lineWidth = lineWidth;
        ctx.lineCap = 'round';
        ctx.stroke();
    }
});
</script>
@endsection

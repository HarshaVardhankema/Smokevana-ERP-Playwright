@extends('layouts.app')
@section('title', 'Orders')

@section('content')
<div style="padding: 24px; background-color: #f3f4f6; min-height: 100vh; overflow-x: hidden;">
    <!-- Summary Statistics Section -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <!-- Total Orders Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #10b981, #f59e0b);"></div>
            <div style="padding: 20px;">
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart" style="color: #10b981; font-size: 20px;"></i>
                    </div>
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Orders (This Month)</div>
                <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">4,230</div>
            </div>
        </div>

        <!-- GMV Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #3b82f6, #10b981);"></div>
            <div style="padding: 20px;">
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dollar-sign" style="color: #3b82f6; font-size: 20px;"></i>
                    </div>
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">GMV</div>
                <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">$347,820</div>
            </div>
        </div>

        <!-- Platform Fees Collected Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #a855f7, #ec4899);"></div>
            <div style="padding: 20px;">
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #e9d5ff; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-badge-check" style="color: #a855f7; font-size: 20px;"></i>
                    </div>
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Platform Fees Collected</div>
                <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">$48,295</div>
            </div>
        </div>

        <!-- Avg Take Rate Card -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(to right, #f59e0b, #ef4444);"></div>
            <div style="padding: 20px;">
                <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background-color: #fef3c7; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-percentage" style="color: #f59e0b; font-size: 20px;"></i>
                    </div>
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Avg Take Rate</div>
                <div style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">13.9%</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Order Status Filters -->
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="status-filter" data-status="all" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #111827; background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; cursor: pointer;">All <span style="font-weight: 600;">4230</span></button>
                <button class="status-filter" data-status="pending" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Pending <span style="font-weight: 600;">120</span></button>
                <button class="status-filter" data-status="processing" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Processing <span style="font-weight: 600;">340</span></button>
                <button class="status-filter" data-status="shipped" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Shipped <span style="font-weight: 600;">2800</span></button>
                <button class="status-filter" data-status="delivered" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Delivered <span style="font-weight: 600;">890</span></button>
                <button class="status-filter" data-status="disputed" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #ef4444; border: 1px solid #ef4444; border-radius: 6px; cursor: pointer;">Disputed <span style="font-weight: 600;">24</span></button>
                <button class="status-filter" data-status="cancelled" style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Cancelled <span style="font-weight: 600;">56</span></button>
            </div>

            <!-- Search and Filters Row -->
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr; gap: 12px; margin-bottom: 12px; flex-wrap: wrap;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 14px;"></i>
                    <input type="text" placeholder="Order # / Seller / Buyer" style="width: 100%; padding: 10px 12px 10px 36px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#d1d5db';">
                </div>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Date Range: All Time</option>
                    <option>Today</option>
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Seller: All</option>
                </select>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Buyer: All</option>
                </select>
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>State: All</option>
                </select>
            </div>

            <!-- Second Row Filters -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; flex-wrap: wrap;">
                <select style="padding: 10px 32px 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; background-color: #ffffff; color: #374151; cursor: pointer; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23374151\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>'); background-repeat: no-repeat; background-position: right 8px center; background-size: 16px;">
                    <option>Fulfillment: All</option>
                    <option>FBS</option>
                    <option>Seller</option>
                </select>
                <input type="text" placeholder="Amount Range (e.g., $100-$500)" style="padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#d1d5db';">
                <input type="text" placeholder="Fee Rate Range (e.g., 10%-15%)" style="padding: 10px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#d1d5db';">
            </div>
        </div>
    </div>

    <!-- All Orders Table Section -->
    <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
        <div style="padding: 20px;">
            <!-- Table Header Info -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 16px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">All Orders</h2>
                <div style="font-size: 14px; color: #374151;">
                    Showing <span style="font-weight: 600; color: #111827;">1-10</span> of <span style="font-weight: 600; color: #111827;">4,230</span> orders
                </div>
            </div>

            <!-- Orders Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background-color: #ffffff;">
                    <thead style="background-color: #ffffff !important;">
                        <tr style="border-bottom: 1px solid #e5e7eb; background-color: #ffffff !important;">
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; cursor: pointer;">ORDER #</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; cursor: pointer;">DATE</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; cursor: pointer;">SELLER</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; cursor: pointer;">BUYER</th>
                            <th style="padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; cursor: pointer;">STATE</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">ITEMS</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important; cursor: pointer;">ORDER TOTAL</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">FEE RATE</th>
                            <th style="padding: 12px; text-align: right; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PLATFORM FEE</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">FULFILLMENT</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">PAYMENT</th>
                            <th style="padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000 !important; text-transform: uppercase; letter-spacing: 0.5px; background-color: #ffffff !important;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1 -->
                        <tr class="order-row" data-status="processing" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4230</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 14:23</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CloudCity Distribution</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Sunset Dispensary</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CA</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">8</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$2,450.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">12.0%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$294.00</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">FBS</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Processing</span></td>
                        </tr>
                        
                        <!-- Row 2 -->
                        <tr class="order-row" data-status="shipped" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4229</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 13:47</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">PureLeaf Wholesale</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Green Valley Retail</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">NV</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">15</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$4,890.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">13.5%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$660.15</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Seller</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Shipped</span></td>
                        </tr>
                        
                        <!-- Row 3 -->
                        <tr class="order-row" data-status="delivered" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4228</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 12:15</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">VaporTech Supply</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CloudNine Shop</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CO</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">22</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$6,780.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">14.0%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$949.20</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">FBS</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Delivered</span></td>
                        </tr>
                        
                        <!-- Row 4 -->
                        <tr class="order-row" data-status="pending" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4227</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 11:03</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">HempKing Distributors</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Pacific Coast Wholesale</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">OR</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">5</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$1,230.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">11.5%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$141.45</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Seller</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Due</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Pending</span></td>
                        </tr>
                        
                        <!-- Row 5 -->
                        <tr class="order-row" data-status="shipped" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4226</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 10:28</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CloudCity Distribution</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Urban Smoke & Vape</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">NV</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">12</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$3,450.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">12.5%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$431.25</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">FBS</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Shipped</span></td>
                        </tr>
                        
                        <!-- Row 6 -->
                        <tr class="order-row" data-status="disputed" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4225</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 09:51</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">TobaccoKing Supply</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">HighTide Brands</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CA</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">18</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$5,670.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">13.0%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$737.10</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Seller</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Overdue</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ef4444; background-color: #fee2e2; border-radius: 25px;">Disputed</span></td>
                        </tr>
                        
                        <!-- Row 7 -->
                        <tr class="order-row" data-status="delivered" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4224</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 29, 08:12</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">PureLeaf Wholesale</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Coastal Vape Lounge</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CA</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">9</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$2,890.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">12.0%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$346.80</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">FBS</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Delivered</span></td>
                        </tr>
                        
                        <!-- Row 8 -->
                        <tr class="order-row" data-status="shipped" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4223</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 28, 16:45</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">VaporTech Supply</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">QuickStop Convenience</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CA</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">6</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$1,890.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">11.0%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$207.90</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Seller</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">Shipped</span></td>
                        </tr>
                        
                        <!-- Row 9 -->
                        <tr class="order-row" data-status="processing" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4222</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 28, 15:30</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CloudCity Distribution</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Elite Gas & Smoke</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CO</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">14</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$4,120.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">13.5%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$556.20</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #3b82f6; background-color: #dbeafe; border-radius: 25px;">FBS</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #f59e0b; background-color: #fef3c7; border-radius: 25px;">Processing</span></td>
                        </tr>
                        
                        <!-- Row 10 -->
                        <tr class="order-row" data-status="delivered" style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 16px 12px; font-size: 14px; color: #3b82f6; font-weight: 500;">#ORD-4221</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #374151;">Oct 28, 14:18</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">HempKing Distributors</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">Rocky Mountain Dispensary</td>
                            <td style="padding: 16px 12px; font-size: 14px; color: #111827;">CO</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">11</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$3,340.00</td>
                            <td style="padding: 16px 12px; text-align: center; font-size: 14px; color: #111827;">12.5%</td>
                            <td style="padding: 16px 12px; text-align: right; font-size: 14px; color: #111827; font-weight: 500;">$417.50</td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #6b7280; background-color: #f3f4f6; border-radius: 25px;">Seller</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Paid</span></td>
                            <td style="padding: 16px 12px; text-align: center;"><span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #10b981; background-color: #d1fae5; border-radius: 25px;">Delivered</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 14px; color: #374151;">
                    Showing <span style="font-weight: 600; color: #111827;">1-10</span> of <span style="font-weight: 600; color: #111827;">4,230</span> orders
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
    
    <!-- Flagged & At-Risk Orders Section -->
    <div style="background-color: #ffffff; margin-top:25px; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="padding: 20px;">
            <!-- Header with Icon and Badge -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <i class="fas fa-flag" style="color: #ef4444; font-size: 20px;"></i>
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Flagged & At-Risk Orders</h2>
                <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #ef4444; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">7</div>
            </div>
            
            <!-- Order Cards List -->
            <div style="display: flex; flex-direction: column; gap: 0;">
                <!-- Order 1 -->
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div style="font-size: 14px; color: #3b82f6; font-weight: 600;">#ORD-4225</div>
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border-radius: 25px;">DISPUTED</span>
                            <span style="font-size: 12px; color: #6b7280;">3 days old</span>
                        </div>
                        <div style="font-size: 14px; color: #3b82f6; font-weight: 500;">$737.10 fee at risk</div>
                    </div>
                    <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Payment overdue + buyer complaint about product quality</div>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: #6b7280;">
                        <span><strong style="color: #111827;">Assigned to:</strong> Sarah Johnson</span>
                        <span><strong style="color: #111827;">Seller:</strong> TobaccoKing Supply</span>
                        <span><strong style="color: #111827;">Buyer:</strong> HighTide Brands</span>
                    </div>
                </div>
                
                <!-- Order 2 -->
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div style="font-size: 14px; color: #3b82f6; font-weight: 600;">#ORD-4227</div>
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #111827; background-color: #fef3c7; border-radius: 25px;">PAYMENT DUE</span>
                            <span style="font-size: 12px; color: #6b7280;">2 days old</span>
                        </div>
                        <div style="font-size: 14px; color: #3b82f6; font-weight: 500;">$141.45 fee pending</div>
                    </div>
                    <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Payment pending verification + compliance check needed</div>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: #6b7280;">
                        <span><strong style="color: #111827;">Assigned to:</strong> Michael Chen</span>
                        <span><strong style="color: #111827;">Seller:</strong> HempKing Distributors</span>
                        <span><strong style="color: #111827;">Buyer:</strong> Pacific Coast Wholesale</span>
                    </div>
                </div>
                
                <!-- Order 3 -->
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div style="font-size: 14px; color: #3b82f6; font-weight: 600;">#ORD-4198</div>
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #111827; background-color: #fef3c7; border-radius: 25px;">SHIPPING DELAY</span>
                            <span style="font-size: 12px; color: #6b7280;">5 days old</span>
                        </div>
                        <div style="font-size: 14px; color: #3b82f6; font-weight: 500;">$523.40 fee collected</div>
                    </div>
                    <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Order marked as shipped 5 days ago, no tracking updates</div>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: #6b7280;">
                        <span><strong style="color: #111827;">Assigned to:</strong> Emily Rodriguez</span>
                        <span><strong style="color: #111827;">Seller:</strong> VaporTech Supply</span>
                        <span><strong style="color: #111827;">Buyer:</strong> Sunset Dispensary</span>
                    </div>
                </div>
                
                <!-- Order 4 -->
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div style="font-size: 14px; color: #3b82f6; font-weight: 600;">#ORD-4176</div>
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #ffffff; background-color: #ef4444; border-radius: 25px;">COMPLIANCE ALERT</span>
                            <span style="font-size: 12px; color: #6b7280;">8 days old</span>
                        </div>
                        <div style="font-size: 14px; color: #3b82f6; font-weight: 500;">$892.70 fee at risk</div>
                    </div>
                    <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Buyer's license expired before delivery, shipment held at warehouse</div>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: #6b7280;">
                        <span><strong style="color: #111827;">Assigned to:</strong> David Park</span>
                        <span><strong style="color: #111827;">Seller:</strong> CloudCity Distribution</span>
                        <span><strong style="color: #111827;">Buyer:</strong> Valley Vape Lounge</span>
                    </div>
                </div>
                
                <!-- Order 5 -->
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div style="font-size: 14px; color: #3b82f6; font-weight: 600;">#ORD-4143</div>
                            <span style="display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 500; color: #111827; background-color: #fef3c7; border-radius: 25px;">BUYER COMPLAINT</span>
                            <span style="font-size: 12px; color: #6b7280;">12 days old</span>
                        </div>
                        <div style="font-size: 14px; color: #3b82f6; font-weight: 500;">$456.30 fee collected</div>
                    </div>
                    <div style="font-size: 13px; color: #374151; margin-bottom: 8px;">Buyer claims incorrect product quantity received (ordered 20, got 15)</div>
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: #6b7280;">
                        <span><strong style="color: #111827;">Assigned to:</strong> Sarah Johnson</span>
                        <span><strong style="color: #111827;">Seller:</strong> PureLeaf Wholesale</span>
                        <span><strong style="color: #111827;">Buyer:</strong> Urban Smoke & Vape</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <!-- Order Volume Trend Chart -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
            <div style="padding: 20px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Order Volume Trend (Last 30 Days)</h2>
                <canvas id="orderVolumeChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution Chart -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
            <div style="padding: 20px;">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Order Status Distribution</h2>
                <canvas id="orderStatusChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Three Sections Row -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px;">
        <!-- Top Revenue Orders -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
            <div style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                    <i class="fas fa-trophy" style="color: #f59e0b; font-size: 18px;"></i>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Top Revenue Orders (This Month)</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #fef3c7; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">1</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">#ORD-4228</div>
                            <div style="font-size: 12px; color: #6b7280;">VaporTech Supply</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$6,780</div>
                            <div style="font-size: 12px; color: #3b82f6;">$949 fee</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f3f4f6; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">2</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">#ORD-4225</div>
                            <div style="font-size: 12px; color: #6b7280;">TobaccoKing Supply</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$5,670</div>
                            <div style="font-size: 12px; color: #3b82f6;">$737 fee</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #fed7aa; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">3</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">#ORD-4229</div>
                            <div style="font-size: 12px; color: #6b7280;">PureLeaf Wholesale</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$4,890</div>
                            <div style="font-size: 12px; color: #3b82f6;">$660 fee</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f3f4f6; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">4</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">#ORD-4222</div>
                            <div style="font-size: 12px; color: #6b7280;">CloudCity Distribution</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$4,120</div>
                            <div style="font-size: 12px; color: #3b82f6;">$556 fee</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #f3f4f6; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">5</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">#ORD-4226</div>
                            <div style="font-size: 12px; color: #6b7280;">CloudCity Distribution</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$3,450</div>
                            <div style="font-size: 12px; color: #3b82f6;">$431 fee</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Sellers by Order Volume -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
            <div style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                    <i class="fas fa-users" style="color: #3b82f6; font-size: 18px;"></i>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Top Sellers by Order Volume</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">CC</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">CloudCity Distribution</div>
                            <div style="font-size: 12px; color: #6b7280;">234 orders</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$89,450</div>
                            <div style="font-size: 12px; color: #3b82f6;">$11,230 fees</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">VT</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">VaporTech Supply</div>
                            <div style="font-size: 12px; color: #6b7280;">198 orders</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$76,890</div>
                            <div style="font-size: 12px; color: #3b82f6;">$9,567 fees</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #6b7280; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">PL</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">PureLeaf Wholesale</div>
                            <div style="font-size: 12px; color: #6b7280;">167 orders</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$64,230</div>
                            <div style="font-size: 12px; color: #3b82f6;">$8,345 fees</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f3f4f6; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">TK</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">TobaccoKing Supply</div>
                            <div style="font-size: 12px; color: #6b7280;">145 orders</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$58,670</div>
                            <div style="font-size: 12px; color: #3b82f6;">$7,234 fees</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; flex-shrink: 0;">HK</div>
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">HempKing Distributors</div>
                            <div style="font-size: 12px; color: #6b7280;">132 orders</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">$52,340</div>
                            <div style="font-size: 12px; color: #3b82f6;">$6,789 fees</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders by State -->
        <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); overflow: hidden;">
            <div style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                    <i class="fas fa-map-marker-alt" style="color: #f97316; font-size: 18px;"></i>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Orders by State</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #dbeafe; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;">CA</div>
                        <div style="flex: 1; font-size: 14px; font-weight: 600; color: #111827;">California</div>
                        <div style="font-size: 14px; font-weight: 600; color: #111827;">1,456 orders</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #d1fae5; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;">CO</div>
                        <div style="flex: 1; font-size: 14px; font-weight: 600; color: #111827;">Colorado</div>
                        <div style="font-size: 14px; font-weight: 600; color: #111827;">892 orders</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #e9d5ff; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;">NV</div>
                        <div style="flex: 1; font-size: 14px; font-weight: 600; color: #111827;">Nevada</div>
                        <div style="font-size: 14px; font-weight: 600; color: #111827;">678 orders</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #14b8a6; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;">OR</div>
                        <div style="flex: 1; font-size: 14px; font-weight: 600; color: #111827;">Oregon</div>
                        <div style="font-size: 14px; font-weight: 600; color: #111827;">534 orders</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background-color: #f9fafb; border-radius: 6px;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #fef3c7; color: #111827; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0;">WA</div>
                        <div style="flex: 1; font-size: 14px; font-weight: 600; color: #111827;">Washington</div>
                        <div style="font-size: 14px; font-weight: 600; color: #111827;">456 orders</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Order Volume Trend Chart
    document.addEventListener('DOMContentLoaded', function() {
        const volumeCtx = document.getElementById('orderVolumeChart');
        if (volumeCtx) {
            new Chart(volumeCtx, {
                type: 'line',
                data: {
                    labels: ['Oct 1', 'Oct 5', 'Oct 10', 'Oct 15', 'Oct 20', 'Oct 25', 'Oct 29'],
                    datasets: [{
                        label: 'Order Volume',
                        data: [120, 140, 110, 150, 180, 200, 270],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 300,
                            ticks: {
                                stepSize: 50
                            }
                        }
                    }
                }
            });
        }

        // Order Status Distribution Donut Chart
        const statusCtx = document.getElementById('orderStatusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Shipped', 'Delivered', 'Processing', 'Pending', 'Cancelled', 'Disputed'],
                    datasets: [{
                        data: [66.2, 21.0, 8.04, 2.04, 1.32, 0.56],
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#a855f7',
                            '#f59e0b',
                            '#ef4444',
                            '#6b7280'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>

<script>
    // Status Filter Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilters = document.querySelectorAll('.status-filter');
        const orderRows = document.querySelectorAll('.order-row');

        statusFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                const status = this.getAttribute('data-status');

                // Update button styles
                statusFilters.forEach(btn => {
                    btn.style.backgroundColor = '#ffffff';
                    btn.style.color = '#374151';
                    btn.style.borderColor = '#d1d5db';
                });

                if (status === 'all') {
                    this.style.backgroundColor = '#fef3c7';
                    this.style.color = '#111827';
                    this.style.borderColor = '#f59e0b';
                } else if (status === 'disputed') {
                    this.style.backgroundColor = '#ef4444';
                    this.style.color = '#ffffff';
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.backgroundColor = '#f3f4f6';
                    this.style.color = '#374151';
                    this.style.borderColor = '#d1d5db';
                }

                // Filter table rows
                orderRows.forEach(row => {
                    if (status === 'all' || row.getAttribute('data-status') === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection

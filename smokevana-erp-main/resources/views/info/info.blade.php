<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smokevana  ERP System - ER Diagram & Workflow</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10.6.1/dist/mermaid.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header p {
            color: #7f8c8d;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .nav-tab {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-tab:hover {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        .nav-tab.active {
            background: #2c3e50;
            color: white;
        }

        .content-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .content-section.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .diagram-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
            cursor: grab;
            transition: transform 0.3s ease;
        }

        .diagram-container:active {
            cursor: grabbing;
        }

        .diagram-container.dragging {
            cursor: grabbing;
        }

        .diagram-container .mermaid {
            text-align: center;
        }

        .diagram-container .mermaid svg {
            max-width: 100%;
            height: auto;
        }

        .diagram-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .diagram-btn {
            background: rgba(44, 62, 80, 0.9);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .diagram-btn:hover {
            background: #3498db;
            transform: scale(1.05);
        }

        .legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
            border: 2px solid;
        }

        .workflow-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .workflow-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .workflow-card:hover {
            transform: translateY(-5px);
        }

        .workflow-card h3 {
            margin-bottom: 15px;
            font-size: 1.3em;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 10px;
        }

        .workflow-card ul {
            list-style: none;
        }

        .workflow-card li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .workflow-card li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #2ecc71;
            font-weight: bold;
        }

        .tech-stack {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .tech-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #3498db;
        }

        .tech-item h4 {
            color: #3498db;
            margin-bottom: 8px;
        }

        .entity-highlight {
            background: #f39c12;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .relationship-highlight {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .clickable-entity {
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .clickable-entity:hover {
            background: #f39c12 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .clickable-entity:active {
            transform: scale(0.95);
        }

        .clickable-card {
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .clickable-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .clickable-card:active {
            transform: translateY(-4px);
        }

        .entity-diagram-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .entity-diagram-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: 95vw;
            height: 95vh;
            overflow: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .entity-diagram-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 12px 16px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            color: #2c3e50;
            z-index: 10000;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .entity-diagram-close:hover {
            background: #e74c3c;
            color: white;
            transform: scale(1.1);
        }

        .entity-diagram-title {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 1.5em;
            font-weight: bold;
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .zoom-info {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            color: #2c3e50;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .fullscreen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .fullscreen-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: 95vw;
            height: 95vh;
            overflow: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .fullscreen-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 12px 16px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            color: #2c3e50;
            z-index: 10000;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .fullscreen-close:hover {
            background: #e74c3c;
            color: white;
            transform: scale(1.1);
        }

        .fullscreen-diagram {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            cursor: grab;
        }

        .fullscreen-diagram:active {
            cursor: grabbing;
        }

        .fullscreen-diagram svg {
            min-width: 100%;
            min-height: 100%;
            transition: transform 0.3s ease;
        }

        .fullscreen-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 10001;
        }

        .fullscreen-zoom-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            color: #2c3e50;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .fullscreen-zoom-btn:hover {
            background: #3498db;
            color: white;
            transform: scale(1.05);
        }

        .fullscreen-zoom-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            color: #2c3e50;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .zoom-btn {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .zoom-btn:hover {
            background: #3498db;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .workflow-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Smokevana ERP System</h1>
            <p>Comprehensive Enterprise Resource Planning System - Entity Relationship Diagram & Workflow</p>
        </div>

        <div class="nav-tabs">
            <button type="button" class="nav-tab active" onclick="showSection('er-diagram')">📊 ER Diagram</button>
            <button type="button" class="nav-tab" onclick="showSection('capabilities')">⚡ Capabilities</button>
            <button type="button" class="nav-tab" onclick="showSection('entities')">📋 Entities</button>
            <button type="button" class="nav-tab" onclick="showSection('workflows')">🔄 Workflows</button>
            <button type="button" class="nav-tab" onclick="showSection('architecture')">🏗️ Architecture</button>
        </div>

        <!-- ER Diagram Section -->
        <div id="er-diagram" class="content-section active">
            <h2>📊 Entity Relationship Diagram</h2>
            <p>Interactive diagram showing the relationships between all major entities in the Smokevana ERP system.</p>
            
            <div class="diagram-container">
                <div class="diagram-controls">
                    <button class="diagram-btn" onclick="toggleLegend()" title="Toggle Legend">📊</button>
                    <button class="diagram-btn" onclick="resetView()" title="Reset View">🔄</button>
                    <button class="diagram-btn" onclick="exportDiagram()" title="Export as PNG">💾</button>
                    <button class="diagram-btn" onclick="exportSVG()" title="Export as SVG">📄</button>
                    <button class="diagram-btn" onclick="fullscreenMode()" title="Fullscreen">⛶</button>
                </div>
                
                <div class="legend" id="legend" style="display: none;">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #e1f5fe; border-color: #01579b;"></div>
                        <span>User Management</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #f3e5f5; border-color: #4a148c;"></div>
                        <span>Product Management</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #e8f5e8; border-color: #1b5e20;"></div>
                        <span>Sales Operations</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #fff3e0; border-color: #e65100;"></div>
                        <span>Purchase Operations</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #fce4ec; border-color: #880e4f;"></div>
                        <span>Customer Management</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #f1f8e9; border-color: #33691e;"></div>
                        <span>Supporting Systems</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #e0f2f1; border-color: #004d40;"></div>
                        <span>E-commerce</span>
                    </div>
                </div>
                
                <div class="mermaid" id="er-diagram-mermaid">
graph TD
    %% TOP: User Management
    subgraph TOP["👥 USER MANAGEMENT"]
        U[User]
        B[Business]
        BL[BusinessLocation]
        SA[StaffAuth]
        UCA[UserContactAccess]
    end
    
    %% CENTER: Product Management
    subgraph CENTER["📦 PRODUCT MANAGEMENT"]
        P[Product]
        PV[ProductVariation]
        V[Variation]
        C[Category]
        BR[Brands]
        UN[Unit]
        TR[TaxRate]
        W[Warranty]
        PGI[ProductGalleryImage]
        PRK[ProductRack]
        VGP[VariationGroupPrice]
    end
    
    %% RIGHT: Sales Operations (SO/SI)
    subgraph RIGHT["💰 SALES OPERATIONS"]
        T[Transaction]
        TSL[TransactionSellLine]
        SPG[SellingPriceGroup]
        D[Discount]
        CD[CustomDiscount]
        SA[StockAdjustmentLine]
        CPR[CustomerPriceRecall]
    end
    
    %% LEFT: Purchase Operations (PO/PR)
    subgraph LEFT["🛒 PURCHASE OPERATIONS"]
        PL[PurchaseLine]
        PR[PurchaseRequisition]
        PO[PurchaseOrder]
        EC[ExpenseCategory]
    end
    
    %% BOTTOM: Customer Management
    subgraph BOTTOM["👤 CUSTOMER MANAGEMENT"]
        CT[Contact]
        CG[CustomerGroup]
        CR[Cart]
        CI[CartItem]
        CU[ContactUs]
        CUM[ContactUsMeta]
        DN[DocumentAndNote]
        WL[Wishlist]
    end
    
    %% Supporting Systems
    subgraph SUPPORT["🔧 SUPPORTING SYSTEMS"]
        TP[TransactionPayment]
        AC[Account]
        AT[AccountTransaction]
        ACT[AccountType]
        PA[PaymentAccount]
        PB[PaymentBuffer]
        NT[NotificationTemplate]
        SYS[System]
        IL[InvoiceLayout]
        IS[InvoiceScheme]
        PRN[Printer]
        CRG[CashRegister]
        CRT[CashRegisterTransaction]
    end
    
    %% E-commerce Integration
    subgraph ECOM["🌐 E-COMMERCE"]
        WC[WooCommerce]
        SS[ShipStation]
        MA[MerchantApplication]
    end
    
    %% Notifications & Jobs
    subgraph JOBS["⚡ NOTIFICATIONS & JOBS"]
        SNJ[SendNotificationJob]
        WCP[WooCommerceWebhookPipeline]
        WCS[WooCommerceWebhookSaleOrder]
        SP[SyncProduct]
        SPM[SyncProductMeta]
        SC[SyncCustomer]
    end
    
    %% Connections - TOP to CENTER
    U --> P
    B --> P
    BL --> P
    U --> UCA
    UCA --> CT
    
    %% Connections - CENTER to SIDES
    P --> TSL
    P --> PL
    P --> CT
    P --> PGI
    P --> PRK
    P --> WL
    V --> VGP
    V --> CPR
    
    %% Connections - RIGHT (Sales)
    T --> TSL
    T --> SPG
    T --> D
    T --> CD
    T --> SA
    TSL --> TP
    CT --> CPR
    
    %% Connections - LEFT (Purchase)
    PL --> T
    PR --> PO
    PO --> PL
    EC --> T
    
    %% Connections - BOTTOM (Customer)
    CT --> T
    CT --> TP
    CT --> DN
    CR --> CI
    CI --> P
    CU --> CUM
    CT --> WL
    
    %% Supporting Connections
    TP --> AT
    TP --> PA
    TP --> PB
    T --> NT
    T --> SS
    T --> WC
    AC --> ACT
    CRG --> CRT
    
    %% Business Connections
    B --> BL
    B --> AC
    B --> SYS
    B --> IL
    B --> IS
    BL --> CRG
    BL --> PRN
    
    %% Product Supporting
    C --> P
    BR --> P
    UN --> P
    TR --> P
    W --> P
    PV --> P
    V --> P
    
    %% Job Connections
    T --> SNJ
    WC --> WCP
    WC --> WCS
    P --> SP
    P --> SPM
    CT --> SC
    
    %% Styling
    classDef userStyle fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef productStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:3px
    classDef salesStyle fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
    classDef purchaseStyle fill:#fff3e0,stroke:#e65100,stroke-width:2px
    classDef customerStyle fill:#fce4ec,stroke:#880e4f,stroke-width:2px
    classDef supportStyle fill:#f1f8e9,stroke:#33691e,stroke-width:2px
    classDef ecomStyle fill:#e0f2f1,stroke:#004d40,stroke-width:2px
    classDef jobStyle fill:#fff8e1,stroke:#f57c00,stroke-width:2px
    
    class U,B,BL,SA,UCA userStyle
    class P,PV,V,C,BR,UN,TR,W,PGI,PRK,VGP productStyle
    class T,TSL,SPG,D,CD,SA,CPR salesStyle
    class PL,PR,PO,EC purchaseStyle
    class CT,CG,CR,CI,CU,CUM,DN,WL customerStyle
    class TP,AC,AT,ACT,PA,PB,NT,SYS,IL,IS,PRN,CRG,CRT supportStyle
    class WC,SS,MA ecomStyle
    class SNJ,WCP,WCS,SP,SPM,SC jobStyle
                </div>
            </div>
        </div>

        <!-- Capabilities Section -->
        <div id="capabilities" class="content-section">
            <h2>⚡ System Capabilities</h2>
            <p>Comprehensive overview of all system features and capabilities.</p>
            
            <div class="workflow-grid">
                <div class="workflow-card">
                    <h3>🏢 Business Management</h3>
                    <ul>
                        <li>Multi-location Support</li>
                        <li>Role-based Access Control</li>
                        <li>Business Configuration</li>
                        <li>Custom Branding</li>
                        <li>Tax Settings</li>
                        <li>Currency Management</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>📦 Inventory Management</h3>
                    <ul>
                        <li>Product Catalog</li>
                        <li>Real-time Stock Tracking</li>
                        <li>Barcode Management</li>
                        <li>Category & Brand Management</li>
                        <li>Stock Adjustments</li>
                        <li>Rack Management</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>💰 Sales Operations</h3>
                    <ul>
                        <li>Point of Sale (POS)</li>
                        <li>Sales Orders</li>
                        <li>Sales Returns</li>
                        <li>Customer Management</li>
                        <li>Discount Management</li>
                        <li>Commission Tracking</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>🛒 Purchase Operations</h3>
                    <ul>
                        <li>Purchase Orders</li>
                        <li>Purchase Returns</li>
                        <li>Supplier Management</li>
                        <li>Purchase Requisitions</li>
                        <li>Stock Transfers</li>
                        <li>Goods Receipt</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>💼 Financial Management</h3>
                    <ul>
                        <li>Chart of Accounts</li>
                        <li>Transaction Recording</li>
                        <li>Payment Processing</li>
                        <li>Cash Register</li>
                        <li>Expense Tracking</li>
                        <li>Financial Reports</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>👥 CRM System</h3>
                    <ul>
                        <li>Contact Management</li>
                        <li>Communication Tools</li>
                        <li>Document Management</li>
                        <li>Price Recall</li>
                        <li>Wishlist Management</li>
                        <li>Customer Groups</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>🌐 E-commerce</h3>
                    <ul>
                        <li>WooCommerce Sync</li>
                        <li>Online Shopping Cart</li>
                        <li>ShipStation Integration</li>
                        <li>Payment Gateways</li>
                        <li>Order Management</li>
                        <li>Product Sync</li>
                    </ul>
                </div>



                <div class="workflow-card">
                    <h3>📊 Reporting & Analytics</h3>
                    <ul>
                        <li>Sales Reports</li>
                        <li>Inventory Reports</li>
                        <li>Financial Reports</li>
                        <li>Customer Reports</li>
                        <li>Tax Reports</li>
                        <li>Business Intelligence</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Workflows Section -->
        <div id="workflows" class="content-section">
            <h2>🔄 Key Business Workflows</h2>
            <p>Detailed workflow processes for different business operations.</p>
            
            <div class="workflow-grid">
                <div class="workflow-card">
                    <h3>🛍️ Sales Workflow</h3>
                    <ol>
                        <li><strong>Customer Selection</strong> → Contact lookup or creation</li>
                        <li><strong>Product Selection</strong> → Product catalog with search/filter</li>
                        <li><strong>Pricing Application</strong> → Discounts, taxes, price groups</li>
                        <li><strong>Payment Processing</strong> → Multiple payment methods</li>
                        <li><strong>Inventory Update</strong> → Automatic stock reduction</li>
                        <li><strong>Document Generation</strong> → Invoice/receipt printing</li>
                        <li><strong>Notification</strong> → Customer and staff notifications</li>
                    </ol>
                </div>

                <div class="workflow-card">
                    <h3>📦 Purchase Workflow</h3>
                    <ol>
                        <li><strong>Supplier Selection</strong> → Contact management</li>
                        <li><strong>Product Selection</strong> → Product catalog</li>
                        <li><strong>Order Creation</strong> → Purchase order generation</li>
                        <li><strong>Receipt Processing</strong> → Goods receipt</li>
                        <li><strong>Payment Processing</strong> → Supplier payments</li>
                        <li><strong>Inventory Update</strong> → Stock addition</li>
                        <li><strong>Document Management</strong> → Purchase documentation</li>
                    </ol>
                </div>

                <div class="workflow-card">
                    <h3>📋 Inventory Workflow</h3>
                    <ol>
                        <li><strong>Stock Monitoring</strong> → Real-time stock levels</li>
                        <li><strong>Reorder Points</strong> → Automatic reorder alerts</li>
                        <li><strong>Stock Transfers</strong> → Inter-location transfers</li>
                        <li><strong>Stock Adjustments</strong> → Manual corrections</li>
                        <li><strong>Stock Takes</strong> → Physical inventory counts</li>
                        <li><strong>Reporting</strong> → Stock movement reports</li>
                    </ol>
                </div>

                <div class="workflow-card">
                    <h3>💰 Financial Workflow</h3>
                    <ol>
                        <li><strong>Transaction Recording</strong> → All financial events</li>
                        <li><strong>Account Posting</strong> → Double-entry bookkeeping</li>
                        <li><strong>Payment Processing</strong> → Cash and bank transactions</li>
                        <li><strong>Reconciliation</strong> → Bank reconciliation</li>
                        <li><strong>Reporting</strong> → Financial statement generation</li>
                        <li><strong>Tax Compliance</strong> → Tax reporting and filing</li>
                    </ol>
                </div>

                <div class="workflow-card">
                    <h3>📦 Order Fulfillment Workflow</h3>
                    <ol>
                        <li><strong>Order Received</strong> → Order entry and validation</li>
                        <li><strong>Picking</strong> → Item selection and verification</li>
                        <li><strong>Processing</strong> → Quality check and preparation</li>
                        <li><strong>Packing</strong> → Packaging and labeling</li>
                        <li><strong>Shipping</strong> → Carrier assignment and tracking</li>
                        <li><strong>Delivery</strong> → Customer receipt confirmation</li>
                    </ol>
                </div>

                <div class="workflow-card">
                    <h3>💳 Payment Processing Workflow</h3>
                    <ol>
                        <li><strong>Payment Initiation</strong> → Customer payment selection</li>
                        <li><strong>Gateway Processing</strong> → Payment gateway integration</li>
                        <li><strong>Buffer Management</strong> → Payment buffer handling</li>
                        <li><strong>Account Posting</strong> → Financial account updates</li>
                        <li><strong>Receipt Generation</strong> → Payment confirmation</li>
                        <li><strong>Reconciliation</strong> → Payment verification</li>
                    </ol>
                </div>

                <div class="workflow-card">
                    <h3>🔄 Sync & Integration Workflow</h3>
                    <ol>
                        <li><strong>Data Extraction</strong> → Source system data retrieval</li>
                        <li><strong>Transformation</strong> → Data format conversion</li>
                        <li><strong>Validation</strong> → Data integrity checks</li>
                        <li><strong>Loading</strong> → Target system data insertion</li>
                        <li><strong>Verification</strong> → Sync success confirmation</li>
                        <li><strong>Error Handling</strong> → Failed sync recovery</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Architecture Section -->
        <div id="architecture" class="content-section">
            <h2>🏗️ Technical Architecture</h2>
            <p>Technical stack and integration capabilities of the Smokevana ERP system.</p>
            
            <div class="workflow-grid">
                <div class="workflow-card">
                    <h3>🏗️ System Architecture</h3>
                    <ul>
                        <li><strong>Laravel Framework</strong> - PHP MVC architecture</li>
                        <li><strong>MySQL Database</strong> - Primary data storage</li>
                        <li><strong>Redis Cache</strong> - Session and cache management</li>
                        <li><strong>Queue System</strong> - Background job processing</li>
                        <li><strong>WebSocket</strong> - Real-time communication</li>
                        <li><strong>API Layer</strong> - RESTful API endpoints</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>🔐 Security Architecture</h3>
                    <ul>
                        <li><strong>JWT Authentication</strong> - Secure token-based auth</li>
                        <li><strong>Role-based Permissions</strong> - Granular access control</li>
                        <li><strong>API Security</strong> - OAuth2 and API tokens</li>
                        <li><strong>Data Encryption</strong> - Sensitive data protection</li>
                        <li><strong>Audit Logging</strong> - Activity tracking</li>
                        <li><strong>CSRF Protection</strong> - Cross-site request forgery prevention</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>📊 Data Architecture</h3>
                    <ul>
                        <li><strong>Relational Database</strong> - Structured data storage</li>
                        <li><strong>Data Migration</strong> - Schema version control</li>
                        <li><strong>Backup System</strong> - Automated data backup</li>
                        <li><strong>Data Validation</strong> - Input sanitization</li>
                        <li><strong>Soft Deletes</strong> - Data recovery capability</li>
                        <li><strong>Indexing</strong> - Query performance optimization</li>
                    </ul>
                </div>

                <div class="workflow-card">
                    <h3>🔄 Integration Architecture</h3>
                    <ul>
                        <li><strong>Webhook System</strong> - Real-time data sync</li>
                        <li><strong>API Gateway</strong> - External service integration</li>
                        <li><strong>Queue Management</strong> - Asynchronous processing</li>
                        <li><strong>Event Broadcasting</strong> - Real-time notifications</li>
                        <li><strong>Service Layer</strong> - Business logic abstraction</li>
                        <li><strong>Module System</strong> - Extensible architecture</li>
                    </ul>
                </div>
            </div>
            
            <div class="tech-stack">
                <div class="tech-item">
                    <h4>🛠️ Framework</h4>
                    <ul>
                        <li><strong>Laravel 9.x</strong> - PHP framework</li>
                        <li><strong>MySQL</strong> - Primary database</li>
                        <li><strong>Redis</strong> - Caching and sessions</li>
                        <li><strong>WebSockets</strong> - Real-time features</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>🔧 Key Packages</h4>
                    <ul>
                        <li><strong>Laravel Passport</strong> - API authentication</li>
                        <li><strong>Spatie Permissions</strong> - Role-based access control</li>
                        <li><strong>Laravel Modules</strong> - Modular architecture</li>
                        <li><strong>Laravel Backup</strong> - Automated backups</li>
                        <li><strong>Laravel Activity Log</strong> - Audit trail</li>
                        <li><strong>Laravel Excel</strong> - Import/export functionality</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>💳 Payment Gateways</h4>
                    <ul>
                        <li><strong>Stripe</strong> - Credit card processing</li>
                        <li><strong>PayPal</strong> - Digital payments</li>
                        <li><strong>Razorpay</strong> - Indian payment gateway</li>
                        <li><strong>Authorize.net</strong> - Payment processing</li>
                        <li><strong>PayStack</strong> - African payment gateway</li>
                        <li><strong>PesaPal</strong> - Kenyan payment gateway</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>🌐 E-commerce Integration</h4>
                    <ul>
                        <li><strong>WooCommerce</strong> - WordPress e-commerce</li>
                        <li><strong>ShipStation</strong> - Shipping management</li>
                        <li><strong>MyFatoorah</strong> - Middle Eastern payments</li>
                        <li><strong>Multiple APIs</strong> - External system integration</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>📱 Communication</h4>
                    <ul>
                        <li><strong>Email</strong> - SMTP integration</li>
                        <li><strong>SMS</strong> - Twilio integration</li>
                        <li><strong>Push Notifications</strong> - FCM integration</li>
                        <li><strong>WebSocket</strong> - Real-time notifications</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>📊 Reporting & Analytics</h4>
                    <ul>
                        <li><strong>Charts</strong> - Visual data representation</li>
                        <li><strong>Excel Export</strong> - Data export capabilities</li>
                        <li><strong>PDF Generation</strong> - Document creation</li>
                        <li><strong>Real-time Dashboards</strong> - Live data monitoring</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>🔐 Security & Authentication</h4>
                    <ul>
                        <li><strong>JWT Authentication</strong> - Token-based auth</li>
                        <li><strong>Role-based Access Control</strong> - Permission management</li>
                        <li><strong>API Token Management</strong> - External API access</li>
                        <li><strong>Staff Authentication</strong> - Multi-user support</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>⚡ Queue & Background Jobs</h4>
                    <ul>
                        <li><strong>Laravel Queue</strong> - Background job processing</li>
                        <li><strong>Email/SMS Queues</strong> - Notification processing</li>
                        <li><strong>Sync Jobs</strong> - Data synchronization</li>
                        <li><strong>Webhook Processing</strong> - External integrations</li>
                    </ul>
                </div>

                <div class="tech-item">
                    <h4>🔄 Integration & Sync</h4>
                    <ul>
                        <li><strong>WooCommerce Sync</strong> - E-commerce integration</li>
                        <li><strong>ShipStation API</strong> - Shipping management</li>
                        <li><strong>Payment Gateway APIs</strong> - Payment processing</li>
                        <li><strong>Webhook Handlers</strong> - Real-time data sync</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Entities Section -->
        <div id="entities" class="content-section">
            <h2>📋 Core Entities Overview</h2>
            <p>Detailed breakdown of the main entities and their relationships in the system.</p>
            
            <div class="workflow-grid">
                <div class="workflow-card clickable-card" onclick="showEntityDiagram('BusinessEntities')">
                    <h3>🏢 Business Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">Business</span> - Main business unit</li>
                        <li><span class="entity-highlight">BusinessLocation</span> - Physical locations</li>
                        <li><span class="entity-highlight">User</span> - System users and staff</li>
                        <li><span class="entity-highlight">Account</span> - Financial accounts</li>
                        <li><span class="entity-highlight">AccountType</span> - Account categories</li>
                        <li><span class="entity-highlight">AccountTransaction</span> - Financial transactions</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('ProductEntities')">
                    <h3>📦 Product Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">Product</span> - Main product catalog</li>
                        <li><span class="entity-highlight">ProductVariation</span> - Product variations</li>
                        <li><span class="entity-highlight">Variation</span> - Product variants</li>
                        <li><span class="entity-highlight">Category</span> - Product categories</li>
                        <li><span class="entity-highlight">Brands</span> - Product brands</li>
                        <li><span class="entity-highlight">Unit</span> - Measurement units</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('ContactEntities')">
                    <h3>👥 Contact Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">Contact</span> - Customers and suppliers</li>
                        <li><span class="entity-highlight">CustomerGroup</span> - Customer segmentation</li>
                        <li><span class="entity-highlight">UserContactAccess</span> - Access permissions</li>
                        <li><span class="entity-highlight">DocumentAndNote</span> - Contact documents</li>
                        <li><span class="entity-highlight">ContactUs</span> - Contact form submissions</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('TransactionEntities')">
                    <h3>💰 Transaction Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">Transaction</span> - Main transaction entity</li>
                        <li><span class="entity-highlight">TransactionSellLine</span> - Sales line items</li>
                        <li><span class="entity-highlight">PurchaseLine</span> - Purchase line items</li>
                        <li><span class="entity-highlight">TransactionPayment</span> - Payment records</li>
                        <li><span class="entity-highlight">StockAdjustmentLine</span> - Stock adjustments</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('PaymentEntities')">
                    <h3>💳 Payment Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">PaymentAccount</span> - Payment accounts</li>
                        <li><span class="entity-highlight">PaymentBuffer</span> - Payment buffering</li>
                        <li><span class="entity-highlight">CashRegister</span> - Cash management</li>
                        <li><span class="entity-highlight">CashRegisterTransaction</span> - Cash transactions</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('SalesPricingEntities')">
                    <h3>🏪 Sales & Pricing</h3>
                    <ul>
                        <li><span class="entity-highlight">SellingPriceGroup</span> - Price groups</li>
                        <li><span class="entity-highlight">Discount</span> - Discount rules</li>
                        <li><span class="entity-highlight">CustomDiscount</span> - Custom discounts</li>
                        <li><span class="entity-highlight">TaxRate</span> - Tax rates</li>
                        <li><span class="entity-highlight">CustomerPriceRecall</span> - Price recalls</li>
                    </ul>
                </div>



                <div class="workflow-card clickable-card" onclick="showEntityDiagram('EcommerceEntities')">
                    <h3>🌐 E-commerce Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">Cart</span> - Shopping cart</li>
                        <li><span class="entity-highlight">CartItem</span> - Cart items</li>
                        <li><span class="entity-highlight">Wishlist</span> - Customer wishlists</li>
                        <li><span class="entity-highlight">ShipStation</span> - Shipping integration</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('SystemEntities')">
                    <h3>📊 System Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">System</span> - System configuration</li>
                        <li><span class="entity-highlight">InvoiceLayout</span> - Invoice templates</li>
                        <li><span class="entity-highlight">InvoiceScheme</span> - Invoice schemes</li>
                        <li><span class="entity-highlight">Printer</span> - Print management</li>
                        <li><span class="entity-highlight">NotificationTemplate</span> - Notification templates</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('NotificationEntities')">
                    <h3>🔔 Notification Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">SendNotificationJob</span> - Queued notifications</li>
                        <li><span class="entity-highlight">WooCommerceWebhookPipeline</span> - WC webhook processing</li>
                        <li><span class="entity-highlight">WooCommerceWebhookSaleOrder</span> - Order sync jobs</li>
                        <li><span class="entity-highlight">SyncProduct</span> - Product synchronization</li>
                        <li><span class="entity-highlight">SyncCustomer</span> - Customer synchronization</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('PaymentEntities')">
                    <h3>💳 Payment & Financial</h3>
                    <ul>
                        <li><span class="entity-highlight">PaymentBuffer</span> - Payment processing buffer</li>
                        <li><span class="entity-highlight">CashRegisterTransaction</span> - Cash register operations</li>
                        <li><span class="entity-highlight">AccountType</span> - Account categorization</li>
                        <li><span class="entity-highlight">ExpenseCategory</span> - Expense classification</li>
                        <li><span class="entity-highlight">MerchantApplication</span> - Payment gateway setup</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('ProductExtendedEntities')">
                    <h3>📦 Extended Product Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">ProductGalleryImage</span> - Product image galleries</li>
                        <li><span class="entity-highlight">ProductRack</span> - Rack management</li>
                        <li><span class="entity-highlight">VariationGroupPrice</span> - Group pricing</li>
                        <li><span class="entity-highlight">CustomerPriceRecall</span> - Customer-specific pricing</li>
                        <li><span class="entity-highlight">Wishlist</span> - Customer wishlists</li>
                    </ul>
                </div>

                <div class="workflow-card clickable-card" onclick="showEntityDiagram('ContactExtendedEntities')">
                    <h3>👥 Extended Contact Entities</h3>
                    <ul>
                        <li><span class="entity-highlight">ContactUs</span> - Contact form submissions</li>
                        <li><span class="entity-highlight">ContactUsMeta</span> - Contact form metadata</li>
                        <li><span class="entity-highlight">UserContactAccess</span> - Contact access permissions</li>
                        <li><span class="entity-highlight">StaffAuth</span> - Staff authentication</li>
                        <li><span class="entity-highlight">DocumentAndNote</span> - Contact documents</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" onclick="zoomIn()" title="Zoom In">🔍+</button>
        <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out">🔍-</button>
        <button class="zoom-btn" onclick="resetZoom()" title="Reset Zoom">🔄</button>
    </div>
    
    <div class="zoom-info" id="zoomInfo">
        Zoom: 100% | Scroll to zoom | Drag to pan
    </div>

    <div class="fullscreen-overlay" id="fullscreenOverlay">
        <button class="fullscreen-close" onclick="closeFullscreen()" title="Close Fullscreen">✕</button>
        <div class="fullscreen-content" id="fullscreenContent">
            <div class="fullscreen-diagram" id="fullscreenDiagram">
                <div class="fullscreen-svg-wrapper" id="fullscreenSvgWrapper">
                    <!-- SVG goes here -->
                </div>
            </div>
            <div class="fullscreen-controls" id="fullscreenControls">
                <button class="fullscreen-zoom-btn" onclick="fullscreenZoomIn()" title="Zoom In">🔍+</button>
                <button class="fullscreen-zoom-btn" onclick="fullscreenZoomOut()" title="Zoom Out">🔍-</button>
                <button class="fullscreen-zoom-btn" onclick="fullscreenResetZoom()" title="Reset Zoom">🔄</button>
            </div>
            <div class="fullscreen-zoom-info" id="fullscreenZoomInfo">
                Zoom: 100% | Scroll to zoom | Drag to pan
            </div>
        </div>
    </div>

    <div class="entity-diagram-overlay" id="entityDiagramOverlay">
        <button class="entity-diagram-close" onclick="closeEntityDiagram()" title="Close Entity Diagram">✕</button>
        <div class="entity-diagram-content" id="entityDiagramContent">
            <div class="entity-diagram-title" id="entityDiagramTitle"></div>
            <div class="entity-diagram-container" id="entityDiagramContainer">
                <!-- Entity diagram will be rendered here -->
            </div>
        </div>
    </div>

    <script>
        // Initialize Mermaid
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true
            },
            er: {
                useMaxWidth: true,
                htmlLabels: true
            }
        });

        // Navigation functionality
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.classList.remove('active');
            });

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.nav-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionId).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
            
            // Prevent any default form submission
            event.preventDefault();
            return false;
        }

        // Enhanced zoom and pan functionality
        let currentZoom = 1;
        let isDragging = false;
        let startX, startY, translateX = 0, translateY = 0;
        const diagramContainer = document.querySelector('.diagram-container');
        const zoomInfo = document.getElementById('zoomInfo');

        // Mouse wheel zoom
        diagramContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? 0.9 : 1.1;
            const newZoom = Math.max(0.1, Math.min(3, currentZoom * delta));
            
            if (newZoom !== currentZoom) {
                currentZoom = newZoom;
                applyTransform();
                updateZoomInfo();
            }
        });

        // Mouse drag pan
        diagramContainer.addEventListener('mousedown', function(e) {
            if (e.target.closest('.diagram-controls')) return;
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            diagramContainer.classList.add('dragging');
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            applyTransform();
        });

        document.addEventListener('mouseup', function() {
            isDragging = false;
            diagramContainer.classList.remove('dragging');
        });

        function zoomIn() {
            currentZoom = Math.min(3, currentZoom + 0.1);
            applyTransform();
            updateZoomInfo();
        }

        function zoomOut() {
            currentZoom = Math.max(0.1, currentZoom - 0.1);
            applyTransform();
            updateZoomInfo();
        }

        function resetZoom() {
            currentZoom = 1;
            translateX = 0;
            translateY = 0;
            applyTransform();
            updateZoomInfo();
        }

        function applyTransform() {
            if (diagramContainer) {
                diagramContainer.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
                diagramContainer.style.transformOrigin = 'center';
            }
        }

        function updateZoomInfo() {
            if (zoomInfo) {
                zoomInfo.textContent = `Zoom: ${Math.round(currentZoom * 100)}% | Scroll to zoom | Drag to pan`;
            }
        }

        // Add smooth scrolling and prevent form submission
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Prevent form submission on all buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.type !== 'submit') {
                        e.preventDefault();
                    }
                });
            });
        });

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case '1':
                        e.preventDefault();
                        showSection('er-diagram');
                        break;
                    case '2':
                        e.preventDefault();
                        showSection('capabilities');
                        break;
                    case '3':
                        e.preventDefault();
                        showSection('entities');
                        break;
                    case '4':
                        e.preventDefault();
                        showSection('workflows');
                        break;
                    case '5':
                        e.preventDefault();
                        showSection('architecture');
                        break;
                }
            }
            
            // Escape key to close fullscreen
            if (e.key === 'Escape') {
                closeFullscreen();
            }
        });

        // Interactive diagram functions
        function toggleLegend() {
            const legend = document.getElementById('legend');
            if (legend.style.display === 'none') {
                legend.style.display = 'flex';
            } else {
                legend.style.display = 'none';
            }
        }

        function resetView() {
            const diagramContainer = document.querySelector('.diagram-container');
            if (diagramContainer) {
                diagramContainer.style.transform = 'scale(1)';
                diagramContainer.style.transformOrigin = 'center';
            }
            currentZoom = 1;
        }

        function exportDiagram() {
            const svg = document.querySelector('.mermaid svg');
            if (svg) {
                const svgData = new XMLSerializer().serializeToString(svg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                
                img.onload = function() {
                    canvas.width = img.width * 2;
                    canvas.height = img.height * 2;
                    ctx.scale(2, 2);
                    ctx.drawImage(img, 0, 0);
                    
                    const link = document.createElement('a');
                    link.download = 'smokevana-go-erp-diagram.png';
                    link.href = canvas.toDataURL('image/png', 1.0);
                    link.click();
                };
                
                img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
            }
        }

        function exportSVG() {
            const svg = document.querySelector('.mermaid svg');
            if (svg) {
                const svgData = new XMLSerializer().serializeToString(svg);
                const blob = new Blob([svgData], {type: 'image/svg+xml'});
                const url = URL.createObjectURL(blob);
                
                const link = document.createElement('a');
                                    link.download = 'zeper-go-erp-diagram.svg';
                link.href = url;
                link.click();
                
                URL.revokeObjectURL(url);
            }
        }

        function fullscreenMode() {
            const overlay = document.getElementById('fullscreenOverlay');
            const diagramDiv = document.getElementById('fullscreenDiagram');
            const originalDiagram = document.querySelector('.mermaid');
            
            if (originalDiagram) {
                // Get the original SVG content
                const svg = originalDiagram.querySelector('svg');
                if (svg) {
                    try {
                        // Clone the SVG
                        const clonedSvg = svg.cloneNode(true);
                        clonedSvg.setAttribute('width', '100vw');
                        clonedSvg.setAttribute('height', '100vh');
                        if (!clonedSvg.hasAttribute('viewBox')) {
                          let vb = {x:0, y:0, width:1000, height:1000};
                          try {
                            vb = svg.viewBox.baseVal || svg.getBBox();
                          } catch (e) {}
                          clonedSvg.setAttribute('viewBox', `0 0 ${vb.width} ${vb.height}`);
                        }
                        
                        // Clear and add the cloned SVG
                        const svgWrapper = document.getElementById('fullscreenSvgWrapper');
                        svgWrapper.innerHTML = '';
                        svgWrapper.appendChild(clonedSvg);
                        
                        // Show fullscreen
                        overlay.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                        
                        // Add click handlers to the cloned nodes
                        setTimeout(() => {
                            const nodes = diagramDiv.querySelectorAll('.node');
                            nodes.forEach(node => {
                                node.style.cursor = 'pointer';
                                node.addEventListener('click', function() {
                                    const nodeText = this.querySelector('text')?.textContent || 'Node';
                                    showNodeInfo(nodeText);
                                });
                            });
                        }, 100);

                        // Add fullscreen zoom and pan functionality
                        setupFullscreenZoomAndPan();
                    } catch (error) {
                        console.error('Fullscreen error:', error);
                        // Fallback: show a message
                        diagramDiv.innerHTML = '<div style="text-align: center; padding: 50px; color: #666;">Fullscreen mode is not available. Please try again.</div>';
                        overlay.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    }
                } else {
                    // Fallback if SVG is not found
                    diagramDiv.innerHTML = '<div style="text-align: center; padding: 50px; color: #666;">Diagram not ready. Please wait and try again.</div>';
                    overlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            } else {
                // Fallback if diagram is not found
                diagramDiv.innerHTML = '<div style="text-align: center; padding: 50px; color: #666;">Diagram not found. Please refresh the page.</div>';
                overlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        // Fullscreen zoom and pan variables
        let fullscreenZoom = 1;
        let fullscreenTranslateX = 0;
        let fullscreenTranslateY = 0;
        let isFullscreenDragging = false;
        let fullscreenStartX, fullscreenStartY;

        function closeFullscreen() {
            const overlay = document.getElementById('fullscreenOverlay');
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
            
            // Clear the fullscreen content
            const diagramDiv = document.getElementById('fullscreenDiagram');
            if (diagramDiv) {
                diagramDiv.innerHTML = '';
            }
            
            // Reset fullscreen zoom and pan
            fullscreenZoom = 1;
            fullscreenTranslateX = 0;
            fullscreenTranslateY = 0;
        }

        function fullscreenZoomIn() {
            fullscreenZoom = Math.min(3, fullscreenZoom + 0.1);
            applyFullscreenTransform();
            updateFullscreenZoomInfo();
        }

        function fullscreenZoomOut() {
            fullscreenZoom = Math.max(0.1, fullscreenZoom - 0.1);
            applyFullscreenTransform();
            updateFullscreenZoomInfo();
        }

        function fullscreenResetZoom() {
            fullscreenZoom = 1;
            fullscreenTranslateX = 0;
            fullscreenTranslateY = 0;
            applyFullscreenTransform();
            updateFullscreenZoomInfo();
        }

        function applyFullscreenTransform() {
            const wrapper = document.getElementById('fullscreenSvgWrapper');
            if (wrapper) {
                wrapper.style.transform = `translate(${fullscreenTranslateX}px, ${fullscreenTranslateY}px) scale(${fullscreenZoom})`;
                wrapper.style.transformOrigin = '0 0';
            }
        }

        function updateFullscreenZoomInfo() {
            const zoomInfo = document.getElementById('fullscreenZoomInfo');
            if (zoomInfo) {
                zoomInfo.textContent = `Zoom: ${Math.round(fullscreenZoom * 100)}% | Scroll to zoom | Drag to pan`;
            }
        }

        function setupFullscreenZoomAndPan() {
            const diagramDiv = document.getElementById('fullscreenDiagram');
            
            // Mouse wheel zoom
            diagramDiv.addEventListener('wheel', function(e) {
                e.preventDefault();
                const delta = e.deltaY > 0 ? 0.9 : 1.1;
                const newZoom = Math.max(0.1, Math.min(3, fullscreenZoom * delta));
                
                if (newZoom !== fullscreenZoom) {
                    fullscreenZoom = newZoom;
                    applyFullscreenTransform();
                    updateFullscreenZoomInfo();
                }
            });

            // Mouse drag pan
            diagramDiv.addEventListener('mousedown', function(e) {
                if (e.target.closest('.fullscreen-controls') || e.target.closest('.fullscreen-close')) return;
                isFullscreenDragging = true;
                fullscreenStartX = e.clientX - fullscreenTranslateX;
                fullscreenStartY = e.clientY - fullscreenTranslateY;
                diagramDiv.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (!isFullscreenDragging) return;
                fullscreenTranslateX = e.clientX - fullscreenStartX;
                fullscreenTranslateY = e.clientY - fullscreenStartY;
                applyFullscreenTransform();
            });

            document.addEventListener('mouseup', function() {
                isFullscreenDragging = false;
                if (diagramDiv) {
                    diagramDiv.style.cursor = 'grab';
                }
            });
        }

        // Add enhanced click interactions to diagram nodes
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const nodes = document.querySelectorAll('.mermaid .node');
                nodes.forEach(node => {
                    node.style.cursor = 'pointer';
                    
                    node.addEventListener('click', function() {
                        const nodeText = this.querySelector('text')?.textContent || 'Node';
                        showNodeInfo(nodeText);
                    });
                    

                });
                

            }, 1000);
        });

        function showNodeInfo(nodeName) {
            const info = getNodeInfo(nodeName);
            if (info) {
                alert(`📋 ${nodeName}\n\n${info.description}\n\nKey Features:\n${info.features.join('\n')}`);
            }
        }

        function getNodeInfo(nodeName) {
            const nodeInfo = {
                'Product': {
                    description: 'Central entity managing all product information and inventory',
                    features: ['• Product variations and attributes', '• Stock management', '• Pricing and discounts', '• Category and brand classification']
                },
                'Transaction': {
                    description: 'Core transaction entity handling sales and purchase operations',
                    features: ['• Sales and purchase processing', '• Payment integration', '• Document generation', '• Status tracking']
                },
                'Contact': {
                    description: 'Customer and supplier management system',
                    features: ['• Customer profiles', '• Supplier information', '• Communication history', '• Group management']
                },
                'User': {
                    description: 'System user management and access control',
                    features: ['• Role-based permissions', '• Business access', '• Activity tracking', '• Authentication']
                }
            };
            return nodeInfo[nodeName];
        }

        // Entity diagram functions
        function showEntityDiagram(entityName) {
            const overlay = document.getElementById('entityDiagramOverlay');
            const title = document.getElementById('entityDiagramTitle');
            const container = document.getElementById('entityDiagramContainer');
            
            // Set the title
            title.textContent = `${entityName} - Entity Relationship Diagram`;
            
            // Generate the entity-specific diagram
            const diagramCode = generateEntityDiagram(entityName);
            
            // Create the diagram container
            container.innerHTML = `
                <div class="diagram-container">
                    <div class="diagram-controls">
                        <button class="diagram-btn" onclick="resetEntityView()" title="Reset View">🔄</button>
                        <button class="diagram-btn" onclick="exportEntityDiagram('${entityName}')" title="Export as PNG">💾</button>
                        <button class="diagram-btn" onclick="exportEntitySVG('${entityName}')" title="Export as SVG">📄</button>
                    </div>
                    <div class="mermaid" id="entity-mermaid-${entityName}">
                        ${diagramCode}
                    </div>
                </div>
            `;
            
            // Show the overlay
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Render the diagram
            setTimeout(() => {
                mermaid.render(`entity-diagram-${entityName}`, diagramCode).then(({svg}) => {
                    const mermaidDiv = document.getElementById(`entity-mermaid-${entityName}`);
                    mermaidDiv.innerHTML = svg;
                });
            }, 100);
        }

        function closeEntityDiagram() {
            const overlay = document.getElementById('entityDiagramOverlay');
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function generateEntityDiagram(entityName) {
            const diagrams = {
                'BusinessEntities': `
graph TD
    B[Business]
    BL[BusinessLocation]
    U[User]
    AC[Account]
    ACT[AccountType]
    AT[AccountTransaction]
    SYS[System]
    IL[InvoiceLayout]
    IS[InvoiceScheme]
    
    B --> BL
    B --> U
    B --> AC
    AC --> ACT
    AC --> AT
    B --> SYS
    B --> IL
    B --> IS
    
    classDef businessStyle fill:#e1f5fe,stroke:#01579b,stroke-width:3px
    class B,BL,U,AC,ACT,AT,SYS,IL,IS businessStyle
                `,
                'ProductEntities': `
graph TD
    P[Product]
    PV[ProductVariation]
    V[Variation]
    C[Category]
    BR[Brands]
    UN[Unit]
    TR[TaxRate]
    W[Warranty]
    
    P --> PV
    P --> C
    P --> BR
    P --> UN
    P --> TR
    P --> W
    PV --> V
    
    classDef productStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:3px
    class P,PV,V,C,BR,UN,TR,W productStyle
                `,
                'ContactEntities': `
graph TD
    CT[Contact]
    CG[CustomerGroup]
    UCA[UserContactAccess]
    DN[DocumentAndNote]
    CU[ContactUs]
    
    CT --> CG
    CT --> UCA
    CT --> DN
    CT --> CU
    
    classDef contactStyle fill:#fce4ec,stroke:#880e4f,stroke-width:3px
    class CT,CG,UCA,DN,CU contactStyle
                `,
                'TransactionEntities': `
graph TD
    T[Transaction]
    TSL[TransactionSellLine]
    PL[PurchaseLine]
    TP[TransactionPayment]
    SA[StockAdjustmentLine]
    SPG[SellingPriceGroup]
    D[Discount]
    CD[CustomDiscount]
    
    T --> TSL
    T --> PL
    T --> TP
    T --> SA
    T --> SPG
    T --> D
    T --> CD
    
    classDef transactionStyle fill:#e8f5e8,stroke:#1b5e20,stroke-width:3px
    class T,TSL,PL,TP,SA,SPG,D,CD transactionStyle
                `,
                'PaymentEntities': `
graph TD
    PA[PaymentAccount]
    PB[PaymentBuffer]
    TP[TransactionPayment]
    CR[CashRegister]
    CRT[CashRegisterTransaction]
    
    PA --> PB
    PA --> TP
    PA --> CR
    CR --> CRT
    
    classDef paymentStyle fill:#e0f2f1,stroke:#004d40,stroke-width:3px
    class PA,PB,TP,CR,CRT paymentStyle
                `,
                'SalesPricingEntities': `
graph TD
    SPG[SellingPriceGroup]
    D[Discount]
    CD[CustomDiscount]
    TR[TaxRate]
    CPR[CustomerPriceRecall]
    
    SPG --> D
    SPG --> CD
    SPG --> TR
    SPG --> CPR
    
    classDef salesStyle fill:#fff3e0,stroke:#e65100,stroke-width:3px
    class SPG,D,CD,TR,CPR salesStyle
                `,
                'EcommerceEntities': `
graph TD
    CR[Cart]
    CI[CartItem]
    P[Product]
    CT[Contact]
    W[Wishlist]
    SS[ShipStation]
    
    CR --> CI
    CI --> P
    CR --> CT
    CT --> W
    CR --> SS
    
    classDef ecomStyle fill:#f1f8e9,stroke:#33691e,stroke-width:3px
    class CR,CI,P,CT,W,SS ecomStyle
                `,
                'SystemEntities': `
graph TD
    SYS[System]
    NT[NotificationTemplate]
    PRN[Printer]
    IL[InvoiceLayout]
    IS[InvoiceScheme]
    
    SYS --> NT
    SYS --> PRN
    SYS --> IL
    SYS --> IS
    
    classDef systemStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:3px
    class SYS,NT,PRN,IL,IS systemStyle
                `,
                'NotificationEntities': `
graph TD
    SNJ[SendNotificationJob]
    WCP[WooCommerceWebhookPipeline]
    WCS[WooCommerceWebhookSaleOrder]
    SP[SyncProduct]
    SPM[SyncProductMeta]
    SC[SyncCustomer]
    
    SNJ --> NT
    WCP --> WC
    WCS --> WC
    SP --> P
    SPM --> P
    SC --> CT
    
    classDef notificationStyle fill:#fff8e1,stroke:#f57c00,stroke-width:3px
    class SNJ,WCP,WCS,SP,SPM,SC notificationStyle
                `,
                'PaymentEntities': `
graph TD
    PB[PaymentBuffer]
    CRT[CashRegisterTransaction]
    ACT[AccountType]
    EC[ExpenseCategory]
    MA[MerchantApplication]
    TP[TransactionPayment]
    CRG[CashRegister]
    
    PB --> TP
    CRG --> CRT
    AC --> ACT
    EC --> T
    MA --> PA
    
    classDef paymentStyle fill:#e0f2f1,stroke:#004d40,stroke-width:3px
    class PB,CRT,ACT,EC,MA,TP,CRG paymentStyle
                `,
                'ProductExtendedEntities': `
graph TD
    PGI[ProductGalleryImage]
    PRK[ProductRack]
    VGP[VariationGroupPrice]
    CPR[CustomerPriceRecall]
    WL[Wishlist]
    P[Product]
    V[Variation]
    CT[Contact]
    
    P --> PGI
    P --> PRK
    V --> VGP
    CT --> CPR
    CT --> WL
    WL --> P
    
    classDef productExtendedStyle fill:#f3e5f5,stroke:#4a148c,stroke-width:3px
    class PGI,PRK,VGP,CPR,WL,P,V,CT productExtendedStyle
                `,
                'ContactExtendedEntities': `
graph TD
    CU[ContactUs]
    CUM[ContactUsMeta]
    UCA[UserContactAccess]
    SA[StaffAuth]
    DN[DocumentAndNote]
    U[User]
    CT[Contact]
    
    CU --> CUM
    U --> UCA
    UCA --> CT
    SA --> U
    CT --> DN
    
    classDef contactExtendedStyle fill:#fce4ec,stroke:#880e4f,stroke-width:3px
    class CU,CUM,UCA,SA,DN,U,CT contactExtendedStyle
                `
            };
            
            return diagrams[entityName] || `
graph TD
    E[${entityName}]
    
    classDef defaultStyle fill:#f0f0f0,stroke:#333,stroke-width:2px
    class E defaultStyle
            `;
        }

        function resetEntityView() {
            const container = document.querySelector('.entity-diagram-container .diagram-container');
            if (container) {
                container.style.transform = 'scale(1)';
                container.style.transformOrigin = 'center';
            }
        }

        function exportEntityDiagram(entityName) {
            const svg = document.querySelector(`#entity-mermaid-${entityName} svg`);
            if (svg) {
                const svgData = new XMLSerializer().serializeToString(svg);
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                
                img.onload = function() {
                    canvas.width = img.width * 2;
                    canvas.height = img.height * 2;
                    ctx.scale(2, 2);
                    ctx.drawImage(img, 0, 0);
                    
                    const link = document.createElement('a');
                    link.download = `${entityName.toLowerCase()}-entity-diagram.png`;
                    link.href = canvas.toDataURL('image/png', 1.0);
                    link.click();
                };
                
                img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
            }
        }

        function exportEntitySVG(entityName) {
            const svg = document.querySelector(`#entity-mermaid-${entityName} svg`);
            if (svg) {
                const svgData = new XMLSerializer().serializeToString(svg);
                const blob = new Blob([svgData], {type: 'image/svg+xml'});
                const url = URL.createObjectURL(blob);
                
                const link = document.createElement('a');
                link.download = `${entityName.toLowerCase()}-entity-diagram.svg`;
                link.href = url;
                link.click();
                
                URL.revokeObjectURL(url);
            }
        }

        // Add keyboard support for entity diagrams
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEntityDiagram();
            }
        });
    </script>
</body>
</html> 
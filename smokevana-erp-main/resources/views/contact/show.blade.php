@extends('layouts.app')
@if ($contact->type == 'customer' || $contact->type == 'both')
    @section('title', __('contact.view_contact'))
@endif

@if ($contact->type == 'supplier' || $contact->type == 'both')
    @section('title', 'Vendor view')
@endif

@section('content')

    <!-- Customer View Styles -->
    <style>
        /* Amazon Theme Export Buttons - Interactive */
        .amazon-export-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 10px 18px !important;
            background: linear-gradient(180deg, #ffffff 0%, #f7f8f8 100%) !important;
            border: 1px solid #d5d9d9 !important;
            border-radius: 8px !important;
            color: #0f1111 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            margin: 0 !important;
            height: 40px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
            text-decoration: none !important;
        }
        .amazon-export-btn:hover {
            background: linear-gradient(180deg, #fff8e7 0%, #fff4d9 100%) !important;
            border-color: #ff9900 !important;
            color: #c45500 !important;
            box-shadow: 0 3px 8px rgba(255, 153, 0, 0.25) !important;
            transform: translateY(-2px) !important;
            text-decoration: none !important;
        }
        .amazon-export-btn:active {
            background: linear-gradient(180deg, #fff4d9 0%, #ffe8b3 100%) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset !important;
            transform: translateY(0) !important;
        }
        .amazon-export-btn i {
            font-size: 14px !important;
            transition: color 0.2s ease !important;
        }
        .amazon-export-btn:hover i {
            color: #ff9900 !important;
        }
        /* CSV Button */
        .amazon-export-btn .fa-file-csv {
            color: #067d62 !important;
        }
        /* Excel Button */
        .amazon-export-btn .fa-file-excel {
            color: #1d6f42 !important;
        }
        /* Print Button */
        .amazon-export-btn .fa-print {
            color: #007185 !important;
        }
        /* Columns Button - Dark Theme */
        .amazon-export-btn.column-visibility {
            background: linear-gradient(180deg, #232f3e 0%, #1a252f 100%) !important;
            border-color: #232f3e !important;
            color: #ffffff !important;
        }
        .amazon-export-btn.column-visibility:hover {
            background: linear-gradient(180deg, #37475a 0%, #2d3d4f 100%) !important;
            border-color: #ff9900 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 8px rgba(35, 47, 62, 0.35) !important;
        }
        .amazon-export-btn.column-visibility i {
            color: #ff9900 !important;
        }
        
        /* =============================================
           DATATABLE TOOLBAR - Search LEFT, Buttons RIGHT
           ============================================= */
        
        /* Main Toolbar Container */
        .amazon-table-toolbar {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 16px !important;
            padding: 16px 0 !important;
            margin-bottom: 16px !important;
            background: transparent !important;
            width: 100% !important;
        }
        
        /* Search Section - LEFT Side */
        .amazon-table-toolbar .toolbar-search {
            order: 1 !important;
            flex: 0 0 auto !important;
        }
        
        .amazon-table-toolbar .toolbar-search .dataTables_filter {
            margin: 0 !important;
            text-align: left !important;
            float: none !important;
        }
        
        .amazon-table-toolbar .toolbar-search .dataTables_filter label {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin: 0 !important;
            font-size: 0 !important;
        }
        
        .amazon-table-toolbar .toolbar-search .dataTables_filter input {
            width: 300px !important;
            height: 40px !important;
            padding: 8px 14px 8px 40px !important;
            border: 1px solid #d5d9d9 !important;
            border-radius: 8px !important;
            font-size: 14px !important;
            color: #0f1111 !important;
            background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23565959' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E") no-repeat 12px center !important;
            background-size: 16px !important;
            transition: all 0.15s ease !important;
            margin: 0 !important;
        }
        
        .amazon-table-toolbar .toolbar-search .dataTables_filter input:hover {
            border-color: #ff9900 !important;
        }
        
        .amazon-table-toolbar .toolbar-search .dataTables_filter input:focus {
            border-color: #ff9900 !important;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15) !important;
            outline: none !important;
        }
        
        /* Buttons Section - RIGHT Side */
        .amazon-table-toolbar .toolbar-buttons {
            order: 2 !important;
            flex: 0 0 auto !important;
            margin-left: auto !important;
        }
        
        .amazon-table-toolbar .toolbar-buttons .dt-buttons {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
            margin: 0 !important;
            float: none !important;
        }
        
        /* DataTables Buttons Container - Default override */
        .dt-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 12px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .amazon-table-toolbar {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            
            .amazon-table-toolbar .toolbar-search,
            .amazon-table-toolbar .toolbar-buttons {
                width: 100% !important;
                margin-left: 0 !important;
            }
            
            .amazon-table-toolbar .toolbar-search .dataTables_filter input {
                width: 100% !important;
            }
        }
        
        /* Receive Payment Button Style */
        .receive-payment-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: linear-gradient(180deg, #ff9900 0%, #e47911 100%);
            border: 1px solid #c45500;
            border-radius: 6px;
            color: #0f1111;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .receive-payment-btn:hover {
            background: linear-gradient(180deg, #fa8900 0%, #c45500 100%);
            border-color: #a24700;
            color: #0f1111;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .receive-payment-btn i {
            font-size: 14px;
        }
        /* Customer View Header Styles */
        .customer-view-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 12px 0;
            flex-wrap: wrap;
        }
        .customer-view-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: nowrap;
        }
        .customer-view-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            margin: 0;
        }
        /* Compact Customer Dropdown */
        .customer-switch-dropdown {
            position: relative;
            min-width: 280px;
            max-width: 350px;
        }
        .customer-switch-dropdown .select2-container {
            width: 100% !important;
        }
        .customer-switch-dropdown .select2-container--default .select2-selection--single {
            height: 36px;
            border: 1px solid #d5d9d9;
            border-radius: 6px;
            background: #fff;
            padding: 4px 8px;
        }
        .customer-switch-dropdown .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #0f1111;
            font-size: 13px;
            padding-left: 4px;
            max-width: 280px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .customer-switch-dropdown .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }
        
        /* Modern Tab Styles - Amazon Theme with Equal Distribution */
        .customer-view-tabs {
            position: relative;
            background: #fff;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }
        .customer-view-tabs .nav-tabs {
            border-bottom: 2px solid #e5e7eb;
            display: flex !important;
            flex-wrap: nowrap !important;
            justify-content: stretch !important;
            gap: 0;
            padding: 0;
            margin: 0;
            background: linear-gradient(180deg, #f7f8f8 0%, #fff 100%);
            width: 100%;
        }
        .customer-view-tabs .nav-tabs::-webkit-scrollbar {
            height: 4px;
        }
        .customer-view-tabs .nav-tabs::-webkit-scrollbar-track {
            background: transparent;
        }
        .customer-view-tabs .nav-tabs::-webkit-scrollbar-thumb {
            background: #d5d9d9;
            border-radius: 4px;
        }
        .customer-view-tabs .nav-tabs::-webkit-scrollbar-thumb:hover {
            background: #a2a6a6;
        }
        .customer-view-tabs .nav-tabs > li {
            margin-bottom: -2px;
            flex: 1 1 0 !important;
            float: none !important;
            display: flex !important;
            text-align: center;
        }
        .customer-view-tabs .nav-tabs > li > a {
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px 8px;
            border: none;
            border-bottom: 3px solid transparent;
            color: #565959;
            font-size: 12px;
            font-weight: 500;
            background: transparent;
            border-radius: 0;
            transition: all 0.15s ease;
            white-space: nowrap;
            text-decoration: none;
            width: 100% !important;
            text-align: center;
        }
        .customer-view-tabs .nav-tabs > li > a:hover {
            background: rgba(255, 153, 0, 0.06);
            color: #ff9900;
            border-bottom-color: #ffcc80;
            text-decoration: none;
        }
        .customer-view-tabs .nav-tabs > li.active > a,
        .customer-view-tabs .nav-tabs > li.active > a:hover,
        .customer-view-tabs .nav-tabs > li.active > a:focus {
            background: rgba(255, 153, 0, 0.08);
            color: #c45500;
            border-bottom-color: #ff9900;
            font-weight: 600;
            text-decoration: none;
        }
        .customer-view-tabs .nav-tabs > li > a i {
            font-size: 13px;
        }
        
        /* Override nav-justified for equal distribution */
        .customer-view-tabs .nav-tabs.nav-justified {
            display: flex !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
        }
        .customer-view-tabs .nav-tabs.nav-justified > li {
            display: flex !important;
            float: none !important;
            flex: 1 1 0 !important;
            width: auto !important;
            table-layout: auto !important;
        }
        .customer-view-tabs .nav-tabs.nav-justified > li > a {
            text-align: center !important;
            margin-bottom: 0 !important;
            width: 100% !important;
            justify-content: center !important;
        }
        
        /* Responsive - smaller screens - enable horizontal scroll */
        @media (max-width: 1400px) {
            .customer-view-tabs .nav-tabs {
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #d5d9d9 transparent;
            }
            .customer-view-tabs .nav-tabs > li {
                flex: 0 0 auto !important;
            }
            .customer-view-tabs .nav-tabs > li > a {
                padding: 10px 14px;
                font-size: 11px;
                gap: 4px;
                width: auto !important;
            }
            .customer-view-tabs .nav-tabs > li > a i {
                font-size: 12px;
            }
        }

        /* Tab Icon Colors - Amazon Theme Standard */
        .customer-view-tabs .nav-tabs > li > a i {
            color: #ff9900;
        }
        
        /* Active tab - darker orange for emphasis */
        .customer-view-tabs .nav-tabs > li.active > a i {
            color: #c45500;
        }
        
        /* Hover state */
        .customer-view-tabs .nav-tabs > li > a:hover i {
            color: #c45500;
        }
        
        /* =============================================
           TAB CONTENT - FULL WIDTH LAYOUT
           ============================================= */
        .customer-view-tabs .tab-content {
            padding: 20px;
            background: #FAFAFA;
        }
        
        .customer-view-tabs .tab-pane {
            width: 100%;
        }
        
        /* Full width tables in tab content */
        .customer-view-tabs .tab-pane .table-responsive,
        .customer-view-tabs .tab-pane table,
        .customer-view-tabs .tab-pane .dataTables_wrapper {
            width: 100% !important;
        }
        
        /* Remove default box margins */
        .customer-view-tabs .tab-pane .box {
            margin-bottom: 0;
            box-shadow: none;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
        }
        
        /* Customer Details Cards - Full Width */
        .customer-view-tabs .tab-pane .row {
            margin: 0 -10px;
        }
        
        .customer-view-tabs .tab-pane .row > [class*="col-"] {
            padding: 0 10px;
        }
        
        /* DataTables full width override */
        .dataTables_wrapper {
            width: 100% !important;
        }
        
        .dataTables_wrapper .dataTables_scroll {
            width: 100% !important;
        }
        
        .dataTables_wrapper table.dataTable {
            width: 100% !important;
            margin: 0 !important;
        }
        
        /* =============================================
           HIDE EMPTY TABLE HEADER ROWS
           ============================================= */
        #sell_table thead tr:nth-child(2),
        #sales_order_table thead tr:nth-child(2),
        .dataTables_scrollHead thead tr:nth-child(2),
        table.dataTable thead tr:empty,
        table.dataTable thead tr:not(:first-child):not(:has(th:not(:empty))) {
            display: none !important;
            height: 0 !important;
            visibility: hidden !important;
        }
        
        /* Hide any empty th cells row */
        table.dataTable thead tr th:empty {
            padding: 0 !important;
            height: 0 !important;
        }
        
        /* Ensure single header row */
        #sell_table_wrapper .dataTables_scrollHead table thead tr:not(:first-child),
        #sales_order_table_wrapper .dataTables_scrollHead table thead tr:not(:first-child) {
            display: none !important;
        }
    </style>

    <!-- Main content -->
    <section class="content no-print">
        <div class="customer-view-header amazon-theme-banner">
            <div class="customer-view-header-left">
                @if ($contact->type == 'customer' || $contact->type == 'both')
                    <h3 class="customer-view-title">@lang('contact.view_contact')</h3>
                @endif

                @if ($contact->type == 'supplier' || $contact->type == 'both')
                    <h3 class="customer-view-title">Vendor View</h3>
                @endif

                <input type="text" id="contact_id" class="hide" value={{$contact->id}}>
                <div class="customer-switch-dropdown">
                    <select name="contact_id" id="contact_id_change" class="form-control select2">
                        <option value="">Select contact</option>
                        @foreach ($contact_dropdown as $c)
                            <option value="{{ $c['id'] }}" data-customer_type="{{ $c['type'] }}"
                                @if (isset($contact->id) && $contact->id == $c['id']) selected @endif>
                                {{ $c['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if ($contact->type == 'customer' || $contact->type == 'both')
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" id="newTransactionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #28a745; border-color: #28a745; color: white; font-weight: 500;">
                            New transaction
                            <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="newTransactionDropdown" style="min-width: 200px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                            <li><a class="dropdown-item" href="{{ action([\App\Http\Controllers\SellPosController::class, 'create'], ['cid' => $contact->id]) }}"><i class="fas fa-file-invoice" style="margin-right: 8px; width: 16px;"></i> Invoice</a></li>
                            <li>
                                <a class="dropdown-item pay_sale_due"
                                   href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$contact->id]) }}?type=sell"
                                   data-container=".pay_contact_due_modal">
                                    <i class="fas fa-money-bill-wave" style="margin-right: 8px; width: 16px;"></i>
                                    Payment
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-redo" style="margin-right: 8px; width: 16px;"></i> Recurring payment</a></li>
                            <li><a class="dropdown-item" href="{{ action([\App\Http\Controllers\SellPosController::class, 'create'], ['sale_type' => 'quotation', 'cid' => $contact->id]) }}"><i class="fas fa-file-alt" style="margin-right: 8px; width: 16px;"></i> Estimate</a></li>
                            <li><a class="dropdown-item" href="{{ action([\App\Http\Controllers\SellPosController::class, 'create'], ['sale_type' => 'sales_order', 'cid' => $contact->id]) }}"><i class="fas fa-shopping-cart" style="margin-right: 8px; width: 16px;"></i> Sales order</a></li>
                            {{-- <li><a class="dropdown-item" href=""><i class="fas fa-link" style="margin-right: 8px; width: 16px;"></i> Payment link</a></li> --}}
                            {{-- <li><a class="dropdown-item" href="#"><i class="fas fa-receipt" style="margin-right: 8px; width: 16px;"></i> Sales receipt</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-invoice-dollar" style="margin-right: 8px; width: 16px;"></i> Credit memo</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-clock" style="margin-right: 8px; width: 16px;"></i> Delayed charge</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-stopwatch" style="margin-right: 8px; width: 16px;"></i> Time activity</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-text" style="margin-right: 8px; width: 16px;"></i> Statement</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-contract" style="margin-right: 8px; width: 16px;"></i> Contract <span class="badge badge-danger" style="margin-left: 8px; font-size: 10px;">NEW</span></a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-comment-dots" style="margin-right: 8px; width: 16px;"></i> Request feedback</a></li> --}}
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <div class="hide print_table_part">
            <style type="text/css">
                .info_col {
                    width: 25%;
                    float: left;
                    padding-left: 10px;
                    padding-right: 10px;
                }
            </style>
            <div style="width: 100%;">
                <div class="info_col">
                    @include('contact.contact_basic_info')
                </div>
                <div class="info_col">
                    @include('contact.contact_more_info')
                </div>
                @if ($contact->type != 'customer')
                    <div class="info_col">
                        @include('contact.contact_tax_info')
                    </div>
                @endif
                <div class="info_col">
                    @include('contact.contact_payment_info')
                </div>
            </div>
        </div>
        <input type="hidden" id="sell_list_filter_customer_id" value="{{ $contact->id }}">
        <input type="hidden" id="purchase_list_filter_supplier_id" value="{{ $contact->id }}">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid" style='margin-top: -20px;'>
                    <div class="box-body" style="padding: 0; padding-top: 0px; margin: 0; ">
                        @include('contact.partials.contact_info_tab')
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom customer-view-tabs">
                    <style>
                        #purchase_table_wrapper .dataTables_scrollHead {
                            width: 100%;
                        }

                        #purchase_table_wrapper .dataTables_scrollHeadInner {
                            width: 100% !important;
                        }

                        #purchase_table_wrapper .dataTables_scrollHeadInner table {
                            width: 100% !important;
                        }
                    </style>
                    <ul class="nav nav-tabs nav-justified">
                        {{-- Ledger Tab - Book/Ledger Icon --}}
                        <li class="@if (!empty($view_type) && $view_type == 'ledger') active @endif">
                            <a href="#ledger_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-book tab-icon-ledger" aria-hidden="true"></i> @lang('lang_v1.ledger')
                            </a>
                        </li>
                        
                        @if (in_array($contact->type, ['both', 'supplier']))
                            {{-- Purchase Receipt Tab --}}
                            <li class="@if (!empty($view_type) && $view_type == 'purchase') active @endif">
                                <a href="#purchases_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-truck-loading tab-icon-purchase" aria-hidden="true"></i> @lang('purchase.purchases') Receipt
                                </a>
                            </li>
                            {{-- Purchase Order Tab --}}
                            <li class="@if (!empty($view_type) && $view_type == 'purchase_order') active @endif">
                                <a href="#purchase_order_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-clipboard-list tab-icon-order" aria-hidden="true"></i> @lang('purchase.purchase_order')
                                </a>
                            </li>
                            {{-- Purchase Return Tab --}}
                            <li class="@if (!empty($view_type) && $view_type == 'purchase_return') active @endif">
                                <a href="#purchase_return_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-undo-alt tab-icon-return" aria-hidden="true"></i> @lang('purchase.purchase_return')
                                </a>
                            </li>
                            {{-- Stock Report Tab --}}
                            <li class="@if (!empty($view_type) && $view_type == 'stock_report') active @endif">
                                <a href="#stock_report_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-boxes tab-icon-stock" aria-hidden="true"></i> @lang('report.stock_report')
                                </a>
                            </li>
                        @endif
                        
                        @if (in_array($contact->type, ['both', 'customer']))
                            {{-- Sales Invoice Tab - File Invoice Icon --}}
                            <li class="@if (!empty($view_type) && $view_type == 'sales') active @endif">
                                <a href="#sales_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-file-invoice-dollar tab-icon-invoice" aria-hidden="true"></i> Sales Invoice
                                </a>
                            </li>
                            
                            {{-- Subscriptions Tab - Only show ONCE --}}
                            @if (in_array('subscription', $enabled_modules))
                                <li class="@if (!empty($view_type) && $view_type == 'subscriptions') active @endif">
                                    <a href="#subscriptions_tab" data-toggle="tab" aria-expanded="true">
                                        <i class="fas fa-sync-alt tab-icon-subscription" aria-hidden="true"></i> @lang('lang_v1.subscriptions')
                                    </a>
                                </li>
                            @endif
                            
                            {{-- Sales Order Tab - Shopping Bag Icon --}}
                            <li class="@if (!empty($view_type) && $view_type == 'sales_order') active @endif">
                                <a href="#sales_order_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-shopping-bag tab-icon-order" aria-hidden="true"></i> @lang('lang_v1.sales_order')
                                </a>
                            </li>
                            
                            {{-- Sales Return Tab - Exchange Icon --}}
                            <li class="@if (!empty($view_type) && $view_type == 'sales_return') active @endif">
                                <a href="#sales_return_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-exchange-alt tab-icon-return" aria-hidden="true"></i> Sales Return
                                </a>
                            </li>
                        @endif
                        
                        {{-- Documents & Notes Tab --}}
                        <li class="@if (!empty($view_type) && $view_type == 'documents_and_notes') active @endif">
                            <a href="#documents_and_notes_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-folder-open tab-icon-docs" aria-hidden="true"></i> @lang('lang_v1.documents_and_notes')
                            </a>
                        </li>
                        
                        {{-- Payments Tab --}}
                        <li class="@if (!empty($view_type) && $view_type == 'payments') active @endif">
                            <a href="#payments_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-credit-card tab-icon-payments" aria-hidden="true"></i> @lang('sale.payments')
                            </a>
                        </li>

                        @if (in_array($contact->type, ['customer', 'both']) && session('business.enable_rp'))
                            {{-- Reward Points Tab --}}
                            <li class="@if (!empty($view_type) && $view_type == 'reward_point') active @endif">
                                <a href="#reward_point_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-award tab-icon-rewards" aria-hidden="true"></i>
                                    {{ session('business.rp_name') ?? __('lang_v1.reward_points') }}
                                </a>
                            </li>
                        @endif

                        {{-- Activities Tab --}}
                        <li class="@if (!empty($view_type) && $view_type == 'activities') active @endif">
                            <a href="#activities_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-history tab-icon-activities" aria-hidden="true"></i> @lang('lang_v1.activities')
                            </a>
                        </li>

                        @if (in_array($contact->type, ['customer', 'both']))
                            {{-- Addresses Tab --}}
                            <li class="@if (!empty($view_type) && $view_type == 'addresses') active @endif">
                                <a href="#addresses_tab" data-toggle="tab" aria-expanded="true">
                                    <i class="fas fa-map-marked-alt tab-icon-addresses" aria-hidden="true"></i> @lang('lang_v1.addresses')
                                </a>
                            </li>
                        @endif

                        @if (!empty($contact_view_tabs))
                            @foreach ($contact_view_tabs as $key => $tabs)
                                @foreach ($tabs as $index => $value)
                                    @if (!empty($value['tab_menu_path']))
                                        @php
                                            $tab_data = !empty($value['tab_data']) ? $value['tab_data'] : [];
                                        @endphp
                                        @include($value['tab_menu_path'], $tab_data)
                                    @endif
                                @endforeach
                            @endforeach
                        @endif

                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane
                                @if (!empty($view_type) && $view_type == 'ledger') active
                                @else
                                    '' @endif"
                            id="ledger_tab">
                            @include('contact.partials.ledger_tab')
                        </div>
                        @if (in_array($contact->type, ['both', 'supplier']))
                            <div class="tab-pane
                            @if (!empty($view_type) && $view_type == 'purchase') active
                            @else
                                '' @endif"
                                id="purchases_tab">

                                <div class="row">

                                    {{-- filters --}}
                                    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog"
                                        aria-labelledby="filterModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">@lang('report.filters')</h4>
                                                </div>
                                                <div class="modal-body" style="padding: 0px; margin-top: 10px;">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {!! Form::label('purchase_list_filter_date_range', __('report.date_range') . ':') !!}
                                                            {!! Form::text('purchase_list_filter_date_range', null, [
                                                                'placeholder' => __('lang_v1.select_a_date_range'),
                                                                'class' => 'form-control',
                                                                'readonly',
                                                            ]) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"
                                                        class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                                        data-dismiss="modal">@lang('messages.close')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- filters end --}}
                                    <div class="col-md-12">
                                        @include('purchase.partials.purchase_table')
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane
                            @if (!empty($view_type) && $view_type == 'purchase_order') {{-- active --}}
                            @else
                                '' @endif"
                                id="purchase_order_tab">
                                <div class="row">
                                    <div class="col-md-12" id="purchase-order">
                                        <div>
                                            <a href="/purchase-order/create?cid={{ $contact->id }}"
                                                id="acount-add-purchase-order"> <button type="button"
                                                    class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2">@lang('lang_v1.add_purchase_order')</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div style="overflow-x: auto; width: 100%;">
                                            @include('purchase.partials.purchase_order_table')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane
                            @if (!empty($view_type) && $view_type == 'purchase_return') {{-- active --}}
                            @else
                                '' @endif"
                                id="purchase_return_tab">
                                <div class="row">
                                    <div class="col-md-12" id="purchase-return">
                                        <div>
                                            <a href="/purchase-return/create?cid={{ $contact->id }}"
                                                id="acount-add-purchase-return"> <button type="button"
                                                    class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2">@lang('lang_v1.add_purchase_return')</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div style="overflow-x: auto; width: 100%;">
                                            @include('purchase.partials.purchase_return_table')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane 
                            @if (!empty($view_type) && $view_type == 'stock_report') active
                            @else
                                '' @endif"
                                id="stock_report_tab">
                                @include('contact.partials.stock_report_tab')
                            </div>
                        @endif
                        @if (in_array($contact->type, ['both', 'customer']))
                            <div class="tab-pane 
                            @if (!empty($view_type) && $view_type == 'sales') active
                            @else
                                '' @endif"
                                id="sales_tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="tw-flex tw-flex-row tw-justify-end">
                                            <div>
                                                <button type="button"
                                                    class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2"
                                                    data-toggle="modal" data-target="#filterModal">
                                                    Filters
                                                </button>
                                                <script>
                                                    $(document).ready(function() {
                                                        $('#filterModal').modal('show');
                                                    });
                                                </script>
                                            </div>
                                            <div>
                                                <a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$contact->id]) }}?type=sell"
                                                    id="add-sell-payment" class="pay_sale_due">
                                                    <button type="button"
                                                        class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2">
                                                        Add Payment
                                                    </button>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="/sells/create?cid={{ $contact->id }}" {{-- id="account-add-sell" --}}>
                                                    <button type="button"
                                                        class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2">Add
                                                        Sales Invoice</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        {{-- sell list filters --}}
                                        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog"
                                            aria-labelledby="filterModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document"> {{-- Use modal-lg or modal-xl as needed --}}
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title">@lang('report.filters')</h4>
                                                    </div>
                                                    <div class="modal-body" style="padding: 0px; margin-top: 10px;">
                                                        @php
                                                            $only = [
                                                                'sell_list_filter_payment_status',
                                                                'sell_list_filter_date_range',
                                                                'only_subscriptions',
                                                            ];
                                                        @endphp

                                                        @if (empty($only) || in_array('sell_list_filter_location_id', $only))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    {!! Form::label('sell_list_filter_location_id', __('purchase.business_location') . ':') !!}
                                                                    {!! Form::select('sell_list_filter_location_id', $business_locations, null, [
                                                                        'class' => 'form-control select2',
                                                                        'style' => 'width:100%',
                                                                        'placeholder' => __('lang_v1.all'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (empty($only) || in_array('sell_list_filter_customer_id', $only))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    {!! Form::label('sell_list_filter_customer_id', __('contact.customer') . ':') !!}
                                                                    {!! Form::select('sell_list_filter_customer_id', $customers, null, [
                                                                        'class' => 'form-control select2',
                                                                        'style' => 'width:100%',
                                                                        'placeholder' => __('lang_v1.all'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (empty($only) || in_array('sell_list_filter_payment_status', $only))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    {!! Form::label('Payment Status', __('purchase.payment_status') . ':') !!}
                                                                    {!! Form::select(
                                                                        'sell_list_filter_payment_status',
                                                                        [
                                                                            'paid' => __('lang_v1.paid'),
                                                                            'due' => __('lang_v1.due'),
                                                                            'partial' => __('lang_v1.partial'),
                                                                            'overdue' => __('lang_v1.overdue'),
                                                                        ],
                                                                        null,
                                                                        ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')],
                                                                    ) !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (empty($only) || in_array('sell_list_filter_date_range', $only))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                                                                    {!! Form::text('sell_list_filter_date_range', null, [
                                                                        'placeholder' => __('lang_v1.select_a_date_range'),
                                                                        'class' => 'form-control',
                                                                        'readonly',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ((empty($only) || in_array('created_by', $only)) && !empty($sales_representative))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    {!! Form::label('created_by', __('report.user') . ':') !!}
                                                                    {!! Form::select('created_by', $sales_representative, null, [
                                                                        'class' => 'form-control select2',
                                                                        'style' => 'width:100%',
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (empty($only) || in_array('sales_cmsn_agnt', $only))
                                                            @if (!empty($is_cmsn_agent_enabled))
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        {!! Form::label('sales_cmsn_agnt', __('lang_v1.sales_commission_agent') . ':') !!}
                                                                        {!! Form::select('sales_cmsn_agnt', $commission_agents, null, [
                                                                            'class' => 'form-control select2',
                                                                            'style' => 'width:100%',
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif

                                                        @if (empty($only) || in_array('service_staffs', $only))
                                                            @if (!empty($service_staffs))
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        {!! Form::label('service_staffs', __('restaurant.service_staff') . ':') !!}
                                                                        {!! Form::select('service_staffs', $service_staffs, null, [
                                                                            'class' => 'form-control select2',
                                                                            'style' => 'width:100%',
                                                                            'placeholder' => __('lang_v1.all'),
                                                                        ]) !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif

                                                        @if (!empty($shipping_statuses))
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    {!! Form::label('shipping_status', __('lang_v1.shipping_status') . ':') !!}
                                                                    {!! Form::select('shipping_status', $shipping_statuses, null, [
                                                                        'class' => 'form-control select2',
                                                                        'style' => 'width:100%',
                                                                        'placeholder' => __('lang_v1.all'),
                                                                    ]) !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (empty($only) || in_array('only_subscriptions', $only))
                                                            <div class="col-md-3" style="margin-bottom: 7px;">
                                                                <div class="form-group">
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <br>
                                                                            {!! Form::checkbox('only_subscriptions', 1, false, ['class' => 'input-icheck', 'id' => 'only_subscriptions']) !!}
                                                                            {{ __('lang_v1.subscriptions') }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button"
                                                            class="tw-dw-btn tw-dw-btn-neutral tw-text-white"
                                                            data-dismiss="modal">@lang('messages.close')</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- filter end  --}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('sale_pos.partials.sales_table')
                                    </div>
                                </div>
                            </div>
                            {{-- Subscriptions tab content - included once below --}}
                        @endif
                        @if (in_array($contact->type, ['both', 'customer']))
                            <div class="tab-pane @if (!empty($view_type) && $view_type == 'sales_order') active @endif"
                                id="sales_order_tab">
                                <div class="row">
                                    <div class="col-md-12" id="sale-order">
                                        {{-- @component('components.widget')
                                        @include('sell.partials.sell_list_filters', ['only' => ['sell_list_filter_payment_status', 'sell_list_filter_date_range', 'only_subscriptions']])
                                    @endcomponent --}}
                                        <div>
                                            <a href="/sells/create?sale_type=sales_order&cid={{ $contact->id }}"
                                                {{-- id="acount-add-sales-order" --}}> <button type="button"
                                                    class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm pull-right tw-m-2">@lang('lang_v1.add_sales_order')</button>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('sale_pos.partials.sales_order_contact')
                                    </div>
                                </div>
                            </div>
                            {{-- Subscriptions tab content - included once below --}}
                        @endif
                        @if (in_array($contact->type, ['both', 'customer']))
                            <div class="tab-pane @if (!empty($view_type) && $view_type == 'sales_return') active @endif" id="sales_return_tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('sell_return.partials.sell_return_list')
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Subscriptions Tab Content - Single Include --}}
                        @if (in_array($contact->type, ['both', 'customer']) && in_array('subscription', $enabled_modules))
                            @include('contact.partials.subscriptions')
                        @endif
                        
                        <div class="tab-pane @if (!empty($view_type) && $view_type == 'documents_and_notes') active @endif"
                            id="documents_and_notes_tab">
                            @include('contact.partials.documents_and_notes_tab')
                        </div>
                        <div class="tab-pane 
                        @if (!empty($view_type) && $view_type == 'payments') active
                        @else
                            '' @endif"
                            id="payments_tab">
                            <div id="contact_payments_div" style="height: 500px;overflow-y: scroll;"></div>
                        </div>
                        @if (in_array($contact->type, ['customer', 'both']) && session('business.enable_rp'))
                            <div class="tab-pane
                            @if (!empty($view_type) && $view_type == 'reward_point') active
                            @else
                                '' @endif"
                                id="reward_point_tab">
                                <br>
                                <div class="row">
                                    @if ($reward_enabled)
                                        <div class="col-md-3">
                                            <div class="info-box bg-yellow">
                                                <span class="info-box-icon"><i class="fa fa-gift"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">{{ session('business.rp_name') }}</span>
                                                    <span class="info-box-number">{{ $contact->total_rp ?? 0 }}</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="rp_log_table"
                                                width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('messages.date')</th>
                                                        <th>@lang('sale.invoice_no')</th>
                                                        <th>@lang('lang_v1.earned')</th>
                                                        <th>@lang('lang_v1.redeemed')</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane" id="activities_tab">
                            @include('activity_log.activities')
                        </div>

                        @if (in_array($contact->type, ['customer', 'both']))
                            <div class="tab-pane
                                @if (!empty($view_type) && $view_type == 'addresses') active
                                @else
                                    '' @endif"
                                id="addresses_tab">
                                @include('contact.partials.addresses_tab')
                            </div>
                        @endif

                        @if (!empty($contact_view_tabs))
                            @foreach ($contact_view_tabs as $key => $tabs)
                                @foreach ($tabs as $index => $value)
                                    @if (!empty($value['tab_content_path']))
                                        @php
                                           $tab_data = !empty($value['tab_data']) ? $value['tab_data'] : [];
                                        @endphp
                                        @include($value['tab_content_path'], $tab_data)
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade" id="edit_ledger_discount_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    @include('ledger_discount.create')

@stop
@section('javascript')
    <script type="text/javascript">
        var customer_addresses_table;
        
        $(document).ready(function() {
            // Scroll active tab into view on page load
            setTimeout(function() {
                var $activeTab = $('.customer-view-tabs .nav-tabs > li.active');
                if ($activeTab.length) {
                    var $tabsContainer = $activeTab.closest('.nav-tabs');
                    var tabOffset = $activeTab.position().left;
                    var containerWidth = $tabsContainer.width();
                    var tabWidth = $activeTab.outerWidth();
                    var scrollPosition = tabOffset - (containerWidth / 2) + (tabWidth / 2);
                    $tabsContainer.animate({ scrollLeft: scrollPosition }, 300);
                }
            }, 100);
            
            // Smooth scroll when clicking tabs
            $('.customer-view-tabs .nav-tabs > li > a').on('click', function() {
                var $tab = $(this).parent();
                var $tabsContainer = $tab.closest('.nav-tabs');
                var tabOffset = $tab.position().left + $tabsContainer.scrollLeft();
                var containerWidth = $tabsContainer.width();
                var tabWidth = $tab.outerWidth();
                var scrollPosition = tabOffset - (containerWidth / 2) + (tabWidth / 2);
                $tabsContainer.animate({ scrollLeft: scrollPosition }, 200);
            });
            
            // Initialize separate date pickers for ledger
            var ledgerStartDate = moment().subtract(29, 'days');
            var ledgerEndDate = moment();
            
            // Start Date Picker
            $('#ledger_start_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                startDate: ledgerStartDate,
                locale: {
                    format: moment_date_format
                }
            });
            
            // End Date Picker
            $('#ledger_end_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                startDate: ledgerEndDate,
                locale: {
                    format: moment_date_format
                }
            });
            
            // Function to sync hidden date range field
            function syncLedgerDateRange() {
                var start = $('#ledger_start_date').val();
                var end = $('#ledger_end_date').val();
                $('#ledger_date_range').val(start + ' ~ ' + end);
                get_contact_ledger();
            }
            
            // Trigger on date change
            $('#ledger_start_date, #ledger_end_date').on('apply.daterangepicker', function() {
                syncLedgerDateRange();
            });
            
            $('#ledger_location').change(function() {
                get_contact_ledger();
            });
            
            // Initial load after a short delay
            setTimeout(function() {
                get_contact_ledger();
            }, 200);
            
            // Initialize payment group table if it exists on page load
            if ($('#contact_payment_group_table').length > 0) {
                initializeContactPaymentGroupTable();
            }

            rp_log_table = $('#rp_log_table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                fixedHeader: false,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: '/sells?customer_id={{ $contact->id }}&rewards_only=true',
                columns: [{
                        data: 'transaction_date',
                        name: 'transactions.transaction_date'
                    },
                    {
                        data: 'invoice_no',
                        name: 'transactions.invoice_no'
                    },
                    {
                        data: 'rp_earned',
                        name: 'transactions.rp_earned'
                    },
                    {
                        data: 'rp_redeemed',
                        name: 'transactions.rp_redeemed'
                    },
                ]
            });

            supplier_stock_report_table = $('#supplier_stock_report_table').DataTable({
                processing: true,
                language: {
                    processing: `<div id="main_loader"><span class='loader'></span></div>`
                },
                serverSide: true,
                fixedHeader: false,
                'ajax': {
                    url: "{{ action([\App\Http\Controllers\ContactController::class, 'getSupplierStockReport'], [$contact->id]) }}",
                    data: function(d) {
                        d.location_id = $('#sr_location_id').val();
                    }
                },
                columns: [{
                        data: 'product_name',
                        name: 'p.name'
                    },
                    {
                        data: 'sub_sku',
                        name: 'v.sub_sku'
                    },
                    {
                        data: 'purchase_quantity',
                        name: 'purchase_quantity',
                        searchable: false
                    },
                    {
                        data: 'total_quantity_sold',
                        name: 'total_quantity_sold',
                        searchable: false
                    },
                    {
                        data: 'total_quantity_transfered',
                        name: 'total_quantity_transfered',
                        searchable: false
                    },
                    {
                        data: 'total_quantity_returned',
                        name: 'total_quantity_returned',
                        searchable: false
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock',
                        searchable: false
                    },
                    {
                        data: 'stock_price',
                        name: 'stock_price',
                        searchable: false
                    }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#supplier_stock_report_table'));
                },
            });

            $('#sr_location_id').change(function() {
                supplier_stock_report_table.ajax.reload();
            });

            $('#contact_id_change').change(function() {
                const selectedOption = $(this).find(':selected');
                const id = selectedOption.val();
                const type = selectedOption.data('customer_type');
                if (id) {
                    window.location = "{{ url('/contacts') }}/" + id + `?type=${type}`;
                }
            });

            @if (in_array($contact->type, ['customer', 'both']))
            // Initialize customer addresses table
            customer_addresses_table = $('#customer_addresses_table').DataTable({
                processing: true,
                language: {
                    processing: '<div id="main_loader"><span class="loader"></span></div>'
                },
                serverSide: true,
                ajax: {
                    url: "{{ action([\App\Http\Controllers\CustomerAddressController::class, 'index']) }}",
                    data: function(d) {
                        d.contact_id = {{ $contact->id }};
                    }
                },
                columns: [
                    { data: 'name', name: 'name', orderable: false },
                    { data: 'address_label', name: 'address_label' },
                    { data: 'address_type', name: 'address_type' },
                    { data: 'address_line_1', name: 'address_line_1' },
                    { data: 'city', name: 'city' },
                    { data: 'state', name: 'state' },
                    { data: 'zip_code', name: 'zip_code' },
                    { data: 'country', name: 'country' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
            @endif

            sell_return_table = $('#sell_return_table').DataTable({
            processing: true,
                    language: { processing: `<div id="main_loader"><span class='loader'></span></div>`},
            serverSide: true,
            fixedHeader:true,
            scrollY:'60vh',
            scrollX: true,

            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#sell_list_filter_customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7, 8],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
            ],
            buttons: [
                    {
                        text: '<i class="fa fa-filter"></i> Filters',
                        className: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                        action: function () {
                            $('#filterModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                        exportOptions: {
                            columns: ':visible',
                            stripHtml: true,
                        },
                        footer: true,
                        customize: function (win) {
                            if ($('.print_table_part').length > 0) {
                                $($('.print_table_part').html()).insertBefore(
                                    $(win.document.body).find('table')
                                );
                            }
                            if ($(win.document.body).find('table.hide-footer').length) {
                                $(win.document.body).find('table.hide-footer tfoot').remove();
                            }
                            __currency_convert_recursively($(win.document.body).find('table'));
                        },
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
                        className: 'tw-dw-btn-xs  tw-dw-btn tw-dw-btn-outline tw-my-2',
                    },
                ],

            "fnDrawCallback": function (oSettings) {
                var total_sell = sum_table_col($('#sell_return_table'), 'final_total');
                $('.footer_sell_return_total').text(total_sell);
                
                // $('.footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_table'), 'payment-status-label'));

                var total_due = sum_table_col($('#sell_return_table'), 'payment_due');
                $('.footer_total_due_sr').text(total_due);

                __currency_convert_recursively($('#sell_return_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by',  function() {
            sell_return_table.ajax.reload();
        });

            $('a[href="#sales_tab"]').on('shown.bs.tab', function(e) {
                sell_table.ajax.reload();
                sell_return_table.ajax.reload();
            });
            $('a[href="#sales_order_tab"]').on('shown.bs.tab', function(e) {
                sales_order_table.ajax.reload();
            });
            $('a[href="#sales_return_tab"]').on('shown.bs.tab', function(e) {
                sell_return_table.ajax.reload();
            });
            @if (in_array($contact->type, ['customer', 'both']))
            $('a[href="#addresses_tab"]').on('shown.bs.tab', function(e) {
                customer_addresses_table.ajax.reload();
            });
            @endif

            //Date picker
            $('#discount_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $(document).on('submit', 'form#add_discount_form, form#edit_discount_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            $('div#add_discount_modal').modal('hide');
                            $('div#edit_ledger_discount_modal').modal('hide');
                            toastr.success(result.msg);
                            form[0].reset();
                            form.find('button[type="submit"]').removeAttr('disabled');
                            get_contact_ledger();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });

            $(document).on('click', 'button.delete_ledger_discount', function() {
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        var data = $(this).serialize();

                        $.ajax({
                            method: 'DELETE',
                            url: href,
                            dataType: 'json',
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    get_contact_ledger();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
        });

        $(document).on('shown.bs.modal', '#edit_ledger_discount_modal', function(e) {
            $('#edit_ledger_discount_modal').find('#edit_discount_date').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
        })

        $("input.transaction_types, input#show_payments").on('ifChanged', function(e) {
            get_contact_ledger();
        });

        // Format is now fixed to format_4 - no change handler needed

        $(document).one('shown.bs.tab', 'a[href="#payments_tab"]', function() {
            get_contact_payments();
        })

        $(document).on('click', '#contact_payments_pagination a', function(e) {
            e.preventDefault();
            get_contact_payments($(this).attr('href'));
        })

        function get_contact_payments(url = null) {
            if (!url) {
                url =
                    "{{ action([\App\Http\Controllers\ContactController::class, 'getContactPayments'], [$contact->id]) }}";
            }
            $.ajax({
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('#contact_payments_div').fadeOut(400, function() {
                        $('#contact_payments_div')
                            .html(result).fadeIn(400, function() {
                                // Initialize DataTable after the content is loaded and visible
                                initializeContactPaymentGroupTable();
                            });
                    });
                },
            });
        }

        function initializeContactPaymentGroupTable() {
            console.log('Initializing contact payment group table...');
            
            if ($.fn.DataTable.isDataTable('#contact_payment_group_table')) {
                $('#contact_payment_group_table').DataTable().destroy();
            }
            
            var table = $('#contact_payment_group_table').DataTable({
                searching: false,
                ordering: false,
                paging: false,
                fixedHeader: false,
                dom: 't',
                ajax: {
                    url: "{{ route('get-contact-payment-group', ['contact_id' => $contact->id]) }}",
                    data: function(d) {
                        d.location_id = $('#ledger_location').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX error:', error, thrown);
                    }
                },
                columns: [
                    { data: 'group_ref_no', name: 'group_ref_no' },
                    { data: 'group_name', name: 'group_name' },
                    { data: 'total_amount', name: 'total_amount' },
                    { data: 'edit_payment', name: 'edit_payment' }
                ]
            });
            
            console.log('DataTable initialized:', table);
        }

        function get_contact_ledger() {
            var start_date = '';
            var end_date = '';
            var transaction_types = $('input.transaction_types:checked').map(function(i, e) {
                return e.value
            }).toArray();
            var show_payments = $('input#show_payments').is(':checked');
            var location_id = $('#ledger_location').val();

            // Get dates from separate date pickers
            if ($('#ledger_start_date').length && $('#ledger_start_date').data('daterangepicker')) {
                start_date = $('#ledger_start_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
            }
            if ($('#ledger_end_date').length && $('#ledger_end_date').data('daterangepicker')) {
                end_date = $('#ledger_end_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
            }

            // Always use format_4
            var format = 'format_4';
            
            var data = {
                start_date: start_date,
                transaction_types: transaction_types,
                show_payments: show_payments,
                end_date: end_date,
                format: format,
                location_id: location_id
            }
            
            $.ajax({
                url: '/contacts/ledger?contact_id={{ $contact->id }}',
                data: data,
                dataType: 'html',
                success: function(result) {
                    $('#contact_ledger_div').html(result);
                    __currency_convert_recursively($('#contact_ledger_div'));

                    // Initialize DataTable for ledger
                    if ($.fn.DataTable.isDataTable('#ledger_table')) {
                        $('#ledger_table').DataTable().destroy();
                    }
                    $('#ledger_table').DataTable({
                        searching: false,
                        ordering: false,
                        paging: false,
                        fixedHeader: false,
                        dom: 't',
                        scrollX: true
                    });
                },
            });
        }

        $(document).on('click', '#send_ledger', function() {
            var start_date = $('#ledger_start_date').data('daterangepicker') ? 
                             $('#ledger_start_date').data('daterangepicker').startDate.format('YYYY-MM-DD') : '';
            var end_date = $('#ledger_end_date').data('daterangepicker') ? 
                           $('#ledger_end_date').data('daterangepicker').startDate.format('YYYY-MM-DD') : '';
            var format = 'format_4'; // Always use format_4

            var location_id = $('#ledger_location').val();

            var url =
                "{{ action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], [$contact->id, 'send_ledger']) }}" +
                '?start_date=' + start_date + '&end_date=' + end_date + '&format=' + format + '&location_id=' +
                location_id;

            $.ajax({
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('.view_modal')
                        .html(result)
                        .modal('show');
                },
            });
        })
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Handle price update
        $(document).on('click', '.price-update-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const container = $(this).closest('.price-update-container');
            const input = container.find('.price-input');
            const productId = input.data('product-id');
            const variationId = input.data('variation-id');
            const contactId = input.data('contact-id');
            const newPrice = input.val();

            if (!newPrice || isNaN(newPrice)) {
                toastr.error('Please enter a valid price');
                return;
            }

            // Disable button during request
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: "{{ action([\App\Http\Controllers\ContactController::class, 'updateRecallPrice']) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId,
                    variation_id: variationId,
                    contact_id: contactId,
                    new_price: newPrice
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        // Update the price recall info
                        const priceRecallInfo = container.closest('.price-recall-info');
                        if (priceRecallInfo.length) {
                            priceRecallInfo.find('.badge-warning').html(
                                'Last Recall: ' + response.data.last_price +
                                ' <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" ' +
                                'title="' + response.data.updated_at + ' by ' + response.data
                                .updated_by + '"></i>'
                            );
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Something went wrong. Please try again.');
                    console.error(xhr);
                },
                complete: function() {
                    // Re-enable button after request
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i>');
                }
            });
        });

        // Handle input changes
        $('.price-input').on('input', function() {
            const btn = $(this).siblings('.price-update-btn');
            const value = $(this).val();

            if (value && !isNaN(value)) {
                btn.show();
            } else {
                btn.hide();
            }
        });




        $(document).on('click', '#customer_cart', function() {
            var url =
                "{{ action([\App\Http\Controllers\ContactController::class, 'getCustomerCart'], [$contact->id]) }}";

            $.ajax({
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('.view_modal')
                        .html(result)
                        .modal('show');
                },
            });
        });
        $(document).on('click', '#customer_prices', function() {
            var url =
                "{{ action([\App\Http\Controllers\ContactController::class, 'getCustomerPrices'], [$contact->id]) }}";

            $.ajax({
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('.view_modal')
                        .html(result)
                        .modal('show');
                },
            });
        });
        $(document).on('click', '#customer_edit', function() {
            var url = "{{ action([\App\Http\Controllers\ContactController::class, 'edit'], [$contact->id]) }}";

            $.ajax({
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('.contact_modal')
                        .html(result)
                        .modal('show');
                },
            });
        });



        $(document).on('click', '#print_ledger_pdf', function() {
            var start_date = $('#ledger_start_date').data('daterangepicker') ? 
                             $('#ledger_start_date').data('daterangepicker').startDate.format('YYYY-MM-DD') : '';
            var end_date = $('#ledger_end_date').data('daterangepicker') ? 
                           $('#ledger_end_date').data('daterangepicker').startDate.format('YYYY-MM-DD') : '';

            var format = 'format_4'; // Always use format_4
            var location_id = $('#ledger_location').val();

            var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date + '&format=' +
                format + '&location_id=' + location_id;
            window.open(url);
        });
        // $(document).on('click', '#print_ledger_pdf', function() {
        //     var start_date = $('#ledger_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
        //     var end_date = $('#ledger_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
        //     var format = $('input[name="ledger_format"]:checked').val();
        //     var location_id = $('#ledger_location').val();

        //     // Transform format_4 to format_1
        //     if (format === 'format_4') {
        //         format = 'format_1';
        //     }

        //     var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date + '&format=' +
        //         format + '&location_id=' + location_id;
        //     window.open(url);
        // });
            $(document).on('click', 'a.delete_sell_return', function (e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        var data = $(this).serialize();

                        $.ajax({
                            method: 'DELETE',
                            url: href,
                            dataType: 'json',
                            data: data,
                            success: function (result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    sell_return_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
    </script>
    @include('sale_pos.partials.sale_table_javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    @if (in_array($contact->type, ['both', 'supplier']))
        <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    @endif

    <!-- document & note.js -->
    @include('documents_and_notes.document_and_note_js')
    @if (!empty($contact_view_tabs))
        @foreach ($contact_view_tabs as $key => $tabs)
            @foreach ($tabs as $index => $value)
                @if (!empty($value['module_js_path']))
                    @include($value['module_js_path'])
                @endif
            @endforeach
        @endforeach   
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
            $('#purchase_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end
                        .format(moment_date_format));
                    purchase_table.ajax.reload();
                }
            );
            $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#purchase_list_filter_date_range').val('');
                purchase_table.ajax.reload();
            });
        });
    </script>

    <!-- Customer Addresses JavaScript -->
    @if (in_array($contact->type, ['customer', 'both']))
    <script type="text/javascript">
        $(document).ready(function() {
            // Show add address modal
            $(document).on('click', '#add_address_btn', function() {
                $('#add_address_modal').modal('show');
            });

            // Handle add address form submission
            $(document).on('submit', '#add_address_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: 'POST',
                    url: form.attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            $('#add_address_modal').modal('hide');
                            toastr.success(result.msg);
                            form[0].reset();
                            if (customer_addresses_table) {
                                customer_addresses_table.ajax.reload();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                            toastr.error(errorMsg);
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });

            // Show edit address modal
            $(document).on('click', '.edit-address-btn', function() {
                var url = $(this).data('href');
                
                $.ajax({
                    url: url,
                    dataType: 'html',
                    success: function(result) {
                        $('#edit_address_modal')
                            .html(result)
                            .modal('show');
                    },
                });
            });

            // Handle edit address form submission
            $(document).on('submit', '#edit_address_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: 'POST',
                    url: form.attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success === true) {
                            $('#edit_address_modal').modal('hide');
                            toastr.success(result.msg);
                            if (customer_addresses_table) {
                                customer_addresses_table.ajax.reload();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });
                            toastr.error(errorMsg);
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });

            // Delete address
            $(document).on('click', '.delete-address-btn', function(e) {
                e.preventDefault();
                var url = $(this).data('href');

                swal({
                    title: LANG.sure,
                    text: 'Are you sure you want to delete this address?',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        $.ajax({
                            method: 'DELETE',
                            url: url,
                            dataType: 'json',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    if (customer_addresses_table) {
                                        customer_addresses_table.ajax.reload();
                                    }
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
        });

        // Merge Customer Account functionality
        @php
            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) || auth()->user()->can('admin');
        @endphp
        @if ($is_admin && in_array($contact->type, ['customer', 'both']))
        $(document).ready(function() {
            var source_contact_id = {{ $contact->id }};

            // Initialize select2 for target contact
            $('#target_contact_id').select2({
                ajax: {
                    url: '{{ action([\App\Http\Controllers\ContactController::class, "getCustomersForMerge"]) }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            source_contact_id: source_contact_id
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data || []
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                placeholder: '-- Select Customer Account --',
                width: '100%'
            });

            // Open merge modal
            $(document).on('click', '#merge_customer_account', function() {
                $('#merge_customer_modal').modal('show');
                $('#target_contact_id').val(null).trigger('change');
                $('#merge_preview').hide();
                $('#confirm_merge_btn').prop('disabled', true);
            });

            // Load preview when target is selected
            $('#target_contact_id').on('change', function() {
                var target_id = $(this).val();
                if (target_id) {
                    // Load preview
                    $.ajax({
                        url: '{{ action([\App\Http\Controllers\ContactController::class, "getMergePreview"]) }}',
                        method: 'GET',
                        data: {
                            source_contact_id: source_contact_id
                        },
                        success: function(response) {
                            var summary = response;
                            var html = '<ul class="list-unstyled">';
                            
                            if (summary.transactions > 0) {
                                html += '<li><i class="fa fa-file-invoice"></i> ' + summary.transactions + ' Transaction(s)</li>';
                            }
                            if (summary.transaction_payments > 0) {
                                html += '<li><i class="fa fa-money-bill"></i> ' + summary.transaction_payments + ' Payment(s)</li>';
                            }
                            if (summary.addresses > 0) {
                                html += '<li><i class="fa fa-map-marker"></i> ' + summary.addresses + ' Address(es)</li>';
                            }
                            if (summary.user_access > 0) {
                                html += '<li><i class="fa fa-users"></i> ' + summary.user_access + ' User Access Record(s)</li>';
                            }
                            if (summary.documents > 0) {
                                html += '<li><i class="fa fa-file"></i> ' + summary.documents + ' Document(s)/Note(s)</li>';
                            }
                            if (summary.cart_items > 0) {
                                html += '<li><i class="fa fa-shopping-cart"></i> ' + summary.cart_items + ' Cart Item(s)</li>';
                            }
                            if (summary.wishlists > 0) {
                                html += '<li><i class="fa fa-heart"></i> ' + summary.wishlists + ' Wishlist Item(s)</li>';
                            }
                            if (summary.reviews > 0) {
                                html += '<li><i class="fa fa-star"></i> ' + summary.reviews + ' Review(s)</li>';
                            }
                            if (summary.credit_applications > 0) {
                                html += '<li><i class="fa fa-credit-card"></i> ' + summary.credit_applications + ' Credit Application(s)</li>';
                            }
                            if (summary.stock_alerts > 0) {
                                html += '<li><i class="fa fa-bell"></i> ' + summary.stock_alerts + ' Stock Alert(s)</li>';
                            }
                            if (summary.complaints > 0) {
                                html += '<li><i class="fa fa-exclamation-triangle"></i> ' + summary.complaints + ' Complaint(s)</li>';
                            }
                            if (summary.business_identifications > 0) {
                                html += '<li><i class="fa fa-id-card"></i> ' + summary.business_identifications + ' Business Identification(s)</li>';
                            }
                            if (summary.price_recalls > 0) {
                                html += '<li><i class="fa fa-tags"></i> ' + summary.price_recalls + ' Price Recall(s)</li>';
                            }

                            html += '</ul>';
                            $('#migration_summary').html(html);
                            $('#merge_preview').show();
                            $('#confirm_merge_btn').prop('disabled', false);
                        },
                        error: function() {
                            toastr.error('Error loading preview');
                        }
                    });
                } else {
                    $('#merge_preview').hide();
                    $('#confirm_merge_btn').prop('disabled', true);
                }
            });

            // Confirm merge - show confirmation modal
            $('#confirm_merge_btn').on('click', function() {
                var target_id = $('#target_contact_id').val();
                if (!target_id) {
                    toastr.error('Please select a target account');
                    return;
                }

                // Show confirmation modal
                $('#merge_confirmation_modal').modal('show');
                $('#merge_confirmation_input').val('').focus();
                $('#merge_confirmation_error').hide();
            });
            
            // Handle confirmation input keypress
            $('#merge_confirmation_input').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    $('#final_confirm_merge_btn').click();
                }
            });
            
            // Final confirm merge button
            $('#final_confirm_merge_btn').on('click', function() {
                var confirmationText = $('#merge_confirmation_input').val();
                
                // Check if user typed "Merge" exactly (case-sensitive)
                if (confirmationText !== 'Merge') {
                    $('#merge_confirmation_error').show();
                    $('#merge_confirmation_input').focus().select();
                    return;
                }
                
                // Hide confirmation modal
                $('#merge_confirmation_modal').modal('hide');
                
                // Get target ID
                var target_id = $('#target_contact_id').val();
                
                // Proceed with merge
                proceedWithMerge(target_id);
            });
            
            // Function to proceed with the merge
            function proceedWithMerge(target_id) {
                // Show loading
                $('#confirm_merge_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Merging...');

                $.ajax({
                    url: '{{ action([\App\Http\Controllers\ContactController::class, "mergeCustomerAccounts"]) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        source_contact_id: source_contact_id,
                        target_contact_id: target_id
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $('#merge_customer_modal').modal('hide');
                            
                            // Show migration log
                            if (response.migration_log && response.migration_log.length > 0) {
                                var logMsg = 'Migration completed:\n' + response.migration_log.join('\n');
                                swal({
                                    title: 'Merge Successful',
                                    text: logMsg,
                                    icon: 'success',
                                    button: 'OK'
                                });
                            }
                            
                            // Reload page after a short delay
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.msg);
                            $('#confirm_merge_btn').prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Merge');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error merging accounts';
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            errorMsg = xhr.responseJSON.msg;
                        }
                        toastr.error(errorMsg);
                        $('#confirm_merge_btn').prop('disabled', false).html('<i class="fa fa-check"></i> Confirm Merge');
                    }
                });
            }
        });
        @endif
    </script>
    @endif

    @include('sale_pos.partials.subscriptions_table_javascript', ['contact_id' => $contact->id])
@endsection

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Smokevana Vendor Central</title>
    
    @if(file_exists(public_path('uploads/business_logos/favicon.ico')))
        <link rel="icon" type="image/x-icon" href="{{ asset('uploads/business_logos/favicon.ico') }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Amazon Ember Fonts - Applied globally -->
    <link rel="stylesheet" href="{{ asset('css/amazon-ember-fonts.css') }}">
    
    <!-- Bootstrap Icons - More reliable than Material Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
 
    <link rel="stylesheet" href="{{ asset('css/vendor.css') }}?v={{ @filemtime(public_path('css/vendor.css')) }}">
    
    <style>
        /* =============================================
           SMOKEVANA VENDOR CENTRAL - AMAZON STYLE
           ============================================= */
        
        :root {
            /* Amazon-inspired color palette */
            --amazon-navy: #232f3e;
            --amazon-navy-light: #37475a;
            --amazon-navy-dark: #131921;
            --amazon-orange: #ff9900;
            --amazon-orange-hover: #e88b00;
            --amazon-orange-light: #febd69;
            --amazon-teal: #007185;
            --amazon-teal-hover: #005f6f;
            --amazon-link: #007185;
            --amazon-success: #067d62;
            --amazon-warning: #c45500;
            --amazon-error: #b12704;
            --amazon-star: #ffa41c;
            
            /* Neutrals */
            --gray-50: #f7f8f8;
            --gray-100: #f0f2f2;
            --gray-200: #e6e6e6;
            --gray-300: #d5d9d9;
            --gray-400: #adb1b8;
            --gray-500: #888c8c;
            --gray-600: #565959;
            --gray-700: #393939;
            --gray-800: #222;
            --gray-900: #111;
            
            /* Layout */
            --header-height: 60px;
            --subnav-height: 44px;
            --sidebar-width: 220px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Amazon Ember', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
        }

        a {
            color: var(--amazon-link);
            text-decoration: none;
        }

        a:hover {
            color: var(--amazon-warning);
            text-decoration: underline;
        }

        /* =============================================
           TOP HEADER - Amazon Style
           ============================================= */
        .sc-header {
            background: var(--amazon-navy);
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sc-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none !important;
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 3px;
            transition: all 0.15s ease;
        }

        .sc-logo:hover {
            border-color: #fff;
            text-decoration: none !important;
        }

        .sc-logo-icon {
            width: 36px;
            height: 36px;
            background: var(--amazon-orange);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--amazon-navy-dark);
            font-size: 18px;
            font-weight: 700;
        }

        .sc-logo-text {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .sc-logo-text span {
            color: var(--amazon-orange);
        }

        .sc-logo-subtitle {
            font-size: 11px;
            color: var(--gray-400);
            font-weight: 400;
            display: block;
            margin-top: -2px;
        }

        /* Search Bar */
        .sc-search {
            flex: 1;
            max-width: 500px;
            margin: 0 30px;
            position: relative;
        }

        .sc-search-input {
            width: 100%;
            height: 40px;
            padding: 0 44px 0 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            background: #fff;
            outline: none;
        }

        .sc-search-input:focus {
            box-shadow: 0 0 0 3px var(--amazon-orange);
        }

        .sc-search-btn {
            position: absolute;
            right: 0;
            top: 0;
            width: 44px;
            height: 40px;
            background: var(--amazon-orange);
            border: none;
            border-radius: 0 4px 4px 0;
            color: var(--amazon-navy-dark);
            font-size: 16px;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .sc-search-btn:hover {
            background: var(--amazon-orange-hover);
        }

        /* Header Actions */
        .sc-header-actions {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: auto;
        }

        .sc-header-item {
            padding: 8px 12px;
            border: 1px solid transparent;
            border-radius: 3px;
            color: #fff;
            text-decoration: none !important;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transition: all 0.15s ease;
            cursor: pointer;
            background: none;
        }

        .sc-header-item:hover {
            border-color: #fff;
            color: #fff;
            text-decoration: none !important;
        }

        .sc-header-item-label {
            font-size: 11px;
            color: var(--gray-300);
            line-height: 1;
        }

        .sc-header-item-value {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sc-header-item-value i {
            font-size: 10px;
        }

        /* Notifications */
        .sc-notif {
            position: relative;
            padding: 8px 14px;
            border: 1px solid transparent;
            border-radius: 3px;
            color: #fff;
            background: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .sc-notif:hover {
            border-color: #fff;
        }

        .sc-notif i {
            font-size: 20px;
        }

        .sc-notif-badge {
            position: absolute;
            top: 4px;
            right: 8px;
            min-width: 18px;
            height: 18px;
            background: var(--amazon-orange);
            color: var(--amazon-navy-dark);
            font-size: 11px;
            font-weight: 700;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
        }

        /* =============================================
           SUB NAVIGATION
           ============================================= */
        .sc-subnav {
            background: var(--amazon-navy-light);
            height: var(--subnav-height);
            position: fixed;
            top: var(--header-height);
            left: 0;
            right: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .sc-nav-list {
            display: flex;
            align-items: center;
            gap: 0;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sc-nav-item {
            position: relative;
        }

        .sc-nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none !important;
            border: 1px solid transparent;
            border-radius: 3px;
            transition: all 0.15s ease;
        }

        .sc-nav-link:hover {
            border-color: #fff;
            color: #fff;
        }

        .sc-nav-link.active {
            background: rgba(255,255,255,0.1);
            border-color: var(--amazon-orange);
        }

        .sc-nav-link i {
            font-size: 14px;
        }

        .sc-nav-badge {
            background: var(--amazon-orange);
            color: var(--amazon-navy-dark);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 4px;
        }

        /* =============================================
           MAIN CONTENT AREA
           ============================================= */
        .sc-body {
            margin-top: calc(var(--header-height) + var(--subnav-height));
            padding: 20px;
            min-height: calc(100vh - var(--header-height) - var(--subnav-height));
        }

        /* Breadcrumb */
        .sc-breadcrumb {
            font-size: 12px;
            color: var(--gray-600);
            margin-bottom: 16px;
        }

        .sc-breadcrumb a {
            color: var(--amazon-link);
        }

        .sc-breadcrumb span {
            color: var(--gray-500);
            margin: 0 6px;
        }

        /* Page Header */
        .sc-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .sc-page-title {
            font-size: 24px;
            font-weight: 400;
            color: var(--gray-900);
            margin: 0;
        }

        .sc-page-title strong {
            font-weight: 700;
        }

        /* =============================================
           CARDS
           ============================================= */
        .sc-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid var(--gray-300);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .sc-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--gray-50);
        }

        .sc-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sc-card-title i {
            color: var(--amazon-navy);
            font-size: 18px;
        }

        .sc-card-body {
            padding: 20px;
        }

        .sc-card-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* =============================================
           METRICS / STAT CARDS
           ============================================= */
        .sc-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .sc-metric {
            background: #fff;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .sc-metric:hover {
            border-color: var(--amazon-orange);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .sc-metric-icon {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 44px;
            height: 44px;
            background: var(--gray-100);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--amazon-navy);
        }

        .sc-metric-label {
            font-size: 12px;
            color: var(--gray-600);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .sc-metric-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
            margin-bottom: 6px;
        }

        .sc-metric-value.orange { color: var(--amazon-orange); }
        .sc-metric-value.teal { color: var(--amazon-teal); }
        .sc-metric-value.success { color: var(--amazon-success); }
        .sc-metric-value.warning { color: var(--amazon-warning); }

        .sc-metric-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sc-metric-change.up {
            color: var(--amazon-success);
        }

        .sc-metric-change.down {
            color: var(--amazon-error);
        }

        .sc-metric-desc {
            font-size: 12px;
            color: var(--gray-500);
        }

        /* =============================================
           BUTTONS
           ============================================= */
        .sc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none !important;
            border: none;
            white-space: nowrap;
        }

        .sc-btn-primary {
            background: linear-gradient(to bottom, #f7dfa5, #f0c14b);
            border: 1px solid #a88734;
            color: var(--gray-900);
        }

        .sc-btn-primary:hover {
            background: linear-gradient(to bottom, #f5d78e, #eeba37);
            color: var(--gray-900);
            text-decoration: none !important;
        }

        .sc-btn-secondary {
            background: linear-gradient(to bottom, #f7f8fa, #e7e9ec);
            border: 1px solid #adb1b8;
            color: var(--gray-800);
        }

        .sc-btn-secondary:hover {
            background: linear-gradient(to bottom, #e7e9ec, #d9dce1);
            text-decoration: none !important;
        }

        .sc-btn-orange {
            background: var(--amazon-orange);
            border: 1px solid var(--amazon-orange-hover);
            color: #fff;
        }

        .sc-btn-orange:hover {
            background: var(--amazon-orange-hover);
            color: #fff;
            text-decoration: none !important;
        }

        .sc-btn-link {
            background: none;
            border: none;
            color: var(--amazon-link);
            padding: 4px 8px;
        }

        .sc-btn-link:hover {
            color: var(--amazon-warning);
            text-decoration: underline !important;
        }

        .sc-btn-sm {
            padding: 5px 12px;
            font-size: 12px;
        }

        /* =============================================
           TABLES
           ============================================= */
        .sc-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sc-table thead th {
            background: var(--amazon-navy);
            color: #fff;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 12px 14px;
            text-align: left;
            border: none;
        }

        .sc-table tbody tr {
            border-bottom: 1px solid var(--gray-200);
            transition: background 0.15s ease;
        }

        .sc-table tbody tr:hover {
            background: #fffbf3;
        }

        .sc-table tbody tr:last-child {
            border-bottom: none;
        }

        .sc-table tbody td {
            padding: 14px;
            font-size: 13px;
            color: var(--gray-700);
            vertical-align: middle;
        }

        .sc-table tbody td a {
            color: var(--amazon-link);
            font-weight: 600;
        }

        .sc-table tbody td a:hover {
            color: var(--amazon-warning);
        }

        /* =============================================
           BADGES / STATUS
           ============================================= */
        .sc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .sc-badge-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .sc-badge-processing {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #007bff;
        }

        .sc-badge-shipped {
            background: #d1ecf1;
            color: var(--amazon-teal);
            border: 1px solid var(--amazon-teal);
        }

        .sc-badge-completed {
            background: #d4edda;
            color: var(--amazon-success);
            border: 1px solid var(--amazon-success);
        }

        .sc-badge-cancelled {
            background: #f8d7da;
            color: var(--amazon-error);
            border: 1px solid var(--amazon-error);
        }

        .sc-badge-active {
            background: #d4edda;
            color: var(--amazon-success);
        }

        .sc-badge-low {
            background: #fff3cd;
            color: #856404;
        }

        .sc-badge-out {
            background: #f8d7da;
            color: var(--amazon-error);
        }

        /* =============================================
           ALERTS
           ============================================= */
        .sc-alert {
            padding: 14px 18px;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px;
        }

        .sc-alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sc-alert-info {
            background: #e7f3fe;
            border: 1px solid #b6d4fe;
        }

        .sc-alert-info .sc-alert-icon {
            background: #0d6efd;
            color: #fff;
        }

        .sc-alert-warning {
            background: #fff8e6;
            border: 1px solid var(--amazon-orange);
        }

        .sc-alert-warning .sc-alert-icon {
            background: var(--amazon-orange);
            color: #fff;
        }

        .sc-alert-success {
            background: #e7f5ed;
            border: 1px solid var(--amazon-success);
        }

        .sc-alert-success .sc-alert-icon {
            background: var(--amazon-success);
            color: #fff;
        }

        .sc-alert-content h4 {
            margin: 0 0 4px 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--gray-800);
        }

        .sc-alert-content p {
            margin: 0;
            font-size: 13px;
            color: var(--gray-600);
        }

        /* =============================================
           FORMS
           ============================================= */
        .sc-form-group {
            margin-bottom: 16px;
        }

        .sc-form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 6px;
        }

        .sc-form-label .required {
            color: var(--amazon-error);
        }

        .sc-form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.15s ease;
            background: #fff;
        }

        .sc-form-control:focus {
            outline: none;
            border-color: var(--amazon-orange);
            box-shadow: 0 0 0 3px rgba(255,153,0,0.15);
        }

        .sc-form-control:disabled {
            background: var(--gray-100);
            color: var(--gray-500);
        }

        /* =============================================
           MODALS
           ============================================= */
        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: var(--amazon-navy);
            color: #fff;
            padding: 16px 20px;
            border: none;
            border-radius: 8px 8px 0 0;
        }

        .modal-header .close {
            color: #fff;
            opacity: 0.8;
            text-shadow: none;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        /* =============================================
           QUICK ACTION GRID
           ============================================= */
        .sc-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        .sc-action-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: #fff;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .sc-action-card:hover {
            border-color: var(--amazon-orange);
            box-shadow: 0 4px 12px rgba(255,153,0,0.15);
            transform: translateY(-2px);
            text-decoration: none !important;
        }

        .sc-action-card i {
            font-size: 28px;
            color: var(--amazon-navy);
            margin-bottom: 10px;
        }

        .sc-action-card span {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            text-align: center;
        }

        /* =============================================
           EMPTY STATE
           ============================================= */
        .sc-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-500);
        }

        .sc-empty i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .sc-empty h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 8px;
        }

        .sc-empty p {
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* =============================================
           USER DROPDOWN MENU - Custom Implementation
           ============================================= */
        .sc-user-dropdown {
            position: relative;
        }

        .sc-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 220px;
            background: #fff;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 1050;
            display: none;
            overflow: hidden;
        }

        .sc-dropdown-menu.show {
            display: block;
        }

        .sc-dropdown-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .sc-dropdown-header strong {
            display: block;
            color: var(--gray-800);
            font-size: 14px;
            font-weight: 700;
        }

        .sc-dropdown-header small {
            color: var(--gray-500);
            font-size: 12px;
        }

        .sc-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--gray-700);
            font-size: 14px;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }

        .sc-dropdown-item:hover {
            background: var(--gray-100);
            color: var(--amazon-orange);
        }

        .sc-dropdown-item i {
            width: 18px;
            text-align: center;
            color: var(--gray-500);
            font-size: 14px;
        }

        .sc-dropdown-item:hover i {
            color: var(--amazon-orange);
        }

        .sc-dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 4px 0;
        }

        /* =============================================
           DATA TABLES OVERRIDE
           ============================================= */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            padding: 6px 12px;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--amazon-orange);
            box-shadow: 0 0 0 2px rgba(255,153,0,0.15);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            padding: 4px 8px;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 10px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block !important;
            padding: 8px 14px !important;
            border: 1px solid var(--gray-300) !important;
            border-radius: 4px !important;
            background: #fff !important;
            margin: 0 3px !important;
            cursor: pointer !important;
            color: var(--gray-700) !important;
            font-size: 14px !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            background: var(--gray-100) !important;
            border-color: var(--amazon-orange) !important;
            color: var(--gray-800) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(to bottom, #f7dfa5, #f0c14b) !important;
            border-color: #a88734 !important;
            color: var(--gray-900) !important;
            font-weight: 600 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-weight: 500 !important;
        }

        /* =============================================
           RESPONSIVE
           ============================================= */
        @media (max-width: 991px) {
            .sc-search {
                display: none;
            }
            
            .sc-header-item-label {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sc-nav-list {
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 5px;
            }
            
            .sc-nav-link span {
                display: none;
            }
            
            .sc-metrics {
                grid-template-columns: 1fr 1fr;
            }
            
            .sc-page-title {
                font-size: 20px;
            }
        }

        @media (max-width: 576px) {
            .sc-body {
                padding: 16px;
            }
            
            .sc-metrics {
                grid-template-columns: 1fr;
            }
            
            .sc-card-body {
                padding: 16px;
            }
        }

        /* =============================================
           UTILITY CLASSES
           ============================================= */
        .text-muted { color: var(--gray-500) !important; }
        .text-success { color: var(--amazon-success) !important; }
        .text-warning { color: var(--amazon-warning) !important; }
        .text-danger { color: var(--amazon-error) !important; }
        .text-orange { color: var(--amazon-orange) !important; }
        .fw-bold { font-weight: 700 !important; }
        .fs-sm { font-size: 12px !important; }
    </style>
    @yield('css')
</head>
<body>
    @php
        $vendor = session('vendor_portal.vendor');
        $pendingOrdersCount = session('vendor_portal.pending_orders_count', 0);
        $pendingProductRequestsCount = session('vendor_portal.pending_product_requests_count', 0);
    @endphp

    <!-- Top Header -->
    <header class="sc-header">
        <a href="{{ route('vendor.dashboard') }}" class="sc-logo">
            <span class="sc-logo-icon">S</span>
            <span class="sc-logo-text">
                Smokevana<span>Central</span>
                <span class="sc-logo-subtitle">Vendor Portal</span>
            </span>
        </a>

        <div class="sc-header-actions">
            @if($vendor)
            <a href="{{ route('vendor.orders') }}" class="sc-header-item">
                <span class="sc-header-item-label">Pending</span>
                <span class="sc-header-item-value">
                    {{ $pendingOrdersCount }} Orders
                    @if($pendingOrdersCount > 0)
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 10px;"></i>
                    @endif
                </span>
            </a>

            <button class="sc-notif" title="Notifications">
                <i class="bi bi-bell"></i>
                @if($pendingOrdersCount > 0)
                <span class="sc-notif-badge">{{ $pendingOrdersCount }}</span>
                @endif
            </button>

            <button class="sc-notif" title="Help">
                <i class="bi bi-question-circle"></i>
            </button>

            <div class="sc-user-dropdown">
                <a href="#" class="sc-header-item" onclick="toggleUserDropdown(event)">
                    <span class="sc-header-item-label">Hello, {{ $vendor->display_name ?? 'Vendor' }}</span>
                    <span class="sc-header-item-value">Account <i class="bi bi-chevron-down" style="font-size: 10px;"></i></span>
                </a>
                <div class="sc-dropdown-menu" id="userDropdownMenu">
                    <div class="sc-dropdown-header">
                        <strong>{{ $vendor->display_name ?? 'Vendor' }}</strong>
                        <small>{{ $vendor->email ?? '' }}</small>
                    </div>
                    <a href="{{ route('vendor.profile') }}" class="sc-dropdown-item"><i class="bi bi-person"></i> Profile Settings</a>
                    <a href="{{ route('vendor.earnings') }}" class="sc-dropdown-item"><i class="bi bi-wallet"></i> My Earnings</a>
                    <div class="sc-dropdown-divider"></div>
                    <a href="#" class="sc-dropdown-item" onclick="event.preventDefault(); document.getElementById('vendor-logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </a>
                </div>
            </div>
            @endif
        </div>
    </header>

    <!-- Sub Navigation -->
    <nav class="sc-subnav">
        <ul class="sc-nav-list">
            <li class="sc-nav-item">
                <a href="{{ route('vendor.dashboard') }}" class="sc-nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.orders') }}" class="sc-nav-link {{ request()->routeIs('vendor.orders*') ? 'active' : '' }}">
                    <i class="bi bi-cart"></i>
                    <span>Orders</span>
                    @if($pendingOrdersCount > 0)
                    <span class="sc-nav-badge">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.products') }}" class="sc-nav-link {{ request()->routeIs('vendor.products*') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span>Inventory</span>
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.earnings') }}" class="sc-nav-link {{ request()->routeIs('vendor.earnings*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.product-requests') }}" class="sc-nav-link {{ request()->routeIs('vendor.product-requests*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Product Requests</span>
                    @if($pendingProductRequestsCount > 0)
                    <span class="sc-nav-badge">{{ $pendingProductRequestsCount }}</span>
                    @endif
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.purchase-orders') }}" class="sc-nav-link {{ request()->routeIs('vendor.purchase-orders*') ? 'active' : '' }}">
                    <i class="bi bi-file-text"></i>
                    <span>Purchase Orders</span>
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.purchase-receipts') }}" class="sc-nav-link {{ request()->routeIs('vendor.purchase-receipts*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Purchase Receipts</span>
                </a>
            </li>
            <li class="sc-nav-item">
                <a href="{{ route('vendor.profile') }}" class="sc-nav-link {{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="sc-body">
        @if(session('success'))
        <div class="sc-alert sc-alert-success">
            <div class="sc-alert-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="sc-alert-content">
                <p>{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="sc-alert sc-alert-warning">
            <div class="sc-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="sc-alert-content">
                <p>{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <form id="vendor-logout-form" action="{{ route('vendor.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="{{ asset('js/vendor.js') }}?v={{ @filemtime(public_path('js/vendor.js')) }}"></script>

    @php
        $vendor_portal_locale = app()->getLocale() ?: 'en';
    @endphp
    @if (file_exists(public_path('js/lang/' . $vendor_portal_locale . '.js')))
        <script src="{{ asset('js/lang/' . $vendor_portal_locale . '.js') }}?v={{ @filemtime(public_path('js/lang/' . $vendor_portal_locale . '.js')) }}"></script>
    @else
        <script src="{{ asset('js/lang/en.js') }}?v={{ @filemtime(public_path('js/lang/en.js')) }}"></script>
    @endif

    <script>
        // Toastr configuration
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 4000
        };

        // CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // User dropdown toggle
        function toggleUserDropdown(e) {
            e.preventDefault();
            e.stopPropagation();
            var dropdown = document.getElementById('userDropdownMenu');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            var dropdown = document.getElementById('userDropdownMenu');
            var trigger = document.querySelector('.sc-user-dropdown');
            if (dropdown && trigger && !trigger.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>

    @yield('javascript')
</body>
</html>

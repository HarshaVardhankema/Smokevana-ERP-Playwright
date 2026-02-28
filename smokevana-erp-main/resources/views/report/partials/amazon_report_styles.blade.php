{{-- Amazon-style report page: dark banner, section cards, orange action buttons --}}
@include('layouts.partials.amazon_admin_styles')
<style>
/* Page background */
.report-amazon-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
.report-amazon-page .content { padding-top: 0; }

/* Content Header – Amazon banner */
.report-amazon-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e;
    border-radius: 10px;
    padding: 24px 32px !important;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.report-amazon-page .content-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    opacity: 0.9;
}
.report-amazon-page .content-header h1 {
    color: #fff !important;
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    margin: 0 !important;
}

/* Report Amazon Banner – icon + title + subtitle */
.report-amazon-page .sr-banner {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border-radius: 10px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.report-amazon-page .sr-banner .banner-inner { display: flex; align-items: center; gap: 18px; }
.report-amazon-page .sr-banner .banner-icon {
    width: 52px; height: 52px; min-width: 52px;
    border-radius: 10px; background: rgba(255,255,255,0.1);
    color: #fff; font-size: 24px;
    display: flex; align-items: center; justify-content: center;
}
.report-amazon-page .sr-banner .banner-text { display: flex; flex-direction: column; gap: 6px; }
.report-amazon-page .sr-banner .banner-title { font-size: 24px; font-weight: 700; margin: 0; color: #fff; }
.report-amazon-page .sr-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.78); margin: 0; }
.report-amazon-page .sr-banner .btn-sr-filters {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important; color: #fff !important;
    font-weight: 600; padding: 10px 20px; border-radius: 6px;
}
.report-amazon-page .sr-banner .btn-sr-filters:hover { color: #fff !important; opacity: 0.95; }

/* Section cards: dark header + orange line + light body */
.report-amazon-page .box-primary,
.report-amazon-page .box { border-radius: 10px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #D5D9D9; background: #fff !important; }
.report-amazon-page .box .box-header,
.report-amazon-page .box-primary .box-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important; border: none !important;
    padding: 14px 20px !important;
    position: relative;
}
.report-amazon-page .box .box-header::before,
.report-amazon-page .box-primary .box-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900;
}
.report-amazon-page .box .box-title,
.report-amazon-page .box-primary .box-title { color: #fff !important; font-weight: 600; font-size: 1rem; }
.report-amazon-page .box .box-title i,
.report-amazon-page .box-primary .box-title i { color: #FF9900 !important; }
.report-amazon-page .box .box-header i,
.report-amazon-page .box-primary .box-header i { color: #FF9900 !important; }
.report-amazon-page .box .box-body { background: #f7f8f8 !important; padding: 1.25rem 1.5rem; }
.report-amazon-page .box .chart-container { background: #fff; border-radius: 8px; padding: 12px; margin: 0; }

/* Tabs card – inactive tabs dark, active tab Amazon orange */
.report-amazon-page .nav-tabs-custom {
    background: #fff; border-radius: 10px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #D5D9D9;
}
.report-amazon-page .nav-tabs-custom > .nav-tabs {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    border: none; margin: 0;
    padding: 14px 16px 0; position: relative;
}
.report-amazon-page .nav-tabs-custom > .nav-tabs::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900;
}
.report-amazon-page .nav-tabs-custom > .nav-tabs > li > a {
    color: rgba(255,255,255,0.9) !important; border: none !important; border-radius: 8px 8px 0 0;
    padding: 10px 18px; font-weight: 500;
    transition: background 0.2s ease, color 0.2s ease;
}
.report-amazon-page .nav-tabs-custom > .nav-tabs > li > a:hover {
    color: #fff !important; background: rgba(255,255,255,0.12) !important;
}
/* Active tab: Amazon orange so selected option is clearly visible */
.report-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a,
.report-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a:hover,
.report-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a:focus {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 -2px 8px rgba(255,153,0,0.25);
}
.report-amazon-page .nav-tabs-custom > .nav-tabs > li.active > a i {
    color: #fff !important;
}
.report-amazon-page .nav-tabs-custom > .tab-content {
    background: #f7f8f8; padding: 1.25rem 1.5rem; border: none;
}

/* DataTables: toolbar + visible action buttons (no white/hidden on hover) */
.report-amazon-page .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #D5D9D9; border-radius: 6px; padding: 8px 12px;
}
.report-amazon-page .dataTables_wrapper .dataTables_filter input:focus {
    border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
}
.report-amazon-page .dataTables_wrapper .dataTables_length select { border: 1px solid #D5D9D9; border-radius: 6px; }
.report-amazon-page .dt-buttons .dt-button,
.report-amazon-page .dt-buttons button {
    background: linear-gradient(to bottom, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #232f3e !important;
    color: #fff !important;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease !important;
}
.report-amazon-page .dt-buttons .dt-button:hover,
.report-amazon-page .dt-buttons button:hover {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(255,153,0,0.35);
    transform: translateY(-1px);
}

/* Primary buttons */
.report-amazon-page .tw-dw-btn-primary,
.report-amazon-page .tw-dw-btn.tw-dw-btn-primary {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}
.report-amazon-page .tw-dw-btn-primary:hover,
.report-amazon-page .tw-dw-btn.tw-dw-btn-primary:hover {
    opacity: 0.95;
    color: #fff !important;
}

/* Table inside tabs */
.report-amazon-page .nav-tabs-custom .table { background: #fff; }
.report-amazon-page .nav-tabs-custom thead th {
    background: #232f3e !important; color: #fff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
    font-weight: 600; font-size: 13px;
}

/* All report tables – Amazon dark header */
.report-amazon-page table thead th {
    background: #232f3e !important;
    color: #fff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
    font-weight: 600;
    font-size: 13px;
}
.report-amazon-page table tbody td {
    padding: 12px 14px;
    color: #0f1111;
    border-color: #e5e7eb;
    font-size: 13px;
}
.report-amazon-page table tbody tr:nth-child(even) td { background: #f9fafb !important; }
.report-amazon-page table tbody tr:hover td { background: #fff8e7 !important; }
.report-amazon-page table tfoot td { background: #f7f8f8 !important; font-weight: 600; }

/* Pagination */
.report-amazon-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
    border-color: #ff9900 !important;
    color: #0f1111 !important;
}
.report-amazon-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border-color: #ff9900;
    background: #fff8e7 !important;
}

/* Table action buttons (e.g. View in Sell Payment Report) – Amazon orange, visible on hover */
.report-amazon-page .table .btn-primary,
.report-amazon-page .table .btn.btn-default.btn-sm.view-payment,
.report-amazon-page .table td .btn {
    background: linear-gradient(to bottom, #37475a 0%, #232f3e 100%) !important;
    border-color: #232f3e !important; color: #fff !important;
    border-radius: 6px; font-weight: 600;
    transition: all 0.2s ease !important;
}
.report-amazon-page .table .btn-primary:hover,
.report-amazon-page .table .btn.btn-default.btn-sm.view-payment:hover,
.report-amazon-page .table td .btn:hover {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important; color: #fff !important;
    box-shadow: 0 2px 8px rgba(255,153,0,0.35);
    transform: translateY(-1px);
}
</style>

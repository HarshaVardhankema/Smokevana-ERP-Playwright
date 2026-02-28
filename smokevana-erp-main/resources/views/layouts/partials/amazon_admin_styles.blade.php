{{-- Amazon-style admin/settings pages: dark blue banner + orange primary buttons --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* Base layout spacing for Amazon admin pages */
.admin-amazon-page {
    box-sizing: border-box;
    padding: 16px 20px 24px;
}
@media (max-width: 768px) {
    .admin-amazon-page {
        padding: 12px 12px 20px;
    }
}

/* ========== Amazon theme banner (all pages): thin orange stripe on top, dark bar, rounded bottom ========== */
.amazon-theme-banner {
    position: relative;
    overflow: hidden;
    border-radius: 0 0 10px 10px !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
}
.amazon-theme-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px !important;
    background: #ff9900 !important;
    z-index: 1;
}

/* Admin Amazon Banner Header: gradient + 3px orange bar on top */
.admin-amazon-page .content-header {
    background: linear-gradient(180deg, #37475a 0%, #232f3e 100%) !important;
    border: 1px solid #4a5d6e;
    border-radius: 10px;
    padding: 24px 32px !important;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.admin-amazon-page .content-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9900, #e47911);
    opacity: 0.9;
}
.admin-amazon-page .content-header h1,
.admin-amazon-page .content-header h1 small {
    color: #fff !important;
}
.admin-amazon-page .content-header h1 {
    font-size: 22px !important;
    font-weight: 700 !important;
}
.admin-amazon-page .content-header h1 small,
.admin-amazon-page .content-header small {
    color: rgba(249, 250, 251, 0.88) !important;
    font-size: 13px !important;
}

/* Amazon orange primary buttons */
.admin-amazon-page .tw-dw-btn-primary,
.admin-amazon-page .tw-dw-btn.tw-dw-btn-primary,
.admin-amazon-page .amazon-add-btn,
.admin-amazon-page .btn-primary,
.admin-amazon-page .box-tools .btn-modal,
.admin-amazon-page .box-tools .tw-dw-btn,
.admin-amazon-page .box-tools .tw-bg-gradient-to-r,
.admin-amazon-page .tw-bg-gradient-to-r,
.admin-amazon-page button.btn-modal,
.admin-amazon-page .gradiantDiv,
.admin-amazon-page a.gradiantDiv {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}
.admin-amazon-page .tw-dw-btn-primary:hover,
.admin-amazon-page .amazon-add-btn:hover,
.admin-amazon-page .btn-primary:hover,
.admin-amazon-page .box-tools .btn-modal:hover,
.admin-amazon-page .box-tools .tw-dw-btn:hover,
.admin-amazon-page .tw-bg-gradient-to-r:hover,
.admin-amazon-page button.btn-modal:hover,
.admin-amazon-page .gradiantDiv:hover,
.admin-amazon-page a.gradiantDiv:hover {
    opacity: 0.95;
    color: #fff !important;
}

/* Save/Update primary buttons */
.admin-amazon-page .tw-dw-btn-error[type="submit"],
.admin-amazon-page button.tw-dw-btn-error[type="submit"] {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}

/* DataTables export buttons */
.admin-amazon-page .dt-buttons .dt-button,
.admin-amazon-page .dt-buttons button {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}
.admin-amazon-page .dt-buttons .dt-button:hover,
.admin-amazon-page .dt-buttons button:hover {
    opacity: 0.95;
    color: #fff !important;
}

/* File input wrapper - style the label that triggers file input */
.admin-amazon-page input[type="file"] + .input-group-btn .btn,
.admin-amazon-page .fileinput .btn {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: #fff !important;
}

/* ========== Amazon Settings Cards (Tailwind-inspired) ========== */
.admin-amazon-page .amazon-settings-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 4px 12px rgba(55, 71, 90, 0.06);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}
.admin-amazon-page .amazon-settings-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 153, 0, 0.15);
    border-color: #4a5d6e;
}
.admin-amazon-page .amazon-settings-card__header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #ffffff;
    font-weight: 600;
    font-size: 0.9375rem;
    padding: 14px 20px;
    border-bottom: 2px solid #ff9900;
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-amazon-page .amazon-settings-card__header i,
.admin-amazon-page .amazon-settings-card__header .fa {
    color: #ff9900;
    font-size: 1.1rem;
}
.admin-amazon-page .amazon-settings-card__body {
    padding: 20px 24px;
    background: #ffffff;
}
.admin-amazon-page .amazon-settings-card__body .form-group label {
    color: #111827;
    font-weight: 500;
}
.admin-amazon-page .amazon-settings-card__body .help-block,
.admin-amazon-page .amazon-settings-card__body .text-muted {
    color: #6b7280 !important;
}

/* Tab menu - Amazon theme */
.admin-amazon-page .pos-tab-menu {
    background: #37475a !important;
    border: 1px solid #4a5d6e !important;
    border-radius: 12px !important;
    padding: 8px 0 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
}
.admin-amazon-page .pos-tab-menu .list-group-item {
    background: transparent !important;
    border: none !important;
    color: #b8c4ce !important;
    padding: 12px 16px !important;
    margin: 2px 8px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}
.admin-amazon-page .pos-tab-menu .list-group-item:hover {
    background: rgba(255, 153, 0, 0.12) !important;
    color: #ffffff !important;
}
.admin-amazon-page .pos-tab-menu .list-group-item.active {
    background: linear-gradient(135deg, #ff9900 0%, #e47911 100%) !important;
    color: #0f1111 !important;
    font-weight: 600 !important;
}
.admin-amazon-page .pos-tab-container {
    background: transparent !important;
    border: none !important;
}
.admin-amazon-page .pos-tab {
    padding-left: 1.5rem !important;
    font-family: 'Inter', sans-serif !important;
}

/* Modern Settings Components */
.modern-settings-card {
    background: #ffffff !important;
    border-radius: 12px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
    padding: 24px !important;
    margin-bottom: 24px !important;
    border: 1px solid #f0f0f0 !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.modern-settings-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}

.modern-settings-card__title {
    font-size: 16px !important;
    font-weight: 600 !important;
    color: #1a1a1a !important;
    margin-bottom: 20px !important;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #f5f5f5;
    padding-bottom: 12px;
}

.modern-settings-card__title i {
    color: #ff9900;
}

/* Responsive Grid for Cards */
.settings-cards-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 24px !important;
}

@media (max-width: 991px) {
    .settings-cards-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Modern Toggle Switch Style */
.modern-switch-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #fafafa;
}

.modern-switch-container:last-child {
    border-bottom: none;
}

.modern-switch-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
}

.modern-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
    margin: 0 !important;
}

.modern-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.modern-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #e5e7eb;
    transition: .3s;
    border-radius: 22px;
}

.modern-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.modern-switch input:checked + .modern-slider {
    background-color: #ff9900;
}

/* Style for form-control and input-groups in Amazon theme */
.admin-amazon-page .form-control {
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    height: 40px !important;
    box-shadow: none !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    font-size: 14px !important;
}

.admin-amazon-page .form-control:focus {
    border-color: #ff9900 !important;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1) !important;
}

.admin-amazon-page .input-group-addon {
    background-color: #f9fafb !important;
    border: 1px solid #d1d5db !important;
    color: #6b7280 !important;
    border-radius: 8px 0 0 8px !important;
    padding: 0 16px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.admin-amazon-page .input-group .form-control {
    border-radius: 0 8px 8px 0 !important;
    border-left: none !important;
}

.admin-amazon-page .input-group {
    display: flex !important;
    width: 100% !important;
}

.admin-amazon-page .input-group select.form-control {
    flex: 1 !important;
}

.admin-amazon-page .modern-switch-container.no-border {
    border-bottom: none !important;
}

/* Tooltip icon styling */
.admin-amazon-page .fa-info-circle, 
.admin-amazon-page .fa-question-circle {
    color: #9ca3af !important;
    margin-left: 4px;
    font-size: 14px;
}

/* Select2 styling for Amazon theme */
.admin-amazon-page .select2-container--default .select2-selection--single {
    border: 1px solid #d1d5db !important;
    border-radius: 8px !important;
    height: 40px !important;
    display: flex !important;
    align-items: center !important;
}

.admin-amazon-page .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #374151 !important;
    padding-left: 0 !important;
}

.admin-amazon-page .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    top: 1px !important;
}

.admin-amazon-page .input-group .select2-container--default .select2-selection--single {
    border-radius: 0 8px 8px 0 !important;
    border-left: none !important;
}

.modern-switch input:checked + .modern-slider:before {
    transform: translateX(22px);
}

.modern-switch input:focus + .modern-slider {
    box-shadow: 0 0 1px #ff9900;
}

/* Improve consistent font styling for labels and inputs */
.admin-amazon-page .pos-tab label,
.admin-amazon-page .pos-tab .form-group label {
    font-family: 'Inter', sans-serif !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    color: #4b5563 !important;
}

.admin-amazon-page .pos-tab .form-control {
    font-family: 'Inter', sans-serif !important;
    font-size: 14px !important;
    border-radius: 8px !important;
    border: 1px solid #d1d5db !important;
    padding: 10px 12px !important;
}

.admin-amazon-page .pos-tab .form-control:focus {
    border-color: #ff9900 !important;
    box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1) !important;
}

/* Price group table inside card - Amazon header */
.admin-amazon-page .amazon-settings-card .table thead th {
    background: #232f3e !important;
    color: #ffffff !important;
    border-color: #4a5d6e !important;
    padding: 12px 14px !important;
}
.admin-amazon-page .amazon-settings-card .table tbody td {
    border-color: #e5e7eb !important;
}
.admin-amazon-page .amazon-settings-card .table tbody tr:nth-child(even) {
    background: #f9fafb !important;
}
.admin-amazon-page .amazon-settings-card h4 {
    color: #111827;
    font-weight: 600;
    margin-top: 0;
}

/* Content header with back button - flex layout */
.admin-amazon-page .content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}
.admin-amazon-page .content-header .btn-back,
.admin-amazon-page .content-header a.btn-header {
    background: rgba(255,255,255,0.2);
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.35);
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}
.admin-amazon-page .content-header .btn-back:hover,
.admin-amazon-page .content-header a.btn-header:hover {
    background: rgba(255,255,255,0.3);
    color: #fff !important;
    text-decoration: none !important;
}

/* Page background for bookkeeping/form pages */
.admin-amazon-page.bk-amazon-page {
    background: #EAEDED;
    min-height: 100%;
    padding-bottom: 2rem;
}
.admin-amazon-page .content { padding-top: 0; }

/* Form controls - Amazon theme */
.admin-amazon-page .form-control,
.admin-amazon-page .al-form-input,
.admin-amazon-page input[type="text"],
.admin-amazon-page input[type="number"],
.admin-amazon-page input[type="date"],
.admin-amazon-page textarea.form-control,
.admin-amazon-page select.form-control {
    border: 1px solid #D5D9D9 !important;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
}
.admin-amazon-page .form-control:focus,
.admin-amazon-page .al-form-input:focus,
.admin-amazon-page input:focus,
.admin-amazon-page textarea:focus,
.admin-amazon-page select:focus {
    border-color: #FF9900 !important;
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25) !important;
}

/* Select2 in admin-amazon-page */
.admin-amazon-page .select2-container--default .select2-selection--single,
.admin-amazon-page .select2-container--default .select2-selection--multiple {
    border: 1px solid #D5D9D9 !important;
    border-radius: 8px;
}
.admin-amazon-page .select2-container--default.select2-container--focus .select2-selection--single,
.admin-amazon-page .select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
}
.admin-amazon-page .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #FF9900 !important;
    color: #fff !important;
}

/* Add Line / Action buttons - Amazon orange */
.admin-amazon-page .btn-add-line,
.admin-amazon-page .btn-submit:not(.btn-default) {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border: 1px solid #C7511F !important;
    color: #fff !important;
}
.admin-amazon-page .btn-add-line:hover,
.admin-amazon-page .btn-submit:hover {
    opacity: 0.95;
    color: #fff !important;
}

/* Table header in cards - Amazon dark */
.admin-amazon-page .lines-table th,
.admin-amazon-page .deposit-table th {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
    color: #fff !important;
    border-color: #4a5d6e !important;
}
.admin-amazon-page .lines-table th::before,
.admin-amazon-page .deposit-table thead::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: #ff9900;
}

/* Type selector cards - Amazon selected state */
.admin-amazon-page .coa-type-option.selected,
.admin-amazon-page .al-type-card.selected {
    border-color: #FF9900 !important;
    background: #fff8e7 !important;
}
.admin-amazon-page .coa-type-option:hover,
.admin-amazon-page .al-type-card:hover {
    border-color: #FF9900;
}

/* Bank account card - Amazon selected */
.admin-amazon-page .bank-account-card.selected {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.25);
}

/* ========== Discount $ / % toggle (used in sales invoice product rows) ========== */
.discount-input-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    width: 140px !important;
    margin: 0 auto !important;
}
.discount-input-wrap .discount-amt {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    height: 30px !important;
    padding: 2px 6px !important;
    font-size: 12px !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 4px !important;
    box-sizing: border-box !important;
}
.discount-input-wrap .discount-type-sel {
    flex: 0 0 44px !important;
    width: 44px !important;
    min-width: 44px !important;
    max-width: 44px !important;
    height: 30px !important;
    padding: 0 !important;
    margin: 0 !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    line-height: 30px !important;
    text-align: center !important;
    text-align-last: center !important;
    color: #0F1111 !important;
    background: #F0F2F2 !important;
    border: 1px solid #D5D9D9 !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    box-shadow: none !important;
    outline: none !important;
    overflow: visible !important;
    box-sizing: border-box !important;
}
.discount-input-wrap .discount-type-sel:hover {
    border-color: #FF9900 !important;
    background: #FFF8E7 !important;
}
.discount-input-wrap .discount-type-sel:focus {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.25) !important;
}
.discount-input-wrap .discount-type-sel option {
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    padding: 6px 10px;
}

/* ========== Module Cards Grid (Business Settings - Modules Tab) ========== */
.admin-amazon-page .modules-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 1rem;
}
@media (max-width: 992px) {
    .admin-amazon-page .modules-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .admin-amazon-page .modules-grid {
        grid-template-columns: 1fr;
    }
}

.admin-amazon-page .module-card {
    padding: 16px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}
.admin-amazon-page .module-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.admin-amazon-page .module-card--checked {
    border-color: #3b82f6;
    background: #eff6ff;
}
.admin-amazon-page .module-card--checked:hover {
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.admin-amazon-page .module-card__label {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    cursor: pointer;
    width: 100%;
}
.admin-amazon-page .module-card__checkbox {
    margin: 0;
    flex-shrink: 0;
    cursor: pointer;
}
.admin-amazon-page .module-card__text {
    flex: 1;
    font-size: 0.9375rem;
    color: #111827;
    font-weight: 500;
    line-height: 1.5;
    display: flex;
    align-items: center;
    gap: 6px;
}
.admin-amazon-page .module-card__text .fa-info-circle,
.admin-amazon-page .module-card__text .fa-info {
    color: #3b82f6;
    font-size: 0.875rem;
    cursor: help;
}

/* ========== Referral Program Settings: Card-based Layout ========== */
.admin-amazon-page .referral-program-settings {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.admin-amazon-page .referral-section-title {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 22px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 24px;
    margin-top: 0;
}

/* Main Card */
.admin-amazon-page .referral-main-card {
    background: #ffffff;
    padding: 28px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

/* Sub Cards */
.admin-amazon-page .referral-sub-card {
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    background: #f9fafb;
    margin-bottom: 20px;
}
.admin-amazon-page .referral-sub-card:last-child {
    margin-bottom: 0;
}

.admin-amazon-page .referral-sub-card__title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    margin-top: 0;
}

/* Labels */
.admin-amazon-page .referral-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}

/* Helper Text */
.admin-amazon-page .referral-helper-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
    margin-bottom: 0;
}

/* Form Groups */
.admin-amazon-page .referral-form-group {
    margin-bottom: 0;
}

/* Inputs and Selects */
.admin-amazon-page .referral-input,
.admin-amazon-page .referral-input.form-control {
    height: 42px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 0 12px;
    font-size: 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.admin-amazon-page .referral-input:focus,
.admin-amazon-page .referral-input.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    outline: none;
}

/* Select2 styling for referral inputs */
.admin-amazon-page .referral-input.select2-container--default .select2-selection--single,
.admin-amazon-page .referral-input.select2-container--default .select2-selection--multiple {
    height: 42px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.admin-amazon-page .referral-input.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px;
    padding-left: 12px;
    padding-right: 20px;
}
.admin-amazon-page .referral-input.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}
.admin-amazon-page .referral-input.select2-container--default.select2-container--focus .select2-selection--single,
.admin-amazon-page .referral-input.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* Checkbox Groups */
.admin-amazon-page .referral-checkbox-group {
    margin-bottom: 16px;
}
.admin-amazon-page .referral-checkbox-group:last-child {
    margin-bottom: 0;
}
.admin-amazon-page .referral-checkbox-group--inline {
    margin-bottom: 20px;
}

/* Checkbox Wrapper */
.admin-amazon-page .referral-checkbox-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    gap: 10px;
}

/* Modern Checkbox Styling */
.admin-amazon-page .referral-checkbox-input {
    width: 20px;
    height: 20px;
    margin: 0;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    background-color: #ffffff;
    position: relative;
    flex-shrink: 0;
    transition: all 0.2s ease;
}
.admin-amazon-page .referral-checkbox-input:hover {
    border-color: #3b82f6;
}
.admin-amazon-page .referral-checkbox-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
.admin-amazon-page .referral-checkbox-input:checked::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
.admin-amazon-page .referral-checkbox-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* Checkbox Label */
.admin-amazon-page .referral-checkbox-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    line-height: 1.5;
}

/* Availability Row (B2B and B2C side-by-side) */
.admin-amazon-page .referral-availability-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.admin-amazon-page .referral-availability-row .referral-checkbox-group {
    flex: 1;
    min-width: 200px;
    margin-bottom: 0;
}
@media (max-width: 576px) {
    .admin-amazon-page .referral-availability-row {
        flex-direction: column;
        gap: 16px;
    }
    .admin-amazon-page .referral-availability-row .referral-checkbox-group {
        min-width: 100%;
    }
}

/* Hide class support */
.admin-amazon-page .referral-program-settings .hide {
    display: none !important;
}

/* ========== Product Settings: Card-based Layout ========== */
.admin-amazon-page .product-settings {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.admin-amazon-page .product-section-title {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 22px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 24px;
    margin-top: 0;
}

/* Main Card */
.admin-amazon-page .product-main-card {
    background: #ffffff;
    padding: 28px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

/* Sub Cards */
.admin-amazon-page .product-sub-card {
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    background: #f9fafb;
    margin-bottom: 20px;
}
.admin-amazon-page .product-sub-card:last-child {
    margin-bottom: 0;
}

.admin-amazon-page .product-sub-card__title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    margin-top: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.admin-amazon-page .product-sub-card__title i {
    color: #3b82f6;
    font-size: 18px;
    flex-shrink: 0;
}

/* Labels */
.admin-amazon-page .product-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
    display: block;
}

/* Form Groups */
.admin-amazon-page .product-form-group {
    margin-bottom: 0;
}

/* Form Grid */
.admin-amazon-page .product-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
@media (max-width: 768px) {
    .admin-amazon-page .product-form-grid {
        grid-template-columns: 1fr;
    }
}

/* Checkbox Grid */
.admin-amazon-page .product-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}
@media (max-width: 576px) {
    .admin-amazon-page .product-checkbox-grid {
        grid-template-columns: 1fr;
    }
}

/* Inputs */
.admin-amazon-page .product-input,
.admin-amazon-page .product-input.form-control {
    height: 42px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 0 12px;
    font-size: 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.admin-amazon-page .product-input:focus,
.admin-amazon-page .product-input.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    outline: none;
}

/* Input Wrapper with Icon */
.admin-amazon-page .product-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.admin-amazon-page .product-input-icon {
    position: absolute;
    left: 12px;
    z-index: 1;
    color: #6b7280;
    pointer-events: none;
}
.admin-amazon-page .product-input-wrapper .product-input,
.admin-amazon-page .product-input-wrapper .select2-container {
    padding-left: 40px;
}

/* Expiry Row (Select + Number Input) */
.admin-amazon-page .product-expiry-row {
    display: flex;
    gap: 12px;
}
.admin-amazon-page .product-expiry-select {
    flex: 1;
    min-width: 0;
}
.admin-amazon-page .product-expiry-days {
    width: 180px;
    flex-shrink: 0;
}
@media (max-width: 576px) {
    .admin-amazon-page .product-expiry-row {
        flex-direction: column;
    }
    .admin-amazon-page .product-expiry-days {
        width: 100%;
    }
}

/* Select2 styling for product inputs */
.admin-amazon-page .product-input.select2-container--default .select2-selection--single,
.admin-amazon-page .product-input.select2-container--default .select2-selection--multiple {
    height: 42px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.admin-amazon-page .product-input.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px;
    padding-left: 12px;
    padding-right: 20px;
}
.admin-amazon-page .product-input-wrapper .product-input.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 40px;
}
.admin-amazon-page .product-input.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}
.admin-amazon-page .product-input.select2-container--default.select2-container--focus .select2-selection--single,
.admin-amazon-page .product-input.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* Checkbox Groups */
.admin-amazon-page .product-checkbox-group {
    margin-bottom: 0;
}

/* Checkbox Wrapper */
.admin-amazon-page .product-checkbox-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    gap: 10px;
}

/* Modern Checkbox Styling */
.admin-amazon-page .product-checkbox-input {
    width: 20px;
    height: 20px;
    margin: 0;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    background-color: #ffffff;
    position: relative;
    flex-shrink: 0;
    transition: all 0.2s ease;
}
.admin-amazon-page .product-checkbox-input:hover {
    border-color: #3b82f6;
}
.admin-amazon-page .product-checkbox-input:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
.admin-amazon-page .product-checkbox-input:checked::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
.admin-amazon-page .product-checkbox-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
.admin-amazon-page .product-checkbox-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Disabled input styling */
.admin-amazon-page .product-input:disabled,
.admin-amazon-page .product-input.product-input-disabled,
.admin-amazon-page .product-input[disabled] {
    background-color: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}
.admin-amazon-page .product-expiry-type-wrapper {
    position: relative;
}
.admin-amazon-page .product-expiry-group .product-expiry-type-wrapper select:disabled {
    background-color: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

/* Checkbox Label */
.admin-amazon-page .product-checkbox-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    line-height: 1.5;
}

/* Hide class support */
.admin-amazon-page .product-settings .hide {
    display: none !important;
}
</style>

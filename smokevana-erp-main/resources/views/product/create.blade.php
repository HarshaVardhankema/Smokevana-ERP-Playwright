@extends('layouts.app')
@section('title', __('product.add_new_product'))

@section('css')
<style>
    /* Match list products page background - same light gray (#EAEDED) as product index */
    .amazon-products-container { background: #EAEDED; min-height: 100vh; padding: 16px 20px 40px; }
    @media (max-width: 768px) { .amazon-products-container { padding: 10px 12px 30px; } }
    .amazon-product-page { background: #EAEDED; padding: 0; min-height: auto; max-width: 100%; overflow-x: hidden; width: 100%; }
    @media (max-width: 768px) { .amazon-product-page { padding: 0; } }
    .amazon-product-header { margin-bottom: 0; }
    .amazon-product-header-banner { background: #232f3e !important; border-radius: 6px; padding: 22px 28px; margin-top: 4px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4); border-bottom: 3px solid #FF9900 !important; }
    .amazon-product-header-content { position: relative; z-index: 2; }
    .amazon-product-header-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 4px; }
    .amazon-product-header-title i { font-size: 22px; color: #FF9900; }
    .amazon-product-header-subtitle { font-size: 13px; color: rgba(249, 250, 251, 0.88); margin: 0; }
    .amazon-product-header-banner .btn-default,
    .amazon-product-header-banner .amazon-add-back-btn { background: #232f3e !important; border: 1px solid #1a2530 !important; color: #fff !important; font-weight: 500; border-radius: 6px; padding: 10px 20px; transition: background 0.2s ease; }
    .amazon-product-header-banner .btn-default:hover,
    .amazon-product-header-banner .amazon-add-back-btn:hover { background: #1a2530 !important; border-color: #232f3e !important; color: #fff !important; }
    .amazon-product-page .amazon-card { border-radius: 12px; box-shadow: 0 2px 12px rgba(15, 17, 17, 0.1); border: 1px solid #D5D9D9; overflow: hidden; background-color: #ffffff; margin-bottom: 20px; max-width: 100%; }
    /* Amazon-style section headers: Basic Information, Description & Media, Warranty & Size, Pricing
       Override global .box-header (amazon-theme.css) so these cards get navy + orange, not light gray */
    .amazon-product-page .amazon-card .tw-p-2,
    .amazon-card .tw-p-2 { padding: 0 !important; }
    .amazon-products-container .amazon-card .box-header,
    .amazon-product-page .amazon-card .box-header,
    .amazon-card.box-primary .box-header,
    div.amazon-card .box-header {
        background: #232f3e !important;
        background-color: #232f3e !important;
        color: #fff !important;
        padding: 16px 24px !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        border: none !important;
        border-bottom: none !important;
        min-height: 52px;
        position: relative;
        border-left: 4px solid #FF9900 !important;
    }
    .amazon-products-container .amazon-card .box-header::after,
    .amazon-product-page .amazon-card .box-header::after,
    .amazon-card .box-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #FF9900, #E47911);
    }
    /* Force white title text (override global .box-title color) */
    .amazon-products-container .amazon-card .box-header .box-title,
    .amazon-product-page .amazon-card .box-header .box-title,
    .amazon-card .box-header .box-title {
        font-size: 17px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .amazon-products-container .amazon-card .box-header .box-title i,
    .amazon-products-container .amazon-card .box-header .box-title .fa,
    .amazon-products-container .amazon-card .box-header .box-title .fas,
    .amazon-product-page .amazon-card .box-header .box-title i,
    .amazon-card .box-header .box-title i,
    .amazon-card .box-header .box-title .fa,
    .amazon-card .box-header .box-title .fas {
        color: #FF9900 !important;
    }
    /* Card content area – same for all cards (Basic Info, Description & Media, Warranty & Size, Pricing) */
    .amazon-product-page .amazon-card .tw-flow-root,
    .amazon-products-container .amazon-card .tw-flow-root {
        padding: 24px 28px !important;
        background: linear-gradient(180deg, #fff 0%, #F7F8F8 100%) !important;
        border-top: 1px solid rgba(213, 217, 217, 0.5) !important;
        min-height: auto;
    }
    /* Remove extra padding from inner content divs to maintain alignment */
    .amazon-product-page .amazon-card .tw-flow-root .tw-py-2,
    .amazon-products-container .amazon-card .tw-flow-root .tw-py-2 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .amazon-product-page .amazon-card .tw-flow-root .sm\:tw-px-5,
    .amazon-products-container .amazon-card .tw-flow-root [class*="sm:tw-px-5"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    @media (max-width: 768px) {
        .amazon-product-page .amazon-card .tw-flow-root,
        .amazon-products-container .amazon-card .tw-flow-root {
            padding: 18px 20px !important;
        }
    }
    /* Ensure consistent card sizing and alignment */
    .amazon-product-page .amazon-card,
    .amazon-products-container .amazon-card {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
        display: block;
    }
    .product-create-card.card-product-images .default-image-preview-wrap img {
        max-width: 100%;
        max-height: 220px;
        object-fit: contain;
    }
    .product-create-card.card-product-images .product-gallery-box {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fafafa;
        padding: 1rem;
        margin-top: 0.5rem;
    }
    .product-create-card.card-product-images .product-gallery-thumbs {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .product-text-card label {
        color: #374151;
        font-weight: 600;
    }

    .product-form-card .product-text-card label {
        color: #374151 !important;
    }
    /* ss2: Basic Information card body – light background, dark labels */
    .card-basic-info .form-group label,
    .card-basic-info .box-body label,
    .card-basic-info .label-inline-tooltip label { color: #374151 !important; font-weight: 600; }
    .card-basic-info .tw-flow-root { background: #f7f8f8 !important; }
    .card-basic-info .box-body { padding: 14px 18px !important; }
    /* Basic Info: compact layout – minimal white space, many fields per line (match reference image) */
    .card-basic-info .basic-info-section { margin-bottom: 12px; padding-bottom: 0; border-bottom: none; }
    .card-basic-info .basic-info-section:last-of-type { margin-bottom: 0; }
    .card-basic-info .basic-info-section-title { font-size: 12px; font-weight: 700; color: #6B7280; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 0.04em; }
    .card-basic-info .form-group { margin-bottom: 0; }
    .card-basic-info .row.gap-row { margin-left: -6px; margin-right: -6px; }
    .card-basic-info .row.gap-row > [class*="col-"] { padding-left: 6px; padding-right: 6px; }
    .card-basic-info .product-type-checkboxes { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-bottom: 0; }
    .card-basic-info .product-type-checkboxes label { margin: 0; font-weight: 600; color: #374151 !important; cursor: pointer; }

    .product-text-card .form-control {
        background-color: #ffffff;
        border-color: #d1d5db;
        color: #111827;
    }
    /* File inputs within navy cards - ensure visibility */
    .product-text-card .file-input,
    .product-text-card .file-caption,
    .product-text-card .file-caption-name,
    .product-text-card .input-group,
    .product-text-card .form-control.file-caption-name {
        background-color: #ffffff !important;
        color: #111827 !important;
        border-color: #d1d5db !important;
    }
    .product-text-card .btn-file,
    .product-text-card .btn-default,
    .product-text-card .fileinput-remove-button,
    .product-text-card .fileinput-upload-button {
        background-color: #FF9900 !important;
        border-color: #E47911 !important;
        color: #ffffff !important;
        font-weight: 600;
    }
    .product-text-card .btn-file:hover,
    .product-text-card .btn-default:hover,
    .product-text-card .fileinput-remove-button:hover,
    .product-text-card .fileinput-upload-button:hover {
        background-color: #E47911 !important;
        border-color: #D2691E !important;
    }
    /* Gallery box */
    .product-text-card .product-gallery-box {
        background-color: #F9FAFB !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 8px;
        padding: 12px;
    }
    .product-text-card .add-gallery-link {
        color: #FF9900 !important;
        font-weight: 600;
    }
    .product-text-card .add-gallery-link:hover {
        color: #E47911 !important;
        text-decoration: underline;
    }
    /* File preview */
    .product-text-card .file-preview,
    .default-image-card .file-preview {
        background-color: #F9FAFB !important;
        border: 1px solid #E5E7EB !important;
    }
    /* Ensure file input buttons are visible and styled */
    .product-text-card .input-group-btn .btn,
    .product-text-card .file-input .btn,
    .product-text-card .kv-file-upload .btn {
        background-color: #FF9900 !important;
        border-color: #E47911 !important;
        color: #ffffff !important;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
    }
    .product-text-card .input-group-btn .btn:hover,
    .product-text-card .file-input .btn:hover,
    .product-text-card .kv-file-upload .btn:hover {
        background-color: #E47911 !important;
        border-color: #D2691E !important;
    }
    /* File caption text should be dark for readability */
    .product-text-card .file-caption-name,
    .product-text-card .file-caption .form-control {
        background-color: #ffffff !important;
        color: #111827 !important;
    }
    .product-create-card.card-product-images .add-gallery-link {
        display: inline-block;
        margin-top: 0.5rem;
        color: #c7511f;
        font-weight: 500;
        font-size: clamp(0.8125rem, 1vw, 0.875rem);
    }
    .product-create-card.card-product-images .add-gallery-link:hover { color: #b14a1c; text-decoration: underline; }

    .file-preview { width: 100% !important; min-height: 200px; }
    .file-preview .file-preview-frame { margin: 0.25rem; }
    .file-preview .file-preview-image { max-height: 200px !important; object-fit: contain !important; }

    /* Product Warranty, Default Image, Product Gallery – no dark background, light card style */
    .product-text-card {
        background: #ffffff !important;
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 12px;
        margin-bottom: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #E5E7EB;
        color: #374151;
    }
    .product-description-box { overflow: hidden; max-width: 100%; box-sizing: border-box; }
    .product-description-box .form-group { max-width: 100%; overflow: hidden; }
    .product-description-box .tox-tinymce, .product-description-box .tox .tox-edit-area, .product-description-box iframe { max-width: 100% !important; box-sizing: border-box !important; }
    .product-description-box .tox .tox-edit-area { height: 320px !important; min-height: 200px; }
    .product-description-box textarea.form-control#product_description,
    .product-description-box textarea.form-control#product_warranty { max-width: 100%; min-height: 200px; height: 320px; resize: vertical; }
    .dimension-cards { display: flex; flex-wrap: wrap; gap: 16px; margin-top: 8px; margin-bottom: 12px; }
    .dimension-card {
        background: #ffffff !important;
        border-radius: 10px;
        padding: 12px 14px;
        min-width: 120px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #E5E7EB;
    }
    .dimension-card-label { font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
    .dimension-card .form-control {
        background: #F9FAFB !important;
        border: 1px solid #D5D9D9 !important;
        color: #111827 !important;
        border-radius: 6px;
    }
    .dimension-card .form-control::placeholder { color: #9CA3AF; }
    .dimension-card .form-control:focus {
        border-color: #0066C0 !important;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(0, 102, 192, 0.15);
    }
    .product-form-page .product-form-card .box-body { padding: 20px 24px; }
    /* Form fields: consistent spacing and Amazon-style hover/focus */
    .product-form-page .product-form-card .form-group,
    .amazon-card .form-group {
        margin-bottom: 18px;
    }
    .product-form-page .product-form-card .form-group:last-child,
    .amazon-card .form-group:last-child {
        margin-bottom: 0;
    }
    .product-form-page .product-form-card label,
    .amazon-card label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #0F1111;
        margin-bottom: 8px;
    }
    .product-form-page .product-form-card .form-control,
    .amazon-card .form-control,
    .product-form-page .product-form-card input[type="text"],
    .product-form-page .product-form-card input[type="number"],
    .product-form-page .product-form-card input[type="email"],
    .product-form-page .product-form-card select,
    .product-form-page .product-form-card textarea {
        display: block;
        width: 100%;
        padding: 10px 14px;
        font-size: 14px;
        line-height: 1.5;
        color: #0F1111;
        background-color: #F7F8F8;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        transition: all 0.2s ease;
        margin-bottom: 0;
    }
    .product-form-page .product-form-card .form-control:hover,
    .amazon-card .form-control:hover {
        background-color: #fff;
        border-color: #B8BDBD;
    }
    .product-form-page .product-form-card .form-control:focus,
    .amazon-card .form-control:focus {
        background-color: #fff;
        border-color: #0066C0;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15);
    }
    .product-form-page .product-form-card .help-block { font-size: 12px; color: #64748b; margin-top: 6px; line-height: 1.4; }
    /* Consistent spacing between form fields in rows */
    .product-form-page .product-form-card .row > [class*="col-"],
    .amazon-card .row > [class*="col-"] {
        margin-bottom: 18px;
    }
    @media (max-width: 768px) {
        .product-form-page .product-form-card .row > [class*="col-"],
        .amazon-card .row > [class*="col-"] {
            margin-bottom: 16px;
        }
    }
    /* Select2 dropdowns: Amazon-style hover/focus */
    .product-form-page .product-form-card .select2-container--default .select2-selection--single,
    .amazon-card .select2-container--default .select2-selection--single {
        background-color: #F7F8F8;
        border: 1px solid #D5D9D9;
        border-radius: 8px;
        min-height: 42px;
        transition: all 0.2s ease;
    }
    .product-form-page .product-form-card .select2-container--default .select2-selection--single:hover,
    .amazon-card .select2-container--default .select2-selection--single:hover {
        background-color: #fff;
        border-color: #B8BDBD;
    }
    .product-form-page .product-form-card .select2-container--default.select2-container--focus .select2-selection--single,
    .amazon-card .select2-container--default.select2-container--focus .select2-selection--single {
        background-color: #fff;
        border-color: #0066C0;
        box-shadow: 0 0 0 3px rgba(0, 102, 192, 0.15);
    }
    .product-form-page .product-form-card .select2-container--default .select2-selection--single .select2-selection__rendered,
    .amazon-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 14px;
        color: #0F1111;
    }
    /* Checkboxes and radio buttons: Amazon-style hover/focus */
    .amazon-card input[type="checkbox"],
    .amazon-card input[type="radio"],
    .product-form-card input[type="checkbox"],
    .product-form-card input[type="radio"] {
        width: auto;
        margin-right: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .amazon-card input[type="checkbox"]:hover,
    .amazon-card input[type="radio"]:hover,
    .product-form-card input[type="checkbox"]:hover,
    .product-form-card input[type="radio"]:hover {
        transform: scale(1.1);
    }
    .amazon-card input[type="checkbox"]:focus,
    .amazon-card input[type="radio"]:focus,
    .product-form-card input[type="checkbox"]:focus,
    .product-form-card input[type="radio"]:focus {
        outline: 2px solid #0066C0;
        outline-offset: 2px;
    }
    /* Buttons: Amazon orange hover effects */
    .amazon-product-page .btn-primary,
    .amazon-product-page .btn-success,
    .amazon-product-page .submit_product_form {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 1px solid #E47911 !important;
        color: #fff !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .amazon-product-page .btn-primary:hover,
    .amazon-product-page .btn-success:hover,
    .amazon-product-page .submit_product_form:hover {
        background: linear-gradient(to bottom, #E47911 0%, #D2691E 100%) !important;
        border-color: #D2691E !important;
        color: #fff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 153, 0, 0.3);
    }
    .amazon-product-page .btn-primary:active,
    .amazon-product-page .btn-success:active,
    .amazon-product-page .submit_product_form:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.2);
    }
    .amazon-product-page { box-sizing: border-box; }
    .amazon-product-page *, .amazon-product-page *::before, .amazon-product-page *::after { box-sizing: border-box; }
    .amazon-product-page .product-form-page .form-control, .amazon-product-page .product-form-page input[type="text"], .amazon-product-page .product-form-page input[type="number"], .amazon-product-page .product-form-page select, .amazon-product-page .product-form-page textarea { max-width: 100%; width: 100%; }
    .amazon-product-page .product-form-card .box-body { padding: clamp(1rem, 2.5vw, 1.5rem) clamp(1.25rem, 3vw, 1.5rem); }
    .amazon-product-page .row { margin-left: -0.5rem; margin-right: -0.5rem; }
    .amazon-product-page .row > [class*="col-"] { padding-left: 0.5rem; padding-right: 0.5rem; }
    .amazon-product-page .form-group { margin-bottom: clamp(0.75rem, 1.5vw, 1rem); }
    .product-form-card .card-subtitle {
        font-size: 13px;
        color: #565959;
        margin: 0 0 18px 0;
        line-height: 1.45;
    }
    .amazon-product-page .price-tiers-banner {
        background: linear-gradient(135deg, #fff8e7 0%, #ffedd5 100%) !important;
        border: 1px solid #FF9900 !important;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 16px;
    }
    .amazon-product-page .price-tiers-banner strong,
    .amazon-product-page .price-tiers-banner i { color: #B45309 !important; }
    /* Product type & variations (inside Pricing card) – Amazon accent */
    .product-form-page .product-type-section {
        background: #F7F8F8;
        border: 1px solid #D5D9D9;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 16px;
        border-left: 4px solid #FF9900;
    }
    .product-form-page .product-type-section .section-label {
        font-size: 13px;
        font-weight: 600;
        color: #0F1111;
        margin-bottom: 12px;
        display: block;
    }
    .product-form-page #product_form_part {
        max-width: 100%;
        overflow-x: auto;
    }
    /* Contain form and cards – prevent overflow into sidebar (match edit page layout) */
    .amazon-product-page #product_add_form,
    .amazon-product-page .product_form { max-width: 100%; overflow-x: hidden; min-width: 0; }
    .product-form-page .add-product-price-table,
    .product-form-page .table-responsive {
        max-width: 100%;
    }
    .product-form-page .add-product-price-table input.form-control {
        min-width: 0;
    }
    /* Action buttons – alignment and gap between buttons */
    .amazon-product-page .btn-group .btn {
        margin: 0;
    }
    @media (max-width: 768px) {
        .amazon-product-page .btn-group {
            flex-direction: column;
        }
        .amazon-product-page .btn-group .btn {
            margin: 0.25rem 0;
            width: 100%;
        }
    }

    /* Stepper - match image: active orange, inactive light grey with dark text */
    .amazon-products-container .product-edit-stepper,
    .product-edit-stepper {
        display: flex !important;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
        padding: 16px 20px !important;
        background: #F7F8F8 !important;
        border-radius: 10px;
        border: 1px solid #D5D9D9 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    .amazon-products-container .product-edit-stepper .step,
    .product-edit-stepper .step {
        display: inline-flex;
        align-items: center;
        padding: 10px 18px !important;
        border-radius: 6px !important;
        font-size: 14px !important;
        font-weight: 600;
        background: #E5E7EB !important;
        color: #374151 !important;
        border: 1px solid #D1D5DB !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .amazon-products-container .product-edit-stepper .step.active,
    .product-edit-stepper .step.active {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        color: #ffffff !important;
        border: 1px solid #E47911 !important;
        box-shadow: 0 2px 6px rgba(255, 153, 0, 0.35);
    }
    .product-edit-stepper .step .step-num {
        margin-right: 6px;
        font-weight: 700;
    }
    .amazon-products-container .product-edit-stepper .step:hover:not(.active),
    .product-edit-stepper .step:hover:not(.active) {
        background: #D1D5DB !important;
        border-color: #9CA3AF !important;
        color: #0F1111 !important;
        transform: translateY(-1px);
    }
    .product-form-card .card-subtitle,
    .amazon-card .card-subtitle {
        font-size: 13px;
        color: #565959;
        margin: 0 0 20px 0;
        line-height: 1.5;
        display: block;
    }
    /* Keep tooltip icon inline with label – compact to match reference */
    .label-inline-tooltip { display: inline-flex; align-items: center; gap: 4px; flex-wrap: nowrap; margin-bottom: 4px; min-height: 20px; }
    .label-inline-tooltip label { display: inline; margin-bottom: 0; }

    /* ===== Basic Information: responsive grid (1 / 2 / 3 columns) ===== */
    .card-basic-info .tw-flow-root .row,
    .amazon-card .tw-flow-root .row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -6px;
        margin-right: -6px;
    }
    .card-basic-info .tw-flow-root .row > [class*="col-"],
    .amazon-card .tw-flow-root .row > [class*="col-"] {
        padding-left: 6px;
        padding-right: 6px;
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
    }
    /* Responsive grid: compact – less white space, many fields per line */
    .card-basic-info .row.basic-info-grid {
        display: flex;
        flex-wrap: wrap;
        margin-left: -6px;
        margin-right: -6px;
    }
    .card-basic-info .row.basic-info-grid > .basic-info-col {
        padding-left: 6px;
        padding-right: 6px;
        margin-bottom: 10px;
        flex: 0 0 100%;
        max-width: 100%;
    }
    .card-basic-info .row.basic-info-grid > .basic-info-col--full {
        flex: 0 0 100%;
        max-width: 100%;
    }
    @media (min-width: 768px) {
        .card-basic-info .row.basic-info-grid > .basic-info-col:not(.basic-info-col--full) {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (min-width: 1024px) {
        .card-basic-info .row.basic-info-grid > .basic-info-col:not(.basic-info-col--full) {
            flex: 0 0 33.33333333%;
            max-width: 33.33333333%;
        }
    }
    @media (max-width: 767px) {
        .card-basic-info .row.basic-info-grid > .basic-info-col {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 10px;
        }
    }
    .card-basic-info .form-group,
    .amazon-card .card-basic-info .form-group {
        margin-bottom: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .card-basic-info .form-group label,
    .card-basic-info label[for],
    .amazon-card .card-basic-info label {
        text-align: left !important;
        margin-bottom: 4px !important;
        padding: 0 !important;
        font-weight: 600;
        font-size: 13px;
        color: #0F1111;
        display: block;
        min-height: 20px;
    }
    .card-basic-info .form-group > .label-inline-tooltip { margin-bottom: 4px !important; }
    .card-basic-info .form-control,
    .card-basic-info select.form-control,
    .card-basic-info input.form-control,
    .card-basic-info textarea.form-control,
    .amazon-card .card-basic-info .form-control {
        width: 100% !important;
        min-height: 36px;
        padding: 6px 10px !important;
        border-radius: 6px !important;
        border: 1px solid #D5D9D9 !important;
        margin: 0 !important;
        box-sizing: border-box;
        font-size: 13px;
    }
    .card-basic-info textarea.form-control {
        min-height: 80px;
        resize: vertical;
    }
    .card-basic-info .select2-container,
    .amazon-card .card-basic-info .select2-container {
        width: 100% !important;
    }
    .card-basic-info .select2-container--default .select2-selection--single,
    .amazon-card .card-basic-info .select2-container--default .select2-selection--single {
        min-height: 36px !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        border: 1px solid #D5D9D9 !important;
    }
    .card-basic-info .select2-container--default .select2-selection__rendered,
    .amazon-card .card-basic-info .select2-container--default .select2-selection__rendered {
        line-height: 22px !important;
        padding-left: 0 !important;
    }
    .card-basic-info .input-group .form-control,
    .card-basic-info .input-group select {
        border-radius: 6px 0 0 6px !important;
        min-height: 36px;
    }
    .card-basic-info .input-group-btn .btn,
    .card-basic-info .input-group .input-group-btn .btn {
        border-radius: 0 6px 6px 0;
        min-height: 36px;
        padding: 6px 10px;
    }
    /* Action buttons: centered, even gap between each button */
    .amazon-product-page .btn-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 20px;
        padding: 24px 0 8px;
        margin-top: 8px;
        border-top: 1px solid #E5E7EB;
    }
    .amazon-product-page .btn-group .btn,
    .amazon-product-page .btn-group .submit_product_form {
        margin: 0 !important;
        padding: 12px 28px !important;
        border-radius: 6px !important;
        font-size: 15px;
        min-height: 44px;
    }
    /* Responsive: stack fields, labels above inputs */
    @media (max-width: 768px) {
        .card-basic-info .tw-flow-root .row > [class*="col-"],
        .amazon-card .tw-flow-root .row > [class*="col-"] {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 18px;
        }
        .card-basic-info .form-group label,
        .amazon-card .card-basic-info label {
            margin-bottom: 6px !important;
        }
        .amazon-product-page .btn-group {
            flex-direction: column;
            padding: 20px 0;
        }
        .amazon-product-page .btn-group .btn,
        .amazon-product-page .btn-group .submit_product_form {
            width: 100%;
        }
    }

    /* Amazon-inspired UI: clean sans-serif, consistent form and buttons */
    .amazon-products-container,
    .amazon-product-page {
        font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif !important;
    }
    .amazon-product-page .product-form-page .form-control,
    .amazon-product-page .product-form-page input[type="text"],
    .amazon-product-page .product-form-page input[type="number"],
    .amazon-product-page .product-form-page input[type="email"],
    .amazon-product-page .product-form-page select,
    .amazon-product-page .product-form-page textarea {
        border: 1px solid #D5D9D9 !important;
        border-radius: 6px !important;
        padding: 10px 14px !important;
        margin: 10px 0 10px 0 !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .amazon-product-page .product-form-page .form-control:focus,
    .amazon-product-page .product-form-page input:focus,
    .amazon-product-page .product-form-page select:focus,
    .amazon-product-page .product-form-page textarea:focus {
        border-color: #FF9900 !important;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.2) !important;
        outline: none !important;
    }
    /* Prominent button section: Amazon yellow for Save and Add Another */
    .amazon-product-page .btn-group .submit_product_form,
    .amazon-product-page .btn-group .tw-dw-btn-primary.submit_product_form,
    .amazon-product-page .btn-group .bg-maroon.submit_product_form,
    .amazon-product-page .btn-group .tw-dw-btn-warning.submit_product_form {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border: 1px solid #E47911 !important;
        color: #ffffff !important;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }
    .amazon-product-page .btn-group .submit_product_form:hover,
    .amazon-product-page .btn-group .tw-dw-btn-primary.submit_product_form:hover,
    .amazon-product-page .btn-group .bg-maroon.submit_product_form:hover,
    .amazon-product-page .btn-group .tw-dw-btn-warning.submit_product_form:hover {
        background: linear-gradient(to bottom, #f78c00 0%, #E47911 100%) !important;
        border-color: #D2691E !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 153, 0, 0.35);
    }
    .amazon-product-page .btn-group .submit_product_form:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.2);
    }
    /* Add product page: ensure button row alignment and gap */
    .add-product-btn-wrap .btn-group.add-product-actions {
        display: flex !important;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 20px !important;
    }
    .add-product-btn-wrap .btn-group.add-product-actions .btn {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    /* Add product image / file upload area – no dark background */
    .amazon-product-page .default-image-card,
    .amazon-product-page .product-text-card.default-image-card {
        background: #ffffff !important;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 20px;
    }
    .amazon-product-page .product-text-card .btn-file,
    .amazon-product-page .product-text-card .input-group-btn .btn,
    .amazon-product-page .product-text-card label.btn {
        background: #FF9900 !important;
        color: #fff !important;
        border-radius: 6px;
        padding: 10px 20px;
        transition: background 0.3s ease;
    }
    .amazon-product-page .product-text-card .btn-file:hover,
    .amazon-product-page .product-text-card .input-group-btn .btn:hover {
        background: #f78c00 !important;
        color: #fff !important;
    }
    /* Price section styling */
    .amazon-product-page .price-tiers-banner,
    .amazon-product-page .product-type-section {
        border-radius: 8px;
        padding: 16px 20px;
        margin-top: 16px;
        margin-bottom: 16px;
        border: 1px solid #D5D9D9;
        transition: box-shadow 0.2s ease;
    }
    .amazon-product-page .price-tiers-banner:focus-within,
    .amazon-product-page .product-type-section:focus-within {
        box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
    }
</style>
@endsection

@section('content')
<div class="amazon-products-container">
    <!-- Content Header (Page header) -->
    <section class="content-header amazon-product-header">
        <div class="amazon-product-header-banner">
            <div class="amazon-product-header-content">
                <h1 class="amazon-product-header-title">
                    <i class="fas fa-box-open"></i>
                    @lang('product.add_new_product')
                </h1>
                <p class="amazon-product-header-subtitle">
                    Fill in the product details below to add a new product to your inventory.
                </p>
            </div>
            <a href="{{ action([\App\Http\Controllers\ProductController::class, 'index']) }}" class="btn btn-default amazon-add-back-btn">
                <i class="fas fa-arrow-left"></i> @lang('messages.back')
            </a>
        </div>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->

        <!-- Gallery Image Preview Modal -->
        <div class="modal fade" id="galleryImagePreviewModal" tabindex="-1" role="dialog"
            aria-labelledby="galleryImagePreviewLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="max-width:900px;">
                <div class="modal-content">
                    <div class="modal-header" style="background:#6c63ff;">
                        <h5 class="modal-title" id="galleryImagePreviewLabel" style="color:#fff;">Detailed Preview <span
                                id="galleryImageName" style="color:#e0e0e0;font-size:14px;"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" style="background:#fff;">
                        <img id="galleryImagePreview" src="" alt="Gallery Preview"
                            style="max-width:100%;max-height:70vh;box-shadow:0 2px 8px #0002;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Edit Gallery Modal -->
    <div class="modal fade" id="editGalleryModal" tabindex="-1" role="dialog" aria-labelledby="editGalleryModalLabel">
        <style>
            .product-gallery-box {
                margin-bottom: 15px;
            }

            .product-gallery-thumbs {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 10px;
            }

            .product-gallery-thumb {
                position: relative;
                width: 100px;
                height: 100px;
                border: 1px solid #ddd;
                border-radius: 4px;
                overflow: hidden;
            }

            .product-gallery-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .gallery-thumb-actions {
                position: absolute;
                top: 0;
                right: 0;
                display: flex;
                gap: 5px;
                padding: 5px;
                background: rgba(0, 0, 0, 0.5);
                opacity: 0;
                transition: opacity 0.2s;
            }

            .product-gallery-thumb:hover .gallery-thumb-actions {
                opacity: 1;
            }

            .gallery-thumb-actions button {
                background: none;
                border: none;
                color: white;
                padding: 2px 5px;
                cursor: pointer;
                margin-left: 10px;
                font-size: 14px;
            }

            .gallery-thumb-actions button:hover {
                color: #ddd;
            }

            .add-gallery-link {
                display: inline-block;
                margin-top: 10px;
                color: #3c8dbc;
                text-decoration: none;
            }

            .add-gallery-link:hover {
                text-decoration: underline;
            }
        </style>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editGalleryModalLabel">View Gallery Image</h4>
                </div>
                <form id="editGalleryForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <img id="editGalleryImage" src="" alt="Gallery Image" class="img-responsive"
                                style="max-height: 200px; margin: 0 auto;">
                        </div>
                        {{-- <div class="form-group">
                      <label for="editGalleryAlt">@lang('Image Alt Text')</label>
                      <input type="text" class="form-control" id="editGalleryAlt" name="alt_text">
                  </div>
                  <input type="hidden" id="editGalleryIndex" name="index"> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                        {{-- <button type="submit" class="btn btn-primary">@lang('Save Changes')</button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content amazon-product-page">
        @php
            $form_class = empty($duplicate_product) ? 'create' : '';
            $is_image_required = !empty($common_settings['is_product_image_required']);
        @endphp
        {!! Form::open([
            'url' => action([\App\Http\Controllers\ProductController::class, 'store']),
            'method' => 'post',
            'id' => 'product_add_form',
            'class' => 'product_form product-form-page ' . $form_class,
            'files' => true,
        ]) !!}
        {{-- Stepper --}}
        <div class="product-edit-stepper">
            <span class="step active" data-section="section-basic-info"><span class="step-num">1</span> Basic Info</span>
            <span class="step" data-section="section-media"><span class="step-num">2</span> Media</span>
            <span class="step" data-section="section-pricing"><span class="step-num">3</span> Pricing</span>
            <span class="step" data-section="section-variants"><span class="step-num">4</span> Variants</span>
        </div>

        {{-- Card 1: Basic Information (ss2 layout) --}}
        @component('components.widget', [
            'class' => 'box-primary amazon-card product-form-card card-basic-info',
            'id' => 'section-basic-info',
            'title' => 'Basic Information',
            'title_svg' => '<i class="fas fa-check-circle" style="margin-right:6px; color:#FF9900;"></i>',
        ])
            <p class="card-subtitle">Fill in the product details below to add a new product to your inventory.</p>
            {{-- Gift card options: only visible when Gift card checkbox is checked --}}
            <div class="row" id="gift_card_options_row" style="display: none; margin-bottom: 16px; padding: 12px 16px; background: #F7F8F8; border-radius: 8px; border: 1px solid #D5D9D9;">
                <div class="col-md-12"><strong style="margin-bottom: 8px; display: block;">Gift card options</strong></div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('gift_card_expires_at', __('Gift card expiry date') . ':') !!}
                        {!! Form::date('gift_card_expires_at', optional($duplicate_product)->gift_card_expires_at, [
                            'class' => 'form-control',
                            'placeholder' => __('Select date'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('gift_card_stock', __('Gift card stock') . ':') !!}
                        {!! Form::number('gift_card_stock', optional($duplicate_product)->gift_card_stock, [
                            'class' => 'form-control input_number',
                            'placeholder' => __('Quantity'),
                            'min' => '0',
                            'step' => '1',
                        ]) !!}
                    </div>
                </div>
            </div>
            {{-- Section 1: Core Product Info --}}
            <div class="basic-info-section">
                <h4 class="basic-info-section-title"></h4>
                <div class="row gap-row basic-info-grid">
                    <div class="col-xs-12 basic-info-col basic-info-col--full">
                        <div class="form-group">
                            {!! Form::label('name', __('product.product_name') . ':*') !!}
                            {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, [
                                'class' => 'form-control prevent-select',
                                'required',
                                'placeholder' => __('product.product_name'),
                            ]) !!}
                        </div>
                    </div>
                </div>
                {{-- Section 2: Product Type (under Product Name) --}}
                <div class="form-group">
                    <span class="basic-info-section-title" style="margin-bottom: 10px; display: block;">Product type</span>
                    <div class="product-type-checkboxes">
                        <label>
                            {!! Form::checkbox('is_tobacco_product', 1, false, [
                                'class' => 'input-icheck',
                                'id' => 'is_tobacco_product',
                            ]) !!} Tobacco product
                        </label>
                        <label>
                            {!! Form::checkbox('enable_selling', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, [
                                'class' => 'input-icheck',
                                'id' => 'enable_selling',
                            ]) !!} Ecom
                        </label>
                        <label>
                            {!! Form::checkbox('is_gift_card', 1, !empty($duplicate_product) ? $duplicate_product->is_gift_card : false, [
                                'class' => 'input-icheck',
                                'id' => 'is_gift_card',
                            ]) !!} Gift card
                        </label>
                    </div>
                </div>
                <div class="row gap-row basic-info-grid">
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            <span class="label-inline-tooltip">{!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))</span>
                            {!! Form::text('sku', null, [
                                'class' => 'form-control',
                                'placeholder' => __('product.sku'),
                                'pattern' => '^[a-zA-Z0-9@-]+$',
                                'title' => 'Only letters, numbers, @ and - are allowed as special characters',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('ct', __('product.ct') . ':') !!}
                            {!! Form::number('ct', null, ['class' => 'form-control', 'placeholder' => __('Fill if it needed')]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col @if (!session('business.enable_brand')) hide @endif">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('product.brand') . ':') !!}
                            <div class="input-group">
                                {!! Form::select(
                                    'brand_id',
                                    $brands,
                                    !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id : null,
                                    ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'],
                                ) !!}
                                <span class="input-group-btn">
                                    <button type="button" @if (!auth()->user()->can('brand.create')) disabled @endif
                                        class="btn btn-default bg-white btn-flat btn-modal"
                                        data-href="{{ action([\App\Http\Controllers\BrandController::class, 'create'], ['quick_add' => true]) }}"
                                        title="@lang('brand.add_brand')" data-container=".view_modal"><i
                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('productVisibility', __('product.productVisibility') . ':') !!}
                            {!! Form::select('productVisibility', $productVisibility, null, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <style>
                            .select2-selection__choice:has(.locked-option) {
                                background-color: #474747 !important;
                                border-color: #474747 !important;
                                pointer-events: none;
                                opacity: 0.6;
                                padding-right: 6px;
                            }
                        </style>
                        <div class="form-group">
                            {!! Form::label('category_id', __('product.category') . ':') !!}
                            {!! Form::select('category_id', $categories, null, [
                                'placeholder' => __('messages.please_select'),
                                'class' => 'form-control select2',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                            {!! Form::select('sub_category_id', $sub_categories, null, [
                                'placeholder' => __('messages.please_select'),
                                'class' => 'form-control select2',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('custom_sub_categories', __('Web categories') . ':') !!}
                            {!! Form::select('custom_sub_categories[]', $catList, null, [
                                'class' => 'form-control select2',
                                'id' => 'custom_sub_categories',
                                'multiple' => 'multiple',
                                'placeholder' => __('Other Categories'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('maxSaleLimit', __('product.maxSaleLimit') . ':') !!}
                            {!! Form::number('maxSaleLimit', null, ['class' => 'form-control', 'placeholder' => __('max Sale Limit')]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col @if (!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
                        <div class="form-group">
                            <span class="label-inline-tooltip">{!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity'))</span>
                            {!! Form::text(
                                'alert_quantity',
                                !empty($duplicate_product->alert_quantity) ? @format_quantity($duplicate_product->alert_quantity) : null,
                                ['class' => 'form-control input_number', 'placeholder' => __('product.alert_quantity'), 'min' => '0'],
                            ) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('ml', __('product.ml') . ':') !!}
                            {!! Form::number('ml', null, ['class' => 'form-control', 'placeholder' => __('fill ml if needed')]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('locationTaxType', __('product.locationTaxType') . ':') !!}
                            {!! Form::select('locationTaxType[]', $taxTypes, null, [
                                'class' => 'form-control select2',
                                'placeholder' => __('Select location tax types'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('state_check', __('State Restriction') . ':') !!}
                            {!! Form::select(
                                'state_check',
                                [
                                    'all' => 'All States (No Restriction)',
                                    'in' => 'Only These States',
                                    'not_in' => 'Exclude These States',
                                ],
                                'all',
                                ['class' => 'form-control select2', 'id' => 'state_check'],
                            ) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Business & Restrictions (same grid, 3 per row) --}}
            @php
                $default_location = null;
                if (count($business_locations) == 1) {
                    $default_location = array_key_first($business_locations->toArray());
                }
            @endphp
            <div class="basic-info-section">
                <h4 class="basic-info-section-title">Business & Restrictions</h4>
                <div class="row gap-row basic-info-grid">
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group">
                            {!! Form::label('product_source', __('Product Source') . ':') !!}
                            {!! Form::select('product_source', [
                                'in_house' => 'In House',
                                'out_source' => 'Out Source'
                            ], !empty($duplicate_product->product_source) ? $duplicate_product->product_source : 'in_house', [
                                'class' => 'form-control select2',
                                'id' => 'product_source'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col @if (count($business_locations) > 1) @else hide @endif">
                        <div class="form-group">
                            <span class="label-inline-tooltip">{!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))</span>
                            {!! Form::select('product_locations[]', $business_locations, $default_location, [
                                'class' => 'form-control select',
                                'id' => 'product_locations',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col">
                        <div class="form-group" id="manage_stock_container" style="display: none;">
                            <label>
                                {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, [
                                    'class' => 'input-icheck',
                                    'id' => 'enable_stock',
                                ]) !!} <strong>@lang('product.manage_stock')</strong>
                            </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
                        </div>
                    </div>
                    <div class="col-xs-12 basic-info-col" id="states_selection_div" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('states', __('Select States') . ':') !!}
                            <div style="min-width:100%;max-width:100%">
                                {!! Form::select('states[]', config('us_states'), null, [
                                    'class' => 'form-control select2',
                                    'id' => 'states',
                                    'multiple' => 'multiple',
                                    'placeholder' => 'Select states...',
                                    'style' => 'min-width:100%;max-width:100%;',
                                ]) !!}
                            </div>
                            <small class="help-block">
                                <span id="state_help_text"></span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hidden / optional fields (unchanged logic) --}}
            <div class="hide">
                <div class="form-group">
                    {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
                    {!! Form::select(
                        'barcode_type',
                        $barcode_types,
                        !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default,
                        ['class' => 'form-control select2', 'required'],
                    ) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                    <div class="input-group">
                        {!! Form::select(
                            'unit_id',
                            $units,
                            !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'),
                            ['class' => 'form-control select2', 'required'],
                        ) !!}
                        <span class="input-group-btn">
                            <button type="button" @if (!auth()->user()->can('unit.create')) disabled @endif
                                class="btn btn-default bg-white btn-flat btn-modal"
                                data-href="{{ action([\App\Http\Controllers\UnitController::class, 'create'], ['quick_add' => true]) }}"
                                title="@lang('unit.add_unit')" data-container=".view_modal"><i
                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            @if (session('business.enable_sub_units'))
                <div class="basic-info-section hide">
                    <div class="form-group">
                        {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))
                        {!! Form::select(
                            'sub_unit_ids[]',
                            [],
                            !empty($duplicate_product->sub_unit_ids) ? $duplicate_product->sub_unit_ids : null,
                            ['class' => 'form-control select2', 'multiple', 'id' => 'sub_unit_ids'],
                        ) !!}
                    </div>
                </div>
            @endif
            @if (!empty($common_settings['enable_secondary_unit']))
                <div class="form-group hide">
                    {!! Form::label('secondary_unit_id', __('lang_v1.secondary_unit') . ':') !!} @show_tooltip(__('lang_v1.secondary_unit_help'))
                    {!! Form::select(
                        'secondary_unit_id',
                        $units,
                        !empty($duplicate_product->secondary_unit_id) ? $duplicate_product->secondary_unit_id : null,
                        ['class' => 'form-control select2'],
                    ) !!}
                </div>
            @endif
            @if (!empty($common_settings['enable_product_warranty']))
                <div class="basic-info-section">
                    <div class="row gap-row">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!}
                                {!! Form::select('warranty_id', $warranties, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('messages.please_select'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (!empty($pos_module_data))
                <div class="basic-info-section">
                    @foreach ($pos_module_data as $key => $value)
                        @if (!empty($value['view_path']))
                            @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                        @endif
                    @endforeach
                </div>
            @endif

        @endcomponent

        {{-- Card 2: Product Details & Pricing (Description & Media + Warranty & Size + Pricing in one card) --}}
        @component('components.widget', [
            'class' => 'box-primary amazon-card product-form-card',
            'id' => 'section-media',
            'title' => 'Product Details & Pricing',
            'title_svg' => '<i class="fas fa-check-circle" style="margin-right:6px; color:#FF9900;"></i>',
        ])
            <p class="card-subtitle">Description, media, warranty, dimensions and pricing.</p>
            {{-- Section: Description & Media --}}
            <div class="tw-flow-root-section" style="margin-bottom: 28px;">
                <h4 class="tw-section-title" style="font-size: 15px; font-weight: 700; color: #232f3e; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #FF9900;"><i class="fas fa-image" style="margin-right: 6px; color: #FF9900;"></i>Description & Media</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="product-text-card product-description-box">
                        <div class="form-group mb-0">
                            {!! Form::label('product_warranty', __('lang_v1.product_warranty') . ':') !!}
                            {!! Form::textarea(
                                'product_warranty',
                                !empty($duplicate_product->product_warranty) ? $duplicate_product->product_warranty : null,
                                ['class' => 'form-control', 'id' => 'product_warranty'],
                            ) !!}
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 16px;">
                        {!! Form::label('product_brochure', __('lang_v1.product_brochure') . ':') !!}
                        {!! Form::file('product_brochure', [
                            'id' => 'product_brochure',
                            'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                        ]) !!}
                        <small>
                            <p class="help-block mb-2">
                                @lang('lang_v1.previous_file_will_be_replaced')<br>
                                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                                @includeIf('components.document_help_text')
                            </p>
                        </small>
                    </div>
                </div>
                <div class="col-md-6 product-card1-images">
                    <div class="product-text-card default-image-card ss2-default-image-card">
                        <div class="form-group mb-0">
                            <strong class="d-block mb-2" style="font-size:15px; color:#374151;"><i class="fas fa-check-circle" style="color:#FF9900; margin-right:6px;"></i>Default Product Image</strong>
                            {!! Form::label('image', __('Default Product Image') . ':') !!}
                            {!! Form::file('image', [
                                'id' => 'upload_image',
                                'accept' => 'image/*',
                                'required' => $is_image_required,
                                'class' => 'upload-element',
                            ]) !!}
                            <small class="help-block text-muted" style="color: #6B7280;">
                                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000]) &middot; @lang('lang_v1.aspect_ratio_should_be_1_1')
                            </small>
                        </div>
                    </div>
                    <style>
                        /* Keep Remove/Browse and caption inside the box — no overflow */
                        .product-card1-images .form-group,
                        .product-card1-images .file-input {
                            max-width: 100%;
                            overflow: hidden;
                        }
                        .product-card1-images .file-caption {
                            max-width: 100%;
                            overflow: hidden;
                            display: flex;
                            flex-wrap: wrap;
                            align-items: center;
                            gap: 8px;
                        }
                        .product-card1-images .file-caption-name {
                            min-width: 0;
                            max-width: 50%;
                            flex: 1 1 auto;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                        .product-card1-images .file-caption .form-control,
                        .product-card1-images .file-input .file-caption .form-control {
                            max-width: 50%;
                            min-width: 0;
                        }
                        .product-card1-images .file-input .input-group {
                            max-width: 100%;
                            flex-wrap: wrap;
                        }
                        .product-card1-images .file-input .btn {
                            flex-shrink: 0;
                        }
                        /* Default image: show full image inside box (contain), no cropping */
                        .product-card1-images .file-preview {
                            width: 100%;
                            display: block !important;
                            min-height: 160px;
                            height: auto;
                            overflow: visible;
                            border: 1px solid #e2e8f0;
                            border-radius: 6px;
                            padding: 8px;
                            background: #f8fafc;
                        }

                        .product-card1-images .file-preview .kv-file-content {
                            min-height: 160px;
                            height: auto;
                            overflow: visible;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }

                        .product-card1-images .file-preview .file-preview-image,
                        .product-card1-images .file-preview .kv-preview-data,
                        .product-card1-images .file-preview img {
                            max-height: 200px !important;
                            max-width: 100% !important;
                            width: auto !important;
                            height: auto !important;
                            object-fit: contain;
                            display: block;
                            margin: 0 auto;
                        }

                        .product-card1-images .krajee-default.file-preview-frame {
                            top: 0;
                        }

                        /* Typography for Default Image / Gallery – light card */
                        .product-card1-images .form-group label,
                        .product-card1-images .product-text-card label {
                            font-weight: 600;
                            font-size: 13px;
                            color: #374151 !important;
                            margin-bottom: 8px;
                        }

                        .product-card1-images .help-block,
                        .product-card1-images .product-text-card .help-block {
                            font-size: 12px;
                            line-height: 1.4;
                            color: #6B7280 !important;
                            margin: 8px 0 0;
                        }

                        .product-card1-images .file-footer-caption {
                            font-size: 12px;
                            color: #475569;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            max-width: 100%;
                            display: block;
                        }
                        .product-card1-images .file-caption-name {
                            font-size: 12px;
                            color: #475569;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            max-width: 50%;
                            display: block;
                        }

                        .product-card1-images .add-gallery-link {
                            font-size: 13px;
                            font-weight: 500;
                        }

                        .product-gallery-box {
                            border: 1px solid #e2e2e2;
                            padding: 10px;
                            border-radius: 4px;
                            background: #fafbfc;
                            margin-bottom: 10px;
                        }

                        .product-gallery-thumbs {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 8px;
                            margin-bottom: 8px;
                        }

                        .product-gallery-thumb {
                            position: relative;
                            width: 70px;
                            height: 70px;
                            border: 1px solid #ddd;
                            border-radius: 3px;
                            overflow: hidden;
                            background: #fff;
                        }

                        .product-gallery-thumb img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }

                        .remove-gallery-thumb {
                            /* position: absolute;
                            top: 12px;
                            right: 12px; */
                            background: rgba(0, 0, 0, 0.6);
                            color: #fff;
                            border: none;
                            border-radius: 50%;
                            /* width: 18px; */
                            /* height: 18px; */
                            /* font-size: 5px !important; */
                            /* line-height: 16px; */
                            text-align: center;
                            cursor: pointer;
                            z-index: 2;
                        }

                        .remove-gallery-thumb .fa-times {
                            font-size: 13px !important;
                        }

                        .add-gallery-link {
                            color: #0073aa;
                            text-decoration: underline;
                            cursor: pointer;
                            font-size: 14px;
                        }

                        .add-gallery-link:hover {
                            color: #005177;
                        }

                        #galleryImagePreviewModal .modal-header {
                            background: #6c63ff;
                            color: #fff;
                            border-bottom: none;
                        }

                        #galleryImagePreviewModal .modal-title {
                            font-size: 22px;
                            font-weight: 600;
                        }

                        #galleryImagePreviewModal .close {
                            color: #fff;
                            opacity: 1;
                        }
                    </style>
                    <div class="product-text-card product-gallery-card" style="margin-top: 16px;">
                        <div class="form-group mb-0">
                            <label>@lang('Product gallery')</label>
                            <div id="product-gallery-box" class="product-gallery-box">
                                <div id="product-gallery-thumbs" class="product-gallery-thumbs"></div>
                                <a href="#" id="add-gallery-images" class="add-gallery-link">@lang('Add product gallery images')</a>
                                <input type="file" id="gallery_images" name="gallery[]" accept="image/*" multiple
                                    style="display:none;">
                            </div>
                            <small>
                                <p class="help-block" style="color: #6B7280; margin-top: 8px;">
                                    @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])<br>
                                    @lang('lang_v1.aspect_ratio_should_be_1_1')
                                </p>
                            </small>
                        </div>
                    </div>
                    </div></div></div>
            </div>
            </div>
            {{-- Section: Warranty & Size --}}
            <div class="tw-flow-root-section" style="margin-bottom: 28px;">
                <h4 class="tw-section-title" style="font-size: 15px; font-weight: 700; color: #232f3e; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #FF9900;"><i class="fas fa-ruler-combined" style="margin-right: 6px; color: #FF9900;"></i>Warranty & Size</h4>
            <div class="row">
                <div class="col-sm-12">
                    <div class="product-text-card">
                        <div class="form-group mb-0">
                            {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                            {!! Form::textarea(
                                'product_description',
                                !empty($duplicate_product->product_description) ? $duplicate_product->product_description : null,
                                ['class' => 'form-control', 'id' => 'product_description'],
                            ) !!}
                        </div>
                    </div>

                    <div class="product-text-card">
                        {{-- Product dimensions (L × W × H) for shipping/carton size --}}
                        <div class="form-group" style="margin-top: 2px;">
                            {!! Form::label('dimensions', 'Size (L × W × H)') !!}
                            <div class="dimension-cards">
                                <div class="dimension-card">
                                    <div class="dimension-card-label">Length (L)</div>
                                    {!! Form::number('length', !empty($duplicate_product->length) ? $duplicate_product->length : null, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'inches',
                                    ]) !!}
                                </div>

                                <div class="dimension-card">
                                    <div class="dimension-card-label">Width (W)</div>
                                    {!! Form::number('width', !empty($duplicate_product->width) ? $duplicate_product->width : null, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'inches',
                                    ]) !!}
                                </div>

                                <div class="dimension-card">
                                    <div class="dimension-card-label">Height (H)</div>
                                    {!! Form::number('height', !empty($duplicate_product->height) ? $duplicate_product->height : null, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'inches',
                                    ]) !!}
                                </div>

                                <div class="dimension-card">
                                    <div class="dimension-card-label">Weight (Lbs)</div>
                                    {!! Form::number('weight', !empty($duplicate_product->weight) ? $duplicate_product->weight : null, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'Weight in pounds (Lbs)',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            {{-- Section: Pricing & Variants --}}
            <div class="tw-flow-root-section" id="section-pricing" style="margin-bottom: 0;">
                <h4 class="tw-section-title" style="font-size: 15px; font-weight: 700; color: #232f3e; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #FF9900;"><i class="fas fa-dollar-sign" style="margin-right: 6px; color: #FF9900;"></i>Pricing & Variants</h4>
            <div class="row">
                <div class="col-sm-12">
                    <div class="price-tiers-banner" style="background: linear-gradient(135deg, #FF9900 0%, #E47911 100%); border: 1px solid #E47911; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px;">
                        <strong style="color: #ffffff;"><i class="fas fa-tags" style="margin-right:6px;"></i>Price Tiers</strong>
                        <p style="margin: 6px 0 0; font-size: 13px; color: rgba(255,255,255,0.95);">Configure wholesale/retail price levels (Silver, Gold, Platinum) after saving. Use the pricing section below to set cost and selling prices.</p>
                    </div>
                </div>
                <div class="col-sm-4  hide @if (!session('business.enable_price_tax')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                        {!! Form::select(
                            'tax',
                            $taxes,
                            !empty($duplicate_product->tax) ? $duplicate_product->tax : null,
                            ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2'],
                            $tax_attributes,
                        ) !!}
                    </div>
                </div>

                <div class="col-sm-4 hide @if (!session('business.enable_price_tax')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
                        {!! Form::select(
                            'tax_type',
                            ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')],
                            !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
                            ['class' => 'form-control select2', 'required'],
                        ) !!}
                    </div>
                </div>


                <div class="clearfix"></div>

                {{-- Product type & variations: single product form or variable product (add/select variations, SKUs, + row, shortcuts) --}}
                <div class="col-sm-12 product-type-section" id="section-variants">
                    <span class="section-label">@lang('product.product_type') &amp; variations</span>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                                {!! Form::select('type', $product_types, !empty($duplicate_product->type) ? $duplicate_product->type : null, [
                                    'class' => 'form-control select2',
                                    'required',
                                    'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add',
                                    'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12" id="product_form_part">
                        @include('product.partials.single_product_form_part', [
                            'profit_percent' => $default_profit_percent,
                        ])
                    </div>
                </div>

                <input type="hidden" id="variation_counter" value="1">
                <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">

            </div>
            </div>

        @endcomponent

        @component('components.widget', [
            'class' => 'box-primary amazon-card hide',
            'title' => 'Advanced options',
        ])
            <div class="row">
                @if (session('business.enable_product_expiry'))
                    @if (session('business.expiry_type') == 'add_expiry')
                        @php
                            $expiry_period = 12;
                            $hide = true;
                        @endphp
                    @else
                        @php
                            $expiry_period = null;
                            $hide = false;
                        @endphp
                    @endif
                    <div class="col-sm-4 @if ($hide) hide @endif">
                        <div class="form-group">
                            <div class="multi-input">
                                {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
                                {!! Form::text(
                                    'expiry_period',
                                    !empty($duplicate_product->expiry_period) ? @num_format($duplicate_product->expiry_period) : $expiry_period,
                                    [
                                        'class' => 'form-control pull-left input_number',
                                        'placeholder' => __('product.expiry_period'),
                                        'style' => 'width:60%;',
                                    ],
                                ) !!}
                                {!! Form::select(
                                    'expiry_period_type',
                                    ['months' => __('product.months'), 'days' => __('product.days'), '' => __('product.not_applicable')],
                                    !empty($duplicate_product->expiry_period_type) ? $duplicate_product->expiry_period_type : 'months',
                                    ['class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type'],
                                ) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-sm-4">
                    <div class="form-group">
                        <br>
                        <label>
                            {!! Form::checkbox('enable_sr_no', 1, !empty($duplicate_product) ? $duplicate_product->enable_sr_no : false, [
                                'class' => 'input-icheck',
                            ]) !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
                        </label> @show_tooltip(__('lang_v1.tooltip_sr_no'))
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <br>
                        <label>
                            {!! Form::checkbox(
                                'not_for_selling',
                                1,
                                !empty($duplicate_product) ? $duplicate_product->not_for_selling : false,
                                ['class' => 'input-icheck'],
                            ) !!} <strong>@lang('lang_v1.not_for_selling')</strong>
                        </label> @show_tooltip(__('lang_v1.tooltip_not_for_selling'))
                    </div>
                </div>

                <div class="clearfix"></div>

                <!-- Rack, Row & position number -->
                @if (session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
                    <div class="col-md-12">
                        <h4>@lang('lang_v1.rack_details'):
                            @show_tooltip(__('lang_v1.tooltip_rack_details'))
                        </h4>
                    </div>
                    @foreach ($business_locations as $id => $location)
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('rack_' . $id, $location . ':') !!}

                                @if (session('business.enable_racks'))
                                    {!! Form::text(
                                        'product_racks[' . $id . '][rack]',
                                        !empty($rack_details[$id]['rack']) ? $rack_details[$id]['rack'] : null,
                                        ['class' => 'form-control', 'id' => 'rack_' . $id, 'placeholder' => __('lang_v1.rack')],
                                    ) !!}
                                @endif

                                @if (session('business.enable_row'))
                                    {!! Form::text(
                                        'product_racks[' . $id . '][row]',
                                        !empty($rack_details[$id]['row']) ? $rack_details[$id]['row'] : null,
                                        ['class' => 'form-control', 'placeholder' => __('lang_v1.row')],
                                    ) !!}
                                @endif

                                @if (session('business.enable_position'))
                                    {!! Form::text(
                                        'product_racks[' . $id . '][position]',
                                        !empty($rack_details[$id]['position']) ? $rack_details[$id]['position'] : null,
                                        ['class' => 'form-control', 'placeholder' => __('lang_v1.position')],
                                    ) !!}
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Weight field moved to Size (L × W × H) section above --}}
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                    $product_custom_fields = !empty($custom_labels['product']) ? $custom_labels['product'] : [];
                    $product_cf_details = !empty($custom_labels['product_cf_details'])
                        ? $custom_labels['product_cf_details']
                        : [];

                @endphp
                <!--custom fields-->
                <div class="clearfix"></div>

                @foreach ($product_custom_fields as $index => $cf)
                    @if (!empty($cf))
                        @php
                            $db_field_name = 'product_custom_field' . $loop->iteration;
                            $cf_type = !empty($product_cf_details[$loop->iteration]['type'])
                                ? $product_cf_details[$loop->iteration]['type']
                                : 'text';
                            $dropdown = !empty($product_cf_details[$loop->iteration]['dropdown_options'])
                                ? explode(PHP_EOL, $product_cf_details[$loop->iteration]['dropdown_options'])
                                : [];
                        @endphp

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label($db_field_name, $cf . ':') !!}

                                @if (in_array($cf_type, ['text', 'date']))
                                    <input type="{{ $cf_type }}" name="{{ $db_field_name }}" id="{{ $db_field_name }}"
                                        value="{{ !empty($duplicate_product->$db_field_name) ? $duplicate_product->$db_field_name : null }}"
                                        class="form-control" placeholder="{{ $cf }}">
                                @elseif($cf_type == 'dropdown')
                                    {!! Form::select(
                                        $db_field_name,
                                        $dropdown,
                                        !empty($duplicate_product->$db_field_name) ? $duplicate_product->$db_field_name : null,
                                        ['placeholder' => $cf, 'class' => 'form-control select2'],
                                    ) !!}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('preparation_time_in_minutes', __('lang_v1.preparation_time_in_minutes') . ':') !!}
                        {!! Form::number(
                            'preparation_time_in_minutes',
                            !empty($duplicate_product->preparation_time_in_minutes) ? $duplicate_product->preparation_time_in_minutes : null,
                            ['class' => 'form-control', 'placeholder' => __('lang_v1.preparation_time_in_minutes')],
                        ) !!}
                    </div>
                </div>
                <!--custom fields-->
                <div class="clearfix"></div>
                @include('layouts.partials.module_form_part')
            </div>
        @endcomponent

        <div class="row">
            <div class="col-sm-12">
                <input type="hidden" name="submit_type" id="submit_type">
                <div class="text-center add-product-btn-wrap" style="margin-top: 24px; margin-bottom: 24px;">
                    <div class="btn-group add-product-actions" role="group">
                        @if ($selling_price_group_count && $has_b2b_access)
                            <button type="submit" value="submit_n_add_selling_prices"
                                class="btn btn-lg submit_product_form" style="background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #fff; border: 1px solid #E47911; font-weight: 600; padding: 10px 20px; border-radius: 6px;">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                        @endif

                        <button type="submit" value="save_n_add_another"
                            class="btn btn-lg submit_product_form" style="background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #fff; border: 1px solid #E47911; font-weight: 600; padding: 10px 20px; border-radius: 6px;">@lang('lang_v1.save_n_add_another')</button>

                        <button type="submit" value="submit"
                            class="btn btn-lg submit_product_form" style="background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%); color: #fff; border: 1px solid #E47911; font-weight: 600; padding: 10px 20px; border-radius: 6px;">@lang('messages.save')</button>
                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}

    </section>
    <!-- /.content -->
</div>
<!-- /.amazon-products-container -->

@endsection

@section('javascript')

    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Stepper: click to scroll to section
            $('.product-edit-stepper .step').on('click', function() {
                var sectionId = $(this).data('section');
                var $target = $('#' + sectionId);
                if ($target.length) {
                    $target[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
                    $('.product-edit-stepper .step').removeClass('active');
                    $(this).addClass('active');
                }
            });
            __page_leave_confirmation('#product_add_form');
            
            // Product Source toggle - show/hide manage stock based on selection
            function toggleManageStock() {
                var productSource = $('#product_source').val();
                if (productSource === 'out_source') {
                    $('#manage_stock_container').show();
                } else {
                    $('#manage_stock_container').hide();
                }
            }
            
            // Initial toggle on page load
            toggleManageStock();
            
            // Toggle on change
            $('#product_source').on('change', function() {
                toggleManageStock();
            });

            // Gift card options: show only when Gift card checkbox is checked (iCheck uses ifChecked/ifUnchecked)
            function toggleGiftCardOptions() {
                var checked = $('#is_gift_card').is(':checked');
                if (checked) {
                    $('#gift_card_options_row').show();
                } else {
                    $('#gift_card_options_row').hide();
                }
            }
            toggleGiftCardOptions();
            $(document).on('ifChecked ifUnchecked', '#is_gift_card', function() {
                toggleGiftCardOptions();
            });
            $(document).on('change', '#is_gift_card', function() {
                toggleGiftCardOptions();
            });

            // Default product image preview (visible after upload)
            $('#upload_image').on('change', function() {
                var el = this;
                var $wrap = $('#default-image-preview-wrap');
                var $img = $('#default-image-preview');
                if (el.files && el.files[0]) {
                    var r = new FileReader();
                    r.onload = function() { $img.attr('src', r.result); $wrap.show(); };
                    r.readAsDataURL(el.files[0]);
                } else { $wrap.hide(); $img.attr('src', ''); }
            });
            
            onScan.attachTo('input#sku', {
                suffixKeyCodes: [13], // enter-key expected at the end of a scan
                reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
                onScan: function(sCode, iQty) {
                    $('input#sku').val(sCode);
                },
                onScanError: function(oDebug) {
                    console.log(oDebug);
                },
                minLength: 2,
                ignoreIfFocusOn: ['input', '.form-control']
                // onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
                //     console.log('Pressed: ' + iKeyCode);
                // }
            });

            if ($('#upload_image').data('fileinput')) {
                $('#upload_image').fileinput('destroy');
            }

            // $('#upload_image').fileinput({
            //     showUpload: false,
            //     showRemove: true,
            //     overwriteInitial: true,
            //     initialPreviewAsData: true,
            //     initialPreview: [
            //         "{{ asset('img/default.png') }}"
            //     ],
            //     initialPreviewConfig: [
            //         {
            //             caption: "default.png",
            //             url: "0",
            //             width: "120px",
            //             key: 1
            //         }
            //     ],
            //     allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
            //      maxFileSize: 5120,
            //     defaultPreviewContent:'<div class="file-preview-frame krajee-default  kv-preview-thumb" id="preview-1747636814017-0" data-fileindex="0" data-template="image"><div class="kv-file-content"><img src="{{ asset('img/default.png') }}"class="file-preview-image kv-preview-data" title="default.png" alt="default.png" style="width:auto;height:160px;"></div><div class="file-thumbnail-footer"><div class="file-footer-caption" title="default.png">default.png<br></div><div class="file-upload-indicator" title="Not uploaded yet"><i class="glyphicon glyphicon-hand-down text-warning"></i></div> <div class="file-actions"></div></div></div>',
            // });

            // Handle file removal via AJAX
            $(document).on('click', '.kv-file-remove', function() {
                try {
                    var url = $(this).data('url');
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            // Set the preview to the default image after successful removal
                            $('#upload_image').fileinput('clear');
                            $('#upload_image').fileinput({
                                defaultPreviewContent: '<div class="file-preview-frame krajee-default  kv-preview-thumb" id="preview-1747636814017-0" data-fileindex="0" data-template="image"><div class="kv-file-content"><img src="{{ asset('img/default.png') }}"class="file-preview-image kv-preview-data" title="default.png" alt="default.png" style="width:auto;height:160px;"></div><div class="file-thumbnail-footer"><div class="file-footer-caption" title="default.png">default.png<br></div><div class="file-upload-indicator" title="Not uploaded yet"><i class="glyphicon glyphicon-hand-down text-warning"></i></div> <div class="file-actions"></div></div></div>'
                            });
                        },
                        error: function(response) {
                            toastr.error(response.message);
                        }
                    })
                } catch (error) {
                    toastr.error(error.message);
                }
            });

            // State restriction toggle logic
            // $('#state_check').on('change', function() {
            //     var value = $(this).val();
            //     if (value === 'in' || value === 'not_in') {
            //         $('#states_selection_div').slideDown();
            //         if (value === 'in') {
            //             $('#state_help_text').text('Product will only be available in selected states');
            //         } else {
            //             $('#state_help_text').text('Product will be excluded from selected states');
            //         }
            //     } else {
            //         $('#states_selection_div').slideUp();
            //         $('#states').val(null).trigger('change');
            //     }
            // });
        });
    </script>
@endsection

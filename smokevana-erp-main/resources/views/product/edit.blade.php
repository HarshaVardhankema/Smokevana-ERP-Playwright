@extends('layouts.app')
@section('title', __('product.edit_product'))

@section('css')
<style>
    /* Match list products page background - same light gray (#EAEDED) as product index */
    .amazon-products-container { background: #EAEDED; min-height: 100vh; padding: 16px 20px 40px; }
    @media (max-width: 768px) { .amazon-products-container { padding: 10px 12px 30px; } }
    .amazon-product-page { background: #EAEDED; padding: 0; min-height: auto; max-width: 100%; overflow-x: hidden; width: 100%; }
    @media (max-width: 768px) { .amazon-product-page { padding: 0; } }
    .amazon-product-header { margin-bottom: 0; }
    .amazon-product-header-banner { background: #232f3e !important; border-radius: 6px; padding: 22px 28px; margin-top: 4px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4); border-bottom: 3px solid #FF9900; }
    .amazon-product-header-content { position: relative; z-index: 2; }
    .amazon-product-header-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; color: #ffffff; margin: 0 0 4px; }
    .amazon-product-header-title i { font-size: 22px; color: #FF9900 !important; }
    .amazon-product-header-subtitle { font-size: 13px; color: rgba(249, 250, 251, 0.88); margin: 0; }
    .amazon-product-header-banner .btn-default,
    .amazon-product-header-banner .amazon-add-back-btn { background: #37475a !important; border: 1px solid #485769 !important; color: #fff !important; font-weight: 500; border-radius: 6px; padding: 10px 20px; transition: background 0.2s ease; }
    .amazon-product-header-banner .btn-default:hover,
    .amazon-product-header-banner .amazon-add-back-btn:hover { background: #485769 !important; border-color: #5a6b7d !important; color: #fff !important; }
    .amazon-product-page .amazon-card { border-radius: 12px; box-shadow: 0 2px 12px rgba(15, 17, 17, 0.1); border: 1px solid #D5D9D9; overflow: hidden; background-color: #ffffff; margin-bottom: 20px; max-width: 100%; }
    /* Amazon-style section headers: Basic Information, Description & Media, Warranty & Size, Pricing
       Override global .box-header (amazon-theme.css) so these cards get navy + orange, not light gray */
    .amazon-product-page .amazon-card .tw-p-2,
    .amazon-card .tw-p-2 { padding: 0 !important; }
    .amazon-products-container .amazon-card .box-header,
    .amazon-product-page .amazon-card .box-header,
    .amazon-card.box-primary .box-header,
    div.amazon-card .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        background-color: #37475a !important;
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
        color: #ffffff;
        font-weight: 600;
    }

    .product-form-card .product-text-card label {
        color: #ffffff !important;
    }

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
    /* Gallery box within navy card */
    .product-text-card .product-gallery-box {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
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
    /* File preview within navy card */
    .product-text-card .file-preview,
    .default-image-card .file-preview {
        background-color: rgba(255, 255, 255, 0.95) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
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

    /* Inner blocks (Warranty text, Size L×W×H) – Amazon navy + orange accent */
    .product-text-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 12px;
        margin-bottom: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        color: #f9fafb;
        border-left: 4px solid #FF9900;
    }
    .product-description-box { overflow: hidden; max-width: 100%; box-sizing: border-box; }
    .product-description-box .form-group { max-width: 100%; overflow: hidden; }
    .product-description-box .tox-tinymce, .product-description-box .tox .tox-edit-area, .product-description-box iframe { max-width: 100% !important; box-sizing: border-box !important; }
    .product-description-box .tox .tox-edit-area { height: 320px !important; min-height: 200px; }
    .product-description-box textarea.form-control#product_description { max-width: 100%; min-height: 200px; height: 320px; resize: vertical; }
    .dimension-cards { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; margin-bottom: 12px; }
    .dimension-card {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        border-radius: 10px;
        padding: 12px 14px;
        min-width: 120px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        border-left: 4px solid #FF9900;
    }
    .dimension-card-label { font-size: 11px; font-weight: 600; color: #ffffff; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
    .dimension-card .form-control {
        background: rgba(255,255,255,0.12) !important;
        border: 1px solid rgba(255,255,255,0.25) !important;
        color: #fff !important;
        border-radius: 6px;
    }
    .dimension-card .form-control::placeholder { color: rgba(255,255,255,0.6); }
    .dimension-card .form-control:focus {
        border-color: #FF9900 !important;
        outline: 0;
        box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.3);
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
    /* Action buttons – consistent spacing at any zoom */
    .amazon-product-page .btn-group .btn {
        margin: 0 0.25rem;
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
        border-top: none;
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
    /* Keep tooltip icon inline with label */
    .label-inline-tooltip { display: inline-flex; align-items: center; gap: 6px; flex-wrap: nowrap; margin-bottom: 6px; }
    .label-inline-tooltip label { display: inline; margin-bottom: 0; }

    /* ===== Basic Information: consistent grid, alignment, spacing ===== */
    .card-basic-info .tw-flow-root .row,
    .amazon-card .tw-flow-root .row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -8px;
        margin-right: -8px;
    }
    .card-basic-info .tw-flow-root .row > [class*="col-"],
    .amazon-card .tw-flow-root .row > [class*="col-"] {
        padding-left: 8px;
        padding-right: 8px;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
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
        margin-bottom: 8px !important;
        padding: 0 !important;
        font-weight: 600;
        font-size: 14px;
        color: #0F1111;
        display: block;
    }
    .card-basic-info .form-control,
    .card-basic-info select.form-control,
    .card-basic-info input.form-control,
    .card-basic-info textarea.form-control,
    .amazon-card .card-basic-info .form-control {
        width: 100% !important;
        min-height: 42px;
        padding: 10px 14px !important;
        border-radius: 6px !important;
        border: 1px solid #D5D9D9 !important;
        margin: 0 !important;
        box-sizing: border-box;
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
        min-height: 42px !important;
        padding: 6px 14px !important;
        border-radius: 6px !important;
        border: 1px solid #D5D9D9 !important;
    }
    .card-basic-info .select2-container--default .select2-selection__rendered,
    .amazon-card .card-basic-info .select2-container--default .select2-selection__rendered {
        line-height: 26px !important;
        padding-left: 0 !important;
    }
    .card-basic-info .input-group .form-control,
    .card-basic-info .input-group select {
        border-radius: 6px 0 0 6px !important;
    }
    .card-basic-info .input-group-btn .btn,
    .card-basic-info .input-group .input-group-btn .btn {
        border-radius: 0 6px 6px 0;
        min-height: 42px;
    }
    /* Action buttons: aligned, consistent padding and spacing */
    .amazon-product-page .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: center;
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
</style>
@endsection

@section('content')
<div class="amazon-products-container">
    @php
        $is_image_required = !empty($common_settings['is_product_image_required']) && empty($product->image);
        
        // Fulfillment type badge
        $sourceType = $product->product_source_type ?? 'in_house';
        $fulfillmentBadge = '<span class="label bg-green" style="font-size: 12px; margin-left: 15px; vertical-align: middle;"><i class="fas fa-warehouse"></i> In-House</span>';
        if ($sourceType === 'dropshipped') {
            $vendor = $product->vendors->first();
            if ($vendor) {
                if ($vendor->vendor_type === 'woocommerce') {
                    $fulfillmentBadge = '<span class="label bg-blue" style="font-size: 12px; margin-left: 15px; vertical-align: middle;"><i class="fas fa-globe"></i> Dropship - WooCommerce</span><small style="margin-left: 8px; color: #666; vertical-align: middle;">(' . e($vendor->name) . ')</small>';
                } else {
                    $fulfillmentBadge = '<span class="label bg-purple" style="font-size: 12px; margin-left: 15px; vertical-align: middle;"><i class="fas fa-user-tie"></i> Dropship - ERP Portal</span><small style="margin-left: 8px; color: #666; vertical-align: middle;">(' . e($vendor->name) . ')</small>';
                }
            } else {
                $fulfillmentBadge = '<span class="label bg-orange" style="font-size: 12px; margin-left: 15px; vertical-align: middle;"><i class="fas fa-truck"></i> Dropship</span>';
            }
        }
    @endphp

    <!-- Content Header (Page header) -->
    <section class="content-header amazon-product-header">
        <div class="amazon-product-header-banner">
            <div class="amazon-product-header-content">
                <h1 class="amazon-product-header-title">
                    <i class="fas fa-edit"></i>
                    @lang('product.edit_product') {!! $fulfillmentBadge !!}
                </h1>
                <p class="amazon-product-header-subtitle">Fill in the product details below to update your catalog item.</p>
            </div>
            <a href="{{ action([\App\Http\Controllers\ProductController::class, 'index']) }}" class="btn btn-default amazon-add-back-btn">
                <i class="fas fa-arrow-left"></i> @lang('messages.back')
            </a>
        </div>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->


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
    </section>

    <!-- Main content -->
    <section class="content amazon-product-page">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\ProductController::class, 'update'], [$product->id]),
            'method' => 'PUT',
            'id' => 'product_add_form',
            'class' => 'product_form product-form-page',
            'files' => true,
        ]) !!}
        <input type="hidden" id="product_id" value="{{ $product->id }}">

        {{-- Stepper --}}
        <div class="product-edit-stepper">
            <span class="step active" data-section="section-basic-info"><span class="step-num">1</span> Basic Info</span>
            <span class="step" data-section="section-media"><span class="step-num">2</span> Media</span>
            <span class="step" data-section="section-pricing"><span class="step-num">3</span> Pricing</span>
            <span class="step" data-section="section-variants"><span class="step-num">4</span> Variants</span>
        </div>

        {{-- Card 1: Basic Information --}}
        @component('components.widget', [
            'class' => 'box-primary amazon-card product-form-card card-basic-info',
            'id' => 'section-basic-info',
            'title' => 'Basic Information',
            'title_svg' => '<i class="fas fa-file-alt" style="margin-right:6px;"></i>',
        ])
            <p class="card-subtitle">Enter the core product details.</p>
            {{-- Tobacco & Ecom at top right --}}
            <div class="row" style="margin-bottom: 12px;">
                <div class="col-md-12" style="display: flex; justify-content: flex-end; gap: 20px; flex-wrap: wrap;">
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::checkbox('is_tobacco_product', 1, $product->is_tobacco_product ? true : false, [
                            'class' => 'input-icheck',
                            'id' => 'is_tobacco_product',
                        ]) !!} <strong>Tobacco product</strong>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::checkbox('enable_selling', 1, $product->enable_selling ? true : false, [
                            'class' => 'input-icheck',
                            'id' => 'enable_selling',
                        ]) !!} <strong>Ecom</strong>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        {!! Form::checkbox('is_gift_card', 1, $product->is_gift_card ?? false, [
                            'class' => 'input-icheck',
                            'id' => 'is_gift_card',
                        ]) !!} <strong>Gift card</strong>
                    </div>
                </div>
            </div>
            {{-- Gift card options: only visible when Gift card checkbox is checked (visibility toggled by JS) --}}
            <div class="row" id="gift_card_options_row" style="display: none; margin-bottom: 16px; padding: 12px 16px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                <div class="col-md-12">
                    <strong style="margin-bottom: 8px; display: block;">Gift card options</strong>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('gift_card_expires_at', __('Gift card expiry date') . ':') !!}
                        {!! Form::date('gift_card_expires_at', $product->gift_card_expires_at ?? null, [
                            'class' => 'form-control',
                            'placeholder' => __('Select date'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('gift_card_stock', __('Gift card stock') . ':') !!}
                        {!! Form::number('gift_card_stock', $product->gift_card_stock !== null && $product->gift_card_stock !== '' ? (int) round($product->gift_card_stock) : null, [
                            'class' => 'form-control input_number',
                            'placeholder' => __('Quantity'),
                            'min' => '0',
                            'step' => '1',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('name', __('product.product_name') . ':*') !!}
                        {!! Form::text('name', $product->name, [
                            'class' => 'form-control prevent-select',
                            'required',
                            'placeholder' => __('product.product_name'),
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-1-5">
                    <div class="form-group">
                        <span class="label-inline-tooltip">{!! Form::label('sku', __('product.sku') . ':*') !!} @show_tooltip(__('tooltip.sku'))</span>
                        {!! Form::text('sku', $product->sku, [
                            'class' => 'form-control',
                            'placeholder' => __('product.sku'),
                            'required',
                            'pattern' => '^[a-zA-Z0-9@-]+$',
                            'title' => 'Only letters, numbers, @ and - are allowed as special characters',
                        ]) !!}
                    </div>
                </div>
                {{-- <div class="col-sm-2"> --}}
                {{-- <div class="form-group">
                  {!! Form::label('barcode_no', __('product.barcode_no') . ':') !!} @show_tooltip(__('tooltip.barcode_no'))
                  {!! Form::text('barcode_no',$product->barcode_no , ['class' => 'form-control',
                  'placeholder' => __('product.barcode_no')]); !!}
              </div> --}}
                {{-- </div> --}}
                <div class="col-sm-2 hide">
                    <div class="form-group">
                        {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
                        {!! Form::select('barcode_type', $barcode_types, $product->barcode_type, [
                            'placeholder' => __('messages.please_select'),
                            'class' => 'form-control select2',
                            'required',
                        ]) !!}
                    </div>
                </div>

                {{-- <div class="clearfix"></div> --}}

                <div class="col-sm-1">
                    <div class="form-group">
                        {!! Form::label('ml', __('product.ml') . ':') !!}
                        {!! Form::number('ml', $product->ml, ['class' => 'form-control ', 'placeholder' => __('fill ml if needed')]) !!}
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                        {!! Form::label('ct', __('product.ct') . ':') !!}
                        {!! Form::number('ct', $product->ct, ['class' => 'form-control ', 'placeholder' => __('fill ct if needed')]) !!}
                    </div>
                </div>
                <div class="col-md-1-5">
                    <div class="form-group">
                        {!! Form::label('locationTaxType', __('product.locationTaxType') . ':') !!}
                        {!! Form::select('locationTaxType[]', $taxTypes, $product->locationTaxType, [
                            'class' => 'form-control select2',
                            // 'multiple' => 'multiple',
                            'placeholder' => __('Select location tax types'),
                        ]) !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-1-5">
                    <div class="form-group">
                        {!! Form::label('productVisibility', __('product.productVisibility') . ':') !!}
                        {!! Form::select('productVisibility', $productVisibility, $product->productVisibility, [
                            'class' => 'form-control select2',
                            'placeholder' => __('Product Visibility'),
                        ]) !!}
                    </div>
                </div>



                <div class="col-sm-2 hide">
                    <div class="form-group">
                        {!! Form::label('unit_id', __('product.unit') . ':*') !!}
                        <div class="input-group">
                            {!! Form::select('unit_id', $units, $product->unit_id, ['class' => 'form-control select2', 'required']) !!}
                            <span class="input-group-btn">
                                <button type="button" @if (!auth()->user()->can('unit.create')) disabled @endif
                                    class="btn btn-default bg-white btn-flat quick_add_unit btn-modal"
                                    data-href="{{ action([\App\Http\Controllers\UnitController::class, 'create'], ['quick_add' => true]) }}"
                                    title="@lang('unit.add_unit')" data-container=".view_modal"><i
                                        class="fa fa-plus-circle text-primary fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- <div class="clearfix"></div> --}}
                <div class="col-sm-4 @if (!session('business.enable_sub_units')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('sub_unit_ids', __('lang_v1.related_sub_units') . ':') !!} @show_tooltip(__('lang_v1.sub_units_tooltip'))

                        <select name="sub_unit_ids[]" class="form-control select2" multiple id="sub_unit_ids">
                            @foreach ($sub_units as $sub_unit_id => $sub_unit_value)
                                <option value="{{ $sub_unit_id }}" @if (is_array($product->sub_unit_ids) && in_array($sub_unit_id, $product->sub_unit_ids)) selected @endif>
                                    {{ $sub_unit_value['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if (!empty($common_settings['enable_secondary_unit']))
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('secondary_unit_id', __('lang_v1.secondary_unit') . ':') !!} @show_tooltip(__('lang_v1.secondary_unit_help'))
                            {!! Form::select('secondary_unit_id', $units, $product->secondary_unit_id, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                @endif

                <div class="col-sm-2 @if (!session('business.enable_brand')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('brand_id', __('product.brand') . ':') !!}
                        <div class="input-group">
                            {!! Form::select('brand_id', $brands, $product->brand_id, [
                                'placeholder' => __('messages.please_select'),
                                'class' => 'form-control select2',
                            ]) !!}
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
                <div class="col-sm-2 @if (!session('business.enable_category')) hide @endif">
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
                        {!! Form::select('category_id', $categories, $product->category_id, [
                            'placeholder' => __('messages.please_select'),
                            'class' => 'form-control select2',
                        ]) !!}
                    </div>
                </div>

                <div class="col-sm-2 @if (!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                        {!! Form::select('sub_category_id', $sub_categories, $product->sub_category_id, [
                            'placeholder' => __('messages.please_select'),
                            'class' => 'form-control select2',
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-2 @if (!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                    <div class="form-group">
                        {!! Form::label('custom_sub_categories', __('Web categories') . ':') !!}
                        {!! Form::select('custom_sub_categories[]', $catList, $product->webcategories->pluck('id'), [
                            'placeholder' => __('messages.please_select'),
                            'id' => 'custom_sub_categories',
                            'class' => 'form-control select2',
                            'multiple' => 'multiple',
                        ]) !!}
                    </div>
                </div>

                


                <div class="col-sm-1">
                    <div class="form-group">
                        {!! Form::label('maxSaleLimit', __('product.maxSaleLimit') . ':') !!}
                        {!! Form::number('maxSaleLimit', $product->maxSaleLimit, [
                            'class' => 'form-control ',
                            'placeholder' => __('max Sale Limit'),
                        ]) !!}
                    </div>
                </div>
                {{-- 
            <div class="clearfix"></div> --}}
                <div class="col-sm-1" id="alert_quantity_div" @if (!$product->enable_stock) style="display:none" @endif>
                    <div class="form-group">
                        <span class="label-inline-tooltip">{!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!} @show_tooltip(__('tooltip.alert_quantity'))</span>
                        {!! Form::text('alert_quantity', $alert_quantity, [
                            'class' => 'form-control input_number',
                            'placeholder' => __('product.alert_quantity'),
                            'min' => '0',
                        ]) !!}
                    </div>
                </div>
                <!-- include module fields -->
                @if (!empty($pos_module_data))
                    @foreach ($pos_module_data as $key => $value)
                        @if (!empty($value['view_path']))
                            @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                        @endif
                    @endforeach
                @endif
                <div class="clearfix"></div>
                </div>
                <div class="form-group">
                        <div class="row" style="align-items: flex-end;">
                            <div class="col-md-3">
                                {!! Form::label('state_check', __('State Restriction') . ':') !!}
                                {!! Form::select(
                                    'state_check',
                                    [
                                        'all' => 'All States (No Restriction)',
                                        'in' => 'Only These States',
                                        'not_in' => 'Exclude These States',
                                    ],
                                    $product->state_check ?? 'all',
                                    ['class' => 'form-control select2', 'id' => 'state_check'],
                                ) !!}
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {!! Form::label('product_source', __('Product Source') . ':') !!}
                                    {!! Form::select('product_source', [
                                        'in_house' => 'In House',
                                        'out_source' => 'Out Source'
                                    ], $product->product_source ?? 'in_house', [
                                        'class' => 'form-control select2',
                                        'id' => 'product_source',
                                        'disabled' => (($product->product_source ?? 'in_house') === 'out_source')
                                    ]) !!}
                                    @if ((($product->product_source ?? 'in_house') === 'out_source'))
                                        {!! Form::hidden('product_source', $product->product_source) !!}
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-2" id="manage_stock_container" style="display: {{ ($product->product_source ?? 'in_house') == 'out_source' ? 'block' : 'none' }};">
                                <div class="form-group">
                                    <br>
                                    <label>
                                        {!! Form::checkbox('enable_stock', 1, $product->enable_stock, [
                                            'class' => 'input-icheck',
                                            'id' => 'enable_stock',
                                        ]) !!} <strong>@lang('product.manage_stock')</strong>
                                    </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
                                </div>
                            </div>
                            <div class="col-sm-2 @if (count($business_locations) > 1) @else hide @endif">
                                <div class="form-group">
                                    <span class="label-inline-tooltip">{!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))</span>
                                    {!! Form::select('product_locations[]', $business_locations, $product->product_locations->pluck('id'), [
                                        'class' => 'form-control select',
                                        'id' => 'product_locations',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-5" id="states_selection_div"
                                style="display: {{ $product->state_check == 'in' || $product->state_check == 'not_in' ? 'block' : 'none' }};">
                                {!! Form::label('states', __('Select States') . ':') !!}
                                <div style="min-width:220px;max-width:100%">
                                    {!! Form::select('states[]', config('us_states'), $product->product_states->pluck('state')->toArray(), [
                                        'class' => 'form-control select2',
                                        'id' => 'states',
                                        'multiple' => 'multiple',
                                        'placeholder' => 'Select states...',
                                        'style' => 'min-width:220px;max-width:100%;',
                                    ]) !!}
                                </div>
                                <small class="help-block">
                                    <span id="state_help_text">
                                        @if ($product->state_check == 'in')
                                            Product will only be available in selected states
                                        @elseif($product->state_check == 'not_in')
                                            Product will be excluded from selected states
                                        @endif
                                    </span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        @endcomponent

        {{-- Card 2: Description & Media — Left: description + brochure, Right: image + gallery --}}
        @component('components.widget', [
            'class' => 'box-primary amazon-card product-form-card',
            'id' => 'section-media',
            'title' => 'Description & Media',
            'title_svg' => '<i class="fas fa-image" style="margin-right:6px;"></i>',
        ])
            <p class="card-subtitle">Add images and product description.</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="product-text-card product-description-box">
                        <div class="form-group mb-0">
                            {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
                            {!! Form::textarea('product_description', $product->product_description, ['class' => 'form-control', 'id' => 'product_description']) !!}
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
                <div class="col-md-6 product-card1-images" data-image-url="{{ $product->image_url }}">
                    <div class="product-text-card default-image-card">
                        <div class="form-group mb-0">
                            {!! Form::label('image', __('Default Product Image') . ':') !!}
                            {!! Form::file('image', [
                                'id' => 'upload_image',
                                'accept' => 'image/*',
                                'required' => $is_image_required,
                                'class' => 'upload-element',
                            ]) !!}
                            <small class="help-block text-muted" style="color: rgba(255,255,255,0.8) !important;">
                                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000]) &middot; @lang('lang_v1.aspect_ratio_should_be_1_1')
                                @if (!empty($product->image))
                                    &middot; @lang('lang_v1.previous_image_will_be_replaced')
                                @endif
                            </small>

                            @if (!empty($product->image_url))
                            @endif
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

                        .product-card1-images .krajee-default.file-preview-frame { top: 0; }

                        .product-card1-images .form-group label {
                            font-weight: 600;
                            font-size: 13px;
                            color: #334155;
                            margin-bottom: 6px;
                        }

                        .product-card1-images .help-block {
                            font-size: 12px;
                            line-height: 1.4;
                            color: #64748b;
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

                        .product-card1-images .add-gallery-link { font-size: 13px; font-weight: 500; }

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
                    <div class="form-group">
                        <label>@lang('Product gallery')</label>
                        <div id="product-gallery-box" class="product-gallery-box">
                            <div id="product-gallery-thumbs" class="product-gallery-thumbs">
                                @foreach ($product->product_gallery_images as $gallery_image)
                                    <div class="product-gallery-thumb" data-idx="{{ $loop->index }}"
                                        data-id="{{ $gallery_image->id }}">
                                        <img src="{{ $gallery_image->image_url }}" alt="Gallery Image">
                                        <div class="gallery-thumb-actions">
                                            <button type="button" class="edit-gallery-thumb" title="Edit"
                                                data-id="{{ $gallery_image->id }}"><i class="fa fa-edit"></i></button>
                                            <button type="button" class="remove-gallery-thumb" title="Remove"
                                                data-id="{{ $gallery_image->id }}"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <a href="#" id="add-gallery-images" class="add-gallery-link">@lang('Add product gallery images')</a>
                            <input type="file" id="gallery_images" name="gallery[]" accept="image/*" multiple
                                style="display:none;">
                        </div>
                        <small>
                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])<br>
                                @lang('lang_v1.aspect_ratio_should_be_1_1')
                            </p>
                        </small>
                    </div>
                </div>


            </div>

            {{-- Warranty & Size (under Description & Media) --}}
            <div class="row" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <div class="col-sm-12">
                    <h5 class="text-muted" style="font-size: 14px; font-weight: 600; margin-bottom: 12px;"><i class="fas fa-ruler-combined" style="margin-right:6px;"></i> Warranty & Size</h5>
                    <div class="product-text-card">
                        <div class="form-group mb-0">
                            {!! Form::label('product_warranty', __('lang_v1.product_warranty') . ':') !!}
                            {!! Form::textarea('product_warranty', $product->product_warranty, ['class' => 'form-control', 'id' => 'product_warranty']) !!}
                        </div>
                    </div>

                    <div class="product-text-card">
                        {{-- Product dimensions (L × W × H) for shipping/carton size --}}
                        <div class="form-group" style="margin-top: 2px;">
                            {!! Form::label('dimensions', 'Size (L × W × H)') !!}
                            <div class="dimension-cards">
                                <div class="dimension-card">
                                    <div class="dimension-card-label">Length (L)</div>
                                    {!! Form::number('length', $product->length, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'inches',
                                    ]) !!}
                                </div>
                                <div class="dimension-card">
                                    <div class="dimension-card-label">Width (W)</div>
                                    {!! Form::number('width', $product->width, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'inches',
                                    ]) !!}
                                </div>
                                <div class="dimension-card">
                                    <div class="dimension-card-label">Height (H)</div>
                                    {!! Form::number('height', $product->height, [
                                        'class' => 'form-control',
                                        'min' => 0,
                                        'step' => '0.01',
                                        'placeholder' => 'inches',
                                    ]) !!}
                                </div>
                                <div class="dimension-card">
                                    <div class="dimension-card-label">Weight (Lbs)</div>
                                    {!! Form::number('weight', $product->weight, [
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

        @endcomponent

        @component('components.widget', ['class' => 'box-primary hide'])
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
                                @php
                                    $disabled = false;
                                    $disabled_period = false;
                                    if (empty($product->expiry_period_type) || empty($product->enable_stock)) {
                                        $disabled = true;
                                    }
                                    if (empty($product->enable_stock)) {
                                        $disabled_period = true;
                                    }
                                @endphp
                                {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
                                {!! Form::text('expiry_period', @num_format($product->expiry_period), [
                                    'class' => 'form-control pull-left input_number',
                                    'placeholder' => __('product.expiry_period'),
                                    'style' => 'width:60%;',
                                    'disabled' => $disabled,
                                ]) !!}
                                {!! Form::select(
                                    'expiry_period_type',
                                    ['months' => __('product.months'), 'days' => __('product.days'), '' => __('product.not_applicable')],
                                    $product->expiry_period_type,
                                    [
                                        'class' => 'form-control select2 pull-left',
                                        'style' => 'width:40%;',
                                        'id' => 'expiry_period_type',
                                        'disabled' => $disabled_period,
                                    ],
                                ) !!}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-sm-4">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('enable_sr_no', 1, $product->enable_sr_no, ['class' => 'input-icheck']) !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
                        </label>
                        @show_tooltip(__('lang_v1.tooltip_sr_no'))
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <br>
                        <label>
                            {!! Form::checkbox('not_for_selling', 1, $product->not_for_selling, ['class' => 'input-icheck']) !!} <strong>@lang('lang_v1.not_for_selling')</strong>
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


                                @if (!empty($rack_details[$id]))
                                    @if (session('business.enable_racks'))
                                        {!! Form::text('product_racks_update[' . $id . '][rack]', $rack_details[$id]['rack'], [
                                            'class' => 'form-control',
                                            'id' => 'rack_' . $id,
                                        ]) !!}
                                    @endif

                                    @if (session('business.enable_row'))
                                        {!! Form::text('product_racks_update[' . $id . '][row]', $rack_details[$id]['row'], ['class' => 'form-control']) !!}
                                    @endif

                                    @if (session('business.enable_position'))
                                        {!! Form::text('product_racks_update[' . $id . '][position]', $rack_details[$id]['position'], [
                                            'class' => 'form-control',
                                        ]) !!}
                                    @endif
                                @else
                                    {!! Form::text('product_racks[' . $id . '][rack]', null, [
                                        'class' => 'form-control',
                                        'id' => 'rack_' . $id,
                                        'placeholder' => __('lang_v1.rack'),
                                    ]) !!}

                                    {!! Form::text('product_racks[' . $id . '][row]', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('lang_v1.row'),
                                    ]) !!}

                                    {!! Form::text('product_racks[' . $id . '][position]', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __('lang_v1.position'),
                                    ]) !!}
                                @endif

                            </div>
                        </div>
                    @endforeach
                @endif


                {{-- Weight field moved to Size (L × W × H) section above --}}
                <div class="clearfix"></div>

                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                    $product_custom_fields = !empty($custom_labels['product']) ? $custom_labels['product'] : [];
                    $product_cf_details = !empty($custom_labels['product_cf_details'])
                        ? $custom_labels['product_cf_details']
                        : [];
                @endphp
                <!--custom fields-->

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
                                    <input type="{{ $cf_type }}" name="{{ $db_field_name }}"
                                        id="{{ $db_field_name }}" value="{{ $product->$db_field_name }}"
                                        class="form-control" placeholder="{{ $cf }}">
                                @elseif($cf_type == 'dropdown')
                                    {!! Form::select($db_field_name, $dropdown, $product->$db_field_name, [
                                        'placeholder' => $cf,
                                        'class' => 'form-control select2',
                                    ]) !!}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('preparation_time_in_minutes', __('lang_v1.preparation_time_in_minutes') . ':') !!}
                        {!! Form::number('preparation_time_in_minutes', $product->preparation_time_in_minutes, [
                            'class' => 'form-control',
                            'placeholder' => __('lang_v1.preparation_time_in_minutes'),
                        ]) !!}
                    </div>
                </div>
                <!--custom fields-->
                @include('layouts.partials.module_form_part')
            </div>
        @endcomponent

        {{-- Card 4: Pricing & Variants --}}
        @component('components.widget', [
            'class' => 'box-primary amazon-card product-form-card',
            'id' => 'section-pricing',
            'title' => 'Pricing',
            'title_svg' => '<i class="fas fa-dollar-sign" style="margin-right:6px;"></i>',
        ])
            <p class="card-subtitle">Set product type, cost, and price tiers.</p>
            <div class="row">
                <div class="col-sm-4 hide @if (!session('business.enable_price_tax')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
                        {!! Form::select(
                            'tax',
                            $taxes,
                            $product->tax,
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
                            $product->tax_type,
                            ['class' => 'form-control select2', 'required'],
                        ) !!}
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-12">
                    <div class="price-tiers-banner" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px;">
                        <strong style="color: #92400e;"><i class="fas fa-tags" style="margin-right:6px;"></i>Price Tiers</strong>
                        <p style="margin: 6px 0 0; font-size: 13px; color: #78350f;">Configure wholesale/retail price levels (Silver, Gold, Platinum) after saving. Use the pricing section below to set cost and selling prices.</p>
                    </div>
                </div>

                <div class="col-sm-12 product-type-section" id="section-variants">
                    <span class="section-label">@lang('product.product_type') &amp; variations</span>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
                                {!! Form::select('type', $product_types, $product->type, [
                                    'class' => 'form-control select2',
                                    'required',
                                    'data-action' => 'edit',
                                    'data-product_id' => $product->id,
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12" id="product_form_part"></div>
                </div>
                <input type="hidden" id="variation_counter" value="0">
                <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
            </div>
        @endcomponent

        {{-- Product Source & Fulfillment Settings Section --}}
        @if(Module::has('Woocommerce') && Module::find('Woocommerce')->isEnabled())
        @php
            // Determine current fulfillment type based on product data
            $currentVendor = $product->vendors->first();
            $currentSourceType = $product->product_source_type ?? 'in_house';
            $currentFulfillmentType = 'in_house';
            
            if ($currentSourceType === 'dropshipped' && $currentVendor) {
                $currentFulfillmentType = $currentVendor->vendor_type === 'woocommerce' ? 'dropship_woocommerce' : 'dropship_erp';
            } elseif ($currentSourceType === 'dropshipped') {
                $currentFulfillmentType = 'dropshipped'; // Fallback
            }
            
            // Get all dropship-capable vendors grouped by type
            $allVendors = \App\Models\WpVendor::where(function($q) {
                    $q->where('business_id', session('user.business_id'))
                      ->orWhereNull('business_id');
                })
                ->dropshipCapable()
                ->active()
                ->get();
            $wooVendors = $allVendors->where('vendor_type', 'woocommerce');
            $erpDropshipVendors = $allVendors->where('vendor_type', 'erp_dropship');
        @endphp
        
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Product Fulfillment Type', 'title_svg' => '<i class="fas fa-shipping-fast"></i>'])
            
            {{-- Current Status Banner --}}
            <div class="row tw-mb-4">
                <div class="col-md-12">
                    <div class="alert {{ $currentFulfillmentType === 'in_house' ? 'alert-success' : ($currentFulfillmentType === 'dropship_woocommerce' ? 'alert-info' : 'alert-warning') }}" style="margin-bottom: 0;">
                        <strong><i class="fas fa-info-circle"></i> Current Status:</strong>
                        @if($currentFulfillmentType === 'in_house')
                            <span class="label label-success"><i class="fas fa-warehouse"></i> In-House</span> 
                            This product is managed and fulfilled by your ERP inventory.
                        @elseif($currentFulfillmentType === 'dropship_woocommerce')
                            <span class="label label-info"><i class="fas fa-globe"></i> Dropship - WooCommerce</span>
                            Fulfilled by: <strong>{{ $currentVendor->name ?? 'N/A' }}</strong> (syncs to WooCommerce/Duoplane)
                        @elseif($currentFulfillmentType === 'dropship_erp')
                            <span class="label label-warning"><i class="fas fa-user-tie"></i> Dropship - ERP Portal</span>
                            Fulfilled by: <strong>{{ $currentVendor->name ?? 'N/A' }}</strong> (vendor uses ERP portal)
                        @else
                            <span class="label label-default">Dropshipped</span> 
                            No vendor assigned yet.
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Fulfillment Type: <span class="text-danger">*</span></label>
                        <select name="fulfillment_type" id="fulfillment_type" class="form-control">
                            <option value="in_house" {{ $currentFulfillmentType === 'in_house' ? 'selected' : '' }}>
                                🏭 In-House (ERP Managed)
                            </option>
                            <option value="dropship_woocommerce" {{ $currentFulfillmentType === 'dropship_woocommerce' ? 'selected' : '' }} {{ $wooVendors->isEmpty() ? 'disabled' : '' }}>
                                🌐 Dropship - WooCommerce Vendor {{ $wooVendors->isEmpty() ? '(No vendors)' : '' }}
                            </option>
                            <option value="dropship_erp" {{ $currentFulfillmentType === 'dropship_erp' ? 'selected' : '' }} {{ $erpDropshipVendors->isEmpty() ? 'disabled' : '' }}>
                                👤 Dropship - ERP Portal Vendor {{ $erpDropshipVendors->isEmpty() ? '(No vendors)' : '' }}
                            </option>
                        </select>
                        <input type="hidden" name="product_source_type" id="product_source_type" value="{{ $currentSourceType }}">
                    </div>
                </div>

                {{-- WooCommerce Vendor Selection --}}
                <div class="col-md-4 vendor-selection woo-vendor-selection" style="{{ $currentFulfillmentType !== 'dropship_woocommerce' ? 'display:none;' : '' }}">
                    <div class="form-group">
                        <label>WooCommerce Vendor: <span class="text-danger">*</span></label>
                        <select name="woo_vendor_id" id="woo_vendor_id" class="form-control select2">
                            <option value="">-- Select WooCommerce Vendor --</option>
                            @foreach($wooVendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $currentVendor && $currentVendor->id == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }} {{ $vendor->company_name ? '(' . $vendor->company_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block text-muted" style="font-size: 11px;">
                            <i class="fas fa-sync"></i> Orders sync to WooCommerce → Duoplane for fulfillment
                        </p>
                    </div>
                </div>
                
                {{-- ERP Dropship Vendor Selection --}}
                <div class="col-md-4 vendor-selection erp-vendor-selection" style="{{ $currentFulfillmentType !== 'dropship_erp' ? 'display:none;' : '' }}">
                    <div class="form-group">
                        <label>ERP Dropship Vendor: <span class="text-danger">*</span></label>
                        <select name="erp_vendor_id" id="erp_vendor_id" class="form-control select2">
                            <option value="">-- Select ERP Dropship Vendor --</option>
                            @foreach($erpDropshipVendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $currentVendor && $currentVendor->id == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }} {{ $vendor->company_name ? '(' . $vendor->company_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block text-muted" style="font-size: 11px;">
                            <i class="fas fa-desktop"></i> Vendor logs into ERP portal to fulfill orders
                        </p>
                    </div>
                </div>
                
                {{-- Hidden field for actual vendor ID --}}
                <input type="hidden" name="dropship_vendor_id" id="dropship_vendor_id" value="{{ $currentVendor->id ?? '' }}">
                
                {{-- Fulfillment Type Description --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div id="fulfillment-description" class="well well-sm" style="margin-bottom: 0; font-size: 12px;">
                            @if($currentFulfillmentType === 'in_house')
                                <strong>🏭 In-House:</strong> Stock is managed in ERP. Orders are fulfilled from your warehouse.
                            @elseif($currentFulfillmentType === 'dropship_woocommerce')
                                <strong>🌐 WooCommerce:</strong> Orders sync to WooCommerce. Vendor fulfills via Duoplane integration.
                            @elseif($currentFulfillmentType === 'dropship_erp')
                                <strong>👤 ERP Portal:</strong> Vendor logs into ERP to view and fulfill assigned orders directly.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Dropship Pricing Section (shown for both dropship types) --}}
            <div class="row dropship-fields" style="{{ $currentSourceType !== 'dropshipped' ? 'display:none;' : '' }}">
                <div class="col-md-12">
                    <hr style="margin: 10px 0;">
                    <h5><i class="fas fa-dollar-sign"></i> Vendor Pricing</h5>
                </div>
            </div>
            
            <div class="row dropship-fields" style="{{ $currentSourceType !== 'dropshipped' ? 'display:none;' : '' }}">
                @php
                    $pivotData = $currentVendor ? $currentVendor->pivot : null;
                @endphp

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Vendor Cost Price:</label>
                        <input type="number" step="0.01" name="vendor_cost_price" id="vendor_cost_price" 
                               class="form-control" placeholder="0.00"
                               value="{{ $pivotData->vendor_cost_price ?? '' }}">
                        <p class="help-block text-muted" style="font-size: 11px;">Vendor's price to you</p>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Markup %:</label>
                        <input type="number" step="0.01" name="vendor_markup_percentage" id="vendor_markup_percentage" 
                               class="form-control" placeholder="0.00"
                               value="{{ $pivotData->vendor_markup_percentage ?? '' }}">
                        <p class="help-block text-muted" style="font-size: 11px;">Your profit margin</p>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Selling Price:</label>
                        <input type="number" step="0.01" name="dropship_selling_price" id="dropship_selling_price" 
                               class="form-control" placeholder="0.00" readonly
                               value="{{ $pivotData->dropship_selling_price ?? '' }}"
                               style="background: #f5f5f5; font-weight: bold;">
                        <p class="help-block text-muted" style="font-size: 11px;">Auto-calculated</p>
                    </div>
                </div>
            </div>

            <div class="row dropship-fields" style="{{ $currentSourceType !== 'dropshipped' ? 'display:none;' : '' }}">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vendor SKU:</label>
                        <input type="text" name="vendor_sku" id="vendor_sku" class="form-control" 
                               placeholder="Vendor's SKU"
                               value="{{ $pivotData->vendor_sku ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Lead Time (Days):</label>
                        <input type="number" name="lead_time_days" id="lead_time_days" class="form-control" 
                               placeholder="0"
                               value="{{ $pivotData->lead_time_days ?? 0 }}">
                    </div>
                </div>
            </div>
        @endcomponent
        @endif

        <div class="row">
            <input type="hidden" name="submit_type" id="submit_type">
            <div class="col-sm-12">
                <div class="text-center">
                    <div class="btn-group">
                        @if ($selling_price_group_count)
                            <button type="submit" value="submit_n_add_selling_prices"
                                class="tw-dw-btn tw-dw-btn-warning tw-text-white tw-dw-btn-lg submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                        @endif

                        {{-- @can('product.opening_stock')
              <button type="submit" @if (empty($product->enable_stock)) disabled="true" @endif id="opening_stock_button"  value="update_n_edit_opening_stock" class="tw-dw-btn tw-text-white tw-dw-btn-lg bg-purple submit_product_form">@lang('lang_v1.update_n_edit_opening_stock')</button>
              @endif --}}

                        <button type="submit" value="save_n_add_another"
                            class="tw-dw-btn tw-text-white tw-dw-btn-lg bg-maroon submit_product_form">@lang('lang_v1.update_n_add_another')</button>

                        <button type="submit" value="submit"
                            class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-lg submit_product_form">@lang('messages.update')</button>

                        @if(Module::has('Woocommerce') && Module::find('Woocommerce')->isEnabled())
                            @if($product->woocommerce_product_id)
                                <button type="button" id="sync-to-woo-btn"
                                    class="tw-dw-btn tw-text-white tw-dw-btn-lg" style="background: #96588a;" title="Re-sync to WooCommerce">
                                    <i class="fas fa-sync"></i> Sync to WooCommerce
                                </button>
                            @else
                                <button type="button" id="sync-to-woo-btn"
                                    class="tw-dw-btn tw-text-white tw-dw-btn-lg" style="background: #96588a;" title="Push to WooCommerce">
                                    <i class="fab fa-wordpress"></i> Push to WooCommerce
                                </button>
                            @endif
                        @endif
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

            if ($('#upload_image').data('fileinput')) {
                $('#upload_image').fileinput('destroy');
            }

            $('#upload_image').fileinput({
                showUpload: false,
                showRemove: true,
                overwriteInitial: true,
                initialPreviewAsData: true,
                initialPreview: [
                    "{{ $product->image_url }}"
                ],
                initialPreviewConfig: [{
                    caption: "{{ basename($product->image_url) }}",
                    url: "{{ url('/products/delete-media/' . $product->id) }}",
                    width: "120px",
                    key: 1
                }],
                allowedFileExtensions: ["jpg", "jpeg", "png", "gif"],
                maxFileSize: 5120,
                defaultPreviewContent: '<div class="file-preview-frame krajee-default  kv-preview-thumb" id="preview-1747636814017-0" data-fileindex="0" data-template="image"><div class="kv-file-content"><img src="{{ asset('img/default.png') }}"class="file-preview-image kv-preview-data" title="default.png" alt="default.png" style="width:auto;height:160px;"></div><div class="file-thumbnail-footer"><div class="file-footer-caption" title="default.png">default.png<br></div><div class="file-upload-indicator" title="Not uploaded yet"><i class="glyphicon glyphicon-hand-down text-warning"></i></div> <div class="file-actions"></div></div></div>',
                // defaultPreviewContent: '<img src="{{ asset('img/default.png') }}" alt="Default Image" style="width:100%;">'
            });

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
            // setTimeout(function() {
            // $('#category_id').val(@json($product->category_id)).trigger('change');
            // }, 1);
            // setTimeout(function() {
            // $('#sub_category_id').val(@json($product->sub_category_id)).trigger('change');
            //  }, 2000);

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

            // Create variation instantly when a new variation row is added (for edit page only)
            $(document).ajaxSuccess(function(event, xhr, settings) {
                if (settings.url && settings.url.includes('/products/get_variation_value_row')) {
                    // New variation row was added via AJAX
                    var product_id = $('#product_id').val();
                    if (product_id) {
                        setTimeout(function() {
                            // Find the newly added row (last row in the table)
                            $('table.variation_value_table').each(function() {
                                var $table = $(this);
                                var $rows = $table.find('tr.variation_value_row');
                                if ($rows.length > 0) {
                                    var $newRow = $rows.last();
                                    
                                    // Check if this row doesn't have a variation_id yet
                                    if ($newRow.find('.row_variation_id').length === 0 && !$newRow.data('variation-creating')) {
                                        createVariationForNewRow($newRow, product_id);
                                    }
                                }
                            });
                        }, 300);
                    }
                }
            });

            function createVariationForNewRow($row, product_id) {
                // Mark as being processed to avoid duplicate calls
                $row.data('variation-creating', true);

                // Extract variation data from the row
                var variation_data = {};
                var product_variation_id = null;
                var row_index = null;

                // Get product variation row (parent)
                var $productVariationRow = $row.closest('table').closest('td').closest('.variation_row');
                if ($productVariationRow.length) {
                    row_index = $productVariationRow.find('.row_index').val();
                    if (row_index) {
                        // In edit mode, row_index is the product_variation_id
                        product_variation_id = row_index;
                    }
                }

                // Extract variation data from form inputs (use defaults if empty)
                $row.find('input[type="text"], input[type="number"]').each(function() {
                    var $field = $(this);
                    var name = $field.attr('name');
                    if (name && (name.includes('[variations]') || name.includes('[variations_edit]'))) {
                        var matches = name.match(/\[variations(?:_edit)?\]\[[^\]]+\]\[([^\]]+)\]/);
                        if (matches && matches[1]) {
                            var key = matches[1];
                            var value = $field.val();
                            
                            if (key === 'sub_sku' || key === 'var_barcode_no' || key === 'var_maxSaleLimit' || 
                                key === 'value' || key === 'default_purchase_price' || key === 'dpp_inc_tax' ||
                                key === 'profit_percent' || key === 'default_sell_price' || key === 'sell_price_inc_tax') {
                                variation_data[key] = value;
                            }
                        }
                    }
                });

                // Get variation name (use default if empty)
                if (!variation_data.variation_name && variation_data.value) {
                    variation_data.variation_name = variation_data.value;
                }
                if (!variation_data.variation_name) {
                    variation_data.variation_name = $row.find('.variation_value_name').val() || 'New Variation';
                }

                // Get SKU type from form
                var sku_type = $('input[name="sku_type"]:checked').val() || 'with_out_variation';
                if (!sku_type) {
                    // Try to find it in the form
                    var $skuTypeInput = $('input[name="sku_type"]');
                    if ($skuTypeInput.length) {
                        sku_type = $skuTypeInput.val() || 'with_out_variation';
                    } else {
                        sku_type = 'with_out_variation';
                    }
                }

                // Create variation via API (even with minimal data)
                var formData = new FormData();
                formData.append('product_id', product_id);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('sku_type', sku_type);

                if (product_variation_id) {
                    formData.append('product_variation_id', product_variation_id);
                }

                // Add variation data (use defaults if empty)
                formData.append('variation_name', variation_data.variation_name || 'New Variation');
                // Don't send sub_sku if empty - let backend generate it
                if (variation_data.sub_sku && variation_data.sub_sku.trim() !== '') {
                    formData.append('sub_sku', variation_data.sub_sku);
                }
                if (variation_data.var_barcode_no) {
                    formData.append('var_barcode_no', variation_data.var_barcode_no);
                }
                if (variation_data.var_maxSaleLimit) {
                    formData.append('var_maxSaleLimit', variation_data.var_maxSaleLimit);
                }
                if (variation_data.default_purchase_price) {
                    formData.append('default_purchase_price', variation_data.default_purchase_price);
                }
                if (variation_data.dpp_inc_tax) {
                    formData.append('dpp_inc_tax', variation_data.dpp_inc_tax);
                }
                if (variation_data.profit_percent) {
                    formData.append('profit_percent', variation_data.profit_percent);
                }
                if (variation_data.default_sell_price) {
                    formData.append('default_sell_price', variation_data.default_sell_price);
                }
                if (variation_data.sell_price_inc_tax) {
                    formData.append('sell_price_inc_tax', variation_data.sell_price_inc_tax);
                }

                $.ajax({
                    url: '/products/create-variation-instantly',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Add hidden input with variation_id
                            if ($row.find('.row_variation_id').length === 0) {
                                $row.prepend('<input type="hidden" class="row_variation_id" value="' + response.variation_id + '">');
                            } else {
                                $row.find('.row_variation_id').val(response.variation_id);
                            }

                        // Convert this row to an existing variation so it posts as variations_edit
                        setRowToExistingVariation($row, response.variation_id);
                            
                            // Update the SKU field with generated SKU
                            if (response.sub_sku) {
                                var $skuInput = $row.find('.input_sub_sku');
                                if ($skuInput.length && !$skuInput.val()) {
                                    $skuInput.val(response.sub_sku);
                                }
                            }

                            // Mark as created
                            $row.data('variation-created', true);
                            $row.data('variation-creating', false);
                        } else {
                            $row.data('variation-creating', false);
                            toastr.error(response.msg || 'Error creating variation');
                        }
                    },
                    error: function(xhr) {
                        $row.data('variation-creating', false);
                        
                        var errorMsg = 'Error creating variation';
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            errorMsg = xhr.responseJSON.msg;
                        } else if (xhr.responseText) {
                            try {
                                var error = JSON.parse(xhr.responseText);
                                if (error.msg) {
                                    errorMsg = error.msg;
                                }
                            } catch(e) {}
                        }
                        toastr.error(errorMsg);
                    }
                });
            }

            // Instant variation image upload (for edit page only)
            $(document).on('change', '.variation_images', function(e) {
                var $input = $(this);
                var files = this.files;
                
                if (!files || files.length === 0) {
                    return;
                }

                // Get product ID
                var product_id = $('#product_id').val();
                if (!product_id) {
                    toastr.error('Product ID not found');
                    return;
                }

                // Find the closest variation row to get variation data
                var $row = $input.closest('tr');
                var variation_id = null;
                var product_variation_id = null;
                var row_index = null;
                var variation_data = {};

                // Check if this is an existing variation (has variation_id)
                var $variationIdInput = $row.find('.row_variation_id').first();
                if ($variationIdInput.length && $variationIdInput.val()) {
                    // Variation already exists - use it directly
                    variation_id = $variationIdInput.val();
                } else {
                    // This is a new variation that hasn't been created yet
                    // First, try to create it, then upload the image
                    // Get the product variation row (parent row)
                    var $productVariationRow = $row.closest('table').closest('td').closest('.variation_row');
                    if ($productVariationRow.length) {
                        row_index = $productVariationRow.find('.row_index').val();
                        if (!row_index) {
                            // Try to get from the name attribute of variation name input
                            var $nameInput = $productVariationRow.find('input.variation_name');
                            if ($nameInput.length) {
                                var nameAttr = $nameInput.attr('name');
                                if (nameAttr) {
                                    var match = nameAttr.match(/\[(\d+)\]/);
                                    if (match) {
                                        row_index = match[1];
                                        // In edit mode, row_index is the product_variation_id
                                        product_variation_id = match[1];
                                    }
                                }
                            }
                        } else {
                            // In edit mode, row_index is the product_variation_id
                            product_variation_id = row_index;
                        }
                    }

                    // Extract variation data from form inputs in the current row
                    $row.find('input[type="text"], input[type="number"]').each(function() {
                        var $field = $(this);
                        var name = $field.attr('name');
                        if (name && (name.includes('[variations]') || name.includes('[variations_edit]'))) {
                            // Extract field name from the name attribute
                            var matches = name.match(/\[variations(?:_edit)?\]\[[^\]]+\]\[([^\]]+)\]/);
                            if (matches && matches[1]) {
                                var key = matches[1];
                                var value = $field.val();
                                
                                if (key === 'sub_sku' || key === 'var_barcode_no' || key === 'var_maxSaleLimit' || 
                                    key === 'value' || key === 'default_purchase_price' || key === 'dpp_inc_tax' ||
                                    key === 'profit_percent' || key === 'default_sell_price' || key === 'sell_price_inc_tax') {
                                    variation_data[key] = value;
                                }
                            }
                        }
                    });

                    // Get variation name (value field)
                    if (!variation_data.variation_name && variation_data.value) {
                        variation_data.variation_name = variation_data.value;
                    }
                    if (!variation_data.variation_name) {
                        variation_data.variation_name = $row.find('.variation_value_name').val() || 'New Variation';
                    }
                }

                // If variation_id exists, upload directly
                // Otherwise, create variation first, then upload
                if (variation_id) {
                    // Variation exists - upload image directly
                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];
                        uploadVariationImage(file, product_id, variation_id, product_variation_id, row_index, variation_data, $input, $row);
                    }
                } else {
                    // Variation doesn't exist - create it first, then upload image
                    createVariationAndUploadImage(files, product_id, product_variation_id, row_index, variation_data, $input, $row);
                }
            });

            function createVariationAndUploadImage(files, product_id, product_variation_id, row_index, variation_data, $input, $row) {
                // Show loading indicator for variation creation
                var $createLoadingIndicator = $('<span class="fa fa-spinner fa-spin" style="margin-left: 5px; color: #3c8dbc; font-size: 12px;" data-indicator="create"></span>');
                $input.after($createLoadingIndicator);

                // First create the variation
                var formData = new FormData();
                formData.append('product_id', product_id);
                formData.append('_token', '{{ csrf_token() }}');

                if (product_variation_id) {
                    formData.append('product_variation_id', product_variation_id);
                }

                // Add variation data
                formData.append('variation_name', variation_data.variation_name || 'New Variation');
                if (variation_data.sub_sku) {
                    formData.append('sub_sku', variation_data.sub_sku);
                }
                if (variation_data.var_barcode_no) {
                    formData.append('var_barcode_no', variation_data.var_barcode_no);
                }
                if (variation_data.var_maxSaleLimit) {
                    formData.append('var_maxSaleLimit', variation_data.var_maxSaleLimit);
                }
                if (variation_data.default_purchase_price) {
                    formData.append('default_purchase_price', variation_data.default_purchase_price);
                }
                if (variation_data.dpp_inc_tax) {
                    formData.append('dpp_inc_tax', variation_data.dpp_inc_tax);
                }
                if (variation_data.profit_percent) {
                    formData.append('profit_percent', variation_data.profit_percent);
                }
                if (variation_data.default_sell_price) {
                    formData.append('default_sell_price', variation_data.default_sell_price);
                }
                if (variation_data.sell_price_inc_tax) {
                    formData.append('sell_price_inc_tax', variation_data.sell_price_inc_tax);
                }

                $.ajax({
                    url: '/products/create-variation-instantly',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Remove variation creation loading indicator before starting image upload
                        $createLoadingIndicator.remove();
                        
                        if (response.success) {
                            // Add hidden input with variation_id
                            if ($row.find('.row_variation_id').length === 0) {
                                $row.prepend('<input type="hidden" class="row_variation_id" value="' + response.variation_id + '">');
                            } else {
                                $row.find('.row_variation_id').val(response.variation_id);
                            }

                            // Convert this row to an existing variation so it posts as variations_edit
                            setRowToExistingVariation($row, response.variation_id);
                            
                            // Update the SKU field with generated SKU
                            if (response.sub_sku) {
                                var $skuInput = $row.find('.input_sub_sku');
                                if ($skuInput.length && !$skuInput.val()) {
                                    $skuInput.val(response.sub_sku);
                                }
                            }
                            
                            // Update the file input name to use variation_id
                            var newName = 'edit_variation_images_' + (row_index || 0) + '_' + response.variation_id + '[]';
                            $input.attr('name', newName);

                            // Now upload the images (this will add its own loading indicator)
                            for (var i = 0; i < files.length; i++) {
                                var file = files[i];
                                uploadVariationImage(file, product_id, response.variation_id, product_variation_id, row_index, variation_data, $input, $row);
                            }
                        } else {
                            toastr.error(response.msg || 'Error creating variation');
                        }
                    },
                    error: function(xhr) {
                        // Remove variation creation loading indicator on error
                        $createLoadingIndicator.remove();
                        
                        var errorMsg = 'Error creating variation';
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            errorMsg = xhr.responseJSON.msg;
                        }
                        toastr.error(errorMsg);
                    }
                });
            }

            // Helper: after a variation is created, switch this row to use variations_edit with real variation_id
            function setRowToExistingVariation($row, variation_id) {
                // Update hidden variation_row_index value
                $row.find('.variation_row_index').val(variation_id);

                // Update input/select/textarea names from variations[...] to variations_edit[variation_id][...]
                $row.find('input, select, textarea').each(function() {
                    var $field = $(this);
                    var name = $field.attr('name');
                    if (!name) return;

                    // Replace the first occurrence of [variations][something] with [variations_edit][variation_id]
                    var newName = name.replace(/\[variations\]\[[^\]]+\]/, '[variations_edit][' + variation_id + ']');
                    $field.attr('name', newName);
                });

                // Update file input name for future uploads
                var $fileInput = $row.find('.variation_images');
                if ($fileInput.length) {
                    var parentRowIndex = $row.closest('.variation_row').find('.row_index').val() || 0;
                    var newFileName = 'edit_variation_images_' + parentRowIndex + '_' + variation_id + '[]';
                    $fileInput.attr('name', newFileName);
                }
            }

            function uploadVariationImage(file, product_id, variation_id, product_variation_id, row_index, variation_data, $input, $row) {
                var formData = new FormData();
                formData.append('image', file);
                formData.append('product_id', product_id);
                formData.append('_token', '{{ csrf_token() }}');

                if (variation_id) {
                    // Existing variation - just upload image
                    formData.append('variation_id', variation_id);
                } else {
                    // New variation - add all necessary data
                    if (product_variation_id) {
                        formData.append('product_variation_id', product_variation_id);
                    }
                    if (row_index !== null && row_index !== undefined) {
                        formData.append('row_index', row_index);
                    }
                    
                    // Add variation data
                    if (variation_data.variation_name) {
                        formData.append('variation_name', variation_data.variation_name);
                    }
                    if (variation_data.sub_sku) {
                        formData.append('sub_sku', variation_data.sub_sku);
                    }
                    if (variation_data.var_barcode_no) {
                        formData.append('var_barcode_no', variation_data.var_barcode_no);
                    }
                    if (variation_data.var_maxSaleLimit) {
                        formData.append('var_maxSaleLimit', variation_data.var_maxSaleLimit);
                    }
                    if (variation_data.default_purchase_price) {
                        formData.append('default_purchase_price', variation_data.default_purchase_price);
                    }
                    if (variation_data.dpp_inc_tax) {
                        formData.append('dpp_inc_tax', variation_data.dpp_inc_tax);
                    }
                    if (variation_data.profit_percent) {
                        formData.append('profit_percent', variation_data.profit_percent);
                    }
                    if (variation_data.default_sell_price) {
                        formData.append('default_sell_price', variation_data.default_sell_price);
                    }
                    if (variation_data.sell_price_inc_tax) {
                        formData.append('sell_price_inc_tax', variation_data.sell_price_inc_tax);
                    }
                }

                // Show loading indicator for image upload
                // Remove any existing upload loading indicators first to avoid duplicates
                $input.siblings('span[data-indicator="upload"]').remove();
                var $loadingIndicator = $('<span class="fa fa-spinner fa-spin" style="margin-left: 5px; color: #3c8dbc; font-size: 12px;" data-indicator="upload"></span>');
                $input.after($loadingIndicator);

                $.ajax({
                    url: '/products/upload-variation-image-instantly',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Remove image upload loading indicator
                        $loadingIndicator.remove();
                        
                        if (response.success) {
                            toastr.success(response.msg || 'Image uploaded successfully');
                            
                            // If this was a new variation, update the form with the variation_id
                            if (!variation_id && response.variation_id) {
                                // Add hidden input with variation_id
                                if ($row.find('.row_variation_id').length === 0) {
                                    $row.prepend('<input type="hidden" class="row_variation_id" value="' + response.variation_id + '">');
                                } else {
                                    $row.find('.row_variation_id').val(response.variation_id);
                                }
                                
                                // Update the file input name to use variation_id for future uploads
                                var newName = 'edit_variation_images_' + (row_index || 0) + '_' + response.variation_id + '[]';
                                $input.attr('name', newName);
                            }

                            // Display uploaded images
                            if (response.images && response.images.length > 0) {
                                var $imageContainer = $input.closest('td');
                                
                                // Get all existing media IDs to avoid duplicates
                                var existingMediaIds = [];
                                $imageContainer.find('div.img-thumbnail[data-media-id]').each(function() {
                                    existingMediaIds.push($(this).attr('data-media-id'));
                                });
                                
                                response.images.forEach(function(image) {
                                    // Check if this image is already displayed (avoid duplicates)
                                    if (existingMediaIds.indexOf(String(image.id)) === -1 && image.thumbnail) {
                                        // Create image div matching the server-side format
                                        var $imgDiv = $('<div class="img-thumbnail" data-media-id="' + image.id + '" style="display: inline-block; margin: 5px; position: relative;">' + 
                                            '<span class="badge bg-red delete-media" data-href="/delete-media/' + image.id + '" style="cursor: pointer; position: absolute; top: 0; right: 0; z-index: 10; background-color: #dd4b39; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;"><i class="fas fa-times"></i></span>' +
                                            image.thumbnail + 
                                            '</div>');
                                        
                                        // Insert before the file input (after any existing images)
                                        $input.before($imgDiv);
                                        
                                        // Add to existing IDs list to prevent duplicates in same response
                                        existingMediaIds.push(String(image.id));
                                    }
                                });
                            }
                            
                            // Clear the file input after successful upload
                            $input.val('');
                        } else {
                            toastr.error(response.msg || 'Error uploading image');
                        }
                    },
                    error: function(xhr) {
                        $loadingIndicator.remove();
                        var errorMsg = 'Error uploading image';
                        if (xhr.responseJSON && xhr.responseJSON.msg) {
                            errorMsg = xhr.responseJSON.msg;
                        } else if (xhr.responseText) {
                            try {
                                var error = JSON.parse(xhr.responseText);
                                if (error.msg) {
                                    errorMsg = error.msg;
                                }
                            } catch(e) {}
                        }
                        toastr.error(errorMsg);
                    }
                });
            }

            // ======== FULFILLMENT TYPE HANDLERS ========
            // Handle fulfillment type change
            $('#fulfillment_type').on('change', function() {
                var fulfillmentType = $(this).val();
                
                // Update hidden product_source_type field
                if (fulfillmentType === 'in_house') {
                    $('#product_source_type').val('in_house');
                    $('.dropship-fields').slideUp();
                    $('.vendor-selection').hide();
                    $('#dropship_vendor_id').val('');
                } else {
                    $('#product_source_type').val('dropshipped');
                    $('.dropship-fields').slideDown();
                    
                    // Show appropriate vendor selection
                    if (fulfillmentType === 'dropship_woocommerce') {
                        $('.woo-vendor-selection').show();
                        $('.erp-vendor-selection').hide();
                        // Copy WooCommerce vendor to main field
                        $('#dropship_vendor_id').val($('#woo_vendor_id').val());
                    } else if (fulfillmentType === 'dropship_erp') {
                        $('.woo-vendor-selection').hide();
                        $('.erp-vendor-selection').show();
                        // Copy ERP vendor to main field
                        $('#dropship_vendor_id').val($('#erp_vendor_id').val());
                    }
                }
                
                // Update description
                updateFulfillmentDescription(fulfillmentType);
            });
            
            // Sync vendor selections to hidden field
            $('#woo_vendor_id').on('change', function() {
                $('#dropship_vendor_id').val($(this).val());
            });
            
            $('#erp_vendor_id').on('change', function() {
                $('#dropship_vendor_id').val($(this).val());
            });
            
            // Update fulfillment description
            function updateFulfillmentDescription(type) {
                var descriptions = {
                    'in_house': '<strong>🏭 In-House:</strong> Stock is managed in ERP. Orders are fulfilled from your warehouse.',
                    'dropship_woocommerce': '<strong>🌐 WooCommerce:</strong> Orders sync to WooCommerce. Vendor fulfills via Duoplane integration.',
                    'dropship_erp': '<strong>👤 ERP Portal:</strong> Vendor logs into ERP to view and fulfill assigned orders directly.'
                };
                $('#fulfillment-description').html(descriptions[type] || '');
            }

            // Calculate selling price based on cost and markup
            function calculateDropshipSellingPrice() {
                var costPrice = parseFloat($('#vendor_cost_price').val()) || 0;
                var markupPercentage = parseFloat($('#vendor_markup_percentage').val()) || 0;
                
                if (costPrice > 0) {
                    var markupAmount = costPrice * (markupPercentage / 100);
                    var sellingPrice = costPrice + markupAmount;
                    $('#dropship_selling_price').val(sellingPrice.toFixed(2));
                } else {
                    $('#dropship_selling_price').val('');
                }
            }

            $('#vendor_cost_price, #vendor_markup_percentage').on('input change', function() {
                calculateDropshipSellingPrice();
            });

            // Trigger initial calculation
            calculateDropshipSellingPrice();

            // WooCommerce Sync Button Handler
            $('#sync-to-woo-btn').on('click', function() {
                var btn = $(this);
                var originalText = btn.html();
                var productId = $('#product_id').val();
                var syncUrl = '/woocommerce/sync-product-to-woo/' + productId;
                
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
                
                $.ajax({
                    url: syncUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Product synced to WooCommerce successfully!');
                            if (response.woocommerce_product_id) {
                                btn.html('<i class="fas fa-check"></i> Synced #' + response.woocommerce_product_id);
                                setTimeout(function() {
                                    btn.html('<i class="fas fa-sync"></i> Sync to WooCommerce');
                                    btn.prop('disabled', false);
                                }, 2000);
                            } else {
                                btn.html(originalText);
                                btn.prop('disabled', false);
                            }
                        } else {
                            toastr.error(response.message || 'Failed to sync product');
                            btn.html(originalText);
                            btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error syncing product';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>

@endsection

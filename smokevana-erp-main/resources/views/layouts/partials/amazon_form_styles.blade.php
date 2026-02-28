{{-- Amazon Form Modal Styles - Shared across all forms --}}
<style>
  /* Amazon Form Modal - Standard styling for all forms */
  .amazon-form-modal { box-sizing: border-box; }
  .amazon-form-modal .modal-content,
  .amazon-form-modal .modal-body,
  .amazon-form-modal .amazon-form-card .form-control { box-sizing: border-box; }
  .amazon-form-modal .modal-content { border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.2); }
  .amazon-form-modal .modal-header {
    background: #37475a;
    color: #fff;
    padding: 1rem 1.25rem;
    border-bottom: none;
    flex-shrink: 0;
  }
  .amazon-form-modal .modal-header .modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
  .amazon-form-modal .modal-header .close { color: #fff; opacity: 0.9; text-shadow: none; margin-top: -0.25rem; }
  .amazon-form-modal .modal-body {
    background: #37475a;
    padding: 1rem 1.25rem;
    max-height: min(85vh, 720px);
    overflow-y: auto;
    overflow-x: hidden;
  }
  .amazon-form-modal .modal-footer {
    background: #37475a;
    border-top: 1px solid rgba(255,255,255,0.15);
    padding: 0.75rem 1.25rem;
    flex-shrink: 0;
  }

  /* Cards - white fields on Amazon background */
  .amazon-form-modal .amazon-form-card {
    background: #fff;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }
  .amazon-form-modal .amazon-form-card-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #232F3E;
    margin: 0 0 0.75rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #D5D9D9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .amazon-form-modal .amazon-form-card-title i { color: #FF9900; }

  /* Form groups - consistent spacing */
  .amazon-form-modal .amazon-form-card .form-group {
    margin-bottom: 0.75rem;
  }
  .amazon-form-modal .amazon-form-card .form-group:last-child,
  .amazon-form-modal .amazon-form-card .row:last-child .form-group { margin-bottom: 0; }
  .amazon-form-modal .amazon-form-card label,
  .amazon-form-modal .amazon-form-card .control-label,
  .amazon-form-modal .amazon-form-card .help-block,
  .amazon-form-modal .amazon-form-card .text-muted {
    color: #0F1111 !important;
    font-size: 0.8125rem;
  }
  .amazon-form-modal .amazon-form-card .help-block { margin: 0.25rem 0 0; color: #565959 !important; font-size: 0.75rem; }
  .amazon-form-modal .amazon-form-card .form-control {
    background: #fff;
    border: 1px solid #D5D9D9;
    color: #0F1111;
    font-size: 0.8125rem;
    padding: 0.375rem 0.5rem;
    min-height: 2rem;
    max-width: 100%;
    box-sizing: border-box;
  }
  .amazon-form-modal .amazon-form-card .form-control:focus {
    border-color: #FF9900;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
  }
  .amazon-form-modal .amazon-form-card .input-group-addon {
    background: #F7F8F8;
    color: #232F3E;
    border-color: #D5D9D9;
    font-size: 0.8125rem;
    padding: 0.375rem 0.5rem;
    min-width: 2.25rem;
  }
  .amazon-form-modal .amazon-form-card .input-group .form-control { border-left-color: #D5D9D9; }
  
  /* Select2 styling */
  .amazon-form-modal .amazon-form-card .select2-container--default .select2-selection--single {
    border: 1px solid #D5D9D9 !important;
    border-radius: 0 4px 4px 0 !important;
    height: 2rem;
  }
  .amazon-form-modal .amazon-form-card .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2rem;
    padding-left: 0.5rem;
    font-size: 0.8125rem;
  }
  .amazon-form-modal .amazon-form-card .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #FF9900 !important;
    box-shadow: 0 0 0 2px rgba(255,153,0,0.2);
  }
  .amazon-form-modal .amazon-form-card .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #FF9900 !important;
    color: #fff !important;
  }

  /* Row gaps inside cards */
  .amazon-form-modal .amazon-form-card .row { margin-left: -0.375rem; margin-right: -0.375rem; }
  .amazon-form-modal .amazon-form-card .row > [class*="col-"] { padding-left: 0.375rem; padding-right: 0.375rem; }

  /* Buttons - Amazon orange */
  .amazon-form-modal .modal-footer .btn-primary,
  .amazon-form-modal .modal-footer .btn-primary:hover,
  .amazon-form-modal .modal-footer .tw-dw-btn-primary,
  .amazon-form-modal .modal-footer .tw-dw-btn-primary:hover {
    background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
    border-color: #C7511F !important;
    color: #fff !important;
    font-weight: 500;
    padding: 0.375rem 1rem;
  }
  .amazon-form-modal .modal-footer .btn-default,
  .amazon-form-modal .modal-footer .tw-dw-btn-neutral {
    background: transparent !important;
    border: 1px solid rgba(255,255,255,0.6) !important;
    color: #fff !important;
  }
  .amazon-form-modal .modal-footer .btn-default:hover,
  .amazon-form-modal .modal-footer .tw-dw-btn-neutral:hover {
    background: rgba(255,255,255,0.1) !important;
    color: #fff !important;
  }

  /* Responsive: stack on narrow */
  @media (max-width: 768px) {
    .amazon-form-modal .modal-dialog { width: 100% !important; max-width: 100% !important; margin: 0.5rem; }
    .amazon-form-modal .amazon-form-card .row > [class*="col-"] { margin-bottom: 0.5rem; }
  }
</style>

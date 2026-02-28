@extends('layouts.app')
@section('title', __('lang_v1.customer_groups'))

@section('css')
<style>
    /* ----- Banner ----- */
    .customer-group-header-banner {
        background: #37475a;
        border-radius: 8px;
        padding: 22px 28px;
        margin-bottom: 16px;
        border: 1px solid #4a5d6e;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    }
    .customer-group-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .customer-group-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }
    .customer-group-header-title i {
        font-size: 22px;
        color: #ff9900 !important;
    }
    .customer-group-header-subtitle {
        font-size: 13px;
        color: #b8c4ce;
        margin: 0;
    }

    /* ----- Amazon Theme: Page Background ----- */
    .customer-groups-page {
        background: linear-gradient(135deg, #f3f4f6 0%, #eaeded 35%, #f9fafb 100%) !important;
    }
    .customer-groups-page .content {
        background: transparent !important;
    }

    /* ----- Table card: Amazon styled (keep original positioning) ----- */
    .customer-groups-page .box-primary,
    .customer-groups-page .tw-mb-4 {
        background: #ffffff !important;
        border: 1px solid #d5d9d9 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 16px rgba(15, 17, 17, 0.16) !important;
    }
    .customer-groups-page .box-header {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #ffffff !important;
        border-bottom: 2px solid #ff9900 !important;
        border-radius: 10px 10px 0 0 !important;
        padding: 14px 20px !important;
    }
    .customer-groups-page .box-title {
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .customer-groups-page #dynamic_button,
    .customer-groups-page .box-tools .btn-modal {
        background: #FF9900 !important;
        border: 1px solid #e47911 !important;
        color: #FFFFFF !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3) !important;
    }
    .customer-groups-page #dynamic_button:hover,
    .customer-groups-page .box-tools .btn-modal:hover {
        background: #ffac33 !important;
        border-color: #ff9900 !important;
        color: #FFFFFF !important;
        box-shadow: 0 3px 8px rgba(255, 153, 0, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    /* Override rounded-full for Add button */
    .customer-groups-page #dynamic_button.tw-rounded-full {
        border-radius: 6px !important;
    }

    /* ----- DataTables: Amazon styled (keep original positioning) ----- */
    .customer-groups-page #customer_groups_table_wrapper {
        background: #ffffff !important;
        border-radius: 0 0 10px 10px;
        padding: 12px 16px !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_filter input {
        background: #ffffff !important;
        border: 1px solid #d5d9d9 !important;
        color: #0f1111 !important;
        border-radius: 6px !important;
        transition: all 0.2s ease !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #ff9900 !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.15) !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_length select {
        background: #ffffff !important;
        border: 1px solid #d5d9d9 !important;
        color: #0f1111 !important;
        border-radius: 6px !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_filter label,
    .customer-groups-page .dataTables_wrapper .dataTables_length label,
    .customer-groups-page .dataTables_wrapper .dataTables_info {
        color: #565959 !important;
        font-weight: 500 !important;
    }
    .customer-groups-page .dt-buttons .btn,
    .customer-groups-page .dt-buttons button {
        background: #f5f5f5 !important;
        border: 1px solid #d5d9d9 !important;
        color: #0f1111 !important;
        border-radius: 6px !important;
        transition: all 0.2s ease !important;
    }
    .customer-groups-page .dt-buttons .btn:hover,
    .customer-groups-page .dt-buttons button:hover {
        background: #ffffff !important;
        border-color: #ff9900 !important;
        color: #0f1111 !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.2) !important;
    }

    /* ----- Table: Amazon styled (keep original positioning) ----- */
    .customer-groups-page #customer_groups_table thead th {
        background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important;
        color: #ffffff !important;
        border-color: #485769 !important;
        font-weight: 600 !important;
    }
    .customer-groups-page #customer_groups_table tbody td {
        background: #ffffff !important;
        color: #0f1111 !important;
        border-color: #e2e8f0 !important;
    }
    .customer-groups-page #customer_groups_table tbody tr:hover td {
        background: #f8fafc !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #0f1111 !important;
        border-color: #d5d9d9 !important;
        background: #ffffff !important;
        border-radius: 6px !important;
        transition: all 0.2s ease !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #ff9900 !important;
        border-color: #ff9900 !important;
        color: #0f1111 !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3) !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #fff7ed !important;
        border-color: #ff9900 !important;
        color: #0f1111 !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.2) !important;
    }
    .customer-groups-page .dataTables_wrapper .dataTables_empty {
        color: #6b7280 !important;
    }

    /* ----- Add/Edit Customer Group modal ----- */
    .customer_groups_modal .modal-content {
        border: 1px solid #4a5d6e;
        border-radius: 10px;
        overflow: hidden;
    }
    .customer_groups_modal .modal-header {
        background: #37475a !important;
        border-bottom: 1px solid #4a5d6e !important;
        padding: 16px 20px !important;
    }
    .customer_groups_modal .modal-title {
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .customer_groups_modal .modal-header .close {
        color: #ffffff !important;
        opacity: 0.9;
    }
    .customer_groups_modal .modal-header .close:hover {
        color: #ff9900 !important;
    }
    .customer_groups_modal .modal-body {
        background: #ffffff;
        padding: 20px;
    }
    .customer_groups_modal .modal-footer {
        background: #f7f8f8 !important;
        border-top: 1px solid #e5e7eb !important;
        padding: 14px 20px !important;
    }
    .customer_groups_modal .modal-footer .tw-dw-btn-primary {
        background: linear-gradient(to bottom, #ff9900 0%, #e47911 100%) !important;
        border-color: #c45500 !important;
        color: #0f1111 !important;
    }
    .customer_groups_modal .modal-footer .tw-dw-btn-neutral {
        background: #37475a !important;
        border-color: #4a5d6e !important;
        color: #ffffff !important;
    }
    .customer_groups_modal .modal-footer .tw-dw-btn-neutral:hover {
        background: #2d3d4d !important;
        border-color: #ff9900 !important;
        color: #ffffff !important;
    }

    /* ----- Edit/Delete row buttons (light style for white table) ----- */
    #customer_groups_table td:last-child {
        vertical-align: middle;
    }
    /* ----- Edit/Delete buttons: Keep current colors (orange edit, dark navy delete) ----- */
    /* Edit Button - Amazon Orange */
    #customer_groups_table td:last-child button.edit_customer_group_button,
    #customer_groups_table td:last-child .table-action-btn-edit {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        min-height: 32px !important;
        padding: 6px 14px !important;
        margin: 0 2px !important;
        background: #ff9900 !important;
        color: #ffffff !important;
        border: 1px solid #e47911 !important;
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(255, 153, 0, 0.3) !important;
        outline: none !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }
    #customer_groups_table td:last-child button.edit_customer_group_button:hover,
    #customer_groups_table td:last-child .table-action-btn-edit:hover {
        background: #ffac33 !important;
        border-color: #ff9900 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 8px rgba(255, 153, 0, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    #customer_groups_table td:last-child button.edit_customer_group_button i,
    #customer_groups_table td:last-child button.edit_customer_group_button .glyphicon,
    #customer_groups_table td:last-child .table-action-btn-edit i,
    #customer_groups_table td:last-child .table-action-btn-edit .glyphicon {
        color: #ffffff !important;
        font-size: 13px !important;
    }
    
    /* Delete Button - Dark Navy */
    #customer_groups_table td:last-child button.delete_customer_group_button,
    #customer_groups_table td:last-child .table-action-btn-delete {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        min-height: 32px !important;
        padding: 6px 14px !important;
        margin: 0 2px !important;
        background: #232f3e !important;
        color: #ffffff !important;
        border: 1px solid #37475a !important;
        border-radius: 6px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 4px rgba(35, 47, 62, 0.3) !important;
        outline: none !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }
    #customer_groups_table td:last-child button.delete_customer_group_button:hover,
    #customer_groups_table td:last-child .table-action-btn-delete:hover {
        background: #37475a !important;
        border-color: #ff9900 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 8px rgba(35, 47, 62, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    #customer_groups_table td:last-child button.delete_customer_group_button i,
    #customer_groups_table td:last-child button.delete_customer_group_button .glyphicon,
    #customer_groups_table td:last-child .table-action-btn-delete i,
    #customer_groups_table td:last-child .table-action-btn-delete .glyphicon {
        color: #ffffff !important;
        font-size: 13px !important;
    }
</style>
@endsection

@section('content')

    <!-- Amazon-style banner -->
    <section class="content-header">
        <div class="customer-group-header-banner amazon-theme-banner">
            <div class="customer-group-header-content">
                <h1 class="customer-group-header-title">
                    <i class="fas fa-users"></i>
                    @lang('lang_v1.customer_groups')
                </h1>
                <p class="customer-group-header-subtitle">
                    @lang('lang_v1.all_your_customer_groups'). Organize customers and assign selling price groups.
                </p>
            </div>
        </div>
    </section>

    <!-- Main content - Amazon theme -->
    <section class="content customer-groups-page">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_your_customer_groups')])
            @can('customer.create')
                @slot('tool')
                    <div class="box-tools">
                        <a id="dynamic_button" class="tw-dw-btn tw-dw-btn-sm tw-font-bold tw-text-white tw-border-none btn-modal"
                            data-href="{{ action([\App\Http\Controllers\CustomerGroupController::class, 'create']) }}"
                            data-container=".customer_groups_modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg> @lang('messages.add')
                        </a>
                    </div>
                @endslot
            @endcan
            @can('view_customer_group')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap" id="customer_groups_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.customer_group_name')</th>
                                <th>@lang('lang_v1.selling_price_group')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade customer_groups_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
@stop
@section('javascript')

    <script type="text/javascript">
        $(document).on('change', '#price_calculation_type', function() {
            var price_calculation_type = $(this).val();

            if (price_calculation_type == 'percentage') {
                $('.percentage-field').removeClass('hide');
                $('.selling_price_group-field').addClass('hide');
            } else {
                $('.percentage-field').addClass('hide');
                $('.selling_price_group-field').removeClass('hide');
            }
        });

        // Calculate price based on selling price group and percentage
        function calculatePrice() {
            var percentage = parseFloat($('#price_percentage').val()) || 0;
            var sellingPriceGroupId = $('#selling_price_group_id').val();
            
            if (sellingPriceGroupId && percentage > 0) {
                // Example base price - using $100 as example
                // In production, you could fetch actual prices from the selected price group
                var basePrice = 100;
                
                // Calculate: basePrice + (basePrice * percentage / 100)
                // This adds the percentage to the base price (e.g., 10% of $100 = $110)
                var calculatedPrice = basePrice + (basePrice * percentage / 100);
                
                // Update display
                $('#base_price_example').text(basePrice.toFixed(2));
                $('#percentage_example').text(percentage);
                $('#calculated_price_result').text(calculatedPrice.toFixed(2));
                $('#calculated_price_display').show();
            } else {
                $('#calculated_price_display').hide();
            }
        }

        // Listen for changes in percentage and selling price group
        $(document).on('change', '#price_percentage, #selling_price_group_id', function() {
            calculatePrice();
        });

        // Also trigger on input for real-time calculation
        $(document).on('input', '#price_percentage', function() {
            calculatePrice();
        });

        // Initialize calculation when modal is shown (in case values are pre-filled)
        $(document).on('shown.bs.modal', '.customer_groups_modal', function() {
            setTimeout(function() {
                calculatePrice();
            }, 100);
        });
    </script>
@endsection

@extends('layouts.app') @section('title', __('product.add_new_product')) @section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Add New Products</h1>
    <!-- <ol class="breadcrumb"><li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li><li class="active">Here</li>                                                                                                                                                                                                                                                                                                                                                                </ol> -->
</section>
{!! Form::open([
    'url' => action([\App\Http\Controllers\CustomDiscountController::class, 'store']),
    'method' => 'post',
    'id' => 'custom_discount_add_form',
]) !!} @csrf
<section class="tw-flex tw-justify-center tw-mt-6" id="main_section">
    <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6  tw-w-full m-5">
        <div class='row'>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <input type="text" id="product_name" name="product_name"
                                class="form-control tw-w-full tw-border tw-rounded-md tw-p-2" placeholder="Rule Title">
                        </div>
                    </div>
                    <div class="col-sm-6 tw-flex tw-items-center tw-gap-4">
                        <div class="form-check">
                            <input class="form-check-input tw-w-5 tw-h-5" type="checkbox" name="enable" id="enable">
                            <label class="form-check-label tw-font-semibold" for="enable">
                                Enable?
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input tw-w-5 tw-h-5" type="checkbox" id="option2"
                                name="discount_matched_ignor_other_rules">
                            <label class="form-check-label tw-font-semibold" for="option2">Apply this rule if matched
                                and ignore all other rules</label>
                        </div>
                    </div>
                </div>
                <select id="discount_type" name="discount_type" class="form-control form-control-xs">
                    <option value="" disabled selected>Select Category</option>
                    <optgroup label="Simple Discount">
                        <option value="product_adjustment">Product Adjustment</option>
                        <option value="cart_adjustment">Cart Adjustment</option>
                        <option value="free_shipping">Free Shipping</option>
                    </optgroup>
                    <optgroup label="Bulk Discount">
                        <option value="bulk_discount">Bulk Discount</option>
                        <option value="bundle_discount">Bundle (Set) Discount</option>
                    </optgroup>
                    <optgroup label="BOGO Discount">
                        <option value="bogo_x">BUY X GET X</option>
                        <option value="bogo_y">BUY X GET Y</option>
                    </optgroup>

                </select>

            </div>
            <div class="col-sm-1">
                <div class="tw-flex tw-flex-col tw-gap-2 tw-w-48">
                    <button type="submit" class="tw-bg-blue-500 tw-text-white tw-py-2 tw-rounded-md">Save</button>
                    <button type="submit&close" class="btn-success tw-text-white tw-py-2 tw-rounded-md">Save & Close
                    </button>
                    <button type="cancel" class="btn-danger tw-text-white tw-py-2 tw-rounded-md">Cancel</button>
                </div>

            </div>
        </div>

    </div>
</section>

<div id="filter"></div>
<div id="discount"></div>
<div id='rules'></div>

{!! Form::close() !!} @endsection @section('javascript')
<script type="text/javascript">
    $(document).ready(function() {

        //filter component started
        let indaxfilter = 1;

        $(document).on("change", ".filter_type", function() {
            let selectedValue = $(this).val();
            let filterContainer = $(this).closest(".filter-row").find(".filter-content");

            filterContainer.empty();

            if (selectedValue === "on_sale_products") {
                filterContainer.append(`
                <div class="col-sm-2">
                    <select class="form-control filter_in_notin" name='filter_section[filters][${indaxfilter}][in_list_of_not_in]'>
                        <option value="exclude" selected>Exclude</option>
                        <option value="include">Include</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <input type="number" class="form-control" placeholder="change">
                </div>
            `);
            } else if (selectedValue !== "all") {
                filterContainer.append(`
                <div class="col-sm-2">
                    <select class="form-control filter_in_notin" name='filter_section[filters][${indaxfilter}][in_list_of_not_in]'>
                        <option value="in_list" selected>In List</option>
                        <option value="not_in_list">Not in List</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <input type="number" class="form-control" placeholder="change" name='filter_section_selected_things'>
                </div>
            `);
            }
        });

        $(document).on("click", "#add_filter_row", function(e) {
            e.preventDefault();
            indaxfilter++;
            let newRow = `
            <div class="row tw-p-4 filter-row">                           
                <div class="col-sm-3">
                    <select class="form-control filter_type" name='filter_section[filters][${indaxfilter}][category]'>
                        <option value="" disabled selected>Select Category</option>
                        <optgroup label="Products">
                            <option value="all">All Products</option>
                            <option value="products">Products</option>
                            <option value="category">Category</option>
                            <option value="attributes">Attributes</option>
                            <option value="tags">Tags</option>
                            <option value="skus">SKUs</option>
                            <option value="on_sale_products">On sale products</option>
                        </optgroup>
                        <optgroup label="Custom Taxonomy">
                            <option value="brands">Brands</option>
                        </optgroup>
                    </select>
                </div>
                <div class="filter-content"></div>
                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-filter">✖</button>
            </div>
        `;

            $("#filter_container").append(newRow);
        });

        $(document).on("click", ".remove-filter", function() {
            if ($(".filter-row").length > 1) {
                $(this).closest(".filter-row").remove();
            }
        });

        function getFilterSection() {
            return `
        <section class="tw-flex tw-justify-center tw-mt-6" id='filter_section'>
            <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6 tw-w-full">
                <div class="row">
                    <div class="col-sm-2">
                        <h4>Filter</h4>
                        <p class="tw-p-5">Choose what gets discount (products/categories/attributes/SKU and so on)
                            Note: You can also exclude products/categories.</p>
                    </div>
                    <div class="col-sm-10 tw-bg-white tw-border-lg">
                        <div id="filter_container">
                            <div class="row tw-p-4 filter-row">                          
                                <div class="col-sm-3">
                                    <select class="form-control filter_type" name='filter_section[filters][${indaxfilter}][category]'>
                                        <option value="" disabled selected>Select Category</option>
                                        <optgroup label="Products">
                                            <option value="all">All Products</option>
                                            <option value="products">Products</option>
                                            <option value="category">Category</option>
                                            <option value="attributes">Attributes</option>
                                            <option value="tags">Tags</option>
                                            <option value="skus">SKUs</option>
                                            <option value="on_sale_products">On sale products</option>
                                        </optgroup>
                                        <optgroup label="Custom Taxonomy">
                                            <option value="brands">Brands</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="filter-content "></div>
                                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-filter">✖</button>
                            </div>
                        </div>
                        <hr>
                        <button type="button" class="btn btn-info" id="add_filter_row">Add Filter</button>
                    </div>
                </div>
            </div>
        </section>
    `;
        }



        // Rules Component Started

        let indexRuleRange = 0;
        $(document).on('click', '#add_condition_row', function(e) {
            e.preventDefault(); // Prevent default form submission

            indexRuleRange++;

            let newConditionRow = `
            <div class="row tw-gap-4 tw-border tw-p-4 tw-rounded-md condition-row">
                <div class='col-sm-2'>
                    <select name="rule[rule][${indexRuleRange}][condition_type]" class="form-control tw-p-2">
                        <optgroup label="Cart">
                            <option value="cart_subtotal">Subtotal</option>
                            <option value="cart_items_quantity">Item quantity</option>
                            <option value="cart_coupon">Coupons</option>
                            <option value="cart_items_weight">Total weight</option>
                            <option value="cart_payment_method">Payment Method</option>
                            <option value="cart_line_items_count">Line Item Count</option>
                        </optgroup>
                        <optgroup label="Cart Items">
                            <option value="cart_item_product_attributes">Attributes</option>
                            <option value="cart_item_product_category">Category</option>
                            <option value="cart_item_product_combination">Product combination</option>
                            <option value="cart_item_product_onsale">On sale products</option>
                            <option value="cart_item_product_sku">SKU</option>
                            <option value="cart_item_product_tags">Product Tags</option>
                            <option value="cart_item_products">Products</option>
                            <option value="cart_item_category_combination">Category combination</option>
                        </optgroup>
                    </select>
                    <span class="tw-text-sm tw-text-gray-500">Condition Type</span>
                </div>

                <div class='col-md-3'> 
                    <select name="rule[rule][${indexRuleRange}][subtotal_should_be]" class="form-control tw-p-2">
                        <option value="less_than">Less than (&lt;)</option>
                        <option value="less_than_or_equal">Less than or equal (&lt;=)</option>
                        <option value="greater_than_or_equal" selected>Greater than or equal (&gt;=)</option>
                        <option value="greater_than">Greater than (&gt;)</option>
                    </select>
                    <span class="tw-text-sm tw-text-gray-500">Subtotal should be</span>
                </div>

                <div class='col-md-2'>
                    <input name="rule[rule][${indexRuleRange}][subtotal_amount]" type="number" class="form-control tw-p-2" placeholder="0.00" min="0">
                    <span class="tw-text-sm tw-text-gray-500">Subtotal Amount</span>
                </div>

                <div class='col-md-2'>
                    <select name="rule[rule][${indexRuleRange}][how_to_calculate_subtotal]" class="form-control tw-p-2">
                        <option value="from_cart" selected>Count all items in cart</option>
                        <option value="from_filter">Only count items chosen in the filters set for this rule</option>
                    </select>
                    <span class="tw-text-sm tw-text-gray-500">How to calculate the subtotal</span>
                </div>

                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-condition">✖</button>
            </div>`;

            $('#conditions_container').append(newConditionRow);
        });

        $(document).on('click', '.remove-condition', function() {
            $(this).closest('.condition-row').remove();
        });

        function getRulesSection() {
            // Return the rules section HTML
            return `
        <section id='rules_section' class="tw-flex tw-justify-center tw-mt-6">
            <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6 tw-w-full m-5">
                <div class="row">
                    <div class='col-sm-2'>
                        <h4>Rules (Optional) - Read Docs</h4>
                        <p class="tw-p-5">Include additional conditions (if necessary)</p>
                    </div>
                    <div class='col-sm-10 tw-bg-white tw-border tw-border-gray-700 tw-rounded-lg tw-p-4'>
                        <div class="tw-flex tw-gap-6">
                            <label class="form-check-label tw-font-semibold" for="rule">
                                Conditions Relationship
                            </label>
                            <div class="tw-flex tw-items-center tw-gap-2" id="rule">
                                <input class="form-check-input tw-w-5 tw-h-5" type="radio" id="match_all" name="rules_relation_match_all" value="all">
                                <label class="form-check-label tw-font-semibold" for="match_all">Match All</label>
                            </div>
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <input class="form-check-input tw-w-5 tw-h-5" type="radio" id="match_any" name="rules_relation_match_any" value="any">
                                <label class="form-check-label tw-font-semibold" for="match_any">Match Any</label>
                            </div>
                        </div>
                        <div id="conditions_container"></div>
                        <button type="button" class="btn btn-info" id="add_condition_row">Add New Condition</button>
                    </div>
                </div>
            </div>
        </section>`;
        }

        function getDiscountSection() {
            return `<section id='discount_section' class="tw-flex tw-justify-center tw-mt-6">
        <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6  tw-w-full m-5">
            <div class="row">
                <div class='col-sm-2'>
                    <h4>Discount</h4>
                    <p class="tw-p-5">Select discount type and its value (percentage/price/fixed price)</p>
                </div>
                <div class='col-sm-10 tw-bg-white tw-border-lg'>
                    <div class="row tw-p-4 ">
                        <div class="col-sm-2">
                            <select id="discount_type" name="discount_type" class="form-control ">
                                <option value="" disabled selected>Select Discount Type</option>
                                <option value="percentage_discount">Percentage Discount</option>
                                <option value="fixed_discount">Fixed Discount</option>
                                <option value="fix_price_per_item">Fixed Price Per item</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" class="form-control " id="value" placeholder="Value"
                                name='discount_value'>

                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control " id="discount_lable" name="discount_lable"
                                placeholder="Discount Label">
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>`
        }





        //Bulk discount Component

        let indaxbulk_discount = 1;
        $(document).on('click', '#add_range_bulk_discount', function(e) {
            indaxbulk_discount++;
            e.preventDefault(); // Prevent form submission

            let newDiscountRow = `
           <div class="row tw-mt-3 tw-items-center tw-gap-4 tw-border tw-p-4 tw-rounded-md discount-row">
                                <div class="col-sm-2">
                                    <input type="number" class="form-control tw-p-2" name="bulk_discount[ranges][${indaxbulk_discount}][min]" placeholder="Min Quantity">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control tw-p-2" name="bulk_discount[ranges][${indaxbulk_discount}][max]" placeholder="Max Quantity">
                                </div>
                                <div class="col-md-3">
                                    <select name="bulk_discount[ranges][${indaxbulk_discount}]type]" class="form-control tw-p-2">
                                        <option value="" disabled selected>Select Discount Type</option>
                                        <option value="percentage_discount">Percentage Discount</option>
                                        <option value="fixed_discount">Fixed Discount</option>
                                        <option value="fix_price_per_item">Fixed Price Per Item</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control tw-p-2" name="bulk_discount[ranges][${indaxbulk_discount}][discount_value]" placeholder="Discount Value">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control tw-p-2" name="bulk_discount[ranges][${indaxbulk_discount}][label]" placeholder="Label">
                                </div>
                                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-discount">✖</button>
                            </div>`;

            $('#discount_container').append(newDiscountRow);
        });

        $(document).on('click', '.remove-discount', function() {
            $(this).closest('.discount-row').remove();
        });

        function getBulkDiscountSection() {

            // Return the bulk discount section HTML
            return `
        <section class="tw-flex tw-justify-center tw-mt-6" id="discount_section">
            <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6 tw-w-full m-5">
                <div class="row">
                    <div class="col-sm-2 tw-p-5">
                        <h4>Discount</h4>
                        <p>Select discount type and its value (percentage/price/fixed price)</p>
                    </div>
                    <div class="col-sm-10 tw-bg-white tw-border tw-rounded-lg tw-p-4">
                        <div class="row tw-mb-4">
                            <div class="col-sm-2">
                                <label for="discount_type" class="tw-font-semibold">Count Quantities by:</label>
                            </div>
                            <div class="col-sm-3">
                                <select name="bulk_discount_type" class="form-control tw-p-2">
                                    <option value="" disabled selected>Filters set above</option>
                                    <option value="product_adjustment">Individual product</option>
                                    <option value="cart_adjustment">All variants in each product together</option>
                                </select>
                            </div>
                        </div>
                        <div id="discount_container">
                            <div class="row tw-mt-3 tw-items-center tw-gap-4 tw-border tw-p-4 tw-rounded-md discount-row">
                                <div class="col-sm-2">
                                    <input type="number" class="form-control tw-p-2" name="bulk_discount[ranges][1][min]" placeholder="Min Quantity">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control tw-p-2" name="bulk_discount[ranges][1][max]" placeholder="Max Quantity">
                                </div>
                                <div class="col-md-3">
                                    <select name="bulk_discount[ranges][1][type]" class="form-control tw-p-2">
                                        <option value="" disabled selected>Select Discount Type</option>
                                        <option value="percentage_discount">Percentage Discount</option>
                                        <option value="fixed_discount">Fixed Discount</option>
                                        <option value="fix_price_per_item">Fixed Price Per Item</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control tw-p-2" name="bulk_discount[ranges][1][discount_value]" placeholder="Discount Value">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control tw-p-2" name="bulk_discount[ranges][1][label]" placeholder="Label">
                                </div>
                                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-discount">✖</button>
                            </div>
                        </div>
                        <hr class="tw-my-4" />
                        <div class="tw-flex tw-gap-4">
                            <input type="text" class="form-control tw-p-2" id="discount_label" placeholder="Discount Label" name="bulk_Main_discount_label">
                            <button class="btn btn-primary tw-p-2" id="add_range_bulk_discount">Add Range</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>`;
        }




        // Bundle Discount component
        let rangeIndexBundalDiscount = 1;

        $(document).on('click', '#add_bundle_range', function(e) {
            e.preventDefault(); // Prevent form submission
            rangeIndexBundalDiscount++;
            let newDiscountRow = `
            <div class="row tw-mt-3 tw-items-center tw-gap-4 tw-border tw-p-4 tw-rounded-md discount-row">
                <div class="col-sm-1">
                    <input type="number" class="form-control tw-p-2" name="bundle_discount[ranges][${rangeIndexBundalDiscount}][min]" placeholder="Min Quantity">
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control tw-p-2" name="bundle_discount[ranges][${rangeIndexBundalDiscount}][max]" placeholder="Max Quantity">
                </div>
                <div class="col-md-3">
                    <select name="bundle_discount[ranges][${rangeIndexBundalDiscount}][type]" class="form-control tw-p-2">
                        <option value="" disabled selected>Select Discount Type</option>
                        <option value="percentage_discount">Percentage Discount</option>
                        <option value="fixed_discount">Fixed Discount</option>
                        <option value="fix_price_per_item">Fixed Price Per Item</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control tw-p-2" name="bundle_discount[ranges][${rangeIndexBundalDiscount}][value]" placeholder="Discount Value">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control tw-p-2" name="bundle_discount[ranges][${rangeIndexBundalDiscount}][label]" placeholder="Label">
                </div>
                <div class="form-check col-md-2 tw-flex tw-items-center">
                    <input class="form-check-input" type="checkbox" name="bundle_discount[ranges][${rangeIndexBundalDiscount}][recursive]" id="recursive${rangeIndexBundalDiscount}">
                    <label class="form-check-label tw-font-semibold tw-ml-2" for="recursive">
                        Recursive
                    </label>
                </div>
                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-bundle-discount">✖</button>
            </div>`;

            $('#discount_container').append(newDiscountRow);
        });

        $(document).on('click', '.remove-bundle-discount', function() {
            $(this).closest('.discount-row').remove();
        });

        $(document).on('click', `#recursive${rangeIndexBundalDiscount}`, function() {
            $('#add_bundle_range').remove();
        });



        function getBundleDiscountSection() {


            // Return the bundle discount section HTML
            return `
        <section class="tw-flex tw-justify-center tw-mt-6" id="discount_section">
    <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6 tw-w-full m-5">
        <div class="row">
            <div class="col-sm-2 tw-p-5">
                <h4>Bundle Discount</h4>
                <p>Select discount type and its value (percentage/price/fixed price)</p>
            </div>
            <div class="col-sm-10 tw-bg-white tw-border tw-rounded-lg tw-p-4">
                <div class="row tw-mb-4">
                    <div class="col-sm-2">
                        <label for="bundle_count_quantities" class="tw-font-semibold">Count Quantities by:</label>
                    </div>
                    <div class="col-sm-3">
                        <select name="bundle_count_quantities" class="form-control tw-p-2">
                            <option value="" disabled selected>Filters set above</option>
                            <option value="product_adjustment">Individual product</option>
                            <option value="cart_adjustment">All variants in each product together</option>
                        </select>
                    </div>
                </div>
                <div id="discount_container">
                    <div class="row tw-mt-3 tw-items-center tw-gap-4 tw-border tw-p-4 tw-rounded-md discount-row">
                        <div class="row tw-mt-3 tw-items-center tw-gap-4 tw-border tw-p-4 tw-rounded-md discount-row">
                            <div class="col-sm-1">
                                <input type="number" class="form-control tw-p-2" name="bundle_discount[ranges][1][min]]" placeholder="Min Quantity">
                            </div>
                            <div class="col-md-1">
                                <input type="number" class="form-control tw-p-2" name="bundle_discount[ranges][1][max]" placeholder="Max Quantity">
                            </div>
                            <div class="col-md-3">
                                <select name="bundle_discount[ranges][1][type]" class="form-control tw-p-2">
                                    <option value="" disabled selected>Select Discount Type</option>
                                    <option value="percentage_discount">Percentage Discount</option>
                                    <option value="fixed_discount">Fixed Discount</option>
                                    <option value="fix_price_per_item">Fixed Price Per Item</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <input type="number" class="form-control tw-p-2" name="bundle_discount[ranges][1][value]" placeholder="Discount Value">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control tw-p-2" name="bundle_discount[ranges][1][label]" placeholder="Label">
                            </div>
                            <div class="form-check col-md-2 tw-flex tw-items-center">
                                <input class="form-check-input" type="checkbox" name="bundle_discount[ranges][1][recursive]" id="recursive1">
                                <label class="form-check-label tw-font-semibold tw-ml-2" for="recursive">
                                    Recursive
                                </label>
                            </div>
                            <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-bundle-discount">✖</button>
                        </div>
                    </div>
                    </div>
                    <hr class="tw-my-4" />
                    <div class='row tw-p-2'>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" placeholder="Discount Label" name='bundel_main_discount_label'>
                        </div>
                        <div id="buttonforrange"></div>
                               <button class="btn btn-primary" id="add_bundle_range"> Add Range</button>
                    </div>
                </div>
            </div>
        </div>
</section>`;
        }




        // Discount bogo_x
        let rangeIndexBogoX = 1;

        $(document).on('click', '#add_bogox_range', function() {
            rangeIndexBogoX++; // Increment index for uniqueness
            let newRow = `
            <div class="tw-flex tw-items-center tw-gap-4 tw-border tw-border-gray-300 tw-rounded-lg tw-p-4 discount-row">
                
                <!-- Min Quantity -->
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="number" name="buyx_getx_adjustments[ranges][${rangeIndexBogoX}][from]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Min Quantity" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Minimum Quantity</span>
                </div>

                <!-- Max Quantity -->
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="number" name="buyx_getx_adjustments[ranges][${rangeIndexBogoX}][to]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Max Quantity" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Maximum Quantity</span>
                </div>

                <!-- Free Quantity -->
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="number" name="buyx_getx_adjustments[ranges][${rangeIndexBogoX}][free_qty]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Free Quantity" min="1" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Free Quantity</span>
                </div>

                <!-- Discount Type -->
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <select name="buyx_getx_adjustments[ranges][${rangeIndexBogoX}][free_type]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full">
                        <option value="free_product">Free</option>
                        <option value="percentage">Percentage Discount</option>
                        <option value="flat">Fixed Discount</option>
                    </select>
                    <span class="tw-text-xs tw-text-gray-600">Discount Type</span>
                </div>

                <!-- Discount Value (Hidden by Default) -->
                <div class="tw-flex tw-flex-col tw-w-1/5 tw-hidden">
                    <input type="number" name="buyx_getx_adjustments[ranges][${rangeIndexBogoX}][free_value]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Value" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Discount Percentage</span>
                </div>

                <!-- Recursive Checkbox -->
                <div class="tw-flex tw-items-center tw-gap-2">
                    <input type="checkbox" class="tw-w-5 tw-h-5 tw-border-gray-400" name="buyx_getx_adjustments[ranges][${rangeIndexBogoX}][recursive]" value="1">
                    <label class="tw-text-sm tw-text-gray-700">Recursive?</label>
                </div>

                <!-- Remove Button -->
                <button type="button" class="tw-bg-red-500 tw-text-white tw-py-2 tw-px-4 tw-rounded remove-discount">✖</button>

            </div>`;

            $('#discount_container').append(newRow);
        });

        $(document).on('click', '.remove-buyx_discount', function() {
            if ($('.discount-row').length > 1) {
                $(this).closest('.discount-row').remove();
            }
        });

        function discountBOGOXsection() {

            return (`
    <section class="tw-flex tw-justify-center tw-mt-6" id='discount_section'>
        <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6 tw-w-full m-5">
            <div class="row">
                <div class='col-sm-2 tw-p-5'>
                    <h4>Discount</h4>
                    <p>Select discount type and its value (percentage/price/fixed price)</p>
                </div>
                <div class="col-sm-10 tw-bg-white tw-border-lg">
                    <div id="discount_container">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-border tw-border-gray-300 tw-rounded-lg tw-p-4 discount-row">
                            
                            <!-- Min Quantity -->
                            <div class="tw-flex tw-flex-col tw-w-1/5">
                                <input type="number" name="buyx_getx_adjustments[ranges][1][min]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Min Quantity" min="0" step="any" value="1">
                                <span class="tw-text-xs tw-text-gray-600">Minimum Quantity</span>
                            </div>

                            <!-- Max Quantity -->
                            <div class="tw-flex tw-flex-col tw-w-1/5">
                                <input type="number" name="buyx_getx_adjustments[ranges][1][max]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Max Quantity" min="0" step="any" value="1">
                                <span class="tw-text-xs tw-text-gray-600">Maximum Quantity</span>
                            </div>

                            <!-- Free Quantity -->
                            <div class="tw-flex tw-flex-col tw-w-1/5">
                                <input type="number" name="buyx_getx_adjustments[ranges][1][free_qty]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Free Quantity" min="1" step="any" value="1">
                                <span class="tw-text-xs tw-text-gray-600">Free Quantity</span>
                            </div>

                            <!-- Discount Type -->
                            <div class="tw-flex tw-flex-col tw-w-1/5">
                                <select name="buyx_getx_adjustments[ranges][1][free_type]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full">
                                    <option value="free_product">Free</option>
                                    <option value="percentage">Percentage Discount</option>
                                    <option value="flat">Fixed Discount</option>
                                </select>
                                <span class="tw-text-xs tw-text-gray-600">Discount Type</span>
                            </div>

                            <!-- Discount Value (Hidden by Default) -->
                            <div class="tw-flex tw-flex-col tw-w-1/5 tw-hidden">
                                <input type="number" name="buyx_getx_adjustments[ranges][1][free_value]" class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full" placeholder="Value" min="0" step="any" value="1">
                                <span class="tw-text-xs tw-text-gray-600">Discount Percentage</span>
                            </div>

                            <!-- Recursive Checkbox -->
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <input type="checkbox" class="tw-w-5 tw-h-5 tw-border-gray-400" name="buyx_getx_adjustments[ranges][1][recursive]" value="1">
                                <label class="tw-text-sm tw-text-gray-700">Recursive?</label>
                            </div>

                            <!-- Remove Button -->
                            <button type="button" class=" remove-bogox_discount">✖</button>

                        </div>
                    </div>

                    <!-- Add Range Button -->
                    <div class="tw-p-3">
                        <button type="button" id="add_bogox_range" class="tw-bg-blue-500 tw-text-white tw-py-2 tw-px-4 hover:tw-bg-blue-600">
                            Add Range
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>`);
        }



        // Discount bogo_Y
        let bogoY_range_index = 1;

        $(document).on("click", "#add_bogoy_range", function(e) {
            e.preventDefault();

            bogoY_range_index++;
            let newRange = `<div class="row">
        <div class="col-sm-3">
            <div class="tw-flex tw-items-center tw-gap-4 tw-border tw-border-gray-300 tw-rounded-lg tw-p-4">
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="number" name="buyx_gety[ranges][${bogoY_range_index}][min]"
                        class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full"
                        placeholder="Min Quantity" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Minimum Quantity</span>
                </div>
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="number" name="buyx_gety[ranges][${bogoY_range_index}][max]"
                        class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full"
                        placeholder="Maximum Quantity" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Maximum Quantity</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="tw-flex tw-items-center tw-gap-4 tw-border tw-border-gray-300 tw-rounded-lg tw-p-4">
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="text" name="buyx_gety[ranges][${bogoY_range_index}][things]"
                        class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full"
                        placeholder="Product">
                    <span class="tw-text-xs tw-text-gray-600">Product</span>
                </div>
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <input type="number" name="buyx_gety[ranges][${bogoY_range_index}][free_quantity]"
                        class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full"
                        placeholder="Free Quantity" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Free Quantity</span>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="tw-flex tw-items-center tw-gap-4 tw-border tw-border-gray-300 tw-rounded-lg tw-p-4">
                <div class="tw-flex tw-flex-col tw-w-1/5">
                    <select name="buyx_gety[ranges][${bogoY_range_index}][free_type]"
                        class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full">
                        <option value="free_product">Free</option>
                        <option value="percentage">Percentage Discount</option>
                        <option value="flat">Fixed Discount</option>
                    </select>
                    <span class="tw-text-xs tw-text-gray-600">Discount Type</span>
                </div>
                <div class="tw-flex tw-flex-col tw-w-1/5 tw-hidden">
                    <input type="number" name="buyx_gety[ranges][${bogoY_range_index}][free_value]"
                        class="form-control tw-border tw-rounded-md tw-p-2 tw-w-full"
                        placeholder="Value" min="0" step="any" value="1">
                    <span class="tw-text-xs tw-text-gray-600">Discount Percentage</span>
                </div>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <input type="checkbox" class="tw-w-5 tw-h-5 tw-border-gray-400"
                        name="buyx_gety[ranges][${bogoY_range_index}][recursive]" value="1">
                    <label class="tw-text-sm tw-text-gray-700">Recursive?</label>
                </div>
                <div class="tw-text-red-500 tw-cursor-pointer remove-discount_bogoY">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
        </div>
    </div>`;

            $("#bogoy_rows").append(newRange);
        });

        $(document).on("click", ".remove-discount_bogoY", function() {
            $(this).closest(".row").remove();
        });

        function discountBOGOYsection() {
            return (`
    <section class="tw-flex tw-justify-center tw-mt-6" id="discount_section">
        <div class="tw-bg-white tw-shadow-lg tw-rounded-lg tw-p-6 tw-w-full m-5">
            <div class="row">
                <div class="col-sm-2 tw-p-5">
                    <h4>Discount</h4>
                    <p>Select discount type and its value (percentage/price/fixed price)</p>
                </div>
                <div class="col-sm-10 tw-bg-white tw-border-lg">
                    <div class="tw-space-y-4" id="bogoy_rows">
                        <div class="tw-flex tw-items-center tw-gap-4 tw-border tw-border-gray-300 tw-rounded-lg tw-p-4">
                            <div class="tw-flex tw-flex-col tw-w-1/5">
                                <select name="buyx_gety_adjustments" class="form-control">
                                    <option value="0">Select Types</option>
                                    <option value="bxgy_product">Buy X Get Y - Products</option>
                                    <option value="bxgy_category">Buy X Get Y - Categories</option>
                                    <option value="bxgy_all">Buy X Get Y - All</option>
                                </select>
                                <span class="tw-text-xs tw-text-gray-600">Get Y Discount Type</span>
                            </div>
                            <div class="tw-flex tw-flex-col tw-w-1/5">
                                <select name="buyx_gety_adjustments" class="form-control">
                                    <option value="product_cumulative">Filters set above</option>
                                    <option value="product">All variants in each product together</option>
                                </select>
                                <span class="tw-text-xs tw-text-gray-600">Buy X Count Based on</span>
                            </div>
                            <div class="tw-flex tw-gap-6">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <input class="form-check-input tw-w-5 tw-h-5" type="radio" id="option1" name="radio_group">
                                    <label class="form-check-label tw-font-semibold" for="option1">Add Auto</label>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <input class="form-check-input tw-w-5 tw-h-5" type="radio" id="option2" name="radio_group">
                                    <label class="form-check-label tw-font-semibold" for="option2">Cheapest</label>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <input class="form-check-input tw-w-5 tw-h-5" type="radio" id="option3" name="radio_group">
                                    <label class="form-check-label tw-font-semibold" for="option3">Highest</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tw-p-3">
                        <button type="button" id="add_bogoy_range" class="tw-bg-blue-500 tw-text-white tw-py-2 tw-px-4 tw-rounded-md hover:tw-bg-blue-600">
                            Add Range
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>`);
        }


        $('#discount_type').on('change', function() {
            let selectedValue = $(this).val();
            console.log(selectedValue)

            $('#filter_section').remove();
            $('#rules_section').remove();
            $('#discount_section').remove();


            switch (selectedValue) {
                case 'product_adjustment':

                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();


                    //Add useble component
                    $('#filter').append(getFilterSection()); // Append when selected
                    $('#discount').append(getDiscountSection()); // Append when selected
                    $('#rules').append(getRulesSection()); // Append when selected

                    break;

                case 'cart_adjustment':

                    //remove other extra component
                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();


                    //Add useble component
                    $('#filter').append(getFilterSection());
                    $('#discount').append(getDiscountSection());
                    $('#rules').append(getRulesSection());
                    break;


                case 'free_shipping':
                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();



                    $('#rules').append(getRulesSection());
                    break;


                case 'bulk_discount':

                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();

                    //Add useble component
                    $('#filter').append(getFilterSection());
                    $('#discount').append(getBulkDiscountSection());
                    $('#rules').append(getRulesSection());
                    break;

                case 'bundle_discount':

                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();


                    //Add useble component
                    $('#filter').append(getFilterSection());
                    $('#discount').append(getBundleDiscountSection());
                    $('#rules').append(getRulesSection());

                    break;



                case 'bogo_x':
                    //remove other extra component
                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();


                    //Add useble component
                    $('#filter').append(getFilterSection());
                    $('#discount').append(discountBOGOXsection());
                    $('#rules').append(getRulesSection());
                    break;

                case 'bogo_y':

                    //remove other extra component
                    $('#filter_section').remove();
                    $('#rules_section').remove();
                    $('#discount_section').remove();

                    //Add useble component
                    $('#filter').append(getFilterSection());
                    $('#discount').append(discountBOGOYsection());
                    $('#rules').append(getRulesSection());
                    break;

                default:

                    break;
            }
        });
    });
</script>
@endsection

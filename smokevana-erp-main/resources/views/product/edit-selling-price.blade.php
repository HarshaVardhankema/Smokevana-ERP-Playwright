@extends('layouts.app')
@section('title', "Edit Selling Price")

@section('css')
<style>
    .edit-selling-price-banner {
        background: #37475a;
        border-radius: 6px;
        padding: 22px 28px;
        margin-bottom: 16px;
        box-shadow: 0 3px 10px rgba(15, 17, 17, 0.4);
    }
    .edit-selling-price-banner .banner-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #ffffff;
    }
    .edit-selling-price-banner .banner-title i { color: #ffffff !important; }
    .edit-selling-price-banner .banner-subtitle {
        font-size: 13px;
        color: rgba(249, 250, 251, 0.88);
        margin: 4px 0 0 0;
    }
    .amazon-orange-btn {
        background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important;
        border-color: #C7511F !important;
        color: white !important;
    }
    .amazon-orange-btn:hover { color: white !important; opacity: 0.95; }
    /* Ensure Variation Image and group price columns stay visible */
    #variations_table th:nth-child(2),
    #variations_table .variation-image-cell {
        min-width: 60px;
        width: 60px;
    }
    #variations_table .variation-image-cell img {
        margin: 0 auto;
    }
</style>
@endsection

@section('content')

<!-- Amazon-style banner -->
<section class="content-header">
    <div class="edit-selling-price-banner">
        <h1 class="banner-title"><i class="fas fa-dollar-sign"></i> Edit Selling Price</h1>
        <p class="banner-subtitle">Filter products and update selling prices by location, brand, or category.</p>
    </div>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">Filter Products / Variations</h3>
				</div>
				<div class="box-body">
					<div class="row">
						@if($has_multiple_locations)
						<div class="col-md-2">
							<div class="form-group">
								<label for="filter_location_id">Business Location:</label>
								{!! Form::select('location_id', $business_locations, null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_location_id',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="filter_product_type">Product Type:</label>
								{!! Form::select('product_type', [
									'' => 'All',
									'single' => 'Single',
									'variable' => 'Variable'
								], null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_product_type',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="filter_brand_id">Brand:</label>
								{!! Form::select('brand_id', $brands, null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_brand_id',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="filter_category_id">Category:</label>
								{!! Form::select('category_id', $categories, null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_category_id',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>&nbsp;</label>
								<div>
									<button type="button" class="btn btn-primary amazon-orange-btn" id="apply_filters_btn">
										<i class="fa fa-filter"></i> Apply
									</button>
									<button type="button" class="btn btn-default" id="clear_filters_btn">
										<i class="fa fa-trash"></i> Clear
									</button>
								</div>
							</div>
						</div>
						@else
						<div class="col-md-3">
							<div class="form-group">
								<label for="filter_product_type">Product Type:</label>
								{!! Form::select('product_type', [
									'' => 'All',
									'single' => 'Single',
									'variable' => 'Variable'
								], null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_product_type',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="filter_brand_id">Brand:</label>
								{!! Form::select('brand_id', $brands, null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_brand_id',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="filter_category_id">Category:</label>
								{!! Form::select('category_id', $categories, null, [
									'class' => 'form-control select2',
									'style' => 'width:100%',
									'id' => 'filter_category_id',
									'placeholder' => 'All'
								]); !!}
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>&nbsp;</label>
								<div>
									<button type="button" class="btn btn-primary amazon-orange-btn" id="apply_filters_btn">
										<i class="fa fa-filter"></i> Apply
									</button>
									<button type="button" class="btn btn-default" id="clear_filters_btn">
										<i class="fa fa-trash"></i> Clear
									</button>
								</div>
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	{!! Form::open(['url' => '#', 'method' => 'post', 'id' => 'selling_price_form', 'onsubmit' => 'return false;' ]) !!}
	<div class="row">
		<div class="col-xs-12">
		<div class="box box-solid">
			<div class="box-header">
	            <h3 class="box-title">Products / Variations List</h3>
	        </div>
			<div class="box-body">
				<!-- Bulk Price Input Section -->
				<div class="row" id="bulk_price_section" style="display: none;">
					<div class="col-xs-12">
						<h4 style="margin-top: 0; margin-bottom: 15px;">Bulk Price Update</h4>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="bulk_purchase_price">Cost:</label>
									<input type="text" id="bulk_purchase_price" class="form-control input_number input-sm" placeholder="Enter purchase price">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="bulk_selling_price">Selling Price:</label>
									<input type="text" id="bulk_selling_price" class="form-control input_number input-sm" placeholder="Enter selling price">
								</div>
							</div>
							@if(isset($is_group_pricing_enabled) && $is_group_pricing_enabled && !empty($price_groups) && $price_groups->count() > 0)
								@foreach($price_groups->reverse() as $price_group)
									<div class="col-md-2">
										<div class="form-group">
											<label for="bulk_group_price_{{ $price_group->id }}">{{ $price_group->name }}:</label>
											<input type="text" id="bulk_group_price_{{ $price_group->id }}" class="form-control input_number input-sm bulk_group_price" data-group-id="{{ $price_group->id }}" placeholder="Enter price">
										</div>
									</div>
								@endforeach
							@endif
							<div class="col-md-1">
								<div class="form-group">
									<label>&nbsp;</label>
									<div>
										<button type="button" class="btn btn-primary btn-sm" id="apply_bulk_prices_btn" style="width: 100%;">
											<i class="fa fa-check"></i> Apply to All
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="table-responsive">
							<table class="table table-condensed table-bordered text-center table-striped" id="variations_table">
								<thead>
									<tr>
										<th>
											<input type="checkbox" id="select_all_checkbox" title="Select All">
										</th>
										<th class="variation-image-cell">Image</th>
										<th>Product Name</th>
										<th>Variation</th>
										<th>SKU</th>
										<th>Cost</th>
										<th style="width: 100px;">Selling Price</th>
										@if(isset($is_group_pricing_enabled) && $is_group_pricing_enabled && !empty($price_groups) && $price_groups->count() > 0)
											@foreach($price_groups->reverse() as $price_group)
												<th style="width: 100px;">{{ $price_group->name }}</th>
											@endforeach
										@endif
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="variations_tbody">
									<tr id="no_items_row">
										<td colspan="{{ 7 + (isset($is_group_pricing_enabled) && $is_group_pricing_enabled && $price_groups ? $price_groups->count() : 0) }}" class="text-center">No items added. Apply filters to load products/variations.</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<!-- Pagination Controls -->
						<div class="row" id="pagination_section" style="display: none;">
							<div class="col-sm-12">
								<div class="tw-flex tw-items-center tw-justify-between tw-mt-4 tw-pt-4 tw-border-t">
									<div class="tw-text-sm tw-text-gray-600" id="pagination_info">
										Showing 0 to 0 of 0 entries
									</div>
									<div class="tw-flex tw-items-center tw-gap-2">
										<button type="button" class="tw-dw-btn tw-dw-btn-sm" id="prev_page_btn" disabled style="background: #fff; border: 1px solid #d1d5db; color: #485769;">
											<i class="fa fa-chevron-left"></i> Previous
										</button>
										<span class="tw-px-3 tw-py-1 tw-bg-gray-100 tw-rounded" id="page_indicator">Page 1 of 1</span>
										<button type="button" class="tw-dw-btn tw-dw-btn-sm" id="next_page_btn" disabled style="background: #fff; border: 1px solid #d1d5db; color: #485769;">
											Next <i class="fa fa-chevron-right"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			{!! Form::hidden('submit_type', 'save', ['id' => 'submit_type']); !!}
			<div class="text-center">
      			<div class="btn-group">
          			<button type="submit" value="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white submit_form amazon-orange-btn">Save</button>
          		</div>
          	</div>
		</div>
	</div>
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			var addedVariations = {}; // Track added variations to avoid duplicates
			var isGroupPricingEnabled = {{ isset($is_group_pricing_enabled) && $is_group_pricing_enabled ? 'true' : 'false' }};
			var priceGroups = @json($price_groups ?? []); // Pass price groups from PHP
			priceGroups = [...priceGroups].reverse(); // Reverse to show highest tier on right, lowest on left
			var priceGroupPercentages = @json($price_group_percentage ?? []); // Pass price group percentages from PHP
			var hasMultipleLocations = {{ $has_multiple_locations ? 'true' : 'false' }};
			
			// Pagination state
			var currentPage = 1;
			var perPage = 15;
			var totalPages = 0;
			var totalItems = 0;

			// Initialize select2 for dropdowns
			$('.select2').select2();

			// Prevent form from submitting normally
			$('#selling_price_form').on('submit', function(e) {
				e.preventDefault();
				return false;
			});

			// Handle location change - refresh filter options
			$('#filter_location_id').on('change', function() {
				var locationId = $(this).val();
				loadFilterOptions(locationId);
			});

			// Load filter options on initial page load if location is pre-selected
			var initialLocationId = $('#filter_location_id').val();
			if (initialLocationId) {
				loadFilterOptions(initialLocationId);
			}

			// Load filter options based on location
			function loadFilterOptions(locationId) {
				$.ajax({
					url: '/products/get-filter-options',
					type: 'GET',
					dataType: 'json',
					data: {
						location_id: locationId || null
					},
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					success: function(response) {
						if (response.success) {
							// Update brand dropdown
							var brandSelect = $('#filter_brand_id');
							brandSelect.empty();
							brandSelect.append('<option value="">All</option>');
							$.each(response.brands, function(id, name) {
								brandSelect.append('<option value="' + id + '">' + name + '</option>');
							});
							brandSelect.trigger('change');

							// Update category dropdown
							var categorySelect = $('#filter_category_id');
							categorySelect.empty();
							categorySelect.append('<option value="">All</option>');
							$.each(response.categories, function(id, name) {
								categorySelect.append('<option value="' + id + '">' + name + '</option>');
							});
							categorySelect.trigger('change');
						}
					},
					error: function(xhr, status, error) {
						console.error('Error loading filter options:', error);
						toastr.error('Failed to load filter options');
					}
				});
			}

			// Apply filters button
			$('#apply_filters_btn').on('click', function() {
				loadFilteredProducts();
			});

			// Clear filters button
			$('#clear_filters_btn').on('click', function() {
				$('#filter_location_id').val(null).trigger('change');
				$('#filter_product_type').val(null).trigger('change');
				$('#filter_brand_id').val(null).trigger('change');
				$('#filter_category_id').val(null).trigger('change');
				
				// Reload filter options with no location
				if (hasMultipleLocations) {
					loadFilterOptions(null);
				}
				
				// Reset pagination
				currentPage = 1;
				hidePagination();
				
				// Clear table
				clearTable();
			});

			// Load filtered products with pagination
			function loadFilteredProducts(page) {
				page = page || 1;
				currentPage = page;
				
				var filters = {
					location_id: $('#filter_location_id').val() || null,
					product_type: $('#filter_product_type').val() || null,
					brand_id: $('#filter_brand_id').val() || null,
					category_id: $('#filter_category_id').val() || null,
					page: page,
					per_page: perPage
				};

				// Show loading
				$('#apply_filters_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

				$.ajax({
					url: '/products/get-filtered-products',
					type: 'GET',
					dataType: 'json',
					data: filters,
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					success: function(response) {
						$('#apply_filters_btn').prop('disabled', false).html('<i class="fa fa-filter"></i> Apply');
						
						if (response.success) {
							// Clear existing table
							clearTable();
							
							// Update price groups if available
							if (response.price_groups && response.price_groups.length > 0) {
								priceGroups = response.price_groups;
								// Update bulk price section with new price groups if needed
								updateBulkPriceSectionGroups(response.price_groups);
							}

							// Add all variations to table
							if (response.variations && response.variations.length > 0) {
								response.variations.forEach(function(variation) {
									if (!addedVariations[variation.id]) {
										addVariationToTable(variation, response.price_groups || priceGroups);
										addedVariations[variation.id] = true;
									}
								});
								
								// Update pagination
								if (response.pagination) {
									updatePagination(response.pagination);
								}
								
								toastr.success('Loaded ' + response.variations.length + ' of ' + response.pagination.total + ' product(s)/variation(s)');
							} else {
								toastr.warning('No products found matching the selected filters');
								hidePagination();
							}
						} else {
							toastr.error(response.msg || 'Failed to load products');
							hidePagination();
						}
					},
					error: function(xhr, status, error) {
						$('#apply_filters_btn').prop('disabled', false).html('<i class="fa fa-filter"></i> Apply');
						console.error('Error loading products:', error);
						
						var errorMsg = 'Failed to load products';
						if (xhr.responseJSON && xhr.responseJSON.msg) {
							errorMsg = xhr.responseJSON.msg;
						}
						toastr.error(errorMsg);
						hidePagination();
					}
				});
			}
			
			// Update pagination controls
			function updatePagination(pagination) {
				currentPage = pagination.current_page;
				totalPages = pagination.total_pages;
				totalItems = pagination.total;
				
				var start = ((currentPage - 1) * perPage) + 1;
				var end = Math.min(currentPage * perPage, totalItems);
				
				$('#pagination_info').text('Showing ' + start + ' to ' + end + ' of ' + totalItems + ' entries');
				$('#page_indicator').text('Page ' + currentPage + ' of ' + totalPages);
				
				// Enable/disable buttons
				$('#prev_page_btn').prop('disabled', currentPage <= 1);
				$('#next_page_btn').prop('disabled', currentPage >= totalPages);
				
				// Show pagination section
				if (totalItems > 0) {
					$('#pagination_section').show();
				} else {
					$('#pagination_section').hide();
				}
			}
			
			// Hide pagination
			function hidePagination() {
				$('#pagination_section').hide();
			}
			
			// Pagination button handlers
			$('#prev_page_btn').on('click', function() {
				if (currentPage > 1) {
					loadFilteredProducts(currentPage - 1);
				}
			});
			
			$('#next_page_btn').on('click', function() {
				if (currentPage < totalPages) {
					loadFilteredProducts(currentPage + 1);
				}
			});

			// Clear table
			function clearTable() {
				$('#variations_tbody').empty();
				addedVariations = {};
				var colspan = 7 + (isGroupPricingEnabled && priceGroups.length ? priceGroups.length : 0);
				$('#variations_tbody').html('<tr id="no_items_row"><td colspan="' + colspan + '" class="text-center">No items added. Apply filters to load products/variations.</td></tr>');
				updateBulkPriceSectionVisibility();
			}

			function addVariationToTable(variation, priceGroupsData) {
				// Remove "no items" row if it exists
				$('#no_items_row').remove();

				var variationId = variation.id;
				var defaultImg = '{{ asset("/img/default.png") }}';
				var imageUrl = (variation.image_url && variation.image_url.length) ? variation.image_url : defaultImg;
				var productName = (variation.product_name || 'N/A').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
				var variationName = (variation.variation_name || 'N/A').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
				var subSku = (variation.sub_sku || 'N/A').toString().replace(/</g, '&lt;').replace(/>/g, '&gt;');
				
				// Format current prices
				var purchasePrice = variation.purchase_price || 0;
				var sellPrice = variation.selling_price || 0;
				var groupPrices = variation.group_prices || {};

				// Build row HTML (image with fallback so variation images column always displays something)
				var row = '<tr data-variation-id="' + variationId + '">' +
					'<td><input type="checkbox" class="row-checkbox" data-variation-id="' + variationId + '"></td>' +
					'<td class="variation-image-cell"><img src="' + imageUrl + '" alt="" style="width: 50px; height: 50px; object-fit: cover; display: block;" onerror="this.src=\'' + defaultImg + '\'; this.onerror=null;"></td>' +
					'<td>' + productName + '</td>' +
					'<td>' + variationName + '</td>' +
					'<td>' + subSku + '</td>' +
					'<td>' +
						'<input type="text" name="variations[' + variationId + '][purchase_price]" ' +
						'class="form-control input_number input-sm bulk-purchase-price-input" ' +
						'placeholder="Purchase Price" value="' + (purchasePrice > 0 ? parseFloat(purchasePrice).toFixed(2) : '') + '">' +
					'</td>' +
					'<td style="width: 100px;">' +
						'<input type="text" name="variations[' + variationId + '][selling_price]" ' +
						'class="form-control input_number input-sm selling_price_input bulk-selling-price-input" ' +
						'placeholder="Selling Price" value="' + (sellPrice > 0 ? parseFloat(sellPrice).toFixed(2) : '') + '">' +
					'</td>';

				// Add group price inputs only if module is enabled (support string or number keys from API)
				if (isGroupPricingEnabled && priceGroupsData && priceGroupsData.length > 0) {
					priceGroupsData.forEach(function(pg) {
						var gp = groupPrices[pg.id] || groupPrices[String(pg.id)];
						var currentGroupPrice = (gp && gp.price != null && gp.price !== '') ? parseFloat(gp.price) : 0;
						row += '<td style="width: 100px;">' +
							'<input type="text" name="variations[' + variationId + '][group_prices][' + pg.id + '][price]" ' +
							'class="form-control input_number input-sm bulk-group-price-input" data-group-id="' + pg.id + '" ' +
							'placeholder="' + pg.name + '" value="' + (currentGroupPrice > 0 ? parseFloat(currentGroupPrice).toFixed(2) : '') + '">' +
							'<input type="hidden" name="variations[' + variationId + '][group_prices][' + pg.id + '][price_type]" value="fixed">' +
							'</td>';
					});
				}

				row += '<td>' +
					'<button type="button" class="btn btn-sm btn-danger remove-row">' +
					'<i class="fa fa-trash"></i> Remove' +
					'</button>' +
				'</td>' +
				'</tr>';

				$('#variations_tbody').append(row);
				
				// Show bulk price section if there are items
				updateBulkPriceSectionVisibility();
				
				// Initialize input_number formatting for new inputs
				$('#variations_tbody tr[data-variation-id="' + variationId + '"] input.input_number').each(function() {
					var $input = $(this);
					var value = $input.val();
					if (value && value !== '') {
						// Format the number if it exists
						if (typeof __write_number !== 'undefined') {
							var numValue = typeof __read_number !== 'undefined' ? __read_number($input) : parseFloat(value);
							if (!isNaN(numValue)) {
								__write_number($input, numValue);
							}
						}
					}
				});
				
				// Attach event listener to selling price input for auto-calculation
				attachSellingPriceChangeHandler(variationId);
			}
			
			// Function to calculate group prices based on percentage when selling price changes
			function calculateGroupPricesFromPercentage(sellingPriceInput) {
				var $row = $(sellingPriceInput).closest('tr');
				var variationId = $row.data('variation-id');
				
				// Get the selling price value
				var sellingPrice = 0;
				if (typeof __read_number !== 'undefined') {
					sellingPrice = __read_number($(sellingPriceInput));
				} else {
					sellingPrice = parseFloat($(sellingPriceInput).val()) || 0;
				}
				
				// Only calculate if selling price is valid and greater than 0
				if (sellingPrice <= 0 || isNaN(sellingPrice)) {
					return;
				}
				
				// Calculate group prices based on percentage settings
				if (isGroupPricingEnabled && priceGroups && priceGroups.length > 0 && priceGroupPercentages) {
					priceGroups.forEach(function(pg) {
						var percentage = priceGroupPercentages[pg.id];
						
						// Only calculate if percentage is set for this price group
						if (percentage !== undefined && percentage !== null && percentage !== '') {
							var percentageValue = parseFloat(percentage);
							
							// Calculate the group price: selling_price * (1 - percentage/100)
							// Percentage is a discount (e.g., 10 means 10% decrease)
							var calculatedPrice = sellingPrice * (1 - percentageValue / 100);
							
							// Find the group price input for this price group
							var $groupPriceInput = $row.find('.bulk-group-price-input[data-group-id="' + pg.id + '"]');
							
							if ($groupPriceInput.length > 0) {
								// Only update if the input is empty or was previously auto-calculated
								// Check if input has a data attribute to track if it was auto-calculated
								var wasAutoCalculated = $groupPriceInput.data('auto-calculated') === true;
								var currentValue = 0;
								if (typeof __read_number !== 'undefined') {
									currentValue = __read_number($groupPriceInput);
								} else {
									currentValue = parseFloat($groupPriceInput.val()) || 0;
								}
								
								// Update if empty or was previously auto-calculated
								if (currentValue === 0 || wasAutoCalculated) {
									if (typeof __write_number !== 'undefined') {
										__write_number($groupPriceInput, calculatedPrice);
									} else {
										$groupPriceInput.val(calculatedPrice.toFixed(2));
									}
									$groupPriceInput.data('auto-calculated', true);
								}
							}
						}
					});
				}
			}
			
			// Function to attach selling price change handler
			function attachSellingPriceChangeHandler(variationId) {
				var $row = $('#variations_tbody tr[data-variation-id="' + variationId + '"]');
				var $sellingPriceInput = $row.find('.selling_price_input');
				
				// Remove existing handlers to avoid duplicates
				$sellingPriceInput.off('change keyup blur');
				
				// Attach change handler
				$sellingPriceInput.on('change keyup blur', function() {
					// Use a small delay to ensure the value is updated
					setTimeout(function() {
						calculateGroupPricesFromPercentage($sellingPriceInput);
					}, 100);
				});
			}

			// Update bulk price section visibility
			function updateBulkPriceSectionVisibility() {
				var hasItems = $('#variations_tbody tr[data-variation-id]').length > 0;
				if (hasItems) {
					$('#bulk_price_section').show();
				} else {
					$('#bulk_price_section').hide();
				}
			}

			// Update bulk price section with dynamic price groups
			function updateBulkPriceSectionGroups(newPriceGroups) {
				// Remove existing dynamic group price inputs (keep static ones if they exist)
				$('#bulk_price_section .bulk_group_price').each(function() {
					var groupId = $(this).data('group-id');
					var existsInNew = newPriceGroups.some(function(pg) {
						return pg.id == groupId;
					});
					// Only remove if it doesn't exist in new groups (this handles dynamic additions)
					// For now, we'll keep the existing structure since price groups are loaded on page load
				});
			}

			// Apply bulk prices to selected rows only
			$('#apply_bulk_prices_btn').on('click', function() {
				// Get all checked rows
				var checkedRows = $('.row-checkbox:checked');
				
				if (checkedRows.length === 0) {
					toastr.warning('Please select at least one row to apply prices');
					return;
				}

				// Get bulk purchase price
				var bulkPurchasePrice = $('#bulk_purchase_price').val();
				if (bulkPurchasePrice && bulkPurchasePrice !== '') {
					var purchaseValue = typeof __read_number !== 'undefined' ? __read_number($('#bulk_purchase_price')) : parseFloat(bulkPurchasePrice);
					if (!isNaN(purchaseValue)) {
						checkedRows.each(function() {
							var variationId = $(this).data('variation-id');
							var row = $('tr[data-variation-id="' + variationId + '"]');
							var purchaseInput = row.find('.bulk-purchase-price-input');
							if (purchaseInput.length > 0) {
								if (typeof __write_number !== 'undefined') {
									__write_number(purchaseInput, purchaseValue);
								} else {
									purchaseInput.val(purchaseValue.toFixed(2));
								}
							}
						});
					}
				}

				// Get bulk selling price
				var bulkSellingPrice = $('#bulk_selling_price').val();
				if (bulkSellingPrice && bulkSellingPrice !== '') {
					var sellingValue = typeof __read_number !== 'undefined' ? __read_number($('#bulk_selling_price')) : parseFloat(bulkSellingPrice);
					if (!isNaN(sellingValue)) {
						checkedRows.each(function() {
							var variationId = $(this).data('variation-id');
							var row = $('tr[data-variation-id="' + variationId + '"]');
							var sellingInput = row.find('.bulk-selling-price-input');
							if (sellingInput.length > 0) {
								if (typeof __write_number !== 'undefined') {
									__write_number(sellingInput, sellingValue);
								} else {
									sellingInput.val(sellingValue.toFixed(2));
								}
								// Trigger group price calculation after setting selling price
								setTimeout(function() {
									calculateGroupPricesFromPercentage(sellingInput);
								}, 100);
							}
						});
					}
				}

				// Get bulk group prices
				$('.bulk_group_price').each(function() {
					var groupId = $(this).data('group-id');
					var groupPrice = $(this).val();
					if (groupPrice && groupPrice !== '') {
						var groupValue = typeof __read_number !== 'undefined' ? __read_number($(this)) : parseFloat(groupPrice);
						if (!isNaN(groupValue)) {
							checkedRows.each(function() {
								var variationId = $(this).data('variation-id');
								var row = $('tr[data-variation-id="' + variationId + '"]');
								var groupInput = row.find('.bulk-group-price-input[data-group-id="' + groupId + '"]');
								if (groupInput.length > 0) {
									if (typeof __write_number !== 'undefined') {
										__write_number(groupInput, groupValue);
									} else {
										groupInput.val(groupValue.toFixed(2));
									}
								}
							});
						}
					}
				});

				toastr.success('Prices applied to ' + checkedRows.length + ' selected item(s)');
			});

			// Remove row functionality
			$(document).on('click', '.remove-row', function() {
				var row = $(this).closest('tr');
				var variationId = row.data('variation-id');
				delete addedVariations[variationId];
				row.remove();

				// Add "no items" row if table is empty
				var colspan = 7 + (isGroupPricingEnabled && priceGroups.length ? priceGroups.length : 0);
				if ($('#variations_tbody tr').length === 0) {
					$('#variations_tbody').html('<tr id="no_items_row"><td colspan="' + colspan + '" class="text-center">No items added. Apply filters to load products/variations.</td></tr>');
				}
				
				// Update bulk price section visibility
				updateBulkPriceSectionVisibility();
			});

			// Select All checkbox functionality
			$('#select_all_checkbox').on('change', function() {
				var isChecked = $(this).is(':checked');
				$('.row-checkbox').prop('checked', isChecked);
			});

			// Update select all checkbox when individual checkboxes change
			$(document).on('change', '.row-checkbox', function() {
				var totalCheckboxes = $('.row-checkbox').length;
				var checkedCheckboxes = $('.row-checkbox:checked').length;
				$('#select_all_checkbox').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
			});

			$('button.submit_form').click( function(e){
				e.preventDefault();

				// Check if there are any items in the table
				if ($('#variations_tbody tr').length === 0 || $('#no_items_row').length > 0) {
					toastr.error('Please add at least one product/variation to update');
					return;
				}

				// Check if at least one price is filled OR if there are group price fields (even if empty, to allow deletion)
				var hasPrice = false;
				var hasGroupPriceFields = false;
				$('input[type="text"].input_number').each(function() {
					var $input = $(this);
					if ($input.val() && $input.val() !== '') {
						hasPrice = true;
					}
					// Check if this is a group price field
					if ($input.attr('name') && $input.attr('name').indexOf('[group_prices]') !== -1) {
						hasGroupPriceFields = true;
					}
				});

				if (!hasPrice && !hasGroupPriceFields) {
					toastr.error('Please enter at least one price to update or clear group prices');
					return;
				}

				// Collect all variation data
				var variationsData = {};
				$('#variations_tbody tr[data-variation-id]').each(function() {
					var variationId = $(this).data('variation-id');
					var variationData = {};

					// Get purchase price
					var purchasePrice = $(this).find('input[name*="[purchase_price]"]').val();
					if (purchasePrice && purchasePrice !== '') {
						variationData.purchase_price = purchasePrice;
					}

					// Get selling price
					var sellingPrice = $(this).find('input[name*="[selling_price]"]').val();
					if (sellingPrice && sellingPrice !== '') {
						variationData.selling_price = sellingPrice;
					}

					// Get group prices if enabled - include ALL group prices, even if empty
					if (isGroupPricingEnabled) {
						variationData.group_prices = {};
						// Collect all group price inputs for this variation row
						var $row = $(this);
						$row.find('input.bulk-group-price-input').each(function() {
							var $input = $(this);
							var name = $input.attr('name');
							if (name && name.indexOf('[group_prices]') !== -1 && name.indexOf('[price]') !== -1) {
								var matches = name.match(/variations\[(\d+)\]\[group_prices\]\[(\d+)\]\[price\]/);
								if (matches && matches.length === 3) {
									var priceGroupId = matches[2];
									// Get the value - if empty, send empty string explicitly
									var price = $input.val();
									if (price === undefined || price === null || price === '') {
										price = '';
									} else {
										price = String(price).trim();
									}
									// Always include, even if empty (so controller can delete them)
									variationData.group_prices[priceGroupId] = {
										price: price,
										price_type: 'fixed'
									};
								}
							}
						});
					}

					// Add variation if it has at least one price field
					// Include variations with empty group prices (to allow deletion)
					// If variationData has any keys (including group_prices with empty values), include it
					if (Object.keys(variationData).length > 0) {
						variationsData[variationId] = variationData;
					}
				});

				if (Object.keys(variationsData).length === 0) {
					toastr.error('Please enter at least one price to update');
					return;
				}

				// Get CSRF token
				var csrfToken = $('meta[name="csrf-token"]').attr('content') || 
				                $('input[name="_token"]').val() || 
				                '{{ csrf_token() }}';

				// Prepare JSON data
				var requestData = {
					variations: variationsData,
					_token: csrfToken
				};

				// Disable submit button and show loading
				var submitBtn = $(this);
				var originalText = submitBtn.html();
				submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

				// Send AJAX request
				$.ajax({
					url: '{{ action([\App\Http\Controllers\ProductController::class, "updateBulkSellingPrice"]) }}',
					type: 'POST',
					dataType: 'json',
					contentType: 'application/json',
					data: JSON.stringify(requestData),
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': csrfToken,
						'Accept': 'application/json'
					},
					success: function(response) {
						submitBtn.prop('disabled', false).html(originalText);
						
						if (response.success == 1 || response.success === true) {
							toastr.success(response.msg || 'Prices updated successfully');
							// Optionally reload the page or refresh the table after a delay
							setTimeout(function() {
								// You can reload the page or just show success message
								// window.location.reload();
							}, 1500);
						} else {
							toastr.error(response.msg || 'Failed to update prices');
						}
					},
					error: function(xhr, status, error) {
						submitBtn.prop('disabled', false).html(originalText);
						
						var errorMsg = 'Failed to update prices';
						if (xhr.responseJSON && xhr.responseJSON.msg) {
							errorMsg = xhr.responseJSON.msg;
						} else if (xhr.status === 422) {
							errorMsg = 'Validation error. Please check your input.';
						} else if (xhr.status === 500) {
							errorMsg = 'Server error. Please try again later.';
						}
						
						toastr.error(errorMsg);
						console.error('AJAX Error:', {
							status: xhr.status,
							statusText: xhr.statusText,
							responseText: xhr.responseText,
							error: error
						});
					}
				});
			});
		});
	</script>
@endsection

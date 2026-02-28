<style>
/* ========================================
   PRODUCT VARIATIONS SECTION - AMAZON THEME
   ======================================== */

.variations-section {
    margin-top: 24px;
}

.variations-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.variations-title {
    font-size: 15px;
    font-weight: 600;
    color: #0F1111;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.variations-title i {
    color: #FF9900;
}

.variations-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Amazon Theme Action Buttons */
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    border: 1px solid;
    white-space: nowrap;
    position: relative;
}

.btn-action:hover {
    text-decoration: none;
    transform: translateY(-1px);
}

.btn-action:active {
    transform: translateY(0);
}

.btn-action-primary {
    background: linear-gradient(180deg, #FF9900 0%, #E47911 100%);
    border-color: #C7511F;
    color: #0F1111;
}

.btn-action-primary:hover {
    background: linear-gradient(180deg, #FFB84D 0%, #FF9900 100%);
    box-shadow: 0 4px 12px rgba(255, 153, 0, 0.3);
    color: #0F1111;
}

.btn-action-secondary {
    background: linear-gradient(180deg, #FFFFFF 0%, #F7F8F8 100%);
    border-color: #D5D9D9;
    color: #0F1111;
}

.btn-action-secondary:hover {
    background: linear-gradient(180deg, #F7FAFA 0%, #E3E6E6 100%);
    border-color: #BBBFBF;
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    color: #0F1111;
}

.btn-action i {
    font-size: 12px;
}

/* View More Dropdown */
.dropdown-wrapper {
    position: relative;
    display: inline-block;
}

.dropdown-menu-custom {
    position: absolute;
    right: 0;
    top: calc(100% + 4px);
    z-index: 100;
    display: none;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
    min-width: 200px;
}

.dropdown-menu-custom.show {
    display: block;
    animation: dropdownFadeIn 0.15s ease;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    padding: 8px 16px;
    font-size: 11px;
    font-weight: 600;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
}

.dropdown-item-custom {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    font-size: 13px;
    color: #0F1111;
    cursor: pointer;
    transition: all 0.15s ease;
}

.dropdown-item-custom:hover {
    background: #FFF8E7;
    color: #C7511F;
}

.dropdown-item-custom input[type="radio"] {
    accent-color: #FF9900;
    width: 16px;
    height: 16px;
    cursor: pointer;
}

.dropdown-item-custom label {
    margin: 0;
    cursor: pointer;
    flex: 1;
}

/* Variations Table */
.variations-table-wrapper {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    overflow: hidden;
}

.variations-table-scroll {
    max-height: 45vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #D5D9D9 transparent;
}

.variations-table-scroll::-webkit-scrollbar {
    width: 6px;
}

.variations-table-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.variations-table-scroll::-webkit-scrollbar-thumb {
    background: #D5D9D9;
    border-radius: 3px;
}

.variations-table-scroll::-webkit-scrollbar-thumb:hover {
    background: #BBBFBF;
}

#productTable {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

#productTable thead {
    background: linear-gradient(180deg, #232F3E 0%, #1A252F 100%);
    position: sticky;
    top: 0;
    z-index: 10;
}

#productTable thead th {
    color: #FFFFFF;
    font-weight: 500;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 12px 14px;
    text-align: left;
    border: none;
    white-space: nowrap;
}

#productTable thead th:first-child {
    padding-left: 16px;
}

#productTable tbody tr {
    transition: background 0.15s ease;
    border-bottom: 1px solid #F3F4F6;
}

#productTable tbody tr:last-child {
    border-bottom: none;
}

#productTable tbody tr:hover {
    background: #FFF8E7;
}

#productTable tbody td {
    padding: 12px 14px;
    font-size: 13px;
    color: #0F1111;
    vertical-align: middle;
}

#productTable tbody td:first-child {
    padding-left: 16px;
}

/* Price Cell Styling */
#productTable tbody td:nth-child(4),
#productTable tbody td:nth-child(5),
#productTable tbody td:nth-child(6) {
    font-weight: 500;
}

/* Stock Values */
.stock-value {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    padding: 4px 8px;
    background: #E7F5E8;
    color: #067D17;
    border-radius: 4px;
    font-weight: 600;
    font-size: 12px;
}

.stock-value.low {
    background: #FEF2F2;
    color: #B91C1C;
}

.stock-value.warning {
    background: #FFF8E7;
    color: #B45309;
}

/* Product Thumbnail in Table */
.table-product-image {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid #E5E7EB;
    background: #FFF;
}

/* Price Text */
.price-text {
    color: #0F1111;
    font-weight: 500;
}

.price-text.primary {
    color: #B12704;
    font-weight: 600;
}

/* Empty State */
.empty-cell {
    color: #9CA3AF;
    font-style: italic;
}
</style>

<div class="variations-section">
    {{-- Header with Title and Actions --}}
    <div class="variations-header">
        <h4 class="variations-title">
            <i class="fas fa-layer-group"></i> @lang('product.variations')
        </h4>
        <div class="variations-actions">
            @can('product.update')
                <button class="btn-action btn-action-primary btn-modal-cl" 
                        data-href="/sells/pos/edit_price_product_modal/{{ $product_stock_details[0]->product_id }}/0" 
                        id="edit_price"
                        title="Edit product prices">
                    <i class="fas fa-dollar-sign"></i> Edit Price
                </button>
            @endcan
            
            <div class="dropdown-wrapper">
                <button id="toggleDropdown" class="btn-action btn-action-secondary" title="Additional view options">
                    <i class="fas fa-sliders-h"></i> View More
                    <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
                </button>
                <div id="columnMenu-table1" class="dropdown-menu-custom">
                    <div class="dropdown-header">Display Options</div>
                    <div class="dropdown-item-custom">
                        <input type="radio" name="column_option" value="current_stock_value" id="opt_stock_value">
                        <label for="opt_stock_value">Current Stock Value</label>
                    </div>
                    <div class="dropdown-item-custom">
                        <input type="radio" name="column_option" value="total_unit_sold" id="opt_unit_sold">
                        <label for="opt_unit_sold">Total Unit Sold</label>
                    </div>
                    <div class="dropdown-item-custom">
                        <input type="radio" name="column_option" value="total_unit_adjusted" id="opt_unit_adjusted">
                        <label for="opt_unit_adjusted">Total Unit Adjusted</label>
                    </div>
                    <div class="dropdown-item-custom" style="border-top: 1px solid #E5E7EB; margin-top: 4px; padding-top: 12px;">
                        <input type="radio" name="column_option" value="" id="opt_none" checked>
                        <label for="opt_none">None (Default)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Variations Table --}}
    <div class="variations-table-wrapper">
        <div class="variations-table-scroll">
            <table id="productTable">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var products = @json($product_stock_details);
        var priceGroupSequence = @json($price_group_name_to_sequence ?? []);
        var selectedColumn = null;

        function extractGroupPriceKeys(products) {
            let priceKeys = new Set();
            products.forEach(product => {
                if (product.group_prices) {
                    let priceEntries = product.group_prices.split(',');
                    priceEntries.forEach(entry => {
                        let key = entry.split(':')[0].trim();
                        if (key) {
                            priceKeys.add(key);
                        }
                    });
                }
            });

            let priceKeysArray = Array.from(priceKeys);
            if (Object.keys(priceGroupSequence).length > 0) {
                priceKeysArray.sort((a, b) => {
                    let seqA = priceGroupSequence[a] !== undefined ? priceGroupSequence[a] : 9999;
                    let seqB = priceGroupSequence[b] !== undefined ? priceGroupSequence[b] : 9999;
                    return seqA - seqB;
                });
            }

            return priceKeysArray;
        }

        function formatPrice(price) {
            return price ? `<span class="price-text">$${parseFloat(price).toFixed(2)}</span>` : '<span class="empty-cell">--</span>';
        }

        function formatStock(stock) {
            let value = parseFloat(stock || 0);
            let className = 'stock-value';
            if (value <= 0) {
                className += ' low';
            } else if (value < 10) {
                className += ' warning';
            }
            return `<span class="${className}">${value.toFixed(0)}</span>`;
        }

        function generateTable() {
            let groupPriceKeys = extractGroupPriceKeys(products);
            let thead = `
                <tr>
                    <th>Barcode</th>
                    <th>SKU</th>
                    <th>@lang('product.variations')</th>
                    <th>Selling Price</th>
                    <th>Stock Hand</th>
                    <th>Stock Available</th>`;

            groupPriceKeys.forEach(key => {
                let formattedKey = key.replace(/([a-z])([A-Z])/g, '$1 $2').split(' ')[0];
                thead += `<th>${formattedKey}</th>`;
            });

            @can('view_purchase_price')
                thead += `<th>Cost</th>`;
            @endcan

            if (selectedColumn) {
                let columnTitle = {
                    "current_stock_value": "Stock Value",
                    "total_unit_sold": "Units Sold",
                    "total_unit_adjusted": "Units Adjusted"
                };
                thead += `<th>${columnTitle[selectedColumn]}</th>`;
            }

            thead += `<th>Image</th></tr>`;
            $("#productTable thead").html(thead);

            let tbody = "";
            products.forEach(product => {
                let priceEntries = product.group_prices.split(',').reduce((acc, curr) => {
                    let [key, value] = curr.split(':');
                    acc[key.trim()] = value?.trim();
                    return acc;
                }, {});

                let variationName = (product.variation_name == null || product.variation_name == 'DUMMY') ? '<span class="empty-cell">--</span>' : product.variation_name;

                tbody += `
                    <tr>
                        <td>${product.barcode || '<span class="empty-cell">--</span>'}</td>
                        <td><strong>${product.sku || '<span class="empty-cell">--</span>'}</strong></td>
                        <td>${variationName}</td>
                        <td>${formatPrice(product.unit_price)}</td>
                        <td class="text-center">${formatStock(product.stock)}</td>
                        <td class="text-center">${formatStock(product.webstock)}</td>`;

                groupPriceKeys.forEach(key => {
                    tbody += `<td>${formatPrice(priceEntries[key])}</td>`;
                });

                @can('view_purchase_price')
                    tbody += `<td>${formatPrice(product.cost)}</td>`;
                @endcan

                if (selectedColumn) {
                    let columnValue = {
                        "current_stock_value": formatPrice(product.stock * product.unit_price),
                        "total_unit_sold": product.total_sold || '<span class="empty-cell">--</span>',
                        "total_unit_adjusted": product.total_adjusted || '<span class="empty-cell">--</span>'
                    };
                    tbody += `<td>${columnValue[selectedColumn]}</td>`;
                }

                let imageHtml = '<span class="empty-cell">--</span>';
                if (product.media && product.media[0]?.display_url) {
                    imageHtml = `<img src="${product.media[0].display_url}" alt="image" class="table-product-image">`;
                } else if (product.parent_product_image) {
                    imageHtml = `<img src="${product.parent_product_image}" alt="image" class="table-product-image">`;
                }

                tbody += `<td>${imageHtml}</td></tr>`;
            });

            $("#productTable tbody").html(tbody);
        }

        // Toggle dropdown
        $("#toggleDropdown").on("click", function(event) {
            event.stopPropagation();
            $("#columnMenu-table1").toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on("click", function(event) {
            if (!$(event.target).closest("#toggleDropdown, #columnMenu-table1").length) {
                $("#columnMenu-table1").removeClass('show');
            }
        });

        // Handle column option change
        $("input[name='column_option']").on("change", function() {
            selectedColumn = $(this).val() || null;
            generateTable();
        });

        // Initial table load
        generateTable();

        // Edit Price button handler
        $('#edit_price').on('click', function() {
            let url = $(this).data('href');
            let modalId = 'modal-' + new Date().getTime();

            $.ajax({
                url: url,
                success: function(response) {
                    let newModal = $('<div class="modal fade" id="' + modalId +
                        '" data-backdrop="static" data-keyboard="false">' +
                        '<div class="modal-content">' + response + '</div>' +
                        '</div>');
                    $('body').append(newModal);
                    newModal.modal('show');

                    newModal.on('hidden.bs.modal', function() {
                        $(this).remove();
                    });
                }
            });
        });
    });
</script>

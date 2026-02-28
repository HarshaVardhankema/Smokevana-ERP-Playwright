<div class="modal-dialog no-print  modal-xl" id='metrix_modal' role="document">
    <div class="modal-content">
        <div class="modal-header" style="padding: 5px 10px;">
            {{-- <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button> --}}
            <div class="tw-flex tw-justify-between">
                <h4 class="modal-title tw-flex" style="align-items: center" id="modalTitle">Item History</h4>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" data-dismiss="modal"
                    id='close_button'>@lang('messages.close')</button>

            </div>
        </div>
        <div class="modal-body">
            <div class="tw-bg-white tw-rounded-lg tw-shadow-lg  tw-p-4 ">
                <div class="tw-flex tw-justify-between">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('date_range', 'Date Range' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('date_range', null, ['placeholder' =>'Select a date range','class' => 'form-control','id' => 'timeFrameSelect']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('customer_id', __('contact.customer') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('customer_id', $contacts, null, ['class' => 'form-control
                                select2', 'placeholder' => __('lang_v1.all')]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('is_metrix', ' Enable Metrix' . ':') !!}
                            <br>
                            <label style="cursor: pointer;">
                                <input type="checkbox" 
                                       style="display: none;" 
                                       id="is_metrix"
                                       name="is_metrix"
                                       onchange="this.nextElementSibling.style.backgroundColor = this.checked ? '#4CAF50' : '#ccc'; 
                                                this.nextElementSibling.firstElementChild.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';">
                                <div style="width: 40px; height: 20px; background-color: #ccc; border-radius: 20px; position: relative; transition: background-color 0.3s; margin-bottom: 5px;">
                                    <div style="width: 18px; height: 18px; background-color: white; border-radius: 50%; position: absolute; top: 1px; left: 1px; transition: transform 0.3s;">
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="tw-overflow-x-auto" style="max-height:70vh; overflow-y: auto;">
                        <table class="tw-w-full tw-border tw-text-center" id="salesHistory">
                            <thead style="position: sticky; top: 0; ">
                                <tr>
                                    <th class="tw-p-2 tw-border">Date</th>
                                    <th class="tw-p-2 tw-border">Variation</th>
                                    <th class="tw-p-2 tw-border">Trans No</th>
                                    <th class="tw-p-2 tw-border">Location</th>
                                    <th class="tw-p-2 tw-border">Sell Qty</th>
                                    <th class="tw-p-2 tw-border">Picked Qty</th>
                                    <th class="tw-p-2 tw-border">Ordered Qty</th>
                                    <th class="tw-p-2 tw-border">Verified Qty</th>
                                    <th class="tw-p-2 tw-border">Unit Price</th>
                                    <th class="tw-p-2 tw-border">Cost Price</th>
                                    <th class="tw-p-2 tw-border">Profit/Loss @show_tooltip('Red Text indicates Loss, Green Text indicates Profit')</th>
                                    <th class="tw-p-2 tw-border">Current Price</th>
                                    <th class="tw-p-2 tw-border">Item Tax</th>
                                    <th class="tw-p-2 tw-border">Line Discount</th>
                                    <th class="tw-p-2 tw-border">Qty Available</th>
                                    <th class="tw-p-2 tw-border">Return Invoice No</th>
                                    <th class="tw-p-2 tw-border">Return Date</th>
                                    <th class="tw-p-2 tw-border">Return Qty</th>
                                    <th class="tw-p-2 tw-border">Customer</th>
                                    <th class="tw-p-2 tw-border">Sales Person</th>
                                    <th class="tw-p-2 tw-border">Picked By</th>
                                    <th class="tw-p-2 tw-border">Verified By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        let salesData = @json($purchaseHistory); 
        let tbody = $("#salesHistory tbody");
        tbody.empty(); 

        salesData.flat().forEach((sale) => {            
            if (sale.invoice_no) {           
                let date = sale.date??'-';
                let variation_id = sale.variation_id??'-';
                let invoice_no = sale.invoice_no??'-';
                let shipping_address = sale.shipping_address??'-';
                let sale_quantity = sale.sale_quantity??'-';
                let picked_quantity = sale.picked_quantity??'-';
                let ordered_quantity = sale.ordered_quantity??'-';
                let verified_qty = sale.verified_qty??'-';
                let unit_price = sale.unit_price?'$ '+parseFloat(sale.unit_price).toFixed(2):'-';
                let cost_price = sale.cost_price?'$ '+parseFloat(sale.cost_price).toFixed(2):'-';
                let current_price = sale.current_price?'$ '+parseFloat(sale.current_price).toFixed(2):'-';
                let item_tax = sale.item_tax?'$ '+parseFloat(sale.item_tax).toFixed(2):'-';
                let line_discount = sale.line_discount_amount?'$ '+parseFloat(sale.line_discount_amount).toFixed(2):'-';
                let qty_available = sale.qty_available?parseInt(sale.qty_available):'-';
                let return_invoice_no = sale.return_invoice_no??'-';
                let return_date = sale.return_date??'-';
                let return_qty = sale.return_quantity??'-';
                let customer = sale.contact?.name??'-';
                let sales_person = sale.sales_person?.name??'-';
                let picked_by = sale.picker?.name??'-';
                let verified_by = sale.verifier?.name??'-';
                let profit_loss ='';
                if(parseFloat(sale.unit_price) > parseFloat(sale.cost_price)){
                    profit_loss = "<span class='tw-text-green-500'> $ "+(parseFloat(sale.unit_price) - parseFloat(sale.cost_price)).toFixed(2)+"</span>";
                }else if(parseFloat(sale.unit_price) < parseFloat(sale.cost_price)){
                    profit_loss = "<span class='tw-text-red-500'> $ "+(parseFloat(sale.cost_price) - parseFloat(sale.unit_price)).toFixed(2)+"</span>";
                }else{
                    profit_loss = "-";
                }
                let row = `
                <tr>
                    <td class="tw-p-2 tw-border " >${date}</</td>
                    <td class="tw-p-2 tw-border">${variation_id}</td>
                    <td class="tw-p-2 tw-border">${invoice_no}</td>
                    <td class="tw-p-2 tw-border tw-text-left">${shipping_address || "-"}</td>
                    <td class="tw-p-2 tw-border">${sale_quantity}</td>
                    <td class="tw-p-2 tw-border">${picked_quantity}</td>
                    <td class="tw-p-2 tw-border">${ordered_quantity}</td>
                    <td class="tw-p-2 tw-border">${verified_qty}</td>
                    <td class="tw-p-2 tw-border">${unit_price}</td>
                    <td class="tw-p-2 tw-border">${cost_price}</td>
                    <td class="tw-p-2 tw-border">${profit_loss}</td>
                    <td class="tw-p-2 tw-border">${current_price}</td>
                    <td class="tw-p-2 tw-border">${item_tax}</td>
                    <td class="tw-p-2 tw-border">${line_discount}</td>
                    <td class="tw-p-2 tw-border">${qty_available}</td>
                    <td class="tw-p-2 tw-border">${return_invoice_no}</td>
                    <td class="tw-p-2 tw-border">${return_date}</td>
                    <td class="tw-p-2 tw-border">${return_qty}</td>
                    <td class="tw-p-2 tw-border">${customer}</td>
                    <td class="tw-p-2 tw-border">${sales_person}</td>
                    <td class="tw-p-2 tw-border">${picked_by}</td>
                    <td class="tw-p-2 tw-border">${verified_by}</td>
                </tr>`;
                tbody.append(row);
            }
        });
        if ($('#timeFrameSelect').length == 1) {
            $('#timeFrameSelect').daterangepicker(dateRangeSettings, function (start, end) {
                $('#timeFrameSelect').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                getSalesHistory();
            });
            $('#timeFrameSelect').on('cancel.daterangepicker', function (ev, picker) {
                $('#timeFrameSelect').val('');
                getSalesHistory();
            });
        }
        $('#is_metrix').on('change', function() {
            console.log('is_metrix');
            getSalesHistory();
        });
        $('#customer_id').on('change', function(e) {
            getSalesHistory();
        });
       function getSalesHistory() {
            var startDate = '';
            var endDate = '';
            if ($('#timeFrameSelect').val()) {
                startDate = $('input#timeFrameSelect')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                endDate = $('input#timeFrameSelect')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }
            let is_metrix = $('#is_metrix').is(':checked');
            let customer_id = $('#customer_id').val();
            let variation_id = {!! $variation_id !!};
            let url = `/sells/pos/history_modal?customer_id=${customer_id}&startDate=${startDate}&endDate=${endDate}&variation_id=${variation_id}&is_parent=${is_metrix}`;
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let tbody = $("#salesHistory tbody");
                    tbody.empty(); 
                    if (response.length === 0) {
                        tbody.append(
                            '<tr><td colspan="20" class="tw-text-center tw-p-2 tw-border">No Data Available</td></tr>'
                        );
                    } else {
                        response.flat().forEach((sale) => {
                            let date = sale.date ?? '-';
                            let variation_id = sale.variation_id ?? '-';
                            let invoice_no = sale.invoice_no ?? '-';
                            let shipping_address = sale.shipping_address ?? '-';
                            let sale_quantity = sale.sale_quantity ?? '-';
                            let picked_quantity = sale.picked_quantity ?? '-';
                            let ordered_quantity = sale.ordered_quantity ?? '-';
                            let verified_qty = sale.verified_qty ?? '-';
                            let unit_price = sale.unit_price ? '$ ' + parseFloat(sale.unit_price).toFixed(2) : '-';
                            let cost_price = sale.cost_price ? '$ ' + parseFloat(sale.cost_price).toFixed(2) : '-';
                            let current_price = sale.current_price ? '$ ' + parseFloat(sale.current_price).toFixed(2) : '-';
                            let item_tax = sale.item_tax ? '$ ' + parseFloat(sale.item_tax).toFixed(2) : '-';
                            let line_discount = sale.line_discount_amount ? '$ ' + parseFloat(sale.line_discount_amount).toFixed(2) : '-';
                            let qty_available = sale.qty_available ? parseInt(sale.qty_available) : '-';
                            let return_invoice_no = sale.return_invoice_no ?? '-';
                            let return_date = sale.return_date ?? '-';
                            let return_qty = sale.return_quantity ?? '-';
                            let customer = sale.contact?.name ?? '-';
                            let sales_person = sale.sales_person?.name ?? '-';
                            let picked_by = sale.picker?.name ?? '-';
                            let verified_by = sale.verifier?.name ?? '-';   
                            let profit_loss = '';
                            if(sale.unit_price > sale.cost_price){
                                profit_loss = "<span class='tw-text-green-500'> $ "+(sale.unit_price - sale.cost_price).toFixed(2)+"</span>";
                            }else if(sale.unit_price < sale.cost_price){
                                profit_loss = "<span class='tw-text-red-500'> $ "+(sale.cost_price - sale.unit_price).toFixed(2)+"</span>";
                            }else{
                                profit_loss = "-";
                            }
                            let row = `<tr>
                                <td class="tw-p-2 tw-border " >${date}</td>
                                <td class="tw-p-2 tw-border">${variation_id}</td>
                                <td class="tw-p-2 tw-border">${invoice_no}</td>
                                <td class="tw-p-2 tw-border tw-text-left">${shipping_address || "-"}</td>
                                <td class="tw-p-2 tw-border">${sale_quantity}</td>
                                <td class="tw-p-2 tw-border">${picked_quantity}</td>
                                <td class="tw-p-2 tw-border">${ordered_quantity}</td>
                                <td class="tw-p-2 tw-border">${verified_qty}</td>
                                <td class="tw-p-2 tw-border">${unit_price}</td>
                                <td class="tw-p-2 tw-border">${cost_price}</td>
                                <td class="tw-p-2 tw-border">${profit_loss}</td>
                                <td class="tw-p-2 tw-border">${current_price}</td>
                                <td class="tw-p-2 tw-border">${item_tax}</td>
                                <td class="tw-p-2 tw-border">${line_discount}</td>
                                <td class="tw-p-2 tw-border">${qty_available}</td>
                                <td class="tw-p-2 tw-border">${return_invoice_no}</td>
                                <td class="tw-p-2 tw-border">${return_date}</td>
                                <td class="tw-p-2 tw-border">${return_qty}</td>
                                <td class="tw-p-2 tw-border">${customer}</td>
                                <td class="tw-p-2 tw-border">${sales_person}</td>
                                <td class="tw-p-2 tw-border">${picked_by}</td>
                                <td class="tw-p-2 tw-border">${verified_by}</td>
                            </tr>`;
                            tbody.append(row);
                        });
                    }
                },
                error: function() {
                    alert("Error fetching data. Please try again.");
                }
            });
        }
    });
</script>
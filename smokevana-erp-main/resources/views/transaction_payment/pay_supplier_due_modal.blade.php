<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\TransactionPaymentController::class, 'postPayContactDue']), 'method' => 'post', 'id' => 'pay_contact_due_form', 'files' => true ]) !!}

    {!! Form::hidden("contact_id", $contact_details->contact_id);!!}
    {!! Form::hidden("due_payment_type", $due_payment_type); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.add_payment' ) jfhnjksdh</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        @if($due_payment_type == 'purchase')
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $contact_details->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $contact_details->supplier_business_name }}<br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('report.total_purchase'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase }}</span><br>
            <strong>@lang('contact.total_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_paid }}</span><br>
            <strong>@lang('contact.total_purchase_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase - $contact_details->total_paid }}</span><br>
             @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
                  <strong>@lang('lang_v1.opening_balance'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $contact_details->opening_balance }}</span><br>
                  <strong>@lang('lang_v1.opening_balance_due'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $ob_due }}</span>
              @endif
          </div>
        </div>
        @elseif($due_payment_type == 'purchase_return')
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $contact_details->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $contact_details->supplier_business_name }}<br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('lang_v1.total_purchase_return'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase_return }}</span><br>
            <strong>@lang('lang_v1.total_purchase_return_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
            <strong>@lang('lang_v1.total_purchase_return_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase_return - $contact_details->total_return_paid }}</span>
          </div>
        </div>
        @elseif(in_array($due_payment_type, ['sell']))
          <div class="col-md-6">
            <div class="well">
              <strong>@lang('sale.customer_name'): </strong>{{ $contact_details->name }}<br>
              <strong>@lang('Account No'): </strong>{{ $contact->contact_id ?? 'N/A' }}<br>
              <strong>@lang('Tier'): </strong>{{ $contact->customerGroup->name ?? 'N/A' }}<br>
              <strong>@lang('business.address'): </strong>{!! $contact->contact_address ?? 'N/A' !!}<br>
              <br><br>
            </div>
          </div>
          <div class="col-md-6">
            <div class="well">
              <strong>@lang('report.total_sell'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_invoice }}</span><br>
              <strong>@lang('contact.total_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_paid }}</span><br>
              <strong>@lang('contact.total_sale_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_invoice - $contact_details->total_paid }}</span><br>
              <strong>@lang('lang_v1.total_sell_return'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_sell_return }}</span><br>
              <strong>@lang('lang_v1.total_sell_return_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
              <strong>@lang('lang_v1.total_sell_return_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_sell_return - $contact_details->total_return_paid }}</span><br>
              @php
                $final_due = ($contact_details->total_invoice - $contact_details->total_paid ) - ($contact_details->total_sell_return - $contact_details->total_return_paid );
                $show_final_due = $final_due > 0 ? 'text-success' : 'text-danger';
                if($final_due < 0){
                  $final_due = $final_due * -1;
                }
              @endphp
              <strong>Final Due: </strong><span class="display_currency {{$show_final_due}}" data-currency_symbol="true">{{$final_due}}</span><br>
              @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
                  <strong>@lang('lang_v1.opening_balance'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $contact_details->opening_balance }}</span><br>
                  <strong>@lang('lang_v1.opening_balance_due'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $ob_due }}</span>
              @endif
            </div>
          </div>
         @elseif(in_array($due_payment_type, ['sell_return']))
         <div class="col-md-6">
          <div class="well">
            <strong>@lang('sale.customer_name'): </strong>{{ $contact_details->name }}<br>
              <br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('lang_v1.total_sell_return'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_sell_return }}</span><br>
            <strong>@lang('lang_v1.total_sell_return_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
            <strong>@lang('lang_v1.total_sell_return_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_sell_return - $contact_details->total_return_paid }}</span>
          </div>
        </div>
        @endif
      </div>
        @if(config('constants.show_payment_type_on_contact_pay') && ($due_payment_type == 'purchase' || $due_payment_type == 'sell'))
            @php
                $reverse_payment_types = [];

                if($due_payment_type == 'purchase') {
                    $reverse_payment_types = [
                        0 => __('lang_v1.pay_to_supplier'),
                        1 => __('lang_v1.receive_from_supplier')
                    ];
                } else if($due_payment_type == 'sell') {
                    $reverse_payment_types = [
                        0 => __('lang_v1.receive_from_customer'),
                        1 => __('lang_v1.pay_to_customer')
                    ];
                }
            @endphp
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label("is_reverse" , __('lang_v1.payment_type') . ':') !!}
                        {!! Form::select("is_reverse", $reverse_payment_types, 0, ['class' => 'form-control select2', 'style' => 'width:100%;']); !!}
                    </div>
                </div>
            </div>
        @endif
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              @php
    // Filter out custom payment methods 1-7, keep only Cash, Card, Check, Bank Transfer, Other
    $filtered_payment_types = [];
    $allowed_keys = ['cash', 'card', 'cheque', 'bank_transfer', 'other'];
    foreach ($payment_types as $key => $value) {
        if (in_array($key, $allowed_keys)) {
            $filtered_payment_types[$key] = $value;
        }
    }
@endphp
              {!! Form::select("method", $filtered_payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control', 'id' => 'paid_on', 'required', 'placeholder' => __('lang_v1.paid_on')]); !!}
            </div>
          </div>
        </div>
        {{-- main amount input --}}
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("amount" , __('Customer balance ') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                @if(in_array($due_payment_type, ['sell_return', 'purchase_return']))
                    {!! Form::text("amount", number_format($payment_line->amount, 2, '.', ','), [
                        'class' => 'form-control input_number payment_amount', 
                        'required', 
                        'placeholder' => __('sale.amount'), 
                        'data-rule-max-value' => $payment_line->amount, 
                        'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated]),
                    ]); !!}
                @else
                    {!! Form::text("amount", number_format($payment_line->amount, 2, '.', ','), [
                        'class' => 'form-control input_number payment_amount', 
                        'required', 
                        'placeholder' => __('sale.amount'),
                        'id' => 'total_payment_amount'
                    ]); !!}
                @endif
            </div>
        </div>   
        </div>
        {{-- pay by choice and pay by oldest transaction --}}
        @if ($due_payment_type == 'sell' || $due_payment_type == 'purchase')
            <div class="col-md-12 hide">
                <div class="well">
                  <label class="radio-inline">
                      <input type="radio" name="contact_type_radio" id="pay_by_oldest_transaction" value="pay_by_oldest_transaction" checked style="accent-color: red;">
                      Pay by oldest transaction
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="contact_type_radio" id="pay_by_choice" value="pay_by_choice" style=" accent-color: red;">
                    Pay by choice
                  </label>
                </div>
            </div>
        @endif
        {{-- pending transactions table --}}
        @if ($due_payment_type == 'sell' || $due_payment_type == 'purchase')
          <div class="col-md-12">
              <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('sale.due_date')</th>
                            <th>@lang('sale.invoice_amount')</th>
                            <th>@lang('sale.received_payments')</th>
                            {{-- <th>Sale Due</th> --}}
                            <th>Account Credit</th>
                            <th>@lang('sale.open_balance')</th>
                            <th>@lang('sale.payment')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending_transactions as $transaction)
                        @php
                          $due_amount = $transaction->due_amount - $transaction->return_due;
                          if($due_amount < 0){
                            $due_amount =$due_amount * -1;
                          }
                        @endphp
                            <tr>
                                <td>
                                    @php
                                        if($due_payment_type == 'sell') {
                                            $can_view = auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view') || auth()->user()->can('view_own_sell_only');
                                            $show_url = action([\App\Http\Controllers\SellController::class, 'show'], [$transaction->id]);
                                        } else {
                                            $can_view = auth()->user()->can('purchase.view') || auth()->user()->can('purchase.view_own');
                                            $show_url = action([\App\Http\Controllers\PurchaseController::class, 'show'], [$transaction->id]);
                                        }
                                    @endphp
                                    @if($can_view)
                                        <a href="#" 
                                           class="btn-modal invoice-link" 
                                           data-href="{{ $show_url }}"
                                           data-container=".view_modal"
                                           style="color: #007bff; text-decoration: underline; cursor: pointer;"
                                           title="Click to view/edit invoice">
                                            {{ $transaction->invoice_no }}
                                        </a>
                                    @else
                                        {{ $transaction->invoice_no }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($transaction->due_date))
                                        @php
                                            $date_format = session('business.date_format', 'Y-m-d');
                                            if($transaction->due_date instanceof \Carbon\Carbon) {
                                                echo $transaction->due_date->format($date_format);
                                            } else {
                                                echo \Carbon\Carbon::parse($transaction->due_date)->format($date_format);
                                            }
                                        @endphp
                                    @else
                                        {{ $transaction->payment_status }}
                                    @endif
                                </td>
                                <td>{{ number_format($transaction->final_total, 2, '.', ',') }}</td>
                                <td>{{ number_format($transaction->total_paid, 2, '.', ',') }}</td>
                                {{-- <td class="text-success">{{ number_format($transaction->due_amount, 2, '.', ',') }}</td> --}}
                                <td class="text-danger">{{ number_format($transaction->return_due, 2, '.', ',') }}</td>
                                <td class="{{ $transaction->due_amount - $transaction->return_due >0 ? 'text-success' : 'text-danger' }}">{{ number_format($due_amount, 2, '.', ',') }}</td>
                                <td>
                                    <input type="tel" 
                                          class="form-control input_number row_payment_amount"
                                          data-due_amount="{{ number_format($transaction->due_amount, 2, '.', '') }}"
                                          name="transactions[{{ $transaction->id }}]" 
                                          autocomplete="off"
                                          disabled>
                                </td>
                            </tr>
                        @endforeach
                        @if(count($pending_transactions) > 0)
                        <tr class="payment-total-row" style="background-color: #f5f5f5; font-weight: bold;">
                            <td colspan="6" class="text-right"><strong>Total:</strong></td>
                            <td>
                                <input type="text" 
                                      class="form-control payment-total-display" 
                                      id="payment_total_display"
                                      readonly
                                      style="background-color: #f5f5f5; font-weight: bold; text-align: right;"
                                      value="0.00">
                            </td>
                        </tr>
                        @endif
                    </tbody>
              </table>
          </div>
          <script>
              $(document).ready(function () {
                  function parseFormattedFloat(val) {
                      try {
                          return parseFloat(String(val || '0').replace(/,/g, '').trim()) || 0;
                      } catch (e) {
                          return 0;
                      }
                  }
          
                  function number_format(number, decimals, dec_point, thousands_sep) {
                      number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                      var n = !isFinite(+number) ? 0 : +number,
                          prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                          sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                          dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                          s = '',
                          toFixedFix = function (n, prec) {
                              var k = Math.pow(10, prec);
                              return '' + Math.round(n * k) / k;
                          };
                      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                      if (s[0].length > 3) {
                          s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                      }
                      if ((s[1] || '').length < prec) {
                          s[1] = s[1] || '';
                          s[1] += new Array(prec - s[1].length + 1).join('0');
                      }
                      return s.join(dec);
                  }
          
                  let mainAmount = parseFormattedFloat($('#total_payment_amount').val());
                  let inputTimers = {};
          
                  // Handle payment method dropdown change
                  $('.payment_types_dropdown').on('change', function() {
                      let method = $(this).val();
                      if(method === 'advance') {
                          // Autofill the amount input with advanceAmount
                          $('#total_payment_amount').val(number_format({{$advanceAmount}}, 2, '.', ','));
                          mainAmount = {{$advanceAmount}};
                          
                          // Enable the amount input for editing
                          $('#total_payment_amount').prop('readonly', true);
                          
                          // If pay by oldest transaction is selected, redistribute the amount
                          if ($('#pay_by_oldest_transaction').is(':checked')) {
                              distributeOldestFirst();
                          }
                      } else {
                          // For other payment methods, ensure the input is not readonly
                          $('#total_payment_amount').prop('readonly', false);
                      }
                  });
          
                  $('#total_payment_amount').on('input', function () {
                  mainAmount = parseFormattedFloat($(this).val());

                    if ($('#pay_by_oldest_transaction').is(':checked')) {
                        distributeOldestFirst();
                    } else if ($('#pay_by_choice').is(':checked')) {
let remaining = mainAmount;

                      $('.row_payment_amount').each(function () {
                          const $el = $(this);
                          let entered = parseFormattedFloat($el.val());
                          const dueAmount = parseFormattedFloat($el.data('due_amount'));

                          if (remaining <= 0) {
                              entered = 0;
                          } else if (entered > dueAmount) {
                              entered = dueAmount;
                          }

                          if (entered > remaining) {
                              entered = remaining;
                          }

                          $el.val(number_format(entered, 2, '.', ','));
                          remaining -= entered;
                      });
                      updatePaymentTotal();
                    }
                  });
                  $('#pay_by_choice').change(function () {
                      if ($(this).is(':checked')) {
                          $('.row_payment_amount').val('').prop('disabled', false);
                      }
                  });
          
                  $('#pay_by_oldest_transaction').change(function () {
                      if ($(this).is(':checked')) {
                          distributeOldestFirst();
                      }
                  });
          
                  $('.row_payment_amount').on('input', function (e) {
                      const $el = $(this);
                      const rawInput = $el.val();
                      const cleanInput = parseFormattedFloat(rawInput);
                      const dueAmount = parseFormattedFloat($el.data('due_amount'));
                      const currentValue = parseFormattedFloat($el.val());
                      const remainingMain = getRemainingMainAmount() + currentValue;
          
                      let validAmount = cleanInput;
                      if (cleanInput > dueAmount) validAmount = dueAmount;
                      if (validAmount > remainingMain) validAmount = remainingMain;
          
                      // Keep the raw input until formatting time
                      $el.val(rawInput);
          
                      const elId = $el.attr('name');
                      clearTimeout(inputTimers[elId]);
                      inputTimers[elId] = setTimeout(() => {
                          $el.val(number_format(validAmount, 2, '.', ','));
                          updatePaymentTotal();
                      }, 1000);
                      // Update total immediately with current values
                      updatePaymentTotal();
                  });
          
                  // Optional: Format on blur immediately
                  $('.row_payment_amount').on('blur', function () {
                      const val = parseFormattedFloat($(this).val());
                      $(this).val(number_format(val, 2, '.', ','));
                      updatePaymentTotal();
                  });
                  
                  // Function to calculate and update payment total
                  function updatePaymentTotal() {
                      // Only update if total row exists
                      if ($('#payment_total_display').length === 0) {
                          return;
                      }
                      let total = 0;
                      $('.row_payment_amount').each(function () {
                          total += parseFormattedFloat($(this).val());
                      });
                      $('#payment_total_display').val(number_format(total, 2, '.', ','));
                  }
          
                  function getRemainingMainAmount() {
                      let totalAllocated = 0;
                      $('.row_payment_amount').each(function () {
                          totalAllocated += parseFormattedFloat($(this).val());
                      });
                      return mainAmount - totalAllocated;
                  }
          
                  function distributeOldestFirst() {
                      let remainingAmount = mainAmount;
                      $('.row_payment_amount').val('').prop('disabled', true);
          
                      $('.row_payment_amount').each(function () {
                          if (remainingAmount <= 0) return false;
          
                          let dueAmount = parseFormattedFloat($(this).data('due_amount'));
                          let payAmount = Math.min(dueAmount, remainingAmount);
          
                          $(this).prop('disabled', false).val(number_format(payAmount, 2, '.', ','));
                          remainingAmount -= payAmount;
                      });
                      updatePaymentTotal();
                  }
          
                  // Cleanup on form submit
                  $('form').on('submit', function () {
                      $('.row_payment_amount').each(function () {
                          const raw = parseFormattedFloat($(this).val());
                          $(this).val(raw);
                      });
                  });
          
                  // Auto-distribute if "oldest" is selected initially
                  if ($('#pay_by_oldest_transaction').is(':checked')) {
                      distributeOldestFirst();
                  }
                  
                  // Initialize total on page load
                  updatePaymentTotal();

                  // notification js

                  let SellType=@json($due_payment_type);

                  $('.payment_types_dropdown').on('change', function() {
                     let method = $(this).val();
                     if(method==='custom_pay_1'&&SellType==='sell'){
                      $('#submit').addClass('hide');
                      $('#send_mail').removeClass('hide');
                     }else{
                      $('#submit').removeClass('hide');
                      $('#send_mail').addClass('hide');
                     }
                    });

                    $('#send_mail').on('click', function(e) {
                                e.preventDefault();
                                let formData = new FormData($('#pay_contact_due_form')[0]);
                                console.log("Form submitted successfully");
                                $.ajax({
                                    url: "/notification/get-template-payment",
                                    type: "POST",
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(modalContent) {
                                        $('.view_modal').html(modalContent).modal('show');
                                    },
                                    error: function() {
                                        toastr.error("Failed to load the email template.");
                                    }
                                });
                            });
              });
          </script>
        @endif
        @php
            $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];

            $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
        @endphp

        @if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens')
            <input type="hidden" class="enable_cash_denomination_for_payment_methods" value="{{json_encode($pos_settings['enable_cash_denomination_for_payment_methods'])}}">
            <div class="clearfix"></div>
            <div class="col-md-12 cash_denomination_div @if(!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)) hide @endif">
                <hr>
                <strong>@lang( 'lang_v1.cash_denominations' )</strong>
                  @if(!empty($pos_settings['cash_denominations']))
                    <table class="table table-slim">
                      <thead>
                        <tr>
                          <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-center">@lang('lang_v1.count')</th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-left">@lang('sale.subtotal')</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach(explode(',', $pos_settings['cash_denominations']) as $dnm)
                        <tr>
                          <td class="text-right">{{$dnm}}</td>
                          <td class="text-center" >X</td>
                          <td>{!! Form::number("denominations[$dnm]", null, ['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;' ]); !!}</td>
                          <td class="text-center">=</td>
                          <td class="text-left">
                            <span class="denomination_subtotal">0</span>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="4" class="text-center">@lang('sale.total')</th>
                          <td>
                            <span class="denomination_total">0</span>
                            <input type="hidden" class="denomination_total_amount" value="0">
                            <input type="hidden" class="is_strict" value="{{$pos_settings['cash_denomination_strict_check'] ?? ''}}">
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                    <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
                  @else
                    <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
                  @endif
            </div>
        @endif

        <div class="clearfix"></div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document', ['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <p class="help-block">
            @includeIf('components.document_help_text')</p>
          </div>
        </div>
        @if(!empty($accounts))
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
        @endif
        <div class="clearfix"></div>

          @include('transaction_payment.payment_type_details')
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id='submit'>@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white hide" id='send_mail'>Send</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
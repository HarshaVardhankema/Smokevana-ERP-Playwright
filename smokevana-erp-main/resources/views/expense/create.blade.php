@extends('layouts.app')
@section('title', __('expense.add_expense'))

@section('css')
@include('layouts.partials.amazon_admin_styles')
<style>
    .expense-create-page { background: #EAEDED; min-height: 100%; padding-bottom: 2rem; }
    .add-expense-banner { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%); border-radius: 0 0 10px 10px; padding: 22px 28px; margin-bottom: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; position: relative; overflow: hidden; }
    .add-expense-banner::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #ff9900; z-index: 1; }
    .add-expense-banner .banner-content { display: flex; flex-direction: column; gap: 4px; }
    .add-expense-banner .banner-title { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 700; margin: 0; color: #fff !important; }
    .add-expense-banner .banner-title i { color: #fff !important; }
    .add-expense-banner .banner-subtitle { font-size: 13px; color: rgba(255,255,255,0.9) !important; margin: 4px 0 0 0; }
    .amazon-orange-btn { background: linear-gradient(to bottom, #FF9900 0%, #E47911 100%) !important; border-color: #C7511F !important; color: #fff !important; font-weight: 600; padding: 10px 24px; border-radius: 6px; }
    .amazon-orange-btn:hover { color: #fff !important; opacity: 0.95; }
    /* Amazon-style section cards */
    .expense-create-page .expense-section-card { margin-bottom: 20px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #D5D9D9; }
    .expense-create-page .expense-section-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%); color: #fff; padding: 14px 20px; display: flex; align-items: center; gap: 10px; font-size: 1rem; font-weight: 600; position: relative; }
    .expense-create-page .expense-section-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .expense-create-page .expense-section-header i { color: #fff; font-size: 18px; }
    .expense-create-page .expense-section-body { background: #f7f8f8; padding: 1.25rem 1.5rem; }
    .expense-create-page .expense-section-body .form-group { margin-bottom: 0.75rem; }
    .expense-create-page .expense-section-body label { color: #0F1111 !important; font-size: 0.8125rem; }
    .expense-create-page .expense-section-body .form-control { background: #fff; border: 1px solid #D5D9D9; color: #0F1111; font-size: 0.8125rem; padding: 0.375rem 0.5rem; min-height: 2rem; box-sizing: border-box; }
    .expense-create-page .expense-section-body .form-control:focus { border-color: #FF9900; outline: none; box-shadow: 0 0 0 2px rgba(255,153,0,0.2); }
    .expense-create-page .expense-section-body .help-block { color: #565959; font-size: 0.8125rem; }
    .expense-create-page .expense-section-body input[type="checkbox"] { accent-color: #FF9900; }
    .expense-create-page .expense-section-body .box { border: none; background: transparent; box-shadow: none; margin: 0; }
    .expense-create-page .expense-section-body .box-body { padding: 0; background: transparent; border: none; }
    /* Payment widget as section card */
    .expense-create-page #payment_rows_div { border-radius: 10px; overflow: hidden; border: 1px solid #D5D9D9; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .expense-create-page #payment_rows_div .box-header { background: linear-gradient(135deg, #232f3e 0%, #37475a 100%) !important; color: #fff !important; border: none !important; padding: 14px 20px !important; position: relative; }
    .expense-create-page #payment_rows_div .box-header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: #ff9900; }
    .expense-create-page #payment_rows_div .box-title { color: #fff !important; font-weight: 600; }
    .expense-create-page #payment_rows_div .tw-flow-root { background: #f7f8f8 !important; padding: 1.25rem 1.5rem !important; }
</style>
@endsection

@section('content')

{!! Form::open(['url' => action([\App\Http\Controllers\ExpenseController::class, 'store']), 'method' => 'post', 'id' => 'add_expense_form', 'files' => true ]) !!}
<!-- Amazon-style banner -->
<section class="content-header">
    <div class="add-expense-banner">
        <div class="banner-content">
            <h1 class="banner-title"><i class="fas fa-plus-circle"></i> @lang('expense.add_expense')</h1>
            <p class="banner-subtitle">Record a new expense. Select location, category, and add details.</p>
        </div>
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white amazon-orange-btn">@lang('messages.save')</button>
    </div>
</section>

<!-- Main content -->
<section class="content expense-create-page">
	<!-- Expense Details card -->
	<div class="expense-section-card">
		<div class="expense-section-header">
			<i class="fas fa-file-invoice-dollar"></i>
			<span>@lang('expense.expense_details')</span>
		</div>
		<div class="expense-section-body">
			<div class="row">

				@if(count($business_locations) == 1)
					@php 
						$default_location = current(array_keys($business_locations->toArray())) 
					@endphp
				@else
					@php $default_location = null; @endphp
				@endif
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required'], $bl_attributes);!!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_category_id', __('expense.expense_category').':') !!}
						{!! Form::select('expense_category_id', $expense_categories, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
			            {!! Form::label('expense_sub_category_id', __('product.sub_category') . ':') !!}
			              {!! Form::select('expense_sub_category_id', [],  null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
			          </div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
						<p class="help-block">
			                @lang('lang_v1.leave_empty_to_autogenerate')
			            </p>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_transaction_date']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('expense_for', __('expense.expense_for').':') !!} @show_tooltip(__('tooltip.expense_for'))
						{!! Form::select('expense_for', $users, null, ['class' => 'form-control select2']); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('lang_v1.expense_for_contact').':') !!} 
						{!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                        {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                        @includeIf('components.document_help_text')</p></small>
                    </div>
                </div>
				<div class="col-md-4">
			    	<div class="form-group">
			            {!! Form::label('tax_id', __('product.applicable_tax') . ':' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('tax_id', $taxes['tax_rates'], null, ['class' => 'form-control'], $taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
							value="0">
			            </div>
			        </div>
			    </div>
			    <div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('final_total', __('sale.total_amount') . ':*') !!}
						{!! Form::number('final_total', number_format(0.00, 2, '.', ''), [
							'class' => 'form-control input_number',
							'placeholder' => __('sale.total_amount'),
							'required' => true,
							'min' => '0.01',
							'step' => '0.01',
							'onkeypress' => 'if(event.which === 45 || event.which === 101) return false;', // prevent minus and 'e'
							'oninput' => "this.value = Math.abs(parseFloat(this.value)) || 0;" // force positive float
							]) !!}
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('additional_notes', __('expense.expense_note') . ':') !!}
								{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
				</div>
				<div class="col-md-4 col-sm-6">
					<br>
					<label>
		              {!! Form::checkbox('is_refund', 1, false, ['class' => 'input-icheck', 'id' => 'is_refund']); !!} @lang('lang_v1.is_refund')?
		            </label>@show_tooltip(__('lang_v1.is_refund_help'))
				</div>
			</div>
		</div>
	</div>
	<!-- Recurring Expense card -->
	<div class="expense-section-card">
		<div class="expense-section-header">
			<i class="fas fa-redo"></i>
			<span>@lang('lang_v1.recurring_expense')</span>
		</div>
		<div class="expense-section-body">
			@include('expense.recur_expense_form_part')
		</div>
	</div>
	@component('components.widget', ['class' => 'box-solid', 'id' => "payment_rows_div", 'title' => __('purchase.add_payment')])
	<div class="payment_row">
		@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true])
		<hr>
		<div class="row">
			<div class="col-sm-12">
				<div class="pull-right">
					<strong>@lang('purchase.payment_due'):</strong>
					<span id="payment_due">{{@num_format(0)}}</span>
				</div>
			</div>
		</div>
	</div>
	@endcomponent
	<div class="col-sm-12 text-center">
		
	</div>
{!! Form::close() !!}
</section>
@endsection
@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		$('.paid_on').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });
	});
	
	__page_leave_confirmation('#add_expense_form');
	$(document).on('change', 'input#final_total, input.payment-amount', function() {
		calculateExpensePaymentDue();
	});

	function calculateExpensePaymentDue() {
		var final_total = __read_number($('input#final_total'));
		var payment_amount = __read_number($('input.payment-amount'));
		var payment_due = final_total - payment_amount;
		$('#payment_due').text(__currency_trans_from_en(payment_due, true, false));
	}

	$(document).on('change', '#recur_interval_type', function() {
	    if ($(this).val() == 'months') {
	        $('.recur_repeat_on_div').removeClass('hide');
	    } else {
	        $('.recur_repeat_on_div').addClass('hide');
	    }
	});

	$('#is_refund').on('ifChecked', function(event){
		$('#recur_expense_div').addClass('hide');
		$('#recur_expense_div').closest('.expense-section-card').addClass('hide');
	});
	$('#is_refund').on('ifUnchecked', function(event){
		$('#recur_expense_div').removeClass('hide');
		$('#recur_expense_div').closest('.expense-section-card').removeClass('hide');
	});
	// Sync card visibility with inner recur div on load
	if ($('#recur_expense_div').hasClass('hide')) {
		$('#recur_expense_div').closest('.expense-section-card').addClass('hide');
	}

	$(document).on('change', '.payment_types_dropdown, #location_id', function(e) {
	    var default_accounts = $('select#location_id').length ? 
	                $('select#location_id')
	                .find(':selected')
	                .data('default_payment_accounts') : [];
	    var payment_types_dropdown = $('.payment_types_dropdown');
	    var payment_type = payment_types_dropdown.val();
	    if (payment_type) {
	        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
	            default_accounts[payment_type]['account'] : '';
	        var payment_row = payment_types_dropdown.closest('.payment_row');
	        var row_index = payment_row.find('.payment_row_index').val();

	        var account_dropdown = payment_row.find('select#account_' + row_index);
	        if (account_dropdown.length && default_accounts) {
	            account_dropdown.val(default_account);
	            account_dropdown.change();
	        }
	    }
		    // Custom validator: payment must not be greater than total
			$.validator.addMethod("notGreaterThanTotal", function(value, element) {
        var final_total = parseFloat(__read_number($('#final_total'))) || 0;
        var payment_amount = parseFloat(__read_number($(element))) || 0;
        return payment_amount <= final_total;
    }, "@lang('messages.payment_cannot_exceed_total')");

     // Custom validator: payment must not be greater than total
	 $.validator.addMethod("notGreaterThanTotal", function(value, element) {
        var final_total = parseFloat(__read_number($('#final_total'))) || 0;
        var payment_amount = parseFloat(__read_number($(element))) || 0;
        return payment_amount <= final_total;
    }, "@lang('messages.payment_cannot_exceed_total')");

    // Initialize validation
    $('#add_expense_form').validate({
        ignore: [],
        rules: {
            location_id: {
                required: true
            },
            transaction_date: {
                required: true,
                date: true
            },
            final_total: {
                required: true,
                number: true,
                min: 0.01
            },
            "payment[0][amount]": {
                required: true,
                number: true,
                min: 0,
                notGreaterThanTotal: true
            },
            "payment[0][method]": {
                required: true
            },
            document: {
                extension: "{{ implode('|', array_map(function($ext) { return ltrim($ext, '.'); }, array_keys(config('constants.document_upload_mimes_types')))) }}"
            }
        },
        messages: {
            location_id: "This field is required",
            expense_category_id: "This field is required",
            transaction_date: {
                required: "This field is required",
                date: "@lang('messages.valid_date')"
            },
            final_total: {
                required: "This field is required",
                number: "@lang('messages.valid_number')",
                min: "@lang('messages.value_greater_than_zero')"
            },
            "payment[0][amount]": {
                required: "This field is required",
                number: "@lang('messages.valid_number')",
                min: "@lang('messages.value_greater_than_zero')"
            },
            "payment[0][method]": "This field is required",
            document: "@lang('messages.file_format_not_supported')"
        },
        errorClass: "invalid-feedback", // Add this class to make error message more consistent with Bootstrap
        validClass: "is-valid", // Optional: Mark fields that are valid
        errorPlacement: function(error, element) {
            // Check if the element is a select2, and position error below the select2 dropdown
            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2'));
            } else {
                error.insertAfter(element); // Default placement after the field
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        }
    });
	$('form').on('submit', function(e) {
        let finalTotal = parseFloat($('input[name="final_total"]').val());
        if (isNaN(finalTotal) || finalTotal < 0.01) {
            alert("Total amount must be at least 0.01.");
            e.preventDefault();
        }
    });
	});
</script>
@endsection
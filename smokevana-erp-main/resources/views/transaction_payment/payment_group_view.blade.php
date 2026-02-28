<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"></button>
      <h4 class="modal-title no-print">Edit Payment Group</h4>
    </div>
    <div class="tw-flex tw-justify-end tw-gap-5" style="margin-top: -50px; margin-right: 10px;">
      <button type="button" class="btn btn-primary" id="save-payment-group-btn">
        <i class="fa fa-save"></i> Save Changes
      </button>
      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        
    </div>
    <div class="modal-body">
      <form id="edit-payment-group-form">
        @csrf
        <input type="hidden" name="group_ref_no" value="{{ $data['group_ref_no'] ?? '' }}">
        
        <!-- Group Summary -->
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="group_ref_no">Group Reference No:</label>
              <input type="text" class="form-control" id="group_ref_no" name="group_ref_no" value="{{ $data['group_ref_no'] ?? '' }}" readonly>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="group_amount">Total Group Paid Amount:</label>
              <input type="text" class="form-control" id="group_amount" name="group_amount" value="{{ number_format($data['group_amount'] ?? 0, 2) }}" readonly>
            </div>
          </div>
        </div>
        <hr>
        
        <!-- Payment Lines -->
        <h5>Payment Lines</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Invoice No</th>
                <th>Total Amount</th>
                <th>Total Paid</th>
                <th>Paid Amount</th>
              </tr>
            </thead>
            <tbody id="payment-lines-tbody">
              @if(isset($data['transaction_rows']) && is_array($data['transaction_rows']))
                @foreach($data['transaction_rows'] as $index => $row)
                  <tr data-transaction-id="{{ $row['transaction_id'] }}">
                    <td>
                      <input type="text" class="form-control" name="transaction_rows[{{ $index }}][invoice_no]" value="{{ $row['invoice_no'] }}" readonly>
                      <input type="hidden" name="transaction_rows[{{ $index }}][transaction_payment_group_id]" value="{{ $row['transaction_payment_group_id'] }}">
                      <input type="hidden" name="transaction_rows[{{ $index }}][transaction_id]" value="{{ $row['transaction_id'] }}">
                      <input type="hidden" name="transaction_rows[{{ $index }}][payment_line_id]" value="{{ $row['payment_method_id'] }}">
                    </td>
                    <td>
                      <div class='input-group'>
                        <span class="input-group-addon">
                          <i class="fa fa-dollar-sign"></i>
                        </span>
                        <input type="text" class="form-control" name="transaction_rows[{{ $index }}][total_amount]" value="{{ number_format($row['total_amount'], 2) }}" readonly>
                      </div>
                    </td>
                    <td>
                      <div class='input-group'>
                        <span class="input-group-addon">
                          <i class="fa fa-dollar-sign"></i>
                        </span>
                        <input type="text" class="form-control" name="transaction_rows[{{ $index }}][total_paid_amount]" value="{{ number_format($row['total_paid_amount'], 2) }}" readonly>
                      </div>
                    </td>
                    <td>
                      <div class='input-group'>
                        <span class="input-group-addon">
                          <i class="fa fa-dollar-sign"></i>
                        </span>
                      <input type="number"
                        class="form-control paid-amount-input"
                        name="transaction_rows[{{ $index }}][paid_amount]"
                        value="{{ number_format($row['paid_amount'], 2, '.', '') }}"
                        step="0.01"
                        min="0"
                        max="{{ number_format($row['total_amount'], 2, '.', '') }}"
                        data-total-amount="{{ number_format($row['total_amount'], 2, '.', '') }}"
                        data-total-paid="{{ number_format($row['total_paid_amount'], 2, '.', '') }}"
                        data-transaction-id="{{ number_format($row['transaction_id'], 2, '.', '') }}">
                          <span class="error-message"></span>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </form>
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-md-6">
            <div class="alert alert-info">              
              <strong >Total Amount: <span id="total-paid-display">{{ number_format($data['group_amount'] ?? 0, 2) }}</span></strong>
            </div>
          </div>
          <div class="col-md-6">
            <div class="alert alert-warning">
              <strong>Remaining Amount: <span id="remaining-amount-display">0.00</span></strong>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
      // Format and validate on blur
      $('.paid-amount-input').on('blur', function() {
          let val = parseFloat($(this).val()) || 0;
          $(this).val(val.toFixed(2)); // always show 2 decimals
          validateInput($(this));
          calculateTotals();
      });
  
      // Validate on input change
      $('.paid-amount-input').on('input', function() {
          validateInput($(this));
          calculateTotals();
      });
  
      // Validation function
      function validateInput(input) {
          let amount = parseFloat(input.val()) || 0;
          let max = parseFloat(input.data('total-amount')) || 0;
  
          if (amount > max) {
              input.closest('tr').find('.error-message')
                  .text('Paid amount cannot exceed ' + max.toFixed(2) + '.')
                  .css('color', 'red');
              input.addClass('is-invalid');
              return false;
          } else if (amount < 0) {
              input.closest('tr').find('.error-message')
                  .text('Paid amount cannot be negative.')
                  .css('color', 'red');
              input.addClass('is-invalid');
              return false;
          } else {
              input.closest('tr').find('.error-message').text('');
              input.removeClass('is-invalid');
              return true;
          }
      }
  
      // Calculate totals function
      function calculateTotals() {
          let totalPaid = 0;
          let totalAmount = parseFloat('{{ $data["group_amount"] ?? 0 }}') || 0;
          
          $('.paid-amount-input').each(function() {
              let paidAmount = parseFloat($(this).val()) || 0;
              totalPaid += paidAmount;
          });
  
          let remaining = totalAmount - totalPaid;
  
          $('#total-paid-display').html('$' + totalPaid.toFixed(2));
          $('#remaining-amount-display').html('$' + remaining.toFixed(2));
  
          // Update border color
          if (remaining < 0) {
              $('.alert-info').css('border', '2px solid red');
          } else if (remaining > 0) {
              $('.alert-info').css('border', '2px solid green');
          } else {
              $('.alert-info').css('border', 'none');
          }
      }
  
      // Save payment group with validation
      $('#save-payment-group-btn').on('click', function() {
          let isValid = true;
  
          $('.paid-amount-input').each(function() {
              if (!validateInput($(this))) {
                  isValid = false;
              }
          });
  
          if (!isValid) {
              toastr.error('Please correct validation errors before saving.');
              return;
          }
  
          let formData = $('#edit-payment-group-form').serialize();
  
          $.ajax({
              url: '/payment-group/{{ $data["group_ref_no"] ?? "" }}',
              method: 'PUT',
              data: formData,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                  if (response.success) {
                      toastr.success('Payment group updated successfully!');
                      $('#payment-group-modal').modal('hide');
                      location.reload();
                  } else {
                      toastr.error(response.msg || 'Error updating payment group');
                  }
              },
              error: function(xhr) {
                  let errorMsg = 'Error updating payment group';
                  if (xhr.responseJSON && xhr.responseJSON.msg) {
                      errorMsg = xhr.responseJSON.msg;
                  }
                  toastr.error(errorMsg);
              }
          });
      });
  
      // Initial calculation
      calculateTotals();
  });
</script>
  
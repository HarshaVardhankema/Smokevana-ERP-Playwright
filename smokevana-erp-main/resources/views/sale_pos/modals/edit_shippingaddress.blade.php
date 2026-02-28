<div class="modal-dialog" style="width:70%" role="document" >
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="AddressModalLabel">Edit Address</h4>
            <div class="tw-flex tw-justify-end tw-gap-2" style="margin-top: -35px">
                <button type="button" class="btn btn-primary" id="saveAddress">Save changes</button>
                <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white no-print" style="margin-left: 25px;" data-dismiss="modal"
                    id='close_button'>@lang('messages.close')</button>
            </div>
        </div>
        <div class="modal-body">
            <form id="AddressForm" data-type="shipping">
                <div class="row">
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_company', 'Company Name' . ':') !!}
                        {!! Form::text('shipping_company', $sell->shipping_company, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_first_name', 'First name' . ':') !!}
                        {!! Form::text('shipping_first_name', $sell->shipping_first_name, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_last_name', 'Last Name' . ':') !!}
                        {!! Form::text('shipping_last_name', $sell->shipping_last_name, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                </div>
                <x-address-autocomplete addressInput="shipping_address1" cityInput="shipping_city"
                    stateInput="shipping_state" stateFormat="short_name" zipInput="shipping_zip"
                    countryInput="shipping_country" countryFormat="short_name" />
                <div class="row">
                    <div class="form-group col-md-12">
                        {!! Form::label('shipping_address1', 'Address 1' . ':') !!}
                        {!! Form::text('shipping_address1', $sell->shipping_address1, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                    <div class="form-group col-md-8">
                        {!! Form::label('shipping_address2', 'Address 2' . ':') !!}
                        {!! Form::text('shipping_address2', $sell->shipping_address2, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_city', 'City/Locality' . ':') !!}
                        {!! Form::text('shipping_city', $sell->shipping_city, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_state', 'State/Province' . ':') !!}
                        {!! Form::text('shipping_state', $sell->shipping_state, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_zip', 'Postal Code' . ':') !!}
                        {!! Form::text('shipping_zip', $sell->shipping_zip, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('shipping_country', 'Country' . ':') !!}
                        {!! Form::text('shipping_country', $sell->shipping_country, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#saveAddress').on('click', function () {
            let addressType = $('#AddressForm').data('type');
            if (validateAddressForm(addressType)) {
                let payload = {
                    id: {{ $sell->id }},
                    shipping_company: $('#shipping_company').val(),
                    shipping_first_name: $('#shipping_first_name').val(),
                    shipping_last_name: $('#shipping_last_name').val(),
                    shipping_address1: $('#shipping_address1').val(),
                    shipping_address2: $('#shipping_address2').val(),
                    shipping_city: $('#shipping_city').val(),
                    shipping_state: $('#shipping_state').val(),
                    shipping_zip: $('#shipping_zip').val(),
                    shipping_country: $('#shipping_country').val(),
                }
                $.ajax({
                    url: '/sells/pos/update_shipping_address_transaction',
                    type: 'POST',
                    data: payload,
                    success: function (response) {
                        swal("Success!", "Shipping address updated successfully", "success");
                        $('#AddressModal').modal('hide');
                        $('.shipping_company_name').text(payload.shipping_company);
                        $('.shipping_first_name').text(payload.shipping_first_name);
                        $('.shipping_last_name').text(payload.shipping_last_name);
                        $('.shipping_address1_name').text(payload.shipping_address1);
                        $('.shipping_address2_name').text(payload.shipping_address2);
                        $('.shipping_city_name').text(payload.shipping_city);
                        $('.shipping_state_name').text(payload.shipping_state);
                        console.log(payload.shipping_zip);
                        $('.shipping_zip_name').text(payload.shipping_zip);
                        $('.shipping_country_name').text(payload.shipping_country);
                    },
                    error: function (response) {
                        swal("Error!", "Something went wrong", "error");
                    }
                });
            }else{
                swal("Error!", "Please fill all the fields", "error");
                return false;
            }
        });
        function validateAddressForm(addressType) {
            if (addressType === 'shipping') {
                if ($('#shipping_first_name').val() === '' ){
                    $('#shipping_first_name').focus();
                    $('#shipping_first_name').css('border', '1px solid red');
                }
                if ($('#shipping_last_name').val() === '' ){
                    $('#shipping_last_name').focus();
                    $('#shipping_last_name').css('border', '1px solid red');
                }
                if ($('#shipping_company').val() === '' ){
                    $('#shipping_company').focus();
                    $('#shipping_company').css('border', '1px solid red');
                }
                if ($('#shipping_address1').val() === '' ){
                    $('#shipping_address1').focus();
                    $('#shipping_address1').css('border', '1px solid red');
                }
                if ($('#shipping_city').val() === '' ){
                    $('#shipping_city').focus();
                    $('#shipping_city').css('border', '1px solid red');
                }
                if ($('#shipping_state').val() === '' ){
                    $('#shipping_state').focus();
                    $('#shipping_state').css('border', '1px solid red');
                }
                if ($('#shipping_zip').val() === '' ){
                    $('#shipping_zip').focus();
                    $('#shipping_zip').css('border', '1px solid red');
                }
                if ($('#shipping_country').val() === '' ){
                    $('#shipping_country').focus();
                    $('#shipping_country').css('border', '1px solid red');
                }
                if ($('#shipping_company').val() === '' || $('#shipping_first_name').val() === '' || $('#shipping_last_name').val() === '' || $('#shipping_address1').val() === '' || $('#shipping_city').val() === '' || $('#shipping_state').val() === '' || $('#shipping_zip').val() === '' || $('#shipping_country').val() === '' ){
                    swal("Error!", "Please fill all the fields", "error");
                    return false;
                }
            }
            return true;
        }
    });
</script>
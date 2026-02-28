@forelse ($products as $product)
    @php
        $row_index = $loop->index + $index;
    @endphp
    <tr>
        <td>
            {{$product->product_name}}

            @if($product->variation_name != "DUMMY")
                <b>{{$product->variation_name}}</b>
            @endif
            <input type="hidden" name="products[{{$row_index}}][product_id]" value="{{$product->product_id}}">
            <input type="hidden" name="products[{{$row_index}}][variation_id]" value="{{$product->variation_id}}">
        </td>
        <td>
            <input type="number"
       class="form-control printQty"
       id="printQty"
       min="1"
       step="1"
       name="products[{{$row_index}}][quantity]"
       value="{{ isset($product->quantity) ? max(1, $product->quantity) : 1 }}"
       oninput="this.value = this.validity.valid ? this.value : '';"
       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
       required>

        </td>
        @if(request()->session()->get('business.enable_lot_number') == 1)
            <td>
                <input type="text" class="form-control" name="products[{{$row_index}}][lot_number]"
                    value="{{ isset($product->lot_number) ? $product->lot_number : '' }}">
            </td>
        @endif
        @if(request()->session()->get('business.enable_product_expiry') == 1)
            <td>
                <input type="text" class="form-control label-date-picker" name="products[{{$row_index}}][exp_date]"
                    value="{{ isset($product->exp_date) ? @format_date($product->exp_date) : '' }}">
            </td>
        @endif
        <td>
            <input type="text" class="form-control label-date-picker" name="products[{{$row_index}}][packing_date]"
                value="">
        </td>
        <td>
            {!! Form::select('products[' . $row_index . '][price_group_id]', $price_groups, null, ['class' => 'form-control', 'placeholder' => __('lang_v1.none')]) !!}
        </td>
        <td>
            <button type="button" class="btn btn-sm remove-row">
                <i class="fa fa-times text-danger pos_remove_row cursor-pointer" aria-hidden="true"></i>
            </button>
        </td>
    </tr>
@empty
@endforelse

<script>
$(document).on('click', '.remove-row', function() {
    $(this).closest('tr').remove();
});
$(document).ready(function() {
    $('input[name$="[quantity]"]').on('blur', function() {
        var value = parseInt($(this).val());


    });
});
 </script>
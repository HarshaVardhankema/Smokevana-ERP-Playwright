@can('tax_rate.update')
    <button data-href="{{ action('App\Http\Controllers\TaxRateController@edit', [$tax_charge->id]) }}" 
            class="btn btn-primary btn-xs edit_tax_rate_button">
        <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")
    </button>
@endcan

@can('tax_rate.delete')
    <button data-href="{{ action('App\Http\Controllers\TaxRateController@destroy', [$tax_charge->id]) }}" 
            class="btn btn-danger btn-xs delete_tax_rate_button">
        <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
    </button>
@endcan

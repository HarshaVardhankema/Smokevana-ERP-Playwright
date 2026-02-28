<?php
namespace App\View\Components;

use Illuminate\View\Component;

class AddressAutocomplete extends Component
{
    public $addressInput, $cityInput, $stateInput, $stateFormat, $zipInput, $countryInput, $countryFormat;

    public function __construct(
        $addressInput, $cityInput, $stateInput, $stateFormat, $zipInput, $countryInput, $countryFormat
    ) {
        $this->addressInput = $addressInput;
        $this->cityInput = $cityInput;
        $this->stateInput = $stateInput;
        $this->stateFormat = $stateFormat;
        $this->zipInput = $zipInput;
        $this->countryInput = $countryInput;
        $this->countryFormat = $countryFormat;
    }

    public function render()
    {
        return view('components.address-autocomplete');
    }
}

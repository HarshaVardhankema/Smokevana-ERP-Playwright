<div>
    <style>
        .pac-container {
            z-index: 999999;
        }
    </style>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_address') }}&libraries=places"></script>
    <script type="text/javascript">
        function initAutocomplete() {
            var input = document.getElementById('{{ $addressInput }}');
            var options = {
                types: ['address'],
                componentRestrictions: { country: 'us' }
            };
            var autocomplete = new google.maps.places.Autocomplete(input, options);

            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place.geometry) return;

                // Extract street number and route for address
                let streetNumber = getAddressComponent(place, 'street_number'); 
                let route = getAddressComponent(place, 'route');
                let streetAddress = `${streetNumber} ${route}`.trim();

                // Set extracted values in respective fields
                document.getElementById('{{ $addressInput }}').value = streetAddress;                                                                           
                document.getElementById('{{ $cityInput }}').value = getAddressComponent(place, 'locality');                                                     
                document.getElementById('{{ $stateInput }}').value = getAddressComponent(place, 'administrative_area_level_1', '{{ $stateFormat }}');           
                document.getElementById('{{ $zipInput }}').value = getAddressComponent(place, 'postal_code');                                                   
                document.getElementById('{{ $countryInput }}').value = getAddressComponent(place, 'country', '{{ $countryFormat }}');                           
                
                // CAPTURE LATITUDE & LONGITUDE
                if (place.geometry && place.geometry.location) {
                    var lat = place.geometry.location.lat();
                    var lng = place.geometry.location.lng();
                    
                    console.log('🌍 Google returned coordinates:', lat, lng);
                    
                    // Store globally for form submission
                    window.selectedPlaceCoordinates = { lat: lat, lng: lng };
                    
                    // Try to set latitude field if it exists
                    var latField = document.getElementById('latitude');
                    if (latField) {
                        latField.value = lat;
                        console.log('✅ Latitude set to field:', lat);
                    } else {
                        console.log('ℹ️ No latitude field found');
                    }
                    
                    // Try to set longitude field if it exists
                    var lngField = document.getElementById('longitude');
                    if (lngField) {
                        lngField.value = lng;
                        console.log('✅ Longitude set to field:', lng);
                    } else {
                        console.log('ℹ️ No longitude field found');
                    }
                    
                    console.log('💾 Coordinates stored in window.selectedPlaceCoordinates');
                }
            });
        }

        function getAddressComponent(place, type, format = 'long_name') {
            for (var i = 0; i < place.address_components.length; i++) {
                for (var j = 0; j < place.address_components[i].types.length; j++) {
                    if (place.address_components[i].types[j] === type) {
                        return place.address_components[i][format];
                    }
                }
            }
            return '';
        }


        document.addEventListener("DOMContentLoaded", initAutocomplete);
        $({{ $addressInput }}).on('focus', function () {
            initAutocomplete();
        });
    </script>


</div>
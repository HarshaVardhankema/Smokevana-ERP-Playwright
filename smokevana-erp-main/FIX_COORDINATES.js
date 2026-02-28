// ====================================
// SIMPLE FIX - Add to your lead form
// ====================================

// Add these hidden fields to your HTML form:
/*
<input type="hidden" name="latitude" id="latitude" value="">
<input type="hidden" name="longitude" id="longitude" value="">
*/

// When Google Places autocomplete is triggered, add this listener:

autocomplete.addListener('place_changed', function() {
    const place = autocomplete.getPlace();
    
    // EXTRACT COORDINATES FROM GOOGLE RESPONSE
    if (place.geometry && place.geometry.location) {
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();
        
        // SET HIDDEN FIELDS
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        console.log('Coordinates captured:', lat, lng);
    }
    
    // Parse address components (already working)
    if (place.address_components) {
        for (const component of place.address_components) {
            const types = component.types;
            
            if (types.includes('street_number')) {
                // street number logic
            }
            if (types.includes('route')) {
                // street name logic
            }
            if (types.includes('locality')) {
                document.getElementById('city').value = component.long_name;
            }
            if (types.includes('administrative_area_level_1')) {
                document.getElementById('state').value = component.short_name;
            }
            if (types.includes('country')) {
                document.getElementById('country').value = component.short_name;
            }
            if (types.includes('postal_code')) {
                document.getElementById('zip_code').value = component.long_name;
            }
        }
    }
});


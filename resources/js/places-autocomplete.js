function attachAutocomplete(inputId, latId, lngId) {
    const input = document.getElementById(inputId);
    const latInput = document.getElementById(latId);
    const lngInput = document.getElementById(lngId);

    if (!input || !latInput || !lngInput) {
        return;
    }

    const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['(cities)'],
        fields: ['formatted_address', 'geometry', 'name'],
    });

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();

        if (place.formatted_address) {
            input.value = place.formatted_address;
        }

        if (place.geometry && place.geometry.location) {
            latInput.value = place.geometry.location.lat();
            lngInput.value = place.geometry.location.lng();
        }
    });
}

function initPlacesAutocomplete() {
    if (!window.google || !window.google.maps || !window.google.maps.places) {
        return;
    }

    attachAutocomplete('origin', 'origin_lat', 'origin_lng');
    attachAutocomplete('destination', 'destination_lat', 'destination_lng');
}

// The Google Maps script (loaded separately, async) calls this once ready.
window.initPlacesAutocomplete = initPlacesAutocomplete;

// In case the Maps script finished loading before this module ran.
initPlacesAutocomplete();

"use strict";

const _KEY = 'PasteYourGoogleMapsApiKeyHere';

import {Loader} from "@googlemaps/js-api-loader";
import mapTheme from "./map-theme";

function initMap() {
    const loader = new Loader({
        apiKey: _KEY,
        version: "weekly",

    });
    const mapContainer = document.querySelector('#map');
    const defaultMarker = { lat: 40.74881584652125, lng: -73.98561648634562 };

    loader.load().then(() => {
        const map = new google.maps.Map(mapContainer, {
            center: defaultMarker,
            zoom: 16,
            styles: [...mapTheme],
            disableDefaultUI: true,
        });
        const marker = new google.maps.Marker({
            position: defaultMarker,
            map: map,
            icon: './svg/marker.svg'
        });
    });
}

export default initMap;
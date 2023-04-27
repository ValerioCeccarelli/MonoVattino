let map;
let scooters;
let cdr;

async function initMap(_longitude, _latitude) {
    // The location of Uluru
    const position = {
        lat: _latitude,
        lng: _longitude
    };
    // Request needed libraries.
    //@ts-ignore
    const {
        Map
    } = await google.maps.importLibrary("maps");

    // The map, centered at Uluru
    map = new Map(document.getElementById("map"), {
        zoom: 8,
        center: position,
        mapId: "DEMO_MAP_ID",
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false,
    });

    map.addListener("zoom_changed", () => {
        current_zoom = map.getZoom();
    });

    add_markers();
}

async function add_markers() {
    const {
        AdvancedMarkerView
    } = await google.maps.importLibrary("marker");

    scooters.forEach(scooter => {
        // console.log(scooter.latitude);
        const marker = new AdvancedMarkerView({
            position: {
                lat: parseFloat(scooter.latitude),
                lng: parseFloat(scooter.longitude)
            },
            map: map,
            title: scooter.company_name,
        });
        marker.addEventListener("click", () => {
            console.log(scooter.id);
        });
    });

    add_me();
}

async function add_me() {
    const {
        AdvancedMarkerView,
        PinView
    } = await google.maps.importLibrary("marker");
    const pin = new PinView({
        background: '#FBBC04',
    });
    const marker = new AdvancedMarkerView({
        position: {
            lat: crd.latitude,
            lng: crd.longitude
        },
        map: map,
        title: "Hello World!",
        content: pin.element,
    });

    ciao();
}

function get_scooter(_longitude, _latitude) {
    url = 'http://localhost/scooter/scooter.php';
    const data = {
        longitude: _longitude,
        latitude: _latitude,
        radius: 3000000
    };
    // async: false, to wait for the response
    $.ajax({
        url: url,
        type: 'GET',
        data: data,
        success: function(data) {
            scooters = data;
            console.log(scooters);
            initMap(crd.longitude, crd.latitude);
        },
        error: function(data) {
            console.log(data);
        }
    });
}

$(document).ready(() => {
    const options = {
        enableHighAccuracy: true,
        timeout: 60000,
        maximumAge: 0,
    };

    success = (pos) => {
        crd = pos.coords;
        get_scooter(crd.longitude, crd.latitude);
    };

    error = (err) => {
        console.warn(`ERROR(${err.code}): ${err.message}`);
    };

    navigator.geolocation.getCurrentPosition(success, error, options);
});
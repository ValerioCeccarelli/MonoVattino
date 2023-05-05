(g => {
    var h, a, k, p = "The Google Maps JavaScript API",
        c = "google",
        l = "importLibrary",
        q = "__ib__",
        m = document,
        b = window;
    b = b[c] || (b[c] = {});
    var d = b.maps || (b.maps = {}),
        r = new Set,
        e = new URLSearchParams,
        u = () => h || (h = new Promise(async (f, n) => {
            await (a = m.createElement("script"));
            e.set("libraries", [...r] + "");
            for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
            e.set("callback", c + ".maps." + q);
            a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
            d[q] = f;
            a.onerror = () => h = n(Error(p + " could not load."));
            a.nonce = m.querySelector("script[nonce]")?.nonce || "";
            m.head.append(a)
        }));
    d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() =>
        d[l](f, ...n))
})
    ({
        key: "AIzaSyBUA0q_QDe0sEHpbyLgkibMyeU0iyOAv3k",
        v: "beta"
    });

let map = null;

function isCurrentPositionAvailable() {
    if (navigator.geolocation) {
        return true;
    } else {
        return false;
    }
}

async function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
    });
}

function onZoomChanged(zoom) {
    console.log("zoom changed: " + zoom);
}

async function initMap(_longitude, _latitude) {
    const position = {
        lat: _latitude,
        lng: _longitude
    };

    const {
        Map
    } = await google.maps.importLibrary("maps");

    // The map, centered at Uluru
    map = new Map(document.getElementById("map"), {
        zoom: 14,
        center: position,
        mapId: "DEMO_MAP_ID",
        streetViewControl: false,
        mapTypeControl: false,
        fullscreenControl: false,
    });

    console.log("map initialized");

    map.addListener("zoom_changed", () => {
        let current_zoom = map.getZoom();
        onZoomChanged(current_zoom);
    });
}

async function getScooters(_longitude, _latitude, _radius) {
    return new Promise((resolve, reject) => {
        url = 'http://localhost/scooter/scooter.php';
        const data = {
            longitude: _longitude,
            latitude: _latitude,
            radius: _radius
        };

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            async: false,
            success: resolve,
            error: reject
        });
    });
}

async function renderMe(_latitude, _longitude) {
    const {
        AdvancedMarkerView,
        PinView
    } = await google.maps.importLibrary("marker");
    const pin = new PinView({
        background: '#FBBC04',
    });
    const meMarker = new AdvancedMarkerView({
        position: {
            lat: _latitude,
            lng: _longitude
        },
        map: map,
        title: "You!",
        content: pin.element,
    });
}

function moveMapCenter(_latitude, _longitude) {
    const pos = {
        lat: _latitude,
        lng: _longitude
    };
    map.setCenter(pos);
}

scooters_markers = [];
async function renderScooters(scooters) {
    const {
        AdvancedMarkerView,
        PinView
    } = await google.maps.importLibrary("marker");

    scooters_markers.forEach(scooterMarker => {
        scooterMarker.setMap(null);
    });

    scooters_markers = [];

    scooters.forEach(scooter => {
        const pin = new PinView({
            background: '#' + scooter.company_color,
        });
        const scooterMarker = new AdvancedMarkerView({
            position: {
                lat: parseFloat(scooter.latitude),
                lng: parseFloat(scooter.longitude)
            },
            map: map,
            title: scooter.name,
            content: pin.element,
        });
        scooters_markers.push(scooterMarker);

        scooterMarker.addListener("click", () => {
            console.log(scooter);

            $('#scooter_id').text(scooter.id);
            $('#scooter_company').text(scooter.company_name);
            $('#scooter_battery').text(scooter.battery_level);
            $('#scooter_lat').text(scooter.latitude);
            $('#scooter_lng').text(scooter.longitude);

            $('#info_scooter').show();

            $('#btn_reserve').click(async () => {
                try {
                    await reserveScooter(scooter);
                    $('#info_scooter').hide();
                    alert("Scooter reserved!");
                } catch (error) {
                    console.error(error);
                    alert("Error while reserving scooter!");
                }
            });

            // update the map by removing the scooter withouth refreshing the page
            scooters.remove(scooter);
            scooterMarker.setMap(null);
        });
    });
}

async function reserveScooter(scooter) {
    return new Promise((resolve, reject) => {
        const data = {
            scooter_id: scooter.id,
            action: 'reserve'
        };
        $.ajax({
            url: 'http://localhost/scooter/scooter.php',
            type: 'POST',
            data: data,
            async: false,
            success: resolve,
            error: reject
        });
    });
}

async function changeMarkerIcon() {
    const {
        PinView
    } = await google.maps.importLibrary("marker");

    scooters_markers.forEach(scooterMarker => {
        const pin = new PinView({
            background: '#999999',
        });
        scooterMarker.setContent(pin.element);
    });
}

async function onDocumentReady() {
    // position of Colosseo
    // 41.889819, 12.492234
    let longitude = 41.889819;
    let latitude = 12.492234;
    let radius = 3000000;

    let map_promise = initMap(latitude, longitude);
    let scooters_promise = getScooters(latitude, longitude, radius);

    if (isCurrentPositionAvailable()) {
        position_promise = getCurrentPosition();
    }
    else {
        console.error("Geolocation is not supported by this browser.");
        alert("Geolocation is not supported by this browser.\nWe can not show you the nearest scooters.\nPlease, use another browser.");
    }

    // if map and scooters are ready, render scooters
    await map_promise;
    let scooters = await scooters_promise;

    await renderScooters(scooters);

    // if position is available, wait for it
    // or show error message

    if (!isCurrentPositionAvailable()) {
        return;
    }

    let position = null;
    try {
        position = await position_promise;
    }
    catch (error) {
        if (error.code == error.PERMISSION_DENIED) {
            console.error("User denied the request for Geolocation.");
            alert("This website needs your position to show you the nearest scooters.\nPlease, allow the website to use your position and reload the page.");
        }
        else {
            console.error("An unknown error occurred.");
            alert("An unknown error occurred.\nPlease, reload the page.");
        }
        return;
    }

    let new_latitude = position.coords.latitude;
    let new_longitude = position.coords.longitude;

    scooters_promise = getScooters(latitude, longitude, radius);

    moveMapCenter(new_latitude, new_longitude);
    renderMe(new_latitude, new_longitude);

    scooters = await scooters_promise;
    await renderScooters(scooters);
}

$(document).ready(onDocumentReady);

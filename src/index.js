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

async function ajaxGetAsync(url, data) {
    return new Promise((resolve, reject) => {
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

async function ajaxPostAsync(url, data) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            async: false,
            success: resolve,
            error: reject
        });
    });
}

async function getScooters(latitude, longitude, radius) {
    url = 'http://localhost/scooter/get_scooters.php';
    const data = {
        longitude: longitude,
        latitude: latitude,
        radius: radius
    };

    const scooters = await ajaxGetAsync(url, data);
    return scooters;
}

async function reserveScooter(scooter_id) {
    url = 'http://localhost/scooter/reserve_scooter.php';
    const data = {
        scooter_id: scooter_id
    };

    await ajaxPostAsync(url, data);
}

async function releaseScooter(scooter_id, longitude, latitude) {
    url = 'http://localhost/scooter/release_scooter.php';
    const data = {
        scooter_id: scooter_id,
        longitude: longitude,
        latitude: latitude
    };

    await ajaxPostAsync(url, data);
}

async function getCurrentPosition() {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
    });
}

function isCurrentPositionAvailable() {
    if (navigator.geolocation) {
        return true;
    } else {
        return false;
    }
}

function onZoomChanged() {
    let zoom = map.getZoom();
    // console.log("zoom changed: " + zoom);
}

async function initMap(latitude, longitude) {
    let position = {
        lat: latitude,
        lng: longitude
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

    map.addListener("zoom_changed", onZoomChanged);
}

function moveMapCenter(latitude, longitude) {
    const pos = {
        lat: latitude,
        lng: longitude
    };
    map.setCenter(pos);
}

async function onScooterReserveClick(scooter, scooter_marker) {
    try {
        await reserveScooter(scooter.id);
        $('#info_scooter').hide();
        alert("Scooter reserved!");

        $('#btn_reserve').hide();
        $('#btn_release').show();

        const {
            PinView
        } = await google.maps.importLibrary("marker");
        const pin = new PinView({
            background: '#040404',
        });

        scooter_marker.content = pin.element;
        scooter.is_my_scooter = true;
    } catch (error) {

        if (error.status == 401) {
            console.error("User not logged in.");
            alert("Error while reserving scooter!\nYou have to login first.\nPlease, login and try again.");
            return;
        }

        console.error(error);
        alert("Error while reserving scooter!<<");
    }
}

async function onScooterReleaseClick(scooter, scooter_marker) {

    if (my_position == null) {
        alert("Error while releasing scooter!\nWe can not get your position.\nPlease, reload the page and allow the website to use your position.");
        return;
    }

    try {
        let payment = await releaseScooter(scooter.id, my_position.longitude, my_position.latitude);
        $('#info_scooter').hide();
        // TODO: controllare la conversione in float (tipo arrotondare il valore)
        alert("Scooter released!\nYou paid: " + payment + "€");

        const {
            PinView
        } = await google.maps.importLibrary("marker");
        const pin = new PinView({
            background: '#' + scooter.company_color,
        });

        scooter_marker.content = pin.element;
        scooter.is_my_scooter = false;

        //TODO: questa cosa non funziona, in particolare se si rilascia lo scooter compare sia un marker su dove sei sia rimane il marker vecchio...
        console.log(scooter_marker.position);
        scooter_marker.position = {
            lat: my_position.latitude,
            lng: my_position.longitude
        }
        console.log(scooter_marker.position);
    } catch (error) {
        console.error(error);
        alert("Error while releasing scooter!pp");
    }
}

function onScooterClick(scooter, scooter_marker) {

    $('#scooter_id').text(scooter.id);
    $('#scooter_company').text(scooter.company_name);
    $('#scooter_battery').text(scooter.battery_level);
    $('#scooter_lat').text(scooter.latitude);
    $('#scooter_lng').text(scooter.longitude);

    $('#info_scooter').show();

    if (scooter.is_my_scooter) {
        $('#btn_reserve').hide();
        $('#btn_release').show();
        $('#btn_release').click(() => {
            onScooterReleaseClick(scooter, scooter_marker);
        });
    }
    else {
        $('#btn_release').hide();
        $('#btn_reserve').show();
        $('#btn_reserve').click(() => {
            onScooterReserveClick(scooter, scooter_marker);
        });
    }
}

async function renderScooter(scooter) {
    const {
        AdvancedMarkerView,
        PinView
    } = await google.maps.importLibrary("marker");

    let color = '#040404';
    if (!scooter.is_my_scooter) {
        color = '#' + scooter.company_color;
    }

    const pin = new PinView({
        background: color,
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

    scooterMarker.addListener("click", () => {
        onScooterClick(scooter, scooterMarker);
    });

    return scooterMarker;
}

async function renderMe(latitude, longitude) {
    const {
        AdvancedMarkerView,
        PinView
    } = await google.maps.importLibrary("marker");
    const pin = new PinView({
        background: '#FFFFFF',
    });
    const meMarker = new AdvancedMarkerView({
        position: {
            lat: latitude,
            lng: longitude
        },
        map: map,
        title: "You!",
        content: pin.element,
    });
}

async function onDocumentReady() {
    // center the map on Colosseo
    const colosseo = {
        longitude: 12.492234,
        latitude: 41.889819,
    }
    let radius = 30000000

    const initial_position = colosseo;

    let map_promise = initMap(initial_position.latitude, initial_position.longitude);
    let scooters_promise = getScooters(initial_position.latitude, initial_position.longitude, radius);

    let position_promise = null;
    if (isCurrentPositionAvailable()) {
        position_promise = getCurrentPosition();
    }
    else {
        console.error("Geolocation is not supported by this browser.");
        alert("Geolocation is not supported by this browser.\nWe can not show you the nearest scooters.\nPlease, use another browser.");
    }

    await map_promise;
    let scooters_response = await scooters_promise;
    let scooters = scooters_response.scooters;
    let my_scooters = scooters_response.reserved_scooters;

    scooters.forEach(scooter => {
        renderScooter(scooter);
    });

    my_scooters.forEach(scooter => {
        renderScooter(scooter);
    });

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

    my_position = position.coords;
    let new_latitude = my_position.latitude;
    let new_longitude = my_position.longitude;

    scooters_promise = getScooters(new_latitude, new_longitude, radius);

    moveMapCenter(new_latitude, new_longitude);
    renderMe(new_latitude, new_longitude);

    scooters_response = await scooters_promise;
    scooters = scooters_response.scooters;
    my_scooters = scooters_response.reserved_scooters;

    scooters.forEach(scooter => {
        renderScooter(scooter);
    });

    my_scooters.forEach(scooter => {
        renderScooter(scooter);
    });
}

let map = null;
let my_position = null;
$(document).ready(onDocumentReady);
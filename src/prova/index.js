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

    return await ajaxPostAsync(url, data);
}

async function releaseScooter(scooter_id, longitude, latitude) {
    url = 'http://localhost/scooter/release_scooter.php';
    const data = {
        scooter_id: scooter_id,
        longitude: longitude,
        latitude: latitude
    };

    return await ajaxPostAsync(url, data);
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
}

function showErrorWithModal(message) {
    $('#error_modal_body').html(message);
    $('#error_modal').modal('show');
}

async function initMap(latitude, longitude) {
    let position = {
        lat: latitude,
        lng: longitude
    };

    const {
        Map
    } = await google.maps.importLibrary("maps");

    map = new Map(document.getElementById("map"), {
        zoom: 14,
        center: position,
        // mapId: "18db44928f96d960", // default
        //mapId: "a4960208d9b76361", // dark
        mapId: "7bf73a088c3484e4", // night
        // mapId: "4d28faf75cbe2224", // atlas
        // mapId: "a85cc9c21291463", // classic
        // mapId: "f12bf4b63529e007", // grey
        // mapId: "b00ca340d0b7980f", // light
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

async function onScooterReserveClick(scooter) {
    try {
        let scooter_marker = scooter_markers[scooter.id];

        await reserveScooter(scooter.id);
        $('#info_scooter').hide();

        $('#success_modal_title').text("Scooter reserved!");
        $('#success_modal_body').html("You have successfully reserved the scooter.<br>Now you can go to the scooter and release it.");
        $('#success_modal').modal('show');

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

        $('#offcanvasBottom').offcanvas('hide');
    } catch (error) {

        if (error.status == 401) {
            console.error("User not logged in.");
            showErrorWithModal("Error while reserving scooter!<br>You have to login first.<br>Please, login and try again.");
            return;
        }
        if (error.status == 403) {
            console.error("User not authorized.");
            text = error.responseText;
            // replace \n with <br>
            text = text.replace(/(?:\r\n|\r|\n)/g, '<br>');
            showErrorWithModal(text);
            return;
        }

        console.error(error);
        showErrorWithModal("Error while reserving scooter!<br>Please reload the page and try again.");
    }
}

async function onScooterReleaseClick(scooter) {

    if (my_position == null) {
        showErrorWithModal("Error while releasing scooter!<br>We can not get your position.<br>Please, reload the page and allow the website to use your position.");
        return;
    }

    try {
        let scooter_marker = scooter_markers[scooter.id];

        let payment = await releaseScooter(scooter.id, my_position.longitude, my_position.latitude);
        $('#info_scooter').hide();

        $('#success_modal_title').text("Scooter released!");
        $('#success_modal_mody').text("You paid: " + payment.total_cost + "€");
        $('#success_modal').modal('show');

        const {
            PinView
        } = await google.maps.importLibrary("marker");
        const pin = new PinView({
            background: '#' + scooter.company_color,
        });

        scooter_marker.content = pin.element;
        scooter.is_my_scooter = false;

        scooter_marker.position = {
            lat: my_position.latitude,
            lng: my_position.longitude
        };

        scooter.latitude = my_position.latitude;
        scooter.longitude = my_position.longitude;

        $('#offcanvasBottom').offcanvas('hide');
    } catch (error) {
        console.error(error);
        showErrorWithModal("Error while releasing the scooter!<br>Please reload the page and try again.");
    }
}

function onScooterClick(scooter) {

    $('#scooter_battery').text(scooter.battery_level);
    $('#scooter_company').text(scooter.company_name);

    $('#scooter_fixed_cost').text(scooter.fixed_cost);
    $('#scooter_cost_per_minute').text(scooter.cost_per_minute);

    $('#scooter_latitude').text(scooter.latitude);
    $('#scooter_longitude').text(scooter.longitude);

    if (scooter.is_my_scooter) {
        $('#offcanvas_button').text("Release");

        $('#offcanvas_button').unbind('click').click(() => {
            onScooterReleaseClick(scooter);
        });
    }
    else {
        $('#offcanvas_button').text("Reserve");

        $('#offcanvas_button').unbind('click').click(() => {
            onScooterReserveClick(scooter);
        });
    }

    $('#offcanvasBottom').offcanvas('show');
}

async function renderScooter(scooter) {
    if (scooter_markers[scooter.id] != null) {
        return;
    }

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
        onScooterClick(scooter);
    });

    scooter_markers[scooter.id] = scooterMarker;
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
    try {
        // center the map on Colosseo
        const colosseo = {
            longitude: 12.492234,
            latitude: 41.889819,
        }
        let radius = 3000000000000;

        const initial_position = colosseo;

        let map_promise = initMap(initial_position.latitude, initial_position.longitude);
        let scooters_promise = getScooters(initial_position.latitude, initial_position.longitude, radius);

        let position_promise = null;
        if (isCurrentPositionAvailable()) {
            position_promise = getCurrentPosition();
        }
        else {
            console.error("Geolocation is not supported by this browser.");
            showErrorWithModal("Geolocation is not supported by this browser.<br>We can not show you the nearest scooters.<br>Please, use another browser.");
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
            if (error.PERMISSION_DENIED && error.code === error.PERMISSION_DENIED) {
                console.error("User denied the request for Geolocation.");
                showErrorWithModal("This website needs your position to show you the nearest scooters.<br>Please, allow the website to use your position and reload the page.");
            }
            else {
                console.error("An unknown error occurred.");
                showErrorWithModal("An unknown error occurred.<br>Please, reload the page.");
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
    } catch (error) {
        console.error(error);
        showErrorWithModal("An unknown error occurred.<br>Please, reload the page.");
    }
}



let map = null;
let my_position = null;
let scooter_markers = {};
$(document).ready(onDocumentReady);
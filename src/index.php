<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
    #map {
        height: 70%;
        width: 50%;
        background-color: red;
    }

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    </style>
</head>

<body>
    <?php 
    require_once('lib/jwt.php');
    if (!isset($_COOKIE['jwt']) || !jwt_decode($_COOKIE['jwt'])) {
        echo 'Hello guest<br>yuo need to <a href="account/login.php">login</a> or <a href="account/register.php">register</a>!';
    }else {
        echo 'Hello user<br>you can <a href="account/logout.php">logout</a> or <a href="account/profile.php">view your profile</a>!';
    }
    ?>

    <div id="map"></div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
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
    </script>
    <script>
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
            zoom: 4,
            center: position,
            mapId: "DEMO_MAP_ID",
            streetViewControl: false,
            mapTypeControl: false,
            fullscreenControl: false,
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
            marker.addListener("click", () => {
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
    }

    function get_scooter(_longitude, _latitude) {
        url = 'http://localhost/scooter/scooter.php';
        const data = {
            longitude: _longitude,
            latitude: _latitude,
            radius: 3000000
        };

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
            timeout: 60,
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
    </script>
</body>

</html>
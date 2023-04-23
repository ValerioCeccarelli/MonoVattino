<?php

function process_get_request() {
    if(empty($_POST['longitude']) || empty($_POST['latitude']) || empty($_POST['radius'])) {
        http_response_code(400);
        echo "400 Bad Request";
        exit;
    }

    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];
    $radius = $_POST['radius'];

    require_once('../lib/database.php');
    $conn = connect_to_database();
    if (!$conn) {
        http_response_code(500);
        echo "500 Internal Server Error";
        exit;
    }

    require_once('../lib/scooter.php');

    try {
        $scooters = get_scooters($conn, $longitude, $latitude, $radius);

        header('Content-Type: application/json');
        echo json_encode($scooters);
    } catch (Exception $e) {
        http_response_code(500);
        echo "500 Internal Server Error";
        exit;
    }
}

function process_post_request() {

    exit;
}

if ($_COOKIE['jwt']) {
    require_once('../lib/jwt.php');
    $jwt = $_COOKIE['jwt'];
    $jwt_payload = jwt_decode($jwt);
    if (!$jwt_payload) {
        http_response_code(401);
        echo "401 Unauthorized: Invalid jwt";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        process_get_request();
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        process_post_request();
    }
    else {
        http_response_code(405);
        echo "405 Method Not Allowed";
        exit;
    }
}
else {
    http_response_code(401);
    echo "401 Unauthorized: Missing jwt";
    exit;
}

?>
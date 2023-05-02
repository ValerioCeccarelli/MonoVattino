<?php

require_once('../lib/http_exceptions/bad_request.php');
require_once('../lib/http_exceptions/method_not_allowed.php');
require_once('../lib/database.php');

// to test: http://localhost/scooter/scooter.php?longitude=12&latitude=42&radius=30000
function process_get_request() {
    if(empty($_GET['longitude']) || empty($_GET['latitude']) || empty($_GET['radius'])) {
        throw new BadRequestException("Missing parameters");
    }

    $longitude = $_GET['longitude'];
    $latitude = $_GET['latitude'];
    $radius = $_GET['radius'];

    $conn = connect_to_database();

    require_once('../lib/scooter.php');

    $scooters = get_scooters($conn, $longitude, $latitude, $radius);

    header('Content-Type: application/json');
    echo json_encode($scooters);
}

function process_post_request() {
    require_once('../lib/jwt.php');

    $jwt_payload = validate_jwt();

    $scooter_id = $_POST['scooter_id'];
    $action = $_POST['action'];

    $conn = connect_to_database();

    require_once('../lib/scooter.php');

    if ($action === 'reserve') {
        reserve_scooter($conn, $scooter_id, $jwt_payload->email);
    }
    elseif ($action === 'end') {
        if(empty($_POST['longitude']) || empty($_POST['latitude'])) {
            throw new BadRequestException("Missing parameters");
        }

        $longitude = $_POST['longitude'];
        $latitude = $_POST['latitude'];

        $travel_time = get_travel_time($conn, $scooter_id);
        move_to_position($conn, $scooter_id, $longitude, $latitude);
        free_scoter($conn, $scooter_id);
        
        header('Content-Type: application/json');
        echo json_encode($travel_time);
    }
    else {
        throw new BadRequestException("Invalid action");
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        process_get_request();
    }
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        process_post_request();
    }
    else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} catch (BadRequestException $e) {
    http_response_code(400);
    echo "400 Bad Request";
    exit;
} catch (MethodNotAllowedException $e) {
    http_response_code(405);
    echo "405 Method Not Allowed";
    exit;
} catch (ScooterAlreayReservedException $e) {
    http_response_code(409);
    echo "409 Conflict";
    exit;
} catch (InvalidJWTException $e) {
    http_response_code(401);
    echo "401 Unauthorized";
    exit;
} catch (Exception $e) {
    error_log("ERROR: scooter.php: " . $e->getMessage());
    http_response_code(500);
    echo "500 Internal Server Error";
    exit;
}

?>
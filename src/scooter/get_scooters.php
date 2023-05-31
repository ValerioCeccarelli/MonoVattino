<?php

require_once('../lib/http_exceptions/bad_request.php');
require_once('../lib/http_exceptions/method_not_allowed.php');
require_once('../lib/database.php');
require_once('../lib/scooters/scooter.php');
// require_once('../lib/jwt.php');

session_start();

function process_get_request() {
    if(empty($_GET['longitude']) || empty($_GET['latitude']) || empty($_GET['radius'])) {
        throw new BadRequestException("Missing parameters");
    }

    $longitude = $_GET['longitude'];
    $latitude = $_GET['latitude'];
    $radius = $_GET['radius'];

    $max_radius = 4000;
    if ($radius > $max_radius) {
        $radius = $max_radius;
    }

    $conn = connect_to_database();

    $scooters = get_scooters($conn, $longitude, $latitude, $radius);

    // $jwt = $_COOKIE['jwt'];
    // $reserved_scooters = array();
    // if (!empty($jwt)) {
    //     $jwt_payload = jwt_decode($jwt);
    //     if ($jwt_payload) {
    //         $reserved_scooters = get_my_scooters($conn, $jwt_payload->email);
    //     }
    // }

    $reserved_scooters = array();
    if (!empty($_SESSION['user_email'])) {
        $reserved_scooters = get_my_scooters($conn, $_SESSION['user_email']);
    }
    
    header('Content-Type: application/json');
    echo json_encode(array('scooters' => $scooters, 'reserved_scooters' => $reserved_scooters));
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        process_get_request();
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
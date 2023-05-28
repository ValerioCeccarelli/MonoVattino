<?php

require_once('../lib/http_exceptions/bad_request.php');
require_once('../lib/http_exceptions/method_not_allowed.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');
require_once('../lib/scooter.php');
require_once('../lib/trips.php');

function process_post_request() {

    $jwt_payload = validate_jwt();

    $scooter_id = $_POST['scooter_id'];
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];

    if (empty($scooter_id) || empty($longitude) || empty($latitude)) {
        throw new BadRequestException("Missing parameters");
    }

    $conn = connect_to_database();

    $travel_time = get_travel_time($conn, $scooter_id);
    move_to_position($conn, $scooter_id, $longitude, $latitude);
    free_scoter($conn, $scooter_id);

    create_trip($conn, $scooter_id, $jwt_payload->email, $travel_time);

    $costs = get_scooter_costs($conn, $scooter_id);

    $total_cost = $costs->fixed_cost + $costs->cost_per_minute * $travel_time / 60;

    $total_cost = round($total_cost, 2);
    
    header('Content-Type: application/json');
    echo json_encode(array('total_cost' => $total_cost));
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
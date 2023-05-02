<?php

// to test: http://localhost/scooter/scooter.php?longitude=1&latitude=1&radius=0.5
function process_get_request() {
    if(empty($_GET['longitude']) || empty($_GET['latitude']) || empty($_GET['radius'])) {
        http_response_code(400);
        echo "400 Bad Request";
        exit;
    }

    $longitude = $_GET['longitude'];
    $latitude = $_GET['latitude'];
    $radius = $_GET['radius'];

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
    require_once('../lib/jwt.php');

    try {
        $jwt_payload = validate_jwt();

        $scooter_id = $_POST['scooter_id'];
        $action = $_POST['action'];

        require_once('../lib/database.php');
        $conn = connect_to_database();
        if (!$conn) {
            http_response_code(500);
            echo "500 Internal Server Error";
            exit;
        }

        require_once('../lib/scooter.php');

        if ($action === 'reserve') {
            reserve_scooter($conn, $scooter_id, $jwt_payload->email);
        }
        elseif ($action === 'end') {
            if(empty($_POST['longitude']) || empty($_POST['latitude'])) {
                http_response_code(400);
                echo "400 Bad Request p2";
                exit;
            }
    
            $longitude = $_POST['longitude'];
            $latitude = $_POST['latitude'];

            $travel_time = get_travel_time($conn, $scooter_id);
            move_to_position($conn, $scooter_id, $longitude, $latitude);
            free_scoter($conn, $scooter_id);
            
            header('Content-Type: application/json');
            echo json_encode($travel_time);
            exit;
        }
        else {
            http_response_code(400);
            echo "400 Bad Request";
            exit;
        }
    } catch (ScooterAlreayReservedException $e) {
        http_response_code(409);
        echo "409 Conflict";
        exit;
    } catch (Exception $e) {
        error_log("ERROR: scooter.php: " . $e->getMessage());
        http_response_code(500);
        echo "500 Internal Server Error";
        exit;
    }
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

?>
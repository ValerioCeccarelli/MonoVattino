<?php

require_once('../lib/http_exceptions/bad_request.php');
require_once('../lib/http_exceptions/method_not_allowed.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');
require_once('../lib/scooters/scooter.php');
require_once('../lib/accounts/user.php');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jwt_payload = validate_jwt();
        $email = $jwt_payload->email;

        error_log("ciaoooooooo " . $email . "ciaooooooooo");

        $scooter_id = $_POST['scooter_id'];

        if (empty($scooter_id)) {
            throw new BadRequestException("Missing parameters");
        }

        $conn = connect_to_database();

        error_log("00000000");

        check_if_user_can_reserve($conn, $email);

        reserve_scooter($conn, $scooter_id, $email);
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
} catch (UserCanNotReserveException $e) {
    http_response_code(403);
    echo $e->getMessage();
    exit;
} catch (NoUserFoundException $e) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
} catch (Exception $e) {
    error_log("ERROR: scooter.php: " . $e->getMessage());
    http_response_code(500);
    echo "500 Internal Server Error";
    exit;
}

?>
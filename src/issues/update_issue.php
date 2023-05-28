<?php

require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/scooters/issues.php');
require_once('../lib/http_exceptions/bad_request.php');

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $issue_id = $_GET['id'];

        if(!isset($issue_id)) {
            throw new BadRequestException("Missing issue id");
        }

        $conn = connect_to_database();
        update_issue_status_as_accepted($conn, $issue_id);

        header("Location: /issues/show_issue.php");
    }
} catch (BadRequestException $e) {
    http_response_code(400);
    echo "400 Bad Request";
    exit;
} catch (InvalidJWTException $e) {
    header("Location: /account/login.php");
    exit;
} catch (MethodNotAllowedException $e) {
    http_response_code(405);
    echo "405 Method Not Allowed";
    exit;
} catch (Exception $e) {
    error_log("ERROR: profile.php: " . $e->getMessage());
    http_response_code(500);
    echo "500 Internal Server Error";
    exit;
}

?>
<?php

require_once('../lib/database.php');
require_once('../lib/scooters/issues.php');
require_once('../lib/http_exceptions/bad_request.php');

session_start();

try {
    $user_email = $_SESSION['user_email'];
    $is_admin = $_SESSION['is_admin'];

    if (!isset($user_email)) {
        // TODO: dare errore
        header('Location: /');
        exit;
    }

    if (!$is_admin) {
        // TODO: dare errore
        header('Location: /');
        exit;
    }

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
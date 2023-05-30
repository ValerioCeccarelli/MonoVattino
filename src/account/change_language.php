<?php

require_once('../lib/accounts/user.php');
require_once('../lib/database.php');
// require_once('../lib/jwt.php');
require_once('../lib/redirect_to.php');

session_start();

try {
    if (isset($_SESSION['user_email'])) {
        $lang = $_GET['lang'];

        // $jwt_payload = validate_jwt();

        // TODO: check if lang is valid

        $conn = connect_to_database();
        change_language($conn, $email, $lang);

        try_redirect();

        header('Location: /');
    } else {
        //TODO da cambiare in 401 unauthorized
        throw new Exception("Unauthorized");
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log("ERROR: change_language.php: " . $e->getMessage());
    echo "500 Internal Server Error";
    exit;
}
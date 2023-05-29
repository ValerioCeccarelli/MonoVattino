<?php

require_once('../lib/accounts/user.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');
require_once('../lib/redirect_to.php');
require_once('../lib/account/themes.php');

try {
    $lang = $_GET['lang'];

    // validate_language($lang); //

    $jwt_payload = validate_jwt();

    $conn = connect_to_database();
    change_language($conn, $jwt_payload->email, $lang);

    try_redirect();

    header('Location: /');
} catch (Exception $e) {
    http_response_code(500);
    error_log("ERROR: change_language.php: " . $e->getMessage());
    echo "500 Internal Server Error";
    exit;
}
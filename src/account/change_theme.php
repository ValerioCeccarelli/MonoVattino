<?php

require_once('../lib/accounts/user.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');

try {
    $theme = $_GET['theme'];

    $jwt_payload = validate_jwt();

    if (!is_valid_map_theme($theme)) {
        //TODO da cambiare in 400 bad request
        throw new Exception("Invalid map theme: $theme");
    }

    $conn = connect_to_database();
    update_html_theme($conn, $jwt_payload->email, $theme);
} catch (Exception $e) {
    http_response_code(500);
    error_log("ERROR: change_language.php: " . $e->getMessage());
    echo "500 Internal Server Error";
    exit;
}

?>
<?php

require_once('../lib/accounts/user.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');
require_once('../lib/accounts/themes.php');
require_once('../lib/redirect_to.php');

try {
    $theme = $_GET['map'];

    $jwt_payload = validate_jwt();

    if (!is_valid_map_theme($theme)) {
        //TODO da cambiare in 400 bad request
        throw new Exception("Invalid map theme: $theme");
    }

    $conn = connect_to_database();
    update_map_theme($conn, $jwt_payload->email, $theme);

    try_redirect();
} catch (Exception $e) {
    http_response_code(500);
    error_log("ERROR: change_language.php: " . $e->getMessage());
    echo "500 Internal Server Error";
    exit;
}

?>
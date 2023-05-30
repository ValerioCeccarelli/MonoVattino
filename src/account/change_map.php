<?php

require_once('../lib/accounts/user.php');
require_once('../lib/database.php');
// require_once('../lib/jwt.php');
require_once('../lib/accounts/themes.php');
require_once('../lib/redirect_to.php');

session_start();

try {
    $theme = $_GET['map'];

    if (!is_valid_map_theme($theme)) {
        throw new BadRequestException("Invalid map theme: $theme");
    }

    $_SESSION['map_theme'] = $theme;

    if (isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];

        $conn = connect_to_database();
        update_map_theme($conn, $email, $theme);

    }
} catch (BadRequestException $e) {
    http_response_code(400);
    echo "400 Bad Request";
    exit;
} catch (Exception $e) {
    http_response_code(500);
    error_log("ERROR: change_language.php: " . $e->getMessage());
    echo "500 Internal Server Error";
    exit;
}

?>
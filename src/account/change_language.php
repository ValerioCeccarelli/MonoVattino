<?php

require_once('../lib/accounts/user.php');
require_once('../lib/database.php');
require_once('../lib/redirect_to.php');

session_start();

try {
    $lang = $_GET['lang'];

    if (!in_array($lang, array('en', 'it', 'de', 'es'))) {
        $lang = 'en';
    }

    if (isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];
        $conn = connect_to_database();
        change_language($conn, $email, $lang);
    }
    $_SESSION['language'] = $lang;

    try_redirect();

    header('Location: /');
} catch (Exception $e) {
    http_response_code(500);
    error_log("ERROR: change_language.php: " . $e->getMessage());
    echo "500 Internal Server Error";
    exit;
}
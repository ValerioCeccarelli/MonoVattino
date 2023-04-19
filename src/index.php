<?php 

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo 'ERROR 405: Method Not Allowed';
    exit;
}

if (empty($_COOKIE['jwt'])) {
    echo "You are not logged in!";
} else {
    require_once("lib/jwt.php");

    $jwt = $_COOKIE['jwt'];
    $payload = jwt_decode($jwt);

    if ($payload) {
        echo "Hello, " . $payload->username;
    } else {
        echo "invalid token";
    }
}

?>
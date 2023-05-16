<?php

$is_user_logged = false;
$jwt_payload = null;
$username = null;

if (isset($_COOKIE['jwt'])) {
    require_once('lib/jwt.php');
    $jwt_payload = jwt_decode($_COOKIE['jwt']);
    if ($jwt_payload) {
        $is_user_logged = true;
        $username = $jwt_payload->username;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="index.js" defer></script>
</head>

<body>
    <?php
    if ($is_user_logged === false) {
        echo 'Hello guest<br>you need to <a href="account/login.php">login</a> or <a href="account/register.php">register</a>!';
    }else {
        echo 'Hello ' . $username . '<br>you can <a href="account/logout.php">logout</a>.';
    }
    ?>

    <div id="map"></div>

    <div id="info_scooter" class="info_scooter">
        <h1>Info Scooter</h1>
        <p>Id: <span id="scooter_id"></span></p>
        <p>Latitudine: <span id="scooter_lat"></span></p>
        <p>Longitudine: <span id="scooter_lng"></span></p>
        <p>Battery: <span id="scooter_battery"></span></p>
        <p>Company: <span id="scooter_company"></span></p>

        <button id="btn_reserve">Reserve</button>
        <button id="btn_release">Release</button>
    </div>
</body>

</html>
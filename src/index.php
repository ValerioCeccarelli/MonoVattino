<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="gmaps.js"></script>
    <script src="index.js"></script>
</head>

<body>
    <?php 
    require_once('lib/jwt.php');
    if (!isset($_COOKIE['jwt']) || !jwt_decode($_COOKIE['jwt'])) {
        echo 'Hello guest<br>yuo need to <a href="account/login.php">login</a> or <a href="account/register.php">register</a>!';
    }else {
        echo 'Hello user<br>you can <a href="account/logout.php">logout</a> or <a href="account/profile.php">view your profile</a>!';
    }
    ?>

    <div id="map"></div>
</body>

</html>
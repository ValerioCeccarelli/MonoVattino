<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>MonoVattino</title>
</head>
<body>
    index page</br>
    <a href="account/login.php">Login</a></br>
    <a href="account/register.php">Register</a>

    <?php 
    
    if (isset($_COOKIE['jwt'])) {
        require_once('lib/jwt.php');
        $jwt = $_COOKIE['jwt'];
        $jwt_payload = jwt_decode($jwt);
        if ($jwt_payload) {
            echo 'Hello ' . $jwt_payload->username;
        }
        else {
            echo 'Hello guest, invalid jwt';
        }
    }else {
        echo 'Hello guest';
    }
    
    ?>
</body>
</html>
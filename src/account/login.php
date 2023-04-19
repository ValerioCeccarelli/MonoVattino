<?php 

$email = "";
$email_error = "";
$password = "";
$password_error = "";

function display_error($error) {
    if (!empty($error)) {
        echo '<p style="color: red;">' . $error . '</p>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # pass
}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('../lib/user.php');
    require_once('../lib/validate_user.php');

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        validate_email($email);
        validate_password($password);
        
        require_once('../lib/database.php');

        $conn = connect_to_database();

        if (!$conn) {
            echo 'ERROR 500: Internal Server Error';
            exit;
        }

        $db_user = get_user_by_email($conn, $email);

        $is_valid = verify_password($db_user, $password);

        if ($is_valid) {
            require_once('../lib/jwt.php');

            $jwt_payload = new JwtPayload();
            $jwt_payload->email = $email;
            $jwt_payload->username = $db_user->username;

            $jwt = generate_jwt($db_user);

            setcookie('jwt', $jwt, get_jwt_expire_time(), "/");

            header('Location: /');
            exit;
        }
        else {
            $password_error = "Wrong password!";
        }
    }
    catch (InvalidEmailException $e) {
        $email_error = $e->getMessage();
    }
    catch (InvalidPasswordException $e) {
        $password_error = $e->getMessage();
    }
    catch (UserNotFoundException $e) {
        $email_error = "This email is not registered!";
    }
    catch (Exception $e) {
        echo 'ERROR 500: Internal Server Error';
        exit;
    }
}
else {
    echo 'ERROR 405: Method Not Allowed';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MonoVattino</title>
</head>
<body>
    <form action="/account/login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo $email ?>" required>
        <?php display_error($email_error) ?>
</br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" value="<?php echo $password ?>" required>
        <?php display_error($password_error) ?>
</br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
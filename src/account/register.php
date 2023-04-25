<?php 

$username = "";
$username_error = "";
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
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../lib/user.php');
    require_once('../lib/validate_user.php');

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        validate_username($username);
        validate_email($email);
        validate_password($password);

        require_once('../lib/database.php');

        $conn = connect_to_database();

        if (!$conn) {
            throw new Exception('Connection to database failed!');
        }

        $user = new User();
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;

        create_new_user($conn, $user);

        require_once('../lib/jwt.php');

        $jwt_payload = new JwtPayload();
        $jwt_payload->email = $email;
        $jwt_payload->username = $username;

        $jwt = generate_jwt($db_user);

        setcookie('jwt', $jwt, get_jwt_expire_time(), "/");
            
        header('Location: /');
        exit;
    }
    catch (InvalidEmailException $e) {
        $email_error = $e->getMessage();
    }
    catch (InvalidPasswordException $e) {
        $password_error = $e->getMessage();
    }
    catch (InvalidUsernameException $e) {
        $username_error = $e->getMessage();
    }
    catch (EmailAlreadyUsedException $th) {
        $email_error = "This email is already in use!";
    } catch (Exception $e) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Register - MonoVattino</title>

</head>

<body>
    <div class="row">
        <div class="mx-auto col-10 col-md-8 col-lg-6">

            <form action="/account/register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="username" name="username" id="username" class="form-control"
                        value="<?php echo $username ?>" required>
                    <?php display_error($username_error) ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo $email ?>"
                        required>
                    <?php display_error($email_error) ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                        value="<?php echo $password ?>" required>
                    <?php display_error($password_error) ?>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>

        </div>
    </div>
</body>

</html>
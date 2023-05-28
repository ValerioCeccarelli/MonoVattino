<?php
require_once('../lib/accounts/user.php');
require_once('../lib/accounts/validate_user.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');

$email = "";
$email_error = null;
$password = "";
$password_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # pass
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        validate_email($email);

        try {
            validate_password($password);
        } catch (InvalidPasswordException $e) {
            throw new InvalidPasswordException("Invalid password!");
        }


        $conn = connect_to_database();

        $db_user = get_user_by_email($conn, $email);

        $is_valid = verify_password($db_user, $password);

        if ($is_valid) {

            $jwt_payload = new JwtPayload();
            $jwt_payload->email = $email;
            $jwt_payload->username = $db_user->username;

            $jwt = generate_jwt($db_user);

            setcookie('jwt', $jwt, get_jwt_expire_time(), "/");

            header('Location: /');
            exit;
        } else {
            $password_error = "Wrong password!";
        }
    } catch (InvalidEmailException $e) {
        $email_error = $e->getMessage();
    } catch (InvalidPasswordException $e) {
        $password_error = $e->getMessage();
    } catch (NoUserFoundException $e) {
        $email_error = "This email is not registered!";
    } catch (Exception $e) {
        echo 'ERROR 500: Internal Server Error';
        error_log("ERROR: register page " . $e->getMessage());
        exit;
    }
} else {
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
    <title>Login</title>

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Custom css -->
    <link rel="stylesheet" href="form.css">
</head>

<body>
    <section>
        <div class="form-box">
            <div class="form-padding">
                <div class="form-value">
                    <form action="/account/login.php" method="POST">
                        <!-- Title -->
                        <h2>
                            Login
                            <i class="bi bi-scooter"></i>
                        </h2>

                        <!-- Email input -->
                        <div class="inputbox">
                            <ion-icon name="mail-outline"></ion-icon>
                            <input id="email" name="email" type="email" value="<?php echo $email; ?>" required>
                            <label id="email_label" for="email">Email</label>
                        </div>

                        <!-- Email error -->
                        <?php if ($email_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $email_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Password input -->
                        <div class="inputbox">
                            <ion-icon name="lock-closed-outline" onclick="togglePassword()"></ion-icon>
                            <input id="password" name="password" type="password" value="<?php echo $password; ?>"
                                required>
                            <label id="password_label" for="password">Password</label>
                        </div>

                        <!-- Password error -->
                        <?php if ($password_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $password_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Padding -->
                        <div style="height: 30px"></div>

                        <!-- Login button -->
                        <button type="submit">Log in</button>

                        <!-- Register page link -->
                        <div class="register">
                            <p>Don't have an account? <a href="register.php">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function setLabelControls(input_id, label_id) {
            if ($(input_id).val() != "") {
                $(label_id).css('top', '-5px')
            }
            $(input_id).focus(function () {
                $(label_id).css('top', '-5px')
            });
            $(input_id).blur(function () {
                if ($(input_id).val() == "") {
                    $(label_id).css('top', '50%')
                }
            });
        }

        function togglePassword() {
            $('#password').attr('type', $('#password').attr('type') == 'password' ? 'text' : 'password');
        }

        setLabelControls('#email', '#email_label');
        setLabelControls('#password', '#password_label');
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
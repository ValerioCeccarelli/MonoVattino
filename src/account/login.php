<?php
require_once('../lib/accounts/user.php');
require_once('../lib/accounts/validate_user.php');
require_once('../lib/database.php');
require_once('../lib/redirect_to.php');

session_start();

$email = "";
$email_error = null;
$password = "";
$password_error = null;

$redirect_to = get_redirect_to();
$id = $_GET['id'];

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

            $_SESSION['user_email'] = $db_user->email;
            $_SESSION['user_username'] = $db_user->username;
            $_SESSION['html_theme'] = $db_user->html_theme;
            $_SESSION['map_theme'] = $db_user->map_theme;
            $_SESSION['language'] = $db_user->language;
            $_SESSION['is_admin'] = $db_user->is_admin;

            try_redirect();

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
    <link rel="stylesheet" href="/account/form.css">
</head>

<body>
    <section>
        <div class="form-box">
            <div class="form-padding">
                <div class="form-value">
                    <form action="/account/login.php<?php if ($redirect_to)
                        echo "?redirect_to=$redirect_to"; ?><?php if (isset($id))
                              echo "&id=$id"; ?>" method="POST">
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
                            <ion-icon id="togglePasswordIcon" name="eye-off-outline" onmouseenter="showPassword()"
                                onmouseleave="hidePassword()" onfocusout="checkPassword()"></ion-icon>
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
                            <p>Don't have an account? <a href="/account/register.php<?php if ($redirect_to)
                                echo "?redirect_to=$redirect_to"; ?>">Register</a>
                            </p>
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
        setLabelControls('#email', '#email_label');
        setLabelControls('#password', '#password_label');

        function showPassword() {
            var passwordInput = document.getElementById("password");
            var togglePasswordIcon = document.getElementById("togglePasswordIcon");

            passwordInput.type = "text";
            togglePasswordIcon.name = "eye-outline";
        }

        function hidePassword() {
            var passwordInput = document.getElementById("password");
            var togglePasswordIcon = document.getElementById("togglePasswordIcon");

            passwordInput.type = "password";
            togglePasswordIcon.name = "eye-off-outline";
        }
        function checkPassword() {
            var passwordInput = document.getElementById("password");
            var togglePasswordIcon = document.getElementById("togglePasswordIcon");

            if (passwordInput.value != "") {
                passwordInput.type = "password";
                togglePasswordIcon.name = "eye-off-outline";
            }
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
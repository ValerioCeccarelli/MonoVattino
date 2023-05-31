<?php
require_once('../lib/accounts/user.php');
require_once('../lib/accounts/validate_user.php');
require_once('../lib/database.php');
require_once('../lib/jwt.php');
require_once('../lib/redirect_to.php');

session_start();

$old_password = "";
$new_password = "";
$confirm_password = "";

$old_password_error = "";
$new_password_error = "";
$confirm_password_error = "";

if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
} else {
    header('Location: /account/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # pass
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        validate_password($new_password);

        if ($new_password != $confirm_password) {
            $confirm_password_error = "Passwords do not match!";
            throw new InvalidPasswordException("Passwords do not match!");
        }

        $conn = connect_to_database();

        $db_user = get_user_by_email($conn, $email);

        $is_valid = verify_password($db_user, $old_password);

        if ($is_valid) {

            update_password($conn, $email, $new_password);

            header('Location: /account/profile.php');
            exit;
        } else {
            $old_password_error = "Wrong password!";
        }
    } catch (InvalidPasswordException $e) {
        $new_password_error = $e->getMessage();
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
    <title>Change Password | MV</title>

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
                    <form action="/account/change_password.php" method="POST">
                        <!-- Title -->
                        <h2>
                            Change Password
                            <i class="bi bi-scooter"></i>
                        </h2>

                        <!-- Old Password input -->
                        <div class="inputbox">
                            <ion-icon id="old_togglePasswordIcon" name="eye-off-outline"
                                onmouseenter="showPassword('old')" onmouseleave="hidePassword('old')"></ion-icon>
                            <input id="old_password" name="old_password" type="password"
                                value="<?php echo $old_password; ?>" required>
                            <label id="old_password_label" for="old_password">Password</label>
                        </div>

                        <!-- Old Password error -->
                        <h5 class="error-msg">
                            <?php echo $old_password_error; ?>
                        </h5>

                        <!-- New Password input -->
                        <div class="inputbox">
                            <ion-icon id="new_togglePasswordIcon" name="eye-off-outline"
                                onmouseenter="showPassword('new')" onmouseleave="hidePassword('new')"></ion-icon>
                            <input id="new_password" name="new_password" type="password"
                                value="<?php echo $new_password; ?>" required>
                            <label id="new_password_label" for="new_password">Password</label>
                        </div>

                        <!-- New Password error -->
                        <h5 class="error-msg">
                            <?php echo $new_password_error; ?>
                        </h5>

                        <!-- Confirm Password input -->
                        <div class="inputbox">
                            <ion-icon id="confirm_togglePasswordIcon" name="eye-off-outline"
                                onmouseenter="showPassword('confirm')" onmouseleave="hidePassword('confirm')">
                            </ion-icon>
                            <input id="confirm_password" name="confirm_password" type="password"
                                value="<?php echo $confirm_password; ?>" required>
                            <label id="confirm_password_label" for="confirm_password">Password</label>
                        </div>

                        <!-- Confirm Password error -->
                        <h5 class="error-msg">
                            <?php echo $confirm_password_error; ?>
                        </h5>

                        <!-- Padding -->
                        <div style="height: 30px"></div>

                        <!-- Login button -->
                        <button type="submit">Change</button>

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
        $(input_id).focus(function() {
            $(label_id).css('top', '-5px')
        });
        $(input_id).blur(function() {
            if ($(input_id).val() == "") {
                $(label_id).css('top', '50%')
            }
        });
    }
    setLabelControls('#old_password', '#old_password_label');
    setLabelControls('#new_password', '#new_password_label');
    setLabelControls('#confirm_password', '#confirm_password_label');

    function showPassword(id) {
        var passwordInput = document.getElementById(id + "_password");
        var togglePasswordIcon = document.getElementById(id + "_togglePasswordIcon");

        passwordInput.type = "text";
        togglePasswordIcon.name = "eye-outline";
    }

    function hidePassword(id) {
        var passwordInput = document.getElementById(id + "_password");
        var togglePasswordIcon = document.getElementById(id + "_togglePasswordIcon");

        passwordInput.type = "password";
        togglePasswordIcon.name = "eye-off-outline";
    }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
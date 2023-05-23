<?php

$username = "";
$username_error = null;
$email = "";
$email_error = null;
$password = "";
$password_error = null;
$credit_card = "";
$credit_card_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # pass
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../lib/user.php');
    require_once('../lib/validate_user.php');

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $credit_card = $_POST['credit_card'];

    try {
        validate_username($username);
        validate_email($email);
        validate_password($password);
        validate_credit_card($credit_card);

        require_once('../lib/database.php');

        $conn = connect_to_database();

        $user = new User();
        $user->username = $username;
        $user->password = $password;
        $user->credit_card = $credit_card;
        $user->email = $email;

        create_new_user($conn, $user);

        require_once('../lib/jwt.php');

        $jwt_payload = new JwtPayload();
        $jwt_payload->email = $email;
        $jwt_payload->username = $username;

        $jwt = generate_jwt($jwt_payload);

        setcookie('jwt', $jwt, get_jwt_expire_time(), "/");

        header('Location: /');
        exit;
    } catch (InvalidEmailException $e) {
        $email_error = $e->getMessage();
    } catch (InvalidPasswordException $e) {
        $password_error = $e->getMessage();
    } catch (InvalidUsernameException $e) {
        $username_error = $e->getMessage();
    } catch (InvalidCreditCardException $e) {
        $credit_card_error = $e->getMessage();
    } catch (EmailAlreadyUsedException $th) {
        $email_error = "This email is already in use!";
    } catch (Exception $e) {
        echo 'ERROR 500: Internal Server Error';
        error_log("ERROR: register page" . $e->getMessage());
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
    <title>Register</title>

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
                    <form action="/account/register.php" method="POST">
                        <!-- Title -->
                        <h2>
                            Register
                            <i class="bi bi-scooter"></i>
                        </h2>

                        <!-- Username input -->
                        <div class="inputbox">
                            <ion-icon name="person-outline"></ion-icon>
                            <input id="username" name="username" type="text" value="<?php echo $username; ?>" required>
                            <label id="username_label" for="username">Username</label>
                        </div>

                        <!-- Username error -->
                        <?php if ($username_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $username_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Credit card input -->
                        <div class="inputbox">
                            <ion-icon name="card-outline"></ion-icon>
                            <input id="credit_card" name="credit_card" type="text" value="<?php echo $credit_card; ?>"
                                required>
                            <label id="credit_card_label" for="credit_card">Credit card</label>
                        </div>

                        <!-- Credit card error -->
                        <?php if ($credit_card_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $credit_card_error; ?>
                            </h5>
                        <?php } ?>

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
                            <ion-icon name="lock-closed-outline"></ion-icon>
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

                        <!-- Register button -->
                        <button type="submit">Register</button>

                        <!-- Register page link -->
                        <div class="register">
                            <p>Already signed up? <a href="login.php">Login</a></p>
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
        setLabelControls('#username', '#username_label');
        setLabelControls('#credit_card', '#credit_card_label');
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
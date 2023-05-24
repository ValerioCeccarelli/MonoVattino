<?php

$username = "";
$username_error = null;
$name = "";
$name_error = null;
$surname = "";
$surname_error = null;
$email = "";
$email_error = null;
$password = "";
$password_error = null;
$date_of_birth = "";
$date_of_birth_error = null;


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # pass
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../lib/user.php');
    require_once('../lib/validate_user.php');

    $username = $_POST['username'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $date_of_birth = $_POST['date_of_birth'];

    try {
        validate_username($username);
        validate_email($email);
        validate_password($password);
        validate_name($name);
        validate_surname($surname);
        validate_date_of_birth($date_of_birth);

        require_once('../lib/database.php');

        $conn = connect_to_database();

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $user->name = $name;
        $user->surname = $username;
        $user->date_of_birth = $date_of_birth;

        $user->privacy_policy_accepted = false;
        $user->terms_and_conditions_accepted = false;

        $user->payment_method = null;

        create_new_user($conn, $user);

        require_once('../lib/jwt.php');

        $jwt_payload = new JwtPayload();
        $jwt_payload->email = $email;
        $jwt_payload->username = $username;

        $jwt = generate_jwt($jwt_payload);

        setcookie('jwt', $jwt, get_jwt_expire_time(), "/");

        header('Location: /account/terms.php');
        exit;
    } catch (InvalidEmailException $e) {
        $email_error = $e->getMessage();
    } catch (InvalidPasswordException $e) {
        $password_error = $e->getMessage();
    } catch (InvalidUsernameException $e) {
        $username_error = $e->getMessage();
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

// TODO: aggiungere cose come l'età della persona, il numero di telefono, ecc...

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

                        <!-- Name input -->
                        <div class="inputbox">
                            <ion-icon name="person-outline"></ion-icon>
                            <input id="name" name="name" type="text" value="<?php echo $name; ?>" required>
                            <label id="name_label" for="name">Name</label>
                        </div>

                        <!-- Name error -->
                        <?php if ($name_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $name_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Surname input -->
                        <div class="inputbox">
                            <ion-icon name="person-outline"></ion-icon>
                            <input id="surname" name="surname" type="text" value="<?php echo $surname; ?>" required>
                            <label id="surname_label" for="surname">Surname</label>
                        </div>

                        <!-- Surname error -->
                        <?php if ($surname_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $surname_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Date of birth input -->
                        <div class="inputbox">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <input id="date_of_birth" name="date_of_birth" type="date"
                                value="<?php echo $date_of_birth; ?>" required>
                            <label id="date_of_birth_label" for="date_of_birth">Date of birth</label>
                        </div>

                        <!-- Date of birth error -->
                        <?php if ($date_of_birth_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $date_of_birth_error; ?>
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
        setLabelControls('#name', '#name_label');
        setLabelControls('#surname', '#surname_label');
        setLabelControls('#date_of_birth', '#date_of_birth_label');
        $('#date_of_birth_label').css('top', '-5px')
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
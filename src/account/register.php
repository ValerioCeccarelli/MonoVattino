<?php
require_once('../lib/accounts/user.php');
require_once('../lib/accounts/validate_user.php');
require_once('../lib/database.php');
require_once('../lib/redirect_to.php');

session_start();

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
$phone_number = "";
$phone_number_error = null;

$redirect_to = get_redirect_to();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # pass
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];

    try {
        validate_username($username);
        validate_email($email);
        validate_password($password);
        validate_name($name);
        validate_surname($surname);
        validate_date_of_birth($date_of_birth);
        validate_phone_number($phone_number);

        $conn = connect_to_database();

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = $password;
        $user->name = $name;
        $user->surname = $username;
        $user->date_of_birth = $date_of_birth;
        $user->phone_number = $phone_number;

        $user->privacy_policy_accepted = false;
        $user->terms_and_conditions_accepted = false;

        $user->payment_method = null;

        create_new_user($conn, $user);

        $_SESSION['user_email'] = $email;
        $_SESSION['user_username'] = $username;
        $_SESSION['html_theme'] = 'light';
        $_SESSION['map_theme'] = 'default';
        $_SESSION['language'] = 'en';
        $_SESSION['is_admin'] = false;

        try_redirect();

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
    } catch (InvalidDateOfBirthException $e) {
        $date_of_birth_error = $e->getMessage();
    } catch (InvalidNameException $e) {
        $name_error = $e->getMessage();
    } catch (InvalidSurnameException $e) {
        $surname_error = $e->getMessage();
    } catch (InvalidPhoneNumberException $e) {
        $phone_number_error = $e->getMessage();
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
    <link rel="stylesheet" href="/account/form.css">
</head>

<body>
    <section>
        <div class="form-box">
            <div class="form-padding">
                <div class="form-value">
                    <form action="/account/register.php<?php if ($redirect_to)
                        echo "?redirect_to=$redirect_to"; ?>" method="POST">
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
                            <input id="email" name="email" type="email" value="<?php echo $email; ?>">
                            <label id="email_label" for="email">Email</label>
                        </div>

                        <!-- Email error -->
                        <h5 class="error-msg">
                            <?php echo $email_error; ?>
                        </h5>

                        <!-- Password input -->
                        <div class="inputbox">
                            <ion-icon id="togglePasswordIcon" name="eye-off-outline" onmouseenter="showPassword()"
                                onmouseleave="hidePassword()">
                            </ion-icon>
                            <input id="password" name="password" onfocusout="checkPassword()" type="password"
                                value="<?php echo $password; ?>" required>
                            <label id="password_label" for="password">Password</label>
                        </div>

                        <!-- Password error -->
                        <h5 id="password_error" class="error-msg">
                            <?php echo $password_error; ?>
                        </h5>

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
                            <input id="date_of_birth" onfocusout="checkDOB()" name="date_of_birth" type="date"
                                value="<?php echo $date_of_birth; ?>" required>
                            <label id="date_of_birth_label" for="date_of_birth">Date of birth</label>
                        </div>

                        <!-- Date of birth error -->
                        <h5 id="DOB_err" class="error-msg">
                            <?php echo $date_of_birth_error; ?>
                        </h5>

                        <!-- Phone number input -->
                        <div class="inputbox">
                            <ion-icon name="call-outline"></ion-icon>
                            <input id="phone_number" name="phone_number" type="tel" value="<?php echo $phone_number; ?>"
                                required>
                            <label id="phone_number_label" for="phone_number">Phone number</label>
                        </div>

                        <!-- Phone number error -->
                        <?php if ($phone_number_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $phone_number_error; ?>
                            </h5>
                        <?php } ?>


                        <!-- Padding -->
                        <div style="height: 30px"></div>

                        <!-- Register button -->
                        <button type="submit">Register</button>

                        <!-- Register page link -->
                        <div class="register">

                            <p>Already signed up? <a href="login.php<?php if ($redirect_to)
                                echo "?redirect_to=$redirect_to"; ?>">Login</a>
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
        setLabelControls('#username', '#username_label');
        setLabelControls('#name', '#name_label');
        setLabelControls('#surname', '#surname_label');
        setLabelControls('#phone_number', '#phone_number_label');
        $('#date_of_birth_label').css('top', '-5px')

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
            var passwordLabel = document.getElementById("password_error");

            var password = passwordInput.value;
            var errorMsg = "";

            if (password.length < 8) {
                errorMsg = "Password must be at least 8 characters long";
            }
            else if (password.search(/[a-z]/) == -1) {
                errorMsg = "Password must contain at least one lowercase letter";
            }
            else if (password.search(/[A-Z]/) == -1) {
                errorMsg = "Password must contain at least one uppercase letter";
            }
            else if (password.search(/[0-9]/) == -1) {
                errorMsg = "Password must contain at least one digit";
            }
            else if (password.search(/[!@#$%^&*()\-_=+{};:,<.>]/) == -1) {
                errorMsg = "Password must contain at least one special character";
            }

            passwordLabel.innerHTML = errorMsg;

        }
        function checkDOB() {
            var dateOfBirth = $('#date_of_birth').val();
            var dateOfBirthLabel = $('#DOB_err');

            var errorMsg = "";

            var today = new Date();
            var birthDate = new Date(dateOfBirth);
            var age = today.getFullYear() - birthDate.getFullYear();
            var month = today.getMonth() - birthDate.getMonth();

            if (age < 18) {
                errorMsg = "You must be at least 18 years old to register";
            }
            else if (age == 18 && month < 0) {
                errorMsg = "You must be at least 18 years old to register";
            }

            dateOfBirthLabel.html(errorMsg);
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
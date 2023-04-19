<?php 

function display_generic_error() {
    global $error_to_display;
    if (!empty($error_to_display)) {
        echo '<p style="color: red;">' . $error_to_display . "</p>";
    }
}

function display_password_error() {
    global $password_error;
    if (!empty($password_error)) {
        echo '<p style="color: red;">' . $password_error . "</p>";
    }
}

function display_email_error() {
    global $email_error;
    if (!empty($email_error)) {
        echo '<p style="color: red;">' . $email_error . "</p>";
    }
}

function display_email() {
    global $current_email;
    if (!empty($current_email)) {
        echo 'value="' . $current_email . '"';
    }
}

function display_password() {
    global $current_password;
    if (!empty($current_password)) {
        echo 'value="' . $current_password . '"';
    }
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
    <?php display_generic_error() ?>
    <form action="/account/login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" <?php display_email() ?> required>
        <?php display_email_error() ?>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" <?php display_password() ?> >
        <?php display_password_error() ?>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="/account/register.php">Register</a></p>
</body>
</html>
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
    <title>Register - MonoVattino</title>
</head>
<body>
    <form action="/account/register.php" method="POST">
        <label for="username">Username</label>
        <input type="username" name="username" id="username" value="<?php echo $username ?>" required>
        <?php display_error($username_error) ?>
</br>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo $email ?>" required>
        <?php display_error($email_error) ?>
</br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" value="<?php echo $password ?>" required>
        <?php display_error($password_error) ?>
</br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
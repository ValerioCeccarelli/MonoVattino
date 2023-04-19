<?php 

class InvalidEmailException extends Exception
{
    public function __construct($message) {
        parent::__construct($message, 0, null);
    }
}

class InvalidPasswordException extends Exception
{
    public function __construct($message) {
        parent::__construct($message, 0, null);
    }
}

class InvalidUsernameException extends Exception
{
    public function __construct($message) {
        parent::__construct($message, 0, null);
    }
}

function validate_email($email) {
    // echo $email;
    if (empty($email)) {
        throw new InvalidEmailException("Email is required!");
    }
    if (strlen($email) > 50) {
        throw new InvalidEmailException("Email must be at most 50 characters long!");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidEmailException("Please enter a valid email!");
    }
}

function validate_password($password) {
    if (empty($password)) {
        throw new InvalidPasswordException("Password is required!");
    }
    if (strlen($password) < 8) {
        throw new InvalidPasswordException("Password must be at least 8 characters long!");
    }
    if (strlen($password) > 64) {
        throw new InvalidPasswordException("Password must be at most 64 characters long!");
    }
    if (!preg_match('/[A-Z]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one uppercase letter!");
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one lowercase letter!");
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one number!");
    }
}

function validate_username($username) {
    if (empty($username)) {
        throw new InvalidUsernameException("Username is required!");
    }
    if (strlen($username) < 3) {
        throw new InvalidUsernameException("Username must be at least 3 characters long!");
    }
    if (strlen($username) > 20) {
        throw new InvalidUsernameException("Username must be at most 20 characters long!");
    }
}

# validate the user by comparing the password with the hashed one (db_user is the user from the database with the hashed password and salt)
function verify_password($db_user, $password) {
    $password_hash = hash('sha256', $password . $db_user->salt);
    return $password_hash === $db_user->password;
}

?>
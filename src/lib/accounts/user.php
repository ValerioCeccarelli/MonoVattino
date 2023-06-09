<?php

error_reporting(0);

class User
{
    public $username;
    public $email;
    public $password;
    public $name;
    public $surname;
    public $date_of_birth;
    public $phone_number;
    public $salt;

    public $privacy_policy_accepted;
    public $terms_and_conditions_accepted;

    public $payment_method;

    public $map_theme;
    public $html_theme;

    public $is_admin;
    public $language;
}

class NoUserFoundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

# get the user from the database as a User object (password is the hashed one)
# throws an Exception if the query fails
function get_user_by_email($conn, $email)
{
    $query = "SELECT username, email, password, 
                salt, privacy_policy_accepted, 
                terms_and_conditions_accepted, payment_method,
                map_theme, html_theme,
                name, surname, date_of_birth, phone_number, is_admin, language
            FROM users WHERE email = $1";

    $result1 = pg_prepare($conn, "get_user_by_email", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_user_by_email", array($email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $first_line = pg_fetch_array($result2, null, PGSQL_ASSOC);
    if (!$first_line) {
        throw new NoUserFoundException("No user found with email: $email!");
    }

    $user = new User();
    $user->username = $first_line['username'];
    $user->name = $first_line['name'];
    $user->surname = $first_line['surname'];
    $user->date_of_birth = $first_line['date_of_birth'];
    $user->phone_number = $first_line['phone_number'];
    $user->email = $first_line['email'];
    $user->password = $first_line['password'];
    $user->salt = $first_line['salt'];
    $user->privacy_policy_accepted = $first_line['privacy_policy_accepted'];
    $user->terms_and_conditions_accepted = $first_line['terms_and_conditions_accepted'];
    $user->payment_method = $first_line['payment_method'];
    $user->map_theme = $first_line['map_theme'];
    $user->html_theme = $first_line['html_theme'];
    $user->is_admin = $first_line['is_admin'];
    $user->language = $first_line['language'];

    if ($user->privacy_policy_accepted == 't') {
        $user->privacy_policy_accepted = true;
    } else {
        $user->privacy_policy_accepted = false;
    }

    if ($user->terms_and_conditions_accepted == 't') {
        $user->terms_and_conditions_accepted = true;
    } else {
        $user->terms_and_conditions_accepted = false;
    }

    if ($user->is_admin == 't') {
        $user->is_admin = true;
    } else {
        $user->is_admin = false;
    }

    return $user;
}

function generate_random_string($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}

class EmailAlreadyUsedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Email already used", 0, null);
    }
}

# create the user in the database with a password hashed with a salt
# user should be a User object
# the salt is generated randomly, and the password must be the plain one
# throws an EmailAlreadyUsedException if the email is already in the database
# throws an Exception if the query fails
function create_new_user($conn, $user)
{
    $query = "INSERT INTO users \n(username, password, salt, email, privacy_policy_accepted, 
    terms_and_conditions_accepted, payment_method,
    name, surname, date_of_birth, phone_number, map_theme, html_theme, is_admin, language
     ) \nVALUES ($1, $2, $3, $4, false, false, null, $5, $6, $7, $8, 'default', 'light', false, 'en')";
    $result1 = pg_prepare($conn, "create_new_user", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $username = $user->username;
    $password = $user->password;
    $email = $user->email;
    $name = $user->name;
    $surname = $user->surname;
    $date_of_birth = $user->date_of_birth;
    $phone_number = $user->phone_number;

    $salt = generate_random_string(10);
    $password_hash = hash('sha256', $password . $salt);

    $result2 = pg_execute($conn, "create_new_user", array($username, $password_hash, $salt, $email, $name, $surname, $date_of_birth, $phone_number));

    if (!$result2) {
        $error = pg_last_error();
        if (strpos($error, 'duplicate key value violates unique constraint') !== false) {
            throw new EmailAlreadyUsedException();
        }

        throw new Exception("Could not execute the query: " . $error);
    }
}

function update_password($conn, $email, $new_password)
{
    $salt = generate_random_string(10);
    $password_hash = hash('sha256', $new_password . $salt);

    $query = "UPDATE users SET password = $1, salt = $2 WHERE email = $3";
    $result1 = pg_prepare($conn, "update_password", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "update_password", array($password_hash, $salt, $email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

class UserCanNotReserveException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

// Check if the user can reserve a scooter
// Throws an Exception if the query fails
// Throws a NoUserFoundException if the user is not found
// Throws a UserCanNotReserveException if the user can not reserve, with a message that explains why
function check_if_user_can_reserve($conn, $user_id)
{
    $query = "SELECT privacy_policy_accepted, terms_and_conditions_accepted, payment_method FROM users WHERE email = $1";
    $result1 = pg_prepare($conn, "check_if_user_can_reserve", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "check_if_user_can_reserve", array($user_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $first_line = pg_fetch_array($result2, null, PGSQL_ASSOC);
    if (!$first_line) {
        throw new NoUserFoundException("No user found with id: $user_id!");
    }

    $privacy_policy_accepted = $first_line['privacy_policy_accepted'];
    $terms_and_conditions_accepted = $first_line['terms_and_conditions_accepted'];
    $payment_method = $first_line['payment_method'];

    if ($privacy_policy_accepted == 'f') {
        throw new UserCanNotReserveException("Privacy policy not accepted!\nPlease, accept the privacy policy in your profile!");
    }

    if ($terms_and_conditions_accepted == 'f') {
        throw new UserCanNotReserveException("Terms and conditions not accepted!\nPlease, accept the terms and conditions in your profile!");
    }

    if ($payment_method == null) {
        throw new UserCanNotReserveException("Payment method not set!\nPlease, set a payment method in your profile!");
    }
}

function update_user_policy($conn, $email, $privacy_policy, $terms_and_conditions)
{
    $query = "UPDATE users SET privacy_policy_accepted = $1, terms_and_conditions_accepted = $2 WHERE email = $3";
    $result1 = pg_prepare($conn, "update_user_policy", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "update_user_policy", array($privacy_policy, $terms_and_conditions, $email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

function change_language($conn, $email, $language)
{
    $query = "UPDATE users SET language = $1 WHERE email = $2";
    $result1 = pg_prepare($conn, "change_language", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "change_language", array($language, $email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

?>
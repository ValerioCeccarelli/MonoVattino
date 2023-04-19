<?php

error_reporting(0);

class User {
    public $username;
    public $email;
    public $password;
    public $salt;
}

class NoUserFoundException extends Exception
{
    public function __construct($message) {
        parent::__construct($message, 0, null);
    }
}

# get the user from the database as a User object (password is the hashed one)
# throws an Exception if the query fails
function get_user_by_email($conn, $email) {
    $query = "SELECT username, email, password, salt FROM users WHERE email = $1";
    $result1 = pg_prepare($conn, "get_user_by_email", $query);
    if(!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }
    $result2 = pg_execute($conn, "get_user_by_email", array($email));
    if(!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $first_line = pg_fetch_array($result2, null, PGSQL_ASSOC);
    if(!$first_line) {
        throw new NoUserFoundException("No user found with email: $email!");
    }

    $username = $first_line['username'];
    $email = $first_line['email'];
    $password = $first_line['password'];
    $salt = $first_line['salt'];

    $user = new User();
    $user->username = $username;
    $user->email = $email;
    $user->password = $password;
    $user->salt = $salt;

    return $user;
}

function generate_random_string($length) {
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
    public function __construct() {
        parent::__construct("Email already used", 0, null);
    }
}

# create the user in the database with a password hashed with a salt
# user should be a User object
# the salt is generated randomly, and the password must be the plain one
# throws an EmailAlreadyUsedException if the email is already in the database
# throws an Exception if the query fails
function create_new_user($conn, $user) {
    $query = "INSERT INTO users \n(username, password, salt, email) \nVALUES ($1, $2, $3, $4)";
    $result1 = pg_prepare($conn, "create_new_user", $query);
    if(!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $username = $user->username;
    $password = $user->password;
    $email = $user->email;

    $salt = generate_random_string(10);
    $password_hash = hash('sha256', $password . $salt);
    // echo strlen($password_hash); //64
    
    $result2 = pg_execute($conn, "create_new_user", array($username, $password_hash, $salt, $email));

    if(!$result2) {
        $error = pg_last_error();
        if (strpos($error, 'duplicate key value violates unique constraint') !== false) {
            throw new EmailAlreadyUsedException();
        }

        throw new Exception("Could not execute the query: " . $error);
    }
}

?>
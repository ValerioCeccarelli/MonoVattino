<?php

class InvalidEmailException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidPasswordException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidUsernameException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidNameException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidSurnameException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidDateOfBirthException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidPhoneNumberException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidOwnerException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidCardNumberException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidExpirationDateException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

class InvalidCVVException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

function validate_email($email)
{
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

function validate_password($password)
{
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
        throw new InvalidPasswordException("Password must contain one uppercase letter!");
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new InvalidPasswordException("Password must contain one lowercase letter!");
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new InvalidPasswordException("Password must contain at least one number!");
    }
}

function validate_username($username)
{
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
function verify_password($db_user, $password)
{
    $password_hash = hash('sha256', $password . $db_user->salt);
    return $password_hash === $db_user->password;
}

function validate_name($name)
{
    if (empty($name)) {
        throw new InvalidNameException("Name is required!");
    }
    if (strlen($name) > 50) {
        throw new InvalidNameException("Name must be at most 50 characters long!");
    }
    if (!preg_match('/^[a-zA-Z]+( [a-zA-Z]+)*$/', $name)) {
        throw new InvalidNameException("Name must contain only letters!");
    }
}

function validate_surname($surname)
{
    if (empty($surname)) {
        throw new InvalidSurnameException("Surname is required!");
    }
    if (strlen($surname) > 50) {
        throw new InvalidSurnameException("Surname must be at most 50 characters long!");
    }
    if (!preg_match('/^[a-zA-Z]+$/', $surname)) {
        throw new InvalidSurnameException("Surname must contain only letters!");
    }
}

function validate_date_of_birth($date_of_birth)
{
    if (empty($date_of_birth)) {
        throw new InvalidDateOfBirthException("Date of birth is required!");
    }
    if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date_of_birth)) {
        throw new InvalidDateOfBirthException("Date of birth must be in the format DD-MM-YYYY!");
    }
    $date_of_birth = new DateTime($date_of_birth);
    $now = new DateTime();
    $diff = $date_of_birth->diff($now);
    if ($diff->y < 18) {
        throw new InvalidDateOfBirthException("You must be at least 18 years old to register!");
    }
}

function validate_phone_number($phone_number)
{
    if (empty($phone_number)) {
        throw new InvalidPhoneNumberException("Phone number is required!");
    }
    if (strlen($phone_number) > 20) {
        throw new InvalidPhoneNumberException("Phone number must be at most 20 characters long!");
    }
    if (!preg_match('/^[0-9]+$/', $phone_number)) {
        throw new InvalidPhoneNumberException("Phone number must contain only numbers!");
    }
}

function validate_owner($owner)
{
    if (empty($owner)) {
        throw new InvalidOwnerException("Owner is required!");
    }
    if (strlen($owner) > 50) {
        throw new InvalidOwnerException("Owner must be at most 50 characters long!");
    }
    if (!preg_match('/^[a-zA-Z]+( [a-zA-Z]+)*$/', $owner)) {
        throw new InvalidOwnerException("Owner must contain only letters!");
    }
}

function validate_card_number($card_number)
{
    if (empty($card_number)) {
        throw new InvalidCardNumberException("Card number is required!");
    }
    if (strlen($card_number) < 16) {
        throw new InvalidCardNumberException("Card number must be 16 digits long!");
    }
    if (strlen($card_number) > 16) {
        throw new InvalidCardNumberException("Card number must be 16 digits long!");
    }
    if (!preg_match('/^[0-9]+$/', $card_number)) {
        throw new InvalidCardNumberException("Card number must contain only numbers!");
    }
}

function validate_expiration_date($expiration_date)
{
    if (empty($expiration_date)) {
        throw new InvalidExpirationDateException("Expiry date is required!");
    }

    $year = substr($expiration_date, 0, 4);
    $month = substr($expiration_date, 5, 2);
    if ($month < 1 || $month > 12) {
        throw new InvalidExpirationDateException("Invalid month!");
    }

    $current_date = new DateTime();
    $current_year = $current_date->format('Y');
    $current_month = $current_date->format('m');

    if ($year < $current_year) {
        throw new InvalidExpirationDateException("Card has expired!");
    }
    if ($year == $current_year && $month < $current_month) {
        throw new InvalidExpirationDateException("Card has expired!");
    }

}

function validate_cvv($cvv)
{
    if (empty($cvv)) {
        throw new InvalidCvvException("CVV is required!");
    }
    if (strlen($cvv) != 3) {
        throw new InvalidCvvException("CVV must be 3 digits long!");
    }
    if (!preg_match('/^[0-9]+$/', $cvv)) {
        throw new InvalidCvvException("CVV must contain only numbers!");
    }
}




// class InvalidCreditCardException extends Exception
// {
//     public function __construct($message)
//     {
//         parent::__construct($message, 0, null);
//     }
// }

// function validate_credit_card($credit_card)
// {
//     if (empty($credit_card)) {
//         throw new InvalidCreditCardException("Credit card is required!");
//     }
//     if (strlen($credit_card) < 16) {
//         throw new InvalidCreditCardException("Credit card must be 16 digits long!");
//     }
//     if (strlen($credit_card) > 16) {
//         throw new InvalidCreditCardException("Credit card must be 16 digits long!");
//     }
//     if (!preg_match('/^[0-9]+$/', $credit_card)) {
//         throw new InvalidCreditCardException("Credit card must contain only numbers!");
//     }
// }

?>
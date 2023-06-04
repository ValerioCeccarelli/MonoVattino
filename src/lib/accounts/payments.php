<?php

// Function that create a new payment method in the database and return the id of the payment method
function create_payment_method($conn, $owner, $card_number, $month, $year, $cvv)
{
    $query = "INSERT INTO payment_methods \n(owner, card_number, month, year, cvv) \nVALUES ($1, $2, $3, $4, $5) RETURNING id";

    $result1 = pg_prepare($conn, "create_payment_method", $query);
    if (!$result1) {
        echo pg_last_error();
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "create_payment_method", array($owner, $card_number, $month, $year, $cvv));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $first_line = pg_fetch_array($result2, null, PGSQL_ASSOC);
    if (!$first_line) {
        throw new Exception("Could not fetch the result: " . pg_last_error());
    }

    return $first_line['id'];
}

class PaymentNotFoundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

function get_user_payment_method_id($conn, $email)
{
    $query = "SELECT payment_method FROM users WHERE email = $1";
    $result1 = pg_prepare($conn, "get_user_payment_method", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_user_payment_method", array($email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $first_line = pg_fetch_array($result2, null, PGSQL_ASSOC);
    if (!$first_line) {
        throw new PaymentNotFoundException("Could not fetch the result: " . pg_last_error());
    }

    return $first_line['payment_method'];
}

function update_user_payment_method($conn, $email, $payment_id)
{
    $query = "UPDATE users SET payment_method = $1 WHERE email = $2";
    $result1 = pg_prepare($conn, "update_user_payment_method", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "update_user_payment_method", array($payment_id, $email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

class PaymentMethod
{
    public $id;
    public $owner;
    public $card_number;
    public $month;
    public $year;
    public $cvv;
}

function get_payment_method_by_id($conn, $id)
{
    $query = "SELECT * FROM payment_methods WHERE id = $1";
    $result1 = pg_prepare($conn, "get_payment_metod_by_id", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_payment_metod_by_id", array($id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $first_line = pg_fetch_array($result2, null, PGSQL_ASSOC);
    if (!$first_line) {
        throw new PaymentNotFoundException("Could not fetch the result: " . pg_last_error());
    }

    $payment_method = new PaymentMethod();

    $payment_method->id = $first_line['id'];
    $payment_method->owner = $first_line['owner'];
    $payment_method->card_number = $first_line['card_number'];
    $payment_method->month = $first_line['month'];
    $payment_method->year = $first_line['year'];
    $payment_method->cvv = $first_line['cvv'];

    return $payment_method;
}

function delete_payment_method($conn, $payment_id)
{
    $query = "DELETE FROM payment_methods WHERE id = $1";
    // The foreign key constraint will set to null the payment_method field in the users table
    $result1 = pg_prepare($conn, "delete_payment_method", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "delete_payment_method", array($payment_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

?>
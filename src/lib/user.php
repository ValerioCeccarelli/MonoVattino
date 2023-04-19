<?php

error_reporting(0);

class User {
    public $username;
    public $email;
    public $password;
    public $salt;
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
        throw new Exception("Could not fetch the result: " . pg_last_error());
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

# validate the user by comparing the password with the hashed one (db_user is the user from the database with the hashed password and salt)
function validate_user_password($db_user, $user) {
    $password_hash = hash('sha256', $user->password . $db_user->salt);
    return $password_hash === $db_user->password;
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

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction() {
        echo "A custom function for this type of exception\n";
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



class JwtPayload {
    public $email;
}

function generate_jwt($payload, $secret = "") {
    if($secret == "") {
        $secret = $_ENV['JWT_SECRET'];
    }
    $headers = array('alg'=>'HS256','typ'=>'JWT');
	$headers_encoded = base64url_encode(json_encode($headers));
	
    $payload = array('email'=>$payload->email);
    $payload['exp'] = time() + intval($_ENV['JWT_EXP_TIME']); 
	$payload_encoded = base64url_encode(json_encode($payload));
	
	$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
	$signature_encoded = base64url_encode($signature);
	
	$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
	
	return $jwt;
}

function base64url_encode($str) {
    return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
}

# returns false if the jwt is invalid, otherwise returns the payload as a JwtPayload object
function jwt_decode($jwt, $secret = "") {
    if($secret == "") {
        $secret = $_ENV['JWT_SECRET'];
    }
	// split the jwt
	$tokenParts = explode('.', $jwt);
	$header = base64_decode($tokenParts[0]);
	$payload = base64_decode($tokenParts[1]);
	$signature_provided = $tokenParts[2];

    $obj = json_decode($payload);
	// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
    if(!isset($obj->exp)) {
        return FALSE;
    }
	$expiration = $obj->exp;
	$is_token_expired = ($expiration - time()) < 0;
    if($is_token_expired) {
        return FALSE;
    }

	// build a signature based on the header and payload using the secret
	$base64_url_header = base64url_encode($header);
	$base64_url_payload = base64url_encode($payload);
	$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
	$base64_url_signature = base64url_encode($signature);

	// verify it matches the signature provided in the jwt
	$is_signature_valid = ($base64_url_signature === $signature_provided);
	
	if (!$is_signature_valid) {
        return FALSE;
    }

    $result = new JwtPayload();
    $result->email = $obj->email;
    $result->exp = $obj->exp;

    return $result;
}

?>
<?php 

function get_jwt_expire_time() {
    return time() + intval($_ENV['JWT_EXP_TIME']);
}

class JwtPayload {
    public $username;
    public $email;
}

function generate_jwt($payload) {
    $secret = $_ENV['JWT_SECRET'];

    $headers = array('alg'=>'HS256','typ'=>'JWT');
	$headers_encoded = base64url_encode(json_encode($headers));
	
    $payload = array('email'=>$payload->email, 'username'=>$payload->username);
    $payload['exp'] = get_jwt_expire_time(); 
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
function jwt_decode($jwt) {
    $secret = $_ENV['JWT_SECRET'];

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

    if ($obj->exp < time()) {
        return FALSE;
    }

    $result = new JwtPayload();
    $result->email = $obj->email;
    $result->username = $obj->username;

    return $result;
}

class InvalidJWTException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

function validate_jwt() {
    if (empty($_COOKIE['jwt'])) {
        throw new InvalidJWTException("No JWT provided");
    }
    $jwt_payload = jwt_decode($_COOKIE['jwt']);
    if ($jwt_payload === FALSE) {
        throw new InvalidJWTException("Invalid JWT");
    }
    return $jwt_payload;
}

?>
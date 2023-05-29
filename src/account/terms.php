<?php 
require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/accounts/user.php');
require_once('../lib/http_exceptions/method_not_allowed.php');
require_once('../lib/redirect_to.php');

// TODO: add validatiomn with bad request invece che goto

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;
    $username = $jwt_payload->username;

    $privacy_policy = true;
    $terms_and_conditions = false;

    $privacy_policy_error = null;
    $terms_and_conditions_error = null;

    $redirect_to = get_redirect_to();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $privacy_policy = $_POST['privacy_policy'];
        $terms_and_conditions = $_POST['terms_and_conditions'];
        
        if (!isset($privacy_policy) || $privacy_policy !== 'on') {
            $privacy_policy_error = "You must accept the privacy policy";
            goto end;
        }
        
        if (!isset($terms_and_conditions) || $terms_and_conditions !== 'on') {
            $terms_and_conditions_error = "You must accept the terms and conditions";
            goto end;
        }
        
        $conn = connect_to_database();
        update_user_policy($conn, $email, $privacy_policy, $terms_and_conditions);

        // if ($is_from_terms) {
        //     header('Location: /account/profile.php');
        //     exit;
        // }
        try_redirect();

        header('Location: /account/payment.php');
        exit;
    } else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} catch (InvalidJWTException $e) {
    http_response_code(401);
    echo "401 Unauthorized";
    exit;
} catch (NoUserFoundException $e) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
} catch (MethodNotAllowedException $e) {
    http_response_code(405);
    echo "405 Method Not Allowed";
    exit;
} catch (Exception $e) {
    error_log("ERROR: profile.php: " . $e->getMessage());
    http_response_code(500);
    echo "500 Internal Server Error";
    exit;
}

end:

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms</title>

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
                    <form action="/account/terms.php<?php if($redirect_to) echo "?redirect_to=$redirect_to"; ?>"
                        method="POST">
                        <!-- Title -->
                        <h2>
                            Hi <?php echo $username; ?>!
                        </h2>

                        <h4>
                            By selecting "I accept" you confirm<br>
                            that you are at least 18 years old<br>
                            and that you have read and accepted<br>
                            the following MonoVattino policies:
                        </h4>

                        <!-- Padding -->
                        <div style="height: 30px"></div>

                        <!-- Privacy policy -->
                        <div class="forget">
                            <label for="">
                                <input name="privacy_policy" id="privacy_policy"
                                    <?php if ($privacy_policy) { echo "checked"; } ?> type="checkbox">Privacy Policy
                            </label>
                        </div>

                        <!-- Privacy policy error -->
                        <?php if ($privacy_policy_error) { ?>
                        <h5 class="error-msg" style="padding-top: 0px"><?php echo $privacy_policy_error; ?></h5>
                        <?php } ?>

                        <!-- Padding -->
                        <div style="height: 30px"></div>

                        <!-- Terms and conditions -->
                        <div class="forget">
                            <label for="">
                                <input name="terms_and_conditions" id="terms_and_conditions"
                                    <?php if ($terms_and_conditions) { echo "checked"; } ?> type="checkbox">Terms and
                                Conditions
                            </label>
                        </div>

                        <!-- Terms and conditions error -->
                        <?php if ($terms_and_conditions_error) { ?>
                        <h5 class="error-msg" style="padding-top: 0px"><?php echo $terms_and_conditions_error; ?></h5>
                        <?php } ?>

                        <!-- Padding -->
                        <div style="height: 20px"></div>

                        <!-- Login button -->
                        <button type="submit">Confirm</button>

                        <!-- Padding -->
                        <div style="height: 10px"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
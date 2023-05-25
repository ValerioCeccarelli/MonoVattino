<?php

require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/user.php');
require_once('../lib/http_exceptions/method_not_allowed.php');

// TODO: fai il validation anche sugli altri parametri per evitare il goto

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;
    $username = $jwt_payload->username;

    $owner = $_POST['owner'];
    $card_number = $_POST['card_number'];
    $expiration_date = $_POST['expiration_date'];
    $cvv = $_POST['cvv'];

    $owner_error = null;
    $card_number_error = null;
    $expiration_date_error = null;
    $cvv_error = null;

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // pass
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!isset($owner) || $owner == "") {
            $owner_error = "Missing owner";
            goto end;
        }

        if (!isset($card_number) || $card_number == "") {
            $card_number_error = "Missing card number";
            goto end;
        }

        if (!isset($expiration_date) || $expiration_date == "") {
            $expiration_date_error = "Missing expiration date";
            goto end;
        }

        $month = substr($expiration_date, 0, 2);
        $year = substr($expiration_date, 3, 2);

        if (!isset($cvv) || $cvv == "") {
            $cvv_error = "Missing CVV";
            goto end;
        }

        $conn = connect_to_database();

        try {
            $payment_method = get_user_payment_method($conn, $email);
            if ($payment_method != null) {
                delete_user_payment_method($conn, $email);
            }
        } catch (PaymentNotFoundException $e) {
            // pass
        }

        $payment_id = create_payment_method($conn, $owner, $card_number, $month, $year, $cvv);

        update_user_payment_method($conn, $email, $payment_id);

        header('Location: /');
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
    <title>Payment</title>

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Custom css -->
    <link rel="stylesheet" href="form.css">
</head>

<body>
    <section>
        <div class="form-box">
            <div class="form-padding">
                <div class="form-value">
                    <form action="/account/payment.php" method="POST">
                        <!-- Title -->
                        <h2>
                            Payment
                            <i class="bi bi-scooter"></i>
                        </h2>

                        <!-- Owner -->
                        <div class="inputbox">
                            <ion-icon name="person-outline"></ion-icon>
                            <input id="owner" name="owner" type="text" value="<?php echo $owner; ?>" required>
                            <label id="owner_label" for="username">Card holder</label>
                        </div>

                        <!-- Owner error -->
                        <?php if ($owner_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $owner_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Card number -->
                        <div class="inputbox">
                            <ion-icon name="card-outline"></ion-icon>
                            <input id="card_number" name="card_number" type="text" value="<?php echo $card_number; ?>"
                                required>
                            <label id="card_number_label" for="card_number">Card number</label>
                        </div>

                        <!-- Card number error -->
                        <?php if ($card_number_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $card_number_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Expiration date -->
                        <div class="inputbox">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <input id="expiration_date" name="expiration_date" type="text"
                                value="<?php echo $expiration_date; ?>" required>
                            <label id="expiration_date_label" for="expiration_date">Expiration date</label>
                        </div>

                        <!-- Expiration date error -->
                        <?php if ($expiration_date_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $expiration_date_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- CVV -->
                        <div class="inputbox">
                            <ion-icon name="lock-closed-outline"></ion-icon>
                            <input id="cvv" name="cvv" type="text" value="<?php echo $cvv; ?>" required>
                            <label id="cvv_label" for="cvv">CVV</label>
                        </div>

                        <!-- CVV error -->
                        <?php if ($cvv_error) { ?>
                            <h5 class="error-msg">
                                <?php echo $cvv_error; ?>
                            </h5>
                        <?php } ?>

                        <!-- Padding -->
                        <div style="height: 30px"></div>

                        <!-- Register button -->
                        <button type="submit">Register</button>

                        <!-- Padding -->
                        <div style="height: 10px"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function setLabelControls(input_id, label_id) {
            if ($(input_id).val() != "") {
                $(label_id).css('top', '-5px')
            }
            $(input_id).focus(function () {
                $(label_id).css('top', '-5px')
            });
            $(input_id).blur(function () {
                if ($(input_id).val() == "") {
                    $(label_id).css('top', '50%')
                }
            });
        }

        setLabelControls("#owner", "#owner_label");
        setLabelControls("#card_number", "#card_number_label");
        setLabelControls("#expiration_date", "#expiration_date_label");
        setLabelControls("#cvv", "#cvv_label");
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
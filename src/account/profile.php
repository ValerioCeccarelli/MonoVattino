<?php 
require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/user.php');

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    $conn = connect_to_database();
    $user = get_user_by_email($conn, $email);

    // echo json_encode($user);
    // echo "<br>";
    
    try {
        $payment_method = get_payment_metod_by_id($conn, $user->payment_method);
    } catch (PaymentNotFoundException $e) {
        $payment_method = null;
    }

    // echo json_encode($payment_method);

    $username = $user->username;
} catch (InvalidJWTException $e) {
    // http_response_code(401);
    // echo "401 Unauthorized";
    header("Location: /account/login.php");
    exit;
} catch (NoUserFoundException $e) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
} catch (PaymentNotFoundException  $e) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
} catch (Exception $e) {
    error_log("ERROR: profile.php: " . $e->getMessage());
    http_response_code(500);
    echo "500 Internal Server Error";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- Bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- Bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');


    .profile-img {
        width: 30vw !important;
        height: auto;
    }

    .form-box {
        position: relative;
        width: auto;
        height: auto;

        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;

        background-color: rgba(100, 100, 100, 0.8);
        /* display: flex; */
        justify-content: center;
        align-items: center;
    }

    .form-padding {
        padding: 0px 20px;
        padding-top: 15px;
    }

    .form-box h2 {
        font-size: 2em;
        color: #fff;
        text-align: center;
    }

    .inputbox {
        position: relative;
        margin-top: 30px;
        width: auto;
        border-bottom: 2px solid #fff;
    }

    .inputbox label {
        position: absolute;
        top: -5px;
        left: 5px;
        transform: translateY(-50%);
        color: #fff;
        font-size: 1em;
        pointer-events: none;
        transition: .5s;
    }

    .inputbox input {
        width: 85.4%;
        height: 50px;
        background: transparent;
        border: none;
        outline: none;
        font-size: 1em;

        padding-left: 5px;
        color: #fff;

        left: -15px;
        position: relative;
    }

    .inputbox ion-icon {
        position: absolute;
        right: 8px;
        color: #fff;
        font-size: 1.2em;
        top: 20px;
    }

    .forget {
        margin: -15px 0 15px;
        font-size: .9em;
        color: #fff;
        display: flex;
        justify-content: space-between;
    }

    .forget label input {
        margin-right: 3px;

    }

    .forget label a {
        color: #fff;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="container">
                    <!-- User name title -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1><?php echo $user->username; ?> Profile</h1>
                        </div>
                    </div>

                    <!-- Gravatar icon -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <img src="http://www.gravatar.com/avatar/<?php echo md5($user->email); ?>?d=identicon"
                                alt="Gravatar" class="rounded-circle profile-img" style="width: 120px !important">
                        </div>
                    </div>

                    <!-- User info -->
                    <div class="form-box">
                        <div class="form-padding">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-sm-6 text-center">
                                        <!-- Title -->
                                        <h2>Info</h2>

                                        <!-- Name input -->
                                        <div class="inputbox">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <input id="name" name="name" type="text" value="<?php echo $user->name; ?>"
                                                required>
                                            <label id="name_label" for="name">Name</label>
                                        </div>

                                        <!-- Surname input -->
                                        <div class="inputbox">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <input id="surname" name="surname" type="text"
                                                value="<?php echo $user->surname; ?>" required>
                                            <label id="surname_label" for="surname">Surname</label>
                                        </div>

                                        <!-- Email input -->
                                        <div class="inputbox">
                                            <ion-icon name="mail-outline"></ion-icon>
                                            <input id="email" name="email" type="email"
                                                value="<?php echo $user->email; ?>" disable>
                                            <label id="email_label" for="email">Email</label>
                                        </div>

                                        <!-- Username input -->
                                        <div class="inputbox">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <input id="username" name="username" type="text"
                                                value="<?php echo $user->username; ?>" disable>
                                            <label id="username_label" for="username">Username</label>
                                        </div>

                                        <!-- Padding -->
                                        <div style="height: 30px"></div>

                                    </div>
                                    <div class="col-12 col-sm-6 text-center">
                                        <!-- Title -->
                                        <h2>Payment</h2>

                                        <?php if ($payment_method != null) { ?>
                                        <!-- Owner -->
                                        <div class="inputbox">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <input id="owner" name="owner" type="text"
                                                value="<?php echo $payment_method->owner; ?>" required>
                                            <label id="owner_label" for="username">Card holder</label>
                                        </div>

                                        <!-- Card number -->
                                        <div class="inputbox">
                                            <ion-icon name="card-outline"></ion-icon>
                                            <input id="card_number" name="card_number" type="text"
                                                value="<?php echo $payment_method->card_number; ?>" required>
                                            <label id="card_number_label" for="card_number">Card number</label>
                                        </div>

                                        <!-- Expiration date -->
                                        <div class="inputbox">
                                            <ion-icon name="calendar-outline"></ion-icon>
                                            <input id="expiration_date" name="expiration_date" type="text"
                                                value="<?php echo $payment_method->month; ?>/<?php echo $payment_method->year; ?>"
                                                required>
                                            <label id="expiration_date_label" for="expiration_date">Expiration
                                                date</label>
                                        </div>

                                        <!-- CVV -->
                                        <div class="inputbox">
                                            <ion-icon name="lock-closed-outline"></ion-icon>
                                            <input id="cvv" name="cvv" type="text"
                                                value="<?php echo $payment_method->cvv; ?>" required>
                                            <label id="cvv_label" for="cvv">CVV</label>
                                        </div>

                                        <!-- Padding -->
                                        <div style="height: 30px"></div>

                                        <?php } else { ?>

                                        <p>No payment method</p>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6" style="background-color: red;">ppp</div>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>
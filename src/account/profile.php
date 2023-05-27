<?php 
require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/user.php');
require_once('../lib/reservations.php');
require_once('../lib/trips.php');

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    $conn = connect_to_database();
    $user = get_user_by_email($conn, $email);
    
    try {
        $payment_method = get_payment_metod_by_id($conn, $user->payment_method);
    } catch (PaymentNotFoundException $e) {
        $payment_method = null;
    }

    if ($user->privacy_policy_accepted && $user->terms_and_conditions_accepted) {
        $policy_accepted = true;
    } else {
        $policy_accepted = false;
    }

    $reservations = get_user_reservation($conn, $email);
    $trips = get_user_trips($conn, $email);

    $username = $user->username;
} catch (InvalidJWTException $e) {
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
    <div class="container shadow min-vh-100">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="container">
                    <!-- Padding -->
                    <div style="height: 30px"></div>

                    <!-- User name title -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1><?php echo $user->username; ?> Profile</h1>
                        </div>
                    </div>

                    <!-- Padding -->
                    <div style="height: 10px"></div>

                    <!-- Gravatar icon -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <img src="http://www.gravatar.com/avatar/<?php echo md5($user->email); ?>?d=identicon"
                                alt="Gravatar" class="rounded-circle profile-img" style="width: 120px !important">
                        </div>
                    </div>

                    <!-- Padding -->
                    <div style="height: 20px"></div>

                    <!-- User info -->
                    <div class="form-box">
                        <div class="form-padding">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-sm-6 text-center">
                                        <!-- Title -->
                                        <h2>
                                            Info
                                            <?php if (!$policy_accepted) { ?>
                                            <i class="bi bi-exclamation-triangle-fill" data-toggle="tooltip"
                                                data-placement="top" style="color: yellow;"
                                                title="You need to firm the terms and conditions"></i>
                                            <?php } ?>
                                        </h2>

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

                                        <?php if (!$policy_accepted) { ?>
                                        <!-- Privacy policy link -->
                                        <a href="/account/terms.php?f=p" class="btn btn-danger">Firm terms and
                                            conditions</a>

                                        <!-- Padding -->
                                        <div style="height: 30px"></div>
                                        <?php } ?>

                                    </div>
                                    <div class="col-12 col-sm-6 text-center">
                                        <!-- Title -->
                                        <h2>Payment
                                            <?php if ($payment_method == null) { ?>
                                            <i class="bi bi-exclamation-triangle-fill" data-toggle="tooltip"
                                                data-placement="top" style="color: yellow;"
                                                title="You need to add a payment method"></i>
                                            <?php } ?>
                                        </h2>

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
                                        <div style="height: 20px"></div>

                                        <!-- Change payment method -->
                                        <a href="/account/payment.php?f=p" class="btn btn-primary">Change payment
                                            method</a>

                                        <!-- Padding -->
                                        <div style="height: 30px"></div>

                                        <?php } else { ?>

                                        <!-- No payment method -->
                                        <a href="/account/payment.php?f=p" class="btn btn-danger">Add new method</a>

                                        <!-- Padding -->
                                        <div style="height: 30px"></div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="container">
                    <!-- Padding -->
                    <div style="height: 30px"></div>

                    <!-- Current trips title -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1>Current trips</h1>
                        </div>
                    </div>

                    <!-- Current trips -->
                    <?php if (! $reservations) { ?>
                    <div class="row">
                        <div class="col-12">
                            <p>No current trips</p>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Company</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $reservation) { 
                                        $start_time = strtotime($reservation->date);
                                        $end_time = strtotime("now");   
                                    
                                        $travel_time = $end_time - $start_time;

                                        $price = $reservation->fixed_cost + $reservation->cost_per_minute * $travel_time  / 60;
                                        $price = round($price, 2);

                                        $travel_hours = floor($travel_time / 3600);
                                        $travel_minutes = floor(($travel_time / 60) % 60);
                                        $travel_time = $travel_hours . "h " . $travel_minutes . "m";

                                        $date = date("d/m/Y", $start_time);
                                        ?>
                                    <tr>

                                        <td><i class="bi bi-scooter"
                                                style="color: #<?php echo $reservation->company_color; ?>"></i></td>
                                        <td><?php echo $reservation->company_name; ?></td>
                                        <td><?php echo $date; ?></td>
                                        <td><?php echo $travel_time; ?></td>
                                        <td><?php echo $price; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- Old trips title -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1>Old trips</h1>
                        </div>
                    </div>

                    <!-- Old trips -->
                    <?php if (! $trips) { ?>
                    <div class="row">
                        <div class="col-12">
                            <p>No old trips</p>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Company</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trips as $trip) {  
                                    
                                        $travel_time = $trip->trip_time;

                                        $price = $trip->fixed_cost + $trip->cost_per_minute * $travel_time  / 60;
                                        $price = round($price, 2);

                                        $travel_hours = floor($travel_time / 3600);
                                        $travel_minutes = floor(($travel_time / 60) % 60);
                                        $travel_time = $travel_hours . "h " . $travel_minutes . "m";

                                        $start_time = strtotime($trip->date);
                                        $date = date("d/m/Y", $start_time);
                                        ?>
                                    <tr>
                                        <td><i class="bi bi-scooter"
                                                style="color: #<?php echo $trip->company_color; ?>"></i></td>
                                        <td><?php echo $trip->company_name; ?></td>
                                        <td><?php echo $date; ?></td>
                                        <td><?php echo $travel_time; ?></td>
                                        <td><?php echo $price; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-dark shadow ciao" id="btnSwitch">Toggle Mode</button>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
    document.getElementById('btnSwitch').addEventListener('click', () => {
        if (document.documentElement.getAttribute('data-bs-theme') == 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'light')
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'dark')
        }
    });
    </script>

</body>

</html>
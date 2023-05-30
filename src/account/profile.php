<?php
require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/accounts/user.php');
require_once('../lib/scooters/reservations.php');
require_once('../lib/scooters/trips.php');
require_once('../lib/accounts/payments.php');
require_once('../lib/accounts/themes.php');
require_once('../lib/http_exceptions/method_not_allowed.php');

session_start();

$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_SESSION['user_email'])) {
            header('Location: /account/login.php?redirect_to=profile');
            exit;
        }

        $email = $_SESSION['user_email'];

        $conn = connect_to_database();
        $user = get_user_by_email($conn, $email);

        $payment_method = null;
        if ($user->payment_method) {
            $payment_method = $user->payment_method;
        }

        if ($user->privacy_policy_accepted && $user->terms_and_conditions_accepted) {
            $policy_accepted = true;
        } else {
            $policy_accepted = false;
        }

        // $map_id = theme_to_mapid($user->map_theme);
        $html_theme = $user->html_theme;
        $map_theme = $user->map_theme;

        $reservations = get_user_reservation($conn, $email);
        $trips = get_user_trips($conn, $email);

        $username = $user->username;
        $language = $user->language;
    } else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} catch (NoUserFoundException $e) {
    http_response_code(404);
    echo "404 Not Found (User)";
    exit;
} catch (PaymentNotFoundException $e) {
    http_response_code(404);
    echo "404 Not Found (Payment)";
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
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo $html_theme; ?>">

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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha384-...=" crossorigin="anonymous" />

    <!-- Flag icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        [data-bs-theme="dark"] {
            --theme: #FF4500;
        }

        :root {
            --theme: #FF4500;
        }

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

        .card {
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 6px 10px rgba(0, 0, 0, .08), 0 0 6px rgba(0, 0, 0, .05);
            transition: .3s transform cubic-bezier(.155, 1.105, .295, 1.12), .3s box-shadow, .3s -webkit-transform cubic-bezier(.155, 1.105, .295, 1.12);
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .12), 0 4px 8px rgba(0, 0, 0, .06);
        }

        .card-img-top {
            width: 150px;
            height: auto;
            object-fit: cover;
        }

        .my-fi {}
    </style>
</head>

<body>
    <!-- NavBar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow px-4">
        <div class="container-fluid">
            <i class="bi bi-scooter navbar-brand" style="font-size: 35px;"></i>
            <a class="navbar-brand" href="/index.php"><strong>MonoVattino</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Map</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><strong>Profile</strong></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">About us</a>
                    </li>
                    <?php if ($is_admin) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/issues/show_issue.php">Issues</a>
                        </li>
                    <?php } ?>
                </ul>

                <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span id="selectedLanguageFlag" class="fi fi-gb my-fi" style="padding-right: 5px;"></span>
                            <span id="selectedLanguageText" style="font-size: 1rem;">English</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=index&lang=en"
                                    id="langEN">
                                    <span class="fi fi-gb my-fi" style="padding-right: 5px;"></span>
                                    <span style="font-size: 1rem;">English</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=index&lang=it"
                                    id="langIT">
                                    <span class="fi fi-it"></span>
                                    <span style="font-size: 1rem;">Italian</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=index&lang=de"
                                    id="langDE">
                                    <span class="fi fi-de"></span>
                                    <span style="font-size: 1rem;">German</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=index&lang=es"
                                    id="langES">
                                    <span class="fi fi-es"></span>
                                    <span style="font-size: 1rem;">Spanish</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/account/logout.php">Logout</a>
                    </li>

                </ul>

                <!-- Theme mode -->
                <ul class="navbar-nav ml-auto mt-2">
                    <li>
                        <a id="btnSwitch" @click="toggleTheme">
                            <a id="nav_dark" class="btn btn-primary" onclick="change_theme('dark')" style="background-color:var(--theme); background:none; padding:0px; border:none; 
                                display:<?php echo $html_theme === "light" ? "block" : "none" ?>;">
                                <ion-icon class="p-3" name="moon-outline" style="font-size: 20px; color:gold" />
                            </a>
                            <a id="nav_light" class="btn btn-primary" onclick="change_theme('light')" style="background-color:var(--theme); background:none; padding:0px; border:none;
                                display:<?php echo $html_theme === "dark" ? "block" : "none" ?>;">
                                <ion-icon class="p-3" name="sunny-outline" style="font-size: 20px; color:gold;" />
                            </a>
                            <script>
                                function change_theme(theme) {
                                    html = document.getElementsByTagName('html')[0];
                                    if (theme == 'dark') {
                                        html.setAttribute('data-bs-theme', 'dark');
                                        document.getElementById('nav_dark').style.display = 'none';
                                        document.getElementById('nav_light').style.display = 'block';
                                    } else if (theme == 'light') {
                                        html.setAttribute('data-bs-theme', 'light');
                                        document.getElementById('nav_dark').style.display = 'block';
                                        document.getElementById('nav_light').style.display = 'none';
                                    }
                                    $.ajax({
                                        type: "GET",
                                        url: "/account/change_theme.php",
                                        data: {
                                            theme: theme
                                        }
                                    });
                                }
                            </script>

                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container shadow" style="min-height: calc(100vh - 78.5px)">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="container">
                    <!-- Padding -->
                    <div style="height: 30px"></div>

                    <!-- User name title -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1>
                                <?php echo $user->username; ?> Profile
                            </h1>
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
                                                disabled>
                                            <label id="name_label" for="name">Name</label>
                                        </div>

                                        <!-- Surname input -->
                                        <div class="inputbox">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <input id="surname" name="surname" type="text"
                                                value="<?php echo $user->surname; ?>" disabled>
                                            <label id="surname_label" for="surname">Surname</label>
                                        </div>

                                        <!-- Email input -->
                                        <div class="inputbox">
                                            <ion-icon name="mail-outline"></ion-icon>
                                            <input id="email" name="email" type="email"
                                                value="<?php echo $user->email; ?>" disabled>
                                            <label id="email_label" for="email">Email</label>
                                        </div>

                                        <!-- Username input -->
                                        <div class="inputbox">
                                            <ion-icon name="person-outline"></ion-icon>
                                            <input id="username" name="username" type="text"
                                                value="<?php echo $user->username; ?>" disabled>
                                            <label id="username_label" for="username">Username</label>
                                        </div>

                                        <!-- Padding -->
                                        <div style="height: 30px"></div>

                                        <?php if (!$policy_accepted) { ?>
                                            <!-- Privacy policy link -->
                                            <a href="/account/terms.php?redirect_to=profile" class="btn btn-danger">Firm
                                                terms and
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
                                                    value="<?php echo $payment_method->owner; ?>" disabled>
                                                <label id="owner_label" for="username">Card holder</label>
                                            </div>
                                            <!-- Card number -->
                                            <div class="inputbox">
                                                <ion-icon name="card-outline"></ion-icon>
                                                <input id="card_number" name="card_number" type="text"
                                                    value="•••• •••• •••• <?php echo substr($payment_method->card_number, 12, 4); ?>"
                                                    disabled>
                                                <label id="card_number_label" for="card_number">Card number</label>
                                            </div>
                                            <!-- Expiration date -->
                                            <div class="inputbox">
                                                <ion-icon name="calendar-outline"></ion-icon>
                                                <input id="expiration_date" name="expiration_date" type="text"
                                                    value="<?php echo $payment_method->month; ?>/<?php echo $payment_method->year; ?>"
                                                    disabled>
                                                <label id="expiration_date_label" for="expiration_date">Expiration
                                                    date</label>
                                            </div>
                                            <!-- CVV -->
                                            <div class="inputbox">
                                                <ion-icon name="lock-closed-outline"></ion-icon>
                                                <input id="cvv" name="cvv" type="text"
                                                    value="<?php echo $payment_method->cvv; ?>" disabled>
                                                <label id="cvv_label" for="cvv">CVV</label>
                                            </div>
                                            <!-- Padding -->
                                            <div style="height: 20px"></div>
                                            <!-- Change payment method -->
                                            <a href="/account/payment.php?redirect_to=profile" class="btn btn-primary"
                                                style="background-color:var(--theme); border-color: var(--theme)">Change
                                                payment
                                                method</a>
                                            <!-- Padding -->
                                            <div style="height: 30px"></div>

                                        <?php } else { ?>

                                            <!-- No payment method -->
                                            <a href="/account/payment.php?redirect_to=profile" class="btn btn-danger">Add
                                                new method</a>

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
                    <?php if (!$reservations) { ?>
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

                                            $price = $reservation->fixed_cost + $reservation->cost_per_minute * $travel_time / 60;
                                            $price = round($price, 2);

                                            $travel_hours = floor($travel_time / 3600);
                                            $travel_minutes = floor(($travel_time / 60) % 60);
                                            $travel_time = $travel_hours . "h " . $travel_minutes . "m";

                                            $date = date("d/m/Y", $start_time);
                                            ?>
                                            <tr>

                                                <td><i class="bi bi-scooter"
                                                        style="color: #<?php echo $reservation->company_color; ?>"></i></td>
                                                <td>
                                                    <?php echo $reservation->company_name; ?>
                                                </td>
                                                <td>
                                                    <?php echo $date; ?>
                                                </td>
                                                <td>
                                                    <?php echo $travel_time; ?>
                                                </td>
                                                <td>
                                                    <?php echo $price; ?>
                                                </td>
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
                    <?php if (!$trips) { ?>
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

                                            $price = $trip->fixed_cost + $trip->cost_per_minute * $travel_time / 60;
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
                                                <td>
                                                    <?php echo $trip->company_name; ?>
                                                </td>
                                                <td>
                                                    <?php echo $date; ?>
                                                </td>
                                                <td>
                                                    <?php echo $travel_time; ?>
                                                </td>
                                                <td>
                                                    <?php echo $price; ?>
                                                </td>
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

        <!-- Padding -->
        <div style="height: 30px"></div>

        <p>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                aria-expanded="false" aria-controls="collapseExample"
                style="background-color: var(--theme); border-color: var(--theme) ">
                Map theme
            </button>
        </p>

        <style>
            a {
                text-decoration: none;
                color: inherit;
            }

            .selected-card {
                background-color: #f8f9fa;
                /* Set the desired background color */
                border: 2.1px solid #007bff;
                /* Set the desired border color */
            }

            .card-body {
                background-color: #575758;
                color: #fdfdfd;
            }

            .my-card {
                border-radius: var(--bs-card-inner-border-radius);
                background-color: #575758;
                color: #fdfdfd;
            }
        </style>

        <div class="collapse container text-center" id="collapseExample">
            <div class="row">

                <!-- <script>
                
                function change_map_theme(theme) {
                    $.ajax({
                        type: "GET",
                        url: "/account/change_map.php",
                        data: {
                            map: theme
                        }
                    });
                }
                </script> -->

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=default&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'default') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/default.png" class="card-img-top" alt="default">
                            <div class="card card-body">
                                <h5 class="card-title">Default
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=dark&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'dark') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/dark.png" class="card-img-top" alt="dark">
                            <div class="card card-body">
                                <h5 class="card-title">Dark
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=light&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'light') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/light.png" class="card-img-top" alt="light">
                            <div class="card card-body">
                                <h5 class="card-title">Light
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=grey&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'grey') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/grey.png" class="card-img-top" alt="grey">
                            <div class="card card-body">
                                <h5 class="card-title">Grey
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=classic&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'classic') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/classic.png" class="card-img-top" alt="classic">
                            <div class="card card-body">
                                <h5 class="card-title">Classic
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=night&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'night') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/night.png" class="card-img-top" alt="night">
                            <div class="card card-body">
                                <h5 class="card-title">Night
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mb-3 mb-sm-0">
                    <div style="height: 21px;"></div>
                    <a href="/account/change_map.php?map=atlas&redirect_to=profile">
                        <div class="card my-card <?php echo ($map_theme === 'atlas') ? 'selected-card' : ''; ?>"
                            style="width: max-content;">
                            <img src="/img/thumbnails/atlas.png" class="card-img-top" alt="atlas">
                            <div class="card card-body">
                                <h5 class="card-title">Atlas
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="text-center text-lg-start footer-light text-muted">
        <!-- Section: Social media -->
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
            <!-- Left -->
            <div class="me-5 d-none d-lg-block">
                <span>Get connected with us on social networks:</span>
            </div>
            <!-- Left -->

            <!-- Right -->
            <div>
                <a href class="me-4 text-reset" style="text-decoration:none;">
                    <i class=" fab fa-facebook"></i>
                </a>
                <a href class="me-4 text-reset" style="text-decoration:none;">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href class="me-4 text-reset" style="text-decoration:none;">
                    <i class="fab fa-google"></i>
                </a>
                <a href class="me-4 text-reset" style="text-decoration:none;">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href class="me-4 text-reset" style="text-decoration:none;">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href class="me-4 text-reset" style="text-decoration:none;">
                    <i class="fab fa-github"></i>
                </a>
            </div>
            <!-- Right -->
        </section>
        <!-- Section: Social media -->

        <!-- Section: Links  -->
        <section class="">
            <div class="container text-center text-md-start mt-5">
                <!-- Grid row -->
                <div class="row mt-3">
                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                        <!-- Content -->
                        <h6 class="text-uppercase fw-bold mb-4">
                            <i class="bi bi-scooter me-2"></i>MONOVATTINO
                        </h6>
                        <p>
                            <strong>M</strong>ono<strong>V</strong>attino: Modern e-scooter sharing for urban mobility.
                            Ride, unlock, and explore with
                            ease. Join us in shaping a greener future of transportation.
                        </p>
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                        <!-- Links -->
                        <h6 class="text-uppercase fw-bold mb-4">
                            BUILT WITH
                        </h6>
                        <p>
                            <a href="https://developer.mozilla.org/en-US/docs/Web/HTML" class="text-reset">HTML</a>
                        </p>
                        <p>
                            <a href="https://www.php.net/docs.php" class="text-reset">PHP</a>
                        </p>
                        <p>
                            <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript"
                                class="text-reset">JavaScript</a>
                        </p>
                        <p>
                            <a href="https://getbootstrap.com/" class="text-reset">Bootstrap</a>
                        </p>
                        <p>
                            <a href="https://api.jquery.com/" class="text-reset">jQuery</a>
                        </p>
                        <p>
                            <a href="https://www.postgresql.org/docs/" class="text-reset">PostgreSQL</a>
                        </p>
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <!-- Links -->
                        <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                        <p>
                            <a href="https://goo.gl/maps/BzPKV68sjswbXoFB7"><i class="fas fa-home me-3"></i> Piazzale
                                della
                                Stazione Ponte Mammolo, Rome 00156, IT</a>
                        </p>
                        <p>
                            <i class="fas fa-envelope me-3"></i>
                            info@monovattino.com
                        </p>
                        <p><i class="fas fa-phone me-3"></i> + 00 13 04 2023</p>
                        <p><i class="fas fa-print me-3"></i> + 00 13 04 2023</p>
                    </div>
                    <!-- Grid column -->
                </div>
                <!-- Grid row -->
            </div>
        </section>
        <!-- Section: Links  -->

        <!-- Copyright -->
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2023 Copyright:
            <a class="text-reset fw-bold" href="/index.php">MonoVattino</a>
        </div>
        <!-- Copyright -->
    </footer>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        // Get the current language from the server
        var currentLanguage = "<?php echo $language; ?>";

        var selectedLanguageFlag = document.getElementById("selectedLanguageFlag");
        var selectedLanguageText = document.getElementById("selectedLanguageText");

        // Update the toggle element to show the current language
        var toggleElement = document.getElementById("lang" + currentLanguage.toUpperCase());
        selectedLanguageFlag.classList = toggleElement.querySelector("span.fi").classList;
        selectedLanguageText.textContent = toggleElement.querySelector("span").textContent;
        toggleElement.classList.add("active");
        toggleElement.setAttribute("aria-current", "true");
        toggleElement.querySelector("span.fi").classList.add("my-fi-selected");
    </script>

</body>

</html>
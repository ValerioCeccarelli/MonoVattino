<?php

require_once('lib/jwt.php');

$is_user_logged = false;
$jwt_payload = null;
$username = null;

try {
    $jwt_payload = validate_jwt();
    $is_user_logged = true;
    $username = $jwt_payload->username;
} catch (InvalidJWTException $e) {
    $is_user_logged = false;
    $username = null;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <!-- favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon" />

    <title>MonoVattino</title>

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

    <!-- Custom js -->
    <script src="index.js"></script>

    <!-- Custom css -->
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <!-- NavBar -->
    <nav class="navbar navbar-expand bg-body-tertiary my-nav">
        <div class="container-fluid">
            <!-- Scooter img -->
            <i class="bi bi-scooter navbar-brand my-nav-icon"></i>

            <div class="navbar-collapse " id="navbarSupportedContent">
                <!-- Title -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <span class="nav-link active my-pad" aria-current="page" href="#">
                            <h3 style="margin-bottom: 0%;">MonoVattino</h3>
                        </span>
                    </li>
                </ul>

                <!-- Person img -->
                <a style="color: black" <?php if ($is_user_logged) { ?> href="account/profile.php" <?php } else { ?>
                    href="account/login.php" <?php } ?>>
                    <i class="bi bi-person-circle my-nav-icon"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Map -->
    <div class="map" id="map"></div>

    <!-- Bottom drawer-->
    <div class="offcanvas offcanvas-bottom my-offcanvas" tabindex="-1" id="offcanvasBottom"
        aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-header my-offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasBottomLabel">Info Scooter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body medium">
            <div class="row">
                <div class="col">
                    <p>
                        <i class="bi bi-battery-charging"></i>
                        Battery: <span id="scooter_battery">69</span>%
                    </p>
                    <p>
                        <i class="bi bi-building"></i>
                        Company: <span id="scooter_company">mirko_scuscu</span>
                    </p>
                </div>
                <div class="col">
                    <p>
                        <i class="bi bi-currency-euro"></i>
                        Base cost: <span id="scooter_fixed_cost">1</span>€
                    </p>
                    <p>
                        <i class="bi bi-currency-euro"></i>
                        Per minute: <span id="scooter_cost_per_minute">0.36</span>€
                    </p>
                </div>
                <div class="col d-none d-md-block">
                    <p>
                        <i class="bi bi-geo-alt"></i>
                        Latitude: <span id="scooter_latitude">41.03456</span>
                    </p>
                    <p>
                        <i class="bi bi-geo-alt"></i>
                        Longitude: <span id="scooter_longitude">12.456</span>
                    </p>
                </div>
            </div>

            <button class="btn btn-primary" id="offcanvas_button" type="button">Reserve</button>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="success_modal" tabindex="-1" role="dialog" aria-labelledby="success_modal_title"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="success_modal_title">Scooter reserved!</h5>
                </div>
                <div class="modal-body" id="success_modal_mody">
                    Ok!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="error_modal" tabindex="-1" aria-labelledby="error_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <i class="bi bi-exclamation-triangle-fill pe-4 fs-3" style="color: red;"></i>
                    <h1 class="modal-title fs-5" id="error_modal_title">Error!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="error_modal_body">
                    Something went wrong! Please refresh the page and try again.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php

require_once('lib/jwt.php');
require_once('lib/accounts/user.php');
require_once('lib/accounts/themes.php');
require_once('lib/database.php');

$is_user_logged = false;
$jwt_payload = null;
$username = null;

$map_theme = 'default';
$html_theme = 'light';
$is_admin = false;


try {
    $jwt_payload = validate_jwt();
    $is_user_logged = true;
    $username = $jwt_payload->username;

    $conn = connect_to_database();
    $user = get_user_by_email($conn, $jwt_payload->email);
    $map_theme = $user->map_theme;

    $html_theme = $user->html_theme;
    $is_admin = $user->is_admin;
} catch (InvalidJWTException $e) {
    $is_user_logged = false;
    $username = null;
} catch (Exception $e) {
    error_log("ERROR: index.php: " . $e->getMessage());

    $is_user_logged = false;
    $username = null;
}

$map_id = theme_to_mapid($map_theme);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo $html_theme; ?>">

<head>
    <script>
    var map_id = "<?php echo $map_id; ?>";
    </script>

    <meta charset="UTF-8">

    <!-- favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />

    <title>MonoVattino</title>

    <!-- Bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> -->

    <!-- Bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script> -->

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Custom js -->
    <script src="index.js"></script>

    <!-- Custom css -->
    <link rel="stylesheet" href="/index.css">
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
                        <a class="nav-link" href="#"><strong>Map</strong></a>
                    </li>
                    <?php if ($is_user_logged) { ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/account/profile.php">Profile</a>
                    </li>
                    <?php } ?>
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
                    <li class="nav-item">
                        <?php if ($is_user_logged) { ?>
                        <a class="nav-link" href="/account/logout.php">Logout</a>
                        <?php } else { ?>
                        <a class="nav-link" href="/account/login.php">Login</a>
                        <?php } ?>
                    </li>
                    <?php if (!$is_user_logged) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/account/register.php">Register</a>
                    </li>
                    <?php } ?>
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
                                <ion-icon class="p-3" name="sunny-outline" style="font-size: 20px; color:gold" />
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
            <button class="btn btn-warning" id="offcanvas_report_button" type="button">Report</button>
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
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>

</html>
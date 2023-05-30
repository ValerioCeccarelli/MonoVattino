<?php

session_start();

$is_user_logged = isset($_SESSION['user_email']);
$html_theme = isset($_SESSION['html_theme']) ? $_SESSION['html_theme'] : 'light';
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

?>

<!DOCTYPE html>
<html data-bs-theme="<?php echo $html_theme; ?>">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>About us | MV</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

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

    <!-- Flag icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />

</head>

<body>
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
                    <?php if ($is_user_logged) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/account/profile.php">Profile</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#"> <strong>About us</strong></a>
                    </li>
                    <?php if ($is_admin) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/issues/show_issue.php">Issues</a>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
                    <!-- Language selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span id="selectedLanguageFlag" class="fi fi-gb my-fi" style="padding-right: 5px;"></span>
                            <span id="selectedLanguageText" style="font-size: 1rem;">English</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=about&lang=en"
                                    id="langEN">
                                    <span class="fi fi-gb my-fi" style="padding-right: 5px;"></span>
                                    <span style="font-size: 1rem;">English</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=about&lang=it"
                                    id="langIT">
                                    <span class="fi fi-it"></span>
                                    <span style="font-size: 1rem;">Italiano</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=about&lang=de"
                                    id="langDE">
                                    <span class="fi fi-de"></span>
                                    <span style="font-size: 1rem;">Deutsch</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=about&lang=es"
                                    id="langES">
                                    <span class="fi fi-es"></span>
                                    <span style="font-size: 1rem;">Español</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php if ($is_user_logged) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/account/logout.php">Logout</a>
                        </li>

                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/account/login.php">Login</a>
                        </li>
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


    <div class="container text-center" style="font-size = 30em">
        <div class="row">
            <div class="col-12 col-md-6">
                <div style="height: 31px;"></div>
                <h2>
                    <font size="21"> <strong>M</strong></font>ission
                </h2>
                <p style="font-size: 20px;">
                    <strong>M</strong>ono<strong>V</strong>attino provides cities with the world’s safest and most
                    advanced
                    small vehicle technologies.
                </p>
            </div>



            <div class="col-12 col-md-6">
                <div style="height: 31px;"></div>
                <h2>
                    <font size="21"><strong>V</strong></font>ision
                </h2>
                <p style="font-size: 20px;">
                    We en<strong>v</strong>ision a world in which all cities have safe, green, free-flowing streets, and
                    all
                    city residents
                    have easy access to reliable transportation, without having to download each one of the
                    <strong>m</strong>obility apps.
                </p>
            </div>
        </div>
        <div class="row">
            <div style="height: 31px;"></div>

            <div class="col-12 col-md-6">
                <h2>
                    <font size="21"><strong>V</strong></font>alues
                </h2>
                <div style="height: 21px;"></div>
                <p class="text-start" style="font-size: 20px;"> <strong>Safety</strong> – we keep riders and pedestrians
                    safe through rigorous
                    engineering and
                    ingenuity
                </p>

                <p class="text-start" style="font-size: 20px;">
                    <strong>Sustainability</strong> – we get people out of cars and into right-sized transportation.
                </p>
                <p class="text-start" style="font-size: 20px;">
                    <strong>Partnership</strong> – we partner with cities to meet their unique needs
                </p>
                <p class="text-start" style="font-size: 20px;">
                    <strong>Innovation</strong> – we create cutting-edge technology to enable the safest and most
                    efficient
                    operations
                </p>
                <p class="text-start" style="font-size: 20px;">
                    <strong>Access</strong> – we are expanding access to sustainable and lighter electric vehicles
                </p>
            </div>

            <div class="col-12 col-md-6">
                <img src="img/about1.jpg" alt="mission" width="100%">
            </div>

        </div>

        <div class="row">
            <div style="height: 31px;"></div>

            <div class="col-12 col-md-6">
                <img src="img/about2.png" alt="mission" width="100%">
            </div>

            <div class="col-12 col-md-6">
                <div style="height: 1px;"></div>
                <h2>
                    <font size="21"><strong>M</strong></font>odern
                </h2>
                <p style="font-size: 20px;">
                    MonoVattino offers a <strong>m</strong>odern solution for urban transportation.
                    With its sleek
                    design and user-friendly interface, users can easily locate, unlock, and ride motorized scooters
                    with
                    just one finger. It
                    promotes eco-friendly and efficient tra<strong>v</strong>el, making short-distance commuting
                    hassle-free
                    and stylish.
                </p>
            </div>


        </div>

        <div class="row">
            <div style="height: 31px;"></div>

            <div class="col-12 col-md-4">
                <img src="img/about3.jpg" alt="mission" width="100%">
            </div>

            <div class="col-12 col-md-4">
                <div style="height: 1px;"></div>
                <h1>
                    Story
                </h1>
                <p style="font-size: 20px;">

                    MonoVattino, a mobility sharing website, offers electric scooters for convenient and eco-friendly
                    urban transportation. Founded by <strong>V</strong>alerio and <strong>M</strong>irko, MonoVattino
                    aims to revolutionize the way people
                    move and embrace a greener future.
                </p>
            </div>

            <div class="col-12 col-md-4">
                <img src="img/about4.jpg" alt="mission" width="100%">
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
                            <a href="https://goo.gl/maps/BzPKV68sjswbXoFB7"
                                style="text-decoration:none; color:inherit;"><i class="fas fa-home me-3"></i> Piazzale
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
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://kit.fontawesome.com/d79f0d308d.js" crossorigin="anonymous"></script>
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
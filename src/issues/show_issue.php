<?php

// require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/scooters/scooter.php');
require_once('../lib/scooters/issues.php');
require_once('../lib/http_exceptions/forbidden.php');
require_once('../lib/accounts/user.php');

session_start();

$is_user_logged = isset($_SESSION['user_email']);
$html_theme = isset($_SESSION['html_theme']) ? $_SESSION['html_theme'] : 'light';
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

try {
    if (!$is_user_logged) {
        header("Location: /account/login.php?redirect_to=show_issue");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        if (! $is_admin) {
            throw new ForbiddenException("You are not an admin!");
        }

        $conn = connect_to_database();
        $issues = get_issues_info($conn);
    } else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} 
// catch (InvalidJWTException $e) {
//     header("Location: /account/login.php");
//     exit;
// } 
catch (ForbiddenException $e) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
}catch (MethodNotAllowedException $e) {
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
<html data-bs-theme="<?php echo $html_theme; ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- Bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- Bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha384-...=" crossorigin="anonymous" />

    <!-- Flag icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css" />

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
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
                        <a class="nav-link" href="#">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">About us</a>
                    </li>
                    <?php if ($is_admin) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/issues/show_issue.php"><strong>Issues</strong></a>
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
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=profile&lang=en"
                                    id="langEN">
                                    <span class="fi fi-gb my-fi" style="padding-right: 5px;"></span>
                                    <span style="font-size: 1rem;">English</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=profile&lang=it"
                                    id="langIT">
                                    <span class="fi fi-it"></span>
                                    <span style="font-size: 1rem;">Italian</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=profile&lang=de"
                                    id="langDE">
                                    <span class="fi fi-de"></span>
                                    <span style="font-size: 1rem;">German</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/account/change_language.php?redirect_to=profile&lang=es"
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

    <!-- List of issues -->
    <div class="container">
        <h1>Issues</h1>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Issue ID</th>
                    <th scope="col">User email</th>
                    <th scope="col">Scooter ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($issues as $issue) { 
                    $is_updated = $issue->status == 'accepted'?>
                <tr>
                    <th scope="row"><?php echo $issue->id ?></th>
                    <td><?php echo $issue->user_email ?></td>
                    <td><?php echo $issue->scooter_id ?></td>
                    <td><?php echo $issue->title ?></td>
                    <td><?php echo $issue->created_at ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a class="btn btn-primary" onclick="openInfo('<?php echo $issue->id; ?>');">
                                Info
                            </a>
                            <a class="btn <?php echo $is_updated ? 'btn-secondary' : 'btn-success'; ?>
                             ?>" href="/issues/update_issue.php?id=<?php echo $issue->id; ?>">
                                Update
                            </a disabled>
                            <a class="btn btn-danger" href="/issues/delete_issue.php?id=<?php echo $issue->id; ?>">
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Info Modal -->
    <div class="modal fade" id="info_modal" tabindex="-1" role="dialog" aria-labelledby="info_modal_title"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="info_modal_title">Info</h5>
                </div>
                <div class="modal-body" id="info_modal_mody">
                    Desc
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
    function openInfo(issue_id) {
        var description = document.getElementById("description_" + issue_id).innerHTML;
        var modal = document.getElementById("info_modal_mody");
        modal.innerHTML = description;
        var myModal = new bootstrap.Modal(document.getElementById('info_modal'), {});
        myModal.show();
    }

    var currentLanguage = "<?php echo $user->language; ?>";

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
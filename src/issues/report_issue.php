<?php

require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/scooters/scooter.php');
require_once('../lib/scooters/issues.php');
require_once('../lib/accounts/user.php');
require_once('../lib/http_exceptions/bad_request.php');

try {
    $scooter_id = $_GET['id'];

    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    $conn = connect_to_database();
    $user = get_user_by_email($conn, $email);
    $html_theme = $user->html_theme;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // pass
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];

        if (empty($title)) {
            $title_error = "Title is required";
            throw new BadRequestException("Title is required");
        }

        if (empty($description)) {
            $description_error = "Description is required";
            throw new BadRequestException("Description is required");
        }

        // thows ScooterNotFoundException if the scooter does not exist
        get_scooter_by_id($conn, $scooter_id);

        create_issue($conn, $email, $scooter_id, $title, $description);

        header("Location: /");
    } else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} catch(BadRequestException $e) {
    // pass
} catch (ScooterNotFoundException $e) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
} catch (InvalidJWTException $e) {
    header("Location: /account/login.php?redirect_to=report_issue&id=$scooter_id");
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
<html lang="en" data-bs-theme="<?php echo $html_theme; ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report issue</title>

    <!-- Bootsrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
    .error-msg {
        padding-top: 5px;
        color: #FF9494;
        font-size: 0.8em;
    }

    .my-content {
        height: calc(100vh - 78.5px);
    }
    </style>
</head>

<body>
    <!-- NavBar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow px-4">
        <div class="container-fluid">
            <i class="bi bi-scooter navbar-brand" style="font-size: 35px;"></i>
            <a class="navbar-brand" href="../index.php"><strong>MonoVattino</strong></a>
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
                        <a class="nav-link" href="/account/profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">About us</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/account/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="my-content container text-center shadow">
        <div class="py-5">
            <i class="bi bi-exclamation-triangle" style="font-size: 100px;"></i>
            <h1>Report issue</h1>
            <p class="lead">Report an issue with this scooter: <?php echo $scooter_id; ?></p>
        </div>
        <form action="report_issue.php?id=<?php echo $scooter_id; ?>" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>">

                <!-- Title error -->
                <?php if ($title_error) { ?>
                <h5 class="error-msg">
                    <?php echo $title_error; ?>
                </h5>
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"
                    value="<?php echo $description; ?>"></textarea>

                <!-- Description error -->
                <?php if ($description_error) { ?>
                <h5 class="error-msg">
                    <?php echo $description_error; ?>
                </h5>
                <?php } ?>
            </div>

            <input type="hidden" name="scooter_id" value="<?php echo $scooter_id; ?>">

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>

</html>
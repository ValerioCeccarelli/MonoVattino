<?php 
require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/user.php');

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    $conn = connect_to_database();
    $user = get_user_by_email($conn, $email);

    $username = $user->username;
} catch (InvalidJWTException $e) {
    http_response_code(401);
    echo "401 Unauthorized";
    exit;
} 
// TODO: add other exceptions
catch (Exception $e) {
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
    img {
        width: 30vw !important;
        height: auto;
    }
    </style>
</head>

<body>
    <?php echo json_encode($user); ?>
    <div class="container">
        <div class="row justify-content-center">
            <h1 style="text-align: center;">Your profile!</h1>
        </div>

        <!-- Profile photo -->
        <div class="row justify-content-center">
            <img src="https://www.gravatar.com/avatar/<?php echo md5($email); ?>?s=200" alt="Profile photo"
                class="rounded-circle">
        </div>

        <!-- Profile info -->
        <div class="form">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Username:</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="<?php echo $username ?>" aria-label="Username"
                        aria-describedby="basic-addon1" disabled>
                    <span class="input-group-text" id="basic-addon1" onclick="showFormModal()">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="<?php echo $email ?>" disabled>
            </div>
            <!-- <button type="submit"><i class="bi bi-pencil-square"></i></button> -->
        </div>
    </div>

    <!-- Form Modal -->
    <div class="modal fade" id="form_modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
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

    <script>
    function showErrorWithModal(message) {
        $('#error_modal_body').html(message);
        $('#error_modal').modal('show');
    }

    function showFormModal() {
        $('#form_modal').modal('show');
    }
    </script>
</body>

</html>
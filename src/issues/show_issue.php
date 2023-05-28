<?php

require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/scooters/scooter.php');
require_once('../lib/scooters/issues.php');
require_once('../lib/http_exceptions/forbidden.php');
require_once('../lib/accounts/user.php');

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $conn = connect_to_database();

        $user = get_user_by_email($conn, $email);

        if (!$user->is_admin) {
            throw new ForbiddenException("You are not an admin!");
        }

        $issues = get_issues_info($conn);
    } else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} catch (InvalidJWTException $e) {
    header("Location: /account/login.php");
    exit;
} catch (ForbiddenException $e) {
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
<html lang="en">

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
</head>

<body>
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

    <script>
    function openInfo(issue_id) {
        var description = document.getElementById("description_" + issue_id).innerHTML;
        var modal = document.getElementById("info_modal_mody");
        modal.innerHTML = description;
        var myModal = new bootstrap.Modal(document.getElementById('info_modal'), {});
        myModal.show();
    }
    </script>
</body>

</html>
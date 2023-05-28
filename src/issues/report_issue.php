<?php

require_once('../lib/jwt.php');
require_once('../lib/database.php');
require_once('../lib/scooters/scooter.php');
require_once('../lib/scooters/issues.php');

try {
    $jwt_payload = validate_jwt();
    $email = $jwt_payload->email;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $scooter_id = $_GET['id'];
        $conn = connect_to_database();
        $scooter = get_scooter_by_id($conn, $scooter_id);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $scooter_id = $_POST['scooter_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];

        // TODO: validate input

        $conn = connect_to_database();
        create_issue($conn, $email, $scooter_id, $title, $description);

        header("Location: /");
    } else {
        throw new MethodNotAllowedException("Method not allowed");
    }
} catch (ScooterNotFoundException $e) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
} catch (InvalidJWTException $e) {
    header("Location: /account/login.php");
    // TODO: redirect to login page e di alla login page che venivi da qui...
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report issue</title>
</head>

<body>
    <h1>Report issue</h1>
    <form action="report_issue.php" method="post">
        <!-- Issue title -->
        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>
        <!-- Issue description -->
        <label for="description">Description</label>
        <textarea name="description" id="description" cols="30" rows="10" required></textarea>

        <!-- Scooter id (invisible) -->
        <input type="hidden" name="scooter_id" value="<?php echo $scooter->id; ?>">

        <input type="submit" value="Submit">
    </form>
</body>

</html>
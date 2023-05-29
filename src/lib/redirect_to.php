<?php

function get_redirect_to() {
    $redirect_to = $_GET['redirect_to'];
    if (isset($redirect_to)) {
        return $redirect_to;
    }
    return null;
}

function try_redirect() {
    $redirect_to = $_GET['redirect_to'];
    error_log("REDIRECT TO 1: -" . $redirect_to . "-");
    if (isset($redirect_to)) {
        $url = "/";

        switch($redirect_to) {
            case 'login':
                $url = "/account/login.php";
                break;
            case 'profile':
                $url = "/account/profile.php";
                break;
            case 'show_issue':
                $url = "/issues/show_issue.php";
                break;
            case 'about':
                $url = "/about.php";
                break;
            case 'report_issue':
                $scooter_id = $_GET['id'];
                if (isset($scooter_id)) {
                    $url = "/issues/report_issue.php?id=$scooter_id";
                }
                break;
            default:
                return;
        }
        error_log("REDIRECT TO 2: $url");
        header("Location: $url");
        exit();
    }
}

?>
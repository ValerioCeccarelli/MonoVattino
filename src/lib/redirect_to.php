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
    if (isset($redirect_to)) {
        $url = "/";

        switch($redirect_to) {
            case 'payment':
                $url = "/account/payment.php";
                break;
            case 'terms':
                $url = "/account/terms.php";
                break;
            case 'index':
                $url = "/";
                break;
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
                } else {
                    $url = "/";
                }
                break;
            default:
                $url = "/";
                return;
        }
        error_log("INFO: redirect_to.php: redirecting to $url");
        header("Location: $url");
        exit();
    }
}

?>
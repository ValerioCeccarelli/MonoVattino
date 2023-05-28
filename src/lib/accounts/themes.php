<?php

function is_valid_map_theme($theme) {
    switch ($theme) {
        case 'dark':
        case 'night':
        case 'atlas':
        case 'classic':
        case 'grey':
        case 'light':
        case 'default':
            return true;
        default:
            return false;
    }
}

function theme_to_mapid($theme) {
    switch ($theme) {
        case 'dark':
            return "a4960208d9b76361";
        case 'night':
            return "7bf73a088c3484e4";
        case 'atlas':
            return "4d28faf75cbe2224";
        case 'classic':
            return "a85cc9c21291463";
        case 'grey':
            return "f12bf4b63529e007";
        case 'light':
            return "b00ca340d0b7980f";
        case 'default':
        default:
            return "18db44928f96d960";
    }
}

function is_valid_html_theme($theme) {
    switch ($theme) {
        case 'dark':
        case 'light':
            return true;
        default:
            return false;
    }
}

function update_map_theme($conn, $user_email, $theme) {
    $query = "UPDATE users SET map_theme = $1 WHERE email = $2";
    $result1 = pg_prepare($conn, "update_map_theme", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "update_map_theme", array($theme, $user_email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

function update_html_theme($conn, $user_email, $theme) {
    $query = "UPDATE users SET html_theme = $1 WHERE email = $2";
    $result1 = pg_prepare($conn, "update_html_theme", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "update_html_theme", array($theme, $user_email));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

?>
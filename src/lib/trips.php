<?php

function create_trip($conn, $scooter_id, $user_id, $travel_time) {
    $query = "INSERT INTO trips (scooter_id, user_email, trip_time, date)
        VALUES ($1, $2, $3, NOW())";

    $result1 = pg_prepare($conn, "create_trip", $query);
    if(!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "create_trip", array($scooter_id, $user_id, $travel_time));
    if(!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

?>
<?php

// pg_server   | 2023-05-25 17:06:55.775 UTC [293] ERROR:  null value in column "user_email" of relation "trips" violates not-null constraint
// pg_server   | 2023-05-25 17:06:55.775 UTC [293] DETAIL:  Failing row contains (5, 72, null, 2023-05-25 17:06:55.774863).
// pg_server   | 2023-05-25 17:06:55.775 UTC [293] STATEMENT:  INSERT INTO trips (scooter_id, user_email, trip_time, date)
// pg_server   |           VALUES ($1, $2, $3, NOW())

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
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

class Trip {
    public $scooter_id;
    public $user_email;
    public $trip_time;
    public $date;

    public $company_name;
    public $company_color;

    public $fixed_cost;
    public $cost_per_minute;
}

function get_user_trips($conn, $user_id) {
    $query = "SELECT t.scooter_id, t.user_email, t.trip_time, t.date, s.company, c.name, c.color, c.cost_per_minute, c.fixed_cost
        FROM trips t
        JOIN scooters s ON t.scooter_id=s.id
        JOIN companies c ON s.company=c.id
        WHERE t.user_email=$1
        ORDER BY t.date DESC
        LIMIT 10";

    $result1 = pg_prepare($conn, "get_user_trips", $query);
    if(!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_user_trips", array($user_id));
    if(!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $trips = array();

    while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
        $trip = new Trip();
        $trip->scooter_id = $row['scooter_id'];
        $trip->user_email = $row['user_email'];
        $trip->trip_time = $row['trip_time'];
        $trip->date = $row['date'];
        $trip->company_name = $row['name'];
        $trip->company_color = $row['color'];
        $trip->fixed_cost = $row['fixed_cost'];
        $trip->cost_per_minute = $row['cost_per_minute'];

        array_push($trips, $trip);
    }

    return $trips;
}

?>
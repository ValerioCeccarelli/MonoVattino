<?php

class Reservations {
    public $scooter_id;
    public $user_email;
    public $date;

    public $company_name;
    public $company_color;

    public $fixed_cost;
    public $cost_per_minute;
}

function get_user_reservation($conn, $user_id) {
    $query = "SELECT r.scooter_id, r.user_email, r.start_time, c.name, c.color, c.fixed_cost, c.cost_per_minute
        FROM reservations r
        JOIN scooters s ON r.scooter_id=s.id
        JOIN companies c ON s.company=c.id
        WHERE r.user_email=$1
        ORDER BY r.start_time DESC";

    $result1 = pg_prepare($conn, "get_user_reservation", $query);
    if(!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_user_reservation", array($user_id));
    if(!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $reservations = array();

    while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
        $reservation = new Reservations();
        $reservation->scooter_id = $row['scooter_id'];
        $reservation->user_email = $row['user_email'];
        $reservation->date = $row['start_time'];
        $reservation->company_name = $row['name'];
        $reservation->company_color = $row['color'];
        $reservation->fixed_cost = $row['fixed_cost'];
        $reservation->cost_per_minute = $row['cost_per_minute'];

        array_push($reservations, $reservation);
    }

    return $reservations;
}

?>
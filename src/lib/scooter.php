<?php

class Scooter {
    public $id;
    public $longitude;
    public $latitude;
    public $battery_level;

    public $company_id;
    public $company_name;
}

function get_scooters($conn, $longitude, $latitude, $radius) {

    $query = "SELECT s.id, s.longitude, s.latitude, s.battery_level, s.company_id, c.name
        FROM scooters s
        JOIN companies c ON s.company=c.id
        WHERE s.is_available=true";

    $result1 = pg_prepare($conn, "get_scooters", $query);
    if(!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_scooters", array());
    if(!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $scooters = array();

    while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
        $scooter = new Scooter();
        $scooter->id = $row['id'];
        $scooter->longitude = $row['longitude'];
        $scooter->latitude = $row['latitude'];
        $scooter->battery_level = $row['battery_level'];
        $scooter->company_id = $row['company_id'];
        $scooter->company_name = $row['name'];

        array_push($scooters, $scooter);
    }

    return $scooters;
}

?>
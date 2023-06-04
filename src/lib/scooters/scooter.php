<?php

class Scooter
{
    public $id;
    public $longitude;
    public $latitude;
    public $battery_level;

    public $company_id;
    public $company_name;
    public $company_color;

    public $is_my_scooter;

    public $cost_per_minute;
    public $fixed_cost;
}

function get_scooters($conn, $longitude, $latitude, $radius)
{

    $query = "SELECT s.id, s.longitude, s.latitude, s.battery_level, s.company, c.name, c.color, c.cost_per_minute, c.fixed_cost
        FROM scooters s
        JOIN companies c ON s.company=c.id
        WHERE ST_DistanceSphere(ST_MakePoint($1, $2), ST_MakePoint(s.latitude, s.longitude)) < $3
            AND s.id NOT IN (
                SELECT t.scooter_id
                FROM reservations t
            )";

    $result1 = pg_prepare($conn, "get_scooters", $query);
    if (!$result1) {
        echo "p " . pg_last_error();
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_scooters", array($latitude, $longitude, $radius));
    if (!$result2) {
        echo "e " . pg_last_error();
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $scooters = array();

    while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
        $scooter = new Scooter();
        $scooter->id = $row['id'];
        $scooter->longitude = $row['longitude'];
        $scooter->latitude = $row['latitude'];
        $scooter->battery_level = $row['battery_level'];
        $scooter->company_id = $row['company'];
        $scooter->company_name = $row['name'];
        $scooter->company_color = $row['color'];
        $scooter->is_my_scooter = false;
        $scooter->cost_per_minute = $row['cost_per_minute'];
        $scooter->fixed_cost = $row['fixed_cost'];

        array_push($scooters, $scooter);
    }

    return $scooters;
}

function get_my_scooters($conn, $user_id)
{
    $query = "SELECT s.id, s.longitude, s.latitude, s.battery_level, s.company, c.name, c.color, c.cost_per_minute, c.fixed_cost
        FROM scooters s
        JOIN companies c ON s.company=c.id
        JOIN reservations t ON s.id=t.scooter_id
        WHERE t.user_email=$1";

    $result1 = pg_prepare($conn, "get_my_scooters", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_my_scooters", array($user_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $scooters = array();

    while ($row = pg_fetch_array($result2, null, PGSQL_ASSOC)) {
        $scooter = new Scooter();
        $scooter->id = $row['id'];
        $scooter->longitude = $row['longitude'];
        $scooter->latitude = $row['latitude'];
        $scooter->battery_level = $row['battery_level'];
        $scooter->company_id = $row['company'];
        $scooter->company_name = $row['name'];
        $scooter->company_color = $row['color'];
        $scooter->is_my_scooter = true;
        $scooter->cost_per_minute = $row['cost_per_minute'];
        $scooter->fixed_cost = $row['fixed_cost'];

        array_push($scooters, $scooter);
    }

    return $scooters;
}

class ScooterAlreayReservedException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

function reserve_scooter($conn, $scooter_id, $user_id)
{
    $query = "INSERT INTO reservations (scooter_id, user_email, start_time)
        VALUES ($1, $2, NOW())";

    $result1 = pg_prepare($conn, "reserve_scooter", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "reserve_scooter", array($scooter_id, $user_id));
    if (!$result2) {
        $last_error = pg_last_error();
        if (strpos($last_error, "duplicate key value violates unique constraint") !== false) {
            throw new ScooterAlreayReservedException("Scooter already reserved!");
        }
        throw new Exception("Could not execute the query: " . $last_error);
    }
}

function get_travel_time($conn, $scooter_id)
{
    $query = "SELECT start_time, NOW() AS end_time
        FROM reservations
        WHERE scooter_id=$1";

    $result1 = pg_prepare($conn, "get_travel_time", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_travel_time", array($scooter_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $row = pg_fetch_array($result2, null, PGSQL_ASSOC);
    $start_time = $row['start_time'];
    $end_time = $row['end_time'];

    $start_time = strtotime($start_time);
    $end_time = strtotime($end_time);

    $travel_time = $end_time - $start_time;

    return $travel_time;
}

function move_to_position($conn, $scooter_id, $longitude, $latitude)
{
    $query = "UPDATE scooters
        SET longitude=$1, latitude=$2
        WHERE id=$3";

    $result1 = pg_prepare($conn, "move_to_position", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "move_to_position", array($longitude, $latitude, $scooter_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

function free_scoter($conn, $scooter_id)
{
    $query = "DELETE FROM reservations WHERE scooter_id=$1";

    $result1 = pg_prepare($conn, "free_scooter", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "free_scooter", array($scooter_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }
}

class ScooterCosts
{
    public $cost_per_minute;
    public $fixed_cost;
}

function get_scooter_costs($conn, $scooter_id)
{
    $query = "SELECT c.cost_per_minute, c.fixed_cost
        FROM scooters s
        JOIN companies c ON s.company=c.id
        WHERE s.id=$1";

    $result1 = pg_prepare($conn, "get_scooter_costs", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_scooter_costs", array($scooter_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $row = pg_fetch_array($result2, null, PGSQL_ASSOC);
    $cost_per_minute = $row['cost_per_minute'];
    $fixed_cost = $row['fixed_cost'];

    $scooter_costs = new ScooterCosts();

    $scooter_costs->cost_per_minute = $cost_per_minute;
    $scooter_costs->fixed_cost = $fixed_cost;

    return $scooter_costs;
}

class ScooterNotFoundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

function get_scooter_by_id($conn, $scooter_id)
{
    $query = "SELECT s.id, s.longitude, s.latitude, s.battery_level, s.company, c.name, c.color, c.cost_per_minute, c.fixed_cost
        FROM scooters s
        JOIN companies c ON s.company=c.id
        WHERE s.id=$1";

    $result1 = pg_prepare($conn, "get_scooter_by_id", $query);
    if (!$result1) {
        throw new Exception("Could not prepare the query: " . pg_last_error());
    }

    $result2 = pg_execute($conn, "get_scooter_by_id", array($scooter_id));
    if (!$result2) {
        throw new Exception("Could not execute the query: " . pg_last_error());
    }

    $row = pg_fetch_array($result2, null, PGSQL_ASSOC);

    if (!$row) {
        throw new ScooterNotFoundException("Scooter not found!");
    }

    $scooter = new Scooter();
    $scooter->id = $row['id'];
    $scooter->longitude = $row['longitude'];
    $scooter->latitude = $row['latitude'];
    $scooter->battery_level = $row['battery_level'];
    $scooter->company_id = $row['company'];
    $scooter->company_name = $row['name'];
    $scooter->company_color = $row['color'];
    $scooter->is_my_scooter = false;
    $scooter->cost_per_minute = $row['cost_per_minute'];
    $scooter->fixed_cost = $row['fixed_cost'];

    return $scooter;
}

?>
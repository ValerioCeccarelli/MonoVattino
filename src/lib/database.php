<?php

error_reporting(0);

# this function connects to the postgres database and returns the connection or false if it fails
function connect_to_database() {
    $host = $_ENV["POSTGRES_HOST"];
    $port = $_ENV["POSTGRES_PORT"];
    $user = $_ENV["POSTGRES_USER"];
    $password = $_ENV["POSTGRES_PASSWORD"];
    $dbname = $_ENV["POSTGRES_DB"];

    $connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

    return pg_connect($connection_string);
}

?>
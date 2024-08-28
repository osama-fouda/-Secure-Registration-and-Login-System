<?php
function getDatabaseConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "bestosdb";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($connection->connect_error) {
        die("Error: Failed to connect to MySQL: " . $connection->connect_error);
    }

    return $connection;
}
?>

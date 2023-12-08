<?php

$host = 'localhost'; // or the IP address of your database server
$username = 'root'; // the username you use to access MySQL
$password = ''; // the password you use to access MySQL
$dbname = 'EMS'; // your database name

// Create a new database connection instance
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// echo "Connected successfully";

// This function is just to get the connection from other scripts.
function getDbConnection() {
    global $mysqli;
    return $mysqli;
}

// Use getDbConnection() in other scripts to access the $mysqli object.

?>

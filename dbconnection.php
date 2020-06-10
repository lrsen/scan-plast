<?php


$server     = "localhost";
$username   = "root";
$password   = "";
$db         = "products";

// Create connection to db
global $mysqli;
$mysqli = new mysqli($server, $username, $password, $db);
// Check connection to db
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

<?php
$mysqli = new mysqli("localhost", "root", "", "packmatestest");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
} else {
   //print("Connected to MySQL")
}
?>
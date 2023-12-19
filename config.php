<?php

$servername = "127.0.0.1:3308";
$username = "joe";
$password = "joev2";
$dbname = "api";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

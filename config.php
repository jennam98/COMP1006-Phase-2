<?php

$recaptcha_secret = "6LdEpq0sAAAAADEVr0IHaeoE0i3tgm9hcQG6kni7";
$host = "localhost";
$user = "root";
$password = "";
$database = "phase_2";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
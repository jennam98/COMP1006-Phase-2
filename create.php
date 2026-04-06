<?php include "config.php"; ?>

<?php
// function to sanitize user input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$error = [];

// check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first = clean_input($_POST["first_name"]);
    $last = clean_input($_POST["last_name"]);
    $position = clean_input($_POST["position"]);
    $skills = clean_input($_POST["skills"]);
    $email = clean_input($_POST["email"]);
    $phone = clean_input($_POST["phone"]);
    $bio = clean_input($_POST["bio"]);
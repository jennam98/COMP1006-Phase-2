<?php
session_start();
include "config.php";

// Sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (!$username) $errors[] = "Username is required.";
    if (!$password) $errors[] = "Password is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // reCAPTCHA validation
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';
    if (!$recaptcha) {
        $errors[] = "Please complete the reCAPTCHA.";
    } else {
        $secretKey = $recaptcha_secret; // from config.php
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha");
        $response = json_decode($verify);
        if (!$response->success) $errors[] = "reCAPTCHA verification failed.";
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "Username already taken.";


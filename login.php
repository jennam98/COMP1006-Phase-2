<?php
session_start();
include "config.php";


function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];

    if (!$username) $errors[] = "Username is required.";
    if (!$password) $errors[] = "Password is required.";

    // reCAPTCHA
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';
    if (!$recaptcha) {
        $errors[] = "Please complete the reCAPTCHA.";
    } else {
        $secretKey = $recaptcha_secret;
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptcha");
        $response = json_decode($verify);
        if (!$response->success) $errors[] = "reCAPTCHA verification failed.";
    }
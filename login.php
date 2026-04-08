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

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password);
        if ($stmt->num_rows == 1) {
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "User not found.";
        }
    }
}
?>
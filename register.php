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

    // Insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="p-5">

<h2>Register</h2>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"><?= $err ?></div>
<?php endforeach; ?>

<form method="POST" class="mt-3">
    <div class="mb-2"><input class="form-control" type="text" name="username" placeholder="Username" required></div>
    <div class="mb-2"><input class="form-control" type="password" name="password" placeholder="Password" required></div>
    <div class="mb-2"><input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password" required></div>
    <div class="mb-2">
        <div class="g-recaptcha" data-sitekey="6LdEpq0sAAAAABqBgxM2Hfy9SFnHjpC7FOT7iScR"></div>
    </div>
    <button class="btn btn-primary" type="submit">Register</button>
    <a class="btn btn-secondary" href="login.php">Login</a>
</form>

</body>
</html>


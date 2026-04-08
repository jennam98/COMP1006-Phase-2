<?php
session_start();
include "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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


    // validate input
    if (empty($first)) {
        $error[] = "First name is required.";
    }
    if (empty($last)) {
        $error[] = "Last name is required.";
    }
    if (empty($position)) {
        $error[] = "Position is required.";
    }
    if (empty($skills)) {
        $error[] = "Skills are required.";
    }
    if (empty($email)) {
        $error[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Invalid email format.";
    }
    if (empty($phone)) {
        $error[] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error[] = "Invalid phone number format. Must be 10 digits.";
    }
    if (empty($bio)) {
        $error[] = "Bio is required.";
    }

    
    // File upload
    $resume_file_name = null;
    if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $resume_file_name = time() . "_" . basename($_FILES["resume_file"]["name"]);
        $target_file = $target_dir . $resume_file_name;

        if (!move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file)) {
            $errors[] = "Error uploading file.";
        }
    }


    // if there are no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO resumes (first_name,last_name,position,skills,email,phone,bio,resume_file,user_id) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssi", $first, $last, $position, $skills, $email, $phone, $bio, $resume_file_name, $_SESSION['user_id']);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}
?>
<h2>Create Resume</h2>

<?php
// display errors if there are any
foreach ($error as $err) {
    echo "<p>$err</p>";
}
?>
<!-- // display the form -->
 <!-- reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<form method="POST">
    First Name: <input type="text" name="first_name" required><br>
    Last Name: <input type="text" name="last_name" required><br>
    Current Position: <input type="text" name="position"><br>
    Skills: <textarea name="skills"></textarea><br>
    Email: <input type="email" name="email" required><br>
    Phone: <input type="text" name="phone"><br>
    Bio: <textarea name="bio"></textarea><br>
    <!-- reCAPTCHA html -->
    <div class="g-recaptcha" data-sitekey="6LdEpq0sAAAAABqBgxM2Hfy9SFnHjpC7FOT7iScR"></div>
    <button type="submit">Save</button>
</form>
<!-- // link to go back to the index page -->
<button type="back" onclick="window.location.href='index.php'">Back</button>

</div>

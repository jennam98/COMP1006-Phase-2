<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Fetch existing resume
$stmt = $conn->prepare("SELECT * FROM resumes WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();
if (!$resume) { header("Location: index.php"); exit(); }



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
    $resume_file_name = $resume['resume_file']; // keep old file
    if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $resume_file_name = time() . "_" . basename($_FILES["resume_file"]["name"]);
        $target_file = $target_dir . $resume_file_name;
        if (!move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file)) {
            $errors[] = "Error uploading file.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE resumes SET first_name=?, last_name=?, position=?, skills=?, email=?, phone=?, bio=?, resume_file=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sssssssssi", $first, $last, $position, $skills, $email, $phone, $bio, $resume_file_name, $id, $_SESSION['user_id']);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Resume</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-5">

<h2>Edit Resume</h2>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"><?= $err ?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data" class="mt-3">
    <div class="mb-2"><input class="form-control" type="text" name="first_name" value="<?= $resume['first_name'] ?>" required></div>
    <div class="mb-2"><input class="form-control" type="text" name="last_name" value="<?= $resume['last_name'] ?>" required></div>
    <div class="mb-2"><input class="form-control" type="text" name="position" value="<?= $resume['position'] ?>"></div>
    <div class="mb-2"><textarea class="form-control" name="skills"><?= $resume['skills'] ?></textarea></div>
    <div class="mb-2"><input class="form-control" type="email" name="email" value="<?= $resume['email'] ?>" required></div>
    <div class="mb-2"><input class="form-control" type="text" name="phone" value="<?= $resume['phone'] ?>"></div>
    <div class="mb-2"><textarea class="form-control" name="bio"><?= $resume['bio'] ?></textarea></div>
    <div class="mb-2"><input class="form-control" type="file" name="resume_file"></div>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-secondary" href="index.php">Back</a>
</form>

</body>
</html>




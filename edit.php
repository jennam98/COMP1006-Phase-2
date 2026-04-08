<?php include "config.php"; ?>

<?php
// function to sanitize user input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$id = $_GET["id"];
// fetch existing resume data
$stmt = $conn->prepare("SELECT * FROM resumes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$resume = $result->fetch_assoc();

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

    // if there are no errors, insert data into the database
   if (empty($error)) {
        $stmt = $conn->prepare("UPDATE resumes 
        SET first_name=?, last_name=?, position=?, skills=?, email=?, phone=?, bio=? 
        WHERE id=?");

        $stmt->bind_param("sssssssi", $first, $last, $position, $skills, $email, $phone, $bio, $id);
        $stmt->execute();

        header("Location: index.php");
        exit();

    }
}
?>

<h2>Edit Resume</h2>

<?php
// display errors if there are any
foreach ($error as $err) {
    echo "<p>$err</p>";
}
?>
<!-- // display the form -->
<form method="POST">
    First Name: <input type="text" name="first_name" value="<?= $resume['first_name'] ?>" required><br>
    Last Name: <input type="text" name="last_name" value="<?= $resume['last_name'] ?>" required><br>
    Current Position: <input type="text" name="position" value="<?= $resume['position'] ?>"><br>
    Skills: <textarea name="skills"><?= $resume['skills'] ?></textarea><br>
    Email: <input type="email" name="email" value="<?= $resume['email'] ?>" required><br>
    Phone: <input type="text" name="phone" value="<?= $resume['phone'] ?>"><br>
    Bio: <textarea name="bio"><?= $resume['bio'] ?></textarea><br>
    
    <button type="submit">Save</button>
</form>
<!-- // link to go back to the index page -->
<button type="back" onclick="window.location.href='index.php'">Back</button>




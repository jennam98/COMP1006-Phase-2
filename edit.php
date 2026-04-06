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
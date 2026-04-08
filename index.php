<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$res = $conn->prepare("SELECT * FROM resumes WHERE user_id=?");
$res->bind_param("i", $_SESSION['user_id']);
$res->execute();
$result = $res->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Resumes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-5">

<h2>Your Resumes</h2>
<a class="btn btn-success mb-3" href="create.php">Create New Resume</a>
<a class="btn btn-warning mb-3 float-end" href="logout.php">Logout</a>

<?php if ($result->num_rows > 0): ?>
    <div class="list-group">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="list-group-item">
            <h5><?= $row['first_name'] . ' ' . $row['last_name'] ?></h5>
            <p>Email: <?= $row['email'] ?> | Phone: <?= $row['phone'] ?></p>
            <p>Position: <?= $row['position'] ?> | Skills: <?= $row['skills'] ?></p>
            <p>Bio: <?= $row['bio'] ?></p>
            <?php if($row['resume_file']): ?>
                <p>File: <a href="uploads/<?= $row['resume_file'] ?>" target="_blank">Download</a></p>
            <?php endif; ?>
            <a class="btn btn-primary btn-sm" href="edit.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn btn-danger btn-sm" href="delete.php?id=<?= $row['id'] ?>">Delete</a>
        </div>
    <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No resumes found. Create your first resume!</p>
<?php endif; ?>

</body>
</html>
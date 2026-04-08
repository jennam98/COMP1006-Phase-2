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
    <title>My Resumes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

<h2>Your Resumes</h2>
<a href="create.php" class="btn btn-primary mb-3">Create New Resume</a>
<a href="logout.php" class="btn btn-secondary mb-3">Logout</a>

<?php if ($result->num_rows > 0): ?>
    <table class="list-group">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No resumes found. Click "Create New Resume" to add one.</p>
<?php endif; ?>

</body>
</html>
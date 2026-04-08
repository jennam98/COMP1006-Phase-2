<?php include 'config.php'; ?>

<h2>Your Resumes</h2>
<a href="create.php">Create New Resume</a>

<?php
$result = $conn->query("SELECT * FROM resumes");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . htmlspecialchars($row["first_name"]) . " " . htmlspecialchars($row["last_name"]) . "</h3>";
        echo "<p>Email: " . htmlspecialchars($row["email"]) . "</p>";
        echo "<p>Phone: " . htmlspecialchars($row["phone"]) . "</p>";
        echo "<p>Bio: " . htmlspecialchars($row["bio"]) . "</p>";
        echo "<p>Position: " . htmlspecialchars($row["position"]) . "</p>";
        echo "<p>Skills: " . htmlspecialchars($row["skills"]) . "</p>";

        echo "<a href='edit.php?id=" . $row["id"] . "'>Edit</a> | ";
        echo "<a href='delete.php?id=" . $row["id"] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>";
        echo "</div><hr>";
    }
} else {
    echo "<p>Click \"Create New Resume\" to add your first resume.</p>";
}

$conn->close();
?>
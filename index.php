<?php include 'config.php'; ?>

<h2> Your Resumes </h2>
<a href="create.php">Create New Resume</a>

<?php
$result = $conn->query("SELECT * FROM resumes");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h3>" . $row["first_name"] . " " . $row["last_name"] . "</h3>";
        echo "<p>Email: " . $row["email"] . "</p>";
        echo "<p>Phone: " . $row["phone"] . "</p>";
        echo "<p>Bio: " . $row["bio"] . "</p>";
        echo "<p>Position: " . $row["position"] . "</p>";
        echo "<p>Skills: " . $row["skills"] . "</p>";
   
        echo "<a href='edit.php?id=" . $row["id"] . "'>Edit</a> | ";
        echo "<a href='delete.php?id=" . $row["id"] . "'>Delete</a>"; 
        echo "</div><hr>";
    }
} else {
    echo "<p>Click create resume to add your first resume.</p>";
}

$conn->close();
?>
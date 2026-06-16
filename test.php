<?php
require 'config/db.php';
echo "<h1>Success!</h1>";
echo "<p>Connected to the database successfully.</p>";

// Test fetching dummy data
$result = mysqli_query($conn, "SELECT name, role FROM users");
echo "<h3>Users in Database:</h3><ul>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<li>" . $row['name'] . " (" . $row['role'] . ")</li>";
}
echo "</ul>";
?>
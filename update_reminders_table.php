<?php
// This script ensures the reminders table has all the necessary columns for history tracking
$host = "localhost";
$db = "ADDMEDICINE";
$user = "root";
$pass = "";

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Updating Reminders Table Structure</h1>";

// Check if 'status' column exists and add it if not
$result = $conn->query("SHOW COLUMNS FROM reminders LIKE 'status'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE reminders ADD COLUMN status VARCHAR(50) DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Added 'status' column successfully</p>";
    } else {
        echo "<p>Error adding 'status' column: " . $conn->error . "</p>";
    }
} else {
    echo "<p>'status' column already exists</p>";
}

// Check if 'completion_date' column exists and add it if not
$result = $conn->query("SHOW COLUMNS FROM reminders LIKE 'completion_date'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE reminders ADD COLUMN completion_date DATE DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Added 'completion_date' column successfully</p>";
    } else {
        echo "<p>Error adding 'completion_date' column: " . $conn->error . "</p>";
    }
} else {
    echo "<p>'completion_date' column already exists</p>";
}

// Check if 'completion_time' column exists and add it if not
$result = $conn->query("SHOW COLUMNS FROM reminders LIKE 'completion_time'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE reminders ADD COLUMN completion_time TIME DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Added 'completion_time' column successfully</p>";
    } else {
        echo "<p>Error adding 'completion_time' column: " . $conn->error . "</p>";
    }
} else {
    echo "<p>'completion_time' column already exists</p>";
}

echo "<p>Database update completed.</p>";
$conn->close();
?>

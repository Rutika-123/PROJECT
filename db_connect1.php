<?php
$servername = "localhost"; // Change this if you're using a remote DB
$username = "root";
$password = "";
$database = "sign-up";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
include("db_connect1.php"); // Your DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"], PASSWORD_DEFAULT); // Secure hashing

    // Check if user already exists
    $check = $conn->prepare("SELECT * FROM user WHERE email = ?");
    if(!$check){
        die("check prepare failed:".$conn->error);
    }
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "User with this email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        if(!$stmt){
            die("insert prepare failed:".$conn->error);
        }
        $stmt->bind_param("sss",$username, $email, $password);
    
        if ($stmt->execute()) {
            echo "Registration successful!";
            header("Location:dashboard2.php");
            exit();
        } else {
            echo "Something went wrong. Try again.";
    }
}
}
?>
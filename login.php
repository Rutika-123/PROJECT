 <?php
session_start();
include("db_connect1.php"); // Your DB connection file

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE username=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['username'] = $username;
    echo "Welcome $username! You are now logged in.";
    header("Location:dashboard2.php");
} else {
    header("Location:index1.html?error=1");
    exit();
}

$stmt->close();
$conn->close();
?> 
<?php
include 'db_connect1.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
 $email = trim($_POST['email']);
 $otp = trim($_POST['otp']);
 $newPassword = trim($_POST['newPassword']);
 if (!empty($email) && !empty($otp) && !empty($newPassword)) {
 // Use prepared statements to prevent SQL injection
 $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND token = ?");
 $stmt->bind_param("ss", $email, $otp);
 $stmt->execute();
 $result = $stmt->get_result();
 if ($result && $result->num_rows > 0) {
 $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
 // Update password and clear token
 $updateStmt = $conn->prepare("UPDATE user SET password = ?, token = NULL
WHERE email = ?");
 $updateStmt->bind_param("ss", $hashed_password, $email);
 $updateStmt->execute();
 if ($updateStmt->affected_rows > 0) {
 echo "<script type='text/javascript'>
 alert('Password has been reset successfully.');
 window.location.href = 'index1.html';
 </script>";
 exit();
 } else {
 echo "<script type='text/javascript'>alert('Password update failed!')</script>";
 }
 } else {
 echo "<script type='text/javascript'>alert('Invalid OTP or email!')</script>";
 }
 } else {
 echo "<script type='text/javascript'>alert('Please enter all fields!')</script>";
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Verify OTP</title>
 <style>
  body {
        margin: 0;
        padding: 0;
        font-family: 'Raleway', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        background: linear-gradient(to right, #e6ddea, #cfdef3);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .reset-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        padding: 40px 32px 32px 32px;
        max-width: 350px;
        width: 100%;
        text-align: center;
    }
    .reset-title {
        color: #6a5acd;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 24px;
        letter-spacing: 1px;
    }
    .reset-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .reset-form input[type="email"],
    .reset-form input[type="text"],
    .reset-form input[type="password"] {
        padding: 12px 16px;
        border-radius: 16px;
        border: 1.5px solid #b19cd9;
        font-size: 1rem;
        color: #2f4f4f;
        outline: none;
        transition: border 0.2s;
    }
    .reset-form input:focus {
        border: 2px solid #6a5acd;
        background: #f3f0fa;
    }
    .reset-form input[type="submit"] {
        background: linear-gradient(90deg, #b19cd9 60%, #6a5acd 100%);
        color: #fff;
        border: none;
        border-radius: 16px;
        padding: 12px 0;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
    }
    .reset-form input[type="submit"]:hover {
        background: linear-gradient(90deg, #6a5acd 60%, #b19cd9 100%);
    }
    .back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 18px;
        text-decoration: none;
        color: #6a5acd;
        font-weight: 600;
        font-size: 1rem;
        gap: 8px;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: #b19cd9;
    }
    .back-link img {
        vertical-align: middle;
        height: 28px;
        width: 28px;
    }

    </style>
</head>
<body>
 <section class="contact" id="verifyOTP">
 <h1 class="contactHead">Verify OTP</h1>
 <form method="POST">
 <div class="formMain1">
 <label for="email"></label>
 <input type="email" name="email" placeholder="Enter your registered email"
required>
 </div>
 <div class="formMain1">
 <label for="otp"></label>
 <input type="text" name="otp" placeholder="Enter the OTP" required>
 </div>
 <div class="formMain1">
 <label for="newPassword"></label>
 <input type="password" name="newPassword" placeholder="Enter your new
password" required>
 </div>
 <div class="formMain1" id="btnCont">
 <input type="submit" value="Reset Password">
 </div>
 <div>
 <a href="index1.html"><img src="./resources/arrow-circle-left.png" alt=""
height="40px" width="40px"></a>
 </div>
 </form>
 </section>
</body>
</html>
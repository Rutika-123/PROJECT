<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
include 'db_connect1.php';
if($_SERVER['REQUEST_METHOD'] == "POST") {
 $email = $_POST['email'];
 if(!empty($email)) {
 $query = "SELECT * FROM user WHERE email='$email'";
 $result = mysqli_query($conn, $query);
 if($result && mysqli_num_rows($result) > 0) {
 // Generate a unique OTP
 $otp = rand(100000, 999999);
 $updateQuery = "UPDATE user SET token='$otp' WHERE email='$email'";
 mysqli_query($conn, $updateQuery);
 $resetLink =
"http://localhost/PROJECT%20BCA/reset_password.php?email=$email&otp=$otp";

 // Send OTP to the user's email
 $mail = new PHPMailer(true);
 try {
 $mail->isSMTP();
 $mail->Host = 'smtp.gmail.com';
 $mail->SMTPAuth = true;
 $mail->Username = 'chaatologyy@gmail.com'; // Your Gmail address
 $mail->Password = 'dsbnkxcogimziclq'; // Your Gmail password or App Password
 $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
 $mail->Port = 587;
 //Recipients
 $mail->setFrom('chaatologyy@gmail.com', 'Chaat-O-Logy');
 $mail->addAddress($email);
 // Content
 $mail->isHTML(true);
 $mail->Subject = 'Password Reset OTP';
 $mail->Body = "Your OTP for password reset is: $otp
 ,
 <a href='$resetLink'>Click here to reset your password</a>";

 $mail->send();
 echo "<script type='text/javascript'>alert('OTP has been sent to your
email.')</script>";
 } catch (Exception $e) {
 echo "<script type='text/javascript'>alert('Message could not be sent. Mailer Error:
{$mail->ErrorInfo}')</script>";
 }
 } else {
 echo "<script type='text/javascript'>alert('No user found with that email!')</script>";
 }
 } else {
 echo "<script type='text/javascript'>alert('Please enter a valid email!')</script>";
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Forgot Password</title>
 <style>
 /* Add your styles here */
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
    .forgot-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        padding: 40px 32px 32px 32px;
        max-width: 350px;
        width: 100%;
        text-align: center;
    }
    .forgot-title {
        color: #6a5acd;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 24px;
        letter-spacing: 1px;
    }
    .forgot-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .forgot-form input[type="email"] {
        padding: 12px 16px;
        border-radius: 16px;
        border: 1.5px solid #b19cd9;
        font-size: 1rem;
        color: #2f4f4f;
        outline: none;
        transition: border 0.2s;
    }
    .forgot-form input[type="email"]:focus {
        border: 2px solid #6a5acd;
        background: #f3f0fa;
    }
    .forgot-form input[type="submit"] {
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
    .forgot-form input[type="submit"]:hover {
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
 <div class="forgot-container">
        <div class="forgot-title">Forgot Password</div>
        <form method="POST" class="forgot-form">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <input type="submit" value="Submit">
        </form>
        <a href="index1.html" class="back-link">Back to Login
        </a>
    </div>
</body>
</html>
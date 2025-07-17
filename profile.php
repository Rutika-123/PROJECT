<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "profile";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$msg = "";
$msgClass = "success";
$userData = null;

// Get current profile data
$stmt = $conn->prepare("SELECT * FROM userprofile ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
}
$stmt->close();

// Handle profile picture removal
if (isset($_POST['remove_pic'])) {
    if ($userData && !empty($userData['profile_pic'])) {
        $oldPicPath = "uploads/" . $userData['profile_pic'];
        
        // Update database with empty profile pic
        $stmt = $conn->prepare("UPDATE userprofile SET profile_pic = '' WHERE id = ?");
        $stmt->bind_param("i", $userData['id']);
        
        if ($stmt->execute()) {
            // Try to delete the file (but don't depend on it for success)
            if (file_exists($oldPicPath)) {
                @unlink($oldPicPath);
            }
            
            $msg = "Profile picture removed successfully!";
            
            // Refresh user data
            $userData['profile_pic'] = '';
        } else {
            $msg = "Error removing profile picture.";
            $msgClass = "error";
        }
        $stmt->close();
    }
}

// Handle profile update
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $filename = $userData['profile_pic'] ?? ''; // Default to current picture
    
    // Check if a new file was uploaded
    if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["size"] > 0) {
        $target_dir = "uploads/";
        $filename = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $filename;
        
        // Create uploads directory if it doesn't exist
        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }
        
        // Try to upload the file
        if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $msg = "Error uploading profile picture.";
            $msgClass = "error";
            // Continue with the update using the existing picture
        }
    }
    
    // If this is the first profile or we want to update
    if (!$userData) {
        // Insert new profile
        $stmt = $conn->prepare("INSERT INTO userprofile (name, phone, dob, email, profile_pic) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $phone, $dob, $email, $filename);
    } else {
        // Update existing profile
        $stmt = $conn->prepare("UPDATE userprofile SET name = ?, phone = ?, dob = ?, email = ?, profile_pic = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $phone, $dob, $email, $filename, $userData['id']);
    }
    
    if ($stmt->execute()) {
        $msg = "Profile saved successfully!";
        
        // Refresh user data
        if (!$userData) {
            $userData = [
                'id' => $conn->insert_id,
                'name' => $name,
                'phone' => $phone,
                'dob' => $dob,
                'email' => $email,
                'profile_pic' => $filename
            ];
        } else {
            $userData['name'] = $name;
            $userData['phone'] = $phone;
            $userData['dob'] = $dob;
            $userData['email'] = $email;
            $userData['profile_pic'] = $filename;
        }
    } else {
        $msg = "Error saving profile information.";
        $msgClass = "error";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'theme_header.php'; ?>
    <title>Profile | MedClock</title>
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }
        
        body { 
            font-family: Arial;
            display: flex; 
            justify-content: center; 
            align-items: center;
            margin: 0;
            padding: 0;
            position: relative;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('Resources/medicine.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(1px) brightness(1.0);
            z-index: -1;
        }
        
        body.dark-theme::before {
            filter: blur(1px) brightness(0.7);
        }
        
        body.dark-theme {
            background: none !important;
            color: #f4f4f4 !important;
        }
        
        .card { 
            background: white; 
            padding: 20px; 
            border-radius: 15px; 
            box-shadow: 0px 0px 10px #ccc; 
            width: 90%;
            max-width: 400px;
            max-height: 85vh;
            overflow-y: auto;
            margin: 0;
            scrollbar-width: thin;
            scrollbar-color: #b19cd9 #f0f0f0;
        }
        
        .card::-webkit-scrollbar {
            width: 8px;
        }
        
        .card::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }
        
        .card::-webkit-scrollbar-thumb {
            background-color: #b19cd9;
            border-radius: 10px;
        }
        
        body.dark-theme .card {
            background-color: rgba(45, 55, 72, 0.8) !important;
            box-shadow: 0 8px 16px rgba(0,0,0,0.4) !important;
            color: #f4f4f4 !important;
        }
        
        h2 { 
            background: linear-gradient(to right, #2a9df4, #a29bfe); 
            color: white; 
            padding: 15px; 
            text-align: center; 
            border-radius: 10px; 
            margin-top: 0;
        }
        
        body.dark-theme h2 {
            background: linear-gradient(to right, #3a4d6d, #6c5ce7);
        }
        
        input[type="text"], input[type="date"], input[type="file"], input[type="email"] {
            width: 100%; 
            padding: 8px; 
            margin-top: 8px; 
            margin-bottom: 15px; 
            border-radius: 5px; 
            border: 1px solid #aaa;
            box-sizing: border-box;
        }
        
        body.dark-theme input[type="text"],
        body.dark-theme input[type="date"],
        body.dark-theme input[type="file"],
        body.dark-theme input[type="email"] {
            background-color: #3a4756;
            color: #f4f4f4;
            border: 1px solid #555;
        }
        .btn {
            background: #2a9df4; 
            color: white; 
            border: none; 
            padding: 10px; 
            width: 100%; 
            border-radius: 5px; 
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background: #1a8de4;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .home-btn {
            display: block; 
            margin-bottom: 15px; 
            background: #4c8bf5; 
            color: white; 
            text-align: center; 
            padding: 8px; 
            border-radius: 5px; 
            text-decoration: none;
        }
        .message { 
            margin-top: 15px; 
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        .error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        .profile-section {
            margin-bottom: 20px;
        }
        .current-pic {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        body.dark-theme .current-pic {
            background-color: #2d3748;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #4c8bf5;
        }
        .pic-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 40px;
            color: #aaa;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .optional {
            font-size: 0.8em;
            color: #777;
            font-weight: normal;
        }
        
        @media (max-width: 500px) {
            .card {
                padding: 15px;
                margin: 10px;
            }
            
            h2 {
                font-size: 1.4rem;
                padding: 10px;
            }
            
            .profile-img {
                width: 80px;
                height: 80px;
            }
            
            .pic-placeholder {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }
        }
    </style>
</head>
<body id="body">
    <form method="post" enctype="multipart/form-data" class="card">
        <h2>MedClock Profile</h2>
        <a href="dashboard2.php" class="home-btn">Home</a>
        
        <?php if (!empty($userData['profile_pic'])): ?>
        <div class="profile-section">
            <div class="current-pic">
                <img src="uploads/<?= htmlspecialchars($userData['profile_pic']) ?>" alt="Profile Picture" class="profile-img">
                <p>Current Profile Picture</p>
                <button type="submit" name="remove_pic" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove your profile picture?')">Remove Picture</button>
            </div>
        </div>
        <?php else: ?>
        <div class="profile-section">
            <div class="current-pic">
                <div class="pic-placeholder">👤</div>
                <p>No profile picture set</p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($userData['name'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($userData['dob'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="profile_pic">Upload Profile Picture: <span class="optional">(Optional<?= empty($userData['profile_pic']) ? '' : ' - Will replace current picture' ?>)</span></label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
        </div>
        
        <input type="submit" name="submit" value="Save Profile" class="btn">
        
        <?php if ($msg): ?>
        <p class="message <?= $msgClass ?>"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>
    </form>
    
    <?php include 'theme_footer.php'; ?>
</body>
</html>
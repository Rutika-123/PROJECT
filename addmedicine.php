<?php
$host = "localhost";
$db = "ADDMEDICINE";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}
$success = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $medicine_name = $_POST["medicine_name"];
 $date = $_POST["date"];
 $time = $_POST["time"];
 
 // Current date and time
 $currentDate = date("Y-m-d");
 $currentTime = date("H:i");
 
 if ($medicine_name && $date && $time) {
    // Validate if date and time are not in the past
    if ($date < $currentDate) {
        $error = "Please select a current or future date.";
    } elseif ($date == $currentDate && $time < $currentTime) {
        $error = "Please select a current or future time.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reminders (medicine_name, date, time) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $medicine_name, $date, $time);
        if ($stmt->execute()) {
            $success = "Reminder added successfully!";
        } else {
            $error = "Failed to add reminder.";
        }
        $stmt->close();
    }
 } else {
    $error = "All fields are required.";
 }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<?php include 'theme_header.php'; ?>
<title>MedClock - Add Medicine</title>
<style>
html, body {
    height: 100%;
    overflow: hidden;
}

body { 
    font-family: Arial;
    margin: 0;
    padding: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
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

.content-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-height: 95vh;
    padding: 0;
}

h2 {
    margin: 0 0 10px 0;
    text-align: center;
    font-size: 1.5rem;
}

form {
    background-color: #fff; 
    padding: 20px; 
    width: 90%;
    max-width: 400px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin: 10px 0;
    transition: background-color 0.3s, box-shadow 0.3s;
}

body.dark-theme form {
    background-color: rgba(45, 55, 72, 0.8);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.form-group {
    margin-bottom: 12px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

body.dark-theme label {
    color: #f4f4f4;
}

input {
    width: 100%;
    padding: 10px; 
    margin: 8px 0; 
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    transition: background-color 0.3s, color 0.3s, border 0.3s;
}

body.dark-theme input {
    background-color: #3a4756;
    color: #f4f4f4;
    border: 1px solid #555;
}

button { 
    background-color: #4b79d8; 
    color: white;
    border: none;
    width: 100%;
    padding: 12px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

body.dark-theme button {
    background-color: #5a4cad;
}

button:hover {
    background-color: #3b69c8;
}

body.dark-theme button:hover {
    background-color: #3a2c8d;
}

.success {
    color: green;
    padding: 10px;
    margin-top: 15px;
    background-color: rgba(0, 128, 0, 0.1);
    border-radius: 4px;
}

body.dark-theme .success {
    color: #48bb78;
    background-color: rgba(72, 187, 120, 0.1);
}

.error {
    color: #e53e3e;
    padding: 10px;
    margin-top: 15px;
    background-color: rgba(255, 0, 0, 0.1);
    border-radius: 4px;
    border-left: 4px solid #e53e3e;
    font-weight: bold;
}

body.dark-theme .error {
    color: #fc8181;
    background-color: rgba(252, 129, 129, 0.1);
    border-left: 4px solid #fc8181;
}

.links-container {
    width: 90%;
    max-width: 400px;
    text-align: center;
    margin: 5px 0;
}

.home-link {
    display: inline-block;
    background: #4c8bf5;
    color: white;
    text-align: center;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
    margin: 0;
}

body.dark-theme .home-link {
    background: #5a4cad;
}

.home-link:hover {
    background: #3b7ae4;
}

body.dark-theme .home-link:hover {
    background: #3a2c8d;
}

.notification-banner {
    background-color: rgba(75, 121, 216, 0.1);
    padding: 10px 15px;
    margin: 5px 0;
    border-radius: 8px;
    border-left: 4px solid #4b79d8;
    transition: background-color 0.3s;
    width: 90%;
    max-width: 400px;
    font-size: 0.9rem;
}

body.dark-theme .notification-banner {
    background-color: rgba(75, 121, 216, 0.05);
    border-left: 4px solid #5a4cad;
}

body.dark-theme h2 {
    color: #f4f4f4;
}

.notification-banner p {
    margin: 0;
}

@media (max-width: 500px) {
    h2 {
        font-size: 1.4rem;
        margin-top: 20px;
    }
    
    form {
        padding: 20px;
        margin: 10px auto;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    input {
        padding: 8px;
        font-size: 14px;
    }
    
    button {
        padding: 10px;
        font-size: 14px;
    }
}
</style>
</head>
<body id="body">
<div class="content-wrapper">
    <h2>Add New Medicine Reminder</h2>
    
    <div class="links-container">
        <a href="dashboard2.php" class="home-link">Home</a>
    </div>
    
    <div class="notification-banner">
        <p><strong>Note:</strong> You'll receive notifications when it's time to take your medicine. Make sure to enable sound in the settings!</p>
    </div>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="medicine_name">Medicine Name:</label>
            <input type="text" id="medicine_name" name="medicine_name" required placeholder="Enter medicine name">
        </div>
        
        <div class="form-group">
            <label for="date">Date: <span style="font-size: 0.8rem; font-weight: normal;">(Today or future dates only)</span></label>
            <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group">
            <label for="time">Time: <span style="font-size: 0.8rem; font-weight: normal;">(Future time if today's date)</span></label>
            <input type="time" id="time" name="time" required>
        </div>
        
        <button type="submit">Add Reminder</button>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
</div>

<audio id="reminderSound" src="reminder.mp3" preload="auto"></audio>
<?php include 'theme_footer.php'; ?>
<script src="notification.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission when the page loads
    requestNotificationPermission();
    
    // Set default date to today if the date field is empty
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const form = document.querySelector('form');
    
    // Set minimum date to today
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        if (!dateInput.value) {
            dateInput.value = today;
        }
    }
    
    // Client-side validation before form submission
    if (form) {
        form.addEventListener('submit', function(event) {
            const selectedDate = dateInput.value;
            const selectedTime = timeInput.value;
            
            const now = new Date();
            const currentDate = now.toISOString().split('T')[0];
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const currentTime = `${hours}:${minutes}`;
            
            // Check if date is in the past
            if (selectedDate < currentDate) {
                event.preventDefault();
                alert('Please select a current or future date.');
                return false;
            }
            
            // Check if time is in the past for today's date
            if (selectedDate === currentDate && selectedTime < currentTime) {
                event.preventDefault();
                alert('Please select a current or future time.');
                return false;
            }
            
            return true;
        });
    }
});
</script>
</body>
</html>
<?php
// Start session at the beginning of the file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set default sound if not already set
if (!isset($_SESSION['reminder_sound'])) {
    $_SESSION['reminder_sound'] = 'reminder.mp3';
}

// Get the selected sound from session
$reminderSound = $_SESSION['reminder_sound'];

$host = "localhost";
$db = "ADDMEDICINE";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// --- AUTO DELETE PAST REMINDERS ---
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");
$sqlDeletePast = "DELETE FROM reminders WHERE date < ? OR (date = ? AND time <= ?)";
$stmtDelete = $conn->prepare($sqlDeletePast);
$stmtDelete->bind_param("sss", $currentDate, $currentDate, $currentTime);
$stmtDelete->execute();
// --- END AUTO DELETE ---

if (isset($_POST['clear'])) {
    $sqlClear = "DELETE FROM reminders WHERE date > '$currentDate' OR (date = '$currentDate' AND time > '$currentTime')";
    $conn->query($sqlClear);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT medicine_name, date, time FROM reminders WHERE date > ? OR (date = ? AND time > ?)
ORDER BY date ASC, time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $currentDate, $currentDate, $currentTime);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<?php include 'theme_header.php'; ?>
<title>Upcoming Reminders</title>
<style>
body { 
    font-family: Arial;
    padding: 20px; 
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

.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-top: 15px;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    transition: background-color 0.3s, box-shadow 0.3s;
}

body.dark-theme table {
    background-color: rgba(45, 55, 72, 0.8);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

th, td { 
    padding: 15px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

body.dark-theme th, 
body.dark-theme td {
    border-bottom: 1px solid #4a5568;
    color: #f4f4f4;
}

th { 
    background-color: #87CEEB;
    color: #333;
    font-weight: bold;
}

body.dark-theme th {
    background-color: #5a4cad;
    color: #f4f4f4;
}

tr:hover {
    background-color: #f9f9f9;
}

body.dark-theme tr:hover {
    background-color: rgba(74, 85, 104, 0.3);
}

tr:last-child td {
    border-bottom: none;
}

.home-link {
    display: inline-block;
    margin-bottom: 15px;
    background: #4c8bf5;
    color: white;
    text-align: center;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
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

.clear-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;
}

.clear-btn:hover {
    background-color: #c0392b;
}

.notification-banner {
    background-color: rgba(75, 121, 216, 0.1);
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    border-left: 4px solid #4b79d8;
    transition: background-color 0.3s;
}

body.dark-theme .notification-banner {
    background-color: rgba(75, 121, 216, 0.05);
    border-left: 4px solid #5a4cad;
}

.no-reminders {
    text-align: center;
    padding: 30px;
    color: #777;
    font-style: italic;
}

body.dark-theme .no-reminders {
    color: #a0aec0;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>
</head>
<body id="body">
<div class="container">
    <h2>Upcoming Reminders</h2>
    
    <div class="notification-banner">
        <p><strong>Note:</strong> You will be notified with sound when it's time to take your medicine. Make sure to keep this page open or visit the dashboard to receive reminders.</p>
    </div>
    
    <div class="header-actions">
        <a href="dashboard2.php" class="home-link">Home</a>
        
        <form method="post">
            <button type="submit" name="clear" class="clear-btn" onclick="return confirm('Are you sure you want to clear all upcoming reminders?');">
                Clear All Upcoming Reminders
            </button>
        </form>
    </div>

    <table>
        <tr>
            <th>Medicine</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
        <?php if ($result->num_rows > 0): 
            while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['time']); ?></td>
            </tr>
        <?php endwhile; 
        else: ?>
            <tr>
                <td colspan='3' class="no-reminders">No upcoming reminders.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<audio id="reminderSound" src="<?php echo htmlspecialchars($reminderSound); ?>" preload="auto"></audio>
<?php include 'theme_footer.php'; ?>
<script src="notification.js"></script>
<script>
// Initialize audio context with a user interaction to allow autoplay later
document.addEventListener('click', function initAudio() {
  // Try to play and immediately pause to unlock audio
  const reminderSound = document.getElementById('reminderSound');
  if (reminderSound) {
    reminderSound.volume = 0;
    reminderSound.play().then(() => {
      reminderSound.pause();
      reminderSound.currentTime = 0;
      reminderSound.volume = 1;
      console.log('Audio context unlocked');
    }).catch(e => console.log('Could not unlock audio context:', e));
  }
  document.removeEventListener('click', initAudio);
});
</script>
</body>
</html>
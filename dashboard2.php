<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session at the beginning of the file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set sound to alarm.mp3 specifically as requested
$_SESSION['reminder_sound'] = 'alarm.mp3';

// Get the selected sound from session
$reminderSound = $_SESSION['reminder_sound'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'theme_header.php'; ?>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedClock Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      height: 100vh;
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
    .sidebar {
      width: 250px;
      background-color:rgb(75, 140, 133);
      padding: 20px;
      display: flex;
      flex-direction: column;
      color: white;
      backdrop-filter: blur(2px);
    }
    .sidebar h1 {
      font-size: 1.8rem;
      margin-bottom: 2rem;
    }
    .nav-item {
      margin: 1rem 0;
      font-size: 1.1rem;
      cursor: pointer;
    }
    .nav-item:hover {
      text-decoration: underline;
    }
    .main-content {
      flex-grow: 1;
      padding: 40px;
      color: #333;
    }
    .reminder-card {
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      max-width: 500px;
      backdrop-filter: blur(2px);
      border-left: 5px solid rgb(5, 93, 84);
    }
    .reminder-card h1 {
      margin-top: 0;
      color:rgb(5, 93, 84);
      border-bottom: 2px solid rgba(5, 93, 84, 0.3);
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .reminder-details {
      font-weight:bold;
      font-size: 1.4rem;
      margin: 5px 0;
      color:#2f4f4f;
    }
    .medication-name {
      font-weight: bold;
      font-size: 1.6rem;
      color:#2f4f4f;
      margin:5px 0;
    }
    .reminder-status {
      font-weight: bold;
      font-size: 1.2rem;
      margin: 5px 0;
      padding: 5px 10px;
      border-radius: 5px;
      display: inline-block;
    }
    .reminder-status.upcoming {
      background-color: rgba(52, 152, 219, 0.2);
      color: #2980b9;
    }
    .reminder-status.taken {
      background-color: rgba(46, 204, 113, 0.2);
      color: #27ae60;
    }
    .reminder-upcoming-count {
      margin-top: 15px;
      font-size: 1rem;
      color: #555;
    }
    .view-all-link {
      color: rgb(5, 93, 84);
      text-decoration: underline;
      font-weight: bold;
    }
    .add-reminder-link {
      margin-top: 15px;
      text-align: center;
    }
    .add-reminder-link a {
      display: inline-block;
      background-color: rgb(5, 93, 84);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .add-reminder-link a:hover {
      background-color: rgb(7, 129, 116);
    }
    a {
      text-decoration: none;
      color: inherit;
      cursor: pointer;
    }
    a:hover {
      text-decoration: underline;
    }
    .header {
      position: absolute;
      top: 0;
      right: 0;
      padding: 15px 30px;
      z-index: 10;
      display: flex;
      align-items: center;
    }
    .logout-btn {
      background-color: rgba(177, 156, 217, 0.8);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: bold;
      margin-left: 15px;
    }
    .logout-btn:hover {
      background-color: rgba(157, 136, 197, 0.9);
      text-decoration: none;
    }
    .profile-container {
      position: relative;
    }
    .profile-img-container {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      background-color: rgba(177, 156, 217, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }
    .profile-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .default-profile-icon {
      font-size: 24px;
      color: white;
    }
    .profile-info {
      position: absolute;
      top: 45px;
      right: 0;
      width: 220px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      padding: 15px;
      display: none;
      z-index: 20;
    }
    .profile-container:hover .profile-info {
      display: block;
    }
    .profile-info-header {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }
    .profile-info-img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      margin-right: 10px;
      object-fit: cover;
    }
    .profile-info-name {
      font-weight: bold;
      font-size: 16px;
      color: #333;
    }
    .profile-info-detail {
      display: flex;
      margin-bottom: 8px;
      font-size: 14px;
      color: #555;
    }
    .profile-info-label {
      width: 80px;
      font-weight: bold;
    }
    .profile-info-value {
      flex: 1;
    }
    .profile-link {
      display: block;
      background-color: rgba(177, 156, 217, 0.8);
      color: white;
      text-align: center;
      padding: 8px;
      border-radius: 5px;
      margin-top: 10px;
    }
    .profile-link:hover {
      background-color: rgba(157, 136, 197, 0.9);
      text-decoration: none;
    }
  </style>
</head>
<body id="body">
  <div class="header">
    <div class="profile-container">
      <div class="profile-img-container">
        <?php
        // Check if user has a profile picture
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "profile";
        $conn = new mysqli($host, $user, $pass, $db);
        $profilePic = "";
        $name = "";
        $phone = "";
        $dob = "";
        $email = "";
        if ($conn->connect_error) {
          die(""); // Silently handle the error
        }
        // Get the latest profile information
        $sql = "SELECT name, phone, dob, email, profile_pic FROM userprofile ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $profilePic = $row['profile_pic'];
          $name = $row['name'];
          $phone = $row['phone'];
          $dob = $row['dob'];
          $email = $row['email'];
          if (!empty($profilePic)) {
            echo '<img src="uploads/' . htmlspecialchars($profilePic) . '" alt="Profile" class="profile-img">';
          } else {
            echo '<div class="default-profile-icon">👤</div>';
          }
        } else {
          echo '<div class="default-profile-icon">👤</div>';
        }
        $conn->close();
        ?>
      </div>
      <div class="profile-info">
        <div class="profile-info-header">
          <?php
          if (!empty($profilePic)) {
            echo '<img src="uploads/' . htmlspecialchars($profilePic) . '" alt="Profile" class="profile-info-img">';
          } else {
            echo '<div class="profile-img-container" style="margin-right: 10px;"><div class="default-profile-icon">👤</div></div>';
          }
          ?>
          <div class="profile-info-name"><?php echo !empty($name) ? htmlspecialchars($name) : "User Profile"; ?></div>
        </div>
        <div class="profile-info-detail">
          <div class="profile-info-label">Name:</div>
          <div class="profile-info-value"><?php echo !empty($name) ? htmlspecialchars($name) : "Not set"; ?></div>
        </div>
        <div class="profile-info-detail">
          <div class="profile-info-label">Phone:</div>
          <div class="profile-info-value"><?php echo !empty($phone) ? htmlspecialchars($phone) : "Not set"; ?></div>
        </div>
        <div class="profile-info-detail">
          <div class="profile-info-label">Date of Birth:</div>
          <div class="profile-info-value"><?php echo !empty($dob) ? htmlspecialchars($dob) : "Not set"; ?></div>
        </div>
        <div class="profile-info-detail">
          <div class="profile-info-label">Email:</div>
          <div class="profile-info-value"><?php echo !empty($email) ? htmlspecialchars($email) : "Not set"; ?></div>
        </div>
        <a href="profile.php" class="profile-link">Edit Profile</a>
      </div>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
  <div class="sidebar">
    <h1>MedClock</h1>
    <div class="nav-item"><a href="dashboard2.php">Home</a></div>
    <div class="nav-item"><a href="profile.php">Profile</a></div>
    <div class="nav-item"><a href="upcomingre.php">Upcoming Reminders</a></div>
    <div class="nav-item"><a href="addmedicine.php">Add medicine</a></div>
    <div class="nav-item"><a href="hiostory.php">History</a></div>
    <div class="nav-item"><a href="settings.php">Settings</a></div>
  </div>
  <div class="main-content">
    <div class="reminder-card" id="reminder-container">
      <div style="margin-bottom: 15px;">
        <h1 style="margin: 0;">Upcoming Reminder</h1>
      </div>
      <?php
      try {
        // Include email function - use @ to suppress warnings if file not found
        @require_once('send_reminder_email.php');
        
        // Connect to the database with error handling
        $conn = new mysqli("localhost", "root", "", "ADDMEDICINE");
        if ($conn->connect_error) {
          echo "<div style='color: red; background-color: white; padding: 10px; margin: 10px; border-radius: 5px;'>
                Connection failed: " . $conn->connect_error . "
                </div>";
        } else {
      // First, automatically mark past reminders as played
      $nowDate = date("Y-m-d");
      $nowTime = date("H:i");
      // Update past reminders silently
      $updateSql = "UPDATE reminders 
                    SET played = 1 
                    WHERE (date < ? OR (date = ? AND time < ?)) 
                    AND (played IS NULL OR played = 0)";
      $updateStmt = $conn->prepare($updateSql);
      $updateStmt->bind_param("sss", $nowDate, $nowDate, $nowTime);
      $updateStmt->execute();
      $updateStmt->close();
      // Only get upcoming reminders that haven't been taken/played yet
      // Including ID field explicitly for the reminder deletion functionality
      $sql = "SELECT id, medicine_name, date, time, played FROM reminders 
              WHERE ((date > '$nowDate') OR (date = '$nowDate' AND time >= '$nowTime')) 
              AND (played IS NULL OR played = 0) 
              ORDER BY date ASC, time ASC 
              LIMIT 1";
      $result = $conn->query($sql);
      // Check for upcoming reminders that aren't played
      $activeQuery = "SELECT COUNT(*) as active FROM reminders WHERE ((date > '$nowDate') OR (date = '$nowDate' AND time >= '$nowTime')) AND (played IS NULL OR played = 0)";
      $activeResult = $conn->query($activeQuery);
      $activeRow = $activeResult->fetch_assoc();
      $activeReminders = $activeRow['active'];
      if ($result->num_rows > 0) {
        $reminder = $result->fetch_assoc();
        echo "<div class='medication-name'>Medicine: " . htmlspecialchars($reminder['medicine_name']) . "</div>";
        echo "<div class='reminder-details'>Date: " . htmlspecialchars($reminder['date']) . "</div>";
        echo "<div class='reminder-details'>Time: " . htmlspecialchars($reminder['time']) . "</div>";
        // Check if reminder has been played/taken
        if (isset($reminder['played']) && $reminder['played'] == 1) {
            echo "<div class='reminder-status taken'>Status: Taken</div>";
        } else {
            echo "<div class='reminder-status upcoming'>Status: Upcoming</div>";
        }
        // Show total upcoming reminders
        if ($activeReminders > 1) {
            $otherReminders = $activeReminders - 1;
            echo "<div class='reminder-upcoming-count'>";
            echo "Plus $otherReminders more upcoming reminders. ";
            echo "<a href='upcomingre.php' class='view-all-link'>View all</a>";
            echo "</div>";
        }
      } else {
        echo "<div class='medication-name'>No upcoming reminders</div>";
        echo "<div class='add-reminder-link' style='margin-top: 20px; text-align: center;'>";
        echo "<a href='addmedicine.php' style='display: inline-block; background-color: rgb(5, 93, 84); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 1.1rem;'>";
        echo "<span style='margin-right: 8px;'>+</span>Add New Medicine Reminder";
        echo "</a>";
        echo "</div>";
      }
      $conn->close();
        } // Close the else clause
      } catch (Exception $e) {
        echo "<div style='color: red; background-color: white; padding: 10px; margin: 10px; border-radius: 5px;'>
              Error: " . $e->getMessage() . "
              </div>";
      }
      ?>
    </div>
  </div>
  <!-- Ensure alarm sound is properly loaded, hidden, and set to loop until notification is accepted -->
  <audio id="reminderSound" src="alarm.mp3" preload="auto" loop style="display:none;"></audio>
  <?php include 'theme_footer.php'; ?>
  <script>
  // Global variables to track sound playback
  let continuousSound = null;
  let fallbackSound = null;
  
  // Function to play a reminder sound continuously until notification is accepted
  function playReminderSound() {
    stopReminderSound(); // Stop any previously playing sounds
    
    const reminderSound = document.getElementById('reminderSound');
    if (reminderSound) {
      // Make sure the audio is loaded and volume is up
      reminderSound.volume = 1.0;
      reminderSound.currentTime = 0;
      reminderSound.loop = true; // Enable looping to play continuously
      
      console.log('Playing continuous reminder sound from: ' + reminderSound.src);
      
      // Create a promise to handle the audio playback
      const playPromise = reminderSound.play();
      continuousSound = reminderSound;
      
      if (playPromise !== undefined) {
        playPromise.then(() => {
          console.log('Sound played successfully and will continue until notification is accepted');
        }).catch(error => {
          console.error('Error playing sound:', error);
          
          // Try an alternative method to play sound
          try {
            fallbackSound = new Audio('alarm.mp3');
            fallbackSound.volume = 1.0;
            fallbackSound.loop = true;
            fallbackSound.play().catch(e => {
              console.error('Alternative sound playback failed:', e);
            });
          } catch (e) {
            console.error('Fallback sound creation failed:', e);
          }
        });
      }
    } else {
      console.error('Reminder sound element not found');
      // Try a fallback method if element not found
      try {
        fallbackSound = new Audio('alarm.mp3');
        fallbackSound.volume = 1.0;
        fallbackSound.loop = true;
        fallbackSound.play();
        console.log('Using fallback continuous sound method');
      } catch (e) {
        console.error('Fallback sound playback failed:', e);
      }
    }
  }
  
  // Function to stop all reminder sounds
  function stopReminderSound() {
    const reminderSound = document.getElementById('reminderSound');
    if (reminderSound) {
      reminderSound.pause();
      reminderSound.currentTime = 0;
    }
    
    if (fallbackSound) {
      fallbackSound.pause();
      fallbackSound = null;
    }
    
    console.log('Stopped all reminder sounds');
  }
  
  <?php if (!empty($reminder) && isset($reminder['date']) && isset($reminder['time'])): ?>
    // Ensure reminder ID is available and properly cast to integer
    var reminderId = <?php echo isset($reminder['id']) ? (int)$reminder['id'] : 0; ?>;
    var reminderDate = "<?php echo $reminder['date']; ?>";
    var reminderTime = "<?php echo $reminder['time']; ?>";
    var medicineName = "<?php echo addslashes($reminder['medicine_name']); ?>";
    var reminderDateTime = new Date(reminderDate + "T" + reminderTime);
    var now = new Date();
    var delay = reminderDateTime - now;
    
    // Validate if the timer is still valid (not outdated)
    if (delay > 0) {
      // Request notification permission if not granted
      if (Notification.permission !== "granted") {
        Notification.requestPermission();
      }
      
      console.log('Scheduled reminder for: ' + medicineName + ' at ' + reminderDateTime + ' (in ' + Math.round(delay/1000/60) + ' minutes)');
      
      setTimeout(function remindUser() {
        // Play sound first to ensure it starts immediately
        playReminderSound();
        
        // Show notification with additional properties for click handling
        if (Notification.permission === "granted") {
          var notification = new Notification("MedClock Reminder", {
            body: "Time to take your medicine: " + medicineName,
            // Use a more generic icon that's likely to exist
            icon: "Resources/med.jpg",
            tag: "reminder-" + reminderId, // Tag for identifying this notification
            requireInteraction: true, // Keep notification visible until user interacts with it
            silent: false // Ensure browser doesn't silence the notification
          });
          
          // Try playing the sound again when the notification is shown
          // This helps on some browsers/platforms
          notification.onshow = function() {
            playReminderSound();
          };
          
          // Handle notification click to mark as taken
          notification.onclick = function() {
            // Stop the continuous sound when notification is clicked
            stopReminderSound();
            
            // Double-check reminder ID is valid before trying to mark as taken
            if (reminderId && !isNaN(reminderId) && reminderId > 0) {
              console.log('Notification clicked, marking reminder as taken:', reminderId);
              markReminderTaken(reminderId);
            } else {
              console.error('Cannot mark reminder as taken: invalid ID', reminderId);
            }
            this.close();
          };
        } else {
          // If notification permission not granted, still play sound
          console.log('Notification permission not granted, playing sound only');
        }
        
        // Show alert and mark as taken when acknowledged
        var userResponse = confirm("It's time to take your medicine: " + medicineName + "\n\nClick OK if you've taken it, or Cancel to be reminded later.");
        
        // Stop the sound when user responds to the dialog (whether OK or Cancel)
        stopReminderSound();
        
        if (userResponse) {
          markReminderTaken(reminderId);
        } else {
          // If user clicked Cancel, just log that they'll be reminded later
          console.log('User chose to be reminded later for: ' + medicineName);
        }
      }, delay);
    } else if (reminderId > 0) {
      // Timer is outdated - mark as taken automatically, but only if we have a valid ID
      console.log('Reminder time has passed, marking as taken automatically: ' + medicineName);
      markReminderTaken(reminderId);
    } else {
      // We have an outdated reminder but no valid ID - just log the issue
      console.log('Reminder time has passed but no valid ID available for: ' + medicineName);
    }
  <?php endif; ?>
  
  // Function to mark a reminder as taken/completed and move it to history
  function markReminderTaken(reminderId) {
    // Check for valid reminder ID (must be a positive integer)
    if (!reminderId || isNaN(reminderId) || reminderId <= 0) {
      console.error('Invalid or missing reminder ID:', reminderId);
      return;
    }
    
    console.log('Marking reminder as taken/completed:', reminderId);
    
    // Call the mark_taken.php API to mark the reminder as taken
    fetch('mark_taken.php?id=' + reminderId)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Successfully marked reminder as taken. ID:', reminderId);
          // Reload the page after a short delay to update the UI
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          console.error('Failed to mark reminder as taken:', data.message || 'Unknown error');
        }
      })
      .catch(error => {
        console.error('Error calling mark_taken API:', error);
      });
  }
  
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
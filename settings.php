<!DOCTYPE html>
<html lang="en">
<?php include 'theme_header.php'; ?>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedClock Settings</title>
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
    body.dark-theme::before {
      filter: blur(1px) brightness(0.7);
    }
    .sidebar {
      width: 250px;
      background:rgb(88, 162, 155);;
      padding: 20px;
      display: flex;
      flex-direction: column;
      color: white;
    }
    .sidebar h1 { font-size: 1.8rem; margin-bottom: 2rem; }
    .nav-item { margin: 1rem 0; font-size: 1.1rem; cursor: pointer; }
    .nav-item:hover { text-decoration: underline; }
    .main-content {
      flex-grow: 1;
      padding: 40px;
      color: #333;
    }
    .settings-card {
      background-color: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      max-width: 500px;
      margin: 0 auto;
    }
    .settings-card h2 {
      margin-top: 0;
      color: #6a5acd;
      font-size: 1.6rem;
    }
    .settings-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }
    input[type="checkbox"], input[type="time"], select {
      margin-right: 10px;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ddd;
      background-color: white;
      color: #333;
    }
    button {
      background-color: #6a5acd;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
    }
    button:hover {
      background-color: #4b3c9d;
    }
    #soundStatus {
      margin-left: 10px;
      color: green;
      font-weight: bold;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    a:hover {
      text-decoration: underline;
    }
    .success-message {
      background-color: rgba(76, 175, 80, 0.2);
      color: #4caf50;
      padding: 10px;
      border-radius: 5px;
      margin-top: 20px;
      display: none;
    }
  </style>
</head>
<body id="body">
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
    <div class="settings-card">
      <h2>Settings</h2>
      <form id="settingsForm">
        <div class="settings-group">
          <button type="button" onclick="enableSound()" id="enableSoundBtn">Enable Reminder Sound</button>
          <span id="soundStatus"></span>
          <div style="margin-top: 15px;">
            <button type="button" onclick="testReminderSound()" style="background-color: #5a6268;">Test Sound</button>
            <span id="soundTestResult" style="margin-left: 10px; font-style: italic;"></span>
          </div>
          <div style="margin-top: 15px;">
            <a href="sound_settings.php" class="settings-link" style="display: inline-block; padding: 8px 15px; background-color: #6a5acd; color: white; border-radius: 8px; text-decoration: none;">
              Customize Reminder Sound
            </a>
          </div>
        </div>
        <div class="settings-group">
          <label for="reminderTime">Default Reminder Time:</label>
          <input type="time" id="reminderTime" name="reminderTime" value="08:00">
        </div>
        <div class="settings-group">
          <label for="theme">Theme:</label>
          <select id="theme" name="theme" onchange="applyTheme(this.value)">
            <option value="light">Light</option>
            <option value="dark">Dark</option>
          </select>
        </div>
        <button type="button" onclick="saveSettings()">Save Settings</button>
        <div class="success-message" id="successMessage">Settings saved successfully!</div>
      </form>
    </div>
  </div>
  <audio id="reminderSound" src="alarm.mp3" preload="auto" style="display:none;"></audio>
  <?php include 'theme_footer.php'; ?>
  <script>
    // Apply saved theme on page load
    window.onload = function() {
      loadSettings();

      if(localStorage.getItem('medclockSoundEnabled') === 'yes') {
        document.getElementById('soundStatus').textContent = "Sound enabled!";
        document.getElementById('enableSoundBtn').style.display = 'none';
      }
    }

    function loadSettings() {
      // Load theme setting
      const savedTheme = localStorage.getItem('medclockTheme') || 'light';
      document.getElementById('theme').value = savedTheme;
      applyTheme(savedTheme);

      // Load reminder time
      const savedTime = localStorage.getItem('medclockReminderTime') || '08:00';
      document.getElementById('reminderTime').value = savedTime;
    }

    function saveSettings() {
      // Save theme setting
      const theme = document.getElementById('theme').value;
      localStorage.setItem('medclockTheme', theme);

      // Save reminder time
      const newReminderTime = document.getElementById('reminderTime').value;
      localStorage.setItem('medclockReminderTime', newReminderTime);

      // Always play sound when saving reminder time
      // let audio = document.getElementById('reminderSound');
      // if (audio) {
      //   audio.pause();
      //   audio.currentTime = 0;
      //   audio.volume = 1.0;
      //   audio.play().catch(error => {
      //     // For browsers with autoplay restrictions, prompt user
      //     alert("Please interact with the page to enable sound.");
      //   });
      // }

      // Show success message
      const successMsg = document.getElementById('successMessage');
      successMsg.style.display = 'block';

      // Hide message after 3 seconds
      setTimeout(() => {
        successMsg.style.display = 'none';
      }, 3000);
    }

    function enableSound() {
      let audio = document.getElementById('reminderSound');
      audio.play().then(() => {
        localStorage.setItem('medclockSoundEnabled', 'yes');
        document.getElementById('soundStatus').textContent = "Sound enabled!";
        document.getElementById('enableSoundBtn').style.display = 'none';
      }).catch(() => {
        alert("Please click again to enable sound.");
      });
    }

    // ...existing JS code...

// Schedule daily reminder sound at the saved time
function scheduleDailyReminderSound() {
  const reminderTime = localStorage.getItem('medclockReminderTime') || '08:00';
  const [hours, minutes] = reminderTime.split(':').map(Number);

  function setTimer() {
    const now = new Date();
    const target = new Date();
    target.setHours(hours, minutes, 0, 0);

    // If the target time has already passed today, set for tomorrow
    if (target <= now) {
      target.setDate(target.getDate() + 1);
    }

    const delay = target - now;
    setTimeout(() => {
      // Play the sound
      let audio = document.getElementById('reminderSound');
      if (audio) {
        audio.pause();
        audio.currentTime = 0;
        audio.volume = 1.0;
        audio.play().catch(() => {
          alert("Please interact with the page to enable sound.");
        });
      }
      // Schedule for the next day
      setTimer();
    }, delay);
  }

  setTimer();
}

scheduleDailyReminderSound();

    function testReminderSound() {
      const resultSpan = document.getElementById('soundTestResult');
      resultSpan.textContent = "Playing sound...";

      // Force enable sound for this test
      localStorage.setItem('medclockSoundEnabled', 'yes');
      document.getElementById('soundStatus').textContent = "Sound enabled!";
      document.getElementById('enableSoundBtn').style.display = 'none';

      let audio = document.getElementById('reminderSound');
      if (!audio) {
        resultSpan.textContent = "Error: Audio element not found!";
        return;
      }

      audio.pause();
      audio.currentTime = 0;
      audio.volume = 1.0;

      audio.play().then(() => {
        resultSpan.textContent = "Success! Sound is working.";
        resultSpan.style.color = "green";
        audio.onended = function() {
          setTimeout(() => {
            resultSpan.textContent = "";
          }, 2000);
        };
      }).catch(error => {
        resultSpan.textContent = "Error: " + error.message;
        resultSpan.style.color = "red";
        alert("Sound playback failed. Please make sure your device volume is not muted and try again.");
      });
    }
  </script>
</body>
</html>
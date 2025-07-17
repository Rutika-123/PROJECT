<?php
// sound_settings.php - Allows users to select their preferred reminder sound

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define available sounds
$available_sounds = [
    'alarm.mp3' => 'Default Alarm',
    'custom_reminder.mp3' => 'Bell Sound',
    'alarm_sound.mp3' => 'Alternative Alarm'
];

// Get the current sound from session or use default - now using alarm.mp3 as default
$current_sound = isset($_SESSION['reminder_sound']) && array_key_exists($_SESSION['reminder_sound'], $available_sounds) 
    ? $_SESSION['reminder_sound'] 
    : 'alarm.mp3';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sound']) && array_key_exists($_POST['sound'], $available_sounds)) {
        $_SESSION['reminder_sound'] = $_POST['sound'];
        $current_sound = $_POST['sound'];
    } else {
        // Reset to default (alarm.mp3) if invalid selection
        $_SESSION['reminder_sound'] = 'alarm.mp3';
        $current_sound = 'alarm.mp3';
    }
}

// Debug: Log current session state
error_log("Current session reminder_sound: " . (isset($_SESSION['reminder_sound']) ? $_SESSION['reminder_sound'] : 'not set'));

// Debug: Log final sound setting
error_log("Final sound setting: " . $current_sound);

// Verify sound files exist
foreach ($available_sounds as $sound_file => $sound_name) {
    if (!file_exists($sound_file)) {
        error_log("WARNING: Sound file missing: " . $sound_file);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sound Settings - MedClock</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: url('Resources/medicine.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        .sound-option {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .sound-option:hover {
            background-color: #e9ecef;
        }
        .sound-option input[type="radio"] {
            margin-right: 15px;
        }
        .sound-option label {
            flex-grow: 1;
            font-size: 16px;
            color: #2c3e50;
        }
        .play-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .play-button:hover {
            background-color: #2980b9;
        }
        .save-button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .save-button:hover {
            background-color: #27ae60;
        }
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sound Settings</h1>
        <form method="POST" action="">
            <?php foreach ($available_sounds as $sound_file => $sound_name): ?>
                <div class="sound-option">
                    <input type="radio" name="sound" value="<?php echo htmlspecialchars($sound_file); ?>" 
                           id="<?php echo htmlspecialchars($sound_file); ?>"
                           <?php echo $current_sound === $sound_file ? 'checked' : ''; ?>>
                    <label for="<?php echo htmlspecialchars($sound_file); ?>">
                        <?php echo htmlspecialchars($sound_name); ?>
                    </label>
                    <button type="button" class="play-button" onclick="testSound('<?php echo htmlspecialchars($sound_file); ?>')">
                        Test Sound
                    </button>
                    <div class="error-message" id="error-<?php echo htmlspecialchars($sound_file); ?>">
                        Sound file not found
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="save-button">Save Settings</button>
        </form>
        <a href="settings.php" class="back-link">← Back to Settings</a>
    </div>

    <script>
    // Function to test sound
    function testSound(soundFile) {
        console.log('Testing sound:', soundFile);
        
        // Create audio element
        const audio = new Audio(soundFile);
        audio.volume = 1.0;
        
        // Play the sound
        const playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('Sound played successfully');
                // Hide error message if it was shown
                const errorElement = document.getElementById('error-' + soundFile);
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }).catch(error => {
                console.error('Error playing sound:', error);
                // Show error message
                const errorElement = document.getElementById('error-' + soundFile);
                if (errorElement) {
                    errorElement.style.display = 'block';
                }
                
                // Try alternative method
                const newAudio = new Audio(soundFile);
                newAudio.volume = 1.0;
                newAudio.play().catch(e => {
                    console.error('Still failed to play sound:', e);
                    // Open dedicated sound player as last resort
                    window.open('play_reminder_sound.php?sound=' + encodeURIComponent(soundFile), '_blank', 'width=500,height=400');
                });
            });
        }
    }

    // Check if sound files exist when page loads
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($available_sounds as $sound_file => $sound_name): ?>
        fetch('<?php echo htmlspecialchars($sound_file); ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Sound file not found');
                }
            })
            .catch(error => {
                console.error('Error checking sound file:', error);
                const errorElement = document.getElementById('error-<?php echo htmlspecialchars($sound_file); ?>');
                if (errorElement) {
                    errorElement.style.display = 'block';
                }
            });
        <?php endforeach; ?>
    });
    </script>
</body>
</html> 
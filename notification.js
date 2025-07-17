// Notification System for MedClock

// Check if service workers are supported
function serviceWorkerSupported() {
  return 'serviceWorker' in navigator;
}

// Check if notification is supported
function notificationSupported() {
  return 'Notification' in window;
}

// Register service worker for background notifications
function registerServiceWorker() {
  if (!serviceWorkerSupported()) {
    console.log('Service Workers not supported in this browser');
    return Promise.resolve(false);
  }
  
  return navigator.serviceWorker.register('medclock-sw.js')
    .then(registration => {
      console.log('Service Worker registered with scope:', registration.scope);
      return true;
    })
    .catch(error => {
      console.error('Service Worker registration failed:', error);
      return false;
    });
}

// Request notification permission
function requestNotificationPermission() {
  if (!notificationSupported()) {
    console.log('Notifications not supported in this browser');
    return Promise.resolve(false);
  }
  
  if (Notification.permission === 'granted') {
    return Promise.resolve(true);
  }
  
  if (Notification.permission !== 'denied') {
    return Notification.requestPermission().then(permission => {
      return permission === 'granted';
    });
  }
  
  return Promise.resolve(false);
}

// Always enable sound by default to ensure reminders work
function ensureSoundEnabled() {
  if (!localStorage.getItem('medclockSoundEnabled')) {
    localStorage.setItem('medclockSoundEnabled', 'yes');
    console.log('Sound automatically enabled for reminders');
  }
}

// Check if sound is enabled
function isSoundEnabled() {
  // Default to true if not explicitly set to ensure sound works
  return localStorage.getItem('medclockSoundEnabled') !== 'no';
}

// Function to play the reminder sound
function playReminderSound() {
    console.log('Playing reminder sound...');
    
    // Get sound preference from session if available
    let soundFile = 'reminder.mp3'; // Default sound
    
    // Try to get the sound from the hidden element if available
    const soundElement = document.getElementById('reminderSound');
    if (soundElement && soundElement.src) {
        // Extract filename from the src attribute
        const srcParts = soundElement.src.split('/');
        soundFile = srcParts[srcParts.length - 1];
    }
    
    console.log('Using sound file:', soundFile);
    
    // Create audio element with the sound
    const audio = new Audio(soundFile);
    audio.volume = 1.0;
    
    // Play the sound
    const playPromise = audio.play();
    if (playPromise !== undefined) {
        playPromise.then(() => {
            console.log('Sound played successfully');
        }).catch(error => {
            console.error('Error playing sound:', error);
            // Try alternative method with a different sound file
            const alternativeSounds = ['alarm.mp3', 'alarm_sound.mp3', 'custom_reminder.mp3', 'reminder.mp3'];
            // Use a different sound file than the one that failed
            const currentIndex = alternativeSounds.indexOf(soundFile);
            const nextIndex = (currentIndex + 1) % alternativeSounds.length;
            const alternativeSound = alternativeSounds[nextIndex];
            
            console.log('Trying alternative sound:', alternativeSound);
            const newAudio = new Audio(alternativeSound);
            newAudio.volume = 1.0;
            newAudio.play().catch(e => {
                console.error('Still failed to play sound:', e);
                // Try opening in a new window as last resort
                window.open('play_reminder_sound.php', '_blank', 'width=500,height=400');
            });
        });
    }
}

// Helper function to get medicine name from the current context
function getMedicineNameFromContext() {
  // Try to find medicine name in various places
  
  // 1. Check for a global variable
  if (typeof medicineName !== 'undefined') {
    return medicineName;
  }
  
  // 2. Check for medicine name in the DOM
  const medicineElement = document.querySelector('.medication-name');
  if (medicineElement) {
    const text = medicineElement.textContent || '';
    const match = text.match(/Medicine:\s*(.+)/i);
    if (match && match[1]) {
      return match[1].trim();
    }
  }
  
  // 3. Check for medicine name in table rows
  const medicineCell = document.querySelector('table tr td:first-child');
  if (medicineCell) {
    return medicineCell.textContent.trim();
  }
  
  return null;
}

// Function to show medicine notification
function showMedicineNotification(medicineName) {
    console.log('Showing notification for:', medicineName);
    
    // Play sound first
    playReminderSound();
    
    // Show notification
    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            const notification = new Notification('Medicine Reminder', {
                body: `Time to take ${medicineName}!`,
                icon: 'Resources/medicine.jpg'
            });
            
            // Close notification after 5 seconds
            setTimeout(() => {
                notification.close();
            }, 5000);
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    showMedicineNotification(medicineName);
                }
            });
        }
    } else {
        alert(`Time to take ${medicineName}!`);
    }
}

// Function to check for reminders
function checkReminders() {
    console.log('Checking reminders...');
    
    // Get current date and time
    const now = new Date();
    const currentTime = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' });
    const currentDate = now.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });
    
    console.log('Current time:', currentTime);
    console.log('Current date:', currentDate);
    
    // Check reminders from the table
    const reminderRows = document.querySelectorAll('#reminderTable tbody tr');
    reminderRows.forEach(row => {
        const timeCell = row.querySelector('td:nth-child(2)');
        const dateCell = row.querySelector('td:nth-child(3)');
        const medicineCell = row.querySelector('td:nth-child(1)');
        
        if (timeCell && dateCell && medicineCell) {
            const reminderTime = timeCell.textContent.trim();
            const reminderDate = dateCell.textContent.trim();
            
            if (reminderTime === currentTime && reminderDate === currentDate) {
                console.log('Found matching reminder:', medicineCell.textContent);
                showMedicineNotification(medicineCell.textContent);
            }
        }
    });
    
    // Also check from server
    fetch('check_reminders.php')
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            if (data.active) {
                console.log('Active reminder found:', data.medicine);
                showMedicineNotification(data.medicine);
            }
        })
        .catch(error => {
            console.error('Error checking reminders:', error);
        });
}

// Initialize reminder checking
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    if ('Notification' in window) {
        Notification.requestPermission();
    }
    
    // Check reminders every minute
    setInterval(checkReminders, 60000);
    
    // Also check immediately
    checkReminders();
    
    // Get the available sound file
    let soundFile = 'reminder.mp3';
    const soundElement = document.getElementById('reminderSound');
    if (soundElement && soundElement.src) {
        const srcParts = soundElement.src.split('/');
        soundFile = srcParts[srcParts.length - 1];
    }
    
    // Add click event listener to unlock audio
    document.addEventListener('click', function() {
        // Using the actual sound file for better compatibility
        const audio = new Audio(soundFile);
        audio.volume = 0;
        audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
            console.log('Audio context unlocked successfully');
        }).catch(error => {
            console.error('Error unlocking audio:', error);
        });
    }, { once: true });
});

// Schedule a background notification if service worker is available
function scheduleBackgroundNotification(medicineName, date, time) {
  if (!serviceWorkerSupported() || !navigator.serviceWorker.controller) {
    return;
  }
  
  // Calculate when to show the notification
  const now = new Date();
  const reminderTime = new Date();
  
  // Set the date parts
  if (date) {
    const [year, month, day] = date.split('-');
    reminderTime.setFullYear(parseInt(year));
    reminderTime.setMonth(parseInt(month) - 1); // JS months are 0-indexed
    reminderTime.setDate(parseInt(day));
  }
  
  // Set the time parts
  if (time) {
    const [hours, minutes] = time.split(':');
    reminderTime.setHours(parseInt(hours));
    reminderTime.setMinutes(parseInt(minutes));
    reminderTime.setSeconds(0);
  }
  
  // If time is in the past for today, don't schedule
  if (reminderTime < now) {
    return;
  }
  
  const delayMs = reminderTime.getTime() - now.getTime();
  
  // Send message to service worker to schedule a notification
  navigator.serviceWorker.controller.postMessage({
    action: 'schedule-notification',
    title: 'MedClock Reminder',
    body: `Time to take your medicine: ${medicineName}`,
    icon: 'Resources/med.jpg',
    badge: 'Resources/med.jpg',
    timestamp: reminderTime.getTime(),
    delay: delayMs
  });
  
  console.log(`Scheduled background notification for ${medicineName} at ${date} ${time} (${Math.round(delayMs/1000/60)} minutes from now)`);
}

// Fetch upcoming reminders for background notification scheduling
function fetchUpcomingReminders() {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'get_upcoming_reminders.php', true);
  
  xhr.onload = function() {
    if (this.status === 200) {
      try {
        const reminders = JSON.parse(this.responseText);
        console.log(`Scheduling ${reminders.length} upcoming reminders for background notifications`);
        
        // Schedule each reminder
        reminders.forEach(reminder => {
          scheduleBackgroundNotification(reminder.medicine_name, reminder.date, reminder.time);
        });
      } catch (e) {
        console.error('Error parsing upcoming reminders:', e);
      }
    }
  };
  
  xhr.send();
}

// Export functions for use in other files
export { playReminderSound, showMedicineNotification, checkReminders }; 
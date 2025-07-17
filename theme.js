// Apply theme on page load
document.addEventListener('DOMContentLoaded', function() {
  const savedTheme = localStorage.getItem('medclockTheme') || 'light';
  applyTheme(savedTheme);
});

// Function to apply theme
function applyTheme(theme) {
  if (theme === 'dark') {
    document.body.classList.add('dark-theme');
  } else {
    document.body.classList.remove('dark-theme');
  }
}

// Function to toggle theme
function toggleTheme() {
  const currentTheme = localStorage.getItem('medclockTheme') || 'light';
  const newTheme = currentTheme === 'light' ? 'dark' : 'light';
  
  localStorage.setItem('medclockTheme', newTheme);
  applyTheme(newTheme);
  
  // If on settings page, update the select element
  const themeSelect = document.getElementById('theme');
  if (themeSelect) {
    themeSelect.value = newTheme;
  }
  
  return newTheme;
} 
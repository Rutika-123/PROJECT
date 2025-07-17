<?php
/**
 * Theme Header Include
 * Include this file at the top of all PHP files to add dark/light theme support.
 * 
 * Usage: 
 * <?php include 'theme_header.php'; ?>
 * <head>
 *   ...your head content...
 * </head>
 */
?>
<!-- Theme Support -->
<link rel="stylesheet" href="theme.css">
<script>
// Add this immediately to avoid flash of unstyled content
(function() {
  const savedTheme = localStorage.getItem('medclockTheme') || 'light';
  if (savedTheme === 'dark') {
    document.documentElement.classList.add('dark-theme');
  }
})();
</script>
<!-- End Theme Support --> 
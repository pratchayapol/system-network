<?php
// Start the session
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the specified URL
header("Location: https://system-network.pcnone.com/");
exit(); // Make sure to call exit after header redirection
?>

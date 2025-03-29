<?php
// logout.php

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.php");
exit();
?>
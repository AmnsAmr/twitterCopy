<?php
// backend/delete_account.php

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to delete your account.");
}

// Include database connection
require_once 'connection.php';

// Delete the user's account
try {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    // Destroy the session and redirect to the login page
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("Oops! Something went wrong. Please try again later.");
} finally {
    $stmt->close();
}
?>
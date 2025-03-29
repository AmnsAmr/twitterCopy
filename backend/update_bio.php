<?php
// backend/update_bio.php

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to update your bio.");
}

// Include database connection
require_once 'connection.php';

// Get the new bio from the form
$bio = $_POST['bio'];

// Update the bio in the database
try {
    $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE user_id = ?");
    $stmt->bind_param("si", $bio, $_SESSION['user_id']);
    $stmt->execute();

    // Redirect back to the profile page
    header("Location: ../profile.php");
    exit();
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("Oops! Something went wrong. Please try again later.");
} finally {
    $stmt->close();
}
?>
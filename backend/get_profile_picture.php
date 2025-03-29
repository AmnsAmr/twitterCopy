<?php
// backend/get_profile_picture.php

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Return a default image
    header("Content-Type: image/png");
    readfile("../images/profile_icon.png");
    exit();
}

// Fetch the image from the database
try {
    $stmt = $conn->prepare("SELECT profile_picture, mime_type FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($profile_picture, $mime_type);
    $stmt->fetch();

    // Check if profile picture exists and is binary data
    if ($profile_picture && strlen($profile_picture) > 255) {
        // It's binary data, output it with the correct mime type
        header("Content-Type: " . ($mime_type ?: 'image/jpeg'));
        echo $profile_picture;
    } else {
        // Return a default image
        header("Content-Type: image/png");
        readfile("../images/profile_icon.png");
    }
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    // Return a default image
    header("Content-Type: image/png");
    readfile("../images/profile_icon.png");
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
}
?>
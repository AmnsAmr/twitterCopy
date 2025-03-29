<?php
// backend/get_banner_picture.php

session_start();
require_once 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch the banner image from the database
try {
    $stmt = $conn->prepare("SELECT banner_image FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($banner_image);
        $stmt->fetch();
        
        if ($banner_image) {
            // Set the content type header (adjust if necessary)
            header("Content-Type: image/jpeg"); // Change to image/png or image/gif if needed
            echo $banner_image;
        } else {
            // Send a default banner if no banner is set
            header("Content-Type: image/png");
            readfile("../images/default_banner.png");
        }
    } else {
        // Redirect if no user is found
        header("Location: ../login.php");
    }
    
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error loading banner.";
}
?>

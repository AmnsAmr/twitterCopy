<?php
// upload_banner.php

session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to upload a banner.");
}

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_image'])) {
    $file = $_FILES['banner_image'];

    // Validate the file
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validate file type and size
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
            // Read the file content as binary data
            $bannerData = file_get_contents($file['tmp_name']);

            // Update the banner image in the database
            try {
                $stmt = $conn->prepare("UPDATE users SET banner_image = ? WHERE user_id = ?");
                $stmt->bind_param("si", $bannerData, $_SESSION['user_id']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Redirect back to the profile page
                    header("Location:../profile.php");
                    exit();
                } else {
                    die("No rows were updated. Please check if the user ID is correct.");
                }
            } catch (mysqli_sql_exception $e) {
                error_log("Database error: " . $e->getMessage());
                die("Oops! Something went wrong. Please try again later.");
            } finally {
                $stmt->close();
            }
        } else {
            die("Invalid file type or size. Only JPEG, PNG, and GIF files up to 5MB are allowed.");
        }
    } else {
        die("File upload error. Error code: " . $file['error']);
    }
} else {
    header("Location:../profile.php");
    exit();
}
?>
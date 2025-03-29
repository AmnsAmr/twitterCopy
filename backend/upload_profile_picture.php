<?php
// backend/upload_profile_picture.php

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to upload a profile picture.");
}

// Include database connection
require_once 'connection.php';

// Check if a file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    // Validate the file
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        // Check file type and size
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            // Read the file content
            $image_data = file_get_contents($file['tmp_name']);
            $mime_type = $file['type'];

            // Update the profile_picture column in the database
            try {
                // Update both the image data and mime type
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ?, mime_type = ? WHERE user_id = ?");
                $stmt->bind_param("ssi", $image_data, $mime_type, $_SESSION['user_id']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Success - redirect to the profile page
                    header("Location: ../profile.php?upload=success");
                } else {
                    // No rows affected - user might not exist
                    die("Failed to update profile picture. User not found.");
                }
            } catch (mysqli_sql_exception $e) {
                error_log("Database error: " . $e->getMessage());
                die("Oops! Something went wrong. Please try again later.");
            } finally {
                $stmt->close();
            }
        } else {
            die("Invalid file type or size. Only JPEG, PNG, and GIF files up to 2MB are allowed.");
        }
    } else {
        // Map error codes to messages
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
        ];
        
        $error_message = isset($error_messages[$file['error']]) ? $error_messages[$file['error']] : "Unknown upload error.";
        die("File upload error: " . $error_message);
    }
} else {
    // Redirect if no file was uploaded
    header("Location: ../profile.php");
    exit();
}
?>
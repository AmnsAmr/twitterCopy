<?php
session_start();
require_once 'connection.php';

// Configuration
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $content = trim($_POST['content']);
    $media_filename = null;
    $media_size = null;
    $media_mime_type = null;
    $media_data = null;
    $media_thumbnail = null;

    // Validate content or media
    if (empty($content) && empty($_FILES['media']['name'])) {
        $_SESSION['error'] = "Tweet content or media is required!";
        header("Location: ../index.php");
        exit();
    }

    // Process media upload if exists
    if (!empty($_FILES['media']['name'])) {
        $file = $_FILES['media'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "File upload error!";
            header("Location: ../index.php");
            exit();
        }
        
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Invalid file type! Only images and MP4 videos allowed.";
            header("Location: ../index.php");
            exit();
        }
        
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "File too large! Max 5MB allowed.";
            header("Location: ../index.php");
            exit();
        }
        
        // Store all file data
        $media_filename = $file['name'];
        $media_size = $file['size'];
        $media_mime_type = $file['type'];
        $media_data = file_get_contents($file['tmp_name']);
        
        // For video thumbnails (simple placeholder - implement properly later)
        if (strpos($media_mime_type, 'video/') === 0) {
            $media_thumbnail = 'data:image/png;base64,...'; // Base64 placeholder
        }
    }

    try {
        // Insert tweet into database
        $stmt = $conn->prepare("
            INSERT INTO posts 
            (user_id, content, timestamp, media_filename, media_size, media_mime_type, media_thumbnail, media_data)
            VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)
        ");
    
        // Bind parameters with dummy values for BLOBs
        $dummyThumb = null;
        $dummyData = null;
        $stmt->bind_param("issisbb", 
            $user_id,
            $content,
            $media_filename,
            $media_size,
            $media_mime_type,
            $dummyThumb,
            $dummyData
        );
    
        // Send BLOB data using send_long_data
        if ($media_thumbnail !== null) {
            // If it's a base64 string (for videos), decode it
            if (strpos($media_thumbnail, 'base64') !== false) {
                $base64Data = substr($media_thumbnail, strpos($media_thumbnail, ',') + 1);
                $media_thumbnail = base64_decode($base64Data);
            }
            $stmt->send_long_data(5, $media_thumbnail); // 5th parameter (0-based index 5)
        }
        if ($media_data !== null) {
            $stmt->send_long_data(6, $media_data); // 6th parameter (0-based index 6)
        }
    
        $stmt->execute();
    
        $_SESSION['success'] = "Post created successfully!";
    } catch (Exception $e) {
        error_log("Error posting tweet: " . $e->getMessage());
        $_SESSION['error'] = "Failed to create post. Please try again.";
    } finally {
        $stmt->close();
        header("Location: ../index.php");
        exit();
    }
}
?>
<?php
require_once 'connection.php';

if (isset($_GET['post_id'])) {
    $stmt = $conn->prepare("
        SELECT media_data, media_mime_type 
        FROM posts 
        WHERE post_id = ?
    ");
    $stmt->bind_param("i", $_GET['post_id']);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($media_data, $mime);
        $stmt->fetch();
        
        header("Content-Type: ".$mime);
        echo $media_data;
        exit();
    }
}

// Fallback if no media
header("HTTP/1.0 404 Not Found");
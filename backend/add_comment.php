<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['content'], $_SESSION['user_id'])) {
    $postId = (int)$_POST['post_id'];
    $userId = (int)$_SESSION['user_id'];
    $content = trim(htmlspecialchars($_POST['content']));

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $postId, $userId, $content);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Comment posted successfully';
        } else {
            $response['message'] = 'Database error: Failed to post comment';
        }
    } else {
        $response['message'] = 'Comment cannot be empty';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
exit();
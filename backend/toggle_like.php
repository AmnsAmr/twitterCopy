<?php
session_start(); // MUST BE AT THE TOP
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['post_id']) || !isset($_SESSION['user_id'])) {
            throw new Exception('Missing parameters');
        }

        $postId = (int)$_POST['post_id'];
        $userId = (int)$_SESSION['user_id'];

        // Check if like exists
        $check = $conn->prepare("SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?");
        $check->bind_param("ii", $postId, $userId);
        $check->execute();
        $isLiked = $check->get_result()->num_rows > 0;

        // Toggle like
        if ($isLiked) {
            $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        } else {
            $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        }
        $stmt->bind_param("ii", $postId, $userId);
        $stmt->execute();

        // Get updated count
        $countStmt = $conn->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = ?");
        $countStmt->bind_param("i", $postId);
        $countStmt->execute();
        $count = $countStmt->get_result()->fetch_assoc()['count'];

        echo json_encode([
            'success' => true,
            'liked' => !$isLiked,
            'newCount' => $count
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
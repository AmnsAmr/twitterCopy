<?php
// backend/get_tweets_page.php
session_start();
require_once 'connection.php';
require_once 'get_tweet.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Fetch tweets HTML
$html = getAllTweets($conn, $page, $limit);

// Calculate total posts to determine if more exist
if ($currentUserId > 0) {
    $countQuery = "SELECT COUNT(DISTINCT p.post_id) AS total 
                   FROM posts p
                   WHERE p.user_id IN (SELECT followed_id FROM follows WHERE follower_id = ?) OR p.user_id = ?";
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("ii", $currentUserId, $currentUserId);
} else {
    $countQuery = "SELECT COUNT(DISTINCT p.post_id) AS total FROM posts p";
    $stmt = $conn->prepare($countQuery);
}

$stmt->execute();
$result = $stmt->get_result();
$total = $result->fetch_assoc()['total'];
$hasMore = ($page * $limit) < $total;

header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'hasMore' => $hasMore
]);
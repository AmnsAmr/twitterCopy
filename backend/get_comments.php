<?php
require_once 'connection.php';

if (isset($_GET['post_id'])) {
    $postId = (int)$_GET['post_id'];
    
    $stmt = $conn->prepare("
        SELECT c.*, u.username 
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="comment">';
            echo '<div class="comment-header">';
            echo '<span class="username">@'.htmlspecialchars($row['username']).'</span>';
            echo '<span class="timestamp">'.date('M j, Y g:i a', strtotime($row['created_at'])).'</span>';
            echo '</div>';
            echo '<div class="comment-content">'.nl2br(htmlspecialchars($row['content'])).'</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-comments">No comments yet. Be the first to comment!</div>';
    }
} else {
    echo '<div class="error">Post ID not specified</div>';
}
?>
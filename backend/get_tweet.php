<?php
require_once "connection.php";

function getAllTweets($conn, $page = 1, $limit = 10, $targetUserId = null) {
    $output = '';
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $offset = ($page - 1) * $limit;

    // Base query to get tweets with like and media information
    $query = "SELECT p.*, u.username, u.user_id,
              COUNT(l.user_id) AS like_count,
              SUM(l.user_id = ?) AS user_liked
              FROM posts p
              JOIN users u ON p.user_id = u.user_id
              LEFT JOIN likes l ON p.post_id = l.post_id";

    $params = [$currentUserId];
    $types = 'i';

    if ($targetUserId !== null) {
        $query .= " WHERE p.user_id = ?";
        $params[] = $targetUserId;
        $types .= 'i';
    }

    $query .= " GROUP BY p.post_id ORDER BY p.timestamp DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($query);
    
    // Bind parameters dynamically
    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key];
    }
    array_unshift($refs, $types);
    call_user_func_array([$stmt, 'bind_param'], $refs);

    $stmt->execute();
    $result = $stmt->get_result();

    while ($tweet = $result->fetch_assoc()) {
        // Escape all output
        $username = htmlspecialchars($tweet['username']);
        $content = nl2br(htmlspecialchars($tweet['content']));
        $createdAt = date('M j, Y g:i a', strtotime($tweet['timestamp']));
        $likeCount = (int)$tweet['like_count'];
        $userLiked = (bool)$tweet['user_liked'];
        $tweetId = (int)$tweet['post_id'];
        $userId = (int)$tweet['user_id'];

        // Media handling
        $media = '';
        if (!empty($tweet['media_data'])) {
            $base64 = base64_encode($tweet['media_data']);
            $dataUri = 'data:' . $tweet['media_mime_type'] . ';base64,' . $base64;
            if (strpos($tweet['media_mime_type'], 'image/') === 0) {
                $media = '<img src="'.$dataUri.'" class="tweet-media">';
            } elseif (strpos($tweet['media_mime_type'], 'video/') === 0) {
                $media = '<video controls class="tweet-media"><source src="'.$dataUri.'" type="'.$tweet['media_mime_type'].'"></video>';
            }
        }

        // Build tweet HTML
        $output .= '<div class="tweet">';
        $output .= '<div class="tweet-header">';
        $output .= '<div class="author">';
        $output .= '<img src="backend/get_profile_picture.php?user_id='.$userId.'" alt="Profile" class="profile-pic">';
        $output .= '<span class="username">@'.$username.'</span>';
        $output .= '</div>';
        $output .= '<span class="date">'.$createdAt.'</span>';
        $output .= '</div>';
        $output .= '<div class="content">'.$content.'</div>';
        
        // Add media if present
        if ($media) {
            $output .= '<div class="tweet-media-container">'.$media.'</div>';
        }

        // Tweet actions (like/comment)
        $output .= '<div class="tweet-actions">';
        $output .= '<button class="like-btn" data-post-id="'.$tweetId.'" data-liked="'.($userLiked ? 'true' : 'false').'">';
        $output .= ($userLiked ? '❤️' : '🤍').' <span class="like-count">'.$likeCount.'</span>';
        $output .= '</button>';
        $output .= '<button class="comment-btn" data-post-id="'.$tweetId.'">
              <img src="images/comment_icon.png" alt="Comment" class="comment-pic">
              <span class="comment-text">Comment</span>
            </button>';
        $output .= '</div>';

        // Comment form (only for logged in users)
        if ($currentUserId > 0) {
            $output .= '<form class="comment-form" method="POST" action="backend/add_comment.php">';
            $output .= '<input type="hidden" name="post_id" value="'.$tweetId.'">';
            $output .= '<textarea name="content" placeholder="Write a comment..." rows="2" required></textarea>';
            $output .= '<input type="submit" value="Post Comment">';
            $output .= '</form>';
        }

        // Empty comments container (will load via AJAX when clicked)
        $output .= '<div class="comments-container" id="comments-'.$tweetId.'"></div>';
        $output .= '</div>'; // Close tweet
    }

    return $output ?: '<div class="no-tweets">No tweets found.</div>';
}

function getCommentsForPost($conn, $postId) {
    $stmt = $conn->prepare("SELECT c.*, u.username, u.user_id 
                          FROM comments c
                          JOIN users u ON c.user_id = u.user_id
                          WHERE c.post_id = ?
                          ORDER BY c.created_at ASC");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
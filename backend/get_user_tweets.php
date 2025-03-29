<?php
function getUserTweets($conn, $userId) {
    try {
        $query = "
            SELECT p.*, u.username 
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.user_id = ?
            ORDER BY p.timestamp DESC
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '';
        while ($row = $result->fetch_assoc()) {
            $media = '';
            if (!empty($row['media_data'])) {
                $base64 = base64_encode($row['media_data']);
                $dataUri = 'data:' . $row['media_mime_type'] . ';base64,' . $base64;
                
                if (strpos($row['media_mime_type'], 'image/') === 0) {
                    $media = '<img src="'.$dataUri.'" class="tweet-media">';
                } else if (strpos($row['media_mime_type'], 'video/') === 0) {
                    $media = '
                    <video controls class="tweet-media">
                        <source src="'.$dataUri.'" type="'.$row['media_mime_type'].'">
                        Your browser does not support the video tag.
                    </video>
                    ';
                }
            }
            
            $output .= '
            <div class="tweet">
                <div class="tweet-header">
                    <span class="username">@'.htmlspecialchars($row['username']).'</span>
                    <span class="timestamp">'.date('M j, Y g:i a', strtotime($row['timestamp'])).'</span>
                </div>
                <div class="tweet-content">'.nl2br(htmlspecialchars($row['content'])).'</div>
                '.$media.'
            </div>';
        }
        
        if (empty($output)) {
            $output = '<div class="no-tweets">This user hasn\'t posted any tweets yet!</div>';
        }
        
        return $output;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return '<div class="error">Error loading user tweets</div>';
    }
}
?>
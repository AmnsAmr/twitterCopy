<?php
// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'backend/connection.php';
require_once 'backend/get_tweet.php'; // Include the modified file

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get all tweets
$tweetsContent = getAllTweets($conn);

// Define the main content
$content = '
  <div class="container">
    <h1>
      <img src="images/sword_icon.png" alt="Logo" class="x-logo"> Home
    </h1>

    <div class="tweet-form">
      <form method="POST" action="backend/upload_tweet.php" enctype="multipart/form-data">
        <textarea name="content" placeholder="What\'s happening?" rows="3"></textarea>
        
        <div class="tweet-form-footer">
          <div class="media-upload">
            <input type="file" name="media" id="media_input" accept="image/*, video/*" hidden>
            <label for="media_input" class="media-label">
              <img src="images/clip_icon.png" class="clip-icon" alt="Add media">
            </label>
            <div class="media-preview"></div>
          </div>
          <span class="char-counter">280</span>
          <input type="submit" value="Tweet">
        </div>
      </form>
      '.(isset($_SESSION['error']) ? '<div class="error">'.$_SESSION['error'].'</div>' : '').'
      '.(isset($_SESSION['success']) ? '<div class="success">'.$_SESSION['success'].'</div>' : '').'
    </div>

    <h2>Tweets</h2>
    <div class="tweets-container">
      '.getAllTweets($conn).'
    </div>
  </div>
';

// Include the layout file
include 'layout.php';
?>
<?php
// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'backend/connection.php';
require_once 'backend/get_tweet.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get initial tweets (page 1)
$tweetsContent = getAllTweets($conn, 1, 10);

// Define the main content
$content = '
  <div class="container">
    <div class="middle-scroll-container">
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
        '.$tweetsContent.'
      </div>
      <div id="loading" style="display: none;">Loading more tweets...</div>
    </div>
  </div>

  <script>
    // Infinite scroll variables
    let currentPage = 1;
    let hasMore = true;
    let isLoading = false;

    window.addEventListener("scroll", () => {
        if (isLoading || !hasMore) return;

        const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
        if (scrollTop + clientHeight >= scrollHeight - 100) {
            loadMoreTweets();
        }
    });

    function loadMoreTweets() {
        isLoading = true;
        document.getElementById("loading").style.display = "block";

        fetch(`backend/get_tweets_page.php?page=${currentPage + 1}&limit=10`)
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    document.querySelector(".tweets-container").insertAdjacentHTML("beforeend", data.html);
                    currentPage++;
                    hasMore = data.hasMore;
                }
            })
            .catch(error => console.error("Error:", error))
            .finally(() => {
                isLoading = false;
                document.getElementById("loading").style.display = "none";
            });
    }

    // Character counter for tweet form
    const tweetTextarea = document.querySelector(".tweet-form textarea");
    const charCounter = document.querySelector(".char-counter");
    
    if (tweetTextarea && charCounter) {
        tweetTextarea.addEventListener("input", function() {
            const remaining = 280 - this.value.length;
            charCounter.textContent = remaining;
            charCounter.style.color = remaining < 0 ? "red" : "";
        });
    }
  </script>
';

// Clear session messages
unset($_SESSION['error']);
unset($_SESSION['success']);

// Include the layout file
include 'layout.php';
?>
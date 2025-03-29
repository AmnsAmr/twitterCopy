<!DOCTYPE html>
<html>
<head>
  <title>X (Twitter Clone)</title>
  <link rel="icon" href="images/sword_icon.png" type="image/png" sizes="32x32"> 
  <link rel="stylesheet" href="css/profile.css">
   <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="wrapper">
    <!-- Left Sidebar -->
    <div class="left_side">
  <a href="index.php" class="menu-item">
    <div class="icon-container">
      <img src="images/home_icon.png" alt="Home" class="icon">
    </div>
    <span>Home</span>
  </a>
  <a href="#" class="menu-item messages-button">
    <div class="icon-container">
      <img src="images/message_icon.png" alt="Messages" class="icon">
    </div>
    <span>Messages</span>
  </a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <!-- If the user is logged in, show the Logout link -->
    <a href="logout.php" class="menu-item">
      <div class="icon-container">
        <img src="images/logout_icon.png" alt="Logout" class="icon">
      </div>
      <span>Logout</span>
    </a>
  <?php else: ?>
    <!-- If the user is not logged in, show the Login link -->
    <a href="login.php" class="menu-item">
      <div class="icon-container">
        <img src="images/login_icon.png" alt="Login" class="icon">
      </div>
      <span>Login</span>
    </a>
  <?php endif; ?>
  <a href="profile.php" class="menu-item">
    <div class="icon-container">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <img src="backend/get_profile_picture.php" alt="Profile" class="icon profile-pic">
        <?php else : ?>
            <img src="images/profile_icon.png" alt="Profile" class="icon">
        <?php endif; ?>
    </div>
    <span>Profile</span>
</a>
</div>

    <!-- Main Content -->
    <?php echo $content; ?>

    <!-- Right Sidebar -->
    <div class="right_side">
      <!-- Search Bar -->
      <div class="search-bar">
        <input type="text" placeholder="Search" class="search-input">
        <div class="icon-container">
          <img class="search-button" src="images/search_icon.png" alt="Search">
        </div>
      </div>

      <!-- Messages Window (Hidden by Default) -->
      <div class="messages-window">
        <div class="messages-header">
          <h3>Messages</h3>
          <button class="close-messages">✖️</button>
        </div>
        <div class="messages-list">
          <!-- Messages will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  <style>

/* Media Upload Styles */
.media-upload {
  margin-top: 10px;
  display: flex;
  gap: 10px;
  align-items: center;
}

.clip-icon {
  width: 100%;
  height: 100%;
  cursor: pointer;
  transition: opacity 0.2s;
}

.clip-icon:hover {
  opacity: 0.8;
}

.media-preview {
  max-width: 200px;
  border-radius: 8px;
  overflow: hidden;
}

.tweet-media {
  max-width: 100%;
  max-height: 400px;
  border-radius: 12px;
  margin-top: 15px;
  background: var(--icon-bg);
  display: block;
}

/* Add video specific styling */
video.tweet-media {
  background: #000;
}




/* Add these to your existing style.css */
/* Improved Tweet Form Styles */
.tweet-form {
  background: var(--container-bg);
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 20px;
  border: 1px solid var(--border-color);
  transition: border-color 0.2s ease;
}

.tweet-form:focus-within {
  border-color: var(--accent-color);
}

.tweet-form textarea {
  width: 100%;
  min-height: 100px;
  border: none;
  background: transparent;
  color: var(--text-color);
  font-size: 1.2rem;
  padding: 0;
  margin: 0;
  resize: none;
  font-family: inherit;
}

.tweet-form textarea::placeholder {
  color: var(--light-text);
  font-size: 1.3rem;
  font-weight: 400;
}

.tweet-form-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  margin-top: 12px;
  border-top: 1px solid var(--border-color);
}

.media-upload {
  display: flex;
  align-items: center;
  gap: 8px;
}

.media-preview {
  position: relative;
  max-width: 200px;
  border-radius: 12px;
  overflow: hidden;
  margin-top: 8px;
}

.media-preview img,
.media-preview video {
  width: 100%;
  height: auto;
  max-height: 200px;
  object-fit: contain;
}

.remove-media {
  position: absolute;
  top: 4px;
  right: 4px;
  background: rgba(0, 0, 0, 0.7);
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tweet-form input[type="submit"] {
  background: var(--accent-color);
  border-radius: 24px;
  padding: 10px 20px;
  font-weight: 700;
  font-size: 0.95rem;
  margin-left: auto;
  transition: all 0.2s ease;
}

.tweet-form input[type="submit"]:hover {
  background: var(--accent-hover);
  transform: scale(1.02);
}

.media-label {
  display: flex;
  align-items: center;
  gap: 4px;
  color: var(--accent-color);
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  transition: background 0.2s ease;
}

.media-label:hover {
  background: rgba(29, 161, 242, 0.1);
}

.char-counter {
  color: var(--light-text);
  font-size: 0.9rem;
  margin-right: 12px;
}

.char-counter.warning {
  color: #ffd400;
}

.char-counter.error {
  color: #e0245e;
}




  </style>

  <script src="js/javascript.js"></script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
  <title>X (Twitter Clone)</title>
  <link rel="icon" href="images/sword_icon.png" type="image/png" sizes="32x32"> 
  <link rel="stylesheet" href="css/profile.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body data-user-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false' ?>">
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

  <script src="js/javascript.js?v=<?= time() ?>"></script>
</body>
</html>
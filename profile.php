<?php
// profile.php

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'backend/connection.php';
require_once 'backend/get_tweet.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
try {
    $stmt = $conn->prepare("SELECT username, email, bio, profile_picture, banner_image FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $stmt->bind_result($username, $email, $bio, $profile_picture, $banner_image);
    $stmt->fetch();

    $bio = $bio ?? '';
    $bannerImage = !empty($banner_image) ? 
        'data:image/png;base64,' . base64_encode($banner_image) : 
        'images/default_banner.jpg';

    $userProfile = [
        'username' => $username,
        'email' => $email,
        'bio' => $bio,
        'profile_picture' => $profile_picture,
        'banner_image' => $bannerImage
    ];
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("Oops! Something went wrong.");
} finally {
    $stmt->close();
}

$userTweets = getAllTweets($conn, 1, 10, $_SESSION['user_id']);

if ($userProfile) {
    $content = '
    <div class="container">
        <div class="middle-scroll-container">
                <!-- Banner Image -->
                <div class="profile-banner">
                    <img src="' . $userProfile['banner_image'] . '" alt="Banner" class="banner-image">
                </div>

                <!-- Profile Header -->
                <div class="profile-header">
                    <!-- Profile Picture -->
                    <div class="profile-picture-container">
                        <img src="backend/get_profile_picture.php" alt="Profile Picture" class="profile-picture">
                    </div>

                    <!-- Edit Profile Button -->
                    <div class="edit-profile">
                        <button class="auth-button">
                            <img src="images/setting.png" class="settings-icon"> Edit Profile
                        </button>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="profile-info">
                    <h1>' . htmlspecialchars($userProfile['username']) . '</h1>
                    <p class="username">@' . htmlspecialchars($username) . '</p>
                    <p class="bio">' . nl2br(htmlspecialchars($userProfile['bio'])) . '</p>
                    <div class="profile-stats">
                    <!-- followers -->
                    </div>
                </div>

                <!-- Tweets Section -->
                <div class="profile-tweets">
                <h2>Tweets</h2>
                <div class="profile-tweets-container">
                ' . $userTweets . '
                </div>
                <!-- Overlay and Settings Dropdown -->
            <div class="overlay"></div>
            <div class="settings-dropdown">
                <!-- Update Banner Image -->
                <form method="POST" action="backend/upload_banner_picture.php" enctype="multipart/form-data" class="dropdown-form">
        <h3>Update Banner Image</h3>
        <input type="file" id="banner_input" name="banner_image" class="file-input" accept="image/*" required>
        <label for="banner_input" class="file-input-label">
            <div class="upload-content">
                <img src="images/upload_icon.png" class="upload-icon" alt="Upload">
                <span class="upload-text">Click to upload banner image</span>
                <span class="upload-hint">JPG, PNG or GIF (Max 5MB)</span>
            </div>
        </label>
        <div class="file-preview-container" id="banner_preview_container">
            <img id="banner_preview" class="file-preview" src="#" alt="Banner preview">
            <button type="button" class="remove-preview" onclick="clearPreview("banner_input", "banner_preview_container")">×</button>
        </div>
        <button type="submit" class="auth-button">Upload Banner</button>
    </form>

    <!-- Update Profile Picture -->
    <form method="POST" action="backend/upload_profile_picture.php" enctype="multipart/form-data" class="dropdown-form">
        <h3>Update Profile Picture</h3>
        <input type="file" id="profile_input" name="profile_picture" class="file-input" accept="image/*" required>
        <label for="profile_input" class="file-input-label">
            <div class="upload-content">
                <img src="images/upload_icon.png" class="upload-icon" alt="Upload">
                <span class="upload-text">Click to upload profile picture</span>
                <span class="upload-hint">JPG, PNG or GIF (Max 5MB)</span>
            </div>
        </label>
        <div class="file-preview-container" id="profile_preview_container">
            <img id="profile_preview" class="file-preview" src="#" alt="Profile preview">
            <button type="button" class="remove-preview" onclick="clearPreview("profile_input", "profile_preview_container")">×</button>
        </div>
        <button type="submit" class="auth-button">Upload</button>
    </form>

                <!-- Update Bio -->
                <form method="POST" action="backend/update_bio.php" class="dropdown-form">
                    <h3>Update Bio</h3>
                    <textarea name="bio" placeholder="Tell us about yourself...">' . htmlspecialchars($userProfile['bio']) . '</textarea>
                    <button type="submit" class="auth-button">Update Bio</button>
                </form>

                <!-- Delete Account -->
                <form method="POST" action="backend/delete_account.php" class="dropdown-form" 
                    onsubmit="return confirm(\'Are you sure? This cannot be undone!\');">
                    <button type="submit" class="auth-button delete-account">
                        <img src="images/delete_icon.png" alt="Delete" class="delete_image"> Delete Account
                    </button>
                </form>
            </div>
        </div>
        <script src="js/profile.js"></script>
    </div>
        </div>
    ';
}
include 'layout.php';
?>
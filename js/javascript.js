// State management for tweets
const tweetApp = {
    currentPage: 1,
    hasMore: true,
    isLoading: false
  };
  
  // Initialize when DOM is loaded
  document.addEventListener('DOMContentLoaded', () => {
    initializeTweetForm();
    setupInfiniteScroll();
    setupLikeHandlers();
    setupCommentHandlers();
    
    // Debugging
    console.log('Initialization complete');
  });
  
  // Tweet form functionality
  function initializeTweetForm() {
    const textarea = document.querySelector('.tweet-form textarea');
    const charCounter = document.querySelector('.char-counter');
    const mediaInput = document.getElementById('media_input');
    const mediaPreview = document.querySelector('.media-preview');
  
    // Character counter
    if (textarea && charCounter) {
      textarea.addEventListener('input', () => {
        const remaining = 280 - textarea.value.length;
        charCounter.textContent = remaining;
        
        charCounter.classList.remove('warning', 'error');
        if (remaining < 20) charCounter.classList.add('warning');
        if (remaining < 0) charCounter.classList.add('error');
      });
    }
  
    // Media preview
    if (mediaInput && mediaPreview) {
      mediaInput.addEventListener('change', function(e) {
        const file = this.files[0];
        if (!file) return;
  
        const reader = new FileReader();
        reader.onload = function(e) {
          const isVideo = file.type.startsWith('video/');
          mediaPreview.innerHTML = `
            ${isVideo ? `
            <video controls class="media-preview-content">
              <source src="${e.target.result}" type="${file.type}">
            </video>
            ` : `<img src="${e.target.result}" class="media-preview-content">`}
            <button type="button" class="remove-media" onclick="clearMedia()">×</button>
          `;
        };
        reader.readAsDataURL(file);
      });
    }
  }
  
  // Like button handlers
  function setupLikeHandlers() {
    document.addEventListener('click', async function(e) {
      const likeBtn = e.target.closest('.like-btn');
      if (!likeBtn) return;
      
      e.preventDefault();
      
      // Check login status
      if (document.body.dataset.userLoggedIn !== 'true') {
        window.location.href = 'login.php';
        return;
      }
  
      const postId = likeBtn.dataset.postId;
      
      try {
        // Add loading state
        likeBtn.disabled = true;
        const originalHTML = likeBtn.innerHTML;
        likeBtn.innerHTML = '⏳';
        
        // Send the request
        const response = await fetch('backend/toggle_like.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `post_id=${postId}`
        });
        
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        
        if (!data.success) throw new Error(data.message || 'Unknown error');
        
        // Update UI
        const likeCount = likeBtn.querySelector('.like-count') || document.createElement('span');
        likeCount.className = 'like-count';
        likeCount.textContent = data.newCount;
        
        likeBtn.dataset.liked = data.liked;
        likeBtn.innerHTML = `${data.liked ? '❤️' : '🤍'} `;
        likeBtn.appendChild(likeCount);
        
      } catch (error) {
        console.error('Like Error:', error);
        alert(error.message || 'Failed to process like. Please try again.');
      } finally {
        likeBtn.disabled = false;
      }
    });
  }
  
  // Comment handlers
  function setupCommentHandlers() {
    // Comment button click handler
    document.addEventListener('click', async function(e) {
      const commentBtn = e.target.closest('.comment-btn');
      if (!commentBtn) return;
      
      e.preventDefault();
      const tweetId = commentBtn.dataset.postId;
      console.log('Comment button clicked for tweet:', tweetId); // Debug
      
      const container = document.getElementById(`comments-${tweetId}`);
      if (!container) {
        console.error('Comments container not found');
        return;
      }
      
      // Toggle visibility or load comments
      if (container.style.display !== 'block' || container.innerHTML.trim() === '') {
        console.log('Loading comments...'); // Debug
        await loadComments(tweetId);
        container.style.display = 'block';
      } else {
        console.log('Hiding comments...'); // Debug
        container.style.display = 'none';
      }
    });
  
    // Comment form submission handler
    document.addEventListener('submit', function(e) {
      if (!e.target.classList.contains('comment-form')) return;
      e.preventDefault();
      handleCommentSubmit(e.target);
    });
  }
  
  // Load comments for a tweet
  async function loadComments(tweetId) {
    var container = document.getElementById(`comments-${tweetId}`);
    if (!container) return;
  
    try {
      // Show loading state
      container.classList.add('visible'); // 👈 Add visible class
      container.innerHTML = '<div class="loading-comments">Loading comments...</div>';
  
      // Fetch and insert comments
      const response = await fetch(`backend/get_comments.php?post_id=${tweetId}`);
      const html = await response.text();
      
      container.innerHTML = html;
      container.classList.add('visible'); // 👈 Keep visible
  
    } catch (error) {
      console.error('Failed to load comments:', error);
      container.innerHTML = `Error loading comments`;
      container.classList.add('visible');
    }
  }
  
  // Handle comment submission
  async function handleCommentSubmit(form) {
    const formData = new FormData(form);
    const tweetId = formData.get('post_id');
    console.log('Submitting comment for tweet:', tweetId); // Debug
  
    // Show loading state
    const submitBtn = form.querySelector('[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.value = 'Posting...';
  
    try {
      const response = await fetch('backend/add_comment.php', {
        method: 'POST',
        body: formData
      });
      
      console.log('Comment submission response:', response.status); // Debug
      
      if (!response.ok) throw new Error('Network response was not ok');
      
      const data = await response.json();
      console.log('Comment submission data:', data); // Debug
      
      if (!data.success) throw new Error(data.message || 'Failed to post comment');
      
      // Clear the form
      form.reset();
      
      // Refresh comments
      await loadComments(tweetId);
      
    } catch (error) {
      console.error('Error submitting comment:', error);
      alert(error.message);
    } finally {
      submitBtn.disabled = false;
      submitBtn.value = 'Post Comment';
    }
  }
  
  // Clear media preview
  function clearMedia() {
    const mediaInput = document.getElementById('media_input');
    const mediaPreview = document.querySelector('.media-preview');
    
    if (mediaInput) mediaInput.value = '';
    if (mediaPreview) mediaPreview.innerHTML = '';
  }
  
  // Infinite scroll setup
  function setupInfiniteScroll() {
    window.addEventListener('scroll', () => {
      if (tweetApp.isLoading || !tweetApp.hasMore) return;
  
      const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
      if (scrollTop + clientHeight >= scrollHeight - 100) {
        loadMoreTweets();
      }
    });
  }
  
  // Load more tweets
  async function loadMoreTweets() {
    tweetApp.isLoading = true;
    const loadingElement = document.getElementById('loading');
    if (loadingElement) loadingElement.style.display = 'block';
  
    try {
      const response = await fetch(`backend/get_tweets_page.php?page=${tweetApp.currentPage + 1}&limit=10`);
      if (!response.ok) throw new Error('Network response was not ok');
      
      const data = await response.json();
      
      if (data.html) {
        const container = document.querySelector('.tweets-container');
        if (container) {
          container.insertAdjacentHTML('beforeend', data.html);
          tweetApp.currentPage++;
          tweetApp.hasMore = data.hasMore;
        }
      }
    } catch (error) {
      console.error('Loading Error:', error);
    } finally {
      tweetApp.isLoading = false;
      const loadingElement = document.getElementById('loading');
      if (loadingElement) loadingElement.style.display = 'none';
    }
  }
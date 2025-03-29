// Tweet form functionality
document.addEventListener('DOMContentLoaded', () => {
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
              <video controls>
                <source src="${e.target.result}" type="${file.type}">
              </video>
            ` : `<img src="${e.target.result}">`}
            <button type="button" class="remove-media" onclick="clearMedia()">×</button>
          `;
        }
        reader.readAsDataURL(file);
      });
    }
  });
  
  function clearMedia() {
    document.getElementById('media_input').value = '';
    document.querySelector('.media-preview').innerHTML = '';
  }
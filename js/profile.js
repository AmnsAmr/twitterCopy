// profile.js - Complete JavaScript for Twitter Clone Profile Page

document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // Messages Window Functionality
    // =============================================
    const messagesButton = document.querySelector('.messages-button');
    const messagesWindow = document.querySelector('.messages-window');
    const closeMessages = document.querySelector('.close-messages');

    if (messagesButton && messagesWindow && closeMessages) {
        messagesButton.addEventListener('click', (e) => {
            e.preventDefault();
            messagesWindow.style.display = 'block';
        });

        closeMessages.addEventListener('click', () => {
            messagesWindow.style.display = 'none';
        });
    }

    // =============================================
    // Settings Dropdown Functionality
    // =============================================
    const editProfileButton = document.querySelector('.edit-profile .auth-button');
    const settingsDropdown = document.querySelector('.settings-dropdown');
    const overlay = document.querySelector('.overlay');

    if (editProfileButton && settingsDropdown && overlay) {
        editProfileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            settingsDropdown.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', function() {
            settingsDropdown.classList.remove('show');
            overlay.classList.remove('show');
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                settingsDropdown.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    }

    // =============================================
    // File Upload Preview Functionality
    // =============================================
    function setupFilePreview(inputId, previewId, containerId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const container = document.getElementById(containerId);
        const form = input.closest('.dropdown-form');

        if (!input || !preview || !container) return;

        input.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                // Validate file size (5MB max)
                if (this.files[0].size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                    form.classList.add('has-file');
                }
                
                reader.onloadend = function() {
                    // Add slight transition for smooth appearance
                    preview.style.opacity = '0';
                    setTimeout(() => {
                        preview.style.opacity = '1';
                        preview.style.transition = 'opacity 0.3s ease';
                    }, 10);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    function clearPreview(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        const form = input.closest('.dropdown-form');
        
        if (!input || !container) return;
        
        input.value = '';
        container.style.display = 'none';
        form.classList.remove('has-file');
        
        // Reset the form if it's the only input
        if (input.files.length === 0) {
            form.classList.remove('has-file');
        }
    }

    // Initialize file previews for both banner and profile picture
    setupFilePreview('banner_input', 'banner_preview', 'banner_preview_container');
    setupFilePreview('profile_input', 'profile_preview', 'profile_preview_container');

    // Handle remove preview button clicks
    document.querySelectorAll('.remove-preview').forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            const containerId = this.getAttribute('onclick').match(/'([^']+)'/)[3];
            clearPreview(inputId, containerId);
        });
    });

    // =============================================
    // Form Submission Handling
    // =============================================
    document.querySelectorAll('.dropdown-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Add loading state
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="loading-spinner"></span> Uploading...';
            }
            
            // You could add AJAX submission here if needed
            // Otherwise, the form will submit normally
        });
    });

    // =============================================
    // Additional Profile Page Interactions
    // =============================================
    // Bio character counter
    const bioTextarea = document.querySelector('textarea[name="bio"]');
    if (bioTextarea) {
        const bioCounter = document.createElement('div');
        bioCounter.className = 'bio-counter';
        bioCounter.style.color = 'var(--light-text)';
        bioCounter.style.fontSize = '0.8rem';
        bioCounter.style.textAlign = 'right';
        bioCounter.style.marginTop = '-10px';
        bioCounter.style.marginBottom = '10px';
        bioTextarea.parentNode.insertBefore(bioCounter, bioTextarea.nextSibling);

        bioTextarea.addEventListener('input', function() {
            const remaining = 160 - this.value.length;
            bioCounter.textContent = `${remaining} characters remaining`;
            bioCounter.style.color = remaining < 20 ? '#ff4444' : 'var(--light-text)';
        });

        // Trigger initial count
        bioTextarea.dispatchEvent(new Event('input'));
    }
});

// Utility function for loading states
function setLoadingState(element, isLoading) {
    if (isLoading) {
        element.disabled = true;
        element.innerHTML = '<span class="loading-spinner"></span> Processing...';
    } else {
        element.disabled = false;
        element.innerHTML = element.getAttribute('data-original-text');
    }
}
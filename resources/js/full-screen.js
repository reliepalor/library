function toggleFullScreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    const fullscreenEl = document.querySelector('.fullscreen');
    const button = document.getElementById('fullscreen-btn');

    const isCurrentlyFullscreen = document.fullscreenElement !== null;

    if (!isCurrentlyFullscreen) {
        // Hide all not-fullscreen elements
        notFullscreenEls.forEach(el => el.style.display = 'none');

        // Enter fullscreen mode on the fullscreen element
        if (fullscreenEl.requestFullscreen) {
            fullscreenEl.requestFullscreen();
        } else if (fullscreenEl.webkitRequestFullscreen) {
            fullscreenEl.webkitRequestFullscreen();
        } else if (fullscreenEl.msRequestFullscreen) {
            fullscreenEl.msRequestFullscreen();
        }

        // Change button text
        button.textContent = 'Exit Fullscreen';
    } else {
        // Exit fullscreen mode
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }

        // Show the hidden UI
        notFullscreenEls.forEach(el => el.style.display = '');
        button.textContent = 'Full Screen';
    }
}

// Optional: update button text on exiting fullscreen via ESC or other means
document.addEventListener('fullscreenchange', () => {
    const button = document.getElementById('fullscreen-btn');
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    if (!document.fullscreenElement) {
        // Show normal elements again
        notFullscreenEls.forEach(el => el.style.display = '');
        button.textContent = 'Full Screen';
    }
});

window.toggleFullScreen = toggleFullScreen;

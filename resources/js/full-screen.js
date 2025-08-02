let isAppFullScreen = false;

function toggleFullScreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    const fullscreenEl = document.getElementById('fullscreen-section');
    const button = document.getElementById('fullscreen-btn');

    if (!isAppFullScreen) {
        // Hide all non-fullscreen elements
        notFullscreenEls.forEach(el => el.classList.add('hidden'));

        // Request real fullscreen mode
        if (fullscreenEl.requestFullscreen) {
            fullscreenEl.requestFullscreen();
        } else if (fullscreenEl.webkitRequestFullscreen) {
            fullscreenEl.webkitRequestFullscreen();
        } else if (fullscreenEl.msRequestFullscreen) {
            fullscreenEl.msRequestFullscreen();
        }

        // Update UI
        button.textContent = 'Exit Fullscreen';
        isAppFullScreen = true;
    } else {
        // Exit browser fullscreen mode
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }

        // Restore hidden elements AFTER fullscreen exits (handled below)
        isAppFullScreen = false;
    }
}

// Listen for when fullscreen is exited manually (Esc or button)
document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement) {
        const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
        const button = document.getElementById('fullscreen-btn');

        // Show all elements back
        notFullscreenEls.forEach(el => el.classList.remove('hidden'));

        // Reset button label
        button.textContent = 'Full Screen';

        isAppFullScreen = false;
    }
});

window.toggleFullScreen = toggleFullScreen;

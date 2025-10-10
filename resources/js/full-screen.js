let isAppFullScreen = false;

function enterPseudoFullscreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    const button = document.getElementById('fullscreen-btn');
    notFullscreenEls.forEach(el => el.classList.add('hidden'));
    if (button) button.textContent = 'Exit Fullscreen';
    isAppFullScreen = true;
    try {
        document.body.classList.add('attendance-fullscreen-active');
        document.body.style.overflow = 'hidden'; // prevent scrolling to other UI
    } catch (_) {}
}

function toggleFullScreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    // Prefer full-viewport fullscreen like YouTube
    const rootEl = document.documentElement;
    const sectionEl = document.getElementById('fullscreen-section');
    const button = document.getElementById('fullscreen-btn');

    if (!isAppFullScreen) {
        // Hide all non-fullscreen elements
        notFullscreenEls.forEach(el => el.classList.add('hidden'));

        // Request real fullscreen mode
        try {
            const target = rootEl || sectionEl;
            if (target && target.requestFullscreen) {
                target.requestFullscreen().catch(() => enterPseudoFullscreen());
            } else if (target && target.webkitRequestFullscreen) {
                target.webkitRequestFullscreen();
            } else if (target && target.msRequestFullscreen) {
                target.msRequestFullscreen();
            } else {
                enterPseudoFullscreen();
            }
        } catch (_) {
            // Fallback to pseudo-fullscreen if programmatic fullscreen is blocked
            enterPseudoFullscreen();
        }

        // Update UI
        button.textContent = 'Exit Fullscreen';
        isAppFullScreen = true;
        try { localStorage.setItem('attendance_fullscreen', '1'); } catch (_) {}
        try {
            document.body.classList.add('attendance-fullscreen-active');
            document.body.style.overflow = 'hidden';
        } catch (_) {}
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
        try { localStorage.removeItem('attendance_fullscreen'); } catch (_) {}
        try {
            document.body.classList.remove('attendance-fullscreen-active');
            document.body.style.overflow = '';
        } catch (_) {}
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
        try { localStorage.removeItem('attendance_fullscreen'); } catch (_) {}
        try {
            document.body.classList.remove('attendance-fullscreen-active');
            document.body.style.overflow = '';
        } catch (_) {}
    }
});

window.toggleFullScreen = toggleFullScreen;

// Auto-apply fullscreen on page load if user left previous attendance page in fullscreen
document.addEventListener('DOMContentLoaded', () => {
    try {
        const wantFullscreen = localStorage.getItem('attendance_fullscreen') === '1';
        const rootEl = document.documentElement;
        const sectionEl = document.getElementById('fullscreen-section');
        const button = document.getElementById('fullscreen-btn');
        if (wantFullscreen && button) {
            // Attempt real fullscreen first
            let attempted = false;
            const tryEnter = async () => {
                if (attempted) return;
                attempted = true;
                try {
                    const target = rootEl || sectionEl;
                    if (target && target.requestFullscreen) {
                        await target.requestFullscreen();
                        // hide chrome immediately
                        document.querySelectorAll('.not-fullscreen').forEach(el => el.classList.add('hidden'));
                        if (button) button.textContent = 'Exit Fullscreen';
                        isAppFullScreen = true;
                        return true;
                    }
                } catch (_) { /* fall through */ }
                return false;
            };

            // Immediate attempt (may be blocked by browser policy)
            tryEnter().then((ok) => {
                if (ok) return;
                // If blocked, show a one-time overlay to capture the first user gesture
                const overlay = document.createElement('div');
                overlay.id = 'fs-gesture-overlay';
                overlay.style.position = 'fixed';
                overlay.style.inset = '0';
                overlay.style.background = 'rgba(0,0,0,0.6)';
                overlay.style.color = '#fff';
                overlay.style.display = 'flex';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'center';
                overlay.style.zIndex = '9999';
                overlay.style.cursor = 'pointer';
                overlay.innerHTML = '<div style="text-align:center;font-family:system-ui, -apple-system, Segoe UI, Roboto;">' +
                    '<div style="font-size:20px;font-weight:700;margin-bottom:8px;">Tap to enter Fullscreen</div>' +
                    '<div style="font-size:14px;opacity:0.85;">For better scanning experience</div>' +
                '</div>';

                const onGesture = async (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const ok2 = await tryEnter();
                    if (!ok2) {
                        // fallback to pseudo fullscreen
                        enterPseudoFullscreen();
                    }
                    try {
                        document.body.classList.add('attendance-fullscreen-active');
                        document.body.style.overflow = 'hidden';
                    } catch (_) {}
                    overlay.removeEventListener('click', onGesture);
                    overlay.removeEventListener('touchstart', onGesture);
                    if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                };
                overlay.addEventListener('click', onGesture, { once: true });
                overlay.addEventListener('touchstart', onGesture, { once: true });
                document.body.appendChild(overlay);
            });
        }
    } catch (_) {
        // ignore storage errors
    }
});

let isAppFullScreen = false;
let pendingExitFullscreen = false;

function enterPseudoFullscreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    const button = document.getElementById('fullscreen-btn');
    notFullscreenEls.forEach(el => el.classList.add('hidden'));
    if (button) button.textContent = 'Exit Fullscreen';
    isAppFullScreen = true;
    try {
        // More aggressive pseudo-fullscreen to hide browser chrome
        document.documentElement.style.overflow = 'hidden';
        document.documentElement.style.margin = '0';
        document.documentElement.style.padding = '0';
        document.documentElement.style.width = '100vw';
        document.documentElement.style.height = '100vh';
        document.documentElement.style.position = 'fixed';
        document.documentElement.style.top = '0';
        document.documentElement.style.left = '0';
        document.documentElement.style.zIndex = '999999';

        document.body.classList.add('attendance-fullscreen-active');
        document.body.style.overflow = 'hidden';
        document.body.style.margin = '0';
        document.body.style.padding = '0';
        document.body.style.width = '100vw';
        document.body.style.height = '100vh';
        document.body.style.position = 'fixed';
        document.body.style.top = '0';
        document.body.style.left = '0';
        document.body.style.zIndex = '999999';

        // Force remove margin-left from main-content when in fullscreen
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.style.marginLeft = '0';
        }

        // Prevent scrolling and hide scrollbars
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';

        // Additional measures to hide browser UI
        window.scrollTo(0, 0);
    } catch (_) {}
}

function toggleFullScreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    const button = document.getElementById('fullscreen-btn');

    if (!isAppFullScreen) {
        // Hide all non-fullscreen elements
        notFullscreenEls.forEach(el => el.classList.add('hidden'));

        // Try browser fullscreen first for better UX, fallback to pseudo-fullscreen
        try {
            const rootEl = document.documentElement;
            const sectionEl = document.getElementById('fullscreen-section');
            const target = rootEl || sectionEl;

            if (target && target.requestFullscreen) {
                target.requestFullscreen().then(() => {
                    // Browser fullscreen succeeded
                    isAppFullScreen = true;
                    button.textContent = 'Exit Fullscreen';
                    try { localStorage.setItem('attendance_fullscreen', '1'); } catch (_) {}
                    document.body.classList.add('attendance-fullscreen-active');
                }).catch(() => {
                    // Browser fullscreen failed, use pseudo-fullscreen
                    enterPseudoFullscreen();
                    button.textContent = 'Exit Fullscreen';
                    isAppFullScreen = true;
                    try { localStorage.setItem('attendance_fullscreen', '1'); } catch (_) {}
                    document.body.classList.add('attendance-fullscreen-active');
                });
            } else {
                // Browser fullscreen not supported, use pseudo-fullscreen
                enterPseudoFullscreen();
                button.textContent = 'Exit Fullscreen';
                isAppFullScreen = true;
                try { localStorage.setItem('attendance_fullscreen', '1'); } catch (_) {}
                document.body.classList.add('attendance-fullscreen-active');
            }
        } catch (_) {
            // Fallback to pseudo-fullscreen
            enterPseudoFullscreen();
            button.textContent = 'Exit Fullscreen';
            isAppFullScreen = true;
            try { localStorage.setItem('attendance_fullscreen', '1'); } catch (_) {}
            document.body.classList.add('attendance-fullscreen-active');
        }
    } else {
        // Show password modal instead of exiting immediately
        showExitFullscreenModal();
    }
}

// Listen for when fullscreen is exited manually (Esc or button) - prevent any unauthorized exit
document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement && isAppFullScreen && !pendingExitFullscreen) {
        // User pressed Esc or used browser controls - immediately switch to pseudo-fullscreen to prevent exposure
        // Don't show modal here, just force pseudo-fullscreen
        setTimeout(() => {
            if (isAppFullScreen && !pendingExitFullscreen) {
                enterPseudoFullscreen(); // Use pseudo-fullscreen as it's more reliable
            }
        }, 1); // Immediate re-entry
    } else if (!document.fullscreenElement && pendingExitFullscreen) {
        // Password was validated, proceed with exit
        performExitFullscreen();
    }
});

function showExitFullscreenModal() {
    const modal = document.getElementById('fullscreen-exit-modal');
    const passwordInput = document.getElementById('exit-password');
    const errorMessage = document.getElementById('exit-error-message');

    if (modal) {
        modal.classList.remove('hidden');
        passwordInput.value = '';
        errorMessage.classList.add('hidden');
        passwordInput.focus();
    }
}

function performExitFullscreen() {
    const notFullscreenEls = document.querySelectorAll('.not-fullscreen');
    const button = document.getElementById('fullscreen-btn');

    // Show all elements back
    notFullscreenEls.forEach(el => el.classList.remove('hidden'));

    // Reset button label
    button.textContent = 'Full Screen';

    isAppFullScreen = false;
    pendingExitFullscreen = false;
    try { localStorage.removeItem('attendance_fullscreen'); } catch (_) {}
    try {
        // Restore document element styles
        document.documentElement.style.overflow = '';
        document.documentElement.style.margin = '';
        document.documentElement.style.padding = '';
        document.documentElement.style.width = '';
        document.documentElement.style.height = '';
        document.documentElement.style.position = '';
        document.documentElement.style.top = '';
        document.documentElement.style.left = '';
        document.documentElement.style.zIndex = '';

        // Restore body styles
        document.body.classList.remove('attendance-fullscreen-active');
        document.body.style.overflow = '';
        document.body.style.margin = '';
        document.body.style.padding = '';
        document.body.style.width = '';
        document.body.style.height = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.left = '';
        document.body.style.zIndex = '';

        // Restore margin-left to main-content when exiting fullscreen
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.style.marginLeft = '';
        }
    } catch (_) {}
}

window.toggleFullScreen = toggleFullScreen;

// Prevent Esc key from exiting fullscreen directly - capture at highest priority
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isAppFullScreen && !pendingExitFullscreen) {
        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();
        // Completely disable Esc key in fullscreen - do nothing
        return false;
    }
}, true); // Use capture phase for highest priority

// Handle fullscreen exit modal form submission
document.addEventListener('DOMContentLoaded', () => {
    const exitForm = document.getElementById('fullscreen-exit-form');
    const exitModal = document.getElementById('fullscreen-exit-modal');
    const cancelBtn = document.getElementById('exit-modal-cancel');
    const passwordInput = document.getElementById('exit-password');
    const errorMessage = document.getElementById('exit-error-message');

    if (exitForm) {
        exitForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const password = passwordInput.value.trim();

            if (password === 'csug') {
                // Password correct - exit fullscreen
                pendingExitFullscreen = true;
                exitModal.classList.add('hidden');
                performExitFullscreen();
            } else {
                // Password incorrect - show error
                errorMessage.classList.remove('hidden');
                passwordInput.value = '';
                passwordInput.focus();
            }
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            exitModal.classList.add('hidden');
        });
    }

    // Close modal when clicking outside
    if (exitModal) {
        exitModal.addEventListener('click', (e) => {
            if (e.target === exitModal) {
                exitModal.classList.add('hidden');
            }
        });
    }
});

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
                        // Force remove margin-left from main-content when in fullscreen
                        const mainContent = document.querySelector('.main-content');
                        if (mainContent) {
                            mainContent.style.marginLeft = '0';
                        }
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

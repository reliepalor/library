// Unified Attendance Scanner - Handles both Students and Teachers
// Global variables for scanner state
let html5QrCode;
let isScanning = false;
let lastScannedQR = null;
let lastScanTime = 0;
const SCAN_COOLDOWN = 3000; // 3 seconds cooldown between scans

console.log('[INIT] Unified attendance scanner loading...');
console.log('[INIT] Script loaded at:', new Date().toLocaleTimeString());

// Initialize scanner when DOM is loaded
document.addEventListener('DOMContentLoaded', async function() {
    console.log('[INIT] Starting attendance system...');

    // Verify required elements exist
    const qrInput = document.getElementById('qr-input');
    const qrReader = document.getElementById('qr-reader');
    const webcamContainer = document.getElementById('webcam-container');
    const physicalContainer = document.getElementById('physical-container');
    const webcamModeBtn = document.getElementById('webcam-mode-btn');
    const physicalModeBtn = document.getElementById('physical-mode-btn');
    const modeDescription = document.getElementById('mode-description');

    // Check for core required elements
    const coreMissing = [];
    if (!qrInput) coreMissing.push('qr-input');
    if (!qrReader) coreMissing.push('qr-reader');
    if (!webcamContainer) coreMissing.push('webcam-container');
    if (!physicalContainer) coreMissing.push('physical-container');
    if (!webcamModeBtn) coreMissing.push('webcam-mode-btn');
    if (!physicalModeBtn) coreMissing.push('physical-mode-btn');
    if (!modeDescription) coreMissing.push('mode-description');

    if (coreMissing.length > 0) {
        console.error('[INIT] Missing core elements:', coreMissing);
        showNotification('Missing required scanner elements. Please refresh the page.', 'error');
        return;
    }

    console.log('[INIT] Core elements found. Proceeding with scanner setup.');

    // Set up scanner modes and physical scanner first
    setupScannerModes();
    setupPhysicalScanner();

    // Check if Html5Qrcode library is loaded
    if (typeof Html5Qrcode === 'undefined') {
        console.error('[INIT] Html5Qrcode library not loaded');
        showNotification('Scanner library not loaded. Webcam scanner will not work, but physical scanner is available.', 'error');

        // Default to physical scanner
        const physicalModeBtn = document.getElementById('physical-mode-btn');
        if (physicalModeBtn) {
            physicalModeBtn.click();
        }
        return;
    }
    console.log('[INIT] Html5Qrcode library available');

    // Initialize scanner components
    try {
        // Initialize HTML5 QR Code scanner
        html5QrCode = new Html5Qrcode("qr-reader");
        console.log('[INIT] HTML5 QR Code scanner initialized successfully');

        // Already set up, just default to physical
        const physicalModeBtn = document.getElementById('physical-mode-btn');
        if (physicalModeBtn) {
            physicalModeBtn.click();
        }

        console.log('[INIT] Scanner initialized successfully');
    } catch (error) {
        console.error('[INIT] Error initializing scanner:', error);
        showNotification('Error initializing scanner. Physical scanner is available.', 'error');

        // Physical scanner is already set up
        const physicalModeBtn = document.getElementById('physical-mode-btn');
        if (physicalModeBtn) {
            physicalModeBtn.click();
        }
    }

    // Initial data load
    refreshAttendanceTable();

    // Load initial student attendance records (first 10)
    loadInitialStudentRecords();

    // Set up polling - refresh every 5 seconds
    const POLL_INTERVAL = 5000; // 5 seconds
    let isRefreshing = false;

    const startPolling = () => {
        setInterval(async () => {
            if (!isRefreshing) {
                isRefreshing = true;
                try {
                    await refreshAttendanceTable();
                } catch (error) {
                    console.error('[POLLING] Error refreshing attendance:', error);
                } finally {
                    isRefreshing = false;
                }
            }
        }, POLL_INTERVAL);
    };

    // Start polling
    startPolling();

    // Also refresh when the window regains focus
    window.addEventListener('focus', () => {
        if (!isRefreshing) {
            refreshAttendanceTable();
        }
    });

    // Activity select change listener
    document.getElementById('activity').addEventListener('change', function() {
        const activity = this.value;
        const submitBtn = document.getElementById('modal-submit');
        const otherActivitiesSection = document.getElementById('other-activities-section');
        const backBtn = document.getElementById('modal-back');

        if (activity === 'Borrow' || activity === 'Stay&Borrow') {
            const userType = document.getElementById('modal-user-type').value;
            const identifier = document.getElementById('modal-identifier').value;
            showBookSelectionModal(userType, identifier, activity);
            // Hide submit button since modal is shown
            submitBtn.style.display = 'none';
            // Show back button for borrow activities
            if (backBtn) backBtn.classList.remove('hidden');
        } else if (activity === 'Other') {
            // Show other activities section with smooth transition
            otherActivitiesSection.style.opacity = '1';
            otherActivitiesSection.style.maxHeight = '500px'; // Set a reasonable max height
            // Show submit button for other activities
            submitBtn.style.display = 'inline-block';
            // Show back button for other activities
            if (backBtn) backBtn.classList.remove('hidden');
        } else {
            // Hide other activities section
            otherActivitiesSection.style.opacity = '0';
            otherActivitiesSection.style.maxHeight = '0';
            // Show submit button for other activities
            submitBtn.style.display = 'inline-block';
            // Hide back button for default activities
            if (backBtn) backBtn.classList.add('hidden');
        }
    });

    // Activity modal event listeners
    document.getElementById('activity-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const userType = document.getElementById('modal-user-type').value;
        const identifier = document.getElementById('modal-identifier').value;
        let activity = document.getElementById('activity').value;

        // If "Other" is selected, get the custom activity value
        if (activity === 'Other') {
            const customActivity = document.getElementById('custom-activity').value.trim();
            if (customActivity) {
                activity = customActivity;
            } else {
                showNotification('Please specify an activity or select from the predefined options.', 'error');
                return;
            }
        }

        // Check if activity is study-related and study area is full
        const isStudyRelated = isStudyRelatedActivity(activity);
        if (isStudyRelated) {
            try {
                const studyAreaData = await checkStudyAreaAvailability();
                if (studyAreaData && studyAreaData.available_slots <= 0) {
                    // Show study area full warning modal
                    showStudyAreaFullWarningModal();
                    return; // Prevent attendance logging
                }
            } catch (error) {
                console.error('Error checking study area availability:', error);
                // Continue with attendance logging if check fails
            }
        }

        // Check if this is a borrowing activity
        if (activity === 'Borrow' || activity === 'Stay&Borrow') {
            // Check study area availability for Stay&Borrow activities
            if (activity === 'Stay&Borrow') {
                console.log('[DEBUG] Checking study area for Stay&Borrow activity:', activity);
                const isStudyRelated = isStudyRelatedActivity(activity);
                console.log('[DEBUG] Is study related:', isStudyRelated);

                if (isStudyRelated) {
                    try {
                        console.log('[DEBUG] Fetching study area availability...');
                        const studyAreaData = await checkStudyAreaAvailability();
                        console.log('[DEBUG] Study area data:', studyAreaData);

                        if (studyAreaData && studyAreaData.available_slots <= 0) {
                            console.log('[DEBUG] Study area full, showing warning modal');
                            // Show study area full warning modal
                            showStudyAreaFullWarningModal();
                            return; // Prevent attendance logging
                        } else {
                            console.log('[DEBUG] Study area has slots available:', studyAreaData?.available_slots);
                        }
                    } catch (error) {
                        console.error('Error checking study area availability:', error);
                        // Continue with attendance logging if check fails
                    }
                } else {
                    console.log('[DEBUG] Activity not considered study-related');
                }
            }

            if (activity === 'Stay&Borrow') {
                // For Stay&Borrow, log attendance first, then show book selection modal
                showLoadingOverlay("Logging in...");

                try {
                    let requestBody;
                    if (userType === 'student') {
                        requestBody = {
                            student_id: identifier,
                            activity: activity
                        };
                    } else {
                        requestBody = {
                            user_type: userType,
                            identifier: identifier,
                            activity: activity
                        };
                    }

                    const response = await fetch('/admin/attendance/log', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(requestBody)
                    });

                    const data = await response.json();

                    if (response.ok && data && data.success) {
                        // Successfully logged in, now show book selection modal
                        showBookSelectionModal(userType, identifier, activity);

                        // Show success message
                        const userName = data.name || 'User';
                        const emoji = userType === 'student' ? '🎓' : '👨‍🏫';
                        const welcomeText = `Welcome, ${userName}! ${emoji}`;
                        showNotification(welcomeText, 'success');

                        // Clear input field
                        const qrInput = document.getElementById('qr-input');
                        if (qrInput) qrInput.value = '';

                        // Refresh the attendance table
                        await refreshAttendanceTable();
                    } else {
                        const errorMsg = data.message || data.error || 'Login failed. Please try again.';
                        showNotification(errorMsg, 'error');
                    }
                } catch (error) {
                    console.error("Login error:", error);
                    let errorMsg = 'Login failed. Please try again.';
                    if (error.message?.includes('NetworkError')) {
                        errorMsg = 'Network error. Please check your connection.';
                    }
                    showNotification(errorMsg, 'error');
                } finally {
                    hideLoadingOverlay();
                }
            } else {
                // For Borrow, show book selection modal directly
                showBookSelectionModal(userType, identifier, activity);
            }
            return;
        }

        showLoadingOverlay("Logging attendance...");

        try {
            let requestBody;
            if (userType === 'student') {
                requestBody = {
                    student_id: identifier,
                    activity: activity
                };
            } else {
                requestBody = {
                    user_type: userType,
                    identifier: identifier,
                    activity: activity
                };
            }

            const response = await fetch('/admin/attendance/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestBody)
            });

            const data = await response.json();

            if (response.ok && data && (data.success || data.action === 'logged in' || data.action === 'logged out')) {
                const modal = document.getElementById('activity-modal');
                if (modal) modal.classList.add('hidden');

                // Determine if this was a login or logout based on the action
                const isLogin = data.action === 'logged in';
                const actionText = isLogin ? 'logged in' : 'logged out';
                const message = `${userType === 'student' ? 'Student' : 'Teacher'} ${actionText} successfully!`;
                showNotification(message, "success");

                // Show welcome/goodbye message with user's name if available
                const userName = data.name || 'User';
                const emoji = userType === 'student' ? '🎓' : '👨‍🏫';
                const welcomeText = isLogin ? `Welcome, ${userName}! ${emoji}` : `Goodbye, ${userName}! ${emoji}`;
                showNotification(welcomeText, 'success');

                // Clear input field
                const qrInput = document.getElementById('qr-input');
                if (qrInput) qrInput.value = '';

                // Refresh the attendance table
                await refreshAttendanceTable();
            } else {
                // Show more specific error message from server if available
                const errorMsg = data.message || data.error || 'Login failed. Please try again.';
                showNotification(errorMsg, 'error');
            }
        } catch (error) {
            console.error("Login error:", error);
            // More specific error handling
            let errorMsg = 'Login failed. Please try again.';
            if (error.message?.includes('NetworkError')) {
                errorMsg = 'Network error. Please check your connection.';
            } else if (error.message?.includes('timeout')) {
                errorMsg = 'Request timed out. Please try again.';
            }
            showNotification(errorMsg, 'error');
        } finally {
            hideLoadingOverlay();
        }
    });

    // Cancel modal
    document.getElementById('modal-cancel').addEventListener('click', function() {
        document.getElementById('activity-modal').classList.add('hidden');
        // Reset other activities section when modal is closed
        const otherActivitiesSection = document.getElementById('other-activities-section');
        if (otherActivitiesSection) {
            otherActivitiesSection.style.opacity = '0';
            otherActivitiesSection.style.maxHeight = '0';
        }
        // Clear custom activity input
        const customActivityInput = document.getElementById('custom-activity');
        if (customActivityInput) {
            customActivityInput.value = '';
        }
    });

    // Study area full modal OK button
    const studyAreaFullOkBtn = document.getElementById('study-area-full-ok');
    if (studyAreaFullOkBtn) {
        studyAreaFullOkBtn.addEventListener('click', function() {
            document.getElementById('study-area-full-modal').classList.add('hidden');
        });
    }

    // Back button in activity modal
    document.getElementById('modal-back').addEventListener('click', function() {
        // Reset activity selection to default
        const activitySelect = document.getElementById('activity');
        if (activitySelect) {
            activitySelect.value = 'Stay to Study';
            // Trigger change event to reset the UI
            activitySelect.dispatchEvent(new Event('change'));
        }
        // Reset other activities section
        const otherActivitiesSection = document.getElementById('other-activities-section');
        if (otherActivitiesSection) {
            otherActivitiesSection.style.opacity = '0';
            otherActivitiesSection.style.maxHeight = '0';
        }
        // Clear custom activity input
        const customActivityInput = document.getElementById('custom-activity');
        if (customActivityInput) {
            customActivityInput.value = '';
        }
        // Hide the back button since we're back to default
        this.classList.add('hidden');
    });

    // Handle predefined activity button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('other-activity-btn')) {
            const activity = e.target.getAttribute('data-activity');
            const customActivityInput = document.getElementById('custom-activity');
            if (customActivityInput) {
                customActivityInput.value = activity;
            }
            // Add visual feedback
            e.target.classList.add('bg-blue-200', 'ring-2', 'ring-blue-300');
            setTimeout(() => {
                e.target.classList.remove('bg-blue-200', 'ring-2', 'ring-blue-300');
            }, 200);
        }
    });
});

// Setup scanner mode toggle
function setupScannerModes() {
    const webcamBtn = document.getElementById('webcam-mode-btn');
    const physicalBtn = document.getElementById('physical-mode-btn');
    const webcamContainer = document.getElementById('webcam-container');
    const physicalContainer = document.getElementById('physical-container');
    const modeDescription = document.getElementById('mode-description');

    console.log('[MODE] Setting up scanner mode toggles');

    webcamBtn.addEventListener('click', async () => {
        console.log('[MODE] Webcam mode button clicked');
        if (isScanning) {
            console.log('[MODE] Webcam already scanning, ignoring click');
            return; // Prevent multiple clicks
        }

        console.log('[MODE] Switching to webcam mode');

        // Get required elements
        const scannerContainer = document.getElementById('qr-reader');
        const scannerLoading = document.getElementById('scanner-loading');
        const scannerError = document.getElementById('scanner-error');

        // Update UI
        webcamBtn.classList.add('bg-blue-600', 'text-white');
        webcamBtn.classList.remove('bg-gray-300', 'text-gray-700');
        physicalBtn.classList.add('bg-gray-300', 'text-gray-700');
        physicalBtn.classList.remove('bg-blue-600', 'text-white');

        // Show loading state
        if (scannerLoading) scannerLoading.style.display = 'block';
        if (scannerError) scannerError.classList.add('hidden');
        // Don't hide the scanner container - let HTML5-QR-Code library handle it

        webcamContainer.classList.remove('hidden');
        physicalContainer.classList.add('hidden');
        modeDescription.textContent = 'Using webcam scanner - point camera at QR code';

        try {
            await startWebcamScanner();
            localStorage.setItem('preferredScannerMode', 'webcam');
        } catch (error) {
            console.error('[SCANNER] Failed to start webcam:', error);
            showNotification('Failed to access webcam. Please check permissions and try again.', 'error');
            physicalBtn.click(); // Fallback to physical scanner
        }
    });

    physicalBtn.addEventListener('click', () => {
        console.log('[MODE] Physical mode button clicked');
        console.log('[MODE] Switching to physical mode');

        // Get required elements
        const scannerContainer = document.getElementById('qr-reader');
        const scannerLoading = document.getElementById('scanner-loading');
        const scannerError = document.getElementById('scanner-error');

        // Stop webcam if running
        if (isScanning) {
            stopWebcamScanner().catch(console.error);
        }

        // Update UI
        physicalBtn.classList.add('bg-blue-600', 'text-white');
        physicalBtn.classList.remove('bg-gray-300', 'text-gray-700');
        webcamBtn.classList.add('bg-gray-300', 'text-gray-700');
        webcamBtn.classList.remove('bg-blue-600', 'text-white');

        // Hide webcam elements
        // Don't hide scanner container - let HTML5-QR-Code library handle it
        if (scannerLoading) scannerLoading.style.display = 'none';
        if (scannerError) scannerError.classList.add('hidden');

        physicalContainer.classList.remove('hidden');
        webcamContainer.classList.add('hidden');
        modeDescription.textContent = 'Using physical scanner - scan QR code into input field';

        // Focus the input field
        const qrInput = document.getElementById('qr-input');
        if (qrInput) {
            qrInput.focus();
            qrInput.value = ''; // Clear previous input
        }

        localStorage.setItem('preferredScannerMode', 'physical');
    });

    console.log('[MODE] Event listeners added successfully');
}

// Start webcam scanning
async function startWebcamScanner() {
    if (isScanning) {
        console.log('[SCANNER] Webcam scanner already running');
        return;
    }
    
    console.log('[SCANNER] Starting webcam scanner...');
    
    const scannerContainer = document.getElementById('qr-reader');
    const scannerLoading = document.getElementById('scanner-loading');
    const scannerError = document.getElementById('scanner-error');
    
    if (!scannerContainer) {
        console.error('[SCANNER] QR reader container not found');
        return;
    }

    // Show loading state
    if (scannerLoading) scannerLoading.style.display = 'block';
    if (scannerError) {
        scannerError.classList.add('hidden');
        scannerError.innerHTML = ''; // Clear previous errors
    }
    
    // Ensure webcam container is visible
    const webcamContainer = document.getElementById('webcam-container');
    const physicalContainer = document.getElementById('physical-container');
    if (webcamContainer) webcamContainer.classList.remove('hidden');
    if (physicalContainer) physicalContainer.classList.add('hidden');
    
    try {
        // Initialize the scanner if not already done
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        // Try to get list of cameras first
        const devices = await Html5Qrcode.getCameras();
        if (!devices || devices.length === 0) {
            throw new Error('No cameras found. Please ensure your camera is connected and accessible.');
        }
        
        console.log('[SCANNER] Available cameras:', devices);
        
        // Try to use the environment (back) camera first, fallback to any available camera
        const backCamera = devices.find(device => 
            device.label.toLowerCase().includes('back') || 
            device.label.toLowerCase().includes('rear')
        );
        
        const cameraId = backCamera ? backCamera.id : devices[0].id;
        
        console.log('[SCANNER] Starting camera with ID:', cameraId);
        
        // Start the scanner
        await html5QrCode.start(
            cameraId,
            { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            },
            (decodedText) => onScanSuccess(decodedText),
            (errorMessage) => onScanError(errorMessage)
        );
        
        isScanning = true;
        console.log("[SCANNER] Webcam scanner started successfully");
        
        // Hide loading state
        if (scannerLoading) scannerLoading.style.display = 'none';
        if (scannerError) scannerError.classList.add('hidden');
        
        // The HTML5-QR-Code library handles video display automatically
        // No need to manually show/hide video elements
        
    } catch (err) {
        console.error("[SCANNER] Failed to start scanner:", err);
        isScanning = false;
        
        // Show error message
        if (scannerError) {
            scannerError.innerHTML = `
                <div class="text-red-600 font-medium">Camera Error</div>
                <p class="text-sm text-gray-600 my-2">${err.message || 'Failed to access camera'}</p>
                <button onclick="startWebcamScanner()" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Try Again
                </button>
            `;
            scannerError.classList.remove('hidden');
        }
        
        if (scannerLoading) scannerLoading.style.display = 'none';
        showNotification("Failed to start camera. Please check permissions and try again.", "error");
    }
}

// Stop webcam scanning
async function stopWebcamScanner() {
    if (!isScanning || !html5QrCode) return true;
    
    console.log('[SCANNER] Stopping webcam scanner...');
    
    try {
        await html5QrCode.stop();
        isScanning = false;
        
        // Clean up scanner container - the HTML5-QR-Code library handles video cleanup
        const scannerContainer = document.getElementById('qr-reader');
        if (scannerContainer) {
            // Clear the container completely as the library manages its own elements
            scannerContainer.innerHTML = '<p class="text-gray-500 text-sm p-4">Scanner stopped</p>';
        }
        
        console.log('[SCANNER] Webcam scanner stopped');
        return true;
    } catch (err) {
        console.error('[SCANNER] Failed to stop scanner:', err);
        isScanning = false;
        return false;
    }
}

// Setup physical scanner input
function setupPhysicalScanner() {
    const qrInput = document.getElementById('qr-input');
    const scannerLoading = document.getElementById('scanner-loading');
    const scannerError = document.getElementById('scanner-error');
    
    if (!qrInput) {
        console.error('[SCANNER] Physical scanner input not found');
        return;
    }
    
    // Hide loading and error states for physical scanner
    if (scannerLoading) scannerLoading.style.display = 'none';
    if (scannerError) scannerError.classList.add('hidden');
    
    qrInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const qrCode = qrInput.value.trim();
            
            if (qrCode) {
                onScanSuccess(qrCode);
                qrInput.value = '';
            }
        }
    });
}

// Handle successful QR scan
async function onScanSuccess(decodedText) {
    const now = Date.now();
    
    // Prevent duplicate scans within cooldown period
    if (lastScannedQR === decodedText && (now - lastScanTime) < SCAN_COOLDOWN) {
        console.log("[SCAN] Duplicate scan prevented - cooldown active");
        return;
    }
    
    lastScannedQR = decodedText;
    lastScanTime = now;
    
    console.log("[SCAN] QR Code detected:", decodedText);
    
    // Parse QR code to determine user type
    const userData = parseQRCode(decodedText);
    
    console.log("[SCAN] Parsed user data:", userData);
    
    if (!userData || !userData.userType || !userData.identifier) {
        console.error("[SCAN] Invalid QR code format", userData);
        showNotification("Invalid QR code format", "error");
        return;
    }
    
    console.log(`[SCAN] Processing ${userData.userType} with ID: ${userData.identifier}`);
    
    // Check if user has active session
    try {
        console.log("[SCAN] Checking for active session...");
        const hasActiveSession = await checkActiveSession(userData.userType, userData.identifier);
        
        console.log("[SCAN] Has active session:", hasActiveSession);
        
        if (hasActiveSession) {
            // Logout
            console.log("[SCAN] User has active session - logging out");
            await handleLogout(userData.userType, userData.identifier);
        } else {
            // Show activity modal for login
            console.log("[SCAN] No active session - showing activity modal");
            await showActivityModal(userData.userType, userData.identifier);
        }
    } catch (error) {
        console.error("[SCAN] Error processing scan:", error);
        console.error("[SCAN] Error stack:", error.stack);
        showNotification("Failed to process scan: " + error.message, "error");
    }
}

// Parse QR code to determine user type and identifier
function parseQRCode(qrCode) {
    // Expected formats:
    // Student: "studentId|name|college|year" (pipe-delimited)
    // Teacher: "id|name|department|role" (pipe-delimited)
    
    qrCode = qrCode.trim();
    console.log('[PARSE] Raw QR code:', qrCode);
    
    // Check if it's pipe-delimited
    if (qrCode.includes('|')) {
        const parts = qrCode.split('|').map(part => part.trim());
        console.log('[PARSE] Split parts:', parts);
        
        if (parts.length >= 4) {
            const identifier = parts[0];
            const name = parts[1];
            const collegeOrDept = parts[2];
            const roleOrYear = parts[3];
            
            console.log('[PARSE] Extracted:', { identifier, name, collegeOrDept, roleOrYear });
            
            // Determine if student or teacher/visitor based on role
            if (roleOrYear && (roleOrYear.toLowerCase().includes('teacher') || roleOrYear.toLowerCase().includes('prof') || roleOrYear.toLowerCase().includes('visitor'))) {
                console.log('[PARSE] Detected as TEACHER/VISITOR');
                return {
                    userType: 'teacher',
                    identifier: identifier,
                    rawData: qrCode,
                    name: name,
                    collegeOrDept: collegeOrDept,
                    role: roleOrYear
                };
            } else {
                // Default to student if not explicitly teacher/visitor
                console.log('[PARSE] Detected as STUDENT');
                return {
                    userType: 'student',
                    identifier: identifier,
                    rawData: qrCode,
                    name: name,
                    collegeOrDept: collegeOrDept,
                    year: roleOrYear
                };
            }
        } else {
            console.error('[PARSE] Not enough parts in QR code');
            return null;
        }
    }
    
    // If not pipe-delimited, try other formats
    if (qrCode.startsWith('TEACHER-') || qrCode.startsWith('TV-') || qrCode.startsWith('VISITOR-')) {
        const id = qrCode.split('-').pop();
        console.log('[PARSE] Detected as TEACHER/VISITOR (prefix format)');
        return { userType: 'teacher', identifier: id, rawData: qrCode };
    }
    
    // Check if it's a simple numeric ID - could be teacher or visitor
    if (/^\d{1,3}$/.test(qrCode)) {
        console.log('[PARSE] Detected as TEACHER/VISITOR (small numeric ID)');
        return { userType: 'teacher', identifier: qrCode, rawData: qrCode };
    }
    
    // Longer numeric ID or alphanumeric - likely student
    console.log('[PARSE] Detected as STUDENT (default)');
    return { userType: 'student', identifier: qrCode, rawData: qrCode };
}

// Check if user has active session
async function checkActiveSession(userType, identifier) {
    try {
        let url;
        if (userType === 'student') {
            url = `/admin/attendance/check?student_id=${encodeURIComponent(identifier)}`;
        } else {
            url = `/admin/attendance/check?user_type=${userType}&identifier=${encodeURIComponent(identifier)}`;
        }
        
        console.log("[CHECK] Checking session at:", url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            },
            cache: 'no-store'
        });
        
        console.log("[CHECK] Response status:", response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error("[CHECK] Server error response:", errorText);
            throw new Error(`Server error: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("[CHECK] Response data:", data);
        
        // Handle both response formats: {hasActiveSession: true} or {data: {hasActiveSession: true}}
        return data.hasActiveSession || (data.data && data.data.hasActiveSession) || false;
    } catch (error) {
        console.error("[CHECK] Error checking session:", error);
        showNotification("Failed to check session: " + error.message, "error");
        return false; // Default to false on error to prevent blocking
    }
}

// Handle logout
async function handleLogout(userType, identifier, activity = 'Logout') {
    showLoadingOverlay("Processing...");
    
    try {
        // First verify the user has an active session
        const hasActiveSession = await checkActiveSession(userType, identifier);
        if (!hasActiveSession) {
            showNotification('No active session found', 'info');
            return false;
        }
        
        const isStudent = userType === 'student';
        const requestBody = isStudent
            ? { 
                student_id: identifier, 
                activity: activity,
                action: 'logout',
                _token: document.querySelector('meta[name="csrf-token"]').content
            }
            : { 
                user_type: userType, 
                identifier: identifier, 
                activity: activity,
                action: 'logout',
                _token: document.querySelector('meta[name="csrf-token"]').content
            };
        
        console.log('[LOGOUT] Sending logout request:', requestBody);
        
        const response = await fetch('/admin/attendance/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestBody)
        });
        
        const data = await response.json();
        console.log('[LOGOUT] Response:', { status: response.status, ok: response.ok, data });
        
        if (response.ok) {
            const userName = data.user?.name || (isStudent ? 'Student' : 'User');
            const emoji = isStudent ? '🎓' : '👨‍🏫';
            
            showNotification(`Logout Successful`, 'success');
            
            // Clear input field and refresh table
            const qrInput = document.getElementById('qr-input');
            if (qrInput) qrInput.value = '';
            
            await refreshAttendanceTable();
            return true;
        } else {
            const errorMsg = data.message || 'Logout failed. Please try again.';
            throw new Error(errorMsg);
        }
    } catch (error) {
        console.error("Logout error:", error);
        const errorMsg = error.message || 'Logout failed. Please try again.';
        showNotification(errorMsg, 'error');
        return false;
    } finally {
        hideLoadingOverlay();
    }
}

// Show activity modal
async function showActivityModal(userType, identifier) {
    const modal = document.getElementById('activity-modal');
    const modalHeader = document.getElementById('modal-header');
    const modalTitle = document.getElementById('modal-title');
    const userTypeBadge = document.getElementById('user-type-badge');
    const userInfo = document.getElementById('user-info');
    const userDetails = document.getElementById('user-details');
    const userProfilePic = document.getElementById('user-profile-pic');
    const profilePicBorder = document.getElementById('profile-pic-border');
    const userTypeIcon = document.getElementById('user-type-icon');
    const modalSubmit = document.getElementById('modal-submit');
    
    // Set hidden fields
    document.getElementById('modal-user-type').value = userType;
    document.getElementById('modal-identifier').value = identifier;
    
    // Apply dynamic styling based on user type
    if (userType === 'student') {
        // Student styling (Blue theme)
        if (modalHeader) modalHeader.style.backgroundColor = '#dbeafe'; // blue-100
        if (modalTitle) {
            modalTitle.textContent = '🎓 Student Login';
            modalTitle.style.color = '#1e40af'; // blue-800
        }
        if (userTypeBadge) {
            userTypeBadge.textContent = 'STUDENT';
            userTypeBadge.style.backgroundColor = '#3b82f6'; // blue-500
            userTypeBadge.style.color = 'white';
        }
        if (userInfo) {
            userInfo.style.borderColor = '#3b82f6';
            userInfo.style.backgroundColor = '#eff6ff'; // blue-50
        }
        if (profilePicBorder) profilePicBorder.style.borderColor = '#3b82f6';
        if (userTypeIcon) {
            userTypeIcon.style.backgroundColor = '#3b82f6';
            userTypeIcon.textContent = '🎓';
        }
        if (modalSubmit) modalSubmit.style.backgroundColor = '#3b82f6';
    } else {
        // Teacher styling (Purple theme)
        if (modalHeader) modalHeader.style.backgroundColor = '#f3e8ff'; // purple-100
        if (modalTitle) {
            modalTitle.textContent = '👨‍🏫 Teacher/Visitor Login';
            modalTitle.style.color = '#6b21a8'; // purple-800
        }
        if (userTypeBadge) {
            userTypeBadge.textContent = 'TEACHER';
            userTypeBadge.style.backgroundColor = '#9333ea'; // purple-600
            userTypeBadge.style.color = 'white';
        }
        if (userInfo) {
            userInfo.style.borderColor = '#9333ea';
            userInfo.style.backgroundColor = '#faf5ff'; // purple-50
        }
        if (profilePicBorder) profilePicBorder.style.borderColor = '#9333ea';
        if (userTypeIcon) {
            userTypeIcon.style.backgroundColor = '#9333ea';
            userTypeIcon.textContent = '👨‍🏫';
        }
        if (modalSubmit) modalSubmit.style.backgroundColor = '#9333ea';
    }
    
    // Show loading state
    userDetails.innerHTML = '<p class="text-sm font-medium text-gray-700">Loading...</p>';
    modal.classList.remove('hidden');
    
    // Fetch user details
    try {
        let url;
        if (userType === 'student') {
            url = `/admin/attendance/scan?student_id=${encodeURIComponent(identifier)}`;
        } else {
            url = `/admin/attendance/scan?user_type=${userType}&identifier=${encodeURIComponent(identifier)}`;
        }

        console.log("[MODAL] Fetching user details from:", url);

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        console.log("[MODAL] Response status:", response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error("[MODAL] Server error response:", errorText);
            throw new Error(`Server error: ${response.status}`);
        }

        const data = await response.json();
        console.log("[MODAL] User data received:", data);

        if (response.ok && data.user) {
            const user = data.user;
            const name = data.name;
            const profilePic = data.profile_picture;

            // Update modal with user info
            if (profilePic) {
                userProfilePic.src = profilePic;
            }

            if (userType === 'student') {
                userDetails.innerHTML = `
                    <p class="text-sm font-semibold text-gray-900">${name}</p>
                    <p class="text-xs text-gray-600">ID: ${user.student_id}</p>
                    <p class="text-xs text-gray-600">College: ${user.college || 'N/A'}</p>
                `;
            } else {
                // For teachers and visitors, show the same format
                userDetails.innerHTML = `
                    <p class="text-sm font-semibold text-gray-900">${name}</p>
                    <p class="text-xs text-gray-600">Department: ${user.department || 'N/A'}</p>
                    <p class="text-xs text-gray-600">Role: ${user.role || 'Visitor'}</p>
                `;
            }
        } else {
            showNotification("User not found", "error");
            modal.classList.add('hidden');
        }
    } catch (error) {
        console.error("Error fetching user details:", error);
        showNotification("Failed to load user details", "error");
        modal.classList.add('hidden');
    }
}

// Refresh attendance tables
async function refreshAttendanceTable() {
    const startTime = performance.now();
    console.group(`[${new Date().toLocaleTimeString()}] Refreshing attendance tables...`);

    try {
        // Show loading state on refresh button
        const refreshBtn = document.getElementById('refresh-btn');
        const refreshSpinner = document.getElementById('refresh-spinner');
        const refreshText = document.getElementById('refresh-text');

        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.classList.add('opacity-75', 'cursor-not-allowed');
        }
        if (refreshSpinner) refreshSpinner.classList.remove('hidden');
        if (refreshText) refreshText.textContent = 'Refreshing...';

        // Show loading state
        const statusElement = document.getElementById('refresh-status');
        if (statusElement) {
            statusElement.textContent = 'Refreshing...';
            statusElement.className = 'text-yellow-600 text-sm';
        }

        const response = await fetch('/admin/attendance/realtime?' + new URLSearchParams({
            _: Date.now() // Prevent caching
        }), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }
        
        const data = await response.json().catch(e => {
            throw new Error(`Invalid JSON response: ${e.message}`);
        });
        
        console.debug('[ATTENDANCE] Raw API response:', data);
        
        if (!data) {
            throw new Error('Empty response from server');
        }

        // Normalize the response data structure
        const responseData = data.data || data;
        let studentData = [];
        let teacherData = [];

        // Handle different response formats
        if (Array.isArray(responseData)) {
            // If it's a flat array, separate students and teachers
            studentData = responseData.filter(item => item && (item.student_id || (item.user_type === 'student')));
            teacherData = responseData.filter(item => item && (item.teacher_id || (item.user_type === 'teacher') || item.teacher_visitor_id));
        } else if (typeof responseData === 'object') {
            // Handle object with studentAttendance/teacherAttendance or students/teachers
            studentData = Array.isArray(responseData.studentAttendance || responseData.students) ? 
                (responseData.studentAttendance || responseData.students) : [];
            teacherData = Array.isArray(responseData.teacherAttendance || responseData.teachers) ? 
                (responseData.teacherAttendance || responseData.teachers) : [];
        }

        // Additional filtering to ensure data integrity
        studentData = studentData.filter(Boolean);
        teacherData = teacherData.filter(Boolean);
        
        console.log(`[ATTENDANCE] Processed ${studentData.length} student and ${teacherData.length} teacher records`);
        console.debug('Student data sample:', studentData.slice(0, 2));
        console.debug('Teacher data sample:', teacherData.slice(0, 2));
        
        // Update the UI with the processed data
        updateStudentTable(studentData);
        updateTeacherTable(teacherData);
        
        // Update status
        const endTime = performance.now();
        const duration = Math.round(endTime - startTime);
        if (statusElement) {
            statusElement.textContent = `Last updated: ${new Date().toLocaleTimeString()} (${duration}ms)`;
            statusElement.className = 'text-gray-500 text-xs';
        }

        console.log(`[ATTENDANCE] Update completed in ${duration}ms`);
        return true;

    } catch (error) {
        console.error('[ATTENDANCE] Error refreshing attendance:', error);

        // Show error to user
        const statusElement = document.getElementById('refresh-status') ||
            document.getElementById('status-display') ||
            document.body;

        if (statusElement) {
            const errorMsg = error.message || 'Failed to load attendance data';
            statusElement.textContent = `Error: ${errorMsg}`;
            statusElement.className = 'text-red-600 text-sm';
        }

        return false;
    } finally {
        // Reset refresh button state
        const refreshBtn = document.getElementById('refresh-btn');
        const refreshSpinner = document.getElementById('refresh-spinner');
        const refreshText = document.getElementById('refresh-text');

        if (refreshBtn) {
            refreshBtn.disabled = false;
            refreshBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
        if (refreshSpinner) refreshSpinner.classList.add('hidden');
        if (refreshText) refreshText.textContent = '🔄 Refresh';

        console.groupEnd();
    }
}

// Format date time string to show only time (e.g., '09:05 AM')
function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'N/A';
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    } catch (e) {
        console.error('Error formatting date:', e, 'Input was:', dateString);
        return 'N/A';
    }
}

/**
 * Update the student attendance table with the provided data
 * @param {Array} attendance - Array of student attendance records
 */
function updateStudentTable(attendance) {
    const tbody = document.getElementById('student-attendance-table-body');
    if (!tbody) {
        console.error("[ATTENDANCE] Student table body not found");
        return;
    }
    
    // Ensure we have a valid array
    const attendanceData = Array.isArray(attendance) ? attendance : [];
    
    console.group('[ATTENDANCE] Updating student table');
    console.debug('Raw student data:', attendanceData);
    
    // Create a document fragment for better performance
    const fragment = document.createDocumentFragment();
    
    if (attendanceData.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                No student attendance records found
            </td>
        `;
        fragment.appendChild(row);
    } else {
        // Add new rows
        attendanceData.forEach((record, index) => {
            try {
                if (!record) return; // Skip invalid records
                
                console.groupCollapsed(`Student Record #${index + 1}`);
                console.debug('Raw record:', record);
                
                const row = document.createElement('tr');
                row.className = 'bg-white border-b hover:bg-gray-50 transition-colors';
                row.id = `student-row-${record.student_id || record.id || index}`;
                
                // Safely access nested properties with fallbacks
                const studentInfo = record.student || record; // Handle both nested and flat structure
                const studentName = studentInfo.name || studentInfo.student_name || 'N/A';
                const studentSection = studentInfo.section || studentInfo.student_section || studentInfo.section_name || '';
                const studentCollege = studentInfo.college || studentInfo.student_college || studentInfo.college_name || '';
                const studentCourse = studentInfo.course || studentInfo.student_course || studentInfo.course_name || '';
                const studentId = record.student_id || record.id || '';
                
    // Extract timestamps with fallbacks
    const timeInRaw = record.login || record.time_in || record.created_at || record.date;
    const timeOutRaw = record.logout || record.time_out;
                
                // Format times with validation
                const timeIn = formatDateTime(timeInRaw);
                const timeOut = timeOutRaw ?
                    formatDateTime(timeOutRaw) :
                    '';
                
                // Determine status with fallback logic
                let status = record.status;
                if (!status) {
                    status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
                }
                const statusText = status === 'out' ? 'Signed Out' : 'Signed In';
                
                console.debug('Processed student info:', { 
                    studentName, 
                    studentId,
                    studentSection, 
                    studentCollege, 
                    studentCourse,
                    timeIn,
                    timeOut,
                    status
                });
                
                console.log('Processed times:', { timeIn, timeOut, status, statusText });
                console.groupEnd();
                
                // Build the row HTML with proper fallbacks - prioritize storage profile picture
                const profilePic = record.profile_picture
                    ? (window.assetBaseUrl + 'storage/' + record.profile_picture)
                    : `https://ui-avatars.com/api/?name=${encodeURIComponent(studentName)}&background=random&size=100`;
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" title="Student ID: ${studentId}">
                        ${studentId || '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full object-cover mr-3" 
                                 src="${profilePic}" 
                                 alt="${escapeHtml(studentName)}" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(studentName || 'User')}&background=random&size=100'">
                            <div>
                                <div class="text-sm font-medium text-gray-900">${escapeHtml(studentName || 'N/A')}</div>
                                ${studentSection ? `<div class="text-xs text-gray-500">${escapeHtml(studentSection)}</div>` : ''}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        ${studentCollege ? `
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-${studentCollege.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()}">
                            ${escapeHtml(studentCollege)}
                        </span>
                        ` : '<span class="text-gray-400">N/A</span>'}
                        ${studentCourse ? `
                        <div class="mt-1 text-xs text-gray-500">${escapeHtml(studentCourse)}</div>
                        ` : ''}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${record.activity ? escapeHtml(record.activity) : '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${new Date(timeInRaw || '').toISOString() || ''}">
                        ${timeIn}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${timeOutRaw ? new Date(timeOutRaw).toISOString() : 'Still logged in'}">
                        ${timeOut}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-0.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full
                            ${status === 'out' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                            ${status === 'out' ? (
                                '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                            ) : (
                                '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                            )}
                            ${statusText}
                        </span>
                    </td>
                `;
                fragment.appendChild(row);
                console.groupEnd();
            } catch (error) {
                console.error(`[ATTENDANCE] Error processing student record at index ${index}:`, error, record);
                // Add error row for debugging
                const errorRow = document.createElement('tr');
                errorRow.className = 'bg-red-50';
                errorRow.innerHTML = `
                    <td colspan="5" class="px-6 py-4 text-sm text-red-600">
                        Error displaying record: ${error.message}
                    </td>
                `;
                fragment.appendChild(errorRow);
            }
        });
    }
    
    // Update the DOM in a single operation
    tbody.innerHTML = '';
    tbody.appendChild(fragment);
    
    // Dispatch event that the table was updated
    const event = new CustomEvent('studentTableUpdated', { 
        detail: { count: attendanceData.length }
    });
    document.dispatchEvent(event);
    
    console.log(`[ATTENDANCE] Rendered ${attendanceData.length} student records`);
    console.groupEnd();
}

// Escape HTML function
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Update teacher table (assuming similar structure; implement as needed)
function updateTeacherTable(attendance) {
    const tbody = document.getElementById('teacher-attendance-table-body');
    if (!tbody) {
        console.error("[ATTENDANCE] Teacher table body not found");
        return;
    }
    
    // Similar logic to student table; abbreviated for brevity
    const attendanceData = Array.isArray(attendance) ? attendance : [];
    
    console.group('[ATTENDANCE] Updating teacher table');
    console.debug('Raw teacher data:', attendanceData);
    
    const fragment = document.createDocumentFragment();
    
    if (attendanceData.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                No teacher attendance records found
            </td>
        `;
        fragment.appendChild(row);
    } else {
        attendanceData.forEach((record, index) => {
            try {
                if (!record) return;
                
                console.groupCollapsed(`Teacher Record #${index + 1}`);
                console.debug('Raw record:', record);
                
                const teacherInfo = record.teacher || record;
                console.debug('Teacher info:', teacherInfo);
                
                const row = document.createElement('tr');
                row.className = 'bg-white border-b hover:bg-gray-50 transition-colors';
                row.id = `teacher-row-${record.teacher_id || record.id || index}`;
                
                const teacherName = teacherInfo.name || teacherInfo.teacher_name || 'N/A';
                const teacherType = teacherInfo.type || teacherInfo.teacher_type || 'Staff';
                
                // Debug: Log the full record to see all available fields
                console.log('Full teacher record:', JSON.parse(JSON.stringify(record)));
                console.log('Teacher info object:', JSON.parse(JSON.stringify(teacherInfo)));
                
                // Try multiple possible department field names with better debugging
                const teacherDepartment = (() => {
                    // Check teacherInfo first
                    const dept = teacherInfo.department_name || 
                                teacherInfo.department || 
                                teacherInfo.teacher_department || 
                                teacherInfo.dept || 
                                teacherInfo.departmentName ||
                                (teacherInfo.department_info && teacherInfo.department_info.name) ||
                                
                                // Then check record directly
                                record.department_name ||
                                record.department || 
                                record.dept || 
                                record.departmentName ||
                                (record.department_info && record.department_info.name) ||
                                
                                // Check for nested user object
                                (teacherInfo.user && (
                                    teacherInfo.user.department_name ||
                                    teacherInfo.user.department ||
                                    teacherInfo.user.dept
                                )) ||
                                
                                // Check for any other possible variations
                                (() => {
                                    // Look for any field that might contain 'dept' or 'department' in the name
                                    const allKeys = [...Object.keys(teacherInfo), ...Object.keys(record)];
                                    const deptKey = allKeys.find(key => 
                                        key.toLowerCase().includes('dept') || 
                                        key.toLowerCase().includes('department')
                                    );
                                    
                                    if (deptKey) {
                                        return teacherInfo[deptKey] || record[deptKey];
                                    }
                                    return '';
                                })();
                    
                    console.log('Found department:', dept);
                    return dept || '';
                })();
                
                const teacherId = record.teacher_id || record.id || '';
                
                // Log all available fields for debugging
                const logAllFields = (obj, prefix = '') => {
                    const result = {};
                    for (const key in obj) {
                        if (typeof obj[key] === 'object' && obj[key] !== null) {
                            result[`${prefix}${key}`] = 'Object';
                            Object.assign(result, logAllFields(obj[key], `${prefix}${key}.`));
                        } else {
                            result[`${prefix}${key}`] = obj[key];
                        }
                    }
                    return result;
                };

                console.group('Teacher Data Debug');
                console.log('Teacher Info Fields:', logAllFields(teacherInfo));
                console.log('Record Fields:', logAllFields(record));
                console.log('Extracted Department:', teacherDepartment);
                console.groupEnd();
                
    const timeInRaw = record.login || record.time_in || record.created_at || record.date;
    const timeOutRaw = record.logout || record.time_out;
                
                const timeIn = formatDateTime(timeInRaw);
                const timeOut = timeOutRaw ?
                    formatDateTime(timeOutRaw) :
                    '';
                
                let status = record.status;
                if (!status) {
                    status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
                }
                const statusText = status === 'out' ? 'Signed Out' : 'Signed In';
                
                // Build the row HTML with proper fallbacks
                const profilePic = record.profile_picture || 
                                 teacherInfo.profile_picture || 
                                 `https://ui-avatars.com/api/?name=${encodeURIComponent(teacherName)}&background=random&size=100`;
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        ${teacherType ? `
                        <span class="px-2 py-1 inline-flex items-center text-xs font-medium rounded bg-blue-100 text-blue-700">
                            <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            ${escapeHtml(teacherType)}
                        </span>
                        ` : '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full object-cover mr-3" 
                                 src="${profilePic}" 
                                 alt="${escapeHtml(teacherName)}" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(teacherName || 'User')}&background=random&size=100'">
                            <div>
                                <div class="text-sm font-medium text-gray-900">${escapeHtml(teacherName || 'N/A')}</div>
                                ${teacherDepartment ? `<div class="text-xs text-gray-500">${escapeHtml(teacherDepartment)}</div>` : 
                                    `<div class="text-xs text-gray-400">No department</div>`}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        ${teacherDepartment ? `
                        <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            ${escapeHtml(teacherDepartment)}
                        </span>
                        ` : '<span class="px-2 py-1 text-xs text-gray-500">No department</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        ${record.activity ? escapeHtml(record.activity) : '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${new Date(timeInRaw || '').toISOString() || ''}">
                        ${timeIn}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${timeOutRaw ? new Date(timeOutRaw).toISOString() : 'Still logged in'}">
                        ${timeOut}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-0.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full
                            ${status === 'out' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                            ${status === 'out' ? (
                                '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                            ) : (
                                '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                            )}
                            ${statusText}
                        </span>
                    </td>
                `;
                fragment.appendChild(row);
            } catch (error) {
                console.error(`[ATTENDANCE] Error processing teacher record at index ${index}:`, error, record);
            }
        });
    }
    
    tbody.innerHTML = '';
    tbody.appendChild(fragment);
    
    console.log(`[ATTENDANCE] Rendered ${attendanceData.length} teacher records`);
    console.groupEnd();
}

// Hide loading overlay
function hideLoadingOverlay() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.style.opacity = '0';
        setTimeout(() => overlay.classList.add('hidden'), 200);
    }
}

/**
 * Update or add a single student record in the table
 * @param {Object} record - Student attendance record to update or add
 */
function updateSingleStudentRecord(record) {
    if (!record) {
        console.warn('[ATTENDANCE] No record provided to updateSingleStudentRecord');
        return;
    }
    
    const tbody = document.getElementById('student-attendance-table-body');
    if (!tbody) {
        console.error('[ATTENDANCE] Student table body not found');
        return;
    }
    
    const studentId = record.student_id || record.id;
    if (!studentId) {
        console.error('[ATTENDANCE] No student ID found in record:', record);
        return;
    }
    
    const rowId = `student-row-${studentId}`;
    let row = document.getElementById(rowId);
    
    // Format the data with proper fallbacks
    const studentInfo = record.student || record;
    const studentName = studentInfo.name || studentInfo.student_name || 'N/A';
    const studentSection = studentInfo.section || studentInfo.student_section || studentInfo.section_name || '';
    const studentCollege = studentInfo.college || studentInfo.student_college || studentInfo.college_name || '';
    const studentCourse = studentInfo.course || studentInfo.student_course || studentInfo.course_name || '';
    
    // Extract timestamps with fallbacks
    const timeInRaw = record.login || record.time_in || record.created_at || record.date;
    const timeOutRaw = record.logout || record.time_out;
    
    // Format times with validation
    const timeIn = formatDateTime(timeInRaw);
    const timeOut = timeOutRaw ?
        formatDateTime(timeOutRaw) :
        '';
    
    // Determine status with fallback logic
    let status = record.status;
    if (!status) {
        status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
    }
    const statusText = status === 'out' ? 'Signed Out' : 'Signed In';
    
    // Create new row if it doesn't exist
    if (!row) {
        row = document.createElement('tr');
        row.id = rowId;
        // Insert at the top of the table
        if (tbody.firstChild) {
            tbody.insertBefore(row, tbody.firstChild);
        } else {
            tbody.appendChild(row);
        }
    }
    
    // Update the row content with proper escaping and tooltips
    row.className = 'bg-white border-b hover:bg-gray-50 transition-colors';
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" title="Student ID: ${studentId}">
            ${studentId || 'N/A'}
        </td>
        <td class="px-6 py-4">
            <div class="text-sm font-medium text-gray-900">${escapeHtml(studentName)}</div>
            ${studentSection ? `<div class="text-xs text-gray-500">${escapeHtml(studentSection)}</div>` : ''}
            ${studentCollege ? `<div class="text-xs text-gray-500">${escapeHtml(studentCollege)}</div>` : ''}
            ${studentCourse ? `<div class="text-xs text-gray-400">${escapeHtml(studentCourse)}</div>` : ''}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${new Date(timeInRaw || '').toISOString() || ''}">
            ${timeIn}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${timeOutRaw ? new Date(timeOutRaw).toISOString() : 'Still logged in'}">
            ${timeOut}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2.5 py-0.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full 
                ${status === 'out' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                ${status === 'out' ? (
                    '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                ) : (
                    '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                )}
                ${statusText}
            </span>
        </td>
    `;
    
    // If this was the first row, remove the "no records" message if it exists
    const noRecordsRow = tbody.querySelector('tr td[colspan="5"]');
    if (noRecordsRow && noRecordsRow.textContent.includes('No student attendance records found')) {
        noRecordsRow.closest('tr').remove();
    }
}

/**
 * Update or add a single teacher record in the table
 * @param {Object} record - Teacher attendance record to update or add
 */
function updateSingleTeacherRecord(record) {
    if (!record) {
        console.warn('[ATTENDANCE] No record provided to updateSingleTeacherRecord');
        return;
    }

    const tbody = document.getElementById('teacher-attendance-table-body');
    if (!tbody) {
        console.error('[ATTENDANCE] Teacher table body not found');
        return;
    }

    const teacherId = record.teacher_id || record.teacher_visitor_id || record.id;
    if (!teacherId) {
        console.error('[ATTENDANCE] No teacher ID found in record:', record);
        return;
    }

    const rowId = `teacher-row-${teacherId}`;

    // Format the data with proper fallbacks
    const teacherInfo = record.teacher || record;
    const teacherName = teacherInfo.name || teacherInfo.teacher_name || 'N/A';
    const teacherType = teacherInfo.type || teacherInfo.teacher_type || 'Staff';

    // Try multiple possible department field names
    const teacherDepartment = teacherInfo.department ||
                            teacherInfo.teacher_department ||
                            teacherInfo.dept ||
                            teacherInfo.department_name ||
                            record.department ||
                            record.dept ||
                            record.department_name ||
                            '';

    const timeInRaw = record.login || record.time_in || record.created_at || record.date;
    const timeOutRaw = record.logout || record.time_out;

    // Format times with validation
    const timeIn = formatDateTime(timeInRaw);
    const timeOut = timeOutRaw ?
        formatDateTime(timeOutRaw) :
        '';

    // Determine status with fallback logic
    let status = record.status;
    if (!status) {
        status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
    }
    const statusText = status === 'out' ? 'Signed Out' : 'Signed In';

                // Build profile pic with fallback - prioritize storage profile picture
                const profilePic = record.profile_picture
                    ? (window.assetBaseUrl + 'storage/' + record.profile_picture)
                    : `https://ui-avatars.com/api/?name=${encodeURIComponent(teacherName)}&background=random&size=100`;

    // Create new row if it doesn't exist
    let row = document.getElementById(rowId);
    if (!row) {
        row = document.createElement('tr');
        row.id = rowId;
        // Insert at the top of the table
        if (tbody.firstChild) {
            tbody.insertBefore(row, tbody.firstChild);
        } else {
            tbody.appendChild(row);
        }
    }

    // Update the row content with proper escaping and tooltips
    row.className = 'bg-white border-b hover:bg-gray-50 transition-colors';
    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            ${teacherType ? `
            <span class="px-2 py-1 inline-flex items-center text-xs font-medium rounded bg-blue-100 text-blue-700">
                <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
                ${escapeHtml(teacherType)}
            </span>
            ` : '<span class="text-gray-400">N/A</span>'}
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center">
                <img class="h-10 w-10 rounded-full object-cover mr-3"
                     src="${profilePic}"
                     alt="${escapeHtml(teacherName)}"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(teacherName || 'User')}&background=random&size=100'">
                <div>
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(teacherName || 'N/A')}</div>
                    ${teacherDepartment ? `<div class="text-xs text-gray-500">${escapeHtml(teacherDepartment)}</div>` :
                        `<div class="text-xs text-gray-400">No department</div>`}
                </div>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            ${teacherDepartment ? `
            <span class="px-2 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
                ${escapeHtml(teacherDepartment)}
            </span>
            ` : '<span class="px-2 py-1 text-xs text-gray-500">No department</span>'}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            ${record.activity ? escapeHtml(record.activity) : '<span class="text-gray-400">N/A</span>'}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${new Date(timeInRaw || '').toISOString() || ''}">
            ${timeIn}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" title="${timeOutRaw ? new Date(timeOutRaw).toISOString() : 'Still logged in'}">
            ${timeOut}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2.5 py-0.5 inline-flex items-center text-xs leading-5 font-semibold rounded-full
                ${status === 'out' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                ${status === 'out' ? (
                    '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                ) : (
                    '<svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>'
                )}
                ${statusText}
            </span>
        </td>
    `;

    // If this was the first row, remove the "no records" message if it exists
    const noRecordsRow = tbody.querySelector('tr td[colspan="9"]');
    if (noRecordsRow && noRecordsRow.textContent.includes('No teacher attendance records found')) {
        noRecordsRow.closest('tr').remove();
    }
}

// Show notification with improved styling and auto-dismiss
function showNotification(message, type = "info") {
    // Don't show empty messages
    if (!message) return;
    
    // Close any existing notifications of the same type to avoid duplicates
    document.querySelectorAll(`.notification-${type}`).forEach(el => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300);
    });
    // Create notification element
    const notification = document.createElement('div');
    const icons = {
        success: '✓',
        error: '✕',
        warning: '!',
        info: 'i'
    };

    // Add a class for the notification type
    const notificationClasses = [
        'fixed', 'top-4', 'right-4', 'px-6', 'py-4', 'rounded-lg', 'shadow-2xl',
        'text-white', 'font-medium', 'flex', 'items-center', 'space-x-3', 'z-50',
        'transform', 'transition-all', 'duration-300', 'ease-in-out',
        'notification', `notification-${type}`, // Add notification and type-specific class
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    ];

    notification.className = notificationClasses.join(' ');
    notification.innerHTML = `
        <span class="text-lg">${icons[type]}</span>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    // Auto-remove after appropriate duration
    const duration = type === 'error' ? 8000 : 5000; // Show errors longer
    
    // Add a progress bar
    const progressBar = document.createElement('div');
    progressBar.className = 'absolute bottom-0 left-0 h-1 bg-white bg-opacity-50 w-full origin-left';
    notification.appendChild(progressBar);
    
    // Animate progress bar
    progressBar.style.transition = `transform ${duration}ms linear`;
    setTimeout(() => progressBar.style.transform = 'scaleX(0)', 50);
    
    // Auto-remove after duration
    const removeNotification = () => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    };
    
    const timeoutId = setTimeout(removeNotification, duration);
    
    // Pause on hover
    notification.addEventListener('mouseenter', () => {
        clearTimeout(timeoutId);
        progressBar.style.transition = 'none';
        progressBar.style.transform = 'scaleX(1)';
    });
    
    notification.addEventListener('mouseleave', () => {
        const remainingWidth = progressBar.getBoundingClientRect().width / notification.getBoundingClientRect().width;
        const remainingTime = remainingWidth * duration;
        
        progressBar.style.transition = `transform ${remainingTime}ms linear`;
        progressBar.style.transform = 'scaleX(0)';
        
        setTimeout(removeNotification, remainingTime);
    });
}

// Handle scan errors (suppress frequent errors)
function onScanError(error) {
    // Suppress console spam from scanning errors
    if (error && !error.includes('NotFoundException')) {
        console.warn("Scan error:", error);
    }
}

// Toggle fullscreen
function toggleFullScreen() {
    const section = document.getElementById('fullscreen-section');
    
    if (!document.fullscreenElement) {
        section.requestFullscreen().catch(err => {
            console.error("Fullscreen error:", err);
        });
    } else {
        document.exitFullscreen();
    }
}

// Show loading overlay
function showLoadingOverlay(message = 'Loading...') {
    let overlay = document.getElementById('loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
        overlay.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="flex items-center gap-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                    <p class="text-gray-700">${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    overlay.classList.remove('hidden');
    overlay.style.opacity = '1';
}

// Book Selection Modal Functions
let borrowUiInitialized = false;

// Show book selection modal
function showBookSelectionModal(userType, identifier, activity) {
    const modal = document.getElementById('book-selection-modal');
    const manualUserType = document.getElementById('manual-user-type');
    const manualIdentifier = document.getElementById('manual-identifier');

    // Set the user info for manual borrow form
    manualUserType.value = userType;
    manualIdentifier.value = identifier;

    // Store the activity for later use
    modal.dataset.activity = activity;

    // Hide the activity modal
    document.getElementById('activity-modal').classList.add('hidden');

    // Show the modal
    modal.classList.remove('hidden');

    // Initialize borrow UI if not already done
    if (!borrowUiInitialized) {
        initializeBorrowUI();
        borrowUiInitialized = true;
    }

    // Load available books
    loadAvailableBooks();
}

// Initialize borrow UI
function initializeBorrowUI() {
    const availableBooksSearch = document.getElementById('available-books-search');
    const availableBooksCollege = document.getElementById('available-books-college');
    const refreshAvailableBooksBtn = document.getElementById('refresh-available-books');
    const manualBorrowForm = document.getElementById('manual-borrow-form');
    const bookSelectionCancel = document.getElementById('book-selection-cancel');
    
    // Search functionality
    if (availableBooksSearch) {
        const debouncedSearch = debounce(() => {
            loadAvailableBooks(availableBooksSearch.value.trim());
        }, 300);
        availableBooksSearch.addEventListener('input', debouncedSearch);
    }
    
    // Refresh button
    if (refreshAvailableBooksBtn) {
        refreshAvailableBooksBtn.addEventListener('click', () => {
            const search = availableBooksSearch?.value.trim() || '';
            const college = availableBooksCollege?.value || '';
            loadAvailableBooks(search, college);
        });
    }
    
    // College filter
    if (availableBooksCollege) {
        availableBooksCollege.addEventListener('change', () => {
            const search = availableBooksSearch?.value.trim() || '';
            const college = availableBooksCollege.value || '';
            loadAvailableBooks(search, college);
        });
    }
    
    // Manual borrow form
    if (manualBorrowForm) {
        manualBorrowForm.addEventListener('submit', handleManualBorrow);
    }
    
    // Cancel button (now Back button)
    if (bookSelectionCancel) {
        bookSelectionCancel.addEventListener('click', () => {
            // Hide book selection modal and show activity modal again
            document.getElementById('book-selection-modal').classList.add('hidden');
            document.getElementById('activity-modal').classList.remove('hidden');
            // Keep the back button visible since we're returning to the activity selection
        });
    }
    
    // Load colleges for filter
    loadColleges();
}

// Load colleges for filter
async function loadColleges() {
    try {
        const response = await fetch('/admin/attendance/books/colleges');
        const colleges = await response.json();
        
        const collegeSelect = document.getElementById('available-books-college');
        if (collegeSelect && colleges) {
            collegeSelect.innerHTML = '<option value="">All Colleges</option>';
            colleges.forEach(college => {
                const option = document.createElement('option');
                option.value = college;
                option.textContent = college;
                collegeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading colleges:', error);
    }
}

// Load available books
async function loadAvailableBooks(search = '', college = '') {
    const container = document.getElementById('available-books-container');
    const list = document.getElementById('available-books-list');
    
    if (!container || !list) return;
    
    // Show loading state
    container.innerHTML = '<div class="p-4 text-sm text-gray-500">Loading available books...</div>';
    
    try {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (college) params.append('college', college);
        
        const response = await fetch(`/admin/attendance/available-books?${params.toString()}`);
        const data = await response.json();
        const books = data.data || data;
        
        if (!books || books.length === 0) {
            container.innerHTML = '<div class="p-4 text-sm text-gray-500">No available books found.</div>';
            return;
        }
        
        // Clear loading state and render books
        container.innerHTML = '';
        list.innerHTML = '';
        
        books.forEach(book => {
            const bookCard = createBookCard(book);
            list.appendChild(bookCard);
        });
        
        container.appendChild(list);
        
    } catch (error) {
        console.error('Error loading books:', error);
        container.innerHTML = '<div class="p-4 text-sm text-red-500">Error loading books. Please try again.</div>';
    }
}

// Create book card element
function createBookCard(book) {
    const card = document.createElement('div');
    card.className = 'bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer';

    const imageUrl = book.image1 ? `/storage/${book.image1}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(book.name)}&background=random&size=200`;

    card.innerHTML = `
        <div class="aspect-w-3 aspect-h-4 mb-3">
            <img src="${imageUrl}" alt="${escapeHtml(book.name)}" class="w-full h-48 object-cover rounded-lg" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(book.name)}&background=random&size=200'">
        </div>
        <div class="space-y-2">
            <h3 class="font-semibold text-gray-900 text-sm line-clamp-2">${escapeHtml(book.name)}</h3>
            <p class="text-sm text-gray-600 truncate">${escapeHtml(book.author || 'Unknown author')}</p>
            <p class="text-xs text-gray-500">Code: ${escapeHtml(book.book_code)}</p>
            <p class="text-xs text-gray-500">Section: ${escapeHtml(book.section || 'N/A')}</p>
            <button class="w-full mt-3 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors borrow-book-btn" data-book-code="${escapeHtml(book.book_code)}" data-book-name="${escapeHtml(book.name)}">
                Borrow Book
            </button>
        </div>
    `;
    
    // Add click handler for borrow button
    const borrowBtn = card.querySelector('.borrow-book-btn');
    borrowBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        handleBorrowBook(book.book_code, book.name);
    });
    
    // Add click handler for auto-fill manual form
    card.addEventListener('click', () => {
        const manualBookId = document.getElementById('manual-book-id');
        if (manualBookId) {
            manualBookId.value = book.book_code;
            manualBookId.focus();
        }
    });
    
    return card;
}

// Handle borrow book request
async function handleBorrowBook(bookCode, bookName) {
    const modal = document.getElementById('book-selection-modal');
    const userType = document.getElementById('manual-user-type').value;
    const identifier = document.getElementById('manual-identifier').value;
    const activity = modal.dataset.activity;

    // Close modal immediately
    modal.classList.add('hidden');

    showLoadingOverlay(`Requesting to borrow "${bookName}"...`);

    try {
        const response = await fetch('/admin/borrow/request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                student_id: identifier,
                user_type: userType,
                book_id: bookCode,
                activity: activity
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            showNotification(`Borrow request for "${bookName}" submitted successfully!`, 'success');

            // Clear QR input and refresh table
            const qrInput = document.getElementById('qr-input');
            if (qrInput) qrInput.value = '';
            await refreshAttendanceTable();

        } else {
            const errorMsg = data.message || data.error || 'Failed to submit borrow request.';
            showNotification(errorMsg, 'error');
        }
    } catch (error) {
        console.error('Error submitting borrow request:', error);
        showNotification('Network error. Please try again.', 'error');
    } finally {
        hideLoadingOverlay();
    }
}

// Handle manual borrow form submission
async function handleManualBorrow(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const bookCode = formData.get('book_id');
    
    if (!bookCode) {
        showNotification('Please enter a book code.', 'error');
        return;
    }
    
    await handleBorrowBook(bookCode, `Book ${bookCode}`);
}

// Log attendance with activity (after successful borrow request)
async function logAttendanceWithActivity(userType, identifier, activity) {
    try {
        let requestBody;
        if (userType === 'student') {
            requestBody = {
                student_id: identifier,
                activity: activity
            };
        } else {
            requestBody = {
                user_type: userType,
                identifier: identifier,
                activity: activity
            };
        }
        
        const response = await fetch('/admin/attendance/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestBody)
        });
        
        const data = await response.json();
        
        if (response.ok && data && (data.success || data.action === 'logged in')) {
            const userName = data.name || 'User';
            const emoji = userType === 'student' ? '🎓' : '👨‍🏫';
            const welcomeText = `Welcome, ${userName}! ${emoji} Your borrow request has been submitted.`;
            showNotification(welcomeText, 'success');
        }
    } catch (error) {
        console.error('Error logging attendance:', error);
        // Don't show error for attendance logging as borrow request was successful
    }
}

// Load initial student attendance records (all records for scrolling)
function loadInitialStudentRecords() {
    const tbody = document.getElementById('student-attendance-table-body');
    if (!tbody) {
        console.error('[INIT] Student table body not found');
        return;
    }

    // Check if data is available
    if (!window.studentAttendanceData || !Array.isArray(window.studentAttendanceData)) {
        console.log('[INIT] No student attendance data available');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    No student attendance records for today
                </td>
            </tr>
        `;
        return;
    }

    const attendanceData = window.studentAttendanceData;
    console.log(`[INIT] Loading all ${attendanceData.length} student records for scrolling`);

    // Create a document fragment for better performance
    const fragment = document.createDocumentFragment();

    if (attendanceData.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                No student attendance records for today
            </td>
        `;
        fragment.appendChild(row);
    } else {
        // Load all records for scrolling
        attendanceData.forEach((record, index) => {
            try {
                if (!record) return; // Skip invalid records

                const row = document.createElement('tr');
                row.className = 'bg-white border-b hover:bg-gray-50 transition-colors';
                row.id = `student-row-${record.student_id || record.id || index}`;
                row.setAttribute('data-attendance-id', record.id || '');
                row.setAttribute('data-user-type', 'student');

                // Safely access nested properties with fallbacks
                const studentInfo = record.student || record; // Handle both nested and flat structure
                const studentName = studentInfo.name || studentInfo.student_name || 'N/A';
                const studentSection = studentInfo.section || studentInfo.student_section || studentInfo.section_name || '';
                const studentCollege = studentInfo.college || studentInfo.student_college || studentInfo.college_name || '';
                const studentCourse = studentInfo.course || studentInfo.student_course || studentInfo.course_name || '';
                const studentId = record.student_id || record.id || '';

                // Extract timestamps with fallbacks
                const timeInRaw = record.login || record.time_in || record.created_at || record.date;
                const timeOutRaw = record.logout || record.time_out;

                // Format times with validation
                const timeIn = formatDateTime(timeInRaw);
                const timeOut = timeOutRaw ?
                    formatDateTime(timeOutRaw) :
                    '';

                // Determine status with fallback logic
                let status = record.status;
                if (!status) {
                    status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
                }
                const statusText = status === 'out' ? 'Logged Out' : 'Present';

                // Build the row HTML with proper fallbacks
                const profilePic = record.profile_picture ||
                                 studentInfo.profile_picture ||
                                 `https://ui-avatars.com/api/?name=${encodeURIComponent(studentName)}&background=random&size=100`;

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" title="Student ID: ${studentId}">
                        ${studentId || '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
                        <img src="${profilePic}"
                            alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100" />
                        <span class="font-medium">${escapeHtml(studentName)}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${studentCollege ? `
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-${studentCollege.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()}">
                            ${escapeHtml(studentCollege)}
                        </span>
                        ` : '<span class="text-gray-400">N/A</span>'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${(() => {
                            const activity = record.activity || '';
                            if (activity.toLowerCase().includes('wait for approval')) {
                                return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">${escapeHtml(activity)}</span>`;
                            } else if (activity.toLowerCase().includes('borrow:')) {
                                return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${escapeHtml(activity)}</span>`;
                            } else {
                                return escapeHtml(activity);
                            }
                        })()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${timeIn}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${timeOut}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        ${status === 'out' ?
                            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Logged Out</span>' :
                            '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>'
                        }
                    </td>
                `;
                fragment.appendChild(row);
            } catch (error) {
                console.error(`[INIT] Error processing student record at index ${index}:`, error, record);
                // Add error row for debugging
                const errorRow = document.createElement('tr');
                errorRow.className = 'bg-red-50';
                errorRow.innerHTML = `
                    <td colspan="7" class="px-6 py-4 text-sm text-red-600">
                        Error displaying record: ${error.message}
                    </td>
                `;
                fragment.appendChild(errorRow);
            }
        });
    }

    // Update the DOM in a single operation
    tbody.innerHTML = '';
    tbody.appendChild(fragment);

    // Update loaded count to total (all records loaded)
    tbody.setAttribute('data-loaded', attendanceData.length);

    console.log(`[INIT] Rendered all ${attendanceData.length} student records for scrolling`);
}

// Load more student records when scrolling or clicking load more
function loadMoreStudentRecords() {
    const tbody = document.getElementById('student-attendance-table-body');
    if (!tbody) return;

    const currentLoaded = parseInt(tbody.getAttribute('data-loaded') || '0');
    const totalRecords = parseInt(tbody.getAttribute('data-total') || '0');

    if (currentLoaded >= totalRecords) return;

    const recordsToLoad = Math.min(8, totalRecords - currentLoaded); // Load next 8 or remaining
    const startIndex = currentLoaded;
    const endIndex = startIndex + recordsToLoad;

    console.log(`[LOAD MORE] Loading records ${startIndex + 1} to ${endIndex} of ${totalRecords}`);

    if (!window.studentAttendanceData || !Array.isArray(window.studentAttendanceData)) {
        console.error('[LOAD MORE] No student attendance data available');
        return;
    }

    const attendanceData = window.studentAttendanceData.slice(startIndex, endIndex);

    // Create fragment for new rows
    const fragment = document.createDocumentFragment();

    attendanceData.forEach((record, index) => {
        try {
            if (!record) return;

            const row = document.createElement('tr');
            row.className = 'bg-white border-b hover:bg-gray-50 transition-colors';
            row.id = `student-row-${record.student_id || record.id || (startIndex + index)}`;
            row.setAttribute('data-attendance-id', record.id || '');
            row.setAttribute('data-user-type', 'student');

            const studentInfo = record.student || record;
            const studentName = studentInfo.name || studentInfo.student_name || 'N/A';
            const studentSection = studentInfo.section || studentInfo.student_section || studentInfo.section_name || '';
            const studentCollege = studentInfo.college || studentInfo.student_college || studentInfo.college_name || '';
            const studentCourse = studentInfo.course || studentInfo.student_course || studentInfo.course_name || '';
            const studentId = record.student_id || record.id || '';

            const timeInRaw = record.login || record.time_in || record.created_at || record.date;
            const timeOutRaw = record.logout || record.time_out;

            const timeIn = formatDateTime(timeInRaw);
            const timeOut = timeOutRaw ? formatDateTime(timeOutRaw) : '';

            let status = record.status;
            if (!status) {
                status = (timeOutRaw || record.logout || record.time_out) ? 'out' : 'in';
            }
            const statusText = status === 'out' ? 'Logged Out' : 'Present';

            // Prioritize storage profile picture over avatar service
            const profilePic = record.profile_picture
                ? (window.assetBaseUrl + 'storage/' + record.profile_picture)
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(studentName)}&background=random&size=100`;

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" title="Student ID: ${studentId}">
                    ${studentId || '<span class="text-gray-400">N/A</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
                    <img src="${profilePic}"
                        alt="Profile" class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100" />
                    <span class="font-medium">${escapeHtml(studentName)}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${studentCollege ? `
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-${studentCollege.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()}">
                        ${escapeHtml(studentCollege)}
                    </span>
                    ` : '<span class="text-gray-400">N/A</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${(() => {
                        const activity = record.activity || '';
                        if (activity.toLowerCase().includes('wait for approval')) {
                            return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">${escapeHtml(activity)}</span>`;
                        } else if (activity.toLowerCase().includes('borrow:')) {
                            return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${escapeHtml(activity)}</span>`;
                        } else {
                            return escapeHtml(activity);
                        }
                    })()}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${timeIn}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${timeOut}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${status === 'out' ?
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Logged Out</span>' :
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>'
                    }
                </td>
            `;
            fragment.appendChild(row);
        } catch (error) {
            console.error(`[LOAD MORE] Error processing record at index ${index}:`, error);
        }
    });

    // Insert new rows before the load more button
    const loadMoreRow = document.getElementById('load-more-row');
    if (loadMoreRow) {
        tbody.insertBefore(fragment, loadMoreRow);

        // Update loaded count
        const newLoaded = currentLoaded + recordsToLoad;
        tbody.setAttribute('data-loaded', newLoaded);

        // Update or remove load more button
        if (newLoaded >= totalRecords) {
            loadMoreRow.remove();
        } else {
            const remaining = totalRecords - newLoaded;
            loadMoreRow.querySelector('#load-more-btn').textContent = `Load More (${remaining} remaining)`;
        }
    }

    console.log(`[LOAD MORE] Added ${recordsToLoad} more records. Total loaded: ${tbody.getAttribute('data-loaded')}`);
}

// Check if activity is study-related
function isStudyRelatedActivity(activity) {
    if (!activity) return false;

    console.log('[DEBUG] Checking if activity is study-related:', activity);
    const studyKeywords = ['study', 'stay', 'reading', 'research', 'group study', 'computer use', 'meeting'];
    const lowerActivity = activity.toLowerCase();

    const result = studyKeywords.some(keyword => lowerActivity.includes(keyword));
    console.log('[DEBUG] Study keywords check result:', result, 'for keywords:', studyKeywords);

    return result;
}

// Check study area availability
async function checkStudyAreaAvailability() {
    try {
        console.log('[DEBUG] Fetching study area availability from API...');
        const response = await fetch('/api/study-area/availability');
        console.log('[DEBUG] API response status:', response.status);

        const data = await response.json();
        console.log('[DEBUG] API response data:', data);

        if (data.success) {
            console.log('[DEBUG] Returning study area data:', data.data);
            return data.data;
        }
        console.log('[DEBUG] API call unsuccessful, returning null');
        return null;
    } catch (error) {
        console.error('Error checking study area availability:', error);
        return null;
    }
}

// Show study area full warning modal
function showStudyAreaFullWarningModal() {
    const modal = document.getElementById('study-area-full-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Debounce utility function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

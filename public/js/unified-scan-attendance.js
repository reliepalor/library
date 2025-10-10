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

    // Check if Html5Qrcode library is loaded
    if (typeof Html5Qrcode === 'undefined') {
        console.error('[INIT] Html5Qrcode library not loaded');
        showNotification('Scanner library not loaded. Please refresh the page.', 'error');
        return;
    }
    console.log('[INIT] Html5Qrcode library available');

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

    // Initialize scanner components
    try {
        // Initialize HTML5 QR Code scanner
        html5QrCode = new Html5Qrcode("qr-reader");
        console.log('[INIT] HTML5 QR Code scanner initialized successfully');

        // Set up scanner modes and physical scanner
        setupScannerModes();
        setupPhysicalScanner();

        // Always default to physical scanner for reliability
        console.log('[INIT] Defaulting to physical scanner');
        physicalModeBtn.click();

        console.log('[INIT] Scanner initialized successfully');
    } catch (error) {
        console.error('[INIT] Error initializing scanner:', error);
        showNotification('Error initializing scanner. Falling back to physical mode.', 'error');

        // Fallback to physical scanner
        try {
            setupScannerModes();
            setupPhysicalScanner();
            physicalModeBtn.click();
        } catch (fallbackError) {
            console.error('[INIT] Fallback initialization also failed:', fallbackError);
            showNotification('Scanner initialization completely failed. Please refresh the page.', 'error');
        }
    }

    // Initial data load
    refreshAttendanceTable();

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

    // Borrow modal helpers
    let borrowUiInitialized = false;
    let sectionsInitialized = false;

        const renderAvailableBooks = (books = []) => {
            if (!availableBooksList) return;
            availableBooksList.innerHTML = '';
            if (!books.length) {
                availableBooksList.innerHTML = '<div class="p-6 text-gray-500 text-sm col-span-3">No available books found.</div>';
                return;
            }
            // Do NOT rebuild dropdown on every render; it will hide other options when filtered.
            const frag = document.createDocumentFragment();
            books.forEach((b) => {
                const li = document.createElement('div');
                // Product-like card: breathable but compact
                li.className = 'bg-white rounded-xl shadow-sm ring-1 ring-gray-100 hover:shadow-md transition p-4 flex flex-col gap-3';
                const imgSrc = b.image1 ? (window.assetBaseUrl + 'storage/' + b.image1) : (window.assetBaseUrl + 'images/book-placeholder.png');
                li.innerHTML = `
                    <div class="w-full aspect-[5/3] bg-gray-50 rounded-lg overflow-hidden ring-1 ring-gray-200">
                        <img src="${imgSrc}" alt="${b.name || 'Book'}" class="w-full h-full object-cover" onerror="this.src='${window.assetBaseUrl}images/book-placeholder.png'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">${b.name || 'Untitled'}</p>
                                <p class="text-sm text-gray-600 truncate">${b.author || 'Unknown author'}</p>
                                ${b.section ? `<p class="text-xs text-gray-500 mt-0.5 truncate">Section: ${b.section}</p>` : ''}
                            </div>
                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-200">${b.book_code}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-end pt-1">
                        <button class="borrow-book-btn px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700" data-code="${b.book_code}">Borrow</button>
                    </div>
                `;
                frag.appendChild(li);
            });
            availableBooksList.appendChild(frag);
        };

        const loadAvailableBooks = async (search = '', college = '') => {
            try {
                // Show subtle inline loading state in the container
                if (availableBooksList) {
                    availableBooksList.innerHTML = '<div class="p-4 text-sm text-gray-500 col-span-3">Loading available books...</div>';
                }
                const url = new URL(window.location.origin + '/admin/attendance/available-books');
                if (search) url.searchParams.set('search', search);
                if (college) url.searchParams.set('college', college);
                url.searchParams.set('limit', '100');
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed fetching available books');
                // Initialize section/college dropdown only once using the first unfiltered payload
                if (!sectionsInitialized && availableBooksCollege) {
                    const isUnfiltered = !search && !college;
                    if (isUnfiltered) {
                        const unique = Array.from(new Set((data.data || []).map(b => b.section).filter(Boolean))).sort();
                        // Build options once
                        availableBooksCollege.innerHTML = '';
                        const allOpt = document.createElement('option');
                        allOpt.value = '';
                        allOpt.textContent = 'All Colleges';
                        availableBooksCollege.appendChild(allOpt);
                        unique.forEach(sec => {
                            const o = document.createElement('option');
                            o.value = sec;
                            o.textContent = sec;
                            availableBooksCollege.appendChild(o);
                        });
                        sectionsInitialized = true;
                    }
                }
                renderAvailableBooks(data.data || []);
            } catch (e) {
                console.error(e);
                if (availableBooksList) {
                    availableBooksList.innerHTML = '<div class="p-4 text-sm text-red-600 col-span-3">Failed to load available books.</div>';
                }
            }
        };
    
// Setup scanner mode toggle
function setupScannerModes() {
    const webcamBtn = document.getElementById('webcam-mode-btn');
    const physicalBtn = document.getElementById('physical-mode-btn');
    const webcamContainer = document.getElementById('webcam-container');
    const physicalContainer = document.getElementById('physical-container');
    const modeDescription = document.getElementById('mode-description');
    const scannerContainer = document.getElementById('qr-reader');

    console.log('[MODE] Setting up scanner mode toggles');

    webcamBtn.addEventListener('click', async () => {
        console.log('[MODE] Webcam mode button clicked');
        if (isScanning) {
            console.log('[MODE] Webcam already scanning, ignoring click');
            return; // Prevent multiple clicks
        }

        console.log('[MODE] Switching to webcam mode');

        // Update UI
        webcamBtn.classList.add('bg-blue-600', 'text-white');
        webcamBtn.classList.remove('bg-gray-300', 'text-gray-700');
        physicalBtn.classList.add('bg-gray-300', 'text-gray-700');
        physicalBtn.classList.remove('bg-blue-600', 'text-white');

        // Show loading state
        if (scannerLoading) scannerLoading.style.display = 'block';
        if (scannerError) scannerError.classList.add('hidden');
        if (scannerContainer) scannerContainer.style.display = 'none';

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
        if (scannerContainer) scannerContainer.style.display = 'none';
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

// Initialize webcam scanner
function initializeWebcamScanner() {
    html5QrCode = new Html5Qrcode("qr-reader");
    startWebcamScanner();
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
        throw new Error('Scanner container not found');
    }

    console.log('[SCANNER] Elements found, showing loading');

    // Show loading state
    if (scannerLoading) scannerLoading.style.display = 'block';
    if (scannerError) scannerError.classList.add('hidden');
    scannerContainer.style.display = 'none';
    
    // Ensure webcam container is visible
    const webcamContainer = document.getElementById('webcam-container');
    const physicalContainer = document.getElementById('physical-container');
    if (webcamContainer) webcamContainer.classList.remove('hidden');
    if (physicalContainer) physicalContainer.classList.add('hidden');
    
    // Show loading state
    scannerContainer.innerHTML = `
        <div class="flex items-center justify-center h-64 bg-gray-100 rounded-lg">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-600">Accessing camera...</p>
            </div>
        </div>
    `;
    
    // Try to get list of cameras first
    Html5Qrcode.getCameras().then(devices => {
        if (!devices || devices.length === 0) {
            throw new Error('No cameras found');
        }
        
        // Try to use the environment (back) camera first, fallback to any available camera
        const facingMode = devices.some(device => device.label.toLowerCase().includes('back')) 
            ? { facingMode: "environment" } 
            : { facingMode: "user" };
        
        console.log('[SCANNER] Starting camera with config:', { facingMode });
        
        return html5QrCode.start(
            facingMode,
            { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            },
            (decodedText) => onScanSuccess(decodedText),
            (errorMessage) => onScanError(errorMessage)
        );
    }).then(() => {
        isScanning = true;
        console.log("[SCANNER] Webcam scanner started successfully");

        // Clear loading state
        if (scannerLoading) scannerLoading.style.display = 'none';
        if (scannerContainer) {
            scannerContainer.innerHTML = '';
            scannerContainer.style.display = 'block';
        }
    }).catch(err => {
        console.error("[SCANNER] Failed to start scanner:", err);

        // Show error in scanner container
        if (scannerContainer) {
            scannerContainer.innerHTML = `
                <div class="p-4 text-center">
                    <div class="text-red-600 font-medium mb-2">Camera Error</div>
                    <p class="text-sm text-gray-600 mb-3">${err.message || 'Failed to access camera'}</p>
                    <button onclick="startWebcamScanner()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Try Again
                    </button>
                </div>
            `;
            scannerContainer.style.display = 'block';
        }
        if (scannerLoading) scannerLoading.style.display = 'none';

        showNotification("Failed to start camera. Please check permissions and try again.", "error");
    });
}

// Stop webcam scanning
async function stopWebcamScanner() {
    if (!isScanning || !html5QrCode) return;
    
    try {
        await html5QrCode.stop();
        isScanning = false;
        console.log('[SCANNER] Webcam scanner stopped');
        return true;
    } catch (err) {
        console.error('[SCANNER] Failed to stop scanner:', err);
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
            
            // Determine if student or teacher based on role
            if (roleOrYear && (roleOrYear.toLowerCase().includes('teacher') || roleOrYear.toLowerCase().includes('prof'))) {
                console.log('[PARSE] Detected as TEACHER');
                return { 
                    userType: 'teacher', 
                    identifier: identifier, 
                    rawData: qrCode,
                    name: name,
                    collegeOrDept: collegeOrDept,
                    role: roleOrYear
                };
            } else {
                // Default to student if not explicitly teacher
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
    if (qrCode.startsWith('TEACHER-') || qrCode.startsWith('TV-')) {
        const id = qrCode.split('-').pop();
        console.log('[PARSE] Detected as TEACHER (prefix format)');
        return { userType: 'teacher', identifier: id, rawData: qrCode };
    }
    
    // Check if it's a simple numeric ID - could be teacher
    if (/^\d{1,3}$/.test(qrCode)) {
        console.log('[PARSE] Detected as TEACHER (small numeric ID)');
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
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log("[CHECK] Response status:", response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error("[CHECK] Server error response:", errorText);
            throw new Error(`Server error: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("[CHECK] Response data:", data);
        return data.hasActiveSession;
    } catch (error) {
        console.error("[CHECK] Error checking session:", error);
        throw error;
    }
}

// Handle logout
async function handleLogout(userType, identifier, activity = 'Logout') {
    showLoadingOverlay("Logging out...");
    
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
        
        if (response.ok && (data.type === 'logout' || data.message)) {
            const message = `${userType === 'student' ? 'Student' : 'Teacher'} logged out successfully!`;
            showNotification(message, "success");
            await refreshAttendanceTable();
            // Reset the input field for next scan
            const qrInput = document.getElementById('qr-input');
            if (qrInput) qrInput.value = '';
        } else {
            showNotification(data.message || "Logout failed", "error");
        }
    } catch (error) {
        console.error("Logout error:", error);
        showNotification("Logout failed. Please try again.", "error");
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
        
        if (response.ok) {
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
                userDetails.innerHTML = `
                    <p class="text-sm font-semibold text-gray-900">${name}</p>
                    <p class="text-xs text-gray-600">Department: ${user.department || 'N/A'}</p>
                    <p class="text-xs text-gray-600">Role: ${user.role || 'N/A'}</p>
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

// Handle activity form submission
document.getElementById('activity-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const userType = document.getElementById('modal-user-type').value;
    const identifier = document.getElementById('modal-identifier').value;
    const activity = document.getElementById('activity').value;
    
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
        
        if (response.ok && (data.type === 'login' || data.message)) {
            const modal = document.getElementById('activity-modal');
            if (modal) modal.classList.add('hidden');
            const message = `${userType === 'student' ? 'Student' : 'Teacher'} logged in successfully!`;
            showNotification(message, "success");
            await refreshAttendanceTable();
            // Reset the input field for next scan
            const qrInput = document.getElementById('qr-input');
            if (qrInput) qrInput.value = '';
        } else {
            showNotification(data.message || "Login failed", "error");
        }
    } catch (error) {
        console.error("Login error:", error);
        showNotification("Login failed. Please try again.", "error");
    } finally {
        hideLoadingOverlay();
    }
});

// Cancel modal
document.getElementById('modal-cancel').addEventListener('click', function() {
    document.getElementById('activity-modal').classList.add('hidden');
});

// Refresh attendance tables
async function refreshAttendanceTable() {
    const startTime = performance.now();
    console.group(`[${new Date().toLocaleTimeString()}] Refreshing attendance tables...`);
    
    try {
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
        console.groupEnd();
    }
}

// Format date time string
function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'N/A';
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
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
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
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
                const timeInRaw = record.time_in || record.login || record.created_at || record.date;
                const timeOutRaw = record.time_out || record.logout || record.updated_at;
                
                // Format times with validation
                const timeIn = formatDateTime(timeInRaw);
                const timeOut = timeOutRaw ? 
                    formatDateTime(timeOutRaw) : 
                    '<span class="text-yellow-600">Still logged in</span>';
                
                // Determine status with fallback logic
                let status = record.status;
                if (!status) {
                    status = (timeOutRaw || record.time_out || record.logout) ? 'out' : 'in';
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
                
                // Build the row HTML
                // Build the row HTML
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
    
    console.log(`[ATTENDANCE] Rendered ${attendanceData.length} teacher records`);
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

// Hide loading overlay
function hideLoadingOverlay() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) 
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
    const timeInRaw = record.time_in || record.login || record.created_at || record.date;
    const timeOutRaw = record.time_out || record.logout || record.updated_at;
    
    // Format times with validation
    const timeIn = formatDateTime(timeInRaw);
    const timeOut = timeOutRaw ? 
        formatDateTime(timeOutRaw) : 
        '<span class="text-yellow-600">Still logged in</span>';
    
    // Determine status with fallback logic
    let status = record.status;
    if (!status) {
        status = (timeOutRaw || record.time_out || record.logout) ? 'out' : 'in';
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
    let row = document.getElementById(rowId);
    
    // Format the data with proper fallbacks
    const teacherInfo = record.teacher || record;
    const teacherName = teacherInfo.name || teacherInfo.teacher_name || 'N/A';
    const teacherType = teacherInfo.type || teacherInfo.teacher_type || 'Staff';
    const teacherDepartment = teacherInfo.department || teacherInfo.teacher_department || '';
    
    // Extract timestamps with fallbacks
    const timeInRaw = record.time_in || record.login || record.created_at || record.date;
    const timeOutRaw = record.time_out || record.logout || record.updated_at;
    
    // Format times with validation
    const timeIn = formatDateTime(timeInRaw);
    const timeOut = timeOutRaw ? 
        formatDateTime(timeOutRaw) : 
        '<span class="text-yellow-600">Still logged in</span>';
    
    // Determine status with fallback logic
    let status = record.status;
    if (!status) {
        status = (timeOutRaw || record.time_out || record.logout) ? 'out' : 'in';
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
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" title="Teacher ID: ${teacherId}">
            ${teacherId || 'N/A'}
        </td>
        <td class="px-6 py-4">
            <div class="text-sm font-medium text-gray-900">${escapeHtml(teacherName)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(teacherType)}</div>
            ${teacherDepartment ? `<div class="text-xs text-gray-400">${escapeHtml(teacherDepartment)}</div>` : ''}
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
    if (noRecordsRow && noRecordsRow.textContent.includes('No teacher attendance records found')) {
        noRecordsRow.closest('tr').remove();
    }
}

// Show notification
function showNotification(message, type = "info") {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 animate-fadeInUp ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Handle scan errors (suppress frequent errors)
function onScanError(error) {
    // Suppress console spam from scanning errors
    // Only log if it's not a routine "No QR code found" error
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

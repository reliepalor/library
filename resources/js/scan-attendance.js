 // Main JavaScript for admin scan/webcam attendance
    // (Copied and adapted from user attendance, all AJAX endpoints use /admin/attendance/...)
    document.addEventListener('DOMContentLoaded', () => {
        // DOM Elements
        const qrInput = document.getElementById('qr-input');
        const qrReader = document.getElementById('qr-reader');
        const webcamContainer = document.getElementById('webcam-container');
        const physicalContainer = document.getElementById('physical-container');
        const webcamModeBtn = document.getElementById('webcam-mode-btn');
        const physicalModeBtn = document.getElementById('physical-mode-btn');
        const modeDescription = document.getElementById('mode-description');
        const activityModal = document.getElementById('activity-modal');
        const borrowModal = document.getElementById('borrow-modal');
        const otherModal = document.getElementById('other-modal');
        const modalStudentId = document.getElementById('modal-student-id');
        const borrowStudentId = document.getElementById('borrow-student-id');
        const otherStudentId = document.getElementById('other-student-id');
        const studentInfoDiv = document.getElementById('student-info');
        const activitySelect = document.getElementById('activity');
        const statusDisplay = document.getElementById('status-display');
        const statusText = document.getElementById('status-text');
        const logoutModal = document.getElementById('logout-modal');
        const logoutClose = document.getElementById('logout-close');
        // Borrow modal elements (new)
        const availableBooksSearch = document.getElementById('available-books-search');
        const availableBooksList = document.getElementById('available-books-list');
        const refreshAvailableBooksBtn = document.getElementById('refresh-available-books');
        const availableBooksCollege = document.getElementById('available-books-college');

        // Verify all required elements exist
        if (!qrInput || !qrReader || !webcamContainer || !physicalContainer || 
            !webcamModeBtn || !physicalModeBtn || !modeDescription || !activityModal || 
            !borrowModal || !otherModal || !modalStudentId || !borrowStudentId || 
            !otherStudentId || !studentInfoDiv || !activitySelect || !statusDisplay || 
            !statusText || !logoutModal || !logoutClose) {
            console.error('Required elements not found');
            return;
        }

        let isProcessing = false;
        let html5QrCode = null;
        let currentMode = 'webcam'; // 'webcam' or 'physical'
        let scannerRunning = false;
        let justLoggedOut = false;
        let logoutInProgress = false;

        // Add transition classes for smooth fade
        webcamContainer.classList.add('transition-opacity', 'duration-500', 'ease-in-out');
        physicalContainer.classList.add('transition-opacity', 'duration-500', 'ease-in-out');

        // --- Borrow modal helpers ---
        let borrowUiInitialized = false;
        let sectionsInitialized = false;

        const debounce = (fn, delay = 300) => {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        };

        const renderAvailableBooks = (books = []) => {
            if (!availableBooksList) return;
            availableBooksList.innerHTML = '';
            if (!books.length) {
                availableBooksList.innerHTML = '<div class="p-6 text-gray-500 text-sm col-span-2">No available books found.</div>';
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
                    availableBooksList.innerHTML = '<div class="p-4 text-sm text-gray-500 col-span-2">Loading available books...</div>';
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
                    availableBooksList.innerHTML = '<div class="p-4 text-sm text-red-600 col-span-2">Failed to load available books.</div>';
                }
            }
        };

        // Utility functions
        const showStatus = (message, type = 'info') => {
            statusText.textContent = message;
            statusDisplay.className = `mb-4 p-3 rounded-lg ${type === 'error' ? 'bg-red-50 border border-red-200' : type === 'success' ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200'}`;
            statusDisplay.classList.remove('hidden');
            setTimeout(() => {
                statusDisplay.classList.add('hidden');
            }, 4000);
        };

        // Loading overlay helpers with smooth transitions and delayed show
        const loadingOverlay = document.getElementById('loading-overlay');
        const loadingOverlayText = document.getElementById('loading-overlay-text');
        let loadingTimer = null;
        let loadingShownAt = 0;
        const MIN_VISIBLE_MS = 350; // avoid flicker
        const SHOW_DELAY_MS = 300;   // only show if it takes a bit

        const reallyShowOverlay = (message) => {
            if (!loadingOverlay) return;
            if (loadingOverlayText) loadingOverlayText.textContent = message || 'Processing...';
            // Make visible then fade in
            loadingOverlay.classList.remove('hidden');
            // force next frame to apply transition
            requestAnimationFrame(() => {
                loadingOverlay.classList.remove('opacity-0');
                loadingOverlay.classList.add('opacity-100');
            });
            loadingShownAt = Date.now();
        };

        const showLoading = (message = 'Processing...') => {
            // reset any previous pending show
            if (loadingTimer) clearTimeout(loadingTimer);
            loadingTimer = setTimeout(() => reallyShowOverlay(message), SHOW_DELAY_MS);
        };

        const hideLoading = () => {
            if (loadingTimer) {
                clearTimeout(loadingTimer);
                loadingTimer = null;
            }
            if (!loadingOverlay || loadingOverlay.classList.contains('hidden')) return;
            const elapsed = Date.now() - loadingShownAt;
            const wait = Math.max(0, MIN_VISIBLE_MS - elapsed);
            setTimeout(() => {
                loadingOverlay.classList.remove('opacity-100');
                loadingOverlay.classList.add('opacity-0');
                const onEnd = () => {
                    loadingOverlay.classList.add('hidden');
                    loadingOverlay.removeEventListener('transitionend', onEnd);
                };
                loadingOverlay.addEventListener('transitionend', onEnd);
            }, wait);
        };

        const getCSRFToken = () => {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
        };

        // Mode switching functions
        const switchToWebcamMode = () => {
            currentMode = 'webcam';
            physicalContainer.classList.add('opacity-0');
            setTimeout(() => {
                physicalContainer.classList.add('hidden');
                webcamContainer.classList.remove('hidden');
                setTimeout(() => webcamContainer.classList.remove('opacity-0'), 10);
            }, 300);
            webcamContainer.classList.remove('opacity-0');
            webcamModeBtn.className = 'px-4 py-2 bg-blue-600 text-white rounded-md font-medium transition-colors';
            physicalModeBtn.className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-medium transition-colors hover:bg-gray-400';
            modeDescription.textContent = 'Using webcam scanner - point camera at QR code';
            if (!scannerRunning) {
                initWebcamScanner();
            }
        };

        const switchToPhysicalMode = () => {
            currentMode = 'physical';
            webcamContainer.classList.add('opacity-0');
            setTimeout(() => {
                webcamContainer.classList.add('hidden');
                physicalContainer.classList.remove('hidden');
                setTimeout(() => {
                    physicalContainer.classList.remove('opacity-0');
                    qrInput.value = '';
                    qrInput.focus();
                }, 10);
            }, 300);
            physicalContainer.classList.remove('opacity-0');
            webcamModeBtn.className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded-md font-medium transition-colors hover:bg-gray-400';
            physicalModeBtn.className = 'px-4 py-2 bg-blue-600 text-white rounded-md font-medium transition-colors';
            modeDescription.textContent = 'Using physical scanner - scan QR code in the input field';
            stopWebcamScanner();
        };

        // Initialize webcam scanner
        const initWebcamScanner = () => {
            if (scannerRunning || currentMode !== 'webcam') return;
            try {
                html5QrCode = new Html5Qrcode('qr-reader');
                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        handleQrScan(decodedText);
                    },
                    (error) => {}
                ).then(() => {
                    scannerRunning = true;
                }).catch(err => {
                    qrReader.innerHTML = '<p class="text-orange-600 text-center">Camera access failed. Please allow camera access or use physical scanner.</p>';
                    scannerRunning = false;
                });
            } catch (err) {
                qrReader.innerHTML = '<p class="text-red-600 text-center">Camera initialization failed. Use physical scanner instead.</p>';
                scannerRunning = false;
            }
        };

        // Stop webcam scanner
        const stopWebcamScanner = () => {
            if (html5QrCode && scannerRunning) {
                try {
                    html5QrCode.stop().then(() => {
                        scannerRunning = false;
                    }).catch(err => {
                        scannerRunning = false;
                    });
                } catch (err) {
                    scannerRunning = false;
                }
            }
        };

        // QR Input event listeners (Physical Scanner Mode)
        let inputTimeout;
        let qrParts = [];
        qrInput.addEventListener('input', (e) => {
            const value = e.target.value.trim();
            console.log('Scanner input value:', value); // Debug log
            // If the value contains three pipes, it's a full QR code
            if ((value.match(/\|/g) || []).length === 3) {
                handleQrScan(value);
                qrInput.value = '';
                qrParts = [];
                return;
            }
            if (inputTimeout) clearTimeout(inputTimeout);
            if (value.includes('|')) {
                qrParts.push(value.replace('|', '').trim());
                if (qrParts.length === 4) {
                    const completeQR = qrParts.join('|');
                    handleQrScan(completeQR);
                    qrInput.value = '';
                    qrParts = [];
                    return;
                }
                qrInput.value = '';
                return;
            }
            if (value) {
                inputTimeout = setTimeout(() => {
                    if (value === qrInput.value.trim()) {
                        qrParts.push(value.trim());
                        if (qrParts.length === 4) {
                            const completeQR = qrParts.join('|');
                            handleQrScan(completeQR);
                            qrInput.value = '';
                            qrParts = [];
                        }
                    }
                }, 50);
            }
        });
        qrInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = qrInput.value.trim();
                if (value) {
                    qrParts.push(value.trim());
                    if (qrParts.length === 4) {
                        const completeQR = qrParts.join('|');
                        handleQrScan(completeQR);
                        qrInput.value = '';
                        qrParts = [];
                    }
                }
            }
        });
        qrInput.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (pastedText) {
                handleQrScan(pastedText.trim());
                qrInput.value = '';
                qrParts = [];
            }
        });
        qrInput.addEventListener('focus', () => {
            if (currentMode === 'physical') {
                qrInput.value = '';
                qrParts = [];
            }
        });
        qrInput.addEventListener('blur', () => {
            const value = qrInput.value.trim();
            if (value && currentMode === 'physical' && !isProcessing) {
                qrParts.push(value.trim());
                if (qrParts.length === 4) {
                    const completeQR = qrParts.join('|');
                    handleQrScan(completeQR);
                    qrInput.value = '';
                    qrParts = [];
                }
            }
        });

        // Main QR scan handler
        const handleQrScan = async (qrData) => {
            if (isProcessing || logoutInProgress) return;
            isProcessing = true;
            showStatus('Processing QR code...');
    showLoading('Checking attendance...');
            try {
                // Fix: Only allow printable ASCII and pipe
                const cleanData = qrData.replace(/[^-|]/g, '').trim();
                const parts = cleanData.split('|').map(part => part.trim());
                if (parts.length !== 4) throw new Error('Invalid QR code format. Expected student ID, name, college, and year.');
                const [studentId, name, college, yearRaw] = parts;
                if (!studentId || !name || !college || !yearRaw) throw new Error('QR code missing required fields.');
                let year = yearRaw;
                const yearMatch = yearRaw.match(/\d+/);
                if (yearMatch) year = yearMatch[0];
                const csrfToken = getCSRFToken();
                if (!csrfToken) throw new Error('CSRF token not found. Please refresh the page.');
                // Fetch student data
                const response = await fetch(`/admin/attendance/scan?student_id=${encodeURIComponent(studentId)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!response.ok) throw new Error(`Server error: ${response.status}`);
                const data = await response.json();
                if (data.error) throw new Error(data.error);
                if (!data.students) throw new Error('Student not found in database');
            // Check for active session
            const checkResponse = await fetch(`/admin/attendance/check?student_id=${encodeURIComponent(studentId)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (!checkResponse.ok) throw new Error(`Server error: ${checkResponse.status}`);
            const checkData = await checkResponse.json();

            if (checkData.hasActiveSession) {
                // If student is logged in, automatically log them out without showing activity modal or confirmation
                const logoutResponse = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        activity: checkData.activity || 'Logout'
                    }),
                });
                if (!logoutResponse.ok) throw new Error('Failed to log out student');
                const logoutData = await logoutResponse.json();
                showStatus(logoutData.message || 'Logout successful!', 'success');
        // Update attendance table dynamically
        // Find existing row for student and update it, or add new row if not found
        const tableBody = document.querySelector('table tbody');
        if (tableBody) {
            const existingRow = tableBody.querySelector(`tr[data-student-id="${studentId}"]`);
            if (existingRow) {
                existingRow.querySelector('td:nth-child(4)').textContent = checkData.activity || 'Logout';
                existingRow.querySelector('td:nth-child(6)').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
                const statusSpan = existingRow.querySelector('td:nth-child(7) span');
                if (statusSpan) {
                    statusSpan.textContent = 'Logged Out';
                    statusSpan.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
                }
            } else {
                addAttendanceRow({
                    student_id: studentId,
                    student_name: studentInfoDiv.querySelector('p.font-medium')?.textContent || '',
                    college: studentInfoDiv.querySelector('p.text-sm.text-gray-600:nth-child(3)')?.textContent.replace('College: ', '') || '',
                    activity: checkData.activity || 'Logout',
                    time_in: '', // No change for login time
                    time_out: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }),
                    status: 'Logged Out'
                });
            }
        }
        // Show logout success modal
        logoutModal.classList.remove('hidden');
        setTimeout(() => {
            logoutModal.classList.add('hidden');
        }, 1800);
        hideLoading();
        isProcessing = false;
        return;
            }

                // If not logged in, show the activity modal for login
                modalStudentId.value = studentId;
                const nameParts = name.split(' ');
                const fname = nameParts[0] || '';
                const lname = nameParts.slice(1).join(' ') || '';
                const profilePicUrl = data.profile_picture ? window.assetBaseUrl + 'storage/' + data.profile_picture : window.assetBaseUrl + 'images/default-profile.png';
                const studentDetailsHtml = `
                    <div class="flex flex-col">
                        <p class="text-lg font-semibold text-gray-900">${lname}, ${fname}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ring-1 ring-gray-200">ID: ${studentId}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white college-${college}">${college}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-200">Year: ${year}</span>
                        </div>
                    </div>
                `;
                const profilePicImg = document.getElementById('student-profile-pic');
                if (profilePicImg) {
                    profilePicImg.src = profilePicUrl;
                    // Ensure stable layout and nice ring on success
                    profilePicImg.classList.add('ring-2','ring-blue-200');
                }
                let studentDetailsDiv = document.getElementById('student-details');
                if (!studentDetailsDiv) {
                    studentDetailsDiv = document.createElement('div');
                    studentDetailsDiv.id = 'student-details';
                    studentDetailsDiv.className = 'space-y-1';
                    // If there's a wrapper, append; otherwise append to studentInfoDiv safely
                    studentInfoDiv.appendChild(studentDetailsDiv);
                }
                studentDetailsDiv.innerHTML = studentDetailsHtml;
                // Elevate the card appearance on successful fetch
                studentInfoDiv.classList.remove('bg-gray-50');
                studentInfoDiv.classList.add('bg-white','shadow-sm','ring-1','ring-gray-100','animate-fadeInUp');
                activitySelect.value = 'Study';
                activityModal.classList.remove('hidden');
                showStatus('Student found! Please select activity for login.', 'success');
            } catch (error) {
                showStatus(`Error: ${error.message}`, 'error');
                activityModal.classList.add('hidden');
                borrowModal.classList.add('hidden');
                otherModal.classList.add('hidden');
                logoutModal.classList.add('hidden');
            } finally {
                hideLoading();
                isProcessing = false;
                setTimeout(() => {
                    if (currentMode === 'physical') {
                        qrInput.focus();
                    }
                }, 500);
            }
        };

        // Mode toggle event listeners
        webcamModeBtn.addEventListener('click', switchToWebcamMode);
        physicalModeBtn.addEventListener('click', switchToPhysicalMode);

        // Activity selection handler
        activitySelect.addEventListener('change', function () {
            const selectedActivity = this.value;
            if (selectedActivity === 'Borrow' || selectedActivity === 'Stay&Borrow') {
                activityModal.classList.add('hidden');
                borrowStudentId.value = modalStudentId.value;
                borrowModal.classList.remove('hidden');
                // Initialize borrow UI once
                if (!borrowUiInitialized) {
                    borrowUiInitialized = true;
                    if (availableBooksSearch) {
                        const debouncedSearch = debounce(() => {
                            loadAvailableBooks(availableBooksSearch.value.trim());
                        }, 300);
                        availableBooksSearch.addEventListener('input', debouncedSearch);
                    }
                    if (refreshAvailableBooksBtn) {
                        refreshAvailableBooksBtn.addEventListener('click', () => {
                            const s = availableBooksSearch?.value.trim() || '';
                            const c = availableBooksCollege?.value || '';
                            loadAvailableBooks(s, c);
                        });
                    }
                    if (availableBooksCollege) {
                        availableBooksCollege.addEventListener('change', () => {
                            const s = availableBooksSearch?.value.trim() || '';
                            const c = availableBooksCollege.value || '';
                            loadAvailableBooks(s, c);
                        });
                    }
                    // Delegate borrow button clicks
                    if (availableBooksList) {
                        availableBooksList.addEventListener('click', (e) => {
                            const btn = e.target.closest('.borrow-book-btn');
                            if (!btn) return;
                            const code = btn.getAttribute('data-code');
                            const input = document.getElementById('book_id');
                            if (input) {
                                input.value = code;
                                input.focus();
                                // Auto-submit for speed
                                document.getElementById('borrow-form')?.dispatchEvent(new Event('submit', { cancelable: true }));
                            }
                        });
                    }
                }
                // Load list fresh each time modal opens
                if (availableBooksSearch) availableBooksSearch.value = '';
                if (availableBooksCollege) availableBooksCollege.value = '';
                loadAvailableBooks('', '');
                setTimeout(() => document.getElementById('available-books-search')?.focus(), 80);
            } else if (selectedActivity === 'Other') {
                activityModal.classList.add('hidden');
                otherStudentId.value = modalStudentId.value;
                otherModal.classList.remove('hidden');
                setTimeout(() => document.getElementById('custom_activity').focus(), 100);
            }
        });

        // Activity form submission
        document.getElementById('activity-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const studentId = modalStudentId.value;
            const activity = activitySelect.value;
            try {
                showStatus('Logging attendance...');
                showLoading('Logging attendance...');
                const response = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        activity: activity 
                    }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to log attendance');
                showStatus(data.message || 'Attendance logged successfully!', 'success');
                activityModal.classList.add('hidden');
                // Dynamically add the new attendance row to the table
                addAttendanceRow({
                    student_id: studentId,
                    student_name: document.querySelector('#student-details p.text-lg')?.textContent || '',
                    college: (document.querySelector('#student-details span[class*="college-"]')?.textContent) || '',
                    activity: activity,
                    time_in: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }),
                    time_out: 'N/A',
                    status: 'Present'
                });
                // Reset for next scan
                if (currentMode === 'physical') {
                    qrInput.value = '';
                    qrInput.focus();
                }
            } catch (error) {
                showStatus(`Error: ${error.message}`, 'error');
            } finally {
                hideLoading();
            }
        });

        // Borrow form submission
        document.getElementById('borrow-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const studentId = borrowStudentId.value;
            const bookId = document.getElementById('book_id').value;
            const activityType = activitySelect.value === 'Stay&Borrow' ? 'Stay&Borrow' : 'Borrow';
            try {
                // Close the modal immediately; spinner shows only if slow due to delayed show
                borrowModal.classList.add('hidden');
                showStatus('Processing borrow request...');
                showLoading('Requesting book...');
                // First check if book can be borrowed (admin route)
                const borrowResponse = await fetch('/admin/borrow/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        book_id: bookId 
                    }),
                });
                if (!borrowResponse.ok) {
                    const errorData = await borrowResponse.json().catch(() => ({}));
                    showStatus(errorData.message || 'Failed to request book', 'error');
                    borrowModal.classList.add('hidden');
                    document.getElementById('book_id').value = '';
                    return;
                }
                // Only log attendance if book borrow request was successful
                showLoading('Logging attendance...');
                const attendanceResponse = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        activity: `${activityType}:${bookId}`
                    }),
                });
                if (!attendanceResponse.ok) {
                    const errorData = await attendanceResponse.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to log attendance');
                }
                const data = await borrowResponse.json();
                showStatus(data.message || 'Book borrow request successful!', 'success');
                borrowModal.classList.add('hidden');
                document.getElementById('book_id').value = '';
                // Add a row immediately without reload
                addAttendanceRow({
                    student_id: studentId,
                    student_name: document.querySelector('#student-details p.text-lg')?.textContent || '',
                    college: (document.querySelector('#student-details span[class*="college-"]')?.textContent) || '',
                    activity: `${activityType}:${bookId}`,
                    time_in: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }),
                    time_out: 'N/A',
                    status: 'Present'
                });
            } catch (error) {
                showStatus(`Error processing borrow request: ${error.message}`, 'error');
            } finally {
                hideLoading();
            }
        });

        // Other activities form submission
        document.getElementById('other-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const studentId = otherStudentId.value;
            const customActivity = document.getElementById('custom_activity').value;
            const activity = `Other: ${customActivity}`;
            try {
                showStatus('Logging custom activity...');
                showLoading('Logging activity...');
                const response = await fetch('/admin/attendance/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ 
                        student_id: studentId, 
                        activity: activity 
                    }),
                });
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to log attendance');
                }
                const data = await response.json();
                showStatus(data.message || 'Activity logged successfully!', 'success');
                otherModal.classList.add('hidden');
                document.getElementById('custom_activity').value = '';
                // Add a row immediately without reload
                addAttendanceRow({
                    student_id: studentId,
                    student_name: document.querySelector('#student-details p.text-lg')?.textContent || '',
                    college: (document.querySelector('#student-details span[class*="college-"]')?.textContent) || '',
                    activity: activity,
                    time_in: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }),
                    time_out: 'N/A',
                    status: 'Present'
                });
            } catch (error) {
                showStatus(`Error logging custom activity: ${error.message}`, 'error');
            } finally {
                hideLoading();
            }
        });

        // Preset activities quick select
        const presetActivities = document.getElementById('preset-activities');
        const otherForm = document.getElementById('other-form');
        if (presetActivities && otherForm) {
            presetActivities.addEventListener('click', (e) => {
                const chip = e.target.closest('.preset-chip');
                if (!chip) return;
                const value = chip.textContent.trim();
                const input = document.getElementById('custom_activity');
                if (input) input.value = value;
                // Auto-submit for a fast flow
                otherForm.dispatchEvent(new Event('submit', { cancelable: true }));
            });
        }

        // Modal cancel buttons
        document.getElementById('modal-cancel').addEventListener('click', () => {
            activityModal.classList.add('hidden');
            if (currentMode === 'physical') {
                qrInput.focus();
            }
        });
        document.getElementById('borrow-cancel').addEventListener('click', () => {
            borrowModal.classList.add('hidden');
            activityModal.classList.remove('hidden');
        });
        document.getElementById('other-cancel').addEventListener('click', () => {
            otherModal.classList.add('hidden');
            activityModal.classList.remove('hidden');
        });
        // Auto-focus QR input when in physical mode
        const focusInput = () => {
            if (currentMode === 'physical') {
                qrInput.focus();
            }
        };
        // Handle clicks outside modals to refocus input
        document.body.addEventListener('click', (event) => {
            const modals = [activityModal, borrowModal, otherModal];
            const clickedInsideModal = modals.some(modal =>
                !modal.classList.contains('hidden') && modal.contains(event.target)
            );
            if (!clickedInsideModal && 
                !event.target.closest('#webcam-container') &&
                !event.target.closest('#physical-container') &&
                !event.target.closest('.bg-gray-100')) {
                focusInput();
            }
        });
        // Initialize with physical scanner mode (QR input)
        switchToPhysicalMode();
        // Logout modal close button
        logoutClose.addEventListener('click', () => {
            logoutModal.classList.add('hidden');
            justLoggedOut = false;
            logoutInProgress = false;
            location.reload();
        });

        // Logout confirmation modal buttons
        // Removed logout confirmation modal event listeners as per user request
    });

    // Add this helper function to append a row to the attendance table
    function addAttendanceRow(row) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.student_id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center space-x-3">
        <img src="${row.profile_picture ? window.assetBaseUrl + 'storage/' + row.profile_picture : window.assetBaseUrl + 'images/default-profile.png'}"
             alt="Profile Picture"
             class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-blue-100 transition-transform duration-300 hover:scale-105" />
                <span class="font-medium">${row.student_name}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full college-${row.college}">${row.college}</span></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.activity}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.time_in}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${row.time_out}</td>
            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${row.status}</span></td>
        `;
        tableBody.prepend(tr);
    }
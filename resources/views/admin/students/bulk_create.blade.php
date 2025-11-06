<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Register Students - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

   <script>
        window.assetBaseUrl = "{{ asset('') }}";
    </script>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-100">
    <div id="main-content" class="transition-all duration-500 ml-64 main-content">

    <x-admin-nav-bar />
    <x-grid-background/>

    <div class="flex justify-center">
        <div class="mt-10 w-full max-w-4xl p-8 bg-white border border-gray-200 rounded-xl shadow-lg transition-all duration-300">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6 text-center relative">
                Bulk Register Students
            </h1>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 relative" id="bulk-errors-notification">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800">
                                There were {{ $errors->count() }} errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="ml-3 flex-shrink-0 text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 rounded" onclick="document.getElementById('bulk-errors-notification').style.display='none'">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 relative" id="bulk-session-error-notification">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="ml-3 flex-shrink-0 text-red-500 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 rounded" onclick="document.getElementById('bulk-session-error-notification').style.display='none'">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">File Format Instructions</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="mb-2"><strong>Supported formats:</strong> Excel (.xlsx, .xls, .csv) and PDF files</p>
                                <p class="mb-2"><strong>Excel/CSV columns order:</strong></p>
                                <ol class="list-decimal list-inside mb-2">
                                    <li>Student ID</li>
                                    <li>Last Name</li>
                                    <li>First Name</li>
                                    <li>Middle Initial</li>
                                    <li>Gender</li>
                                    <li>College</li>
                                    <li>Year</li>
                                    <li>Email</li>
                                </ol>
                                <p class="mb-2"><strong>PDF format:</strong> Tab or space-separated values in the same order as Excel columns</p>
                                <p><strong>Note:</strong> First row should be headers in Excel files. PDF files should have one student per line.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form id="bulkForm" action="{{ route('admin.students.bulk-store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Upload Student Data File</label>
                    <div id="uploadArea" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg id="uploadIcon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div id="fileInfo" class="hidden">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <p id="fileName" class="text-sm font-medium text-gray-900"></p>
                                        <p id="fileSize" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                                <button type="button" id="removeFile" class="mt-2 text-xs text-red-600 hover:text-red-800">
                                    Remove file
                                </button>
                            </div>
                            <div id="uploadPrompt" class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv,.pdf" class="sr-only" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">Excel, CSV, or PDF up to 10MB</p>
                        </div>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-center space-x-4">
                    <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                    <button id="submitBtn" type="submit"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Upload & Register Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Progress Modal -->
    <div id="progressModal" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300" id="progressBackdrop"></div>
        <div class="relative w-full h-full flex items-center justify-center p-4">
            <div id="progressCard" class="w-full max-w-xl bg-white rounded-2xl shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Processing Bulk Registration</h3>
                            <p id="statusSubtitle" class="text-sm text-gray-500">Uploading file...</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div id="progressBar" class="h-2 bg-blue-600 rounded-full w-0 transition-all duration-500"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                            <span id="statusText">Starting...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                    </div>
                    <ul class="space-y-3" id="statusList">
                        <li class="flex items-center gap-3 text-sm"><span class="step-dot h-2.5 w-2.5 rounded-full bg-blue-500 animate-pulse"></span><span>Upload file</span></li>
                        <li class="flex items-center gap-3 text-sm opacity-60"><span class="step-dot h-2.5 w-2.5 rounded-full bg-gray-300"></span><span>Parse data</span></li>
                        <li class="flex items-center gap-3 text-sm opacity-60"><span class="step-dot h-2.5 w-2.5 rounded-full bg-gray-300"></span><span>Create student records</span></li>
                        <li class="flex items-center gap-3 text-sm opacity-60"><span class="step-dot h-2.5 w-2.5 rounded-full bg-gray-300"></span><span>Generate QR codes</span></li>
                        <li class="flex items-center gap-3 text-sm opacity-60"><span class="step-dot h-2.5 w-2.5 rounded-full bg-gray-300"></span><span>Send emails</span></li>
                        <li class="flex items-center gap-3 text-sm opacity-60"><span class="step-dot h-2.5 w-2.5 rounded-full bg-gray-300"></span><span>Finish</span></li>
                    </ul>
                    <div id="resultBox" class="hidden mt-6 rounded-lg border border-gray-200 p-4">
                        <div class="text-sm text-gray-700"><span id="resultMessage"></span></div>
                        <ul id="resultErrors" class="mt-3 text-xs text-red-600 list-disc pl-5 space-y-1"></ul>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <div class="flex justify-end gap-3">
                        <a id="viewListBtn" href="{{ route('admin.students.index') }}" class="hidden inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700 transition">View Students</a>
                        <button id="closeModalBtn" type="button" class="hidden inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md shadow hover:bg-gray-200 transition">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Auto-hide notifications after 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const errorNotification = document.getElementById('bulk-errors-notification');
            const sessionErrorNotification = document.getElementById('bulk-session-error-notification');

            if (errorNotification) {
                setTimeout(function() {
                    errorNotification.style.display = 'none';
                }, 10000); // 10 seconds
            }

            if (sessionErrorNotification) {
                setTimeout(function() {
                    sessionErrorNotification.style.display = 'none';
                }, 10000); // 10 seconds
            }
        });
    </script>
    <!-- Minimal full-screen loading spinner (replaced by modal, left in DOM if needed) -->
    <div id="pageSpinner" class="fixed inset-0 z-50 hidden"></div>

    <script>
        // Progress modal + AJAX submit
        (function(){
            const form = document.getElementById('bulkForm');
            const submitBtn = document.getElementById('submitBtn');
            const modal = document.getElementById('progressModal');
            const backdrop = document.getElementById('progressBackdrop');
            const card = document.getElementById('progressCard');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const statusText = document.getElementById('statusText');
            const statusSubtitle = document.getElementById('statusSubtitle');
            const statusList = document.getElementById('statusList');
            const resultBox = document.getElementById('resultBox');
            const resultMessage = document.getElementById('resultMessage');
            const resultErrors = document.getElementById('resultErrors');
            const viewListBtn = document.getElementById('viewListBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');

            function openModal(){
                modal.classList.remove('hidden');
                requestAnimationFrame(()=>{
                    backdrop.classList.remove('opacity-0');
                    card.classList.remove('opacity-0','scale-95');
                });
            }
            function closeModal(){
                backdrop.classList.add('opacity-0');
                card.classList.add('opacity-0','scale-95');
                setTimeout(()=>modal.classList.add('hidden'), 250);
            }
            function setProgress(p, text){
                progressBar.style.width = p + '%';
                progressPercent.textContent = Math.round(p) + '%';
                if (text) { statusText.textContent = text; }
            }
            function setStepActive(idx){
                const items = Array.from(statusList.children);
                items.forEach((li,i)=>{
                    const dot = li.querySelector('.step-dot');
                    if (i < idx){
                        li.classList.remove('opacity-60');
                        li.classList.add('text-gray-700');
                        dot.classList.remove('bg-gray-300','animate-pulse');
                        dot.classList.add('bg-green-500');
                    } else if (i === idx){
                        li.classList.remove('opacity-60');
                        li.classList.add('text-gray-800');
                        dot.classList.remove('bg-gray-300');
                        dot.classList.add('bg-blue-500','animate-pulse');
                    } else {
                        li.classList.add('opacity-60');
                        li.classList.remove('text-gray-800');
                        const d2 = li.querySelector('.step-dot');
                        d2.classList.remove('bg-blue-500','animate-pulse','bg-green-500');
                        d2.classList.add('bg-gray-300');
                    }
                });
            }

            form.addEventListener('submit', function(e){
                e.preventDefault();
                resultBox.classList.add('hidden');
                resultMessage.textContent = '';
                resultErrors.innerHTML = '';
                viewListBtn.classList.add('hidden');
                closeModalBtn.classList.add('hidden');

                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-60','cursor-not-allowed');

                openModal();
                setProgress(5,'Initializing...');
                statusSubtitle.textContent = 'Uploading file...';
                setStepActive(0);

                const formData = new FormData(form);
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                let fake = 5;
                const stages = [20, 40, 60, 80, 90];
                const stageTexts = ['Parsing data...','Creating records...','Generating QR codes...','Sending emails...','Finalizing...'];
                let stageIdx = 0;
                const timer = setInterval(()=>{
                    if (stageIdx < stages.length && fake < stages[stageIdx]){
                        fake += 2;
                        setProgress(fake);
                    } else if (stageIdx < stages.length) {
                        setStepActive(stageIdx+1);
                        statusSubtitle.textContent = stageTexts[stageIdx];
                        stageIdx++;
                    }
                }, 400);

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async (res) => {
                    let data;
                    try { data = await res.json(); } catch (_) { data = { success: false, message: 'Unexpected response' }; }
                    clearInterval(timer);
                    if (!res.ok || !data.success){
                        setProgress(100,'Failed');
                        statusSubtitle.textContent = 'An error occurred';
                        setStepActive(5);
                        resultBox.classList.remove('hidden');
                        resultMessage.textContent = data.message || 'Bulk processing failed.';
                        closeModalBtn.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-60','cursor-not-allowed');
                        return;
                    }
                    setProgress(100,'Completed');
                    statusSubtitle.textContent = 'Finished';
                    setStepActive(5);
                    resultBox.classList.remove('hidden');
                    resultMessage.textContent = data.message || 'Completed.';
                    (data.errors || []).slice(0,5).forEach(err => {
                        const li = document.createElement('li'); li.textContent = err; resultErrors.appendChild(li);
                    });
                    viewListBtn.classList.remove('hidden');
                    closeModalBtn.classList.remove('hidden');

                    // Show success toast and auto-close modal
                    showToast('Bulk registration completed', data.message || 'All students were processed.');
                    setTimeout(()=>{ try { closeModal(); } catch(e){} }, 700);
                })
                .catch((err)=>{
                    clearInterval(timer);
                    setProgress(100,'Failed');
                    statusSubtitle.textContent = 'An error occurred';
                    setStepActive(5);
                    resultBox.classList.remove('hidden');
                    resultMessage.textContent = 'Bulk processing failed: ' + (err?.message || 'Unknown error');
                    closeModalBtn.classList.remove('hidden');
                })
                .finally(()=>{
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-60','cursor-not-allowed');
                });
            });

            closeModalBtn.addEventListener('click', closeModal);

            // Toast helpers (robust: creates DOM if missing)
            let toast = document.getElementById('toast');
            let toastCard = document.getElementById('toastCard');
            let toastTitle = document.getElementById('toastTitle');
            let toastMessage = document.getElementById('toastMessage');
            let toastClose = document.getElementById('toastClose');
            let toastTimer = null;
            function ensureToastDom(){
                if (document.getElementById('toast')) return;
                const wrapper = document.createElement('div');
                wrapper.id = 'toast';
                wrapper.className = 'fixed top-6 right-6 z-[70]';
                wrapper.innerHTML = `
                  <div id="toastCard" class="flex items-start gap-3 bg-white rounded-lg shadow-lg border border-gray-200 p-4 w-80 transform transition-all duration-300 opacity-0 translate-y-2">
                    <div class="h-8 w-8 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                      <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1">
                      <p id="toastTitle" class="text-sm font-semibold text-gray-900"></p>
                      <p id="toastMessage" class="text-xs text-gray-600 mt-0.5"></p>
                    </div>
                    <button id="toastClose" class="text-gray-400 hover:text-gray-600">
                      <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414 5.707 15.707a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                  </div>`;
                document.body.appendChild(wrapper);
                toast = document.getElementById('toast');
                toastCard = document.getElementById('toastCard');
                toastTitle = document.getElementById('toastTitle');
                toastMessage = document.getElementById('toastMessage');
                toastClose = document.getElementById('toastClose');
                toastClose.addEventListener('click', hideToast);
            }
            function showToast(title, message){
                ensureToastDom();
                toast.classList.remove('hidden');
                toastTitle.textContent = title || '';
                toastMessage.textContent = message || '';
                requestAnimationFrame(()=>{
                    toastCard.classList.remove('opacity-0','translate-y-2');
                });
                if (toastTimer) clearTimeout(toastTimer);
                toastTimer = setTimeout(hideToast, 4000);
            }
            function hideToast(){
                if (!toast || !toastCard) return;
                toastCard.classList.add('opacity-0','translate-y-2');
                setTimeout(()=> toast.classList.add('hidden'), 250);
            }
            if (toastClose) { toastClose.addEventListener('click', hideToast); }
        })();

        // File input handling
        const fileInput = document.getElementById('file');
        const uploadArea = document.getElementById('uploadArea');
        const uploadIcon = document.getElementById('uploadIcon');
        const fileInfo = document.getElementById('fileInfo');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFile = document.getElementById('removeFile');

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showFileInfo(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            uploadIcon.classList.add('hidden');
            uploadPrompt.classList.add('hidden');
            fileInfo.classList.remove('hidden');
        }

        function hideFileInfo() {
            uploadIcon.classList.remove('hidden');
            uploadPrompt.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            fileInput.value = '';
        }

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showFileInfo(file);
            } else {
                hideFileInfo();
            }
        });

        removeFile.addEventListener('click', function() {
            hideFileInfo();
        });

        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.classList.add('border-blue-400', 'bg-blue-50');
        }

        function unhighlight(e) {
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                // Trigger change event
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        }
    </script>

</body>
</html>

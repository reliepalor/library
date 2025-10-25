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
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
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

            <form action="{{ route('admin.students.bulk-store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                    <button type="submit"
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

    <!-- Minimal full-screen loading spinner -->
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
    <div id="pageSpinner" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative w-full h-full flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <div class="h-12 w-12 border-4 border-white/70 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-white text-sm">Processing file, please wait...</p>
            </div>
        </div>
    </div>

    <script>
        // Show spinner on form submit
        (function(){
            const form = document.querySelector('form[action="{{ route('admin.students.bulk-store') }}"]');
            const spinner = document.getElementById('pageSpinner');
            if (form && spinner) {
                form.addEventListener('submit', function(){
                    spinner.classList.remove('hidden');
                });
            }
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Library System</title>
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
    <div id="main-content" class="transition-all duration-500 ml-64 main-content">

        <x-admin-nav-bar />
        <x-grid-background/>
    
        <div class="flex justify-center mr-14">
            <div class="mt-10 w-full max-w-2xl p-8 bg-white border border-gray-200 rounded-xl shadow-lg transition-all duration-300">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6 text-center relative">
                    Register Teacher / Visitor
                </h1>

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
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
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.teachers_visitors.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @csrf

                    {{-- Last Name --}}
                    <div>
                        <label for="lname" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="lname" name="lname" value="{{ old('lname') }}" required
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('lname') border-red-500 @enderror">
                        @error('lname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- First Name --}}
                    <div>
                        <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="fname" name="fname" value="{{ old('fname') }}" required
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('fname') border-red-500 @enderror">
                        @error('fname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Middle Initial --}}
                    <div>
                        <label for="MI" class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                        <input type="text" id="MI" name="MI" value="{{ old('MI') }}"
                            class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('lname') border-red-500 @enderror">
                        @error('MI')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
        

                    {{-- Email --}}
                    <div >
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('fname') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Department --}}
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select id="department" name="department" required
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('department') border-red-500 @enderror">
                            <option value="" disabled {{ old('department') ? '' : 'selected' }}>Choose Department</option>
                            <option value="CICS" {{ old('department') == 'CICS' ? 'selected' : '' }}>CICS</option>
                            <option value="CTED" {{ old('department') == 'CTED' ? 'selected' : '' }}>CTED</option>
                            <option value="CCJE" {{ old('department') == 'CCJE' ? 'selected' : '' }}>CCJE</option>
                            <option value="CHM" {{ old('department') == 'CHM' ? 'selected' : '' }}>CHM</option>
                            <option value="CBEA" {{ old('department') == 'CBEA' ? 'selected' : '' }}>CBEA</option>
                            <option value="CA" {{ old('department') == 'CA' ? 'selected' : '' }}>CA</option>
                            <option value="Guest" {{ old('department') == 'Guest' ? 'selected' : '' }}>Guest</option>
                        </select>
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="gender" name="gender"
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('gender') border-red-500 @enderror">
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Choose Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role" name="role" required
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('role') border-red-500 @enderror">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Choose Role</option>
                            <option value="Teacher" {{ old('role') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="Visitor" {{ old('role') == 'Visitor' ? 'selected' : '' }}>Visitor</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="md:col-span-2 flex justify-center mt-6">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Add Teacher/Visitor
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Minimal full-screen loading spinner -->
        <div id="pageSpinner" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="relative w-full h-full flex items-center justify-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-12 w-12 border-4 border-white/70 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-white text-sm">Processing, please wait...</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Show spinner on native form submit without altering submit logic
        (function(){
            const form = document.querySelector('form[action="{{ route('admin.teachers_visitors.store') }}"]');
            const spinner = document.getElementById('pageSpinner');
            if (form && spinner) {
                form.addEventListener('submit', function(){
                    spinner.classList.remove('hidden');
                });
            }
        })();
    </script>
    <script>
        // Handle form submission with AJAX
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading spinner
            const loadingModal = document.getElementById('loadingModal');
            loadingModal.classList.remove('hidden');
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70','cursor-not-allowed');
            
            // Get form data
            const form = document.querySelector('form');
            const formData = new FormData(form);
            
            // Submit via AJAX
            fetch('{{ route("admin.teachers_visitors.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                // Attempt to parse JSON only if the response is JSON
                const contentType = response.headers.get('content-type') || '';
                let data = null;
                if (contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    // Non-JSON likely means a redirect (login) or an error page
                    const text = await response.text();
                    if (response.status === 419) {
                        throw new Error('Your session has expired (419). Please refresh the page and try again.');
                    }
                    if (response.status === 401) {
                        throw new Error('You are not authorized (401). Please log in again.');
                    }
                    // Provide a shortened message, log full HTML to console for debugging
                    console.error('Non-JSON response:', { status: response.status, text });
                    throw new Error('Unexpected server response. Please try again.');
                }

                // If HTTP not ok but server returned JSON, surface message
                if (!response.ok) {
                    const msg = (data && (data.message || data.error)) || 'Request failed.';
                    throw new Error(msg);
                }

                // Hide loading spinner
                loadingModal.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70','cursor-not-allowed');
                
                if (data && data.success) {
                    // Show success message in modal
                    showMessageModal('Success', data.message || 'Student added successfully!', true);
                    form.reset();
                    
                    // Clear any previous error messages
                    clearErrorMessages();
                } else {
                    // Handle validation errors
                    if (data && data.errors) {
                        // Display validation errors in the form
                        displayValidationErrors(data);
                    } else {
                        // Show error message in modal
                        showMessageModal('Error', data.message || 'An error occurred while adding the student.', false);
                    }
                }
            })
            .catch(error => {
                // Hide loading spinner
                loadingModal.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70','cursor-not-allowed');
                
                console.error('Error:', error);
                showMessageModal('Error', error?.message || 'An error occurred while adding the student. Please try again.', false);
            });
        });
        
        // Show message modal
        function showMessageModal(title, message, isSuccess) {
            const messageModal = document.getElementById('messageModal');
            const messageTitle = document.getElementById('messageTitle');
            const messageContent = document.getElementById('messageContent');
            const messageIcon = document.getElementById('messageIcon');
            const closeMessageModal = document.getElementById('closeMessageModal');
            
            messageTitle.textContent = title;
            messageContent.textContent = message;
            
            // Set icon based on success or error
            if (isSuccess) {
                messageIcon.innerHTML = `
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                `;
                messageIcon.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
            } else {
                messageIcon.innerHTML = `
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                `;
                messageIcon.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100";
            }
            
            messageModal.classList.remove('hidden');
            
            // Close modal when close button is clicked
            closeMessageModal.addEventListener('click', function() {
                messageModal.classList.add('hidden');
            });
            
            // Close modal when clicking outside
            messageModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    </script>
</body>

</html>

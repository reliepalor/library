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
                <x-admin-nav-bar />

    <div class="flex justify-center">
            <div class="mt-10 w-full max-w-2xl p-8 bg-white border border-gray-200 rounded-xl shadow-lg transition-all duration-300">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6 text-center relative">
                Register Student
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

            <form action="{{ route('admin.students.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf

                {{-- Student ID --}}
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                    <input type="text" id="student_id" name="student_id" value="{{ old('student_id') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('student_id') border-red-500 @enderror">
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                    <input type="text" id="MI" name="MI" value="{{ old('MI') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('MI') border-red-500 @enderror">
                    @error('MI')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- College --}}
                <div>
                    <label for="college" class="block text-sm font-medium text-gray-700 mb-1">College</label>
                    <select id="college" name="college" required
                        class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('college') border-red-500 @enderror">
                        <option value="" disabled {{ old('college') ? '' : 'selected' }}>Choose College</option>
                        <option value="CICS" {{ old('college') == 'CICS' ? 'selected' : '' }}>CICS</option>
                        <option value="CTED" {{ old('college') == 'CTED' ? 'selected' : '' }}>CTED</option>
                        <option value="CCJE" {{ old('college') == 'CCJE' ? 'selected' : '' }}>CCJE</option>
                        <option value="CHM" {{ old('college') == 'CHM' ? 'selected' : '' }}>CHM</option>
                        <option value="CBEA" {{ old('college') == 'CBEA' ? 'selected' : '' }}>CBEA</option>
                        <option value="CA" {{ old('college') == 'CA' ? 'selected' : '' }}>CA</option>
                    </select>
                    @error('college')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Year Level --}}
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                    <input type="number" id="year" name="year" value="{{ old('year') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('year') border-red-500 @enderror">
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="md:col-span-2 flex justify-center mt-6">
                    <button type="button" id="submitBtn"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Loading Spinner Modal -->
    <div id="loadingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
    
            <!-- Spinner container -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="text-lg font-medium text-gray-900">Processing your request...</p>
                                <p class="text-sm text-gray-500 mt-2">Please wait while we add the student</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Message Modal -->
    <div id="messageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
    
            <!-- Modal container -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <div class="flex justify-center">
                                <div id="messageIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full">
                                    <!-- Icon will be inserted by JavaScript -->
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="messageTitle"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" id="messageContent"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="closeMessageModal" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Handle form submission with AJAX
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading spinner
            const loadingModal = document.getElementById('loadingModal');
            loadingModal.classList.remove('hidden');
            
            // Get form data
            const form = document.querySelector('form');
            const formData = new FormData(form);
            
            // Submit via AJAX
            fetch('{{ route("admin.students.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                loadingModal.classList.add('hidden');
                
                if (data.success) {
                    // Show success message in modal
                    showMessageModal('Success', data.message || 'Student added successfully!', true);
                    form.reset();
                } else {
                    // Show error message in modal
                    showMessageModal('Error', data.message || 'An error occurred while adding the student.', false);
                }
            })
            .catch(error => {
                // Hide loading spinner
                loadingModal.classList.add('hidden');
                
                console.error('Error:', error);
                showMessageModal('Error', 'An error occurred while adding the student. Please try again.', false);
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

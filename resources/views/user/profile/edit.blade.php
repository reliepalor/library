<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile - User</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @vite('resources/css/app.css')
    <!-- Figtree Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="https://via.placeholder.com/32?text=CSU">
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ open: false, qrSrc: '' }">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profile-form');
            const fileInput = document.getElementById('profile_picture');
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
                    console.log('File selected:', fileName);
                    
                    // Update the file name display
                    const fileNameDisplay = document.getElementById('file-name-display');
                    if (fileNameDisplay) {
                        fileNameDisplay.textContent = 'Selected file: ' + fileName;
                    }
                    
                    // Submit the form automatically when a file is selected
                    if (e.target.files.length > 0 && form) {
                        console.log('Submitting form automatically');
                        form.submit();
                    }
                });
            }
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form is being submitted');
                    console.log('Form data:', new FormData(form));
                    
                    // Check if a file is selected
                    if (fileInput && fileInput.files.length > 0) {
                        console.log('File selected:', fileInput.files[0].name);
                    } else {
                        console.log('No file selected');
                    }
                });
            }
        });
    </script>
     <x-header />
    
    <!-- QR Code Modal -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
         @click="open = false">
        
        <div class="bg-white rounded-xl p-6 relative max-w-md w-full shadow-2xl" 
             @click.stop
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4">
            
            <button @click="open = false" 
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <div class="p-5 flex justify-center bg-white rounded-lg">
                <img :src="qrSrc" alt="Student QR Code" class="max-w-full h-auto" />
            </div>
        </div>
    </div>
    
                    
   <div class="flex justify-center">
     <div class="bg-white rounded-2xl shadow-xl p-8 space-y-10 transition duration-300 ease-in-out mt-20 w-[90%]">
        
        @if (session('status') === 'profile-updated')
            <div x-data="{ show: true }" x-show="show" class="p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 flex items-center justify-between animate-fade-in">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Profile updated successfully.</span>
                </div>
                <button @click="show = false" class="text-green-500 hover:text-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Profile Header -->
        <div class="flex flex-col items-center mb-8">
            <div class="relative group">
                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) . '?v=' . time() : asset('images/default-profile.png') }}"
                     alt="Profile Picture"
                     class="w-32 h-32 rounded-full object-cover ring-4 ring-blue-500 shadow-lg transition duration-300">

                <label for="profile_picture"
                       class="absolute bottom-2 right-2 p-2 bg-blue-600 text-white rounded-full cursor-pointer hover:bg-blue-700 shadow-lg transition transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h6m6 0H5a2 2 0 00-2 2v6a2 2 0 002 2h6m6 0h-6m6 0a2 2 0 002-2V9a2 2 0 00-2-2m-6 12V7" />
                    </svg>
                    <input id="profile_picture" name="profile_picture" type="file" class="hidden" accept="image/*" 
                           onchange="this.form.submit()" />
                </label>
                
                <!-- Debug: Show selected file name -->
                @if (config('app.debug'))
                    <div class="text-xs text-gray-500 mt-2 text-center" id="file-name-display"></div>
                    <script>
                        document.getElementById('profile_picture').addEventListener('change', function(e) {
                            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
                            document.getElementById('file-name-display').textContent = 'Selected file: ' + fileName;
                        });
                    </script>
                @endif
            </div>
            
            <div class="mt-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                
                <!-- Debug: Show current profile picture path -->
                @if (config('app.debug'))
                    <div class="text-xs text-gray-500 mt-2">
                        <p>Profile picture path: {{ $user->profile_picture ?? 'None' }}</p>
                        <p>Full image URL: {{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.png') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Profile Form -->
        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ 
            saved: {{ session('status') === 'profile-updated' ? 'true' : 'false' }},
            isSubmitting: false
        }" x-on:submit="isSubmitting = true" id="profile-form">
            @csrf
            @method('PATCH')
            
            <!-- Hidden fields for name and email to pass validation -->
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            
            <!-- Debug: Show form submission status -->
            @if (config('app.debug'))
                <div class="text-xs text-gray-500 mt-2">
                    <p>Form method: PATCH</p>
                    <p>Form action: {{ route('user.profile.update') }}</p>
                    <p>CSRF token: {{ csrf_token() }}</p>
                    <p>Current profile picture path: {{ $user->profile_picture ?? 'None' }}</p>
                </div>
            @endif

            @error('profile_picture')
                <p class="text-red-500 text-sm text-center">{{ $message }}</p>
            @enderror

            <!-- Debug information -->
            @if (config('app.debug'))
                <div class="text-xs text-gray-500 mt-2">
                    <p>Form method: PATCH</p>
                    <p>Form action: {{ route('user.profile.update') }}</p>
                    <p>CSRF token: {{ csrf_token() }}</p>
                    <p>Current profile picture path: {{ $user->profile_picture ?? 'None' }}</p>
                </div>
            @endif

            <!-- Save Button -->
            <div class="flex justify-center pt-4">
                <button type="submit"
                        x-show="!saved"
                        :disabled="isSubmitting"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg shadow-md transition duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }">
                    <span x-show="!isSubmitting">Save Profile Picture</span>
                    <span x-show="isSubmitting">Saving...</span>
                </button>
                <button type="button"
                        x-show="saved"
                        class="bg-green-600 text-white font-medium px-6 py-3 rounded-lg shadow-md cursor-default">
                    Profile Saved!
                </button>
            </div>
            
            <!-- Form submission error message -->
            @if ($errors->any())
                <div class="mt-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">
                    <strong>There were errors saving your profile:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>

        <!-- Student Info Section -->
        @if ($user->student)
            <div class="bg-gray-50 rounded-xl shadow p-6 sm:p-8 space-y-6">
                <h3 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">Student Profile Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <label class="text-gray-600 font-medium">Student ID</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->student->student_id }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <label class="text-gray-600 font-medium">Full Name</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->student->full_name }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <label class="text-gray-600 font-medium">College</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->student->college }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <label class="text-gray-600 font-medium">Year Level</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->student->year }}</p>
                    </div>
               
                    <div class="sm:col-span-2 bg-white p-4 rounded-lg shadow-sm">
                        <label class="text-gray-600 font-medium mb-2 block">QR Code</label>
                        @if ($user->student->qr_code_path)
                            <img src="{{ asset('storage/' . $user->student->qr_code_path) }}"
                                 alt="Student QR Code"
                                 class="w-40 h-40 object-contain border border-gray-200 rounded-lg shadow-md hover:scale-105 transition duration-300 cursor-pointer mx-auto"
                                 @click="qrSrc = '{{ asset('storage/' . $user->student->qr_code_path) }}'; open = true" />
                        @else
                            <p class="text-gray-500">No QR code available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance History Section -->
            <div class="bg-gray-50 rounded-xl shadow p-6 sm:p-8 space-y-6">
                <h3 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">Attendance History</h3>
                @if ($user->student->attendanceHistories->isEmpty())
                    <p class="text-gray-500 text-center py-4">No attendance history available.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Time In - Time Out</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Activity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($user->student->attendanceHistories as $history)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-3 px-4">{{ $history->date->format('Y-m-d') }}</td>
                                        <td class="py-3 px-4">
                                            {{ $history->time_in ? $history->time_in->format('h:i A') : '-' }} - {{ $history->time_out ? $history->time_out->format('h:i A') : '-' }}
                                        </td>
                                        <td class="py-3 px-4">{{ $history->activity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
   </div>
</main>

</body>
</html>

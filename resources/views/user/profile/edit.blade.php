<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Smart Library</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    @vite('resources/css/app.css')
    @vite(['resources/css/app.js'])
    <!-- Figtree Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        /* Smooth image fade-in for preview */
        #profile-preview {
            transition: opacity 0.5s ease, transform 0.3s ease;
        }
        #profile-preview.fade {
            opacity: 0.5;
            transform: scale(0.95);
        }

        /* Modal container transition tweaks */
        .modal-enter, .modal-leave-to {
            opacity: 0;
            transform: scale(0.9);
        }
        .modal-enter-to, .modal-leave {
            opacity: 1;
            transform: scale(1);
            transition: opacity 300ms ease, transform 300ms ease;
        }

        /* Upload button smooth transform */
        #upload-btn:hover:not(:disabled) {
            transform: scale(1.05);
        }
        #upload-btn {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        /* Upload button disabled state */
        #upload-btn:disabled {
            cursor: not-allowed;
            opacity: 0.5;
            transform: none !important;
        }

        /* Responsive table improvements for mobile */
        @media (max-width: 640px) {
            .mobile-table th,
            .mobile-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <x-header />

    <div class="flex flex-col items-center justify-center min-h-screen py-8 sm:py-12 px-4">
        <div class="flex flex-col lg:flex-row justify-center gap-4 lg:gap-10 items-start w-full max-w-7xl mx-auto mt-6 lg:mt-10">
            <!-- Profile Picture Section -->
            <div class="bg-blue-50 rounded-2xl shadow-sm p-4 sm:p-6 lg:p-8 w-full max-w-md order-1" x-data="{ showQrModal: false }">

                @if (session('status') === 'profile-updated')
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 transition-opacity duration-500" 
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base">Profile picture updated successfully!</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 transition-opacity duration-500" 
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition.opacity>
                        <strong class="text-sm sm:text-base">Upload Error:</strong>
                        <ul class="list-disc list-inside mt-1 sm:mt-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Profile Picture Section -->
                <div class="text-center mt-4 sm:mt-8">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Change Profile Picture</h1>

                    <div class="relative group mb-4 sm:mb-6">
                        <!-- Current profile picture -->
                        <img id="profile-preview" 
                             src="{{ \App\Services\AvatarService::getProfilePictureUrl($user->profile_picture, $user->name, 300) }}"
                             alt="Current Profile Picture"
                             class="w-32 sm:w-40 h-32 sm:h-40 rounded-full object-cover mx-auto ring-4 ring-blue-500 shadow-lg transition-transform duration-300 ease-in-out group-hover:scale-105" />

                        <!-- Upload button overlay -->
                        <label for="profile_picture" 
                               class="absolute bottom-1 sm:bottom-2 right-1 sm:right-2 p-1.5 sm:p-2 bg-blue-600 text-white rounded-full cursor-pointer hover:bg-blue-700 shadow-lg transition transform hover:scale-110">
                            <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 9a2 2 0 012-2h6m6 0H5a2 2 0 00-2 2v6a2 2 0 002 2h6m6 0h-6m6 0a2 2 0 002-2V9a2 2 0 00-2-2m-6 12V7" />
                            </svg>
                        </label>
                    </div>

                    <!-- Profile Picture Form -->
                    <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" 
                          class="space-y-4 sm:space-y-6" id="profile-picture-form">
                        @csrf
                        @method('PATCH')

                        <!-- Hidden file input -->
                        <input id="profile_picture" name="profile_picture" type="file" class="hidden" 
                               accept="image/jpeg,image/png,image/jpg" />

                        <!-- File name display -->
                        <div id="file-info" class="text-xs sm:text-sm text-gray-600 text-center hidden">
                            <span id="file-name-display"></span> - <span id="file-size-display"></span>
                        </div>

                        <!-- Upload Button -->
                        <button type="submit" 
                                id="upload-btn"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 sm:py-3 px-4 rounded-lg shadow-md transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <span id="upload-text">Upload Profile Picture</span>
                            <span id="upload-loading" class="hidden">Uploading...</span>
                        </button>

                        <!-- Cancel Button -->
                        <a href="{{ route('user.profile.edit') }}" 
                           class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2.5 sm:py-3 px-4 rounded-lg transition duration-300">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>

            <!-- Student Info Section -->
            @if ($user->student)
                <div class="bg-gray-50 rounded-xl shadow p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6 w-full max-w-4xl order-2 lg:order-none mt-4 lg:mt-0">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">Student Profile Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 text-sm">
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <label class="text-gray-600 font-medium block mb-1 sm:mb-0">Student ID</label>
                            <p class="text-gray-900 font-semibold">{{ $user->student->student_id }}</p>
                        </div>
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <label class="text-gray-600 font-medium block mb-1 sm:mb-0">Full Name</label>
                            <p class="text-gray-900 font-semibold">{{ $user->student->full_name }}</p>
                        </div>
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <label class="text-gray-600 font-medium block mb-1 sm:mb-0">College</label>
                            <p class="text-gray-900 font-semibold">{{ $user->student->college }}</p>
                        </div>
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <label class="text-gray-600 font-medium block mb-1 sm:mb-0">Year Level</label>
                            <p class="text-gray-900 font-semibold">{{ $user->student->year }}</p>
                        </div>
                    </div>
                       
                    <!-- QR Code Section (if user has student profile) -->
                    @if($user->student && $user->student->qr_code_path)
                        <div class="border-t pt-4 sm:pt-6 mt-4 sm:mt-6 flex justify-center flex-col items-center">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">Your QR Code</h3>
                            <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 text-center">Click to view full size</p>

                            <div x-data="{ showQrModal: false }" class="relative w-full max-w-xs mx-auto">

                                <!-- Existing QR code image -->
                                <img src="{{ asset('storage/' . $user->student->qr_code_path) }}" 
                                     alt="Student QR Code"
                                     class="w-full max-w-48 sm:max-w-64 h-auto max-h-48 sm:max-h-64 object-contain border border-gray-200 rounded-lg shadow-md cursor-pointer hover:scale-105 transform transition-transform duration-300 mx-auto"
                                     @click="showQrModal = true" />

                                <!-- QR Code Modal -->
                                <div x-show="showQrModal"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-2 sm:p-4"
                                    @click="showQrModal = false"
                                    x-cloak>

                                    <div class="bg-white rounded-2xl p-4 sm:p-6 relative max-w-sm sm:max-w-lg w-full shadow-2xl transform transition-all mx-auto"
                                        @click.stop
                                        x-show="showQrModal"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-90"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-90">

                                        <button @click="showQrModal = false" 
                                                class="absolute top-2 sm:top-4 right-2 sm:right-4 text-gray-500 hover:text-gray-700 focus:outline-none transition-colors duration-200 z-10">
                                            <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <div class="text-center">
                                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Your Student QR Code</h3>
                                            <div class="flex justify-center">
                                                <img src="{{ asset('storage/' . $user->student->qr_code_path) }}"
                                                     alt="Student QR Code"
                                                     class="w-full max-w-48 sm:max-w-64 h-auto max-h-48 sm:max-h-64 object-contain border border-gray-200 rounded-lg shadow-md mx-auto" />
                                            </div>
                                            <p class="text-xs sm:text-sm text-gray-600 mt-3 sm:mt-4">Use this QR code for attendance and library services</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Attendance History Section -->
        @if ($user->student)
            <div class="bg-gray-50 rounded-xl shadow p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6 mt-6 sm:mt-8 w-full max-w-4xl mx-auto">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">Attendance History</h3>
                @if ($user->student->attendanceHistories->isEmpty())
                    <p class="text-gray-500 text-center py-4 text-sm sm:text-base">No attendance history available.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="mobile-table min-w-full bg-white rounded-lg shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-medium text-gray-700">Date</th>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-medium text-gray-700">Time In - Time Out</th>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-medium text-gray-700">Activity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($user->student->attendanceHistories as $history)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 text-sm">{{ $history->date->format('Y-m-d') }}</td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 text-sm">
                                            {{ $history->time_in ? $history->time_in->format('h:i A') : '-' }} - {{ $history->time_out ? $history->time_out->format('h:i A') : '-' }}
                                        </td>
                                        <td class="py-2 sm:py-3 px-2 sm:px-4 text-sm">{{ $history->activity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_picture');
        const uploadBtn = document.getElementById('upload-btn');
        const uploadText = document.getElementById('upload-text');
        const uploadLoading = document.getElementById('upload-loading');
        const fileInfo = document.getElementById('file-info');
        const fileNameDisplay = document.getElementById('file-name-display');
        const fileSizeDisplay = document.getElementById('file-size-display');
        const previewImage = document.getElementById('profile-preview');
        const form = document.getElementById('profile-picture-form');

        // Helper: fade animation on image change
        function fadePreviewImage(newSrc) {
            previewImage.classList.add('fade');
            setTimeout(() => {
                previewImage.src = newSrc;
                previewImage.classList.remove('fade');
            }, 250);
        }

        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, or JPG).');
                    fileInput.value = '';
                    return;
                }

                // Validate file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB.');
                    fileInput.value = '';
                    return;
                }

                // Show file info
                fileNameDisplay.textContent = file.name;
                fileSizeDisplay.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                fileInfo.classList.remove('hidden');

                // Enable upload button
                uploadBtn.disabled = false;

                // Preview image with fade effect
                const reader = new FileReader();
                reader.onload = function(event) {
                    fadePreviewImage(event.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                fileInfo.classList.add('hidden');
                uploadBtn.disabled = true;
            }
        });

        // Handle form submission
        form.addEventListener('submit', function(e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please select an image file to upload.');
                return;
            }

            uploadText.classList.add('hidden');
            uploadLoading.classList.remove('hidden');
            uploadBtn.disabled = true;
        });
    });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Librarian Profile</title>
        <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }

            /* Ensure proper spacing for fixed sidebar */
            .main-content {
                transition: margin-left 0.5s ease-in-out;
            }

            .sidebar-collapsed {
                margin-left: 4rem;
            }

            .sidebar-expanded {
                margin-left: 15rem;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .sidebar-collapsed {
                    margin-left: 0;
                }

                .sidebar-expanded {
                    margin-left: 0;
                }
            }

            .fade-in { animation: fadeIn 0.5s ease-in; }
            .slide-up { animation: slideUp 0.5s ease-out; }
            .scale-in { animation: scaleIn 0.3s ease-out; }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slideUp {
                from { transform: translateY(20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }

            @keyframes scaleIn {
                from { transform: scale(0.95); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
        </style>

    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-100">
        <div id="main-content" class="transition-all duration-500 ml-64 main-content">
            <!-- Navigation Sidebar -->
            <x-admin-nav-bar/>
            <!-- Main Content Area -->
            <div class="min-h-screen">
                <!-- Top Navigation Bar -->
                <nav class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center h-16">
                            <div class="flex items-center">
                                <button @click="sidebarExpanded = !sidebarExpanded"
                                        class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <h1 class="ml-4 text-xl font-semibold text-gray-800">Librarian Profile</h1>
                            </div>

                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">Welcome, {{ Auth::user()?->name ?? 'Admin' }}</span>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="py-12">
                        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 bg-white border-b border-gray-200">
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium text-gray-900">Manage your personal and account details</h3>
                                    </div>

                                    <!-- Success/Error Messages -->
                                    @if (session('success'))
                                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                            <span class="block sm:inline">{{ session('success') }}</span>
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Profile Information -->
                                    <div class="mb-8">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="text-lg font-medium text-gray-900">Profile Information</h4>
                                            <div class="flex space-x-2">
                                                <button type="button" id="edit-profile-btn"
                                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 rounded-xl p-6">
                                            <form id="profile-form" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Name -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                            Full Name
                                                        </label>
                                                        <div id="name-display" class="text-lg font-semibold text-gray-900 py-2">{{ $admin ? $admin->name : 'N/A' }}</div>
                                                        <input id="name-input" name="name" type="text" value="{{ old('name', $admin ? $admin->name : '') }}"
                                                               class="hidden mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               required>
                                                        @error('name')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <!-- Email -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Email Address
                                                        </label>
                                                        <div id="email-display" class="text-lg font-semibold text-gray-900 py-2">{{ $admin ? $admin->email : 'N/A' }}</div>
                                                        <input id="email-input" name="email" type="email" value="{{ old('email', $admin ? $admin->email : '') }}"
                                                               class="hidden mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               required>
                                                        @error('email')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <!-- Date Joined -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Date Joined
                                                        </label>
                                                        <div class="text-lg font-semibold text-gray-900 py-2">{{ $admin && $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</div>
                                                    </div>

                                                    <!-- Last Login -->
                                                   
                                                </div>

                                                <!-- Edit Mode Buttons (Hidden by default) -->
                                                <div id="edit-buttons" class="hidden flex justify-end space-x-3 mt-6">
                                                    <button type="button" id="cancel-edit-btn"
                                                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Profile Picture Section -->
                                    <div class="bg-gray-50 rounded-xl p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="text-md font-medium text-gray-900">Profile Picture</h4>
                                        </div>

                                        <form id="picture-form" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="flex flex-col items-center space-y-4">
                                                <!-- Profile Picture Preview -->
                                                <div class="relative">
                                                <img id="profile-preview"
                                                         src="{{ $admin && $admin->profile_picture ? asset('storage/profile_pictures/' . basename($admin->profile_picture)) : asset('images/default-profile.png') }}"
                                                         alt="Profile Picture"
                                                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">

                                                    <!-- Upload Overlay -->
                                                    <label for="profile_picture"
                                                           class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full cursor-pointer transition-colors duration-200 shadow-lg">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    </label>
                                                </div>

                                                <!-- Upload Button -->
                                                <div class="text-center">
                                                    <label for="profile_picture" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg cursor-pointer transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                        </svg>
                                                        Upload / Change Photo
                                                    </label>
                                                    <input id="profile_picture" name="profile_picture" type="file" class="hidden" accept="image/*">
                                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG up to 2MB</p>
                                                </div>

                                                <!-- Save Profile Button -->
                                                <div class="text-center">
                                                    <button type="submit" id="save-profile-btn"
                                                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed hidden">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Save Profile Picture
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Change Password Section -->
                                    <div class="mt-8 pt-8 border-t border-gray-200">
                                        <h4 class="text-lg font-medium text-gray-900 mb-4">Password Management</h4>

                                        <!-- Password Action Buttons -->
                                        <div id="password-actions" class="flex flex-col sm:flex-row gap-3 mb-6">
                                            <button type="button" id="change-password-btn"
                                                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                Change Password
                                            </button>

                                            <button type="button" id="forgot-password-btn"
                                                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                </svg>
                                                Forgot Password
                                            </button>
                                        </div>

                                        <!-- Change Password Form (Hidden by default) -->
                                        <div id="change-password-form" class="hidden bg-gray-50 rounded-xl p-6">
                                            <div class="flex justify-between items-center mb-4">
                                                <h5 class="text-md font-medium text-gray-900">Change Your Password</h5>
                                                <button type="button" id="cancel-password-change"
                                                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('admin.profile.change-password') }}">
                                                @csrf

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Current Password -->
                                                    <div>
                                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                                        <input id="current_password" name="current_password" type="password"
                                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               required>
                                                        @error('current_password')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <!-- New Password -->
                                                    <div>
                                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                                        <input id="password" name="password" type="password"
                                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               required>
                                                        @error('password')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <!-- Confirm New Password -->
                                                    <div class="md:col-span-2">
                                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                                        <input id="password_confirmation" name="password_confirmation" type="password"
                                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               required>
                                                    </div>
                                                </div>

                                                <!-- Change Password Button -->
                                                <div class="mt-6 flex justify-end space-x-3">
                                                    <button type="button" id="cancel-password-btn"
                                                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Update Password
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Forgot Password Form (Hidden by default) -->
                                        <div id="forgot-password-form" class="hidden bg-gray-50 rounded-xl p-6">
                                            <div class="flex justify-between items-center mb-4">
                                                <h5 class="text-md font-medium text-gray-900">Reset Your Password</h5>
                                                <button type="button" id="cancel-forgot-password"
                                                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('admin.profile.forgot-password') }}">
                                                @csrf

                                                <div class="mb-4">
                                                    <label for="reset_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                                    <input id="reset_email" name="email" type="email" value="{{ Auth::user()->email }}"
                                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                           required readonly>
                                                    @error('email')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" id="cancel-forgot-btn"
                                                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Send Reset Link
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Forgot Password Success Message (Hidden by default) -->
                                        <div id="forgot-password-message" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-6">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="text-sm font-medium text-blue-900 mb-1">Password Reset Email Sent</h5>
                                                    <p class="text-sm text-blue-700">
                                                        We've sent a password reset link to your email address: <strong>{{ Auth::user()->email }}</strong>.
                                                        Please check your inbox and follow the instructions to reset your password.
                                                    </p>
                                                    <button type="button" id="dismiss-forgot-message"
                                                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                                                        Dismiss
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('profile_picture');
                const previewImage = document.getElementById('profile-preview');
                const editProfileBtn = document.getElementById('edit-profile-btn');
                const cancelEditBtn = document.getElementById('cancel-edit-btn');
                const editButtons = document.getElementById('edit-buttons');
                const nameDisplay = document.getElementById('name-display');
                const nameInput = document.getElementById('name-input');
                const emailDisplay = document.getElementById('email-display');
                const emailInput = document.getElementById('email-input');

                let isEditing = false;

                // Profile edit toggle
                editProfileBtn.addEventListener('click', function() {
                    if (!isEditing) {
                        // Switch to edit mode
                        nameDisplay.classList.add('hidden');
                        nameInput.classList.remove('hidden');
                        emailDisplay.classList.add('hidden');
                        emailInput.classList.remove('hidden');
                        editButtons.classList.remove('hidden');
                        editProfileBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Cancel';
                        isEditing = true;
                    } else {
                        // Switch to view mode
                        cancelEdit();
                    }
                });

                cancelEditBtn.addEventListener('click', function() {
                    cancelEdit();
                });

                function cancelEdit() {
                    nameInput.classList.add('hidden');
                    nameDisplay.classList.remove('hidden');
                    emailInput.classList.add('hidden');
                    emailDisplay.classList.remove('hidden');
                    editButtons.classList.add('hidden');
                    editProfileBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>Edit';
                    isEditing = false;
                }

                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const saveBtn = document.getElementById('save-profile-btn');

                    if (file) {
                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                        if (!validTypes.includes(file.type)) {
                            alert('Please select a valid image file (JPEG, PNG, or JPG).');
                            fileInput.value = '';
                            saveBtn.disabled = true;
                            return;
                        }

                        // Validate file size (2MB limit)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('File size must be less than 2MB.');
                            fileInput.value = '';
                            saveBtn.disabled = true;
                            return;
                        }

                        // Preview image only (no auto-submit)
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            previewImage.src = event.target.result;
                            // Enable save button when valid image is selected
                            saveBtn.disabled = false;
                            saveBtn.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // No file selected, disable save button
                        saveBtn.disabled = true;
                    }
                });

                // Password Management Functionality
                const changePasswordBtn = document.getElementById('change-password-btn');
                const forgotPasswordBtn = document.getElementById('forgot-password-btn');
                const changePasswordForm = document.getElementById('change-password-form');
                const forgotPasswordMessage = document.getElementById('forgot-password-message');
                const cancelPasswordChange = document.getElementById('cancel-password-change');
                const cancelPasswordBtn = document.getElementById('cancel-password-btn');
                const dismissForgotMessage = document.getElementById('dismiss-forgot-message');
                const passwordActions = document.getElementById('password-actions');

                // Show change password form
                changePasswordBtn.addEventListener('click', function() {
                    passwordActions.classList.add('hidden');
                    changePasswordForm.classList.remove('hidden');
                    forgotPasswordMessage.classList.add('hidden');
                });

                // Show forgot password form
                forgotPasswordBtn.addEventListener('click', function() {
                    passwordActions.classList.add('hidden');
                    document.getElementById('forgot-password-form').classList.remove('hidden');
                    changePasswordForm.classList.add('hidden');
                    forgotPasswordMessage.classList.add('hidden');
                });

                // Cancel change password form
                cancelPasswordChange.addEventListener('click', function() {
                    changePasswordForm.classList.add('hidden');
                    passwordActions.classList.remove('hidden');
                });

                cancelPasswordBtn.addEventListener('click', function() {
                    changePasswordForm.classList.add('hidden');
                    passwordActions.classList.remove('hidden');
                });

                // Dismiss forgot password message
                dismissForgotMessage.addEventListener('click', function() {
                    forgotPasswordMessage.classList.add('hidden');
                    passwordActions.classList.remove('hidden');
                });

                // Cancel forgot password form
                const cancelForgotPassword = document.getElementById('cancel-forgot-password');
                const cancelForgotBtn = document.getElementById('cancel-forgot-btn');

                cancelForgotPassword.addEventListener('click', function() {
                    document.getElementById('forgot-password-form').classList.add('hidden');
                    passwordActions.classList.remove('hidden');
                });

                cancelForgotBtn.addEventListener('click', function() {
                    document.getElementById('forgot-password-form').classList.add('hidden');
                    passwordActions.classList.remove('hidden');
                });
            });
        </script>
    </body>
</html>

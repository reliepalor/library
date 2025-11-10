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
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Profile Picture</h1>

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



            <!-- Profile Information Section -->
            <div class="bg-gray-50 rounded-xl shadow p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6 w-full max-w-4xl order-2 lg:order-none mt-4 lg:mt-0">
                @if($user->teacherVisitor)
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">Teacher/Visitor Profile Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 text-sm">
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->teacherVisitor->full_name }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Role</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst($user->teacherVisitor->role) }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Department</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->teacherVisitor->department ?? 'N/A' }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->email }}</dd>
                        </div>
                    </div>
                    @php $qrCodePath = $user->teacherVisitor->qr_code_path ?? null; @endphp
                @elseif($user->student)
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">Student Profile Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 text-sm">
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->student->full_name }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->student->student_id ?? 'N/A' }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">College</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->student->college ?? 'N/A' }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->student->year ?? 'N/A' }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->student->gender ?? 'N/A' }}</dd>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm transition transform hover:scale-[1.02] hover:shadow-md">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->email }}</dd>
                        </div>
                    </div>
                    @php $qrCodePath = $user->student->qr_code_path ?? null; @endphp
                @endif

                @if(isset($qrCodePath) && $qrCodePath)
                    <div class="border-t pt-4 sm:pt-6 mt-4 sm:mt-6 flex justify-center flex-col items-center">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">Your QR Code</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 text-center">Click to view full size</p>

                        <div x-data="{ showQrModal: false }" class="relative w-full max-w-xs mx-auto">
                            <!-- QR code image -->
                            <img src="{{ asset('storage/' . $qrCodePath) }}"
                                 alt="QR Code"
                                 class="w-full max-w-48 sm:max-w-64 h-auto max-h-48 sm:max-h-64 object-contain border border-gray-200 rounded-lg shadow-md cursor-pointer hover:scale-105 transform transition-transform duration-300 mx-auto"
                                 @click="showQrModal = true"
                                 @keydown.escape.window="if(showQrModal) showQrModal = false">

                            <!-- QR Code Modal -->
                            <div x-show="showQrModal"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                                @click.self="showQrModal = false"
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
                                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 focus:outline-none"
                                        aria-label="Close QR code">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                    <div class="text-center">
                                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Library QR Code</h3>
                                        <div class="flex justify-center">
                                            <img id="qrCodeImage" 
                                                 src="{{ asset('storage/' . $qrCodePath) }}"
                                                 alt="QR Code"
                                                 class="w-full max-w-48 sm:max-w-64 h-auto max-h-48 sm:max-h-64 object-contain border border-gray-200 rounded-lg shadow-md" />
                                        </div>
                                        <div class="mt-4 sm:mt-6 flex justify-center">
                                            <button onclick="downloadQRWithDetails()" 
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                                Download QR Code
                                            </button>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Use this QR code for attendance and library services</p>
                                    </div>

                                    <script>
                                        function downloadQRWithDetails() {
                                            const qrCodeImg = document.getElementById('qrCodeImage');
                                            const canvas = document.createElement('canvas');
                                            const ctx = canvas.getContext('2d');
                                            
                                            // Set canvas size with padding
                                            const padding = 40;
                                            const qrSize = 300; // Fixed size for QR code
                                            const name = '{{ $user->student->full_name ?? $user->teacherVisitor->full_name }}';
                                            const college = '{{ $user->student->college ?? ($user->teacherVisitor->department ?? "") }}';
                                            const details = '{{ $user->student ? ($user->student->year ? "Year " . $user->student->year : "") : ($user->teacherVisitor->role ?? "") }}';
                                            
                                            // Calculate text height first
                                            const tempCanvas = document.createElement('canvas');
                                            const tempCtx = tempCanvas.getContext('2d');
                                            tempCtx.font = 'bold 20px Arial';
                                            
                                            // Calculate text height for name
                                            const maxWidth = qrSize * 0.9; // 90% of QR code width
                                            const lineHeight = 24;
                                            let textHeight = 0;
                                            
                                            // Calculate name height
                                            const nameWords = name.split(' ');
                                            let currentLine = '';
                                            let nameLines = 1;
                                            
                                            for (const word of nameWords) {
                                                const testLine = currentLine + word + ' ';
                                                const metrics = tempCtx.measureText(testLine);
                                                if (metrics.width > maxWidth && currentLine !== '') {
                                                    currentLine = word + ' ';
                                                    nameLines++;
                                                } else {
                                                    currentLine = testLine;
                                                }
                                            }
                                            
                                            // Add height for college/role
                                            const hasAdditionalInfo = college || details;
                                            const additionalInfoHeight = hasAdditionalInfo ? lineHeight : 0;
                                            
                                            // Calculate total text height with some padding
                                            const totalTextHeight = (nameLines * lineHeight) + additionalInfoHeight + 20;
                                            
                                            // Set canvas size with enough space for text and QR code
                                            canvas.width = qrSize + (padding * 2);
                                            canvas.height = qrSize + totalTextHeight + (padding * 3);
                                            
                                            // Fill with white background
                                            ctx.fillStyle = '#ffffff';
                                            ctx.fillRect(0, 0, canvas.width, canvas.height);
                                            
                                            // Draw name
                                            ctx.fillStyle = '#000000';
                                            ctx.textAlign = 'center';
                                            ctx.font = 'bold 20px Arial';
                                            
                                            // Reset for actual drawing
                                            let y = padding + 30;
                                            currentLine = '';
                                            
                                            // Draw wrapped name
                                            for (const word of nameWords) {
                                                const testLine = currentLine + word + ' ';
                                                const metrics = ctx.measureText(testLine);
                                                
                                                if (metrics.width > maxWidth && currentLine !== '') {
                                                    ctx.fillText(currentLine, canvas.width / 2, y);
                                                    currentLine = word + ' ';
                                                    y += lineHeight;
                                                } else {
                                                    currentLine = testLine;
                                                }
                                            }
                                            ctx.fillText(currentLine, canvas.width / 2, y);
                                            
                                            // Draw college and year/role
                                            if (college || details) {
                                                y += lineHeight;
                                                ctx.font = '16px Arial';
                                                const infoText = [college, details].filter(Boolean).join(' - ');
                                                ctx.fillText(infoText, canvas.width / 2, y);
                                                y += lineHeight; // Add extra space before QR code
                                            } else {
                                                y += 20; // Add some space if no additional info
                                            }
                                            
                                            // Draw QR code below the text with some padding
                                            qrCodeImg.onload = function() {
                                                const qrY = y + 20; // Add extra space after text
                                                ctx.drawImage(qrCodeImg, padding, qrY, qrSize, qrSize);
                                                
                                                // Trigger download
                                                const link = document.createElement('a');
                                                link.download = `${name.replace(/[^a-z0-9]/gi, '_')}_QR_Code.png`;
                                                link.href = canvas.toDataURL('image/png');
                                                link.click();
                                            };
                                            
                                            // In case image is already loaded
                                            if (qrCodeImg.complete) {
                                                qrCodeImg.onload();
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                </div>
        </div>

        <!-- Reserved Books Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:p-8 mt-6 sm:mt-8 w-full max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Reserved Books
                </h3>
                <span class="text-sm text-gray-500 bg-gray-50 px-3 py-1 rounded-full">
                    {{ $reservations->count() }} books
                </span>
            </div>

            @if ($reservations->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">No reserved books yet</p>
                    <p class="text-gray-400 text-sm mb-4">Start exploring our collection and reserve your favorite books</p>
                    <a href="{{ route('user.books.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse Books
                    </a>
                </div>
            @else
                <div class="grid gap-4 sm:gap-6">
                    @foreach ($reservations as $reservation)
                        <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4 sm:p-6 hover:shadow-md transition-all duration-200 group">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <!-- Book Image -->
                                <div class="flex-shrink-0">
                                    @if($reservation->book && $reservation->book->image1)
                                        <img src="{{ asset('storage/' . $reservation->book->image1) }}"
                                             alt="{{ $reservation->book->name }}"
                                             class="w-16 h-20 sm:w-20 sm:h-24 object-cover rounded-lg shadow-sm group-hover:scale-105 transition-transform duration-200" />
                                    @else
                                        <div class="w-16 h-20 sm:w-20 sm:h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Book Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                        <div class="flex-1">
                                            <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 line-clamp-1">
                                                {{ $reservation->book ? $reservation->book->name : 'Book not found' }}
                                            </h4>
                                            <p class="text-sm text-gray-600 mb-1">
                                                by {{ $reservation->book ? $reservation->book->author : 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Book Code: {{ $reservation->book ? $reservation->book->book_code : $reservation->book_id }}
                                            </p>
                                        </div>

                                        <!-- Status and Date -->
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if($reservation->status === 'active') bg-green-100 text-green-800
                                                @elseif($reservation->status === 'cancelled') bg-red-100 text-red-800
                                                @elseif($reservation->status === 'expired') bg-gray-100 text-gray-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if($reservation->status === 'active')
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                @elseif($reservation->status === 'cancelled')
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                @elseif($reservation->status === 'expired')
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                    </svg>
                                                @endif
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                            <p class="text-xs text-gray-500">
                                                {{ $reservation->reserved_at->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
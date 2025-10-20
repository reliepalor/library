<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(10px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        .animate-fadeInUp { 
            animation: fadeInUp 0.3s ease-out forwards; 
            opacity: 0;
        }
        #dropdownMenu {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #dropdownMenu.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }
        #dropdownButton[aria-expanded="true"] svg {
            transform: rotate(180deg);
        }
        a:hover svg {
            color: #3B82F6;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
        .dropdown-item svg {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body>
    <div class="relative">
        <!-- Dropdown Button -->
        <button id="dropdownButton" class="flex items-center bg-gray-800 px-4 py-2 text-white rounded-lg font-semibold hover:bg-gray-700 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 ring-offset-2" aria-haspopup="true" aria-expanded="false">
            <span>Menu</span>
            <svg class="ml-2 w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="absolute right-0 mt-2 w-56 bg-white/95 backdrop-blur-md rounded-xl shadow-xl py-2 z-50 border border-gray-100">
            <a href="{{ route('admin.attendance.index') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200 animate-fadeInUp group" style="animation-delay: 0.1s;">
                <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Unified Attendance
            </a>
         
            <a href="{{ route('admin.attendance.history') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200 animate-fadeInUp group" style="animation-delay: 0.2s;">
                <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                View History
            </a>
            <a href="{{ route('admin.attendance.analytics') }}" class="dropdown-item flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200 animate-fadeInUp group" style="animation-delay: 0.3s;">
                <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Analytics
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('dropdownButton');
            const menu = document.getElementById('dropdownMenu');

            // Toggle dropdown on button click
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    menu.classList.remove('show');
                    button.setAttribute('aria-expanded', 'false');
                } else {
                    menu.classList.add('show');
                    button.setAttribute('aria-expanded', 'true');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!button.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.remove('show');
                    button.setAttribute('aria-expanded', 'false');
                }
            });

            // Optional: Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    menu.classList.remove('show');
                    button.setAttribute('aria-expanded', 'false');
                    button.focus();
                }
            });
        });
    </script>
</body>
</html>
<nav
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 bg-white/95 backdrop-blur-lg transition-all duration-500 ease-in-out shadow-2xl border-r border-blue-200/30 w-64"
    data-sidebar
    onmouseleave="hideAllTooltips()"
>
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 via-indigo-50/60 to-purple-50/40 -z-10"></div>

    <!-- Logo -->
    <div class="flex items-center justify-center h-16 border-b border-blue-200/40 bg-white/50 backdrop-blur-sm">
        <a href="{{ route('admin.auth.dashboard') }}" class="flex items-center justify-center group relative">
            <div class="relative">
                <img src="{{ asset('images/library.png') }}" alt="Library Logo" width="32" height="32"
                     class="transition-all duration-300 group-hover:scale-110 group-hover:rotate-3 drop-shadow-sm">
                <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-110 transition-transform duration-300"></div>
            </div>
            <span class="ml-3 text-gray-800 font-bold text-lg tracking-tight hidden data-text
                  transition-all duration-300 delay-100 opacity-100 translate-x-0">
                Library
            </span>
        </a>
    </div>

    <!-- Main Navigation -->
    <div class="mt-6 px-2 space-y-2">

        <!-- Dashboard -->
        <div class="relative" data-tooltip-trigger="dashboard">
            <x-nav-link :href="route('admin.auth.dashboard')"
                       :active="request()->routeIs('admin.auth.dashboard')"
                       class="flex items-center px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95 w-full">
                <div class="relative">
                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>
                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">Dashboards</span>
            </x-nav-link>

            <!-- Tooltip for minimized state -->
            <div class="hidden absolute left-16 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap shadow-lg z-50 data-tooltip='dashboard'
                 transition-all duration-200 opacity-100 translate-x-0 scale-100">
                Dashboard
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
            </div>
        </div>

        <!-- Students Dropdown -->
        <div class="relative" data-dropdown="students">

            <!-- Button -->
            <button type="button"
                data-dropdown-toggle="students"
                class="flex items-center w-full px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95"
            >
                <div class="relative">
                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>

                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 flex-1 text-left hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">
                    Students
                </span>

                <svg class="ml-auto h-4 w-4 transform transition-all duration-300 text-gray-500 group-hover:text-blue-600 chevron hidden data-text"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Expanded Dropdown -->
            <div data-dropdown-expanded="students"
                 class="ml-4 mt-2 space-y-1 border-l-2 border-blue-200/50 pl-4 hidden
                       transition-all duration-300 ease-out opacity-100 translate-y-0 scale-100">

                <x-nav-link :href="route('admin.students.index')"
                        :active="request()->routeIs('admin.students.index')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-blue-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">All Students</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.students.create')"
                        :active="request()->routeIs('admin.students.create')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-green-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Register Student</span>
                </x-nav-link>
            </div>

            <!-- Hover Dropdown for Minimized State -->
            <div data-dropdown-minimized="students"
                 class="absolute left-16 top-0 bg-white/95 backdrop-blur-lg rounded-xl shadow-2xl border border-blue-200/50 py-2 min-w-48 z-50 hidden
                 transition ease-out duration-200 opacity-100 translate-x-0 scale-100"
                 onclick="handleClickAway(event, 'students')">

                <div class="px-3 py-2 border-b border-blue-100/50">
                    <h3 class="font-semibold text-gray-800 text-sm">Students</h3>
                </div>

                <x-nav-link :href="route('admin.students.index')"
                        :active="request()->routeIs('admin.students.index')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">All Students</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.students.create')"
                        :active="request()->routeIs('admin.students.create')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Register Student</span>
                </x-nav-link>

                <div class="absolute left-0 top-6 transform -translate-x-1 w-2 h-2 bg-white rotate-45 border-l border-t border-blue-200/50"></div>
            </div>
        </div>

        <!-- Attendance Dropdown -->
        <div class="relative" data-dropdown="attendance">

            <button type="button"
                data-dropdown-toggle="attendance"
                class="flex items-center w-full px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95"
            >
                <div class="relative">
                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>

                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 flex-1 text-left hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">
                    Attendance
                </span>

                <svg class="ml-auto h-4 w-4 transform transition-all duration-300 text-gray-500 group-hover:text-blue-600 chevron hidden data-text"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Expanded Dropdown -->
            <div data-dropdown-expanded="attendance"
                 class="ml-4 mt-2 space-y-1 border-l-2 border-blue-200/50 pl-4 hidden
                       transition-all duration-300 ease-out opacity-100 translate-y-0 scale-100">

                <x-nav-link :href="route('admin.attendance.index')"
                        :active="request()->routeIs('admin.attendance.index')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-green-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">📸 Unified Attendance</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.attendance.history')"
                        :active="request()->routeIs('admin.attendance.history')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-blue-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Attendance History</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.attendance.analytics')"
                        :active="request()->routeIs('admin.attendance.analytics')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-purple-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Analytics</span>
                </x-nav-link>


            </div>

            <!-- Hover Dropdown for Minimized State -->
            <div data-dropdown-minimized="attendance"
                 class="absolute left-16 top-0 bg-white/95 backdrop-blur-lg rounded-xl shadow-2xl border border-blue-200/50 py-2 min-w-52 z-50 hidden
                 transition ease-out duration-200 opacity-100 translate-x-0 scale-100"
                 onclick="handleClickAway(event, 'attendance')">

                <div class="px-3 py-2 border-b border-blue-100/50">
                    <h3 class="font-semibold text-gray-800 text-sm">Attendance</h3>
                </div>

                <x-nav-link :href="route('admin.attendance.index')"
                        :active="request()->routeIs('admin.attendance.index')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Unified Attendance</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.attendance.history')"
                        :active="request()->routeIs('admin.attendance.history')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Attendance History</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.attendance.analytics')"
                        :active="request()->routeIs('admin.attendance.analytics')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Analytics</span>
                </x-nav-link>



                <div class="absolute left-0 top-6 transform -translate-x-1 w-2 h-2 bg-white rotate-45 border-l border-t border-blue-200/50"></div>
            </div>
        </div>

        <!-- Teachers / Visitors Dropdown -->
        <div class="relative" data-dropdown="teachers_visitors">

            <!-- Button -->
            <button type="button"
                data-dropdown-toggle="teachers_visitors"
                class="flex items-center w-full px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95"
            >
                <div class="relative">
                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>

                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 flex-1 text-left hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">
                    Teachers / Visitors
                </span>

                <svg class="ml-auto h-4 w-4 transform transition-all duration-300 text-gray-500 group-hover:text-blue-600 chevron hidden data-text"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Expanded Dropdown -->
            <div data-dropdown-expanded="teachers_visitors"
                 class="ml-4 mt-2 space-y-1 border-l-2 border-blue-200/50 pl-4 hidden
                       transition-all duration-300 ease-out opacity-100 translate-y-0 scale-100">

                <x-nav-link :href="route('admin.teachers_visitors.index')"
                        :active="request()->routeIs('admin.teachers_visitors.index')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-blue-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">All Teachers / Visitors</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.teachers_visitors.create')"
                        :active="request()->routeIs('admin.teachers_visitors.create')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-green-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Register </span>
                </x-nav-link>
            </div>

            <!-- Hover Dropdown for Minimized State -->
            <div data-dropdown-minimized="teachers_visitors"
                 class="absolute left-16 top-0 bg-white/95 backdrop-blur-lg rounded-xl shadow-2xl border border-blue-200/50 py-2 min-w-48 z-50 hidden
                 transition ease-out duration-200 opacity-100 translate-x-0 scale-100"
                 onclick="handleClickAway(event, 'teachers_visitors')">

                <div class="px-3 py-2 border-b border-blue-100/50">
                    <h3 class="font-semibold text-gray-800 text-sm">Teachers / Visitors</h3>
                </div>

                <x-nav-link :href="route('admin.teachers_visitors.index')"
                        :active="request()->routeIs('admin.teachers_visitors.index')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">All Teachers / Visitors</span>
                </x-nav-link>

                <x-nav-link :href="route('admin.teachers_visitors.create')"
                        :active="request()->routeIs('admin.teachers_visitors.create')"
                        class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Register Teacher / Visitor</span>
                </x-nav-link>

                <div class="absolute left-0 top-6 transform -translate-x-1 w-2 h-2 bg-white rotate-45 border-l border-t border-blue-200/50"></div>
            </div>
        </div>

        <!-- Books Dropdown -->
        <div class="relative" data-dropdown="books">
            <button type="button"
                data-dropdown-toggle="books"
                class="flex items-center w-full px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95"
            >
                <div class="relative">
                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>
                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 flex-1 text-left hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">Books</span>
                <svg class="ml-auto h-4 w-4 transform transition-all duration-300 text-gray-500 group-hover:text-blue-600 chevron hidden data-text"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Expanded Dropdown -->
            <div data-dropdown-expanded="books"
                 class="ml-4 mt-2 space-y-1 border-l-2 border-blue-200/50 pl-4 hidden
                       transition-all duration-300 ease-out opacity-100 translate-y-0 scale-100">
                <x-nav-link :href="route('admin.books.index')" 
                            :active="request()->routeIs('admin.books.index')" 
                            class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-blue-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">All Books</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.books.create')" 
                            :active="request()->routeIs('admin.books.create')" 
                            class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 rounded-lg transition-all duration-200 group hover:translate-x-1">
                    <svg class="h-4 w-4 text-green-500 mr-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Add Book</span>
                </x-nav-link>
            </div>

            <!-- Hover Dropdown for Minimized State -->
            <div data-dropdown-minimized="books"
                 class="absolute left-16 top-0 bg-white/95 backdrop-blur-lg rounded-xl shadow-2xl border border-blue-200/50 py-2 min-w-44 z-50 hidden
                 transition ease-out duration-200 opacity-100 translate-x-0 scale-100"
                 onclick="handleClickAway(event, 'books')">
                <div class="px-3 py-2 border-b border-blue-100/50">
                    <h3 class="font-semibold text-gray-800 text-sm">Books</h3>
                </div>
                <x-nav-link :href="route('admin.books.index')" 
                            :active="request()->routeIs('admin.books.index')" 
                            class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">All Books</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.books.create')" 
                            :active="request()->routeIs('admin.books.create')" 
                            class="flex items-center px-4 py-2.5 hover:bg-blue-50/80 transition-all duration-200 group mx-2 my-1 rounded-lg">
                    <svg class="h-4 w-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Add Book</span>
                </x-nav-link>
                <div class="absolute left-0 top-6 transform -translate-x-1 w-2 h-2 bg-white rotate-45 border-l border-t border-blue-200/50"></div>
            </div>
        </div>

        <!-- Borrow Requests -->
        <div class="relative" data-tooltip-trigger="borrow">
            <x-nav-link :href="route('admin.borrow.requests')" 
                    :active="request()->routeIs('admin.borrow.requests')" 
                    class="flex items-center px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95 w-full">
                <div class="relative">
                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>
                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">Borrow Requests</span>
            </x-nav-link>
            
            <!-- Tooltip for minimized state -->
            <div class="hidden absolute left-16 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap shadow-lg z-50 data-tooltip='borrow'
                 transition-all duration-200 opacity-100 translate-x-0 scale-100">
                Borrow Requests
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
            </div>
        </div>

                <!-- Campus News -->
        <div class="relative" data-tooltip-trigger="campus-news">
            <x-nav-link :href="route('admin.campus-news.index')"
                    :active="request()->routeIs('admin.campus-news.index')"
                    class="flex items-center px-4 py-3 group relative rounded-xl hover:bg-gradient-to-r hover:from-blue-100/60 hover:to-indigo-100/60 transition-all duration-300 hover:shadow-md hover:scale-105 active:scale-95 w-full">
                <div class="relative">
                  <svg class=" h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V7a2 2 0 012-2h10l4 4v9a2 2 0 01-2 2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h3" />
                    </svg>

                    <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
                </div>
                <span class="ml-3 text-gray-800 font-medium group-hover:text-gray-900 hidden data-text
                      transition-all duration-200 delay-75 opacity-100 translate-x-0">Campus News</span>
            </x-nav-link>

            <!-- Tooltip for minimized state -->
            <div class="hidden absolute left-16 top-1/2 transform -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap shadow-lg z-50 data-tooltip='campus-news'
                 transition-all duration-200 opacity-100 translate-x-0 scale-100">
                Campus News
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
            </div>
        </div>

    </div>

    <!-- Sidebar Toggle -->
    <button type="button" data-toggle 
            class="absolute -right-4 top-8 bg-white/90 backdrop-blur-sm rounded-full p-2.5 shadow-xl hover:shadow-2xl border border-blue-200/50 hover:bg-blue-50/80 transition-all duration-300 group hover:scale-110 active:scale-95 z-10">
        <svg class="h-5 w-5 transition-all duration-500 text-gray-600 group-hover:text-blue-600 toggle-icon" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <div class="absolute inset-0 bg-blue-400/20 rounded-full scale-0 group-hover:scale-150 transition-transform duration-300 -z-10"></div>
    </button>

    <!-- User Profile Section (Bottom) -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-blue-200/40 bg-white/60 backdrop-blur-sm">
        <div class="p-3">
            <div class="relative" data-profile @click.away="handleProfileClickAway(event)">
                <button data-profile-toggle 
                        class="flex items-center w-full text-left focus:outline-none hover:bg-blue-100/30 rounded-xl transition-all duration-300 p-2.5 group relative overflow-hidden"
                        data-tooltip-trigger="profile">
                    <div class="flex-shrink-0 relative">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 p-0.5 group-hover:scale-110 transition-all duration-300">
                            @if(Auth::user() && Auth::user()->profile_picture)
                                <img src="{{ asset('storage/profile_pictures/' . basename(Auth::user()->profile_picture)) }}"
                                     alt="Profile Picture"
                                     class="h-full w-full rounded-full object-cover">
                            @else
                                <div class="h-full w-full rounded-full bg-white/90 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white animate-pulse"></div>
                    </div>
                    
                    <div class="ml-3 flex-1 min-w-0 hidden data-text
                         transition-all duration-300 delay-100 opacity-100 translate-x-0 scale-100">
                        <div class="text-sm font-semibold text-gray-800 truncate group-hover:text-gray-900 transition-colors duration-200">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-gray-500 truncate group-hover:text-gray-600 transition-colors duration-200">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                    
                    <div class="ml-2 transition-transform duration-300 profile-chevron hidden data-text" >
                        <svg class="h-4 w-4 text-gray-500 group-hover:text-blue-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    
                    <!-- Hover ripple effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 rounded-xl transition-all duration-300 -z-10"></div>
                </button>

                <!-- Profile Dropdown Menu -->
                <div data-profile-dropdown
                     class="absolute bg-white/95 backdrop-blur-lg rounded-xl shadow-2xl border border-blue-200/50 overflow-hidden z-50 min-w-56 hidden
                     transition-all duration-300 ease-out opacity-100 translate-y-0 scale-100 bottom-16 left-0 right-0">
                    
                    <!-- Profile Header -->
                    <div class="px-4 py-3 bg-gradient-to-r from-blue-50/80 to-purple-50/80 border-b border-blue-100/50">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 p-0.5">
                                @if(Auth::user() && Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/profile_pictures/' . basename(Auth::user()->profile_picture)) }}"
                                         alt="Profile Picture"
                                         class="h-full w-full rounded-full object-cover">
                                @else
                                    <div class="h-full w-full rounded-full bg-white flex items-center justify-center">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Menu Items -->
                    <div class="py-2">
                        <a href="{{ route('admin.profile') }}"
                           class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50/80 hover:text-blue-700 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium">Profile Settings</span>
                        </a>
                        

                        <div class="border-t border-gray-100/50 my-2"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50/80 hover:text-red-700 transition-all duration-200 group">
                                <svg class="w-5 h-5 mr-3 text-red-500 group-hover:text-red-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="font-medium">Sign Out</span>
                            </button>
                        </form>
                    </div>

                    <!-- Arrow pointer -->
                    <div class="absolute transform rotate-45 w-2 h-2 bg-white border-l border-b border-blue-200/50 arrow"></div>
                </div>

                <!-- Tooltip for minimized state -->
                <div class="hidden absolute left-16 bottom-1/2 transform translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap shadow-lg z-50 data-tooltip='profile'
                     transition-all duration-200 opacity-100 translate-x-0 scale-100">
                    {{ Auth::user()->name }}
                    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Enhanced animations and effects */
    @keyframes ripple {
        0% { 
            transform: scale(0); 
            opacity: 0.6; 
        }
        50% {
            opacity: 0.3;
        }
        100% { 
            transform: scale(4); 
            opacity: 0; 
        }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-2px);
        }
    }

    .animate-ripple {
        animation: ripple 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    /* Custom scrollbar for dropdowns */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(59, 130, 246, 0.1);
        border-radius: 2px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.3);
        border-radius: 2px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.5);
    }

    /* Backdrop blur fallback */
    @supports not (backdrop-filter: blur(12px)) {
        .backdrop-blur-lg {
            background: rgba(255, 255, 255, 0.95);
        }
        .backdrop-blur-sm {
            background: rgba(255, 255, 255, 0.9);
        }
    }

    /* Active state enhancements */
    .router-link-active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(147, 51, 234, 0.1));
        border-left: 3px solid #3b82f6;
        color: #1e40af !important;
        font-weight: 600;
    }

    /* Hover effects for nav items */
    nav .group:hover .absolute.inset-0 {
        animation: ripple 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Enhanced focus states */
    nav button:focus-visible,
    nav a:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 0.75rem;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        nav {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        nav.mobile-open {
            transform: translateX(0);
        }
    }
</style>

<script>
    // Global variables
    let sidebarExpanded = localStorage.getItem('sidebarExpanded') !== 'false';
    let dropdownStates = {
        students: { isOpen: false, minimizedOpen: false },
        attendance: { isOpen: false, minimizedOpen: false },
        teachers_visitors: { isOpen: false, minimizedOpen: false },
        books: { isOpen: false, minimizedOpen: false }
    };
    let profileOpen = false;

    // Elements
    const sidebar = document.getElementById('sidebar');
    const textElements = document.querySelectorAll('.data-text');
    const toggleBtn = document.querySelector('[data-toggle]');
    const profileToggle = document.querySelector('[data-profile-toggle]');
    const profileDropdown = document.querySelector('[data-profile-dropdown]');
    const profileChevron = document.querySelector('.profile-chevron');
    const toggleIcon = document.querySelector('.toggle-icon');
    const tooltipTriggers = document.querySelectorAll('[data-tooltip-trigger]');
    const tooltips = document.querySelectorAll('[data-tooltip]');

    // Functions for text animation
    function animateTextIn() {
        textElements.forEach(el => {
            el.classList.remove('hidden');
            el.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => {
                el.classList.remove('opacity-0', 'translate-x-4');
                el.classList.add('opacity-100', 'translate-x-0');
            }, 10);
        });
    }

    function animateTextOut() {
        textElements.forEach(el => {
            el.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => {
                el.classList.add('hidden');
            }, 200);
        });
    }

    // Update sidebar state
    function updateSidebar() {
        if (sidebarExpanded) {
            sidebar.classList.remove('w-16');
            sidebar.classList.add('w-64');
            animateTextIn();
            profileToggle.classList.remove('justify-center');
            toggleIcon.classList.remove('rotate-180');
            if (profileOpen) {
                profileOpen = false;
                toggleProfile(false);
            }
            closeAllMinimized();
        } else {
            sidebar.classList.add('w-16');
            sidebar.classList.remove('w-64');
            animateTextOut();
            profileToggle.classList.add('justify-center');
            toggleIcon.classList.add('rotate-180');
            closeAllMinimized();
            hideAllTooltips();
        }

        localStorage.setItem('sidebarExpanded', sidebarExpanded);
        // Dispatch custom event for other components to listen
        window.dispatchEvent(new CustomEvent('sidebarToggled', { detail: { expanded: sidebarExpanded } }));
    }

    // Toggle button
    toggleBtn.addEventListener('click', () => {
        sidebarExpanded = !sidebarExpanded;
        updateSidebar();
    });

    // Initial load
    updateSidebar();

    // Tooltip functions
    function hideAllTooltips() {
        tooltips.forEach(tooltip => {
            tooltip.classList.add('hidden');
        });
    }

    tooltipTriggers.forEach(trigger => {
        trigger.addEventListener('mouseenter', () => {
            if (!sidebarExpanded) {
                const id = trigger.dataset.tooltipTrigger;
                hideAllTooltips();
                const tooltip = document.querySelector(`[data-tooltip="${id}"]`);
                if (tooltip) {
                    tooltip.classList.remove('hidden');
                    tooltip.classList.add('opacity-0', 'translate-x-2', 'scale-95');
                    setTimeout(() => {
                        tooltip.classList.remove('opacity-0', 'translate-x-2', 'scale-95');
                        tooltip.classList.add('opacity-100', 'translate-x-0', 'scale-100');
                    }, 10);
                }
            }
        });
    });

    // Dropdown functions
    function toggleExpanded(id, show) {
        const el = document.querySelector(`[data-dropdown-expanded="${id}"]`);
        const chevron = document.querySelector(`[data-dropdown="${id}"] .chevron`);
        if (show) {
            el.classList.remove('hidden');
            el.classList.add('opacity-0', '-translate-y-2', 'scale-95');
            setTimeout(() => {
                el.classList.remove('opacity-0', '-translate-y-2', 'scale-95');
                el.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            el.classList.add('opacity-0', '-translate-y-2', 'scale-95');
            setTimeout(() => {
                el.classList.add('hidden');
            }, 200);
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    function toggleMinimized(id, show) {
        const el = document.querySelector(`[data-dropdown-minimized="${id}"]`);
        if (show) {
            el.classList.remove('hidden');
            el.classList.add('opacity-0', 'translate-x-4', 'scale-95');
            setTimeout(() => {
                el.classList.remove('opacity-0', 'translate-x-4', 'scale-95');
                el.classList.add('opacity-100', 'translate-x-0', 'scale-100');
            }, 10);
        } else {
            el.classList.add('opacity-0', 'translate-x-4', 'scale-95');
            setTimeout(() => {
                el.classList.add('hidden');
            }, 150);
        }
    }

    function closeAllMinimized() {
        Object.keys(dropdownStates).forEach(id => {
            if (dropdownStates[id].minimizedOpen) {
                dropdownStates[id].minimizedOpen = false;
                toggleMinimized(id, false);
            }
        });
    }

    // Dropdown toggles
    ['students', 'attendance', 'teachers_visitors', 'books'].forEach(id => {
        const button = document.querySelector(`[data-dropdown-toggle="${id}"]`);
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebarExpanded) {
                dropdownStates[id].isOpen = !dropdownStates[id].isOpen;
                toggleExpanded(id, dropdownStates[id].isOpen);
                if (dropdownStates[id].minimizedOpen) {
                    dropdownStates[id].minimizedOpen = false;
                    toggleMinimized(id, false);
                }
            } else {
                dropdownStates[id].minimizedOpen = !dropdownStates[id].minimizedOpen;
                toggleMinimized(id, dropdownStates[id].minimizedOpen);
                if (dropdownStates[id].isOpen) {
                    dropdownStates[id].isOpen = false;
                    toggleExpanded(id, false);
                }
            }
        });
    });

    // Click away for minimized
    document.addEventListener('click', (e) => {
        if (!sidebarExpanded) {
            const isInsideDropdown = Array.from(document.querySelectorAll('[data-dropdown-toggle]')).some(btn => btn.contains(e.target));
            if (!isInsideDropdown) {
                closeAllMinimized();
            }
        }
    });

    // Profile functions
    function toggleProfile(show) {
        const arrow = profileDropdown.querySelector('.arrow');
        if (show) {
            profileDropdown.classList.remove('hidden');
            profileDropdown.classList.add('opacity-0', 'translate-y-4', 'scale-95');
            if (sidebarExpanded) {
                profileDropdown.classList.remove('bottom-16', 'left-16');
                profileDropdown.classList.add('bottom-16', 'left-0', 'right-0');
                arrow.classList.remove('bottom-6', 'left-0', '-translate-x-1');
                arrow.classList.add('bottom-2', 'left-6');
            } else {
                profileDropdown.classList.remove('bottom-16', 'left-0', 'right-0');
                profileDropdown.classList.add('bottom-16', 'left-16');
                arrow.classList.remove('bottom-2', 'left-6');
                arrow.classList.add('bottom-6', 'left-0', '-translate-x-1');
            }
            setTimeout(() => {
                profileDropdown.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                profileDropdown.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
            if (profileChevron) profileChevron.classList.add('rotate-180');
        } else {
            profileDropdown.classList.add('opacity-0', 'translate-y-4', 'scale-95');
            setTimeout(() => {
                profileDropdown.classList.add('hidden');
            }, 200);
            if (profileChevron) profileChevron.classList.remove('rotate-180');
        }
    }

    profileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        profileOpen = !profileOpen;
        toggleProfile(profileOpen);
    });

    // Click away for profile
    document.addEventListener('click', (e) => {
        if (profileOpen && !profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileOpen = false;
            toggleProfile(false);
        }
    });

    // Global click away helper (for compatibility)
    window.handleClickAway = (event, id) => {
        // No-op, handled by document listener
    };

    window.hideAllTooltips = hideAllTooltips;
</script>